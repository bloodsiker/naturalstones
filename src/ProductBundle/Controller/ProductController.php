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
            'title' => "Список изделий, {$category->getName()} | Naturalstones Jewerly - Изделия из натуральных камней".$page,
            'description' => "{$pageDesc} Список изделий, {$category->getName()}",
            'keywords' => 'Натуральные камни, серебро, браслеты, кольца, чокеры, подвески, вставки, нити-обереги, индивидуальные заказы, шамбала',
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
            'title' => " Naturalstones Jewerly - Изделия из натуральных камней для {$whoIs}" . $page,
            'description' => "{$pageDesc} Большой выбор ювелирных изделий для мужчин и женщин",
            'keywords' => 'Натуральные камни, серебро, браслеты, кольца, чокеры, подвески, вставки, нити-обереги, индивидуальные заказы, шамбала',
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

        $title = "Ювелирные изделия по тегу {$tag->getName()} | Naturalstones Jewerly - Изделия из натуральных камней".$page;

        $this->get('app.seo.updater')->doMagic(null, [
            'title' => $title,
            'description' => "{$pageDesc} Большой выбор ювелирных изделий по тегу " . $tag->getName(),
            'keywords' => 'Натуральные камни, серебро, браслеты, кольца, чокеры, подвески, вставки, нити-обереги, индивидуальные заказы, шамбала',
            'og' => [
                'og:type' => 'website',
                'og:title' => $title,
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

        $title = "Ювелирные изделия цвета {$colour->getName()} | Naturalstones Jewerly - Изделия из натуральных камней".$page;

        $this->get('app.seo.updater')->doMagic(null, [
            'title' => $title,
            'description' => "{$pageDesc} Большой выбор ювелирных изделий цвета {$colour->getName()}",
            'keywords' => 'Натуральные камни, серебро, браслеты, кольца, чокеры, подвески, вставки, нити-обереги, индивидуальные заказы, шамбала',
            'og' => [
                'og:type' => 'website',
                'og:title' => $title,
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
        $router = $this->get('router');
        $breadcrumb = $this->get('app.breadcrumb');
        $breadcrumb->addBreadcrumb(['title' => 'Все камни',  'href' => $router->generate('stone_list')]);
        $breadcrumb->addBreadcrumb(['title' => 'Камень: ' . $stone->getName()]);

        $page = $request->get('page') ? " | Страница {$request->get('page', 1)}" : null;
        $pageDesc = $request->get('page') ? "Страница {$request->get('page', 1)} |" : null;

        $title = "Натуральный камень {$stone->getName()} | Naturalstones Jewerly - Изделия из натуральных камней".$page;

        $this->get('app.seo.updater')->doMagic(null, [
            'title' => $title,
            'description' => "{$pageDesc} Изделия по камню " . $stone->getName(),
            'keywords' => 'Натуральные камни, серебро, браслеты, кольца, чокеры, подвески, вставки, нити-обереги, индивидуальные заказы, шамбала',
            'og' => [
                'og:type' => 'website',
                'og:title' => $title,
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

        $title = $product->getName().' -  купить изделие из натуральных камней | Naturalstones Jewerly - Изделия из натуральных камней';

        $this->get('app.seo.updater')->doMagic(null, [
            'title' => $title,
            'description' => mb_substr($product->getDescription(), 0, 150),
            'keywords' => 'Натуральные камни, серебро, браслеты, кольца, чокеры, подвески, вставки, нити-обереги, индивидуальные заказы, шамбала',
            'og' => [
                'og:site_name' => 'Naturalstones Jewerly - Изделия из натуральных камней',
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