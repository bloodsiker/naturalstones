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
        $metaTitle = $this->get('translator')->trans('frontend.meta.meta_title_index', [], 'AppBundle');
        $metaDescription = $this->get('translator')->trans('frontend.meta.meta_description_index', ['%YEAR%' => date("Y")], 'AppBundle');

        $this->get('app.seo.updater')->doMagic(null, [
            'title' => $metaTitle,
            'description' => $metaDescription,
            'og' => [
                'og:site_name' => $metaTitle,
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
        $breadcrumb->addBreadcrumb(['title' => $this->get('translator')->trans('frontend.breadcrumb.cart', [], 'AppBundle')]);

        $this->get('app.seo.updater')->doMagic(null, [
            'title' => $this->get('translator')->trans('frontend.meta.meta_title_cart', [], 'AppBundle'),
            'description' => $this->get('translator')->trans('frontend.meta.meta_description_cart', [], 'AppBundle'),
            'og' => [
                'og:site_name' => $this->get('translator')->trans('frontend.meta.meta_title_index', [], 'AppBundle'),
                'og:url' => $request->getSchemeAndHttpHost(),
            ],
        ]);

        return $this->render('AppBundle:cart:cart.html.twig');
    }

    public function cartStepOneAction(Request $request)
    {
        $breadcrumb = $this->get('app.breadcrumb');
        $breadcrumb->addBreadcrumb(['title' => $this->get('translator')->trans('frontend.breadcrumb.step', [], 'AppBundle')]);

        $this->get('app.seo.updater')->doMagic(null, [
            'title' => $this->get('translator')->trans('frontend.meta.meta_title_cart_step', ['%STEP%' => 1], 'AppBundle'),
            'description' => $this->get('translator')->trans('frontend.meta.meta_description_cart_step', ['%STEP%' => 1], 'AppBundle'),
            'og' => [
                'og:site_name' => $this->get('translator')->trans('frontend.meta.meta_title_index', [], 'AppBundle'),
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
        $breadcrumb->addBreadcrumb(['title' => $this->get('translator')->trans('frontend.breadcrumb.step', [], 'AppBundle')]);

        $this->get('app.seo.updater')->doMagic(null, [
            'title' => $this->get('translator')->trans('frontend.meta.meta_title_cart_step', ['%STEP%' => 2], 'AppBundle'),
            'description' => $this->get('translator')->trans('frontend.meta.meta_description_cart_step', ['%STEP%' => 2], 'AppBundle'),
            'og' => [
                'og:site_name' => $this->get('translator')->trans('frontend.meta.meta_title_index', [], 'AppBundle'),
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
        $breadcrumb->addBreadcrumb(['title' => $this->get('translator')->trans('frontend.breadcrumb.search', [], 'AppBundle')]);

        $this->get('app.seo.updater')->doMagic(null, [
            'title' => $this->get('translator')->trans('frontend.meta.meta_title_search', [], 'AppBundle'),
            'description' => $this->get('translator')->trans('frontend.meta.meta_description_search', [], 'AppBundle'),
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

        $locales = $this->getParameter('locales');

        $urls = [];
        $hostname = $request->getSchemeAndHttpHost();
        $router = $this->get('router');

        $urls['main']['loc'] = $hostname . $router->generate('index');
        foreach ($locales as $local) {
            if ($local === 'uk') {
                $urls['main'][$local] = $hostname . $router->generate('index');
            } else {
                $urls['main'][$local] = sprintf('%s/%s%s', $hostname, $local, $router->generate('index'));
            }
        }

        $urls['stone_list']['loc'] = $hostname . $router->generate('stone_list');
        foreach ($locales as $local) {
            if ($local === 'uk') {
                $urls['stone_list'][$local] = $hostname . $router->generate('stone_list');
            } else {
                $urls['stone_list'][$local] = sprintf('%s/%s%s', $hostname, $local, $router->generate('stone_list'));
            }
        }

        $categories = $em->getRepository(Category::class)->findBy(['isActive' => true]);
        foreach ($categories as $category) {
            $key = 'category_'.$category->getId();
            $url = $router->generate('product_list', ['slug' => $category->getSlug()]);
            $urls[$key]['loc'] = $hostname . $url;
            foreach ($locales as $local) {
                if ($local === 'uk') {
                    $urls[$key][$local] = $hostname . $url;
                } else {
                    $urls[$key][$local] = sprintf('%s/%s%s', $hostname, $local, $url);
                }
            }
        }

        $products = $em->getRepository(Product::class)->findBy(['isActive' => true], ['id' => 'DESC']);
        foreach ($products as $product) {
            $key = 'product_'.$product->getId();
            $url = $router->generate('product_view', ['category' => $product->getCategory()->getSlug(), 'id' => $product->getId(), 'slug' => $product->getSlug()]);
            $urls[$key]['loc'] = $hostname . $url;
            foreach ($locales as $local) {
                if ($local === 'uk') {
                    $urls[$key][$local] = $hostname . $url;
                } else {
                    $urls[$key][$local] = sprintf('%s/%s%s', $hostname, $local, $url);
                }
            }
        }

        $stones = $em->getRepository(Stone::class)->findBy(['isActive' => true]);
        foreach ($stones as $stone) {
            $key = 'stone_'.$stone->getId();
            $url = $router->generate('product_stone_list', ['slug' => $stone->getSlug()]);
            $urls[$key]['loc'] = $hostname . $url;
            foreach ($locales as $local) {
                if ($local === 'uk') {
                    $urls[$key][$local] = $hostname . $url;
                } else {
                    $urls[$key][$local] = sprintf('%s/%s%s', $hostname, $local, $url);
                }
            }
        }

        $letter = $em->getRepository(Stone::class)->uniqLetterByStone();
        foreach ($letter as $key => $let) {
            if (isset($let[1])) {
                $key = 'stone_letter_'.$key;
                $url = $router->generate('stone_list_letter', ['letter' => $let[1]]);
                $urls[$key]['loc'] = $hostname . $url;
                foreach ($locales as $local) {
                    if ($local === 'uk') {
                        $urls[$key][$local] = $hostname . $url;
                    } else {
                        $urls[$key][$local] = sprintf('%s/%s%s', $hostname, $local, $url);
                    }
                }
            }
        }

        $zodiacs = $em->getRepository(Zodiac::class)->findBy(['isActive' => true]);
        foreach ($zodiacs as $zodiac) {
            $key = 'zodiac_'.$zodiac->getId();
            $url = $router->generate('zodiac_stone_list', ['slug' => $zodiac->getSlug()]);
            $urls[$key]['loc'] = $hostname . $url;
            foreach ($locales as $local) {
                if ($local === 'uk') {
                    $urls[$key][$local] = $hostname . $url;
                } else {
                    $urls[$key][$local] = sprintf('%s/%s%s', $hostname, $local, $url);
                }
            }
        }

        $colours = $em->getRepository(Colour::class)->findBy(['isActive' => true]);
        foreach ($colours as $colour) {
            $key = 'colour_'.$colour->getId();
            $url = $router->generate('product_colour_list', ['slug' => $colour->getSlug()]);
            $urls[$key]['loc'] = $hostname . $url;
            foreach ($locales as $local) {
                if ($local === 'uk') {
                    $urls[$key][$local] = $hostname . $url;
                } else {
                    $urls[$key][$local] = sprintf('%s/%s%s', $hostname, $local, $url);
                }
            }
        }

        $tags = $em->getRepository(Tag::class)->findBy(['isActive' => true]);
        foreach ($tags as $tag) {
            $key = 'tag_'.$colour->getId();
            $url = $router->generate('product_tags_list', ['slug' => $tag->getSlug()]);
            $urls[$key]['loc'] = $hostname . $url;
            foreach ($locales as $local) {
                if ($local === 'uk') {
                    $urls[$key][$local] = $hostname . $url;
                } else {
                    $urls[$key][$local] = sprintf('%s/%s%s', $hostname, $local, $url);
                }
            }
        }

        foreach (['man', 'woman'] as $who) {
            $key = $who;
            $url = $router->generate('product_who_list', ['who' => $who]);
            $urls[$key]['loc'] = $hostname . $url;
            foreach ($locales as $local) {
                if ($local === 'uk') {
                    $urls[$key][$local] = $hostname . $url;
                } else {
                    $urls[$key][$local] = sprintf('%s/%s%s', $hostname, $local, $url);
                }
            }
        }

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

    /**
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function removeTrailingSlashAction(Request $request)
    {
        $pathInfo = $request->getPathInfo();
        $requestUri = $request->getRequestUri();

        $url = str_replace($pathInfo, rtrim($pathInfo, ' /'), $requestUri);

        return $this->redirect($url, 301);
    }
}
