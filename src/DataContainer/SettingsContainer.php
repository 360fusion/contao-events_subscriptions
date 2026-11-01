<?php

namespace Codefog\EventsSubscriptionsBundle\DataContainer;

use Codefog\EventsSubscriptionsBundle\EventListener\LanguageListener;
use Codefog\EventsSubscriptionsBundle\Features;
use Contao\Config;
use Contao\Database;
use Contao\Input;
use Contao\Message;
use Contao\StringUtil;
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

        System::loadLanguageFile('tl_events_subscriptions_settings');
        $this->explain($page);
    }

    /**
     * Calendars with subscriptions, to copy the booking settings of
     */
    public function getCalendarOptions(): array
    {
        $result = Database::getInstance()->query('SELECT id, title FROM tl_calendar WHERE subscription_enable=1 ORDER BY title');

        return array_combine($result->fetchEach('id'), $result->fetchEach('title')) ?: [];
    }

    /**
     * The back end modules the explanations link to
     */
    private const LINKS = [
        'subscribers' => 'event_subscribers',
        'emails' => 'event_emails',
        'calendars' => 'calendar',
        'general' => 'event_settings',
        'mailings' => 'event_settings_mailings',
        'providers' => 'event_payment_providers',
        'retention' => 'event_retention',
        'reports' => 'event_reports',
        'venues' => 'event_venues',
    ];

    /**
     * Above the form: what the page is for, how it works and where to go to set things up, then how things stand now
     */
    private function explain(string $page): void
    {
        $L = $GLOBALS['TL_LANG']['tl_events_subscriptions_settings'];
        $router = System::getContainer()->get('router');

        // {name} becomes a link to the back end module
        $linked = static function (string $text) use ($router, $L): string {
            foreach (self::LINKS as $name => $module) {
                $label = $L['sections'][$module] ?? $GLOBALS['TL_LANG']['MOD'][$module][0];
                $text = str_replace('{'.$name.'}', '<a href="'.StringUtil::specialchars($router->generate('contao_backend', ['do' => $module])).'">'.$label.'</a>', $text);
            }

            return $text;
        };

        $html = '';

        foreach ($L['explain'][$page] ?? [] as [$type, $content]) {
            $html .= match ($type) {
                'h' => '<p style="margin:14px 0 4px"><strong>'.$linked($content).'</strong></p>',
                'ul', 'ol' => '<'.$type.' style="margin:4px 0 4px 22px;padding:0;list-style:'.('ul' === $type ? 'disc' : 'decimal').'">'.implode('', array_map(static fn (string $item) => '<li style="margin:4px 0">'.$linked($item).'</li>', $content)).'</'.$type.'>',
                default => '<p style="margin:6px 0">'.$linked($content).'</p>',
            };
        }

        $status = array_filter($this->getStatus($page), static fn (string $line) => '' !== $line);

        if ($status) {
            $html .= '<p style="margin:14px 0 4px"><strong>'.$L['statusTitle'].'</strong></p><ul style="margin:4px 0 4px 22px;padding:0;list-style:disc">'.implode('', array_map(static fn (string $line) => '<li style="margin:4px 0">'.$linked($line).'</li>', $status)).'</ul>';
        }

        Message::addRaw('<div class="tl_info" style="padding:14px 18px;line-height:1.5">'.$html.'</div>');
    }

    /**
     * Lines about the current situation, shown under the explanation
     *
     * @return list<string>
     */
    private function getStatus(string $page): array
    {
        $L = $GLOBALS['TL_LANG']['tl_events_subscriptions_settings']['status'];
        $db = Database::getInstance();
        $escape = static fn (string $value) => StringUtil::specialchars($value);
        $lines = [];

        switch ($page) {
            case 'emails':
                $rows = $db->query("SELECT l.email_sender_name AS name, l.email_sender_address AS address, CONCAT(COALESCE(l.email_text, ''), COALESCE(l.email_html, '')) AS body FROM tl_nc_language l JOIN tl_nc_message m ON m.id=l.pid JOIN tl_nc_notification n ON n.id=m.pid WHERE n.type LIKE 'events_subscription%'")->fetchAllAssoc();

                if ($rows) {
                    $senders = array_values(array_unique(array_map(static fn (array $row) => trim($row['name'].' <'.$row['address'].'>'), $rows)));
                    $withSignature = \count(array_filter($rows, static fn (array $row) => str_contains($row['body'], '##signature')));
                    $lines[] = 1 === \count($senders)
                        ? sprintf($L['senderOne'], \count($rows), $escape($senders[0]))
                        : sprintf($L['senderMany'], \count($rows), \count($senders), '<br>'.implode('<br>', array_map($escape, $senders)));
                    $lines[] = $withSignature ? sprintf($L['signatureUse'], $withSignature, \count($rows)) : sprintf($L['signatureNone'], \count($rows));
                }

                $GLOBALS['TL_DCA']['tl_events_subscriptions_settings']['fields']['eventsSubscriptionsSignature']['eval']['placeholder'] = $GLOBALS['TL_LANG']['tl_events_subscriptions_settings']['signaturePlaceholder'];
                break;

            case 'payments':
                $providers = (int) $db->query('SELECT COUNT(*) AS n FROM tl_calendar_events_payment_provider')->n;
                $lines[] = Features::payments()
                    ? ($providers ? sprintf($L['paymentsOn'], $providers) : $L['paymentsOnNoProvider'])
                    : $L['paymentsOff'];
                break;

            case 'certificates':
                $lines[] = Features::attendance()
                    ? sprintf($L['attended'], number_format((int) $db->query('SELECT COUNT(*) AS n FROM tl_calendar_events_subscription WHERE attended=1')->n))
                    : $L['attendanceOff'];
                break;

            case 'mailings':
                $count = static fn (string $type) => (int) $db->prepare('SELECT COUNT(*) AS n FROM tl_nc_notification WHERE type=?')->execute($type)->n;
                $lines[] = sprintf($L['mailingsReady'], $count('events_subscriptions_trainer'), $count('events_subscriptions_followUp'));
                $lines[] = sprintf($L['trainerEvents'], (int) $db->prepare("SELECT COUNT(*) AS n FROM tl_calendar_events e WHERE e.published=1 AND e.startTime>? AND (e.subscription_trainerEmail!='' OR (e.eventGroup>0 AND EXISTS (SELECT 1 FROM tl_calendar_event_group g WHERE g.id=e.eventGroup AND g.subscription_trainerEmail!='')))")->execute(time())->n);
                break;
        }

        return $lines;
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
     * A new calendar starts with the booking settings of the template calendar (Events › Settings › Booking settings)
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
