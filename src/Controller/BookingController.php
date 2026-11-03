<?php

namespace Codefog\EventsSubscriptionsBundle\Controller;

use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Codefog\EventsSubscriptionsBundle\Backend\Access;
use Codefog\EventsSubscriptionsBundle\Booking\FieldForm;
use Codefog\EventsSubscriptionsBundle\Booking\Links;
use Codefog\EventsSubscriptionsBundle\Payment\PaymentStatus;
use Codefog\EventsSubscriptionsBundle\EventDispatcher;
use Codefog\EventsSubscriptionsBundle\Event\SubscribeEvent;
use Codefog\EventsSubscriptionsBundle\Booking\Page;
use Codefog\EventsSubscriptionsBundle\EventConfig;
use Codefog\EventsSubscriptionsBundle\Features;
use Codefog\EventsSubscriptionsBundle\Model\SubscriptionModel;
use Codefog\EventsSubscriptionsBundle\RegistrationFields;
use Codefog\EventsSubscriptionsBundle\Services;
use Contao\CalendarEventsModel;
use Contao\Config;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\Database;
use Contao\Date;
use Contao\System;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * The booker's own pages. The secret token of the booking, from the e-mails, is the key; nothing here needs a login except the check-in.
 */
#[Route('/_events_subscriptions', defaults: ['_scope' => 'frontend'])]
class BookingController
{
    private const TOKEN = ['token' => '[a-f0-9]{32}'];

    public function __construct(private readonly ContaoFramework $framework)
    {
    }

