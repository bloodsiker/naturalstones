<?php

namespace GenreBundle\Controller;

use GenreBundle\Entity\Genre;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;

/**
 * Class GenreController
 */
class GenreController extends Controller
{
    const GENRE_404 = 'Genre doesn\'t exist';

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
        $breadcrumb->addBreadcrumb(['title' => 'Жанры']);

        $this->get('app.seo.updater')->doMagic(null, [
            'title' => 'Все жанры | TopBook.com.ua - скачать книги бесплатно и без регистрации',
            'description' => 'Жанры | список книг по жанрам | ТопБук - электронная библиотека. Здесь Вы можете скачать бесплатно книги без регистрации в форматах fb2, epub, pdf, txt',
            'keywords' => 'книги по жанрам, скачать книги, рецензии, отзывы на книги, цитаты из книг, краткое содержание, без регистрации, топбук',
            'og' => [
                'og:site_name' => 'TopBook.com.ua - электронная библиотека',
                'og:type' => 'website',
                'og:title' => 'Все жанры | ТопБук',
                'og:url' => $request->getSchemeAndHttpHost(),
            ],
        ]);

        return $this->render('GenreBundle::genre_list.html.twig');
    }

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @Cache(maxage=60, public=true)
     */
    public function listBookAction(Request $request)
    {
        $slugGenre = $request->get('genre');
        $slugSubGenre = $request->get('sub_genre');
        $repo = $this->getDoctrine()->getManager()->getRepository(Genre::class);
        $genre = $repo->findOneBy(['slug' => $slugGenre, 'isActive' => true]);
        if (!$genre) {
            throw $this->createNotFoundException(self::GENRE_404);
        }

        if ($slugSubGenre) {
            $subGenre = $repo->findOneBy(['slug' => $slugSubGenre, 'isActive' => true]);
            if (!$subGenre) {
                throw $this->createNotFoundException(self::GENRE_404);
            }
        }

        $router = $this->get('router');
        $breadcrumb = $this->get('app.breadcrumb');

        if ($slugSubGenre && $subGenre) {
            $breadcrumb->addBreadcrumb([
                'title' => $genre->getName(),
                'href' => $router->generate('genre_books', ['genre' => $genre->getSlug()]),
            ]);
            $breadcrumb->addBreadcrumb(['title' => $subGenre->getName()]);
            $titleGenre = 'Жанр '.$subGenre->getName();
        } else {
            $breadcrumb->addBreadcrumb(['title' => $genre->getName()]);
            $titleGenre = 'Жанр '.$genre->getName();
        }

        $page = $request->get('page') ? " | Страница {$request->get('page', 1)}" : null;
        $pageDesc = $request->get('page') ? "Страница {$request->get('page', 1)} |" : null;

        $this->get('app.seo.updater')->doMagic(null, [
            'title' => $titleGenre.' | Книги'.$page.' | TopBook.com.ua - скачать книги бесплатно и без регистрации',
            'description' => $pageDesc."Скачать книги из жанра {$titleGenre} бесплатно и без регистрации в форматах fb2, epub, pdf, txt",
            'keywords' => $titleGenre.', скачать книги, рецензии, отзывы на книги, цитаты из книг, краткое содержание, без регистрации, топбук',
            'og' => [
                'og:site_name' => 'TopBook.com.ua - электронная библиотека',
                'og:type' => 'website',
                'og:title' => $titleGenre.' | Книги | Страница '.$request->get('page', 1).' | ТопБук',
                'og:url' => $request->getSchemeAndHttpHost(),
            ],
        ]);

        return $this->render('GenreBundle::book_list.html.twig', [
            'genre' => $genre,
            'subGenre' => $subGenre ?? null,
        ]);
    }
}