<?php

namespace ShareBundle\Controller;

use AppBundle\Controller\BaseController;
use AppBundle\Services\BreadcrumbService;
use AppBundle\Services\SeoUpdater;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\Cache;
use Symfony\Component\Routing\RouterInterface;

class StoneController extends BaseController
{
    private const SITE_SUFFIX = ' | Naturalstones Jewerly - Изделия из натуральных камней';
    private const KEYWORDS = 'Натуральные камни, серебро, браслеты, кольца, чокеры, подвески, вставки, нити-обереги, индивидуальные заказы, шамбала';

    public function __construct(
        private readonly BreadcrumbService $breadcrumb,
        private readonly SeoUpdater $seoUpdater,
        private readonly RouterInterface $router,
    ) {
    }

    #[Cache(maxage: 60, public: true)]
    public function listAction(Request $request): Response
    {
        $this->breadcrumb->addBreadcrumb(['title' => 'Все камни']);

        $this->applySeo(
            $request,
            'Все натуральные камни' . self::SITE_SUFFIX,
            'Список всех натуральных камней',
        );

        return $this->render('@Share/stone_list.html.twig');
    }

    public function listLetterAction(Request $request): Response
    {
        $letter = $request->get('letter');
        $this->breadcrumb->addBreadcrumb(['title' => 'Все камни', 'href' => $this->router->generate('stone_list')]);
        $this->breadcrumb->addBreadcrumb(['title' => 'Камни на букву: ' . $letter]);

        $this->applySeo(
            $request,
            "Натуральные камни на букву {$letter}" . self::SITE_SUFFIX,
            "Список натуральных камней на букву {$letter}.  Найдите камни на любую букву алфавита",
        );

        return $this->render('@Share/stone_list.html.twig');
    }

    private function applySeo(Request $request, string $title, string $description): void
    {
        $this->seoUpdater->doMagic(null, [
            'title' => $title,
            'description' => $description . self::SITE_SUFFIX,
            'keywords' => self::KEYWORDS,
            'og' => [
                'og:site_name' => 'Naturalstones Jewerly - Изделия из натуральных камней',
                'og:type' => 'website',
                'og:title' => $title,
                'og:url' => $request->getSchemeAndHttpHost(),
            ],
        ]);
    }
}