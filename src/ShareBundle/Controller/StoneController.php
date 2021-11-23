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

        $title = 'Все авторы | TopBook.com.ua - скачать книги бесплатно и без регистрации';
        $description = "Список авторов";

        $this->get('app.seo.updater')->doMagic(null, [
            'title' => $title,
            'description' => $description.' | ТопБук - электронная библиотека. Здесь Вы можете скачать бесплатно книги без регистрации',
            'keywords' => 'скачать книги, рецензии, отзывы на книги, цитаты из книг, краткое содержание, без регистрации, топбук',
            'og' => [
                'og:site_name' => 'TopBook.com.ua - электронная библиотека',
                'og:type' => 'website',
                'og:title' => $title,
                'og:url' => $request->getSchemeAndHttpHost(),
            ],
        ]);

        return $this->render('ShareBundle::stone_list.html.twig');
    }

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @Cache(maxage=60, public=true)
     */
    public function authorBookAction(Request $request)
    {
        $slug = $request->get('slug');
        $repo = $this->getDoctrine()->getManager()->getRepository(Author::class);
        $author = $repo->findOneBy(['slug' => $slug, 'isActive' => true]);
        if (!$author) {
            throw $this->createNotFoundException(self::AUTHOR_404);
        }

        $router = $this->get('router');
        $breadcrumb = $this->get('app.breadcrumb');
        $breadcrumb->addBreadcrumb(['title' => 'Авторы', 'href' => $router->generate('author_list')]);
        $breadcrumb->addBreadcrumb(['title' => $author->getName()]);

        $this->get('app.seo.updater')->doMagic(null, [
            'title' => $author->getName().' | Книги | Страница '.$request->get('page', 1).' | TopBook.com.ua - скачать книги бесплатно и без регистрации',
            'description' => "Скачать книги автора {$author->getName()} бесплатно и без регистрации",
            'keywords' => "книги автора {$author->getName()}, скачать книги, рецензии, отзывы на книги, цитаты из книг, краткое содержание, без регистрации, топбук",
            'og' => [
                'og:site_name' => 'TopBook.com.ua - электронная библиотека',
                'og:type' => 'website',
                'og:title' => $author->getName().' | Книги | Страница '.$request->get('page', 1).' | TopBook.com.ua - скачать книги бесплатно и без регистрации',
                'og:url' => $request->getSchemeAndHttpHost(),
            ],
        ]);

        return $this->render('ShareBundle::author_books.html.twig', ['author' => $author]);
    }
}