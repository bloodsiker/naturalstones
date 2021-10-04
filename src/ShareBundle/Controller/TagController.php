<?php

namespace ShareBundle\Controller;

use ShareBundle\Entity\Tag;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;

/**
 * Class TagController
 */
class TagController extends Controller
{
    const TAG_404 = 'Tag doesn\'t exist';
    /**
     * @param Request $request
     *
     * @return Response
     *
     * @Cache(maxage=60, public=true)
     */
    public function listAction(Request $request)
    {
        return $this->render('ShareBundle::tag_list.html.twig');
    }

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @Cache(maxage=60, public=true)
     */
    public function tagBookAction(Request $request)
    {
        $slug = $request->get('slug');
        $repo = $this->getDoctrine()->getManager()->getRepository(Tag::class);
        $tag = $repo->findOneBy(['slug' => $slug, 'isActive' => true]);
        if (!$tag) {
            throw $this->createNotFoundException(self::TAG_404);
        }

        $breadcrumb = $this->get('app.breadcrumb');
        $breadcrumb->addBreadcrumb(['title' => $tag->getName()]);

        $this->get('app.seo.updater')->doMagic(null, [
            'title' => 'Книги по тегу ' . $tag->getName().' | Страница '.$request->get('page', 1).' | TopBook.com.ua - скачать книги бесплатно и без регистрации',
            'description' => 'Скачать бесплатно книги без регистрации по тегу '.$tag->getName(),
            'keywords' => $tag->getName().', скачать книги, рецензии, отзывы на книги, цитаты из книг, краткое содержание, без регистрации, топбук',
            'og' => [
                'og:site_name' => 'TopBook.com.ua - электронная библиотека',
                'og:type' => 'website',
                'og:title' => 'Книги по тегу ' . $tag->getName().' | Страница '.$request->get('page', 1).' | TopBook.com.ua - скачать книги бесплатно и без регистрации',
                'og:url' => $request->getSchemeAndHttpHost(),
            ],
        ]);

        return $this->render('ShareBundle::tag_books.html.twig', ['tag' => $tag]);
    }
}