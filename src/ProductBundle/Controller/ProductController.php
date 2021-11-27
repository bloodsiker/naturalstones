<?php

namespace ProductBundle\Controller;

use BookBundle\Entity\Book;
use BookBundle\Entity\BookCollection;
use BookBundle\Entity\BookInfoDownload;
use GenreBundle\Entity\Genre;
use MediaBundle\Entity\MediaFile;
use ProductBundle\Entity\Category;
use ProductBundle\Entity\Product;
use ShareBundle\Entity\Colour;
use ShareBundle\Entity\Stone;
use ShareBundle\Entity\Tag;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;

/**
 * Class BookController
 */
class ProductController extends Controller
{
    const PRODUCT_404 = 'Product doesn\'t exist';

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
        $repo = $this->getDoctrine()->getManager()->getRepository(Category::class);
        $category = $repo->findOneBy(['slug' => $slug]);
        $breadcrumb = $this->get('app.breadcrumb');
        $breadcrumb->addBreadcrumb(['title' => $category->getName()]);

        $page = $request->get('page') ? " | Страница {$request->get('page', 1)}" : null;
        $pageDesc = $request->get('page') ? "Страница {$request->get('page', 1)} |" : null;

        $this->get('app.seo.updater')->doMagic(null, [
            'title' => 'Последние новинки книг в библиотеке ТопБук'.$page,
            'description' => "{$pageDesc} Последние новинки книг | Электронная библиотека, скачать книги бесплатно и без регистрации, читать рецензии, отзывы, книжные рейтинги.",
            'keywords' => 'скачать книги, рецензии, отзывы на книги, цитаты из книг, краткое содержание, топбук',
            'og' => [
                'og:url' => $request->getSchemeAndHttpHost(),
            ],
        ]);

