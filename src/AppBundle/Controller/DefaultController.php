<?php

namespace AppBundle\Controller;

use BookBundle\Entity\Book;
use BookBundle\Entity\BookCollection;
use GenreBundle\Entity\Genre;
use ProductBundle\Entity\Category;
use ProductBundle\Entity\Product;
use ShareBundle\Entity\Colour;
use ShareBundle\Entity\Stone;
use ShareBundle\Entity\Tag;
use ShareBundle\Entity\Zodiac;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
            'title' => 'Naturalstones Jewerly - Изделия из натуральных камней'.$page,
            'description' => $pageDesc.'Когда хочется дарить яркие эмоции и незабываемые впечатления своим родным и близким выбирайте прекрасные изделия от компании «Naturalstones Jewerly»',
            'keywords' => 'Натуральные камни, серебро, браслеты, кольца, чокеры, подвески',
            'og' => [
                'og:site_name' => 'Naturalstones Jewerly - Изделия из натуральных камней',
                'og:url' => $request->getSchemeAndHttpHost(),
            ],
        ]);

        return new Response();
    }

    /**
     * @return Response
     */
    public function cartAction(Request $request)
    {
        $breadcrumb = $this->get('app.breadcrumb');
        $breadcrumb->addBreadcrumb(['title' => 'Корзина']);

        $this->get('app.seo.updater')->doMagic(null, [
            'title' => 'Корзина | Naturalstones Jewerly - Изделия из натуральных камней',
            'description' => 'Корзина | Оформить заказ',
            'keywords' => 'Натуральные камни, серебро, браслеты, кольца, чокеры, подвески',
            'og' => [
                'og:site_name' => 'Naturalstones Jewerly - Изделия из натуральных камней',
                'og:url' => $request->getSchemeAndHttpHost(),
            ],
        ]);

        return $this->render('AppBundle:cart:cart.html.twig');
    }

    public function cartStepOneAction(Request $request)
    {
        $breadcrumb = $this->get('app.breadcrumb');
        $breadcrumb->addBreadcrumb(['title' => 'Оформление заказа']);

        $this->get('app.seo.updater')->doMagic(null, [
            'title' => 'Корзина, шаг 1 | Naturalstones Jewerly - Изделия из натуральных камней',
            'description' => 'Корзина, шаг 1 | Оформить заказ',
            'keywords' => 'Натуральные камни, серебро, браслеты, кольца, чокеры, подвески',
            'og' => [
                'og:site_name' => 'Naturalstones Jewerly - Изделия из натуральных камней',
                'og:title' => 'Корзина, шаг 1',
                'og:url' => $request->getSchemeAndHttpHost(),
            ],
        ]);

        return $this->render('AppBundle:cart:step_1.html.twig');
    }

    public function cartStepTwoAction(Request $request)
    {
        $session = $this->get('session');
        $session->set('infoCart', $request->query->all());
        $breadcrumb = $this->get('app.breadcrumb');
        $breadcrumb->addBreadcrumb(['title' => 'Оформление заказа']);

        $this->get('app.seo.updater')->doMagic(null, [
            'title' => 'Корзина, шаг 2 | Naturalstones Jewerly - Изделия из натуральных камней',
            'description' => 'Корзина, шаг 2 | Оформить заказ',
            'keywords' => 'Натуральные камни, серебро, браслеты, кольца, чокеры, подвески',
            'og' => [
                'og:site_name' => 'Naturalstones Jewerly - Изделия из натуральных камней',
                'og:title' => 'Корзина, шаг 2',
                'og:url' => $request->getSchemeAndHttpHost(),
            ],
        ]);

        return $this->render('AppBundle:cart:step_2.html.twig');
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
        $urls[] = ['loc' => $router->generate('stone_list'), 'changefreq' => 'weekly', 'priority' => '0.75'];

        $categories = $em->getRepository(Category::class)->findBy(['isActive' => true]);
        foreach ($categories as $category) {
            $urls[] = ['loc' => $router->generate('product_list', ['slug' => $category->getSlug()]), 'changefreq' => 'weekly', 'priority' => '0.75'];
        }

        $products = $em->getRepository(Product::class)->findBy(['isActive' => true], ['id' => 'DESC']);
        foreach ($products as $product) {
            $urls[] = ['loc' => $router->generate('product_view', ['category' => $product->getCategory()->getSlug(), 'id' => $product->getId(), 'slug' => $product->getSlug()]), 'changefreq' => 'weekly', 'priority' => '0.75'];
        }

        $stones = $em->getRepository(Stone::class)->findBy(['isActive' => true]);
        foreach ($stones as $stone) {
            $urls[] = ['loc' => $router->generate('product_stone_list', ['slug' => $stone->getSlug()]), 'changefreq' => 'weekly', 'priority' => '0.5'];
        }

        $zodiacs = $em->getRepository(Zodiac::class)->findBy(['isActive' => true]);
        foreach ($zodiacs as $zodiac) {
            $urls[] = ['loc' => $router->generate('zodiac_stone_list', ['slug' => $zodiac->getSlug()]), 'changefreq' => 'weekly', 'priority' => '0.5'];
        }

        $colours = $em->getRepository(Colour::class)->findBy(['isActive' => true]);
        foreach ($colours as $colour) {
            $urls[] = ['loc' => $router->generate('product_colour_list', ['slug' => $colour->getSlug()]), 'changefreq' => 'weekly', 'priority' => '0.5'];
        }

        $tags = $em->getRepository(Tag::class)->findBy(['isActive' => true]);
        foreach ($tags as $tag) {
            $urls[] = ['loc' => $router->generate('product_tags_list', ['slug' => $tag->getSlug()]), 'changefreq' => 'weekly', 'priority' => '0.5'];
        }

        foreach (['man', 'woman'] as $who) {
            $urls[] = ['loc' => $router->generate('product_who_list', ['who' => $who]), 'changefreq' => 'weekly', 'priority' => '0.5'];
        }

//        $urls[] = ['loc' => $router->generate('search'), 'changefreq' => 'weekly', 'priority' => '0.3'];

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