    /**
     * Manage my booking: see it, show the check-in code, change the details, cancel
     */
    #[Route('/booking/{token}', name: Links::BOOKING, requirements: self::TOKEN, methods: ['GET', 'POST'])]
    public function booking(Request $request, string $token): Response
    {
        if (null === ($booking = $this->load($token))) {
            return $this->notFound();
        }

        [$model, $event, $config, $subscription] = $booking;
        $L = $GLOBALS['TL_LANG']['tl_calendar_events_subscription'];
        $guest = 'guest' === $model->type;
        $fields = $guest ? RegistrationFields::fromDatabase()->resolve($config->get('subscription_fields')) : [];
        $waiting = (bool) $subscription->isOnWaitingList();
        // The booking's own cancel rules: before the calendar's "unsubscribe until" time (a guest has no front end cancel button, so the subscription's canUnsubscribe() is always false)
        $canCancel = Services::getSubscriptionValidator()->canUnsubscribe($config);
        $errors = [];
        $posted = null;

        if ($request->isMethod('POST')) {
            $action = (string) $request->request->get('action');

            if ('cancel' === $action && $canCancel) {
                Services::getSubscriber()->unsubscribe($config, $subscription);

                return Page::render(Page::t('bookingTitle'), '<div class="notice"><p>'.Page::e(Page::t('cancelled', $event->title)).'</p></div>');
            }

            if ('save' === $action && $guest) {
                // No markup in names: the back end lists show them
                // Once someone has attended, the name on their certificate stays as it was
                $posted = $model->attended
                    ? ['firstname' => (string) $model->firstname, 'lastname' => (string) $model->lastname]
                    : ['firstname' => trim(strip_tags((string) $request->request->get('firstname'))), 'lastname' => trim(strip_tags((string) $request->request->get('lastname')))];

                foreach ($posted as $name => $value) {
                    if ('' === $value) {
                        $errors[$name] = Page::t('required', $L[$name][0]);
                    } elseif (mb_strlen($value) > 255) {
                        $errors[$name] = Page::t('invalid', $L[$name][0]);
                    }
                }

                [$answers, $fieldErrors] = FieldForm::read($fields, $request->request->all());
                $errors += $fieldErrors;

                if (!$errors) {
                    $registrationFields = RegistrationFields::fromDatabase();

                    $model->firstname = $posted['firstname'];
                    $model->lastname = $posted['lastname'];

                    foreach ($fields as $field) {
                        $model->{$registrationFields->virtualFieldName($field['key'])} = $answers[$field['key']];
                    }

                    $model->tstamp = time();
                    $model->save();

                    return new RedirectResponse($request->getPathInfo().'?saved=1');
                }

                $posted += $answers;
            }
        }

        $body = '';

        if ($request->query->get('saved')) {
            $body .= '<div class="notice"><p>'.Page::e(Page::t('saved')).'</p></div>';
        }

        $registrationFields = RegistrationFields::fromDatabase();
        $answers = [];

        foreach ($fields as $field) {
            $answers[$field['key']] = $posted[$field['key']] ?? $model->{$registrationFields->virtualFieldName($field['key'])};
        }

        $payment = (string) $model->paymentStatus;
        $status = $waiting ? Page::t('statusWaiting') : Page::t('statusBooked');

        if (!\in_array($payment, ['', 'free'], true)) {
            $status .= ' · '.($GLOBALS['TL_LANG']['MSC']['events_subscriptions.paymentStatus'][$payment] ?? $payment);
        }

        $body .= '<div class="card"><dl>'
            .'<dt>'.Page::e(Page::t('event')).'</dt><dd>'.Page::e($event->title).'</dd>'
            .'<dt>'.Page::e(Page::t('date')).'</dt><dd>'.Page::e($this->when($event)).'</dd>'
            .($event->location ? '<dt>'.Page::e(Page::t('place')).'</dt><dd>'.Page::e($event->location).'</dd>' : '')
            .'<dt>'.Page::e(Page::t('status')).'</dt><dd>'.Page::e($status).'</dd>'
            .'</dl></div>';

        // Check-in code, certificate
        if (!$waiting && Features::attendance()) {
            if ($model->attended) {
                $body .= '<div class="notice"><p>'.Page::e(Page::t('attendedNotice')).'</p>'
                    .(Features::certificates() ? '<p><a class="button" href="'.Page::e(Links::path(Links::CERTIFICATE, $model->unsubscribeToken)).'">'.Page::e(Page::t('certificateOpen')).'</a></p>' : '')
                    .'</div>';
            } elseif (max((int) $event->startTime, (int) $event->endTime) >= strtotime('today')) {
                $body .= '<div class="card qr"><h2>'.Page::e(Page::t('checkinTitle')).'</h2>'
                    .'<div role="img" aria-label="'.Page::e(Page::t('checkinTitle')).'">'.$this->qrCode(Links::absolute(Links::CHECKIN, $token)).'</div>'
                    .'<p class="muted">'.Page::e(Page::t('checkinHelp')).'</p></div>';
            }
        }

        if ($guest) {
            $body .= '<h2>'.Page::e(Page::t('edit')).'</h2><form class="card" method="post">'.$this->hidden('save')
                .'<label for="firstname">'.Page::e($L['firstname'][0]).' *</label><input type="text" id="firstname" name="firstname" maxlength="255" value="'.Page::e($posted['firstname'] ?? $model->firstname).'">'.$this->error($errors, 'firstname')
                .'<label for="lastname">'.Page::e($L['lastname'][0]).' *</label><input type="text" id="lastname" name="lastname" maxlength="255" value="'.Page::e($posted['lastname'] ?? $model->lastname).'">'.$this->error($errors, 'lastname')
                .'<label for="email">'.Page::e($L['email'][0]).'</label><input type="email" id="email" value="'.Page::e($model->email).'" disabled><p class="muted">'.Page::e(Page::t('emailLocked')).'</p>'
                .FieldForm::render($fields, $answers, $errors)
                .'<div class="actions"><button type="submit">'.Page::e(Page::t('save')).'</button></div></form>';
        }

        if ($canCancel) {
            $body .= '<h2>'.Page::e(Page::t('cancel')).'</h2><form class="card" method="post" onsubmit="return confirm('.Page::e(json_encode(Page::t('cancelConfirm'))).')">'.$this->hidden('cancel')
                .('paid' === $payment ? '<p>'.Page::e(Page::t('cancelPaid')).'</p>' : '')
                .'<button type="submit" class="danger">'.Page::e(Page::t('cancel')).'</button></form>';
        } else {
            $body .= '<p class="muted">'.Page::e(Page::t('cancelClosed')).'</p>';
        }

        return Page::render(Page::t('bookingTitle'), $body);
    }

