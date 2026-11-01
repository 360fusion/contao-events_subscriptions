<?php

namespace Codefog\EventsSubscriptionsBundle\Backend;

use Contao\StringUtil;
use Contao\System;

/**
 * The Back button of the pages opened from Events › Settings: to the Settings page, or for the payment providers to the Payments page
 */
class BackLink
{
    public static function url(string $module = 'event_setup'): string
    {
        return System::getContainer()->get('router')->generate('contao_backend', ['do' => $module]);
    }

    public static function html(string $module = 'event_setup'): string
    {
        return '<a href="'.StringUtil::specialchars(self::url($module)).'" class="header_back" title="'.StringUtil::specialchars($GLOBALS['TL_LANG']['MSC']['backBTTitle']).'" accesskey="b">'.$GLOBALS['TL_LANG']['MSC']['backBT'].'</a>';
    }

    /**
     * Global operation of a list: back to the Settings page
     */
    public function toSettings(): string
    {
        return self::html();
    }

    /**
     * Global operation of a list: back to the Payments settings page
     */
    public function toPayments(): string
    {
        return self::html('event_settings_payments');
    }
}
