<?php

namespace Codefog\EventsSubscriptionsBundle\Subscription;

use Codefog\EventsSubscriptionsBundle\EventConfig;
use Codefog\EventsSubscriptionsBundle\Model\SubscriptionModel;
use Codefog\EventsSubscriptionsBundle\Payment\PaymentRequest;
use Codefog\EventsSubscriptionsBundle\RegistrationFields;
use Codefog\EventsSubscriptionsBundle\Services;
use Codefog\HasteBundle\Form\Form;
use Codefog\HasteBundle\Util\ArrayPosition;
use Contao\CoreBundle\Exception\RedirectResponseException;
use Contao\Input;
use Contao\System;

class GuestSubscription extends AbstractSubscription implements ExportAwareInterface,  NotificationAwareInterface
{
    /**
     * @inheritDoc
     */
    public function canSubscribe(EventConfig $event)
    {
        return $this->getSubscriptionValidator()->canSubscribe($event);
    }

    /**
     * @inheritDoc
     */
    public function canUnsubscribe(EventConfig $event)
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function isSubscribed(EventConfig $event)
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function writeToModel(EventConfig $event, SubscriptionModel $model)
    {
        if (($form = $this->getForm($event)) === null) {
            return;
        }

        parent::writeToModel($event, $model);

        $model->firstname = $form->fetch('firstname');
        $model->lastname  = $form->fetch('lastname');
        $model->email     = $form->fetch('email');

        $registrationFields = RegistrationFields::fromDatabase();

        foreach ($registrationFields->resolve($event->get('subscription_fields')) as $field) {
            $name = $registrationFields->virtualFieldName($field['key']);
            $value = $form->fetch($name);

            // The front end checkbox submits an array
            $model->$name = 'checkbox' === $field['type'] ? ($value ? '1' : '') : $value;
        }
    }

    /**
     * @inheritDoc
     */
    public function createForm(EventConfig $event)
    {
        if (System::getContainer()->get('contao.security.token_checker')->hasFrontendUser() || !$this->canSubscribe($event)) {
            return false;
        }

        $form = $this->createBasicForm($event);
        $position = null;

        // Determine the fields position
        if ($form->hasFormField('enableReminders')) {
            $position = ArrayPosition::before('enableReminders');
        }

        // Determine the fields position
        if ($form->hasFormField('numberOfParticipants')) {
            $position = ArrayPosition::before('numberOfParticipants');
        }

        $form
            ->addFormField(
                'firstname',
                [
                    'label'     => &$GLOBALS['TL_LANG']['MSC']['events_subscriptions.guestForm.firstname'],
                    'inputType' => 'text',
                    'eval'      => ['mandatory' => true],
                ],
                $position
            )->addFormField(
                'lastname',
                [
                    'label'     => &$GLOBALS['TL_LANG']['MSC']['events_subscriptions.guestForm.lastname'],
                    'inputType' => 'text',
                    'eval'      => ['mandatory' => true],
                ],
                $position
            )->addFormField(
                'email',
                [
                    'label'     => &$GLOBALS['TL_LANG']['MSC']['events_subscriptions.guestForm.email'],
                    'inputType' => 'text',
                    'eval'      => ['mandatory' => true, 'rgxp' => 'email'],
                    'save_callback' => [
                        static function ($value) use ($event) {
                            $domains = $event->getAllowedEmailDomains();

                            if (!Services::getSubscriptionValidator()->isEmailDomainAllowed((string) $value, $domains)) {
                                throw new \RuntimeException(sprintf($GLOBALS['TL_LANG']['MSC']['events_subscriptions.emailDomainError'], implode(', ', $domains)));
                            }

                            if ($event->hasUniqueGuestEmails() && SubscriptionModel::countBy(['pid=?', 'type=?', 'email=?'], [$event->getEvent()->id, 'guest', $value]) > 0) {
                                throw new \RuntimeException($GLOBALS['TL_LANG']['MSC']['events_subscriptions.emailUniqueError']);
                            }

                            return $value;
                        },
                    ],
                ],
                $position
            );

        $registrationFields = RegistrationFields::fromDatabase();

        foreach ($registrationFields->resolve($event->get('subscription_fields')) as $field) {
            $form->addFormField($registrationFields->virtualFieldName($field['key']), $registrationFields->formFieldConfig($field), $position);
        }

        $this->addPaymentFields($form, $event, $position);

        $form
            ->addFormField(
                'captcha_' . $event->getEvent()->id,
                [
                    'label'     => &$GLOBALS['TL_LANG']['MSC']['securityQuestion'],
                    'inputType' => 'captcha',
                    'eval'      => ['mandatory' => true],
                ]
            );

        return $form;
    }

