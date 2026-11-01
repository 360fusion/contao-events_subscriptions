<?php

namespace Codefog\EventsSubscriptionsBundle\Migration;

use Contao\Config;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\CoreBundle\Migration\AbstractMigration;
use Contao\CoreBundle\Migration\MigrationResult;
use Contao\StringUtil;
use Doctrine\DBAL\Connection;

/**
 * The two e-mails the site sends by itself around an event (the trainer pack and the thank-you) exist from the start, with the
 * standard e-mail fields, ready to be chosen in Events › Settings › Booking settings › Before and after events. Needs an e-mail
 * gateway of the Notification Center; an e-mail that already exists is never created again or changed.
 */
class DefaultEmailsMigration extends AbstractMigration
{
    private const EMAILS = [
        'events_subscriptions_trainer' => [
            'title' => 'Trainer pack the day before',
            'subject' => 'Attendees for ##event_title## (##event_startDate##)',
            'text' => "Hello,\n\nHere is everyone booked on ##event_title## on ##event_startDate## at ##event_startTime## (##event_location##).\n\n##attendee_count## bookings, ##attendee_places## places:\n\n##attendee_list##\n\n##signature##",
        ],
        'events_subscriptions_followUp' => [
            'title' => 'After the event: certificate and feedback',
            'subject' => 'Thank you for attending ##event_title##',
            'text' => "Dear ##subscription_firstname##,\n\nThank you for attending ##event_title##.\n\nYour certificate of attendance: ##certificate_link##\n\nWe would be grateful for your feedback, it takes about a minute: ##feedback_link##\n\n##signature##",
        ],
    ];

    public function __construct(
        private readonly Connection $connection,
        private readonly ContaoFramework $framework,
    ) {
    }

    public function shouldRun(): bool
    {
        // The Notification Center tables come with the schema update, the migration runs again after it
        if (!$this->connection->createSchemaManager()->tablesExist(['tl_nc_notification', 'tl_nc_message', 'tl_nc_language', 'tl_nc_gateway'])) {
            return false;
        }

        return false !== $this->getGateway() && [] !== $this->getMissingTypes();
    }

    public function run(): MigrationResult
    {
        $this->framework->initialize();

        $gateway = $this->getGateway();
        [$senderName, $senderAddress] = $this->getSender();
        $created = [];

        foreach ($this->getMissingTypes() as $type) {
            $email = self::EMAILS[$type];
            $now = time();

            $this->connection->insert('tl_nc_notification', ['tstamp' => $now, 'title' => $email['title'], 'type' => $type]);
            $this->connection->insert('tl_nc_message', ['pid' => $this->connection->lastInsertId(), 'tstamp' => $now, 'title' => $email['title'], 'gateway' => $gateway, 'email_priority' => 3, 'email_template' => 'mail_default', 'published' => 1, 'sorting' => 0]);
            $this->connection->insert('tl_nc_language', [
                'pid' => $this->connection->lastInsertId(),
                'tstamp' => $now,
                'language' => 'en',
                'fallback' => 1,
                'recipients' => '##recipient_email##',
                'attachment_tokens' => '',
                'email_sender_name' => $senderName,
                'email_sender_address' => $senderAddress,
                'email_replyTo' => $senderAddress,
                'email_subject' => $email['subject'],
                'email_mode' => 'textOnly',
                'email_text' => $email['text'],
            ]);

            $created[] = $email['title'];
        }

        return $this->createResult(true, 'Created the e-mails: '.implode(', ', $created).'.');
    }

    /**
     * @return list<string> The notification types that have no e-mail yet
     */
    private function getMissingTypes(): array
    {
        $existing = $this->connection->fetchFirstColumn('SELECT DISTINCT type FROM tl_nc_notification WHERE type IN (?, ?)', array_keys(self::EMAILS));

        return array_values(array_diff(array_keys(self::EMAILS), $existing));
    }

    private function getGateway(): int|false
    {
        return $this->connection->fetchOne("SELECT id FROM tl_nc_gateway WHERE type='mailer' ORDER BY id LIMIT 1");
    }

    /**
     * The sender of Booking settings › E-mail signatures, otherwise the sender of an existing event e-mail, otherwise the administrator
     *
     * @return array{0: string, 1: string} Name and address
     */
    private function getSender(): array
    {
        if ('' !== ($address = trim((string) Config::get('eventsSubscriptionsSenderAddress')))) {
            return [trim((string) Config::get('eventsSubscriptionsSenderName')), $address];
        }

        $row = $this->connection->fetchAssociative("SELECT l.email_sender_name, l.email_sender_address FROM tl_nc_language l JOIN tl_nc_message m ON m.id=l.pid JOIN tl_nc_notification n ON n.id=m.pid WHERE n.type LIKE 'events_subscription%' AND l.email_sender_address!='' ORDER BY n.id LIMIT 1");

        if (false !== $row) {
            return [(string) $row['email_sender_name'], (string) $row['email_sender_address']];
        }

        [$name, $address] = StringUtil::splitFriendlyEmail((string) Config::get('adminEmail'));

        return [(string) $name, (string) $address];
    }
}
