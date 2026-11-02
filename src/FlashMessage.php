<?php

/**
 * events_subscriptions extension for Contao Open Source CMS
 *
 * @copyright Copyright (c) 2011-2017, Codefog
 * @author    Codefog <https://codefog.pl>
 * @license   http://opensource.org/licenses/lgpl-3.0.html LGPL
 * @link      http://github.com/codefog/contao-events_subscriptions
 */

namespace Codefog\EventsSubscriptionsBundle;

use Contao\System;
use Symfony\Component\HttpFoundation\Session\FlashBagAwareSessionInterface;

/**
 * Booking messages shown after the page reload, in the Symfony session's flash
 * bag (Contao 5 does not keep $_SESSION).
 */
class FlashMessage
{
    private string $sessionKey = 'event-subscribe-message';

    /**
     * Messages already read in this request, by event ID.
     */
    private array $messages = [];

    public function set($message, $id): void
    {
        unset($this->messages[$id]);

        if (null !== ($session = $this->getSession(true))) {
            $session->getFlashBag()->set($this->sessionKey.'-'.$id, (string) $message);
        }
    }

    public function puke($id): string|null
    {
        // Kept for the request, so every module of the page shows it (e.g. the event
        // reader and the booking form)
        return $this->messages[$id] ??= $this->getSession(false)?->getFlashBag()->get($this->sessionKey.'-'.$id)[0] ?? null;
    }

    /**
     * Only reads an existing session, so event pages do not start one for every visitor.
     */
    private function getSession(bool $start): FlashBagAwareSessionInterface|null
    {
        $request = System::getContainer()->get('request_stack')->getCurrentRequest();

        if (null === $request || !$request->hasSession()) {
            return null;
        }

        $session = $request->getSession();

        if (!$session instanceof FlashBagAwareSessionInterface || (!$start && !$session->isStarted() && !$request->hasPreviousSession())) {
            return null;
        }

        return $session;
    }
}
