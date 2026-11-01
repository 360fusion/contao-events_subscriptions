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
use Contao\BackendUser;
use Contao\CoreBundle\Exception\AccessDeniedException;
use Contao\CoreBundle\Security\ContaoCorePermissions;
use Contao\Database;
use Contao\DataContainer;
use Contao\Input;
use Contao\StringUtil;
use Contao\System;

/**
 * The simple "E-mails" view of the event notifications (Events › Settings › E-mail templates). Each e-mail is a notification with one
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
        return System::getContainer()->get('security.helper')->isGranted(ContaoCorePermissions::USER_CAN_ACCESS_MODULE, 'event_emails');
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

        // When it is sent is the group heading
        $info = EventsSubscriptionsEventType::NAME !== $row['type'] ? [sprintf($L['emailUsedBy'], $this->countUsage((int) $row['id']))] : [];

        $status = match (true) {
            null === $language => '<span class="tl_red">'.$L['emailNotSetUp'].'</span>',
            !$message->published => '<span class="tl_red">'.$L['emailSwitchedOff'].'</span>',
            default => '',
        };

        return $this->getTestDialogScript().sprintf(
            '<strong>%s</strong> %s%s%s',
            $row['title'] ?: '-',
            $status,
            $info ? '<br><span class="tl_gray">'.implode(' · ', $info).'</span>' : '',
            $language ? '<br><span class="tl_gray">'.$L['emailSubject'][0].': '.StringUtil::specialchars((string) $language['email_subject']).'</span>' : '',
        );
    }

    /**
     * "Send me a test" opens a pop-up to confirm or change the address, added to the first row of the list only
     */
    private function getTestDialogScript(): string
    {
        static $added = false;

        if ($added) {
            return '';
        }

        $added = true;
        $L = $GLOBALS['TL_LANG']['tl_nc_notification'];
        $config = json_encode([
            'email' => (string) BackendUser::getInstance()->email,
            'title' => $L['emailTestTitle'],
            'label' => $L['emailTestTo'],
            'help' => $L['emailTestHelp'],
            'send' => $L['emailTestSend'],
            'cancel' => $GLOBALS['TL_LANG']['MSC']['cancelBT'] ?? 'Cancel',
        ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);

        return <<<HTML
            <script>
            window.eventsEmailTest = {$config};

            if (!window.eventsEmailTestBound) {
                window.eventsEmailTestBound = true;

                document.addEventListener('click', event => {
                    const link = event.target.closest('a[href*="key=event_emailTest"]');

                    if (!link) {
                        return;
                    }

                    event.preventDefault();
                    event.stopPropagation();

                    const t = window.eventsEmailTest;
                    let dialog = document.getElementById('events-email-test');

                    if (!dialog) {
                        dialog = document.createElement('dialog');
                        dialog.id = 'events-email-test';
                        dialog.style.cssText = 'min-width:380px;padding:20px 24px;border:1px solid var(--border, #ccc);border-radius:8px;background:var(--content-bg, #fff);color:inherit';
                        dialog.innerHTML = '<form method="dialog"><h2 style="margin:0 0 14px"></h2><label for="events-email-test-to" style="display:block;margin-bottom:4px"></label><input type="email" id="events-email-test-to" class="tl_text" required style="width:100%"><p class="tl_help tl_tip"></p><div style="display:flex;flex-direction:row-reverse;gap:8px;margin-top:16px"><button type="submit" value="send" class="tl_submit"></button><button type="submit" value="cancel" class="tl_submit" formnovalidate></button></div></form>';
                        document.body.append(dialog);
                    }

                    dialog.querySelector('h2').textContent = t.title;
                    dialog.querySelector('label').textContent = t.label;
                    dialog.querySelector('.tl_help').textContent = t.help;
                    dialog.querySelector('[value=cancel]').textContent = t.cancel;
                    dialog.querySelector('[value=send]').textContent = t.send;

                    const input = dialog.querySelector('input');
                    input.value = t.email;
                    dialog.returnValue = '';
                    dialog.onclose = () => {
                        if ('send' === dialog.returnValue && input.value.trim()) {
                            const url = new URL(link.href, window.location.href);
                            url.searchParams.set('email', input.value.trim());
                            window.location.href = url.toString();
                        }
                    };

                    dialog.showModal();
                    input.select();
                }, true);
            }
            </script>
            HTML;
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
     * The "Insert tag" menu above the message: the tags grouped, a click inserts the tag where the cursor is
     */
    public function renderTags(DataContainer $dc): string
    {
        $type = (string) Database::getInstance()->prepare('SELECT type FROM tl_nc_notification WHERE id=?')->execute($dc->id)->type;
        $available = System::getContainer()->get(NotificationCenterHelper::class)->getTokenNames($type);
        $L = $GLOBALS['TL_LANG']['MSC'];
        $groups = '';

        foreach (EmailTags::forType($available, RegistrationFields::fromDatabase()->exportColumns(), $L['events_subscriptions.emailTags']) as $group => $tags) {
            $items = '';

            foreach ($tags as $token => $label) {
                $items .= sprintf('<li><button type="button" data-token="##%1$s##" title="##%1$s##">%2$s</button></li>', $token, StringUtil::specialchars($label));
            }

            $groups .= sprintf('<div class="group"><strong>%s</strong><ul>%s</ul></div>', $L['events_subscriptions.emailTagGroups'][$group] ?? $group, $items);
        }

        $label = $GLOBALS['TL_LANG']['tl_nc_notification']['emailTags'];

        return sprintf('<div class="widget clr" id="events-email-tags"><div class="events-email-toolbar"><details><summary class="tl_submit">%s ▾</summary><div class="menu">%s</div></details><span class="events-email-hint">%s</span></div></div>', $label[0], $groups, $label[1]).<<<'HTML'
            <style>
                /* The toolbar is moved onto the top edge of the message box, like the header of the text area */
                .events-email-toolbar { display: flex; align-items: center; gap: 14px; padding: 6px 8px; border: 1px solid var(--form-border, #aaa); border-bottom: 0; border-radius: 4px 4px 0 0; background: var(--table-header, rgba(0,0,0,.05)); }
                .events-email-toolbar .events-email-hint { flex: 1; color: var(--gray, #777); font-size: .8rem; }
                .events-email-toolbar + textarea, .events-email-toolbar + textarea + .tox-tinymce { border-top-left-radius: 0 !important; border-top-right-radius: 0 !important; }
                .events-email-toolbar details { position: relative; display: inline-block; }
                #events-email-tags summary, .events-email-toolbar summary { list-style: none; cursor: pointer; }
                .events-email-toolbar summary::-webkit-details-marker { display: none; }
                .events-email-toolbar .menu { position: absolute; z-index: 20; top: calc(100% + 4px); left: 0; display: flex; flex-wrap: wrap; gap: 16px 28px; width: min(880px, 80vw); max-height: 420px; overflow: auto; padding: 14px 18px; background: var(--content-bg, #fff); border: 1px solid var(--border, #ccc); border-radius: 6px; box-shadow: 0 6px 24px rgba(0,0,0,.18); }
                .events-email-toolbar .group { min-width: 180px; }
                .events-email-toolbar ul { margin: 6px 0 0; padding: 0; list-style: none; }
                .events-email-toolbar li button { display: block; width: 100%; padding: 4px 6px; border: 0; border-radius: 4px; background: none; color: inherit; font: inherit; text-align: left; cursor: pointer; }
                .events-email-toolbar li button:hover, .events-email-toolbar li button:focus { background: var(--hover-row, rgba(0,0,0,.06)); }
            </style>
            <script>
            (() => {
                const widget = document.getElementById('events-email-tags');
                const toolbar = widget.querySelector('.events-email-toolbar');
                const menu = toolbar.querySelector('details');
                const message = document.getElementById('ctrl_emailText') || document.getElementById('ctrl_emailHtml');

                // Sit on top of the message box, under its "Message" label
                if (message) {
                    message.before(toolbar);
                    widget.remove();
                }

                toolbar.querySelectorAll('[data-token]').forEach(button => button.addEventListener('click', () => {
                    const token = button.dataset.token;
                    const editor = window.tinymce?.get('ctrl_emailHtml');
                    const field = document.getElementById('ctrl_emailText');

                    menu.open = false;

                    // Formatted text: the editor remembers where the cursor was
                    if (editor) {
                        editor.focus();
                        editor.insertContent('##signature##' === token ? '##signature_html##' : token);

                        return;
                    }

                    // Plain text: the text area keeps its cursor position when the menu is used
                    const start = field.selectionStart ?? field.value.length;

                    field.setRangeText(token, start, field.selectionEnd ?? start, 'end');
                    field.focus();
                }));

                // Close the menu when clicking elsewhere
                document.addEventListener('click', event => {
                    if (menu.open && !menu.contains(event.target)) {
                        menu.open = false;
                    }
                });
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
