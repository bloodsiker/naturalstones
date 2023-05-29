<?php

namespace ArticleBundle\Controller;

use AppBundle\Helper\AppHelper;
use ArticleBundle\Entity\Article;
use ArticleBundle\Entity\Category;
use ShareBundle\Entity\Tag;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;

/**
 * Class ArticleController
 */
class ArticleController extends Controller
{
    const ARTICLE_404 = 'Article doesn\'t exist';

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
        $breadcrumb->addBreadcrumb(['title' => $this->get('translator')->trans('frontend.breadcrumb.articles', [], 'AppBundle')]);

        $repo = $this->getDoctrine()->getManager()->getRepository(Category::class);
        $categories = $repo->findBy(['isActive' => true]);

        $page = $request->get('page') ? " | " . $this->get('translator')->trans('frontend.page', [], 'AppBundle') .$request->get('page', 1) : null;
        $pageDesc = $request->get('page') ? $this->get('translator')->trans('frontend.page', [], 'AppBundle') . $request->get('page', 1) . " |" : null;

        $title = $this->get('translator')->trans('frontend.meta.meta_title_articles', [], 'AppBundle') . $page;
        $description = $pageDesc . $this->get('translator')->trans('frontend.meta.meta_description_articles', [], 'AppBundle');

        $this->get('app.seo.updater')->doMagic(null, [
            'title' => $title,
            'description' => $description,
            'og' => [
                'og:site_name' => $this->get('translator')->trans('frontend.meta.meta_title_index', [], 'AppBundle'),
                'og:type' => 'website',
                'og:title' => $title,
                'og:url' => $request->getSchemeAndHttpHost(),
            ],
        ]);

        return $this->render('ArticleBundle::list.html.twig', ['categories' => $categories]);
    }

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @Cache(maxage=60, public=true)
     */
    public function listCategoryAction(Request $request, $category)
    {
        $repo = $this->getDoctrine()->getManager()->getRepository(Category::class);
        $categoryObject = $repo->findOneBy(['slug' => $category]);

        if (!$categoryObject) {
            throw $this->createNotFoundException(self::ARTICLE_404);
        }

        $categories = $repo->findBy(['isActive' => true]);

        $router = $this->get('router');
        $breadcrumb = $this->get('app.breadcrumb');
        $breadcrumb->addBreadcrumb([
            'title' => $this->get('translator')->trans('frontend.breadcrumb.articles', [], 'AppBundle'),
            'href' => $router->generate('article_list'),
        ]);
        $breadcrumb->addBreadcrumb(['title' => $categoryObject->getName()]);

        $page = $request->get('page') ? " | " . $this->get('translator')->trans('frontend.page', [], 'AppBundle') .$request->get('page', 1) : null;
        $pageDesc = $request->get('page') ? $this->get('translator')->trans('frontend.page', [], 'AppBundle') . $request->get('page', 1) . " |" : null;

        $title = $this->get('translator')->trans('frontend.meta.meta_title_article_category', ['%CATEGORY%' => $categoryObject->getName()], 'AppBundle') . $page;
        $description = $pageDesc . $this->get('translator')->trans('frontend.meta.meta_description_article_category', ['%CATEGORY%' => $categoryObject->getName()], 'AppBundle');

        $this->get('app.seo.updater')->doMagic(null, [
            'title' => $title,
            'description' => $description,
            'og' => [
                'og:site_name' => $this->get('translator')->trans('frontend.meta.meta_title_index', [], 'AppBundle'),
                'og:type' => 'website',
                'og:title' => $title,
                'og:url' => $request->getSchemeAndHttpHost(),
            ],
        ]);

        return $this->render('ArticleBundle::list_category.html.twig', [
            'category' => $categoryObject,
            'categories' => $categories
        ]);
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
        $repo = $this->getDoctrine()->getManager()->getRepository(Tag::class);
        $tag = $repo->findOneBy(['slug' => $request->get('slug')]);

        if (!$tag) {
            throw $this->createNotFoundException(self::ARTICLE_404);
        }

        $repoCategory = $this->getDoctrine()->getManager()->getRepository(Category::class);
        $categories = $repoCategory->findBy(['isActive' => true]);

        $router = $this->get('router');
        $breadcrumb = $this->get('app.breadcrumb');
        $breadcrumb->addBreadcrumb([
            'title' => $this->get('translator')->trans('frontend.breadcrumb.articles', [], 'AppBundle'),
            'href' => $router->generate('article_list'),
        ]);
        $breadcrumb->addBreadcrumb(['title' => $tag->getName()]);

        $page = $request->get('page') ? " | " . $this->get('translator')->trans('frontend.page', [], 'AppBundle') .$request->get('page', 1) : null;
        $pageDesc = $request->get('page') ? $this->get('translator')->trans('frontend.page', [], 'AppBundle') . $request->get('page', 1) . " |" : null;

        $title = $this->get('translator')->trans('frontend.meta.meta_title_article_tag', ['%TAG%' => $tag->getName()], 'AppBundle') . $page;
        $description = $pageDesc . $this->get('translator')->trans('frontend.meta.meta_description_article_tag', ['%TAG%' => $tag->getName()], 'AppBundle');

        $this->get('app.seo.updater')->doMagic(null, [
            'title' => $title,
            'description' => $description,
            'og' => [
                'og:site_name' => $this->get('translator')->trans('frontend.meta.meta_title_index', [], 'AppBundle'),
                'og:type' => 'website',
                'og:title' => $title,
                'og:url' => $request->getSchemeAndHttpHost(),
            ],
        ]);

        return $this->render('ArticleBundle::list_tag.html.twig', [
            'tag' => $tag,
            'categories' => $categories
        ]);
    }

    public function viewAction(Request $request)
    {
        $repo = $this->getDoctrine()->getManager()->getRepository(Article::class);
        $article = $repo->findOneBy(['id' => $request->get('id'),'isActive' => true]);

        if (!$article) {
            throw $this->createNotFoundException(self::ARTICLE_404);
        }

        $router = $this->get('router');
        $breadcrumb = $this->get('app.breadcrumb');
        $breadcrumb->addBreadcrumb([
            'title' => $this->get('translator')->trans('frontend.breadcrumb.articles', [], 'AppBundle'),
            'href' => $router->generate('article_list'),
        ]);

        if ($article->getCategory()) {
            $breadcrumb->addBreadcrumb([
                'title' => $article->getCategory()->getName(),
                'href' => $router->generate('article_category', ['category' => $article->getCategory()->getSlug()]),
            ]);
        }

        $breadcrumb->addBreadcrumb(['title' => $article->getTitle()]);

        $title = $article->getTitle().' - '. $this->get('translator')->trans('frontend.meta.meta_title_index', [], 'AppBundle');

        $this->get('app.seo.updater')->doMagic(null, [
            'title' => $title,
            'description' => $article->shortDescription(),
            'og' => [
                'og:site_name' => $this->get('translator')->trans('frontend.meta.meta_title_index', [], 'AppBundle'),
                'og:type' => 'article',
                'og:title' => $title,
                'og:url' => $request->getSchemeAndHttpHost(),
                'og:image' => $request->getSchemeAndHttpHost().$article->getImage()->getPath(),
                'og:description' => $article->shortDescription(),
            ],
        ]);

        if (!AppHelper::isBot($request->headers->get('User-Agent'))) {
            $repo->incViewCounter($article->getId());
        }

        return $this->render('ArticleBundle::view.html.twig', ['article' => $article]);
    }
}