<?php

namespace ArticleBundle\Controller;

use AppBundle\Controller\BaseController;
use AppBundle\Helper\AppHelper;
use AppBundle\Services\BreadcrumbService;
use AppBundle\Services\SeoUpdater;
use ArticleBundle\Entity\Article;
use ArticleBundle\Entity\ArticleRepository;
use ArticleBundle\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use ShareBundle\Entity\Tag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\Cache;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ArticleController extends BaseController
{
    public const ARTICLE_404 = 'Article doesn\'t exist';

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly TranslatorInterface $translator,
        private readonly RouterInterface $router,
        private readonly BreadcrumbService $breadcrumb,
        private readonly SeoUpdater $seoUpdater,
    ) {
    }

    #[Cache(maxage: 60, public: true)]
    public function listAction(Request $request): Response
    {
        $this->breadcrumb->addBreadcrumb([
            'title' => $this->translator->trans('frontend.breadcrumb.articles', [], 'AppBundle'),
        ]);

        $categories = $this->em->getRepository(Category::class)->findBy(['isActive' => true]);

        $this->applySeo(
            $request,
            $this->translator->trans('frontend.meta.meta_title_articles', [], 'AppBundle'),
            $this->translator->trans('frontend.meta.meta_description_articles', [], 'AppBundle'),
        );

        return $this->render('@Article/list.html.twig', ['categories' => $categories]);
    }

    #[Cache(maxage: 60, public: true)]
    public function listCategoryAction(Request $request, string $category): Response
    {
        $repo = $this->em->getRepository(Category::class);
        $categoryObject = $repo->findOneBy(['slug' => $category]);

        if (!$categoryObject) {
            throw $this->createNotFoundException(self::ARTICLE_404);
        }

        $this->addArticlesBreadcrumb();
        $this->breadcrumb->addBreadcrumb(['title' => $categoryObject->getName()]);

        $this->applySeo(
            $request,
            $this->translator->trans('frontend.meta.meta_title_article_category', ['%CATEGORY%' => $categoryObject->getName()], 'AppBundle'),
            $this->translator->trans('frontend.meta.meta_description_article_category', ['%CATEGORY%' => $categoryObject->getName()], 'AppBundle'),
        );

        return $this->render('@Article/list_category.html.twig', [
            'category' => $categoryObject,
            'categories' => $repo->findBy(['isActive' => true]),
        ]);
    }

    #[Cache(maxage: 60, public: true)]
    public function listTagAction(Request $request): Response
    {
        $tag = $this->em->getRepository(Tag::class)->findOneBy(['slug' => $request->get('slug')]);
        if (!$tag) {
            throw $this->createNotFoundException(self::ARTICLE_404);
        }

        $this->addArticlesBreadcrumb();
        $this->breadcrumb->addBreadcrumb(['title' => $tag->getName()]);

        $this->applySeo(
            $request,
            $this->translator->trans('frontend.meta.meta_title_article_tag', ['%TAG%' => $tag->getName()], 'AppBundle'),
            $this->translator->trans('frontend.meta.meta_description_article_tag', ['%TAG%' => $tag->getName()], 'AppBundle'),
        );

        return $this->render('@Article/list_tag.html.twig', [
            'tag' => $tag,
            'categories' => $this->em->getRepository(Category::class)->findBy(['isActive' => true]),
        ]);
    }

    public function viewAction(Request $request): Response
    {
        /** @var ArticleRepository $repo */
        $repo = $this->em->getRepository(Article::class);
        $article = $repo->findOneBy(['id' => $request->get('id'), 'isActive' => true]);

        if (!$article) {
            throw $this->createNotFoundException(self::ARTICLE_404);
        }

        $this->addArticlesBreadcrumb();
        if ($article->getCategory()) {
            $this->breadcrumb->addBreadcrumb([
                'title' => $article->getCategory()->getName(),
                'href' => $this->router->generate('article_category', ['category' => $article->getCategory()->getSlug()]),
            ]);
        }
        $this->breadcrumb->addBreadcrumb(['title' => $article->getTitle()]);

        $title = $article->getTitle() . ' - ' . $this->translator->trans('frontend.meta.meta_title_index', [], 'AppBundle');

        $this->seoUpdater->doMagic(null, [
            'title' => $title,
            'description' => $article->shortDescription(),
            'og' => [
                'og:site_name' => $this->translator->trans('frontend.meta.meta_title_index', [], 'AppBundle'),
                'og:type' => 'article',
                'og:title' => $title,
                'og:url' => $request->getSchemeAndHttpHost(),
                'og:image' => $request->getSchemeAndHttpHost() . $article->getImage()->getPath(),
                'og:description' => $article->shortDescription(),
            ],
        ]);

        if (!AppHelper::isBot($request->headers->get('User-Agent'))) {
            $repo->incViewCounter($article->getId());
        }

        return $this->render('@Article/view.html.twig', ['article' => $article]);
    }

    private function applySeo(Request $request, string $title, string $description): void
    {
        $title .= $this->pageSuffix($request);
        $description = $this->pageDescriptionPrefix($request) . $description;

        $this->seoUpdater->doMagic(null, [
            'title' => $title,
            'description' => $description,
            'og' => [
                'og:site_name' => $this->translator->trans('frontend.meta.meta_title_index', [], 'AppBundle'),
                'og:type' => 'website',
                'og:title' => $title,
                'og:url' => $request->getSchemeAndHttpHost(),
            ],
        ]);
    }

    private function addArticlesBreadcrumb(): void
    {
        $this->breadcrumb->addBreadcrumb([
            'title' => $this->translator->trans('frontend.breadcrumb.articles', [], 'AppBundle'),
            'href' => $this->router->generate('article_list'),
        ]);
    }

    private function pageSuffix(Request $request): string
    {
        return $request->get('page')
            ? ' | ' . $this->translator->trans('frontend.page', [], 'AppBundle') . $request->get('page', 1)
            : '';
    }

    private function pageDescriptionPrefix(Request $request): string
    {
        return $request->get('page')
            ? $this->translator->trans('frontend.page', [], 'AppBundle') . $request->get('page', 1) . ' |'
            : '';
    }
}