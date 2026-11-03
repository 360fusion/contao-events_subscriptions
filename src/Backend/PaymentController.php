<?php

namespace Codefog\EventsSubscriptionsBundle\Backend;

use Codefog\EventsSubscriptionsBundle\Model\PaymentModel;
use Codefog\EventsSubscriptionsBundle\Payment\PaymentManager;
use Codefog\EventsSubscriptionsBundle\Payment\PaymentRequest;
use Contao\CalendarEventsModel;
use Contao\Controller;
use Contao\Input;
use Contao\Message;
use Contao\System;
use Symfony\Component\Security\Csrf\CsrfToken;

/**
 * Refund or mark as paid from the payments list
 */
class PaymentController
{
    public function run(): void
    {
        $container = System::getContainer();
        $backUrl = $container->get('router')->generate('contao_backend', ['do' => 'event_payments']);

        // The links change payments, so they need the request token
        if (!$container->get('contao.csrf.token_manager')->isTokenValid(new CsrfToken($container->getParameter('contao.csrf_token_name'), (string) Input::get('rt')))) {
            Controller::redirect($backUrl);
        }

        if (null === ($payment = PaymentModel::findByPk(Input::get('id')))) {
            Controller::redirect($backUrl);
        }

        // Refunds and mark-as-paid only for the calendars the user may work with (payments of deleted events: admins only)
        $event = CalendarEventsModel::findByPk($payment->event);
        Access::requireCalendar($event ? (int) $event->pid : 0);

        System::loadLanguageFile('tl_calendar_events_payment');
        $amount = PaymentRequest::format((int) $payment->amount, (string) $payment->currency);
        $manager = new PaymentManager();

        try {
            if ('payment_refund' === Input::get('key')) {
                $manager->refund($payment);
                Message::addConfirmation(sprintf($GLOBALS['TL_LANG']['tl_calendar_events_payment']['refunded'], $amount, $payment->name));
            } elseif ('payment_markPaid' === Input::get('key')) {
                $manager->markPaid($payment);
                Message::addConfirmation(sprintf($GLOBALS['TL_LANG']['tl_calendar_events_payment']['markedPaid'], $amount, $payment->name));
            }
        } catch (\Throwable $e) {
            Message::addError(sprintf($GLOBALS['TL_LANG']['tl_calendar_events_payment']['actionFailed'], $e->getMessage()));
        }

        Controller::redirect($backUrl);
    }
}
