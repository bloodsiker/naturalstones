<?php

namespace AppBundle\Block;

use AppBundle\Entity\MenuSection;
use Doctrine\ORM\EntityManagerInterface;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Twig\Environment;

class MenuBlockService extends AbstractEditableBlockService
{
    /**
     * @param list<string> $locales
     */
    public function __construct(
        Environment $twig,
        protected readonly EntityManagerInterface $em,
        protected readonly array $locales,
    ) {
        parent::__construct($twig);
    }

    public function configureSettings(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'template' => '@App/Block/menu.html.twig',
            'type' => 'header',
        ]);
    }

    public function execute(BlockContextInterface $blockContext, ?Response $response = null): Response
    {
        if (!$blockContext->getBlock()->getEnabled()) {
            return new Response();
        }

        $menuSections = $this->em->getRepository(MenuSection::class)
            ->createQueryBuilder('menuSection')
            ->innerJoin('menuSection.items', 'item')
            ->addSelect('item')
            ->innerJoin('item.category', 'category')
            ->addSelect('category')
            ->andWhere('menuSection.isActive = :active')
            ->andWhere('category.isActive = :active')
            ->setParameter('active', true)
            ->orderBy('menuSection.orderNum', 'DESC')
            ->addOrderBy('item.orderNum', 'DESC')
            ->getQuery()
            ->getResult();

        return $this->renderResponse($blockContext->getTemplate(), [
            'settings' => $blockContext->getSettings(),
            'block' => $blockContext->getBlock(),
            'menuSections' => $menuSections,
            'locales' => $this->locales,
        ]);
    }
}