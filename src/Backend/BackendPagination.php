<?php

namespace Codefog\EventsSubscriptionsBundle\Backend;

use Contao\CoreBundle\Pagination\PaginationConfig;
use Contao\System;

/**
 * The back end's own pagination component (styled like the core lists; the old be_pagination template has no styles)
 */
class BackendPagination
{
    public static function render(int $total, int $perPage, string $parameter): string
    {
        $config = (new PaginationConfig($parameter, $total, $perPage))->withIgnoreOutOfBounds()->withPageRange(7);
        $container = System::getContainer();

        return $container->get('twig')->render('@Contao/backend/component/_pagination.html.twig', [
            'pagination' => $container->get('contao.pagination.factory')->create($config),
        ]);
    }
}