    /**
     * The certificate of attendance: printable, for people marked as attended
     */
    #[Route('/certificate/{token}', name: Links::CERTIFICATE, requirements: self::TOKEN, methods: ['GET'])]
    public function certificate(string $token): Response
    {
        if (null === ($booking = $this->load($token))) {
            return $this->notFound();
        }

        [$model, $event] = $booking;

        if (!Features::certificates() || !$model->attended) {
            return Page::render(Page::t('certificateTitle'), '<div class="notice warning"><p>'.Page::e(Page::t('certificateNone')).'</p></div>', Response::HTTP_NOT_FOUND);
        }

        $member = $model->getMember();
        $name = trim(($member ?? $model)->firstname.' '.($member ?? $model)->lastname);
        $group = $event->eventGroup ? Database::getInstance()->prepare('SELECT title FROM tl_calendar_event_group WHERE id=?')->execute($event->eventGroup)->title : null;
        $organisation = (string) Config::get('eventsSubscriptionsCertificateOrganisation');
        $signedBy = (string) Config::get('eventsSubscriptionsCertificateSignedBy');
        $hours = $this->hours($event);

        $head = '<style>@page{size:A4 landscape;margin:0}body{background:#fff}main{max-width:none;min-height:100vh;display:flex;flex-direction:column;justify-content:center;align-items:center;text-align:center;border:14px double #005eb8;padding:48px}'
            .'main>.site,main>h1{display:none}.cert-name{font-size:2.6rem;margin:.3em 0;border-bottom:2px solid #212b32;padding:0 1.5em}.cert-course{font-size:1.7rem;font-weight:700;margin:.2em 0}.cert-title{font-size:2.2rem;color:#005eb8;margin:.2em 0 1em}</style>';
        $body = ($organisation ? '<p class="muted" style="font-size:1.3rem">'.Page::e($organisation).'</p>' : '')
            .'<p class="cert-title">'.Page::e(Page::t('certificateTitle')).'</p>'
            .'<p>'.Page::e(Page::t('certificateCertify')).'</p><p class="cert-name">'.Page::e($name).'</p>'
            .'<p>'.Page::e(Page::t('certificateAttended')).'</p><p class="cert-course">'.Page::e($group ?: $event->title).'</p>'
            .'<p>'.Page::e($this->when($event)).($event->location ? ' · '.Page::e($event->location) : '').($hours ? ' · '.Page::e(Page::t('certificateHours', $hours)) : '').'</p>'
            .($group ? '<p class="muted">'.Page::e($event->title).'</p>' : '')
            .'<p class="muted" style="margin-top:2.5em">'.($signedBy ? Page::e($signedBy).' · ' : '').Page::e(Page::t('certificateIssued', Date::parse(Config::get('dateFormat'), time()))).' · '.Page::e('#'.$model->id).'</p>'
            .'<p class="noprint"><button type="button" onclick="window.print()">'.Page::e(Page::t('certificatePrint')).'</button></p>';

        return Page::render(Page::t('certificateTitle'), $body, Response::HTTP_OK, $head);
    }

    /**
     * Feedback after the event: a rating and a comment, one answer for each booking, no name stored
     */
    #[Route('/feedback/{token}', name: Links::FEEDBACK, requirements: self::TOKEN, methods: ['GET', 'POST'])]
    public function feedback(Request $request, string $token): Response
    {
        if (null === ($booking = $this->load($token))) {
            return $this->notFound();
        }

        [$model, $event, , $subscription] = $booking;
        $title = Page::t('feedbackTitle');
        $db = Database::getInstance();

        if ($db->prepare('SELECT id FROM tl_calendar_events_feedback WHERE subscription=?')->execute($model->id)->numRows) {
            return Page::render($title, '<div class="notice"><p>'.Page::e(Page::t('feedbackThanks')).'</p></div>');
        }

        if ((int) $event->startTime > time() || $subscription->isOnWaitingList()) {
            return Page::render($title, '<div class="notice warning"><p>'.Page::e(Page::t('feedbackNotYet')).'</p></div>');
        }

        $error = '';

        if ($request->isMethod('POST')) {
            $rating = (int) $request->request->get('rating');

            if ($rating < 1 || $rating > 5) {
                $error = Page::t('feedbackRatingMissing');
            } else {
                $db->prepare('INSERT INTO tl_calendar_events_feedback %s')->set([
                    'pid' => $event->id,
                    'tstamp' => time(),
                    'subscription' => $model->id,
                    'rating' => $rating,
                    'comment' => mb_substr(trim((string) $request->request->get('comment')), 0, 2000),
                ])->execute();

                return Page::render($title, '<div class="notice"><p>'.Page::e(Page::t('feedbackThanks')).'</p></div>');
            }
        }

        $stars = '';

        foreach (range(5, 1) as $value) {
            $stars .= '<label class="inline"><input type="radio" name="rating" value="'.$value.'"> '.Page::e(Page::t('rating'.$value)).'</label><br>';
        }

        $body = '<p>'.Page::e($event->title).'</p><form class="card" method="post">'.$this->hidden('')
            .'<label>'.Page::e(Page::t('feedbackRating')).' *</label>'.$stars.($error ? '<p class="error">'.Page::e($error).'</p>' : '')
            .'<label for="comment">'.Page::e(Page::t('feedbackComment')).'</label><textarea id="comment" name="comment" maxlength="2000"></textarea>'
            .'<div class="actions"><button type="submit">'.Page::e(Page::t('feedbackSend')).'</button></div></form>';

        return Page::render($title, $body);
    }

