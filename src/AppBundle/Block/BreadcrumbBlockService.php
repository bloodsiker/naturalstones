<?php

namespace AppBundle\Block;

use AppBundle\Services\BreadcrumbService;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Twig\Environment;

class BreadcrumbBlockService extends AbstractEditableBlockService
{
    public function __construct(
        Environment $twig,
        private readonly BreadcrumbService $breadcrumb,
    ) {
        parent::__construct($twig);
    }

    public function configureSettings(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'template' => '@App/Block/breadcrumb.html.twig',
        ]);
    }

    /**
     * @throws \Exception
     */
    public function execute(BlockContextInterface $blockContext, ?Response $response = null): Response
    {
        if (!$blockContext->getBlock()->getEnabled()) {
            return new Response();
        }

        return $this->renderResponse($blockContext->getTemplate(), [
            'breadcrumbs' => $this->breadcrumb->getBreadcrumb(),
            'settings' => $blockContext->getSettings(),
            'block' => $blockContext->getBlock(),
        ]);
    }
}