<?php

namespace AppBundle\Controller;

use AppBundle\Services\BreadcrumbService;
use AppBundle\Services\SeoUpdater;
use ArticleBundle\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;
use ProductBundle\Entity\Category;
use ProductBundle\Entity\Product;
use ShareBundle\Entity\Colour;
use ShareBundle\Entity\Stone;
use ShareBundle\Entity\Tag;
use ShareBundle\Entity\Zodiac;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use UserBundle\Entity\UserDeliveryAddress;

class DefaultController extends BaseController
{
    /**
     * @param list<string> $locales
     */
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly TranslatorInterface $translator,
        private readonly RouterInterface $router,
        private readonly BreadcrumbService $breadcrumb,
        private readonly SeoUpdater $seoUpdater,
        private readonly RequestStack $requestStack,
        private readonly array $locales,
        private readonly string $companyName,
    ) {
    }

    public function indexAction(Request $request): Response
    {
        $title = $this->trans('frontend.meta.meta_title_index');

        $this->seoUpdater->doMagic(null, [
            'title' => $title,
            'description' => $this->trans('frontend.meta.meta_description_index', ['%YEAR%' => date('Y')]),
            'og' => [
                'og:site_name' => $title,
                'og:url' => $request->getSchemeAndHttpHost(),
            ],
        ]);

        return new Response();
    }

    public function cartAction(Request $request): Response
    {
        $this->breadcrumb->addBreadcrumb(['title' => $this->trans('frontend.breadcrumb.cart')]);

        $this->applyCartSeo($request, 1, 'cart');
        $cartViewData = $this->buildCartViewData($request);

        return $this->render('@App/cart/cart.html.twig', [
            'infoCart' => $cartViewData['infoCart'],
            'savedAddresses' => $cartViewData['savedAddresses'],
        ]);
    }

    public function cartStepOneAction(Request $request): Response
    {
        $this->breadcrumb->addBreadcrumb(['title' => $this->trans('frontend.breadcrumb.step')]);

        $this->applyCartSeo($request, 1, 'step');
        $cartViewData = $this->buildCartViewData($request);

        return $this->render('@App/cart/step_1.html.twig', [
            'infoCart' => $cartViewData['infoCart'],
            'savedAddresses' => $cartViewData['savedAddresses'],
        ]);
    }

    public function cartStepTwoAction(Request $request): Response
    {
        $this->requestStack->getSession()->set('infoCart', $request->query->all());

        $this->breadcrumb->addBreadcrumb(['title' => $this->trans('frontend.breadcrumb.step')]);
        $this->applyCartSeo($request, 2, 'step');

        return $this->render('@App/cart/step_2.html.twig');
    }

    public function constructorAction(Request $request): Response
    {
        $this->breadcrumb->addBreadcrumb(['title' => $this->trans('frontend.breadcrumb.constructor')]);

        $this->applySimpleSeo($request, 'constructor');

        return $this->render('@App/constructor/index.html.twig');
    }

    public function deliveryPaymentAction(Request $request): Response
    {
        $this->breadcrumb->addBreadcrumb(['title' => $this->trans('frontend.breadcrumb.delivery_payment')]);

        $this->applySimpleSeo($request, 'delivery_payment');

        return $this->render('@App/delivery_payment/index.html.twig');
    }

    public function searchAction(Request $request): Response
    {
        $this->breadcrumb->addBreadcrumb([
            'title' => $this->trans('frontend.breadcrumb.search'),
            'href' => $this->router->generate('search_category', ['search' => $request->get('search')]),
        ]);
        $this->breadcrumb->addBreadcrumb(['title' => $this->trans('frontend.breadcrumb.search_category')]);

        $this->seoUpdater->doMagic(null, [
            'title' => $this->trans('frontend.meta.meta_title_search'),
            'description' => $this->trans('frontend.meta.meta_description_search'),
        ]);

        return $this->render('@App/search/search.html.twig');
    }

    public function searchCategoryAction(Request $request): Response
    {
        $this->breadcrumb->addBreadcrumb(['title' => $this->trans('frontend.breadcrumb.search')]);

        $this->seoUpdater->doMagic(null, [
            'title' => $this->trans('frontend.meta.meta_title_search'),
            'description' => $this->trans('frontend.meta.meta_description_search'),
        ]);

        return $this->render('@App/search_category/search.html.twig');
    }

    public function sitemapAction(Request $request): Response
    {
        $hostname = $request->getSchemeAndHttpHost();
        $urls = [];

        $this->addSitemapUrl($urls, 'main', $hostname, $this->router->generate('index'));
        $this->addSitemapUrl($urls, 'stone_list', $hostname, $this->router->generate('stone_list'));
        $this->addSitemapUrl($urls, 'reviews_list', $hostname, $this->router->generate('review_list'));
        $this->addSitemapUrl($urls, 'constructor', $hostname, $this->router->generate('constructor'));
        $this->addSitemapUrl($urls, 'delivery_payment', $hostname, $this->router->generate('delivery_payment'));
        $this->addSitemapUrl($urls, 'article_list', $hostname, $this->router->generate('article_list'));

        foreach (['man', 'woman'] as $who) {
            $this->addSitemapUrl($urls, $who, $hostname, $this->router->generate('product_who_list', ['who' => $who]));
        }

        foreach ($this->em->getRepository(Category::class)->findBy(['isActive' => true]) as $category) {
            $this->addSitemapUrl(
                $urls,
                'category_' . $category->getId(),
                $hostname,
                $this->router->generate('product_list', ['slug' => $category->getSlug()])
            );
        }

        foreach ($this->em->getRepository(Product::class)->findBy(['isActive' => true], ['id' => 'DESC']) as $product) {
            $this->addSitemapUrl(
                $urls,
                'product_' . $product->getId(),
                $hostname,
                $this->router->generate('product_view', [
                    'category' => $product->getCategory()->getSlug(),
                    'id' => $product->getId(),
                    'slug' => $product->getSlug(),
                ])
            );
        }

        foreach ($this->em->getRepository(Stone::class)->findBy(['isActive' => true]) as $stone) {
            $this->addSitemapUrl(
                $urls,
                'stone_' . $stone->getId(),
                $hostname,
                $this->router->generate('product_stone_list', ['slug' => $stone->getSlug()])
            );
        }

        foreach ($this->em->getRepository(Stone::class)->uniqLetterByStone('ru') as $key => $let) {
            if (isset($let[1])) {
                $this->addSitemapUrl(
                    $urls,
                    'stone_letter_' . $key,
                    $hostname,
                    $this->router->generate('stone_list_letter', ['letter' => $let[1]])
                );
            }
        }

        foreach ($this->em->getRepository(Zodiac::class)->findBy(['isActive' => true]) as $zodiac) {
            $this->addSitemapUrl(
                $urls,
                'zodiac_' . $zodiac->getId(),
                $hostname,
                $this->router->generate('zodiac_stone_list', ['slug' => $zodiac->getSlug()])
            );
        }

        foreach ($this->em->getRepository(Colour::class)->findBy(['isActive' => true]) as $colour) {
            $this->addSitemapUrl(
                $urls,
                'colour_' . $colour->getId(),
                $hostname,
                $this->router->generate('product_colour_list', ['slug' => $colour->getSlug()])
            );
        }

        foreach ($this->em->getRepository(Tag::class)->findBy(['isActive' => true]) as $tag) {
            $this->addSitemapUrl(
                $urls,
                'tag_' . $tag->getId(),
                $hostname,
                $this->router->generate('product_tags_list', ['slug' => $tag->getSlug()])
            );
        }

        foreach ($this->em->getRepository(Article::class)->findBy(['isActive' => true]) as $article) {
            $this->addSitemapUrl(
                $urls,
                'article_' . $article->getId(),
                $hostname,
                $this->router->generate('article_view', [
                    'category' => $article->getSlug(),
                    'id' => $article->getId(),
                    'slug' => $article->getSlug(),
                ])
            );
        }

        foreach ($this->em->getRepository(\ArticleBundle\Entity\Category::class)->findBy(['isActive' => true]) as $articleCategory) {
            $this->addSitemapUrl(
                $urls,
                'article_category_' . $articleCategory->getId(),
                $hostname,
                $this->router->generate('article_category', ['category' => $articleCategory->getSlug()])
            );
        }

        foreach ($this->em->getRepository(Tag::class)->getTagsByArticles() as $articleTag) {
            $this->addSitemapUrl(
                $urls,
                'article_tag_' . $articleTag->getId(),
                $hostname,
                $this->router->generate('article_tags_list', ['slug' => $articleTag->getSlug()])
            );
        }

        $response = new Response($this->renderView('@App/Block/sitemap.html.twig', [
            'urls' => $urls,
            'hostname' => $hostname,
        ]));
        $response->headers->set('Content-Type', 'application/xml; charset=utf-8');

        return $response;
    }

    public function generateFeedAction(Request $request): Response
    {
        $hostname = $request->getSchemeAndHttpHost();
        $feedData = [[
            'id', 'title', 'description', 'availability', 'condition', 'price', 'sale_price', 'link', 'image_link', 'brand',
            'fb_product_category', 'gender', 'age_group', 'material', 'rich_text_description',
        ]];

        foreach ($this->em->getRepository(Product::class)->findBy(['isActive' => true], ['id' => 'DESC']) as $product) {
            $feedData[] = $this->buildFeedRow($product, $hostname);
        }

        $filePath = $this->getParameter('kernel.project_dir') . '/public/feeds/facebook_feed.csv';
        $this->saveFeedToFile($filePath, $feedData);

        $response = new Response(file_get_contents($filePath));
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'inline; filename="facebook_feed.csv"');

        return $response;
    }

    public function openSearchAction(Request $request): Response
    {
        $response = new Response($this->renderView('@App/Block/open_searche.html.twig'));
        $response->headers->set('Content-Type', 'application/xml; charset=utf-8');

        return $response;
    }

    public function removeTrailingSlashAction(Request $request): RedirectResponse
    {
        $pathInfo = $request->getPathInfo();
        $url = str_replace($pathInfo, rtrim($pathInfo, ' /'), $request->getRequestUri());

        return $this->redirect($url, 301);
    }

    /**
     * @return array{infoCart: array<string, mixed>, savedAddresses: list<UserDeliveryAddress>}
     */
    private function buildCartViewData(Request $request): array
    {
        $infoCart = $this->requestStack->getSession()->get('infoCart', []);
        $user = $this->getUser();
        $savedAddresses = [];

        if ($user) {
            $name = trim(sprintf('%s %s', (string) $user->getFirstname(), (string) $user->getLastname()));
            if ('' !== $name && empty($infoCart['name'])) {
                $infoCart['name'] = $name;
            }
            if (!empty($user->getPhone()) && empty($infoCart['phone'])) {
                $infoCart['phone'] = $user->getPhone();
            }
            if (!empty($user->getEmail()) && empty($infoCart['email'])) {
                $infoCart['email'] = $user->getEmail();
            }

            $savedAddresses = $this->em->getRepository(UserDeliveryAddress::class)->findBy(
                ['user' => $user],
                ['isDefault' => 'DESC', 'createdAt' => 'DESC']
            );

            if (empty($infoCart['saved_address_id']) && !empty($savedAddresses)) {
                $infoCart['saved_address_id'] = $savedAddresses[0]->getId();
            }
            if (empty($infoCart['address']) && !empty($savedAddresses)) {
                $infoCart['address'] = $savedAddresses[0]->getAddress();
            }
        }

        return [
            'infoCart' => $infoCart,
            'savedAddresses' => $savedAddresses,
        ];
    }

    /**
     * @return list<string>
     */
    private function buildFeedRow(Product $product, string $hostname): array
    {
        $url = $this->router->generate('product_view', [
            'category' => $product->getCategory()->getSlug(),
            'id' => $product->getId(),
            'slug' => $product->getSlug(),
        ]);

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

        return [
            $product->getId(),
            $product->translate('uk')->getName(),
            $this->facebookText($product->translate('uk')->getDescription()),
            'in stock',
            'new',
            $product->getPrice() . ' UAH',
            $product->getDiscount() ? $product->getDiscount() . ' UAH' : '',
            $hostname . $url,
            $hostname . $product->getImage()->getPath(),
            $this->companyName,
            $this->facebookCategory($product->getCategory()->getSlug()),
            $gender,
            'all ages',
            implode(', ', $material),
            preg_replace(['/\[(.+?)\]/', '/[\n\r]/'], ['', ' '], $product->translate('uk')->getDescription()),
        ];
    }

    private function facebookCategory(string $categorySlug): string
    {
        return match (true) {
            in_array($categorySlug, ['braslety', 'nabory', 'niti', 'niti-oberegi', 'shambaly'], true)
                => 'Ювелирные украшения и часы > Ювелирные изделия > Браслеты',
            'kolca' === $categorySlug => 'Ювелирные украшения и часы > Ювелирные изделия > Кольца',
            'kole' === $categorySlug => 'Ювелирные украшения и часы > Ювелирные изделия > Колье и ожерелья',
            'serezhki' === $categorySlug => 'Ювелирные украшения и часы > Ювелирные изделия > Серьги',
            in_array($categorySlug, ['podveski', 'vstavki'], true)
                => 'Ювелирные украшения и часы > Ювелирные изделия > Подвески и кулоны',
            default => 'Ювелирные украшения и часы > Ювелирные изделия',
        };
    }

    /**
     * @param array<int, array<int, string|int|null>> $feedData
     */
    private function saveFeedToFile(string $filePath, array $feedData): void
    {
        $handle = fopen($filePath, 'w');
        foreach ($feedData as $row) {
            fputcsv($handle, $row);
        }
        fclose($handle);
    }

    private function facebookText(string $text): string
    {
        $text = strip_tags($text);
        $text = preg_replace(['/\[(.+?)\]/', '/[\n\r]/'], ['', ' '], $text);
        $text = preg_replace('/(<[^>]+) style=".*?"/i', '$1', $text);
        $text = preg_replace('/ style="[^"]+"/sui', ' ', $text);

        return preg_replace('/<style\b[^>]*>(.*?)<\/style>/si', '', $text);
    }

    /**
     * @param array<string, mixed> $params
     */
    private function trans(string $key, array $params = []): string
    {
        return $this->translator->trans($key, $params, 'AppBundle');
    }

    private function applySimpleSeo(Request $request, string $section): void
    {
        $this->seoUpdater->doMagic(null, [
            'title' => $this->trans('frontend.meta.meta_title_' . $section),
            'description' => $this->trans('frontend.meta.meta_description_' . $section),
            'og' => [
                'og:site_name' => $this->trans('frontend.meta.meta_title_index'),
                'og:url' => 'delivery_payment' === $section ? $request->getUri() : $request->getSchemeAndHttpHost(),
            ],
        ]);
    }

    private function applyCartSeo(Request $request, int $step, string $type): void
    {
        $titleKey = 'cart' === $type ? 'frontend.meta.meta_title_cart' : 'frontend.meta.meta_title_cart_step';
        $descKey = 'cart' === $type ? 'frontend.meta.meta_description_cart' : 'frontend.meta.meta_description_cart_step';
        $params = 'cart' === $type ? [] : ['%STEP%' => $step];

        $seo = [
            'title' => $this->trans($titleKey, $params),
            'description' => $this->trans($descKey, $params),
            'og' => [
                'og:site_name' => $this->trans('frontend.meta.meta_title_index'),
                'og:url' => $request->getSchemeAndHttpHost(),
            ],
        ];

        if ('step' === $type) {
            $seo['og']['og:title'] = sprintf('Корзина, шаг %d', $step);
        }

        $this->seoUpdater->doMagic(null, $seo);
    }

    /**
     * @param array<string, array<string, string>> $urls
     */
    private function addSitemapUrl(array &$urls, string $key, string $hostname, string $path): void
    {
        $urls[$key]['loc'] = $hostname . $path;
        foreach ($this->locales as $locale) {
            $urls[$key][$locale] = 'uk' === $locale
                ? $hostname . $path
                : sprintf('%s/%s%s', $hostname, $locale, $path);
        }
    }
}