    /**
     * Confirm the e-mail address of a booking with a free e-mail domain. A page with a button, so that link checkers and
     * previews that open the e-mail's link cannot confirm for the booker.
     */
    #[Route('/confirm/{token}', name: Links::CONFIRM, requirements: self::TOKEN, methods: ['GET', 'POST'])]
    public function confirm(Request $request, string $token): Response
    {
        if (null === ($booking = $this->load($token))) {
            return $this->notFound();
        }

        [$model, $event, $config, $subscription] = $booking;
        $title = Page::t('confirmTitle');
        $card = '<dl class="card"><dt>'.Page::e(Page::t('event')).'</dt><dd>'.Page::e($event->title).'</dd><dt>'.Page::e(Page::t('date')).'</dt><dd>'.Page::e($this->when($event)).'</dd></dl>';

        if (PaymentStatus::CONFIRM !== $model->paymentStatus) {
            return Page::render($title, $card.'<div class="notice"><p>'.Page::e(Page::t('confirmAlready')).'</p></div>');
        }

        if ($request->isMethod('POST')) {
            $model->paymentStatus = PaymentStatus::FREE;
            $model->tstamp = time();
            $model->save();

            // Now the booking is real: the usual confirmation e-mail
            $dispatch = new SubscribeEvent($model, $subscription);
            $dispatch->setExtras($config->getExtras());
            Services::getEventDispatcher()->dispatch(EventDispatcher::EVENT_ON_SUBSCRIBE, $dispatch);

            return Page::render($title, $card.'<div class="notice"><p>'.Page::e(Page::t('confirmDone')).'</p></div>');
        }

        return Page::render($title, $card.'<p>'.Page::e(Page::t('confirmIntro')).'</p><form method="post">'.$this->hidden('').'<div class="actions"><button type="submit">'.Page::e(Page::t('confirmButton')).'</button></div></form>');
    }

    /**
     * Check-in, from the QR code of the booking page. Only for people signed in to the back end: otherwise anyone could check themselves in from home.
     */
    #[Route('/checkin/{token}', name: Links::CHECKIN, requirements: self::TOKEN, methods: ['GET', 'POST'])]
    public function checkin(Request $request, string $token): Response
    {
        if (null === ($booking = $this->load($token))) {
            return $this->notFound();
        }

        $title = Page::t('checkinPage');

        if (!System::getContainer()->get('contao.security.token_checker')->hasBackendUser()) {
            return Page::render($title, '<div class="notice warning"><p>'.Page::e(Page::t('checkinStaffOnly')).'</p></div><p><a class="button" href="'.Page::e(System::getContainer()->get('router')->generate('contao_backend_login')).'">'.Page::e(Page::t('signIn')).'</a></p>', Response::HTTP_FORBIDDEN);
        }

        [$model, $event, , $subscription] = $booking;

        // Only staff who may work with this calendar
        if (!Access::calendar((int) $event->pid)) {
            return Page::render($title, '<div class="notice warning"><p>'.Page::e(Page::t('checkinStaffOnly')).'</p></div>', Response::HTTP_FORBIDDEN);
        }

        $member = $model->getMember();
        $name = trim(($member ?? $model)->firstname.' '.($member ?? $model)->lastname);
        $head = '<dl class="card"><dt>'.Page::e(Page::t('name')).'</dt><dd><strong>'.Page::e($name).'</strong></dd><dt>'.Page::e(Page::t('event')).'</dt><dd>'.Page::e($event->title).'</dd><dt>'.Page::e(Page::t('date')).'</dt><dd>'.Page::e($this->when($event)).'</dd></dl>';

        if ($subscription->isOnWaitingList()) {
            return Page::render($title, $head.'<div class="notice warning"><p>'.Page::e(Page::t('checkinWaiting')).'</p></div>');
        }

        if ($model->attended) {
            return Page::render($title, $head.'<div class="notice"><p>'.Page::e(Page::t('checkinAlready', $name)).'</p></div>');
        }

        if ($request->isMethod('POST')) {
            $model->attended = '1';
            $model->tstamp = time();
            $model->save();

            return Page::render($title, $head.'<div class="notice"><p>'.Page::e(Page::t('checkinDone', $name)).'</p></div>');
        }

        $notices = '';

        if (date('Y-m-d', (int) $event->startTime) > date('Y-m-d') || max((int) $event->startTime, (int) $event->endTime) < strtotime('today')) {
            $notices .= '<div class="notice warning"><p>'.Page::e(Page::t('checkinWrongDay', $this->when($event))).'</p></div>';
        }

        return Page::render($title, $head.$notices.'<form method="post">'.$this->hidden('').'<button type="submit">'.Page::e(Page::t('checkinButton')).'</button></form>');
    }

