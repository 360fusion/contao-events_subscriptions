<?php

namespace Codefog\EventsSubscriptionsBundle\Booking;

use Contao\Config;
use Contao\Environment;
use Contao\StringUtil;
use Symfony\Component\HttpFoundation\Response;

/**
 * The plain page around the booker's own pages (booking, certificate, feedback, check-in): no site layout is needed, so nothing has to be set up
 */
final class Page
{
    public static function e(mixed $value): string
    {
        return StringUtil::specialchars((string) $value);
    }

    /**
     * Text of $GLOBALS['TL_LANG']['MSC']['events_subscriptions.public'], with sprintf arguments
     */
    public static function t(string $key, mixed ...$args): string
    {
        $text = $GLOBALS['TL_LANG']['MSC']['events_subscriptions.public'][$key] ?? $key;

        return $args ? sprintf($text, ...$args) : $text;
    }

    public static function render(string $title, string $body, int $status = Response::HTTP_OK, string $head = ''): Response
    {
        $site = self::e(Config::get('websiteTitle') ?: Environment::get('host'));
        $html = '<!DOCTYPE html><html lang="'.self::e($GLOBALS['TL_LANGUAGE'] ?? 'en').'"><head><meta charset="utf-8">'
            .'<meta name="viewport" content="width=device-width, initial-scale=1"><meta name="robots" content="noindex, nofollow">'
            .'<title>'.self::e($title).' | '.$site.'</title><style>'.self::CSS.'</style>'.$head.'</head><body><main>'
            .'<p class="site">'.$site.'</p><h1>'.self::e($title).'</h1>'.$body.'</main></body></html>';

        // Personal pages: never keep them in a shared cache
        return new Response($html, $status, ['Cache-Control' => 'private, no-store']);
    }

    private const CSS = <<<'CSS'
:root{--ink:#212b32;--grey:#4c6272;--line:#d8dde0;--blue:#005eb8;--bg:#f0f4f5;--red:#b32226;--green:#00703c}
*{box-sizing:border-box}body{margin:0;background:var(--bg);color:var(--ink);font:18px/1.5 system-ui,-apple-system,"Segoe UI",Arial,sans-serif}
main{max-width:640px;margin:0 auto;padding:24px 16px 56px}.site{margin:0;color:var(--grey);font-size:.85rem}h1{font-size:1.7rem;line-height:1.2;margin:.2em 0 .8em}
h2{font-size:1.15rem;margin:1.6em 0 .5em}.card{background:#fff;border:1px solid var(--line);border-radius:6px;padding:16px 18px;margin:0 0 16px}
dl{display:grid;grid-template-columns:max-content 1fr;gap:6px 18px;margin:0}dt{color:var(--grey)}dd{margin:0;overflow-wrap:anywhere}
label{display:block;font-weight:600;margin:14px 0 4px}label.inline{display:inline-block;font-weight:400;margin:0 14px 0 0}
input[type=text],input[type=email],input[type=number],select,textarea{width:100%;font:inherit;padding:8px 10px;border:2px solid var(--ink);border-radius:4px;background:#fff}
textarea{min-height:110px}button,.button{font:inherit;font-weight:600;display:inline-block;background:var(--green);color:#fff;border:0;border-radius:4px;padding:10px 18px;cursor:pointer;text-decoration:none}
button.secondary,.button.secondary{background:#fff;color:var(--blue);border:2px solid var(--blue)}button.danger{background:var(--red)}
.error{color:var(--red);font-weight:600;margin:4px 0 0}.notice{border-left:6px solid var(--green);background:#fff;padding:12px 16px;margin:0 0 16px}
.warning{border-left-color:#ffb81c}.qr{text-align:center}.qr svg{width:240px;height:240px;max-width:100%}.muted{color:var(--grey)}.actions{margin:18px 0 0;display:flex;gap:12px;flex-wrap:wrap}
@media print{body{background:#fff}.noprint{display:none!important}}
CSS;
}
