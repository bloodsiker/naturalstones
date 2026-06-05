<?php

namespace AppBundle\Block;

use Doctrine\ORM\EntityManagerInterface;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Twig\Environment;

class HeaderBlockService extends AbstractEditableBlockService
{
    public function __construct(
        Environment $twig,
        protected readonly EntityManagerInterface $em,
    ) {
        parent::__construct($twig);
    }

    public function configureSettings(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'template' => '@App/Block/header.html.twig',
            'class' => null,
        ]);
    }

    public function execute(BlockContextInterface $blockContext, ?Response $response = null): Response
    {
        if (!$blockContext->getBlock()->getEnabled()) {
            return new Response();
        }

        return $this->renderResponse($blockContext->getTemplate(), [
            'settings' => $blockContext->getSettings(),
            'block' => $blockContext->getBlock(),
        ]);
    }
}