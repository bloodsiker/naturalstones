<?php

namespace AppBundle\Controller;

use AppBundle\Helper\EncryptHelper;
use AppBundle\Services\BreadcrumbService;
use AppBundle\Services\Cart;
use AppBundle\Services\SendTelegramService;
use AppBundle\Services\SeoUpdater;
use Sonata\BlockBundle\Block\BlockContextManager;
use Sonata\BlockBundle\Block\BlockRenderer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class BaseController extends AbstractController
{
    public static function getSubscribedServices(): array
    {
        return array_merge(parent::getSubscribedServices(), [
            'translator' => TranslatorInterface::class,
            'app.breadcrumb' => '?' . BreadcrumbService::class,
            'app.seo.updater' => '?' . SeoUpdater::class,
            'app.helper.encrypt' => '?' . EncryptHelper::class,
            'app.send_telegram' => '?' . SendTelegramService::class,
            'app.cart' => '?' . Cart::class,
            'sonata.block.renderer.default' => '?' . BlockRenderer::class,
            'sonata.block.context_manager.default' => '?' . BlockContextManager::class,
        ]);
    }
}