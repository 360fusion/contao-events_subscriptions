<?php

namespace Codefog\EventsSubscriptionsBundle\Payment;

final class Checkout
{
    /**
     * @param string $url       The provider's payment page
     * @param string $reference The provider's ID of the payment, used to check its status later
     */
    public function __construct(
        public readonly string $url,
        public readonly string $reference,
    ) {
    }
}
