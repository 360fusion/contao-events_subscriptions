<?php

namespace Codefog\EventsSubscriptionsBundle;

use Codefog\EventsSubscriptionsBundle\Model\SubscriptionModel;

/**
 * Who is booked on an event, with their answers: for the trainer pack and the sign-in sheet
 */
final class Attendees
{
    /**
     * Bookings in the order they were made. Unpaid card bookings (pending, failed) are not real bookings and refunded ones are
     * treated as cancelled: both are left out.
     *
     * @return list<array{id: int, name: string, email: string, places: int, waiting: bool, attended: bool, answers: list<array{0: string, 1: string}>}>
     */
    public static function forEvent(EventConfig $config): array
    {
        $registrationFields = RegistrationFields::fromDatabase();
        $fields = $registrationFields->resolve($config->get('subscription_fields'));
        $factory = Services::getSubscriptionFactory();
        $rows = [];

        foreach (SubscriptionModel::findBy(['pid=?', "paymentStatus NOT IN ('pending', 'failed', 'refunded', 'confirm')"], [$config->getEvent()->id], ['order' => 'dateCreated, id']) ?? [] as $model) {
            try {
                $waiting = (bool) $factory->createFromModel($model)->isOnWaitingList();
            } catch (\InvalidArgumentException) {
                continue;
            }

            $person = $model->getMember() ?? $model;
            $answers = [];

            foreach ($fields as $field) {
                $value = $registrationFields->formatValue($field, $model->{$registrationFields->virtualFieldName($field['key'])});

                if ('' !== trim($value)) {
                    $answers[] = [$field['label'], $value];
                }
            }

            $rows[] = [
                'id' => (int) $model->id,
                'name' => trim($person->firstname.' '.$person->lastname),
                'email' => (string) $person->email,
                'places' => max(1, (int) $model->numberOfParticipants),
                'waiting' => $waiting,
                'attended' => (bool) $model->attended,
                'answers' => $answers,
            ];
        }

        return $rows;
    }
}
