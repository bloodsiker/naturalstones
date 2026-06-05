<?php

namespace MainImageBundle\Block;

use AppBundle\Block\AbstractEditableBlockService;
use Doctrine\ORM\EntityManagerInterface;
use MainImageBundle\Entity\MainImage;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Twig\Environment;

class MainImageBlockService extends AbstractEditableBlockService
{
    public function __construct(
        Environment $twig,
        private readonly EntityManagerInterface $em,
    ) {
        parent::__construct($twig);
    }

    public function configureSettings(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'template' => '@MainImage/Block/main_image.html.twig',
        ]);
    }

    public function execute(BlockContextInterface $blockContext, ?Response $response = null): Response
    {
        $block = $blockContext->getBlock();
        if (!$block->getEnabled()) {
            return new Response();
        }

        $mainImage = $this->em->getRepository(MainImage::class)
            ->findOneBy(['isActive' => true], ['orderNum' => 'ASC']);

        return $this->renderResponse($blockContext->getTemplate(), [
            'mainImage' => $mainImage,
            'block' => $block,
            'settings' => array_merge($blockContext->getSettings(), $block->getSettings()),
        ], $response);
    }
}