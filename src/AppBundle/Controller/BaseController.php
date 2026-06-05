<?php

namespace AppBundle\Controller;

use AppBundle\Helper\EncryptHelper;
use AppBundle\Services\BreadcrumbService;
use AppBundle\Services\Cart;
use AppBundle\Services\SendTelegramService;
use AppBundle\Services\SeoUpdater;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use ProductBundle\Helper\ProductViewHelper;
use ShareBundle\Services\TagFinder;
use Sonata\BlockBundle\Block\BlockContextManager;
use Sonata\BlockBundle\Block\BlockRenderer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class BaseController extends AbstractController
{
    public static function getSubscribedServices(): array
    {
        return array_merge(parent::getSubscribedServices(), [
            'translator' => TranslatorInterface::class,
            'router' => RouterInterface::class,
            'doctrine' => '?' . ManagerRegistry::class,
            'doctrine.orm.entity_manager' => '?' . EntityManagerInterface::class,
            'doctrine.orm.default_entity_manager' => '?' . EntityManagerInterface::class,
            'request_stack' => '?' . \Symfony\Component\HttpFoundation\RequestStack::class,
            'app.breadcrumb' => '?' . BreadcrumbService::class,
            'app.seo.updater' => '?' . SeoUpdater::class,
            'app.helper.encrypt' => '?' . EncryptHelper::class,
            'app.send_telegram' => '?' . SendTelegramService::class,
            'app.cart' => '?' . Cart::class,
            'sonata.block.renderer.default' => '?' . BlockRenderer::class,
            'sonata.block.context_manager.default' => '?' . BlockContextManager::class,
            'product.helper.views' => '?' . ProductViewHelper::class,
            'share.tag.finder' => '?' . TagFinder::class,
        ]);
    }

    /**
     * Symfony 6 removed get() from AbstractController — restore it via the service locator.
     * Only services listed in getSubscribedServices() are accessible.
     */
    protected function get(string $id): object
    {
        if ('session' === $id) {
            return $this->container->get('request_stack')->getSession();
        }

        return $this->container->get($id);
    }

    /**
     * getDoctrine() was removed in Symfony 6.
     */
    protected function getDoctrine(): ManagerRegistry
    {
        return $this->container->get('doctrine');
    }
}
