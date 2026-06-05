<?php

namespace ShareBundle\Block;

use AppBundle\Block\AbstractEditableBlockService;
use Doctrine\Bundle\DoctrineBundle\Registry;
use ShareBundle\Entity\Zodiac;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Twig\Environment;

class ZodiacBlockService extends AbstractEditableBlockService
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
            'items_count' => 100,
            'title' => null,
            'mobile_select' => false,
            'template' => '@Share/Block/zodiac.html.twig',
        ]);
    }

    public function execute(BlockContextInterface $blockContext, ?Response $response = null): Response
    {
        $block = $blockContext->getBlock();
        if (!$block->getEnabled()) {
            return new Response();
        }

        $repository = $this->doctrine->getRepository(Zodiac::class);
        $qb = $repository->filterByShowMain($repository->baseStoneQueryBuilder());

        $zodiacs = $qb->setFirstResult(0)
            ->setMaxResults((int) $blockContext->getSetting('items_count'))
            ->getQuery()
            ->getResult();

        return $this->renderResponse($blockContext->getTemplate(), [
            'zodiacs' => $zodiacs,
            'block' => $block,
            'settings' => array_merge($blockContext->getSettings(), $block->getSettings()),
        ], $response);
    }
}