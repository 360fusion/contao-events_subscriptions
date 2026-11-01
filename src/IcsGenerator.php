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

/**
 * Creates an iCalendar (.ics) file for an event, so it can be added to Outlook, Google Calendar etc.
 */
class IcsGenerator
{
    /**
     * @param array  $event Event row (id, title, startTime, endTime, addTime, location, address, teaser)
     * @param string $url   Absolute URL of the event
     * @param int    $now   Timestamp of the file (DTSTAMP)
     */
    public static function generate(array $event, string $url, int $now): string
    {
        // shortcut: recurring events export their first date only, add RRULE if editors need the series
        $lines = [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//Codefog//events_subscriptions//EN',
            'CALSCALE:GREGORIAN',
            'BEGIN:VEVENT',
            'UID:event-'.$event['id'].'@'.(parse_url($url, PHP_URL_HOST) ?: 'localhost'),
            'DTSTAMP:'.gmdate('Ymd\THis\Z', $now),
        ];

        if ($event['addTime']) {
            $lines[] = 'DTSTART:'.gmdate('Ymd\THis\Z', (int) $event['startTime']);
            $lines[] = 'DTEND:'.gmdate('Ymd\THis\Z', max((int) $event['endTime'], (int) $event['startTime']));
        } else {
            // All-day events end on the day after the last day
            $lines[] = 'DTSTART;VALUE=DATE:'.date('Ymd', (int) $event['startTime']);
            $lines[] = 'DTEND;VALUE=DATE:'.date('Ymd', strtotime('+1 day', max((int) $event['endTime'], (int) $event['startTime'])));
        }

        $lines[] = 'SUMMARY:'.self::text($event['title']);

        $location = implode(', ', array_filter([self::plain($event['location'] ?? ''), self::plain($event['address'] ?? '')]));

        if ('' !== $location) {
            $lines[] = 'LOCATION:'.self::text($location);
        }

        if ('' !== ($teaser = self::plain($event['teaser'] ?? ''))) {
            $lines[] = 'DESCRIPTION:'.self::text($teaser."\n\n".$url);
        } else {
            $lines[] = 'DESCRIPTION:'.self::text($url);
        }

        $lines[] = 'URL:'.$url;
        $lines[] = 'END:VEVENT';
        $lines[] = 'END:VCALENDAR';

        return implode("\r\n", array_map(self::fold(...), $lines))."\r\n";
    }

    private static function plain(string|null $value): string
    {
        return trim(html_entity_decode(strip_tags(str_replace(['<br>', '<br />', '</p>'], "\n", (string) $value)), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    }

    private static function text(string $value): string
    {
        return str_replace(["\\", ';', ',', "\r\n", "\n"], ["\\\\", '\;', '\,', '\n', '\n'], self::plain($value));
    }

    /**
     * Lines longer than 75 octets are folded with CRLF + space (RFC 5545)
     */
    private static function fold(string $line): string
    {
        $folded = '';
        $max = 75;

        while (\strlen($line) > $max) {
            $cut = $max;

            // Do not split a multibyte character
            while ($cut > 0 && 0x80 === (\ord($line[$cut]) & 0xC0)) {
                --$cut;
            }

            $folded .= substr($line, 0, $cut)."\r\n ";
            $line = substr($line, $cut);
            $max = 74; // the leading space counts
        }

        return $folded.$line;
    }
}
