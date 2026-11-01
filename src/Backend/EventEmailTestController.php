<?php

namespace Codefog\EventsSubscriptionsBundle\Backend;

use Codefog\EventsSubscriptionsBundle\DataContainer\EventEmailContainer;
use Codefog\EventsSubscriptionsBundle\Model\SubscriptionModel;
use Codefog\EventsSubscriptionsBundle\NotificationCenterHelper;
use Codefog\EventsSubscriptionsBundle\Services;
use Contao\BackendUser;
use Contao\Controller;
use Contao\Input;
use Contao\Message;
use Contao\System;
use Contao\Validator;
use Symfony\Component\Security\Csrf\CsrfToken;

/**
 * "Send me a test": sends an event e-mail to the logged-in user, filled with the details of the latest guest booking
 */
class EventEmailTestController
{
    public function run(): void
    {
        $container = System::getContainer();
        $backUrl = $container->get('router')->generate('contao_backend', ['do' => 'event_emails']);
        $L = $GLOBALS['TL_LANG']['tl_nc_notification'];

        if (!EventEmailContainer::canManage() || !$container->get('contao.csrf.token_manager')->isTokenValid(new CsrfToken($container->getParameter('contao.csrf_token_name'), (string) Input::get('rt')))) {
            Controller::redirect($backUrl);
        }

        $id = (int) Input::get('id');
        // The address from the pop-up, the user's own by default
        $email = trim((string) Input::get('email')) ?: (string) BackendUser::getInstance()->email;

        if ('' !== $email && !Validator::isEmail($email)) {
            Message::addError(sprintf($L['emailTestInvalidAddress'], $email));
            Controller::redirect($backUrl);
        }
        $language = (new EventEmailContainer())->getLanguage($id);

        if ('' === $email || null === $language) {
            Message::addError($L['emailTestNotPossible']);
            Controller::redirect($backUrl);
        }

        $booking = SubscriptionModel::findOneBy(['type=?'], ['guest'], ['order' => 'id DESC']);
        $tokens = $booking ? Services::getNotificationSender()->getTokensForSubscription($booking) : null;

        if (null === $tokens) {
            Message::addError($L['emailTestNotPossible']);
            Controller::redirect($backUrl);
        }

        $tokens['recipient_email'] = $tokens['subscription_email'] = $email;

        try {
            // Only to the address from the pop-up, never to the e-mail's other recipients, CC or BCC
            $container->get(NotificationCenterHelper::class)->sendTest($id, $tokens, $email);
            Message::addConfirmation(sprintf($L['emailTestSent'], $email, $booking->id));
        } catch (\Throwable $e) {
            Message::addError(sprintf($L['emailTestFailed'], $e->getMessage()));
        }

        Controller::redirect($backUrl);
    }
}
