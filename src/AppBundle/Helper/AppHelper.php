<?php

namespace AppBundle\Helper;

class AppHelper
{
    /** @var list<string> */
    private const BOT_USER_AGENTS = [
        'YandexBot', 'YandexAccessibilityBot', 'YandexMobileBot', 'YandexDirectDyn',
        'YandexScreenshotBot', 'YandexImages', 'YandexVideo', 'YandexVideoParser',
        'YandexMedia', 'YandexBlogs', 'YandexFavicons', 'YandexWebmaster',
        'YandexPagechecker', 'YandexImageResizer', 'YandexAdNet', 'YandexDirect',
        'YaDirectFetcher', 'YandexCalendar', 'YandexSitelinks', 'YandexMetrika',
        'YandexNews', 'YandexNewslinks', 'YandexCatalog', 'YandexAntivirus',
        'YandexMarket', 'YandexVertis', 'YandexForDomain', 'YandexSpravBot',
        'YandexSearchShop', 'YandexMedianaBot', 'YandexOntoDB', 'YandexOntoDBAPI',
        'Googlebot', 'Googlebot-Image', 'Googlebot-News', 'Googlebot-Video',
        'Mediapartners-Google', 'AdsBot-Google', 'Chrome-Lighthouse', 'Lighthouse',
        'Mail.RU_Bot', 'bingbot', 'Accoona', 'ia_archiver', 'Ask Jeeves',
        'OmniExplorer_Bot', 'W3C_Validator', 'WebAlta', 'YahooFeedSeeker', 'Yahoo!',
        'Ezooms', 'Tourlentabot', 'MJ12bot', 'AhrefsBot', 'SearchBot', 'SiteStatus',
        'Nigma.ru', 'Baiduspider', 'Statsbot', 'SISTRIX', 'AcoonBot', 'findlinks',
        'proximic', 'OpenindexSpider', 'Exabot', 'Spider', 'SeznamBot',
        'oBot', 'C-T bot', 'Updownerbot', 'Snoopy', 'heritrix', 'Yeti',
        'DomainVader', 'DCPbot', 'PaperLiBot', 'APIs-Google', 'AdsBot-Google-Mobile',
        'AdsBot-Google-Mobile-Apps', 'FeedFetcher-Google',
        'Google-Read-Aloud', 'DuplexWeb-Google', 'Storebot-Google',
    ];

    public static function isBot(?string $userAgent): bool
    {
        if (null === $userAgent || '' === $userAgent) {
            return false;
        }

        foreach (self::BOT_USER_AGENTS as $bot) {
            if (false !== stripos($userAgent, $bot)) {
                return true;
            }
        }

        return false;
    }
}