    /**
     * The payment method chosen in the form ("card" or "invoice"), empty if there was no choice
     */
    public function getPaymentMethodChoice(): string
    {
        return $this->form instanceof Form && $this->form->hasFormField('paymentMethod') ? (string) $this->form->fetch('paymentMethod') : '';
    }

    /**
     * The page to show after a successful payment (the module's subscribe redirect page)
     */
    public function getSuccessPageId(): int
    {
        return (int) ($this->moduleData['jumpTo_subscribe'] ?? 0);
    }

    public function getPurchaseOrder(): string
    {
        return $this->form instanceof Form && $this->form->hasFormField('purchaseOrder') ? trim((string) $this->form->fetch('purchaseOrder')) : '';
    }

    /**
     * Price, payment method and purchase order fields of paid events
     */
    private function addPaymentFields(Form $form, EventConfig $event, $position): void
    {
        if (!($methods = $event->getAvailablePaymentMethods())) {
            return;
        }

        System::loadLanguageFile('tl_calendar_events');

        $price = sprintf($GLOBALS['TL_LANG']['MSC']['events_subscriptions.price'], PaymentRequest::format($event->getPrice(), $event->getPaymentProvider()?->currency ?: 'GBP', str_replace('-', '_', $GLOBALS['TL_LANGUAGE'] ?? 'en')));

        if ($freeDomains = $event->getFreeEmailDomains()) {
            $price .= ' '.sprintf($GLOBALS['TL_LANG']['MSC']['events_subscriptions.priceFreeFor'], implode(', ', $freeDomains));
        }

        $form->addFormField('paymentInfo', ['inputType' => 'explanation', 'eval' => ['text' => '<p class="price">'.$price.'</p>']], $position);

        if (\count($methods) > 1) {
            $form->addFormField('paymentMethod', [
                'label' => &$GLOBALS['TL_LANG']['MSC']['events_subscriptions.paymentMethod'],
                'inputType' => 'radio',
                'options' => array_combine($methods, array_map(static fn ($method) => $GLOBALS['TL_LANG']['tl_calendar_events']['subscription_paymentMethodsRef'][$method] ?? $method, $methods)),
                'default' => $methods[0],
                'eval' => ['mandatory' => true],
            ], $position);
        }

        if (\in_array('invoice', $methods, true)) {
            $form->addFormField('purchaseOrder', [
                'label' => &$GLOBALS['TL_LANG']['MSC']['events_subscriptions.purchaseOrder'],
                'inputType' => 'text',
                'eval' => ['maxlength' => 255],
                'save_callback' => [
                    static function ($value) use ($event, $methods) {
                        $paysByInvoice = 'invoice' === (Input::post('paymentMethod') ?: $methods[0]);
                        $isFree = $event->getFreeEmailDomains() && Services::getSubscriptionValidator()->isEmailDomainAllowed((string) Input::post('email'), $event->getFreeEmailDomains());

                        // Required when paying by invoice
                        if ($paysByInvoice && !$isFree && '' === trim((string) $value)) {
                            throw new \RuntimeException($GLOBALS['TL_LANG']['MSC']['events_subscriptions.purchaseOrderRequired']);
                        }

                        return $value;
                    },
                ],
            ], $position);
        }
    }

    /**
     * @inheritDoc
     */
    public function processForm(Form $form, EventConfig $event)
    {
        if ($this->canSubscribe($event)) {
            // Validate the number of participants
            if (!$this->validateNumberOfParticipants($form, $event)) {
                return;
            }

            try {
                $this->getSubscriber()->subscribe($event, $this);
            } catch (RedirectResponseException $e) {
                throw $e; // to the payment page
            } catch (\Throwable $e) {
                // The payment provider could not be reached, the booking was removed again
                System::getContainer()->get('monolog.logger.contao')->error('Payment checkout failed: '.$e->getMessage());
                $this->throwRedirectException(null, $GLOBALS['TL_LANG']['MSC']['events_subscriptions.paymentUnavailable'], $event->getEvent()->id);
            }

            $this->throwRedirectException(
                'subscribe',
                $GLOBALS['TL_LANG']['MSC']['events_subscriptions.subscribeConfirmation'],
                $event->getEvent()->id
            );
        }

        $this->throwRedirectException();
    }

