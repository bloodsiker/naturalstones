<?php

namespace AppBundle\Controller;

use BookBundle\Entity\Book;
use BookBundle\Entity\BookCollection;
use GenreBundle\Entity\Genre;
use ShareBundle\Entity\Tag;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class DefaultController
 */
class DefaultController extends Controller
{
    /**
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
//        $metaTitle = $this->get('translator')->trans('app.frontend.meta.meta_title_index', ['%YEAR%' => date("Y")], 'AppBundle');
//        $metaDescription = $this->get('translator')->trans('app.frontend.meta.meta_description_index', ['%YEAR%' => date("Y")], 'AppBundle');
//        $metaKeywords = $this->get('translator')->trans('app.frontend.meta.meta_keywords_index', ['%YEAR%' => date("Y")], 'AppBundle');

        $page = $request->get('page') ? " | Страница {$request->get('page', 1)}" : null;
        $pageDesc = $request->get('page') ? "Страница {$request->get('page', 1)} | " : null;

        $this->get('app.seo.updater')->doMagic(null, [
            'title' => 'TopBook.com.ua - скачать книги в fb2, epub, pdf, txt форматах'.$page,
            'description' => $pageDesc.'Электронная библиотека, скачать книги, читать рецензии, отзывы, книжные рейтинги.',
            'keywords' => 'скачать книги, рецензии, отзывы на книги, цитаты из книг, краткое содержание, топбук',
            'og' => [
                'og:url' => $request->getSchemeAndHttpHost(),
            ],
        ]);

        return new Response();
    }

    /**
     * @return Response
     */
    public function topBooksAction(Request $request)
    {
        $breadcrumb = $this->get('app.breadcrumb');
        $breadcrumb->addBreadcrumb(['title' => 'Топ-100 книг']);

        $this->get('app.seo.updater')->doMagic(null, [
            'title' => '100 лучших книг на сайте | TopBook.com.ua - скачать книги в fb2, epub, pdf, txt форматах',
            'description' => '100 лучших книг на сайте | Электронная библиотека, скачать книги, читать рецензии, отзывы, книжные рейтинги.',
            'keywords' => 'скачать книги, рецензии, отзывы на книги, цитаты из книг, краткое содержание, топбук',
            'og' => [
                'og:site_name' => 'TopBook.com.ua - скачать книги в fb2, epub, pdf, txt форматах',
                'og:type' => 'article',
                'og:title' => '100 лучших книг на сайте',
                'og:url' => $request->getSchemeAndHttpHost(),
            ],
        ]);

        return $this->render('BookBundle::top-100.html.twig');
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function searchAction(Request $request)
    {
        $breadcrumb = $this->get('app.breadcrumb');
        $breadcrumb->addBreadcrumb(['title' => 'Поиск']);

        $this->get('app.seo.updater')->doMagic(null, [
            'title' => 'Поиск по каталогу книг | TopBook.com.ua - скачать книги в fb2, epub, pdf, txt форматах',
            'description' => 'Поиск по каталогу книг | ТопБук - электронная библиотека. Тут Вы можете скачать бесплатно книги',
            'keywords' => 'скачать книги, рецензии, отзывы на книги, цитаты из книг, краткое содержание, топбук',
        ]);

        return $this->render('AppBundle:search:search.html.twig');
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function sitemapAction(Request $request)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $urls = [];
        $hostname = $request->getSchemeAndHttpHost();
        $router = $this->get('router');
        $urls[] = ['loc' => $router->generate('index'), 'changefreq' => 'weekly', 'priority' => '1.0'];
        $urls[] = ['loc' => $router->generate('top_100'), 'changefreq' => 'weekly', 'priority' => '0.75'];
        $urls[] = ['loc' => $router->generate('collection_list'), 'changefreq' => 'weekly', 'priority' => '0.75'];

        $urls[] = ['loc' => $router->generate('book_list'), 'changefreq' => 'weekly', 'priority' => '0.75'];
        $books = $em->getRepository(Book::class)->findBy(['isActive' => true]);
        foreach ($books as $book) {
            $urls[] = ['loc' => $router->generate('book_view', ['id' => $book->getId(), 'slug' => $book->getSlug()]), 'changefreq' => 'weekly', 'priority' => '0.75'];
        }

        $collectionsGenres = $em->getRepository(BookCollection::class)->getGenresCollection();
        foreach ($collectionsGenres as $collectionsGenre) {
            $urls[] = ['loc' => $router->generate('collection_category', ['genre' => $collectionsGenre->getSlug()]), 'changefreq' => 'weekly', 'priority' => '0.75'];
        }

        $collections = $em->getRepository(BookCollection::class)->findBy(['isActive' => true]);
        foreach ($collections as $collection) {
            $urls[] = ['loc' => $router->generate('collection_view', ['slug' => $collection->getSlug()]), 'changefreq' => 'weekly', 'priority' => '0.5'];
        }

        $years = $em->getRepository(Book::class)->getUniqueYear();
        foreach ($years as $year) {
            $urls[] = ['loc' => $router->generate('year_books', ['year' => (int) $year->getYear()]), 'changefreq' => 'weekly', 'priority' => '0.3'];
        }

        $urls[] = ['loc' => $router->generate('search'), 'changefreq' => 'weekly', 'priority' => '0.3'];
        $urls[] = ['loc' => $router->generate('order_board'), 'changefreq' => 'weekly', 'priority' => '0.3'];
        $statuses = ['new', 'completed', 'cancel', 'top'];
        foreach ($statuses as $status) {
            $urls[] = ['loc' => $router->generate('order_board_status', ['status' => $status]), 'changefreq' => 'weekly', 'priority' => '0.3'];
        }

        $tags = $em->getRepository(Tag::class)->findBy(['isActive' => true]);
        foreach ($tags as $tag) {
            $urls[] = ['loc' => $router->generate('tag_books', ['slug' => $tag->getSlug()]), 'changefreq' => 'weekly', 'priority' => '0.5'];
        }

        $urls[] = ['loc' => $router->generate('genre_list'), 'changefreq' => 'weekly', 'priority' => '0.5'];
        $genres = $em->getRepository(Genre::class)->findBy(['isActive' => true, 'parent' => null]);
        foreach ($genres as $genre) {
            $urls[] = ['loc' => $router->generate('genre_books', ['genre' => $genre->getSlug()]), 'changefreq' => 'weekly', 'priority' => '0.5'];
            if ($genre->getChildren()->count()) {
                foreach ($genre->getChildren()->getValues() as $subGenre) {
                    if ($subGenre->getIsActive()) {
                        $urls[] = ['loc' => $router->generate('sub_genre_books',
                            ['genre' => $genre->getSlug(), 'sub_genre' => $subGenre->getSlug()]), 'changefreq' => 'weekly', 'priority' => '0.5'];
                    }
                }
            }
        }

        $urls[] = ['loc' => $router->generate('last_comments'), 'changefreq' => 'weekly', 'priority' => '0.3'];

        $response = new Response($this->renderView('AppBundle:Block:sitemap.html.twig', ['urls' => $urls, 'hostname' => $hostname]));
        $response->headers->set('Content-Type', 'application/xml; charset=utf-8');

        return $response;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function openSearchAction(Request $request)
    {
        $response = new Response($this->renderView('AppBundle:Block:open_searche.html.twig'));
        $response->headers->set('Content-Type', 'application/xml; charset=utf-8');

        return $response;
    }
}
