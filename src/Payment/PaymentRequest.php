<?php

namespace Codefog\EventsSubscriptionsBundle\Payment;

final class PaymentRequest
{
    /**
     * @param int $amount Amount in the minor unit (pence)
     */
    public function __construct(
        public readonly int $amount,
        public readonly string $currency,
        public readonly string $description,
        public readonly string $bookingToken,
        public readonly string $returnUrl,
        public readonly string $webhookUrl,
        public readonly string $email = '',
    ) {
    }

    /**
     * The amount as decimal string, e.g. "12.50"
     */
    public function decimalAmount(): string
    {
        return self::decimal($this->amount);
    }

    public static function decimal(int $amount): string
    {
        return sprintf('%d.%02d', intdiv($amount, 100), $amount % 100);
    }

    /**
     * The amount for people, e.g. "£12.50"
     */
    public static function format(int $amount, string $currency, string $locale = 'en_GB'): string
    {
        if (class_exists(\NumberFormatter::class) && false !== ($formatted = (new \NumberFormatter($locale, \NumberFormatter::CURRENCY))->formatCurrency($amount / 100, strtoupper($currency)))) {
            return $formatted;
        }

        return self::decimal($amount).' '.strtoupper($currency);
    }
}