    /**
     * @inheritDoc
     */
    public function setUnsubscribeCriteria(EventConfig $event, array &$columns, array &$values)
    {
        $columns[] = 'unsubscribeToken=?';
        $values[] = $this->subscriptionModel->unsubscribeToken;
    }

    /**
     * @inheritDoc
     */
    public function getExportColumns()
    {
        return RegistrationFields::fromDatabase()->exportColumns();
    }

    /**
     * @inheritDoc
     */
    public function getExportRow()
    {
        return array_merge([
            'subscription_firstname' => $this->subscriptionModel->firstname,
            'subscription_lastname'  => $this->subscriptionModel->lastname,
            'subscription_email'     => $this->subscriptionModel->email,
            'subscription_numberOfParticipants' => $this->subscriptionModel->numberOfParticipants,
        ], $this->getRegistrationFieldValues());
    }

    /**
     * @inheritDoc
     */
    public function getBackendLabel()
    {
        $label = sprintf(
            '%s %s <span style="padding-left:3px;color:#b3b3b3;">[%s]</span> <span style="padding-left:3px;color:#b3b3b3;">[%s: %s]</span>',
            $this->subscriptionModel->firstname,
            $this->subscriptionModel->lastname,
            $this->subscriptionModel->email,
            $GLOBALS['TL_LANG']['MSC']['events_subscriptions.numberOfParticipants'],
            $this->subscriptionModel->numberOfParticipants
        );

        if ($this->isOnWaitingList()) {
            $label = sprintf(
                '%s <strong class="tl_red">[%s]</strong>',
                $label,
                $GLOBALS['TL_LANG']['MSC']['events_subscriptions.onWaitingList']
            );
        }

        return $label;
    }

    /**
     * @inheritDoc
     */
    public function getFrontendLabel()
    {
        $label = sprintf('%s %s', $this->subscriptionModel->firstname, $this->subscriptionModel->lastname);

        if (isset($this->moduleData['cal_showParticipants']) && $this->moduleData['cal_showParticipants']) {
            $label = sprintf('%s (%s)', $label, $this->subscriptionModel->numberOfParticipants);
        }

        return $label;
    }

    /**
     * @inheritDoc
     */
    public function getNotificationEmail()
    {
        return $this->subscriptionModel->email;
    }

    /**
     * @inheritDoc
     */
    public function getNotificationTokens()
    {
        return [
            'subscription_firstname' => $this->subscriptionModel->firstname,
            'subscription_lastname'  => $this->subscriptionModel->lastname,
            'subscription_email'     => $this->subscriptionModel->email,
            'subscription_numberOfParticipants' => $this->subscriptionModel->numberOfParticipants,
            'subscription_details'   => $this->getDetails(),
            'unsubscribe_link'       => $this->generateUnsubscribeLink(),
        ] + $this->getRegistrationFieldValues();
    }

    /**
     * Everything the booking form asked for this event, including registration fields added later
     */
    private function getDetails(): string
    {
        System::loadLanguageFile('tl_calendar_events_subscription');

        $L = $GLOBALS['TL_LANG']['tl_calendar_events_subscription'];
        $event = EventConfig::create($this->subscriptionModel->pid);
        $answers = [
            [$L['firstname'][0], $this->subscriptionModel->firstname],
            [$L['lastname'][0], $this->subscriptionModel->lastname],
            [$L['email'][0], $this->subscriptionModel->email],
        ];

        if ($event->canProvideNumberOfParticipants()) {
            $answers[] = [$GLOBALS['TL_LANG']['MSC']['events_subscriptions.numberOfParticipants'], $this->subscriptionModel->numberOfParticipants];
        }

        $registrationFields = RegistrationFields::fromDatabase();

        foreach ($registrationFields->resolve($event->get('subscription_fields')) as $field) {
            $answers[] = [$field['label'], $registrationFields->formatValue($field, $this->subscriptionModel->{$registrationFields->virtualFieldName($field['key'])})];
        }

        return $this->formatDetails($answers);
    }

    /**
     * Get the formatted registration field answers, keyed by token/column name
     */
    private function getRegistrationFieldValues(): array
    {
        $registrationFields = RegistrationFields::fromDatabase();
        $values = [];

        foreach ($registrationFields->allFields() as $field) {
            $values['subscription_field_'.$field['key']] = $registrationFields->formatValue($field, $this->subscriptionModel->{$registrationFields->virtualFieldName($field['key'])});
        }

        return $values;
    }
}
