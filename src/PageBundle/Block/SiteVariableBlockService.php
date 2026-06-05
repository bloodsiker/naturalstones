<?php

namespace PageBundle\Block;

use AppBundle\Block\AbstractEditableBlockService;
use Doctrine\Bundle\DoctrineBundle\Registry;
use PageBundle\Entity\SiteVariable;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Twig\Environment;

class SiteVariableBlockService extends AbstractEditableBlockService
{
    public function __construct(
        Environment $twig,
        protected readonly Registry $doctrine,
    ) {
        parent::__construct($twig);
    }

    public function configureSettings(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'placement' => null,
            'template' => '@Page/Block/site_variable.html.twig',
        ]);
    }

    public function execute(BlockContextInterface $blockContext, ?Response $response = null): Response
    {
        $block = $blockContext->getBlock();
        if (!$block->getEnabled()) {
            return new Response();
        }

        $variables = $this->doctrine
            ->getRepository(SiteVariable::class)
            ->findVariables($blockContext->getSetting('placement'));

        return $this->renderResponse($blockContext->getTemplate(), [
            'variables' => $variables,
            'block' => $block,
            'settings' => array_merge($blockContext->getSettings(), $block->getSettings()),
        ], $response);
    }
}