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
            'title' => $zodiac->getName().' | Книги | Страница '.$request->get('page', 1).' | TopBook.com.ua - скачать книги бесплатно и без регистрации',
            'description' => "Скачать книги автора {$zodiac->getName()} бесплатно и без регистрации",
            'keywords' => "книги автора {$zodiac->getName()}, скачать книги, рецензии, отзывы на книги, цитаты из книг, краткое содержание, без регистрации, топбук",
            'og' => [
                'og:site_name' => 'TopBook.com.ua - электронная библиотека',
                'og:type' => 'website',
                'og:title' => $zodiac->getName().' | Книги | Страница '.$request->get('page', 1).' | TopBook.com.ua - скачать книги бесплатно и без регистрации',
                'og:url' => $request->getSchemeAndHttpHost(),
            ],
        ]);

        return $this->render('ShareBundle::zodiac_list.html.twig', ['zodiac' => $zodiac]);
    }
}