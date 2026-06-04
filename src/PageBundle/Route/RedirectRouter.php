<?php
namespace PageBundle\Route;

use Sonata\PageBundle\Model\Site;
use Symfony\Cmf\Component\Routing\ChainedRouterInterface;
use Symfony\Cmf\Component\Routing\VersatileGeneratorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

use PageBundle\Entity\PageRedirect;
use PageBundle\Model\RedirectInterface;
use PageBundle\Model\RedirectManagerInterface;
use PageBundle\Services\RedisCacheKeyDecorator;

/**
 * Class RedirectRouter
 */
class RedirectRouter implements ChainedRouterInterface
{
    /**
     * @var RequestContext
     */
    protected $context;

    /**
     * @var RedirectManagerInterface
     */
    protected $redirectManager;

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var Request
     */
    private $request;

    public function __construct(RedirectManagerInterface $redirectManager, RouterInterface $router)
    {
        $this->redirectManager = $redirectManager;
        $this->router = $router;
        $this->context = $this->router->getContext();
    }

    /**
     * @param mixed $name
     *
     * @return bool
     */
    public function supports($name)
    {
        if (is_string($name)) {
            return false;
        }
        if (is_object($name) && !($name instanceof RedirectInterface)) {
            return false;
        }

        return true;
    }

    /**
     * @param mixed $name
     * @param array $parameters
     *
     * @return string
     */
    public function getRouteDebugMessage(string $name, array $parameters = []): string
    {
        if ($this->router instanceof VersatileGeneratorInterface) {
            return $this->router->getRouteDebugMessage($name, $parameters);
        }

        return "Route '$name' not found";
    }

    /**
     * @param string $name
     * @param array  $parameters
     * @param int    $referenceType
     *
     * @return string|void
     */
    public function generate(string $name, array $parameters = [], int $referenceType = self::ABSOLUTE_PATH): string
    {
        throw new RouteNotFoundException('Implement generate() method');
    }

    public function setContext(RequestContext $context): void
    {
        $this->context = $context;
    }

    /**
     * @return RequestContext|\Symfony\Component\Routing\RequestContext
     */
    public function getContext(): RequestContext
    {
        return $this->context;
    }

    public function getRouteCollection(): RouteCollection
    {
        return new RouteCollection();
    }

    /**
     * @param Request $request
     *
     * @return array|mixed|null|string|void
     *
     * @throws \Exception
     */
    public function getUrlRedirect(Request $request): ?string
    {
        $this->request = $request;

        try {
            $result = $this->match($request->getPathInfo());
            return $result['path'] ?? null;
        } catch (ResourceNotFoundException $e) {
            return null;
        }
    }

    /**
     * @param string $pathinfo
     *
     * @return array|mixed|null|string|void
     *
     * @throws \Exception
     */
    public function match(string $pathinfo): array
    {
        $url = null;
        $listRedirect = (array) $this->getRedirectCollection();
        if (false !== stripos($pathinfo, '_profiler')) {
            throw new ResourceNotFoundException();
        }

        foreach ($listRedirect as $redirect) {
            $fromPath = $redirect->getFromPath();
            $checkPath = substr($fromPath, 0, 8) === 'https://' ? $this->request->getUri() : $this->request->getPathInfo();

            switch ($redirect->getType()) {
                case PageRedirect::TYPE_FULL:
                    if ($fromPath === $checkPath) {
                        $url = $this->getFinalUrl($redirect);
                    }
                    break;
                case PageRedirect::TYPE_SEGMENT:
                    if (preg_match("~$fromPath~", $pathinfo)) {
                        $url = $this->getFinalUrl($redirect);
                    }
                    break;
                case PageRedirect::TYPE_REGEX:
                    if (preg_match($fromPath, $pathinfo)) {
                        $url = $this->getFinalUrl($redirect);
                    }
                    break;
                case PageRedirect::TYPE_STARTS_WITH:
                    $checkPart = mb_substr($checkPath, 0, mb_strlen($fromPath));
                    if ($checkPart === $fromPath) {
                        $url = str_replace($fromPath, $redirect->getToPath(), $checkPath);
                    }
                    break;
                default:
                    throw new \Exception(sprintf('Unknown PageRedirect const %s', $redirect->getType()));
            }
        }

        if (null === $url) {
            throw new ResourceNotFoundException();
        }

        return ['_controller' => 'Symfony\Component\HttpKernel\Controller\RedirectController::urlRedirectAction', 'path' => $url, 'permanent' => true];
    }

