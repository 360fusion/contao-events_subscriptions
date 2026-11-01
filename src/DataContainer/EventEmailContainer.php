<?php

namespace Codefog\EventsSubscriptionsBundle\DataContainer;

use Codefog\EventsSubscriptionsBundle\Email\EmailTags;
use Codefog\EventsSubscriptionsBundle\NotificationCenter\NotificationType\EventsSubscriptionsEventType;
use Codefog\EventsSubscriptionsBundle\NotificationCenter\NotificationType\EventsSubscriptionsListUpdateType;
use Codefog\EventsSubscriptionsBundle\NotificationCenter\NotificationType\EventsSubscriptionsReminderType;
use Codefog\EventsSubscriptionsBundle\NotificationCenter\NotificationType\EventsSubscriptionsSubscribeType;
use Codefog\EventsSubscriptionsBundle\NotificationCenter\NotificationType\EventsSubscriptionsUnsubscribeType;
use Codefog\EventsSubscriptionsBundle\NotificationCenterHelper;
use Codefog\EventsSubscriptionsBundle\RegistrationFields;
use Contao\CoreBundle\Exception\AccessDeniedException;
use Contao\CoreBundle\Security\ContaoCorePermissions;
use Contao\Database;
use Contao\DataContainer;
use Contao\Input;
use Contao\StringUtil;
use Contao\System;

/**
 * The simple "E-mails" view of the event notifications (Events › E-mails). Each e-mail is a notification with one
 * message and one language in the Notification Center: this view edits the content of that language directly.
 */
class EventEmailContainer
{
    public const TYPES = [
        EventsSubscriptionsSubscribeType::NAME,
        EventsSubscriptionsUnsubscribeType::NAME,
        EventsSubscriptionsListUpdateType::NAME,
        EventsSubscriptionsReminderType::NAME,
        EventsSubscriptionsEventType::NAME,
    ];

    /**
     * Calendar, event group and event fields that select a notification
     */
    private const NOTIFICATION_FIELDS = ['subscription_subscribeNotification', 'subscription_unsubscribeNotification', 'subscription_listUpdateNotification', 'subscription_notification'];

    /**
     * @var array<int, array|null> Language rows by notification ID
     */
    private static array $languages = [];

    public static function canManage(): bool
    {
        return System::getContainer()->get('security.helper')->isGranted(ContaoCorePermissions::USER_CAN_ACCESS_MODULE, 'nc_notifications');
    }

    /**
     * Only users who may manage the notifications, and the editor shows the field for the e-mail's format
     */
    public function onLoad(DataContainer|null $dc = null): void
    {
        if (!self::canManage()) {
            throw new AccessDeniedException('Not allowed to manage the event e-mails');
        }

        if ('edit' !== Input::get('act') || !$dc?->id) {
            return;
        }

        $type = (string) Database::getInstance()->prepare('SELECT type FROM tl_nc_notification WHERE id=?')->execute($dc->id)->type;

        // A new e-mail first needs its type
        if ('' === $type) {
            return;
        }

        $editor = $this->isHtml((int) $dc->id) ? 'emailHtml' : 'emailText';

        $GLOBALS['TL_DCA']['tl_nc_notification']['palettes']['default'] = '{title_legend},title,type;{content_legend},emailSubject,emailFormat,'.$editor.',emailTags;{more_legend:hide},emailSenderName,emailSenderAddress,emailEnabled';
    }

    public function getTypeOptions(): array
    {
        return self::TYPES;
    }

    public function generateLabel(array $row): string
    {
        $language = $this->getLanguage((int) $row['id']);
        $message = Database::getInstance()->prepare('SELECT published FROM tl_nc_message WHERE pid=? ORDER BY id LIMIT 1')->execute($row['id']);
        $L = $GLOBALS['TL_LANG']['tl_nc_notification'];

        $info = [$GLOBALS['TL_LANG']['MSC']['events_subscriptions.emailTypes'][$row['type']] ?? $row['type']];

        if (EventsSubscriptionsEventType::NAME !== $row['type']) {
            $info[] = sprintf($L['emailUsedBy'], $this->countUsage((int) $row['id']));
        }

        $status = match (true) {
            null === $language => '<span class="tl_red">'.$L['emailNotSetUp'].'</span>',
            !$message->published => '<span class="tl_red">'.$L['emailSwitchedOff'].'</span>',
            default => '',
        };

        return sprintf(
            '<strong>%s</strong> %s<br><span class="tl_gray">%s</span>%s',
            $row['title'] ?: '-',
            $status,
            implode(' · ', $info),
            $language ? '<br><span class="tl_gray">'.$L['emailSubject'][0].': '.StringUtil::specialchars((string) $language['email_subject']).'</span>' : '',
        );
    }

    public function loadSubject($value, DataContainer $dc): string
    {
        return (string) ($this->getLanguage((int) $dc->id)['email_subject'] ?? '');
    }

