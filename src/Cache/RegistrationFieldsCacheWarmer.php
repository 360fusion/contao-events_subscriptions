<?php

/**
 * events_subscriptions extension for Contao Open Source CMS
 *
 * @copyright Copyright (c) 2011-2017, Codefog
 * @author    Codefog <https://codefog.pl>
 * @license   http://opensource.org/licenses/lgpl-3.0.html LGPL
 * @link      http://github.com/codefog/contao-events_subscriptions
 */

namespace Codefog\EventsSubscriptionsBundle\Cache;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;

/**
 * Removes the cached DCA extract of the subscription table after Contao has written it.
 *
 * The registration fields are virtual fields defined in the database, so a cached extract
 * would not know about fields created after the cache was warmed and their answers would
 * not be saved. Without the file, Contao builds the extract at runtime from the current DCA.
 */
class RegistrationFieldsCacheWarmer implements CacheWarmerInterface
{
    public function __construct(private readonly Filesystem $filesystem)
    {
    }

    public function isOptional(): bool
    {
        return true;
    }

    public function warmUp(string $cacheDir, string|null $buildDir = null): array
    {
        $this->filesystem->remove(Path::join($cacheDir, 'contao/sql/tl_calendar_events_subscription.php'));

        return [];
    }
}
