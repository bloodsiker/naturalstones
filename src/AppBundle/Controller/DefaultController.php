<?php

namespace AppBundle\Controller;

use ArticleBundle\Entity\Article;
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

    public function constructorAction(Request $request)
    {
        $breadcrumb = $this->get('app.breadcrumb');
        $breadcrumb->addBreadcrumb(['title' => $this->get('translator')->trans('frontend.breadcrumb.constructor', [], 'AppBundle')]);

        $this->get('app.seo.updater')->doMagic(null, [
            'title' => $this->get('translator')->trans('frontend.meta.meta_title_constructor', [], 'AppBundle'),
            'description' => $this->get('translator')->trans('frontend.meta.meta_description_constructor', [], 'AppBundle'),
            'og' => [
                'og:site_name' => $this->get('translator')->trans('frontend.meta.meta_title_index', [], 'AppBundle'),
                'og:url' => $request->getSchemeAndHttpHost(),
            ],
        ]);

        return $this->render('AppBundle:constructor:index.html.twig');
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function searchAction(Request $request)
    {
        $router = $this->get('router');
        $breadcrumb = $this->get('app.breadcrumb');
        $breadcrumb->addBreadcrumb([
            'title' => $this->get('translator')->trans('frontend.breadcrumb.search', [], 'AppBundle'),
            'href' => $router->generate('search_category', ['search' => $request->get('search')])
        ]);
        $breadcrumb->addBreadcrumb(['title' => $this->get('translator')->trans('frontend.breadcrumb.search_category', [], 'AppBundle')]);

        $this->get('app.seo.updater')->doMagic(null, [
            'title' => $this->get('translator')->trans('frontend.meta.meta_title_search', [], 'AppBundle'),
            'description' => $this->get('translator')->trans('frontend.meta.meta_description_search', [], 'AppBundle'),
        ]);

        return $this->render('AppBundle:search:search.html.twig');
    }

    public function searchCategoryAction(Request $request)
    {
        $breadcrumb = $this->get('app.breadcrumb');
        $breadcrumb->addBreadcrumb(['title' => $this->get('translator')->trans('frontend.breadcrumb.search', [], 'AppBundle')]);

        $this->get('app.seo.updater')->doMagic(null, [
            'title' => $this->get('translator')->trans('frontend.meta.meta_title_search', [], 'AppBundle'),
            'description' => $this->get('translator')->trans('frontend.meta.meta_description_search', [], 'AppBundle'),
        ]);

        return $this->render('AppBundle:search_category:search.html.twig');
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

        $urls['reviews_list']['loc'] = $hostname . $router->generate('review_list');
        foreach ($locales as $local) {
            if ($local === 'uk') {
                $urls['reviews_list'][$local] = $hostname . $router->generate('review_list');
            } else {
                $urls['reviews_list'][$local] = sprintf('%s/%s%s', $hostname, $local, $router->generate('review_list'));
            }
        }

        $urls['constructor']['loc'] = $hostname . $router->generate('constructor');
        foreach ($locales as $local) {
            if ($local === 'uk') {
                $urls['constructor'][$local] = $hostname . $router->generate('constructor');
            } else {
                $urls['constructor'][$local] = sprintf('%s/%s%s', $hostname, $local, $router->generate('constructor'));
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

        $letter = $em->getRepository(Stone::class)->uniqLetterByStone('ru');
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

        $urls['article_list']['loc'] = $hostname . $router->generate('article_list');
        foreach ($locales as $local) {
            if ($local === 'uk') {
                $urls['article_list'][$local] = $hostname . $router->generate('article_list');
            } else {
                $urls['article_list'][$local] = sprintf('%s/%s%s', $hostname, $local, $router->generate('article_list'));
            }
        }

        $articles = $em->getRepository(Article::class)->findBy(['isActive' => true]);
        foreach ($articles as $article) {
            $key = 'article_'.$article->getId();
            $url = $router->generate('article_view', ['category' => $article->getSlug(), 'id' => $article->getId(), 'slug' => $article->getSlug()]);
            $urls[$key]['loc'] = $hostname . $url;
            foreach ($locales as $local) {
                if ($local === 'uk') {
                    $urls[$key][$local] = $hostname . $url;
                } else {
                    $urls[$key][$local] = sprintf('%s/%s%s', $hostname, $local, $url);
                }
            }
        }

        $articleCategories = $em->getRepository(\ArticleBundle\Entity\Category::class)->findBy(['isActive' => true]);
        foreach ($articleCategories as $articleCategory) {
            $key = 'article_category_'.$articleCategory->getId();
            $url = $router->generate('article_category', ['category' => $articleCategory->getSlug()]);
            $urls[$key]['loc'] = $hostname . $url;
            foreach ($locales as $local) {
                if ($local === 'uk') {
                    $urls[$key][$local] = $hostname . $url;
                } else {
                    $urls[$key][$local] = sprintf('%s/%s%s', $hostname, $local, $url);
                }
            }
        }

        $articleTags = $em->getRepository(Tag::class)->getTagsByArticles();
        foreach ($articleTags as $articleTag) {
            $key = 'article_tag_'.$articleTag->getId();
            $url = $router->generate('article_tags_list', ['slug' => $articleTag->getSlug()]);
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

    public function generateFeedAction(Request $request): Response
    {
        $em = $this->getDoctrine()->getEntityManager();
        $hostname = $request->getSchemeAndHttpHost();
        $router = $this->get('router');

        $feedData = [
            ['id', 'title', 'description', 'availability', 'condition', 'price', 'sale_price', 'link', 'image_link', 'brand',
                'fb_product_category', 'gender', 'age_group', 'material', 'rich_text_description']
        ];

        $products = $em->getRepository(Product::class)->findBy(['isActive' => true], ['id' => 'DESC']);
        foreach ($products as $product) {
            $url = $router->generate('product_view', ['category' => $product->getCategory()->getSlug(), 'id' => $product->getId(), 'slug' => $product->getSlug()]);
            $gender = 'female';
            if ($product->getIsMan() && $product->getIsWoman()) {
                $gender = 'unisex';
            } elseif ($product->getIsMan()) {
                $gender = 'male';
            }

            $material = [];
            foreach ($product->getStones() as $stone) {
                $material[] = $stone->translate('uk')->getName();
            }
            foreach ($product->getMetals() as $metal) {
                $material[] = $metal->translate('uk')->getName();
            }

            $materials = implode(', ', $material);

            $category = 'Ювелирные украшения и часы > Ювелирные изделия';
            if (in_array($product->getCategory()->getSlug(), ['braslety', 'nabory', 'niti', 'niti-oberegi', 'shambaly'])) {
                $category = 'Ювелирные украшения и часы > Ювелирные изделия > Браслеты';
            } elseif ($product->getCategory()->getSlug() === 'kolca') {
                $category = 'Ювелирные украшения и часы > Ювелирные изделия > Кольца';
            } elseif ($product->getCategory()->getSlug() === 'kole') {
                $category = 'Ювелирные украшения и часы > Ювелирные изделия > Колье и ожерелья';
            } elseif ($product->getCategory()->getSlug() === 'serezhki') {
                $category = 'Ювелирные украшения и часы > Ювелирные изделия > Серьги';
            } elseif (in_array($product->getCategory()->getSlug(), ['podveski', 'vstavki'])) {
                $category = 'Ювелирные украшения и часы > Ювелирные изделия > Подвески и кулоны';
            }

            array_push($feedData, [
                $product->getId(),
                $product->translate('uk')->getName(),
                $this->facebookText($product->translate('uk')->getDescription()),
                'in stock',
                'new',
                $product->getPrice() . ' UAH',
                $product->getDiscount() ? $product->getDiscount() . ' UAH' : '',
                $hostname . $url,
                $hostname . $product->getImage()->getPath(),
                $this->getParameter('company_name'),
                $category,
                $gender,
                'all ages',
                $materials,
                preg_replace(['/\[(.+?)\]/', '/[\n\r]/'], ['', ' '], $product->translate('uk')->getDescription())
            ]);
        }

        $filePath = $this->getParameter('kernel.project_dir').'/web/feeds/facebook_feed.csv';
        $this->saveFeedToFile($filePath, $feedData);

        $response = new Response();
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'inline; filename="facebook_feed.csv"');
        $response->setContent(file_get_contents($filePath));

        return $response;
    }

    private function saveFeedToFile(string $filePath, array $feedData): void
    {
        $handle = fopen($filePath, 'w');

        foreach ($feedData as $row) {
            fputcsv($handle, $row);
        }

        fclose($handle);
    }

    public function facebookText($text)
    {
        $text = strip_tags($text);

        $text = preg_replace(['/\[(.+?)\]/', '/[\n\r]/'], ['', ' '], $text);

        $text = preg_replace('/(<[^>]+) style=".*?"/i', '$1', $text);

        $text = preg_replace('/ style="[^"]+"/sui', ' ', $text);

        $text = preg_replace('/<style\b[^>]*>(.*?)<\/style>/si', '', $text);

        return $text;
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
