<?php

namespace AppBundle\Block;

use AppBundle\Entity\MenuSection;
use Doctrine\ORM\EntityManager;
use Sonata\BlockBundle\Block\BlockContextInterface;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Twig\Environment;

/**
 * Class MenuBlockService
 */
class MenuBlockService extends AbstractEditableBlockService
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var array
     */
    protected $locales;

    /**
     * HeaderBlockService constructor.
     *
     * @param string          $name
     * @param EngineInterface $templating
     * @param EntityManager   $em
     * @param array           $locales
     */
    public function __construct(Environment $twig, EntityManager $em, array $locales)
    {
        parent::__construct($twig);

        $this->em = $em;
        $this->locales = $locales;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureSettings(OptionsResolver $resolver): void    {
        $resolver->setDefaults([
            'template'  => '@App/Block/menu.html.twig',
            'type'      => 'header',
        ]);
    }

    /**
     * @param BlockContextInterface $blockContext
     * @param Response|null         $response
     *
     * @return Response
     */
    public function execute(BlockContextInterface $blockContext, ?Response $response = null): Response    {
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
            'settings'      => $blockContext->getSettings(),
            'block'         => $blockContext->getBlock(),
            'menuSections'  => $menuSections,
            'locales'       => $this->locales,
        ]);
    }
}
