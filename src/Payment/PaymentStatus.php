<?php

namespace Codefog\EventsSubscriptionsBundle\Payment;

/**
 * Payment states of a subscription (tl_calendar_events_subscription.paymentStatus)
 */
final class PaymentStatus
{
    public const FREE = 'free';
    public const PENDING = 'pending'; // card checkout started, the place is held for the hold time of Booking settings › Payments, 30 minutes by default
    public const UNPAID = 'unpaid'; // on the waiting list, pays with the link in the promotion e-mail
    public const CONFIRM = 'confirm'; // free e-mail domain: the place is held until the booker confirms the address with the link in the e-mail
    public const PAID = 'paid';
    public const INVOICE = 'invoice';
    public const REFUNDED = 'refunded';
    public const FAILED = 'failed';

    public const ALL = [self::FREE, self::PENDING, self::UNPAID, self::CONFIRM, self::PAID, self::INVOICE, self::REFUNDED, self::FAILED];
}