    /**
     * @return array{0: SubscriptionModel, 1: CalendarEventsModel, 2: EventConfig, 3: \Codefog\EventsSubscriptionsBundle\Subscription\SubscriptionInterface}|null
     */
    private function load(string $token): array|null
    {
        $this->framework->initialize();
        System::loadLanguageFile('default');
        System::loadLanguageFile('tl_calendar_events_subscription');

        if (null === ($model = SubscriptionModel::findOneBy('unsubscribeToken', $token)) || null === ($event = $model->getEvent())) {
            return null;
        }

        try {
            return [$model, $event, Services::getEventConfigFactory()->create((int) $model->pid), Services::getSubscriptionFactory()->createFromModel($model)];
        } catch (\InvalidArgumentException) {
            return null;
        }
    }

    private function notFound(): Response
    {
        return Page::render(Page::t('bookingTitle'), '<div class="notice warning"><p>'.Page::e(Page::t('notFound')).'</p></div>', Response::HTTP_NOT_FOUND);
    }

    private function hidden(string $action): string
    {
        return ($action ? '<input type="hidden" name="action" value="'.$action.'">' : '')
            .'<input type="hidden" name="REQUEST_TOKEN" value="'.Page::e(System::getContainer()->get('contao.csrf.token_manager')->getDefaultTokenValue()).'">';
    }

    private function error(array $errors, string $key): string
    {
        return isset($errors[$key]) ? '<p class="error">'.Page::e($errors[$key]).'</p>' : '';
    }

    private function qrCode(string $url): string
    {
        $svg = (new Writer(new ImageRenderer(new RendererStyle(240, 1), new SvgImageBackEnd())))->writeString($url);

        return preg_replace('/^<\?xml[^>]*\?>\s*/', '', $svg);
    }

    private function when(CalendarEventsModel $event): string
    {
        $start = (int) $event->startTime;
        $end = (int) $event->endTime;

        if ($event->addTime) {
            $text = Date::parse(Config::get('datimFormat'), $start);

            if ($end > $start) {
                $text .= ' – '.Date::parse(date('Y-m-d', $end) === date('Y-m-d', $start) ? Config::get('timeFormat') : Config::get('datimFormat'), $end);
            }

            return $text;
        }

        $text = Date::parse(Config::get('dateFormat'), $start);

        return $end > $start && date('Y-m-d', $end) !== date('Y-m-d', $start) ? $text.' – '.Date::parse(Config::get('dateFormat'), $end) : $text;
    }

    /**
     * Length of a single-day event with times, for the certificate, e.g. "6.5"
     */
    private function hours(CalendarEventsModel $event): string
    {
        $start = (int) $event->startTime;
        $end = (int) $event->endTime;

        if (!$event->addTime || $end <= $start || date('Y-m-d', $end) !== date('Y-m-d', $start)) {
            return '';
        }

        return rtrim(rtrim(number_format(round(($end - $start) / 1800) / 2, 1, '.', ''), '0'), '.');
    }
}