    /**
     * @return false|mixed
     */
    public function getActualRedirects()
    {
        $listRedirect = $this->redirectManager->findBy(['isActive' => true]);
        usort($listRedirect, function ($a, $b) {
            return mb_strlen($a->getFromPath()) <=> mb_strlen($b->getFromPath());
        });
        $listRedirect = array_reverse($listRedirect);

        return $listRedirect;
    }

    /**
     * @param PageRedirect $objectRedirect
     *
     * @return mixed|string|void
     */
    protected function getFinalUrl(PageRedirect $objectRedirect)
    {
        if (null === $objectRedirect) {
            return;
        }

        if (null !== $objectRedirect->getToPage()) {
            if ('page_slug' === $objectRedirect->getToPage()->getRouteName()) {
                $url = $objectRedirect->getToPage()->getUrl();
                $url = $this->decorateUrl($url, [], self::ABSOLUTE_URL, $objectRedirect->getToPage()->getSite());
            } else {
                $this->context->setHost($objectRedirect->getToPage()->getSite()->getHost());
                $url = $this->router->generate($objectRedirect->getToPage()->getRouteName(), [], self::ABSOLUTE_URL);
            }
        } else {
            $url = $this->decorateUrl($objectRedirect->getToPath(), [], self::ABSOLUTE_PATH);
        }

        if (null === $url) {
            throw new ResourceNotFoundException();
        }

        return $url;
    }

    /**
     * Method from Sonata Page Bundle code! Slightly modified
     *
     * Decorates an URL with url context and query
     *
     * @param string      $url           Relative URL
     * @param array       $parameters    An array of parameters
     * @param bool|string $referenceType The type of reference to be generated (one of the constants)
     * @param Site        $site
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    protected function decorateUrl($url, array $parameters = array(), $referenceType = self::RELATIVE_PATH, Site $site = null)
    {
        if (!$this->context) {
            throw new \RuntimeException('No context associated to the CmsPageRouter');
        }
        $schemeAuthority = '';
        if ($this->context->getHost() && (self::ABSOLUTE_URL === $referenceType || self::NETWORK_PATH === $referenceType)) {
            $port = '';
            if ('http' === $this->context->getScheme() && 80 !== $this->context->getHttpPort()) {
                $port = sprintf(':%s', $this->context->getHttpPort());
            } elseif ('https' === $this->context->getScheme() && 443 !== $this->context->getHttpsPort()) {
                $port = sprintf(':%s', $this->context->getHttpsPort());
            }
            $schemeAuthority = self::NETWORK_PATH === $referenceType ? '//' : sprintf('%s://', $this->context->getScheme());
            if (null !== $site && $site instanceof Site) {
                $schemeAuthority = sprintf('%s%s%s', $schemeAuthority, $site->getHost(), $port);
            } else {
                $schemeAuthority = sprintf('%s%s%s', $schemeAuthority, $this->context->getHost(), $port);
            }
        }
        if (self::RELATIVE_PATH === $referenceType) {
            $url = $this->getRelativePath($this->context->getPathInfo(), $url);
        } else {
            $url = sprintf('%s%s', $schemeAuthority, $url);
        }
        if (count($parameters) > 0) {
            return sprintf('%s?%s', $url, http_build_query($parameters, '', '&'));
        }

        return $url;
    }

    /**
     * Returns the target path as relative reference from the base path.
     *
     * @param string $basePath   The base path
     * @param string $targetPath The target path
     *
     * @return string The relative target path
     */
    protected function getRelativePath($basePath, $targetPath)
    {
        return UrlGenerator::getRelativePath($basePath, $targetPath);
    }

    /**
     * @return false|mixed
     */
    private function getRedirectCollection()
    {
        return $this->getActualRedirects();
    }
}
