<?php

namespace Codefog\EventsSubscriptionsBundle\DataContainer;

use Codefog\EventsSubscriptionsBundle\Backend\Access;
use Codefog\EventsSubscriptionsBundle\Payment\PaymentRequest;
use Codefog\EventsSubscriptionsBundle\Payment\PaymentStatus;
use Codefog\EventsSubscriptionsBundle\Payment\ProviderFactory;
use Contao\Backend;
use Contao\Config;
use Contao\DataContainer;
use Contao\Database;
use Contao\Date;
use Contao\Image;
use Contao\Input;
use Contao\Message;
use Contao\StringUtil;
use Contao\System;
use Codefog\EventsSubscriptionsBundle\Payment\WebhookUrl;

class PaymentContainer
{
    /**
     * Only the payments of events in calendars the user may work with (admins see all)
     */
    public function restrictToUserCalendars(): void
    {
        if ($access = Access::calendarSql('e.pid')) {
            $ids = Database::getInstance()->query('SELECT p.id FROM tl_calendar_events_payment p JOIN tl_calendar_events e ON e.id=p.event WHERE '.$access)->fetchEach('id');
            $GLOBALS['TL_DCA']['tl_calendar_events_payment']['list']['sorting']['root'] = $ids ?: [0];
        }
    }

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
    /**
     * The providers to choose from: the ones switched on, and the one already chosen even if it is switched off
     */
    public function getProviderOptions(DataContainer|null $dc = null): array
    {
        System::loadLanguageFile('tl_calendar_events_payment_provider');

        $current = (int) ($dc?->activeRecord?->subscription_paymentProvider ?? 0);
        $options = [];

        foreach (Database::getInstance()->prepare('SELECT id, title, published FROM tl_calendar_events_payment_provider WHERE published=1 OR id=? ORDER BY title')->execute($current)->fetchAllAssoc() as $row) {
            $options[$row['id']] = $row['title'].($row['published'] ? '' : ' ('.$GLOBALS['TL_LANG']['tl_calendar_events_payment_provider']['inactive'].')');
        }

        return $options;
    }

    /**
     * A provider can only be made active once its account details are entered. The edit form sends every field, the list button (act=toggle)
     * sends none: then the saved record counts.
     */
    public function validateActive(mixed $value, DataContainer $dc): mixed
    {
        if (!$value) {
            return $value;
        }

        System::loadLanguageFile('tl_calendar_events_payment_provider');

        $row = Database::getInstance()->prepare('SELECT * FROM tl_calendar_events_payment_provider WHERE id=?')->execute($dc->id)->row();

        if ('tl_calendar_events_payment_provider' === Input::post('FORM_SUBMIT')) {
            foreach (['type', 'secretKey', 'clientId', 'clientSecret', 'apiKey'] as $field) {
                if (null !== ($posted = Input::postRaw($field))) {
                    $row[$field] = $posted;
                }
            }
        }

        if ([] !== ($missing = ProviderFactory::missingCredentials($row))) {
            $L = $GLOBALS['TL_LANG']['tl_calendar_events_payment_provider'];
            $message = sprintf($L['activeNeedsDetails'], implode(', ', array_map(static fn (string $field) => $L[$field][0] ?? $field, $missing)));

            // The list button has no field to show an error at: say it above the list and leave the provider off
            if ('toggle' === Input::get('act')) {
                Message::addError($message);

                return '';
            }

            throw new \RuntimeException($message);
        }

        return $value;
    }

    public function generateProviderLabel(array $row): string
    {
        $type = $GLOBALS['TL_LANG']['tl_calendar_events_payment_provider']['typeRef'][$row['type']] ?? $row['type'];

        return sprintf(
            '%s <span class="label-info">[%s]</span>%s%s<br><span class="tl_gray">%s: %s</span>',
            $row['title'],
            $type,
            $row['published'] ? '' : ' <span class="label-info">'.$GLOBALS['TL_LANG']['tl_calendar_events_payment_provider']['inactive'].'</span>',
            ProviderFactory::hasCredentials($row) ? '' : ' <span class="label-info">'.$GLOBALS['TL_LANG']['tl_calendar_events_payment_provider']['detailsMissing'].'</span>',
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
