<?php

namespace Codefog\EventsSubscriptionsBundle\DataContainer;

use Codefog\EventsSubscriptionsBundle\EventListener\LanguageListener;
use Codefog\EventsSubscriptionsBundle\Features;
use Contao\Config;
use Contao\Database;
use Contao\Input;
use Contao\Message;
use Contao\System;

class SettingsContainer
{
    /**
     * The back end module of each settings page => its palette
     */
    public const PAGES = [
        'event_settings' => 'default',
        'event_settings_payments' => 'payments',
        'event_settings_emails' => 'emails',
        'event_settings_certificates' => 'certificates',
        'event_settings_mailings' => 'mailings',
    ];

    /**
     * Show only the settings of the page that was opened. Saving a page leaves the settings of the others as they are.
     */
    public function selectPalette(): void
    {
        $dca = &$GLOBALS['TL_DCA']['tl_events_subscriptions_settings'];
        $page = self::PAGES[(string) Input::get('do')] ?? 'default';
        $dca['palettes']['default'] = $dca['palettes'][$page];

        if ('payments' === $page && Features::payments()) {
            System::loadLanguageFile('tl_events_subscriptions_settings');
            Message::addInfo(sprintf($GLOBALS['TL_LANG']['tl_events_subscriptions_settings']['paymentsHint'], System::getContainer()->get('router')->generate('contao_backend', ['do' => 'event_payment_providers'])));
        }
    }

    /**
     * Calendars with subscriptions, to copy the booking settings of
     */
    public function getCalendarOptions(): array
    {
        $result = Database::getInstance()->query('SELECT id, title FROM tl_calendar WHERE subscription_enable=1 ORDER BY title');

        return array_combine($result->fetchEach('id'), $result->fetchEach('title')) ?: [];
    }

    public function getTrainerNotifications(): array
    {
        return $this->getNotifications('events_subscriptions_trainer');
    }

    public function getFollowUpNotifications(): array
    {
        return $this->getNotifications('events_subscriptions_followUp');
    }

    private function getNotifications(string $type): array
    {
        $result = Database::getInstance()->prepare('SELECT id, title FROM tl_nc_notification WHERE type=? ORDER BY title')->execute($type);

        return array_combine($result->fetchEach('id'), $result->fetchEach('title')) ?: [];
    }

    /**
     * A new calendar starts with the booking settings of the template calendar (Events › Setup › Booking settings)
     */
    public function applyTemplateCalendar(string $table, int $insertId): void
    {
        $templateId = (int) Config::get('eventsSubscriptionsTemplateCalendar');

        if (!$templateId || $templateId === $insertId) {
            return;
        }

        $template = Database::getInstance()->prepare('SELECT * FROM tl_calendar WHERE id=?')->execute($templateId)->fetchAssoc();

        if (!$template) {
            return;
        }

        $set = array_filter($template, static fn ($key) => str_starts_with((string) $key, 'subscription_'), ARRAY_FILTER_USE_KEY);

        if ($set) {
            Database::getInstance()->prepare('UPDATE tl_calendar %s WHERE id=?')->set($set)->execute($insertId);
        }
    }

    /**
     * Show the default booking form texts as placeholders, empty fields use them
     */
    public function showDefaultTexts(): void
    {
        System::loadLanguageFile('default');

        foreach (Features::TEXTS as $key => $setting) {
            $GLOBALS['TL_DCA']['tl_events_subscriptions_settings']['fields'][$setting]['eval']['placeholder'] = LanguageListener::$defaults[$key] ?? '';
        }
    }

    /**
     * The sender is the same for all event e-mails: saving it updates them
     */
    public function applySender(): void
    {
        $name = trim((string) Input::post('eventsSubscriptionsSenderName'));
        $address = trim((string) Input::post('eventsSubscriptionsSenderAddress'));

        if ('' === $name && '' === $address) {
            return;
        }

        $set = array_filter(['email_sender_name' => $name, 'email_sender_address' => $address], static fn ($value) => '' !== $value);

        Database::getInstance()
            ->prepare("UPDATE tl_nc_language l JOIN tl_nc_message m ON m.id=l.pid JOIN tl_nc_notification n ON n.id=m.pid SET ".implode(', ', array_map(static fn ($column) => "l.$column=?", array_keys($set))).", l.tstamp=? WHERE n.type LIKE 'events_subscription%'")
            ->execute(...[...array_values($set), time()]);
    }
}
