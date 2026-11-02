<?php

declare(strict_types=1);

use Codefog\EventsSubscriptionsBundle\Cache\RegistrationFieldsCacheWarmer;
use Codefog\EventsSubscriptionsBundle\EventListener\BackendMenuListener;
use Codefog\EventsSubscriptionsBundle\EventListener\CronListener;
use Codefog\EventsSubscriptionsBundle\EventListener\EventEmailsListener;
use Codefog\EventsSubscriptionsBundle\EventListener\NewCalendarPermissionsListener;
use Codefog\EventsSubscriptionsBundle\EventListener\PageListener;
use Codefog\EventsSubscriptionsBundle\ExporterHelper;
use Codefog\EventsSubscriptionsBundle\NotificationCenterHelper;
use Contao\CoreBundle\Event\LayoutEvent;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services
        ->defaults()
        ->autoconfigure()
        ->autowire()
    ;

    $services->load('Codefog\\EventsSubscriptionsBundle\\NotificationCenter\\', __DIR__ . '/../src/NotificationCenter');
    $services->load('Codefog\\EventsSubscriptionsBundle\\Migration\\', __DIR__ . '/../src/Migration');

    $services
        ->load('Codefog\\EventsSubscriptionsBundle\\Controller\\', __DIR__ . '/../src/Controller')
        ->tag('controller.service_arguments')
    ;

    // Reminders, trainer packs, follow-ups, payment expiry and retention (#[AsCronJob])
    $services
        ->set(CronListener::class)
    ;

    $services
        ->set(NotificationCenterHelper::class)
        ->public()
    ;

    $services
        ->set(ExporterHelper::class)
        ->public()
    ;

    // New calendars are available to the user groups that manage events
    $services
        ->set(NewCalendarPermissionsListener::class)
        ->autoconfigure(false)
        ->tag('contao.hook', ['hook' => 'loadDataContainer', 'priority' => -100])
    ;

    // Events › E-mails: after the Notification Center's own callbacks
    $services
        ->set(EventEmailsListener::class)
        ->autoconfigure(false)
        ->tag('contao.hook', ['hook' => 'loadDataContainer', 'priority' => -200])
    ;

    // After the menu items were added
    $services
        ->set(BackendMenuListener::class)
        ->autoconfigure(false)
        ->tag('kernel.event_listener', ['event' => 'contao.backend_menu_build', 'priority' => -100])
    ;

    // Cancel link and payment return on pages with a Twig layout; legacy layouts use
    // the generatePage hook
    $services
        ->set(PageListener::class)
        ->autoconfigure(false)
        ->tag('kernel.event_listener', ['event' => LayoutEvent::class, 'method' => 'onLayout'])
    ;

    // Must run after Contao's cache warmer (priority 0), which writes the DCA extracts
    $services
        ->set(RegistrationFieldsCacheWarmer::class)
        ->autoconfigure(false)
        ->tag('kernel.cache_warmer', ['priority' => -1])
    ;
};
