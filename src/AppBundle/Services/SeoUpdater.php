<?php

namespace AppBundle\Services;

use Sonata\PageBundle\CmsManager\CmsManagerSelectorInterface;
use Sonata\PageBundle\Model\PageInterface;
use Sonata\PageBundle\Model\SiteInterface;
use Sonata\SeoBundle\Seo\SeoPageInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class SeoUpdater
{
    private const DEFAULT_OG_IMAGE_SRC = '/bundles/app/images/logo.jpg';

    private ?PageInterface $currentPage = null;
    private ?string $siteTitle = null;

    public function __construct(
        private readonly CmsManagerSelectorInterface $pageSelector,
        private readonly SeoPageInterface $seoPage,
        private readonly RequestStack $requestStack,
        private readonly TranslatorInterface $translator,
        private readonly RouterInterface $router,
        private readonly SaveStateValue $saveStateService,
    ) {
    }

    /**
     * @param array<string, mixed> $params
     */
    public function doMagic(mixed $object = null, array $params = []): void
    {
        $this->currentPage = $this->pageSelector->retrieve()->getCurrentPage();
        if (!$this->currentPage) {
            return;
        }

        $this->updateGeneralMetadata($params);

        $request = $this->requestStack->getCurrentRequest();
        $defaultOgImage = $request->getSchemeAndHttpHost() . self::DEFAULT_OG_IMAGE_SRC;

        $this->setOpenGraph($params, $defaultOgImage);
        $this->setCanonicalUrl($params, $params['custom_route_params'] ?? []);
        $this->setOtherMeta($params);
    }

    /**
     * @param array<string, mixed> $params
     */
    public function setOtherMeta(array $params = []): void
    {
        $metaData = $this->seoPage->getMetas();

        if (isset($metaData['name']['keywords'])) {
            foreach ($metaData['name']['keywords'] as $keywords) {
                if ($keywords) {
                    $this->seoPage->addMeta('name', 'product_keywords', $keywords);
                }
            }
        }

        if (isset($metaData['name']) && !isset($metaData['name']['twitter:card'])) {
            $this->seoPage->addMeta('name', 'twitter:card', 'summary');
        }

        if (isset($params['twitter']) && is_array($params['twitter'])) {
            foreach ($params['twitter'] as $pName => $pVal) {
                $this->seoPage->addMeta('name', $pName, $pVal);
            }
        }

        $robots = !empty($params['page_number']) ? 'NOINDEX, FOLLOW' : 'INDEX, FOLLOW, ALL';
        $this->seoPage->addMeta('name', 'ROBOTS', $robots);
    }

    /**
     * @return array<string, mixed>
     */
    public function getAllMeta(): array
    {
        return $this->seoPage->getMetas();
    }

    public function getSiteTitle(): string
    {
        return $this->siteTitle ?: $this->translator->trans('app.frontend.title', [], 'AppBundle');
    }

    /**
     * @param array<string, mixed> $params
     */
    private function updateGeneralMetadata(array &$params = []): void
    {
        /** @var SiteInterface $site */
        $site = $this->currentPage->getSite();

        $this->seoPage->setTitle(
            $params['title']
                ?? ($this->currentPage->getTitle() ?: ($site->getTitle() ?: $site->getName()))
        );

        $this->seoPage->addMeta(
            'name',
            'description',
            $params['description']
                ?? ($this->currentPage->getMetaDescription() ?: ($site->getMetaDescription() ?: $this->translator->trans('frontend.meta.description', [], 'AppBundle')))
        );

        $this->seoPage->addMeta(
            'name',
            'keywords',
            $params['keywords']
                ?? ($this->currentPage->getMetaKeyword() ?: ($site->getMetaKeywords() ?: $this->translator->trans('frontend.meta.keywords', [], 'AppBundle')))
        );
    }

    /**
     * @param array<string, mixed> $params
     */
    private function setOpenGraph(array $params, string $defaultOgImage): void
    {
        if (!isset($params['og']) || !is_array($params['og'])) {
            return;
        }

        $ogParams = $params['og'];
        if (empty($ogParams['og:image'])) {
            $ogParams['og:image'] = $defaultOgImage;
        }

        foreach ($ogParams as $name => $value) {
            $this->seoPage->addMeta('property', $name, $value);
        }
    }

    /**
     * @param array<string, mixed> $params
     * @param array<string, mixed> $customRouteParams
     */
    private function setCanonicalUrl(array $params, array $customRouteParams = []): void
    {
        $showCanonicalUrl = $params['showCanonicalUrl'] ?? true;

        if (isset($params['canonicalUrl'])) {
            if ($showCanonicalUrl) {
                $this->seoPage->setLinkCanonical($params['canonicalUrl']);
            }
            $this->seoPage->addMeta('property', 'og:url', $params['canonicalUrl']);

            return;
        }

        $request = $this->getRequest();
        $routeParams = array_merge(
            $this->prepareRouteParams($request->attributes->all()),
            $customRouteParams
        );

        if (isset($routeParams['page'])) {
            $routeParams['page'] = null;
        }

        if (!$route = $request->attributes->get('_route')) {
            return;
        }

        $url = $this->router->generate($route, $routeParams, UrlGenerator::ABSOLUTE_URL);
        if ($showCanonicalUrl) {
            $this->seoPage->setLinkCanonical($url);
        }
        $this->seoPage->addMeta('property', 'og:url', $url);
    }

    private function getRequest(): ?Request
    {
        return $this->requestStack->getCurrentRequest();
    }

    /**
     * @param array<string, mixed> $routeParams
     *
     * @return array<string, mixed>
     */
    private function prepareRouteParams(array $routeParams): array
    {
        $resultParams = (array) $this->getRequest()->query->all();
        foreach ($routeParams as $k => $param) {
            if (is_string($param) && preg_match('/^[^_].*$/', (string) $k)) {
                $resultParams[$k] = $param;
            }
        }

        return $resultParams;
    }
}