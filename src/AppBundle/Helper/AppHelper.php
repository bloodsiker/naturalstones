<?php

namespace AppBundle\Helper;

/**
 * Class AppHelper
 */
class AppHelper
{
    public static function isBot($userAgent) {

        $botList = [
            'YandexBot', 'YandexAccessibilityBot', 'YandexMobileBot','YandexDirectDyn',
            'YandexScreenshotBot', 'YandexImages', 'YandexVideo', 'YandexVideoParser',
            'YandexMedia', 'YandexBlogs', 'YandexFavicons', 'YandexWebmaster',
            'YandexPagechecker', 'YandexImageResizer','YandexAdNet', 'YandexDirect',
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
            'AdsBot-Google-Mobile', 'AdsBot-Google-Mobile-Apps', 'FeedFetcher-Google',
            'Google-Read-Aloud', 'DuplexWeb-Google', 'Storebot-Google'
        ];

        foreach ($botList as $bot) {
            if (stripos($userAgent, $bot) !== false) {
                return true;
            }
        }

        return false;
    }
}
