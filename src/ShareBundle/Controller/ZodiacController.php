<?php

namespace ShareBundle\Controller;

use AppBundle\Controller\BaseController;
use AppBundle\Services\BreadcrumbService;
use AppBundle\Services\SeoUpdater;
use Doctrine\ORM\EntityManagerInterface;
use ShareBundle\Entity\Zodiac;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\Cache;

class ZodiacController extends BaseController
{
    public const ZODIAC_404 = 'Zodiac doesn\'t exist';

    public function __construct(
        private readonly BreadcrumbService $breadcrumb,
        private readonly SeoUpdater $seoUpdater,
        private readonly EntityManagerInterface $em,
    ) {
    }

    #[Cache(maxage: 60, public: true)]
    public function listAction(Request $request): Response
    {
        $zodiac = $this->em->getRepository(Zodiac::class)->findOneBy([
            'slug' => $request->get('slug'),
            'isActive' => true,
        ]);

        if (!$zodiac) {
            throw $this->createNotFoundException(self::ZODIAC_404);
        }

        $this->breadcrumb->addBreadcrumb(['title' => 'Камни для знака зодиака ' . $zodiac->getName()]);

        $title = sprintf(
            'Изделия для знака зодиака - %s | Изделия | Страница %s | Naturalstones Jewerly - Изделия из натуральных камней',
            $zodiac->getName(),
            $request->get('page', 1),
        );

        $this->seoUpdater->doMagic(null, [
            'title' => $title,
            'description' => "Купить изделия для знака зодиака {$zodiac->getName()} ",
            'keywords' => "{$zodiac->getName()}, Натуральные камни, серебро, браслеты, кольца, чокеры, подвески, вставки, нити-обереги, индивидуальные заказы, шамбала",
            'og' => [
                'og:site_name' => 'Naturalstones Jewerly - Изделия из натуральных камней',
                'og:type' => 'website',
                'og:title' => $title,
                'og:url' => $request->getSchemeAndHttpHost(),
            ],
        ]);

        return $this->render('@Share/zodiac_list.html.twig', ['zodiac' => $zodiac]);
    }
}