        return $this->render('ProductBundle::product_list.html.twig', ['category' => $category]);
    }

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @Cache(maxage=60, public=true)
     */
    public function listWhoAction(Request $request)
    {
        $who = $request->get('who');
        $whoIs = Product::$whois[$who];
        $breadcrumb = $this->get('app.breadcrumb');
        $breadcrumb->addBreadcrumb(['title' => $whoIs]);

        $page = $request->get('page') ? " | Страница {$request->get('page', 1)}" : null;
        $pageDesc = $request->get('page') ? "Страница {$request->get('page', 1)} |" : null;

        $this->get('app.seo.updater')->doMagic(null, [
            'title' => 'Последние новинки книг в библиотеке ТопБук'.$page,
            'description' => "{$pageDesc} Последние новинки книг | Электронная библиотека, скачать книги бесплатно и без регистрации, читать рецензии, отзывы, книжные рейтинги.",
            'keywords' => 'скачать книги, рецензии, отзывы на книги, цитаты из книг, краткое содержание, топбук',
            'og' => [
                'og:url' => $request->getSchemeAndHttpHost(),
            ],
        ]);

        return $this->render('ProductBundle::product_list_who.html.twig', ['whois' => $whoIs]);
    }

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @Cache(maxage=60, public=true)
     */
    public function listTagAction(Request $request)
    {
        $slug = $request->get('slug');
        $repo = $this->getDoctrine()->getManager()->getRepository(Tag::class);
        $tag = $repo->findOneBy(['slug' => $slug]);
        $breadcrumb = $this->get('app.breadcrumb');
        $breadcrumb->addBreadcrumb(['title' => 'Тег: #' . $tag->getName()]);

        $page = $request->get('page') ? " | Страница {$request->get('page', 1)}" : null;
        $pageDesc = $request->get('page') ? "Страница {$request->get('page', 1)} |" : null;

        $this->get('app.seo.updater')->doMagic(null, [
            'title' => 'Последние новинки книг в библиотеке ТопБук'.$page,
            'description' => "{$pageDesc} Последние новинки книг | Электронная библиотека, скачать книги бесплатно и без регистрации, читать рецензии, отзывы, книжные рейтинги.",
            'keywords' => 'скачать книги, рецензии, отзывы на книги, цитаты из книг, краткое содержание, топбук',
            'og' => [
                'og:url' => $request->getSchemeAndHttpHost(),
            ],
        ]);

        return $this->render('ProductBundle::product_list_tag.html.twig', ['tag' => $tag]);
    }

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @Cache(maxage=60, public=true)
     */
    public function listColourAction(Request $request)
    {
        $slug = $request->get('slug');
        $repo = $this->getDoctrine()->getManager()->getRepository(Colour::class);
        $colour = $repo->findOneBy(['slug' => $slug]);
        $breadcrumb = $this->get('app.breadcrumb');
        $breadcrumb->addBreadcrumb(['title' => 'Цвет: ' . $colour->getName()]);

        $page = $request->get('page') ? " | Страница {$request->get('page', 1)}" : null;
        $pageDesc = $request->get('page') ? "Страница {$request->get('page', 1)} |" : null;

        $this->get('app.seo.updater')->doMagic(null, [
            'title' => 'Последние новинки книг в библиотеке ТопБук'.$page,
            'description' => "{$pageDesc} Последние новинки книг | Электронная библиотека, скачать книги бесплатно и без регистрации, читать рецензии, отзывы, книжные рейтинги.",
            'keywords' => 'скачать книги, рецензии, отзывы на книги, цитаты из книг, краткое содержание, топбук',
            'og' => [
                'og:url' => $request->getSchemeAndHttpHost(),
            ],
        ]);

        return $this->render('ProductBundle::product_list_colour.html.twig', ['colour' => $colour]);
    }

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @Cache(maxage=60, public=true)
     */
    public function listStoneAction(Request $request)
    {
        $slug = $request->get('slug');
        $repo = $this->getDoctrine()->getManager()->getRepository(Stone::class);
        $stone = $repo->findOneBy(['slug' => $slug]);
        $breadcrumb = $this->get('app.breadcrumb');
        $breadcrumb->addBreadcrumb(['title' => 'Камень: ' . $stone->getName()]);

        $page = $request->get('page') ? " | Страница {$request->get('page', 1)}" : null;
        $pageDesc = $request->get('page') ? "Страница {$request->get('page', 1)} |" : null;

        $this->get('app.seo.updater')->doMagic(null, [
            'title' => 'Последние новинки книг в библиотеке ТопБук'.$page,
            'description' => "{$pageDesc} Последние новинки книг | Электронная библиотека, скачать книги бесплатно и без регистрации, читать рецензии, отзывы, книжные рейтинги.",
            'keywords' => 'скачать книги, рецензии, отзывы на книги, цитаты из книг, краткое содержание, топбук',
            'og' => [
                'og:url' => $request->getSchemeAndHttpHost(),
            ],
        ]);

        return $this->render('ProductBundle::product_list_stone.html.twig', ['stone' => $stone]);
    }

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @Cache(maxage=60, public=true)
     */
    public function viewAction(Request $request)
    {
        $repo = $this->getDoctrine()->getManager()->getRepository(Product::class);
        $product = $repo->find($request->get('id'));
        if (!$product || !$product->getIsActive()) {
            throw $this->createNotFoundException(self::PRODUCT_404);
        }

        $sizes = $repo->productGroupQueryBuilder($product->getProductGroup())
            ->getQuery()
            ->getResult();

        $router = $this->get('router');
        $breadcrumb = $this->get('app.breadcrumb');
        if ($product->getCategory()) {
            $breadcrumb->addBreadcrumb([
                'title' => $product->getCategory()->getName(),
                'href' => $router->generate('product_list', ['slug' => $product->getCategory()->getSlug()]),
            ]);
        }
        $breadcrumb->addBreadcrumb(['title' => $product->getName()]);

        $title = $product->getName().' -  скачать книгу без регистрации в fb2, epub, pdf, txt | ТопБук - Электронная библиотека для любителей книг';

        $this->get('app.seo.updater')->doMagic(null, [
            'title' => $title,
            'description' => mb_substr($product->getDescription(), 0, 150),
            'keywords' => 'скачать книги, отзывы на книги, краткое содержание, без регистрации',
            'og' => [
                'og:site_name' => 'TopBook.com.ua - скачать книги без регистрации в fb2, epub, pdf, txt форматах',
                'og:type' => 'article',
                'og:title' => $title,
                'og:url' => $request->getSchemeAndHttpHost(),
                'og:image' => $request->getSchemeAndHttpHost().$product->getImage()->getPath(),
                'og:description' => mb_substr($product->getDescription(), 0, 150),
            ],
        ]);

        $repo->incViewCounter($product->getId());
        $this->container->get('product.helper.views')->doView($product);

        return $this->render('ProductBundle::product_view.html.twig', ['product' => $product, 'sizes' => $sizes]);
    }
}