<?php

namespace ProductBundle\Controller;

use AppBundle\Helper\AppHelper;
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
        $router = $this->get('router');
        $repo = $this->getDoctrine()->getManager()->getRepository(Category::class);
        $category = $repo->findOneBy(['slug' => $slug]);
        $breadcrumb = $this->get('app.breadcrumb');
        $breadcrumb->addBreadcrumb(['title' => $category->getName()]);

        $page = $request->get('page') ? " | " . $this->get('translator')->trans('frontend.page', [], 'AppBundle') .$request->get('page', 1) : null;
        $pageDesc = $request->get('page') ? $this->get('translator')->trans('frontend.page', [], 'AppBundle') . $request->get('page', 1) . " |" : null;

        $this->get('app.seo.updater')->doMagic(null, [
            'title' => $this->get('translator')->trans('frontend.meta.meta_title_products', ['%CATEGORY%' => $category->getName()], 'AppBundle').$page,
            'description' => $pageDesc . $this->get('translator')->trans('frontend.meta.meta_description_products', ['%CATEGORY%' => $category->getName()], 'AppBundle'),
            'og' => [
                'og:url' => $request->getSchemeAndHttpHost(),
            ],
            'canonicalUrl' => $router->generate('product_list', ['slug' => $slug, 'page' => $request->get('page')], 0),
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
        $whoIs = $this->get('translator')->trans(Product::$whois[$who], [], 'AppBundle');
        $breadcrumb = $this->get('app.breadcrumb');
        $breadcrumb->addBreadcrumb(['title' => $whoIs]);

        $page = $request->get('page') ? " | " . $this->get('translator')->trans('frontend.page', [], 'AppBundle') .$request->get('page', 1) : null;
        $pageDesc = $request->get('page') ? $this->get('translator')->trans('frontend.page', [], 'AppBundle') . $request->get('page', 1) . " |" : null;

        $this->get('app.seo.updater')->doMagic(null, [
            'title' => $this->get('translator')->trans('frontend.meta.meta_title_who', ['%WHO%' => $whoIs], 'AppBundle') . $page,
            'description' => $pageDesc . $this->get('translator')->trans('frontend.meta.meta_description_who', [], 'AppBundle'),
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
        $breadcrumb->addBreadcrumb(['title' => $this->get('translator')->trans('frontend.breadcrumb.tag', [], 'AppBundle') . $tag->getName()]);

        $page = $request->get('page') ? " | " . $this->get('translator')->trans('frontend.page', [], 'AppBundle') .$request->get('page', 1) : null;
        $pageDesc = $request->get('page') ? $this->get('translator')->trans('frontend.page', [], 'AppBundle') . $request->get('page', 1) . " |" : null;

        $title = $this->get('translator')->trans('frontend.meta.meta_title_tag', ['%TAG%' => $tag->getName()], 'AppBundle') . $page;

        $this->get('app.seo.updater')->doMagic(null, [
            'title' => $title,
            'description' => $pageDesc . $this->get('translator')->trans('frontend.meta.meta_description_tag', ['%TAG%' => $tag->getName()], 'AppBundle'),
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
        $breadcrumb->addBreadcrumb(['title' => $this->get('translator')->trans('frontend.breadcrumb.colour', [], 'AppBundle') . $colour->getName()]);

        $page = $request->get('page') ? " | " . $this->get('translator')->trans('frontend.page', [], 'AppBundle') .$request->get('page', 1) : null;
        $pageDesc = $request->get('page') ? $this->get('translator')->trans('frontend.page', [], 'AppBundle') . $request->get('page', 1) . " |" : null;

        $title = $this->get('translator')->trans('frontend.meta.meta_title_colour', ['%COLOUR%' => $colour->getName()], 'AppBundle') . $page;

        $this->get('app.seo.updater')->doMagic(null, [
            'title' => $title,
            'description' => $pageDesc . $this->get('translator')->trans('frontend.meta.meta_description_colour', ['%COLOUR%' => $colour->getName()], 'AppBundle'),
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
        $breadcrumb->addBreadcrumb(['title' => $this->get('translator')->trans('frontend.breadcrumb.stones', [], 'AppBundle'),  'href' => $router->generate('stone_list')]);
        $breadcrumb->addBreadcrumb(['title' => $this->get('translator')->trans('frontend.breadcrumb.stone', [], 'AppBundle') . $stone->getName()]);

        $page = $request->get('page') ? " | " . $this->get('translator')->trans('frontend.page', [], 'AppBundle') .$request->get('page', 1) : null;
        $pageDesc = $request->get('page') ? $this->get('translator')->trans('frontend.page', [], 'AppBundle') . $request->get('page', 1) . " |" : null;

        $title = $this->get('translator')->trans('frontend.meta.meta_title_stones', ['%STONE%' => $stone->getName()], 'AppBundle') . $page;

        $this->get('app.seo.updater')->doMagic(null, [
            'title' => $title,
            'description' => $pageDesc . $this->get('translator')->trans('frontend.meta.meta_description_stones', ['%STONE%' => $stone->getName()], 'AppBundle'),
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
        if (!$product) {
            throw $this->createNotFoundException(self::PRODUCT_404);
        }

        if (!$product->getIsActive()) {
           return $this->redirectToRoute('product_list', ['slug' => $product->getCategory()->getSlug()]);
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

        $title = $product->getName().' - '. $this->get('translator')->trans('frontend.meta.meta_title_single_product', [], 'AppBundle');

        $this->get('app.seo.updater')->doMagic(null, [
            'title' => $title,
            'description' => mb_substr($product->getDescription(), 0, 150),
            'og' => [
                'og:site_name' => $this->get('translator')->trans('frontend.meta.meta_title_index', [], 'AppBundle'),
                'og:type' => 'article',
                'og:title' => $title,
                'og:url' => $request->getSchemeAndHttpHost(),
                'og:image' => $request->getSchemeAndHttpHost().$product->getImage()->getPath(),
                'og:description' => mb_substr($product->getDescription(), 0, 150),
            ],
        ]);

        if (!AppHelper::isBot($request->headers->get('User-Agent'))) {
            $repo->incViewCounter($product->getId());
            $this->container->get('product.helper.views')->doView($product);
        }

        return $this->render('ProductBundle::product_view.html.twig', ['product' => $product, 'sizes' => $sizes]);
    }
}