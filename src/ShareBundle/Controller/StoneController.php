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
    const AUTHOR_404 = 'Stone doesn\'t exist';

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

        $title = 'Все камни | Naturalstones Jewerly - Изделия из натуральных камней';
        $description = "Список камней";

        $this->get('app.seo.updater')->doMagic(null, [
            'title' => $title,
            'description' => $description.' | Naturalstones Jewerly - Изделия из натуральных камней',
            'keywords' => 'скачать книги, рецензии, отзывы на книги, цитаты из книг, краткое содержание, без регистрации, топбук',
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