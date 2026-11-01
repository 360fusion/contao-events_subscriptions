<?php

namespace Codefog\EventsSubscriptionsBundle\Widget;

use Codefog\EventsSubscriptionsBundle\Backend\PaymentTypes;
use Contao\Widget;

/**
 * Shows the payment types as tiles inside the settings form. It has nothing to submit.
 */
class PaymentTypesWidget extends Widget
{
    protected $blnSubmitInput = false;

    protected $strTemplate = 'be_widget';

    public function generate(): string
    {
        return PaymentTypes::html();
    }
}
