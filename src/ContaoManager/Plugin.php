<?php

namespace Codefog\EventsSubscriptionsBundle\ContaoManager;

use Codefog\EventsSubscriptionsBundle\CodefogEventsSubscriptionsBundle;
use Codefog\HasteBundle\CodefogHasteBundle;
use Contao\CalendarBundle\ContaoCalendarBundle;
use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Contao\ManagerPlugin\Routing\RoutingPluginInterface;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\RouteCollection;

class Plugin implements BundlePluginInterface, RoutingPluginInterface
{
    public function getBundles(ParserInterface $parser): array
    {
        return [
            BundleConfig::create(CodefogEventsSubscriptionsBundle::class)->setLoadAfter([
                ContaoCoreBundle::class,
                ContaoCalendarBundle::class,
                CodefogHasteBundle::class,
            ]),
        ];
    }

    /**
     * The payment webhook route
     */
    public function getRouteCollection(LoaderResolverInterface $resolver, KernelInterface $kernel): RouteCollection|null
    {
        $file = __DIR__.'/../../config/routes.yaml';

        return $resolver->resolve($file)?->load($file);
    }
}
