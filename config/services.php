<?php

declare(strict_types=1);

use Codefog\EventsSubscriptionsBundle\Cache\RegistrationFieldsCacheWarmer;
use Codefog\EventsSubscriptionsBundle\EventListener\EventEmailsListener;
use Codefog\EventsSubscriptionsBundle\EventListener\NewCalendarPermissionsListener;
use Codefog\EventsSubscriptionsBundle\ExporterHelper;
use Codefog\EventsSubscriptionsBundle\NotificationCenterHelper;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services
        ->defaults()
        ->autoconfigure()
        ->autowire()
    ;

    $services->load('Codefog\\EventsSubscriptionsBundle\\NotificationCenter\\', __DIR__ . '/../src/NotificationCenter');

    $services
        ->load('Codefog\\EventsSubscriptionsBundle\\Controller\\', __DIR__ . '/../src/Controller')
        ->tag('controller.service_arguments')
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

    // Must run after Contao's cache warmer (priority 0), which writes the DCA extracts
    $services
        ->set(RegistrationFieldsCacheWarmer::class)
        ->autoconfigure(false)
        ->tag('kernel.cache_warmer', ['priority' => -1])
    ;
};
