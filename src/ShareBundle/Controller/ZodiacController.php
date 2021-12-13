<?php

namespace ShareBundle\Controller;

use ShareBundle\Entity\Zodiac;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;

/**
 * Class ZodiacController
 */
class ZodiacController extends Controller
{
    const ZODIAC_404 = 'Zodiac doesn\'t exist';

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @Cache(maxage=60, public=true)
     */
    public function listAction(Request $request)
    {
        $slug = $request->get('slug');
        $repo = $this->getDoctrine()->getManager()->getRepository(Zodiac::class);
        $zodiac = $repo->findOneBy(['slug' => $slug, 'isActive' => true]);
        if (!$zodiac) {
            throw $this->createNotFoundException(self::ZODIAC_404);
        }

        $breadcrumb = $this->get('app.breadcrumb');
        $breadcrumb->addBreadcrumb(['title' => 'Камни для знака зодиака ' . $zodiac->getName()]);

        $this->get('app.seo.updater')->doMagic(null, [
            'title' => $zodiac->getName().' | Изделия | Страница '.$request->get('page', 1).' | Naturalstones Jewerly - Изделия из натуральных камней',
            'description' => "Купить изделия для знака зодиака {$zodiac->getName()} ",
            'keywords' => "{$zodiac->getName()}, Натуральные камни, серебро, браслеты, кольца, чокеры, подвески",
            'og' => [
                'og:site_name' => 'Naturalstones Jewerly - Изделия из натуральных камней',
                'og:type' => 'website',
                'og:title' => $zodiac->getName().' | Изделия | Страница '.$request->get('page', 1).' | Naturalstones Jewerly - Изделия из натуральных камней',
                'og:url' => $request->getSchemeAndHttpHost(),
            ],
        ]);

        return $this->render('ShareBundle::zodiac_list.html.twig', ['zodiac' => $zodiac]);
    }
}