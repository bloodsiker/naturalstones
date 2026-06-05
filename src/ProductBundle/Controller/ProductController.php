<?php

namespace ProductBundle\Controller;

use AppBundle\Controller\BaseController;
use AppBundle\Helper\AppHelper;
use AppBundle\Services\BreadcrumbService;
use AppBundle\Services\SaveStateValue;
use AppBundle\Services\SeoUpdater;
use Doctrine\ORM\EntityManagerInterface;
use ProductBundle\Entity\Category;
use ProductBundle\Entity\Product;
use ProductBundle\Entity\ProductRepository;
use ProductBundle\Helper\ProductViewHelper;
use ShareBundle\Entity\Colour;
use ShareBundle\Entity\Stone;
use ShareBundle\Entity\Tag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\Cache;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ProductController extends BaseController
{
    public const PRODUCT_404 = 'Product doesn\'t exist';

    private const SEO_CATEGORY_SLUGS = ['braslety', 'kole', 'podveski', 'niti-oberegi'];
    private const DESCRIPTION_LIMIT = 140;

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly TranslatorInterface $translator,
        private readonly RouterInterface $router,
        private readonly BreadcrumbService $breadcrumb,
        private readonly SeoUpdater $seoUpdater,
        private readonly ProductViewHelper $productViewHelper,
    ) {
    }

    #[Cache(maxage: 60, public: true)]
    public function listAction(Request $request): Response
    {
        $slug = $request->get('slug');
        $category = $this->em->getRepository(Category::class)->findOneBy(['slug' => $slug]);

        if (!$category instanceof Category) {
            return $this->redirectToRoute('index');
        }

        $this->breadcrumb->addBreadcrumb(['title' => $category->getName()]);

        $categorySeo = $this->getCategorySeo($category->getSlug(), $category->getName());
        $metaTitle = $categorySeo['meta_title']
            ?? $this->translator->trans('frontend.meta.meta_title_products', ['%CATEGORY%' => $category->getName()], 'AppBundle');
        $metaDescription = $categorySeo['meta_description']
            ?? $this->translator->trans('frontend.meta.meta_description_products', ['%CATEGORY%' => $category->getName()], 'AppBundle');

        $this->seoUpdater->doMagic(null, $this->buildListSeo($request, $metaTitle, $metaDescription, [
            'canonicalUrl' => $this->router->generate('product_list', ['slug' => $slug, 'page' => $request->get('page')], 0),
        ]));

        return $this->render('@Product/product_list.html.twig', [
            'category' => $category,
            'categorySeo' => $categorySeo,
        ]);
    }

    #[Cache(maxage: 60, public: true)]
    public function listWhoAction(Request $request): Response
    {
        $who = $request->get('who');
        $whoIs = $this->translator->trans(Product::$whois[$who], [], 'AppBundle');
        $this->breadcrumb->addBreadcrumb(['title' => $whoIs]);

        $this->seoUpdater->doMagic(null, $this->buildListSeo(
            $request,
            $this->translator->trans('frontend.meta.meta_title_who', ['%WHO%' => $whoIs], 'AppBundle'),
            $this->translator->trans('frontend.meta.meta_description_who', [], 'AppBundle'),
        ));

        return $this->render('@Product/product_list_who.html.twig', ['whois' => $whoIs]);
    }

    #[Cache(maxage: 60, public: true)]
    public function listTagAction(Request $request): Response
    {
        $tag = $this->em->getRepository(Tag::class)->findOneBy(['slug' => $request->get('slug')]);
        $this->breadcrumb->addBreadcrumb([
            'title' => $this->translator->trans('frontend.breadcrumb.tag', [], 'AppBundle') . $tag->getName(),
        ]);

        $title = $this->translator->trans('frontend.meta.meta_title_tag', ['%TAG%' => $tag->getName()], 'AppBundle');
        $description = $this->translator->trans('frontend.meta.meta_description_tag', ['%TAG%' => $tag->getName()], 'AppBundle');

        $this->seoUpdater->doMagic(null, $this->buildListSeo($request, $title, $description, [
            'og' => $this->ogWebsite($request, $title . $this->pageSuffix($request)),
        ]));

        return $this->render('@Product/product_list_tag.html.twig', ['tag' => $tag]);
    }

    #[Cache(maxage: 60, public: true)]
    public function listColourAction(Request $request): Response
    {
        $colour = $this->em->getRepository(Colour::class)->findOneBy(['slug' => $request->get('slug')]);
        $this->breadcrumb->addBreadcrumb([
            'title' => $this->translator->trans('frontend.breadcrumb.colour', [], 'AppBundle') . $colour->getName(),
        ]);

        $title = $this->translator->trans('frontend.meta.meta_title_colour', ['%COLOUR%' => $colour->getName()], 'AppBundle');
        $description = $this->translator->trans('frontend.meta.meta_description_colour', ['%COLOUR%' => $colour->getName()], 'AppBundle');

        $this->seoUpdater->doMagic(null, $this->buildListSeo($request, $title, $description, [
            'og' => $this->ogWebsite($request, $title . $this->pageSuffix($request)),
        ]));

        return $this->render('@Product/product_list_colour.html.twig', ['colour' => $colour]);
    }

    #[Cache(maxage: 60, public: true)]
    public function listStoneAction(Request $request): Response
    {
        $stone = $this->em->getRepository(Stone::class)->findOneBy(['slug' => $request->get('slug')]);
        $this->breadcrumb->addBreadcrumb([
            'title' => $this->translator->trans('frontend.breadcrumb.stones', [], 'AppBundle'),
            'href' => $this->router->generate('stone_list'),
        ]);
        $this->breadcrumb->addBreadcrumb([
            'title' => $this->translator->trans('frontend.breadcrumb.stone', [], 'AppBundle') . $stone->getName(),
        ]);

        $title = $this->translator->trans('frontend.meta.meta_title_stones', ['%STONE%' => $stone->getName()], 'AppBundle');
        $description = $this->translator->trans('frontend.meta.meta_description_stones', ['%STONE%' => $stone->getName()], 'AppBundle');

        $this->seoUpdater->doMagic(null, $this->buildListSeo($request, $title, $description, [
            'og' => $this->ogWebsite($request, $title . $this->pageSuffix($request)),
        ]));

        return $this->render('@Product/product_list_stone.html.twig', ['stone' => $stone]);
    }

    #[Cache(maxage: 60, public: true)]
    public function viewAction(Request $request): Response
    {
        /** @var ProductRepository $repo */
        $repo = $this->em->getRepository(Product::class);
        $product = $repo->find($request->get('id'));

        if (!$product || !$product->getIsActive()) {
            throw $this->createNotFoundException(self::PRODUCT_404);
        }

        if ($request->get('slug') !== $product->getSlug()) {
            throw $this->createNotFoundException(self::PRODUCT_404);
        }

        $sizes = $repo->productGroupQueryBuilder($product->getProductGroup())
            ->getQuery()
            ->getResult();

        if ($product->getCategory()) {
            $this->breadcrumb->addBreadcrumb([
                'title' => $product->getCategory()->getName(),
                'href' => $this->router->generate('product_list', ['slug' => $product->getCategory()->getSlug()]),
            ]);
        }
        $this->breadcrumb->addBreadcrumb(['title' => $product->getName()]);

        $price = $product->getDiscount() ?: $product->getPrice();
        $title = $product->getName()
            . ($price ? ' — від ' . number_format($price, 0, '.', ' ') . ' грн' : '')
            . ' | Naturalstones Jewerly';

        $metaDescription = ($product->getIsAvailable() ? $this->translator->trans('frontend.meta.in_stock', [], 'AppBundle') : '')
            . $this->truncateDescription($product->getDescription());

        $this->seoUpdater->doMagic(null, [
            'title' => $title,
            'description' => $metaDescription,
            'og' => [
                'og:site_name' => $this->translator->trans('frontend.meta.meta_title_index', [], 'AppBundle'),
                'og:type' => 'product',
                'og:title' => $title,
                'og:url' => $request->getSchemeAndHttpHost() . $request->getPathInfo(),
                'og:image' => $request->getSchemeAndHttpHost() . $product->getImage()->getPath(),
                'og:description' => $metaDescription,
                'product:price:amount' => $price,
                'product:price:currency' => 'UAH',
            ],
        ]);

        if (!AppHelper::isBot($request->headers->get('User-Agent'))) {
            $repo->incViewCounter($product->getId());
            $this->productViewHelper->doView($product);
        }

        $ratingData = $this->em->createQuery(
            'SELECT AVG(c.rating) as avgRating, COUNT(c.id) as reviewCount
             FROM CommentBundle\Entity\Comment c
             WHERE c.product = :product AND c.isActive = 1'
        )->setParameter('product', $product)->getSingleResult();

        return $this->render('@Product/product_view.html.twig', [
            'product' => $product,
            'sizes' => $sizes,
            'avgRating' => $ratingData['avgRating'] ? round((float) $ratingData['avgRating'], 1) : null,
            'reviewCount' => (int) $ratingData['reviewCount'],
        ]);
    }

    /**
     * @param array<string, mixed> $extra
     *
     * @return array<string, mixed>
     */
    private function buildListSeo(Request $request, string $title, string $description, array $extra = []): array
    {
        $seo = [
            'title' => $title . $this->pageSuffix($request),
            'description' => $this->pageDescriptionPrefix($request) . $description,
            'og' => [
                'og:type' => 'website',
                'og:url' => $request->getSchemeAndHttpHost(),
            ],
        ];

        return array_replace_recursive($seo, $extra);
    }

    /**
     * @return array<string, string>
     */
    private function ogWebsite(Request $request, string $title): array
    {
        return [
            'og:type' => 'website',
            'og:title' => $title,
            'og:url' => $request->getSchemeAndHttpHost(),
        ];
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

    private function truncateDescription(?string $description): string
    {
        $clean = trim(strip_tags((string) $description));
        $truncated = mb_substr($clean, 0, self::DESCRIPTION_LIMIT);

        return mb_strlen($clean) > self::DESCRIPTION_LIMIT ? $truncated . '...' : $truncated;
    }

    /**
     * @return array<string, mixed>
     */
    private function getCategorySeo(string $slug, string $fallbackTitle): array
    {
        if (!in_array($slug, self::SEO_CATEGORY_SLUGS, true)) {
            return ['h1' => $fallbackTitle];
        }

        $prefix = 'frontend.category_seo.' . $slug . '.';
        $faq = [];
        for ($i = 1; $i <= 3; ++$i) {
            $faq[] = [
                'question' => $this->translator->trans($prefix . 'faq_' . $i . '_question', [], 'AppBundle'),
                'answer' => $this->translator->trans($prefix . 'faq_' . $i . '_answer', [], 'AppBundle'),
            ];
        }

        return [
            'h1' => $this->translator->trans($prefix . 'h1', [], 'AppBundle'),
            'intro' => $this->translator->trans($prefix . 'intro', [], 'AppBundle'),
            'bottom_title' => $this->translator->trans($prefix . 'bottom_title', [], 'AppBundle'),
            'bottom' => $this->translator->trans($prefix . 'bottom', [], 'AppBundle'),
            'faq_title' => $this->translator->trans('frontend.category_seo.faq_title', [], 'AppBundle'),
            'faq' => $faq,
            'meta_title' => $this->translator->trans($prefix . 'meta_title', [], 'AppBundle'),
            'meta_description' => $this->translator->trans($prefix . 'meta_description', [], 'AppBundle'),
        ];
    }
}