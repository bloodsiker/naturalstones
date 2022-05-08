<?php

namespace ShareBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;

/**
 * Class StoneController
 */
class StoneController extends Controller
{
    /**
     * @param Request $request
     *
     * @return Response
     *
     * @Cache(maxage=60, public=true)
     */
    public function listAction(Request $request)
    {
        $breadcrumb = $this->get('app.breadcrumb');
        $breadcrumb->addBreadcrumb(['title' => 'Все камни']);

        $title = 'Все натуральные камни | Naturalstones Jewerly - Изделия из натуральных камней';
        $description = "Список всех натуральных камней";

        $this->get('app.seo.updater')->doMagic(null, [
            'title' => $title,
            'description' => $description.' | Naturalstones Jewerly - Изделия из натуральных камней',
            'keywords' => 'Натуральные камни, серебро, браслеты, кольца, чокеры, подвески, вставки, нити-обереги, индивидуальные заказы, шамбала',
            'og' => [
                'og:site_name' => 'Naturalstones Jewerly - Изделия из натуральных камней',
                'og:type' => 'website',
                'og:title' => $title,
                'og:url' => $request->getSchemeAndHttpHost(),
            ],
        ]);

        return $this->render('ShareBundle::stone_list.html.twig');
    }

    public function listLetterAction(Request $request)
    {
        $router = $this->get('router');
        $breadcrumb = $this->get('app.breadcrumb');
        $breadcrumb->addBreadcrumb(['title' => 'Все камни',  'href' => $router->generate('stone_list')]);
        $breadcrumb->addBreadcrumb(['title' => 'Камни на букву: ' . $request->get('letter')]);

        $title = "Натуральные камни на букву {$request->get('letter')} | Naturalstones Jewerly - Изделия из натуральных камней";
        $description = "Список натуральных камней на букву {$request->get('letter')}.  Найдите камни на любую букву алфавита";

        $this->get('app.seo.updater')->doMagic(null, [
            'title' => $title,
            'description' => $description.' | Naturalstones Jewerly - Изделия из натуральных камней',
            'keywords' => 'Натуральные камни, серебро, браслеты, кольца, чокеры, подвески, вставки, нити-обереги, индивидуальные заказы, шамбала',
            'og' => [
                'og:site_name' => 'Naturalstones Jewerly - Изделия из натуральных камней',
                'og:type' => 'website',
                'og:title' => $title,
                'og:url' => $request->getSchemeAndHttpHost(),
            ],
        ]);

        return $this->render('ShareBundle::stone_list.html.twig');
    }
}