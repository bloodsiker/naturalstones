<?php

namespace ShareBundle\Block;

use AppBundle\Block\AbstractEditableBlockService;
use Doctrine\Bundle\DoctrineBundle\Registry;
use ShareBundle\Entity\Tag;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Twig\Environment;

class ListTagsBlockService extends AbstractEditableBlockService
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
            'template' => '@Share/Block/tag_list.html.twig',
        ]);
    }

    public function execute(BlockContextInterface $blockContext, ?Response $response = null): Response
    {
        $block = $blockContext->getBlock();
        if (!$block->getEnabled()) {
            return new Response();
        }

        return $this->renderResponse($blockContext->getTemplate(), [
            'tags' => $this->doctrine->getRepository(Tag::class)->findBy(['isActive' => true]),
            'block' => $block,
            'settings' => array_merge($blockContext->getSettings(), $block->getSettings()),
        ], $response);
    }
}