    public function saveSubject($value, DataContainer $dc): string
    {
        $this->updateLanguage((int) $dc->id, ['email_subject' => (string) $value]);

        return '';
    }

    public function loadFormat($value, DataContainer $dc): string
    {
        return $this->isHtml((int) $dc->id) ? 'html' : 'text';
    }

    /**
     * Switching to formatted text keeps the plain text as the start of the formatted one
     */
    public function saveFormat($value, DataContainer $dc): string
    {
        $language = $this->getLanguage((int) $dc->id) ?? [];
        $set = ['email_mode' => 'html' === $value ? 'htmlAndAutoText' : 'textOnly'];

        if ('html' === $value && '' === trim((string) ($language['email_html'] ?? ''))) {
            $set['email_html'] = '<p>'.nl2br(StringUtil::specialchars((string) ($language['email_text'] ?? '')), false).'</p>';
        }

        $this->updateLanguage((int) $dc->id, $set);

        return '';
    }

    public function loadText($value, DataContainer $dc): string
    {
        return (string) ($this->getLanguage((int) $dc->id)['email_text'] ?? '');
    }

    public function saveText($value, DataContainer $dc): string
    {
        $this->updateLanguage((int) $dc->id, ['email_text' => (string) $value]);

        return '';
    }

    public function loadHtml($value, DataContainer $dc): string
    {
        return (string) ($this->getLanguage((int) $dc->id)['email_html'] ?? '');
    }

    public function saveHtml($value, DataContainer $dc): string
    {
        $this->updateLanguage((int) $dc->id, ['email_html' => (string) $value]);

        return '';
    }

    public function loadSenderName($value, DataContainer $dc): string
    {
        return (string) ($this->getLanguage((int) $dc->id)['email_sender_name'] ?? $this->getDefaultSender()['email_sender_name']);
    }

    public function saveSenderName($value, DataContainer $dc): string
    {
        $this->updateLanguage((int) $dc->id, ['email_sender_name' => (string) $value]);

        return '';
    }

    public function loadSenderAddress($value, DataContainer $dc): string
    {
        return (string) ($this->getLanguage((int) $dc->id)['email_sender_address'] ?? $this->getDefaultSender()['email_sender_address']);
    }

    public function saveSenderAddress($value, DataContainer $dc): string
    {
        $this->updateLanguage((int) $dc->id, ['email_sender_address' => (string) $value]);

        return '';
    }

    public function loadEnabled($value, DataContainer $dc): string
    {
        $message = Database::getInstance()->prepare('SELECT published FROM tl_nc_message WHERE pid=? ORDER BY id LIMIT 1')->execute($dc->id);

        return !$message->numRows || $message->published ? '1' : '';
    }

    public function saveEnabled($value, DataContainer $dc): string
    {
        $this->ensureLanguage((int) $dc->id);
        Database::getInstance()->prepare('UPDATE tl_nc_message SET published=?, tstamp=? WHERE pid=?')->execute($value ? 1 : 0, time(), $dc->id);

        return '';
    }

    /**
     * The tags that can be inserted, as buttons. A click inserts the tag where the cursor is.
     */
    public function renderTags(DataContainer $dc): string
    {
        $type = (string) Database::getInstance()->prepare('SELECT type FROM tl_nc_notification WHERE id=?')->execute($dc->id)->type;
        $available = System::getContainer()->get(NotificationCenterHelper::class)->getTokenNames($type);
        $registration = [];

        foreach (RegistrationFields::fromDatabase()->exportColumns() as $token => $label) {
            $registration[$token] = $label;
        }

        $L = $GLOBALS['TL_LANG']['MSC'];
        $html = '';

        foreach (EmailTags::forType($available, $registration, $L['events_subscriptions.emailTags']) as $group => $tags) {
            $buttons = '';

            foreach ($tags as $token => $label) {
                $buttons .= sprintf('<button type="button" class="tl_submit" data-token="##%s##" title="##%s##" style="margin:0 6px 6px 0">%s</button>', $token, $token, StringUtil::specialchars($label));
            }

            $html .= sprintf('<p style="margin:12px 0 4px"><strong>%s</strong></p><div>%s</div>', $L['events_subscriptions.emailTagGroups'][$group] ?? $group, $buttons);
        }

        return '<div class="widget clr" id="events-email-tags"><h3>'.$GLOBALS['TL_LANG']['tl_nc_notification']['emailTags'][0].'</h3>'.$html.'<p class="tl_help tl_tip">'.$GLOBALS['TL_LANG']['tl_nc_notification']['emailTags'][1].'</p></div>'.<<<'HTML'
            <script>
            (() => {
                const panel = document.getElementById('events-email-tags');
                const form = panel.closest('form');
                let last = null;

                form.addEventListener('focusin', event => {
                    if (event.target.matches('#ctrl_emailSubject, #ctrl_emailText, #ctrl_emailSenderName')) {
                        last = event.target;
                    }
                });

                panel.querySelectorAll('[data-token]').forEach(button => button.addEventListener('click', () => {
                    const token = button.dataset.token;
                    const editor = window.tinymce?.get('ctrl_emailHtml');

                    // Into the formatted text, unless the subject was the last field used
                    if (editor && (!last || last.id !== 'ctrl_emailSubject')) {
                        editor.insertContent(token);
                        editor.focus();

                        return;
                    }

                    const field = last || document.getElementById('ctrl_emailText') || document.getElementById('ctrl_emailSubject');
                    const start = field.selectionStart ?? field.value.length;

                    field.setRangeText(token, start, field.selectionEnd ?? start, 'end');
                    field.focus();
                }));
            })();
            </script>
            HTML;
    }

