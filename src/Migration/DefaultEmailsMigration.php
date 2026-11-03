<?php

namespace Codefog\EventsSubscriptionsBundle\Migration;

use Contao\Config;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\CoreBundle\Migration\AbstractMigration;
use Contao\CoreBundle\Migration\MigrationResult;
use Contao\StringUtil;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;

/**
 * One ready-made e-mail for every e-mail type of the booking app (confirmation,
 * cancellation, place from the waiting list, reminder, invitation sent by hand,
 * trainer pack, after the event), with the standard e-mail fields. A calendar
 * that chooses no e-mail sends every e-mail of the type, so bookings send these
 * straight away. Needs an e-mail gateway of the Notification Center; a type that
 * already has an e-mail gets none, and existing e-mails are never changed.
 */
class DefaultEmailsMigration extends AbstractMigration
{
    private const EVENT = "Event: ##event_title##\nDate: ##event_startDate##, ##event_startTime##\nLocation: ##event_location##\nDetails: ##event_link##";

    private const EMAILS = [
        'events_subscriptions_subscribe' => [
            'title' => 'Booking confirmation',
            'subject' => '{if subscription_waitingList=="1"}On the waiting list{else}Booking confirmed{endif}: ##event_title##',
            'text' => "Dear ##subscription_firstname##,\n\n{if subscription_waitingList==\"1\"}The event is full, so you are on the waiting list. We will e-mail you if a place becomes free.{else}Thank you, your place is booked.{endif}\n\n\n".self::EVENT."\nAdd it to your calendar: ##event_ics_link##\n\nYour details:\n##subscription_details##\n\n{if payment_amount!=\"\"}Payment: ##payment_amount##, ##payment_method## (##payment_status##)\n{if payment_link!=\"\"}Pay here: ##payment_link##\n{endif}\n{if payment_invoice_text!=\"\"}##payment_invoice_text##\n{endif}\n\n{endif}\nTo see or change your booking, or to cancel it: ##booking_link##\n\n##signature##",
        ],
        'events_subscriptions_unsubscribe' => [
            'title' => 'Booking cancelled',
            'subject' => 'Booking cancelled: ##event_title##',
            'text' => "Dear ##subscription_firstname##,\n\nYour {if subscription_waitingList==\"1\"}place on the waiting list{else}booking{endif} has been cancelled.\n\n".self::EVENT."\n\nIf this was a mistake, you can book again while places are left: ##event_link##\n\n##signature##",
        ],
        'events_subscriptions_listUpdate' => [
            'title' => 'Place from the waiting list',
            'subject' => 'You have a place: ##event_title##',
            'text' => "Dear ##subscription_firstname##,\n\nA place has become free and it is now yours: you are booked.\n\n".self::EVENT."\nAdd it to your calendar: ##event_ics_link##\n\n{if payment_link!=\"\"}To keep your place, please pay here: ##payment_link##\n\n{endif}Can't come any more? Please cancel, so someone else can have the place: ##booking_link##\n\n##signature##",
        ],
        'events_subscriptions_reminder' => [
            'title' => 'Event reminder',
            'subject' => 'Reminder: ##event_title## on ##event_startDate##',
            'text' => "Dear ##subscription_firstname##,\n\n{if subscription_waitingList==\"1\"}A reminder that you are on the waiting list for this event. We will e-mail you if a place becomes free.{else}A reminder that you are booked on this event on ##event_startDate##.{endif}\n\n\n".self::EVENT."\n\nCan't come any more? Please cancel, so someone else can have the place: ##booking_link##\n\n##signature##",
        ],
        'events_subscription_event' => [
            'title' => 'New event invitation (sent by hand)',
            'subject' => 'New event: ##event_title##',
            'text' => "Hello,\n\nA new event is open for booking.\n\n".self::EVENT."\n\nBook your place here: ##event_link##\n\n##signature##",
        ],
        'events_subscriptions_trainer' => [
            'title' => 'Trainer pack the day before',
            'subject' => 'Attendees for ##event_title## (##event_startDate##)',
            'text' => "Hello,\n\nHere is everyone booked on ##event_title## on ##event_startDate## at ##event_startTime## (##event_location##).\n\nBookings: ##attendee_count##, places: ##attendee_places##\n\n##attendee_list##\n\n##signature##",
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

        $this->framework->initialize();

        return false !== $this->getGateway() && [] !== $this->getMissingTypes();
    }

    public function run(): MigrationResult
    {
        $this->framework->initialize();

        $gateway = $this->getGateway();
        [$senderName, $senderAddress] = $this->getSender();
        $created = $createdTypes = [];

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
            $createdTypes[] = $type;
        }

        // Remember them: an e-mail the administrator deletes later is not created again by the next update
        Config::persist('eventsSubscriptionsDefaultEmails', implode(',', array_unique(array_merge($this->getCreatedTypes(), $createdTypes))));

        return $this->createResult(true, 'Created the e-mails: '.implode(', ', $created).'.');
    }

    /**
     * @return list<string> The notification types that have no e-mail yet
     */
    private function getMissingTypes(): array
    {
        $existing = $this->connection->fetchFirstColumn('SELECT DISTINCT type FROM tl_nc_notification WHERE type IN (?)', [array_keys(self::EMAILS)], [ArrayParameterType::STRING]);

        return array_values(array_diff(array_keys(self::EMAILS), $existing, $this->getCreatedTypes()));
    }

    /**
     * @return list<string> The types this migration has created an e-mail for before
     */
    private function getCreatedTypes(): array
    {
        return array_values(array_filter(explode(',', (string) Config::get('eventsSubscriptionsDefaultEmails'))));
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
