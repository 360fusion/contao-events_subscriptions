<?php

namespace Codefog\EventsSubscriptionsBundle\DataContainer;

use Codefog\EventsSubscriptionsBundle\Payment\PaymentRequest;
use Codefog\EventsSubscriptionsBundle\Payment\PaymentStatus;
use Contao\Backend;
use Contao\Config;
use Contao\DataContainer;
use Contao\Date;
use Contao\Image;
use Contao\StringUtil;
use Codefog\EventsSubscriptionsBundle\Payment\WebhookUrl;

class PaymentContainer
{
    /**
     * Show the price in pounds, it is stored in pence
     */
    public function loadPrice($value): string
    {
        return $value ? PaymentRequest::decimal((int) $value) : '';
    }

    public function savePrice($value): int
    {
        return (int) round((float) str_replace(',', '.', (string) $value) * 100);
    }

    /**
     * Show the provider type and the webhook URL to enter at the provider
     */
    public function generateProviderLabel(array $row): string
    {
        $type = $GLOBALS['TL_LANG']['tl_calendar_events_payment_provider']['typeRef'][$row['type']] ?? $row['type'];

        return sprintf(
            '%s <span class="label-info">[%s]</span><br><span class="tl_gray">%s: %s</span>',
            $row['title'],
            $type,
            $GLOBALS['TL_LANG']['tl_calendar_events_payment_provider']['webhookUrl'],
            WebhookUrl::generate((int) $row['id']),
        );
    }

    public function generatePaymentLabel(array $row, string $label, DataContainer $dc, array $columns): array
    {
        $columns[0] = Date::parse(Config::get('datimFormat'), $row['dateCreated']);
        $columns[1] = $row['name'].'<br><span class="tl_gray">'.$row['email'].($row['purchaseOrder'] ? ' · PO '.$row['purchaseOrder'] : '').'</span>';
        $columns[3] = PaymentRequest::format((int) $row['amount'], (string) $row['currency']).'<br><span class="tl_gray">'.($GLOBALS['TL_LANG']['tl_calendar_events']['subscription_paymentMethodsRef'][$row['method']] ?? $row['method']).'</span>';
        $columns[4] = $GLOBALS['TL_LANG']['MSC']['events_subscriptions.paymentStatus'][$row['status']] ?? $row['status'];

        return $columns;
    }

    /**
     * Refund paid payments (card payments through the provider)
     */
    public function refundButton(array $row, string|null $href, string $label, string $title, string|null $icon, string $attributes): string
    {
        if (PaymentStatus::PAID !== $row['status']) {
            return '';
        }

        $confirm = sprintf($GLOBALS['TL_LANG']['tl_calendar_events_payment']['refundConfirm'], PaymentRequest::format((int) $row['amount'], (string) $row['currency']), $row['name']);

        return sprintf('<a href="%s" title="%s" onclick="if(!confirm(\'%s\'))return false">%s</a> ', Backend::addToUrl($href.'&amp;id='.$row['id']), StringUtil::specialchars($title), StringUtil::specialchars(addslashes($confirm)), Image::getHtml($icon, $label));
    }

    /**
     * Invoices and unpaid payments can be marked as paid by hand
     */
    public function markPaidButton(array $row, string|null $href, string $label, string $title, string|null $icon, string $attributes): string
    {
        if (!\in_array($row['status'], [PaymentStatus::INVOICE, PaymentStatus::UNPAID], true)) {
            return '';
        }

        return sprintf('<a href="%s" title="%s">%s</a> ', Backend::addToUrl($href.'&amp;id='.$row['id']), StringUtil::specialchars($title), Image::getHtml($icon, $label));
    }
}
