<?php

namespace ShareBundle\Controller;

use AppBundle\Services\BreadcrumbService;
use AppBundle\Services\SeoUpdater;
use AppBundle\Controller\BaseController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\Cache;

/**
 * Class StoneController
 */
class StoneController extends BaseController
{
    /**
     * @var BreadcrumbService
     */
    private $breadcrumb;

    /**
     * @var SeoUpdater
     */
    private $seoUpdater;

    public function __construct(BreadcrumbService $breadcrumb, SeoUpdater $seoUpdater)
    {
        $this->breadcrumb = $breadcrumb;
        $this->seoUpdater = $seoUpdater;
    }

    /**
     * @param Request $request
     *
     * @return Response
     *
     */
     #[Cache(maxage: 60, public: true)]
    public function listAction(Request $request)
    {
        $this->breadcrumb->addBreadcrumb(['title' => 'Все камни']);

        $title = 'Все натуральные камни | Naturalstones Jewerly - Изделия из натуральных камней';
        $description = "Список всех натуральных камней";

        $this->seoUpdater->doMagic(null, [
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

        return $this->render('@Share/stone_list.html.twig');
    }

    public function listLetterAction(Request $request)
    {
        $router = $this->get('router');
        $this->breadcrumb->addBreadcrumb(['title' => 'Все камни',  'href' => $router->generate('stone_list')]);
        $this->breadcrumb->addBreadcrumb(['title' => 'Камни на букву: ' . $request->get('letter')]);

        $title = "Натуральные камни на букву {$request->get('letter')} | Naturalstones Jewerly - Изделия из натуральных камней";
        $description = "Список натуральных камней на букву {$request->get('letter')}.  Найдите камни на любую букву алфавита";

        $this->seoUpdater->doMagic(null, [
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

        return $this->render('@Share/stone_list.html.twig');
    }
}