    /**
     * The main language row of the e-mail (fallback first), null if the e-mail has no content yet
     */
    public function getLanguage(int $notificationId): array|null
    {
        if (!\array_key_exists($notificationId, self::$languages)) {
            $row = Database::getInstance()
                ->prepare('SELECT l.* FROM tl_nc_language l JOIN tl_nc_message m ON m.id=l.pid WHERE m.pid=? ORDER BY m.id, l.fallback DESC, l.id LIMIT 1')
                ->execute($notificationId)
                ->fetchAssoc();

            self::$languages[$notificationId] = $row ?: null;
        }

        return self::$languages[$notificationId];
    }

    private function updateLanguage(int $notificationId, array $set): void
    {
        $language = $this->ensureLanguage($notificationId);

        Database::getInstance()->prepare('UPDATE tl_nc_language %s WHERE id=?')->set($set + ['tstamp' => time()])->execute($language['id']);

        self::$languages[$notificationId] = array_merge($language, $set);
    }

    /**
     * Create the message and the language of an e-mail that has none, with the sender most e-mails use
     */
    private function ensureLanguage(int $notificationId): array
    {
        if (null !== ($language = $this->getLanguage($notificationId))) {
            return $language;
        }

        $db = Database::getInstance();
        $messageId = $db->prepare('SELECT id FROM tl_nc_message WHERE pid=? ORDER BY id LIMIT 1')->execute($notificationId)->id;

        if (!$messageId) {
            $messageId = $db->prepare('INSERT INTO tl_nc_message %s')->set([
                'pid' => $notificationId,
                'tstamp' => time(),
                'title' => (string) $db->prepare('SELECT title FROM tl_nc_notification WHERE id=?')->execute($notificationId)->title,
                'gateway' => (int) $db->query("SELECT id FROM tl_nc_gateway WHERE type='mailer' ORDER BY id LIMIT 1")->id,
                'published' => 1,
            ])->execute()->insertId;
        }

        $sender = $this->getDefaultSender();

        $db->prepare('INSERT INTO tl_nc_language %s')->set([
            'pid' => $messageId,
            'tstamp' => time(),
            'language' => (string) ($db->query("SELECT language FROM tl_page WHERE type='root' ORDER BY fallback DESC, sorting LIMIT 1")->language ?: 'en'),
            'fallback' => 1,
            'recipients' => '##recipient_email##',
            'email_sender_name' => $sender['email_sender_name'],
            'email_sender_address' => $sender['email_sender_address'],
            'email_mode' => 'textOnly',
        ])->execute();

        unset(self::$languages[$notificationId]);

        return $this->getLanguage($notificationId);
    }

    /**
     * The sender most e-mails use
     *
     * @return array{email_sender_name: string, email_sender_address: string}
     */
    private function getDefaultSender(): array
    {
        $row = Database::getInstance()->query("SELECT email_sender_name, email_sender_address FROM tl_nc_language WHERE email_sender_address!='' GROUP BY email_sender_name, email_sender_address ORDER BY COUNT(*) DESC LIMIT 1")->fetchAssoc() ?: [];

        return ['email_sender_name' => (string) ($row['email_sender_name'] ?? ''), 'email_sender_address' => (string) ($row['email_sender_address'] ?? '')];
    }

    private function isHtml(int $notificationId): bool
    {
        return 'textOnly' !== ($this->getLanguage($notificationId)['email_mode'] ?? 'textOnly');
    }

    /**
     * Number of calendars, event groups and events that send this e-mail
     */
    private function countUsage(int $notificationId): int
    {
        $db = Database::getInstance();
        $where = implode(' OR ', array_map(static fn ($field) => "$field=?", self::NOTIFICATION_FIELDS));
        $values = array_fill(0, \count(self::NOTIFICATION_FIELDS), $notificationId);
        $count = 0;

        foreach (['tl_calendar', 'tl_calendar_event_group', 'tl_calendar_events'] as $table) {
            if ($db->tableExists($table) && $db->fieldExists('subscription_notification', $table) && $db->fieldExists('subscription_subscribeNotification', $table)) {
                $count += (int) $db->prepare("SELECT COUNT(*) AS n FROM $table WHERE $where")->execute(...$values)->n;
            }
        }

        return $count;
    }
}
