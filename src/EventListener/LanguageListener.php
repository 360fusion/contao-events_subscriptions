<?php

namespace Codefog\EventsSubscriptionsBundle\EventListener;

use Codefog\EventsSubscriptionsBundle\Features;

/**
 * The booking form texts changed in Events › Settings › Booking settings replace the default texts
 */
class LanguageListener
{
    /**
     * @var array<string, string> The default texts, before they were replaced
     */
    public static array $defaults = [];

    public function onLoadLanguageFile(string $name): void
    {
        if ('default' !== $name) {
            return;
        }

        foreach (Features::TEXTS as $key => $setting) {
            self::$defaults[$key] = (string) ($GLOBALS['TL_LANG']['MSC'][$key] ?? '');

            if ('' !== trim($text = Features::text($setting))) {
                $GLOBALS['TL_LANG']['MSC'][$key] = nl2br(htmlspecialchars($text), false);
            }
        }
    }
}
