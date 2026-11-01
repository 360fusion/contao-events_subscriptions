<?php

namespace Codefog\EventsSubscriptionsBundle\Payment;

use Codefog\EventsSubscriptionsBundle\SubscriptionValidator;

/**
 * Decides how a new booking is paid
 */
final class PaymentDecision
{
    /**
     * Payment methods that can be offered: card needs a provider
     *
     * @param list<string> $methods
     *
     * @return list<string>
     */
    public static function availableMethods(int $price, array $methods, bool $hasProvider): array
    {
        if ($price <= 0) {
            return [];
        }

        return array_values(array_filter($methods, static fn ($method) => 'invoice' === $method || ('card' === $method && $hasProvider)));
    }

    /**
     * @param list<string> $availableMethods From availableMethods()
     * @param list<string> $freeEmailDomains
     *
     * @return string PaymentStatus::FREE, ::INVOICE, ::PENDING (pay by card now) or ::UNPAID (on the waiting list)
     */
    public static function decide(array $availableMethods, array $freeEmailDomains, string $email, string $chosenMethod, bool $onWaitingList): string
    {
        if (!$availableMethods || ($freeEmailDomains && (new SubscriptionValidator())->isEmailDomainAllowed($email, $freeEmailDomains))) {
            return PaymentStatus::FREE;
        }

        $method = \in_array($chosenMethod, $availableMethods, true) ? $chosenMethod : $availableMethods[0];

        if ('invoice' === $method) {
            return PaymentStatus::INVOICE;
        }

        return $onWaitingList ? PaymentStatus::UNPAID : PaymentStatus::PENDING;
    }
}
