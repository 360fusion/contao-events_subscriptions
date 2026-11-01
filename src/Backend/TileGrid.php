<?php

namespace Codefog\EventsSubscriptionsBundle\Backend;

use Contao\Image;
use Contao\StringUtil;

/**
 * The tiles of Events › Settings: a title and a description, and along the bottom a state on the left and a number on the right.
 * Used by the Settings page and by the payment types on the Payments page.
 */
final class TileGrid
{
    private const DOTS = ['on' => '#2e9e5b', 'off' => '#8a8f98', 'warn' => '#d9822b'];

    /**
     * @param list<array{title: string, description: string, icon: string, href: string, state?: array{level: string, text: string}|null, count?: string, dashed?: bool}> $tiles
     */
    public static function html(array $tiles): string
    {
        $e = static fn (string $text): string => StringUtil::specialchars($text);
        $html = '<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:16px">';

        foreach ($tiles as $tile) {
            $state = $tile['state'] ?? null;
            $count = (string) ($tile['count'] ?? '');

            $html .= '<a href="'.$e($tile['href']).'" style="display:flex;flex-direction:column;border:1px '.(!empty($tile['dashed']) ? 'dashed' : 'solid').' var(--border, #ddd);border-radius:8px;overflow:hidden;color:inherit;text-decoration:none;background:var(--tile, transparent)">'
                .'<span style="display:block;padding:16px 18px;flex:1 1 auto">'
                .'<span style="display:flex;align-items:center;gap:10px;font-weight:600;font-size:1.05em">'.Image::getHtml($tile['icon']).' '.$e($tile['title']).'</span>'
                .'<span class="tl_gray" style="display:block;margin-top:6px;font-size:.9em">'.$e($tile['description']).'</span>'
                .'</span>';

            if ($state || '' !== $count) {
                $html .= '<span style="display:flex;justify-content:space-between;align-items:center;gap:12px;padding:6px 18px;border-top:1px solid var(--border, #ddd);background:var(--table-header, rgba(127,127,127,.08));font-size:.85em">'
                    .'<span style="display:inline-flex;align-items:center;gap:6px;font-weight:600">'
                    .($state ? '<span aria-hidden="true" style="width:8px;height:8px;border-radius:50%;background:'.self::DOTS[$state['level']].'"></span>'.$e($state['text']) : '')
                    .'</span><span class="tl_gray">'.$e($count).'</span></span>';
            }

            $html .= '</a>';
        }

        return $html.'</div>';
    }
}
