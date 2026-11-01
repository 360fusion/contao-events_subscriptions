<?php

namespace Codefog\EventsSubscriptionsBundle\Payment;

/**
 * Payment states of a subscription (tl_calendar_events_subscription.paymentStatus)
 */
final class PaymentStatus
{
    public const FREE = 'free';
    public const PENDING = 'pending'; // card checkout started, the place is held for 30 minutes
    public const UNPAID = 'unpaid'; // on the waiting list, pays with the link in the promotion e-mail
    public const PAID = 'paid';
    public const INVOICE = 'invoice';
    public const REFUNDED = 'refunded';
    public const FAILED = 'failed';

    public const ALL = [self::FREE, self::PENDING, self::UNPAID, self::PAID, self::INVOICE, self::REFUNDED, self::FAILED];
}
