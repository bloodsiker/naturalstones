<?php

namespace AppBundle\Block;

use Doctrine\ORM\EntityManager;
use ProductBundle\Entity\Category;
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

        $repository = $this->em->getRepository(Category::class);

        $qb = $repository->baseCategoryQueryBuilder();

        $main = clone $qb->andWhere('c.type = :type')->setParameter('type', Category::TYPE_MAIN)->orderBy('c.orderNum', 'DESC');
        $secondary = clone $qb->andWhere('c.type = :type')->setParameter('type', Category::TYPE_SECONDARY)->orderBy('c.orderNum', 'DESC');
        $individual = clone $qb->andWhere('c.type = :type')->setParameter('type', Category::TYPE_INDIVIDUAL)->orderBy('c.orderNum', 'DESC');
        $giftBox = clone $qb->andWhere('c.type = :type')->setParameter('type', Category::TYPE_GIFT_BOX)->orderBy('c.orderNum', 'DESC');
        $scrapers = clone $qb->andWhere('c.type = :type')->setParameter('type', Category::TYPE_SCRAPERS)->orderBy('c.orderNum', 'DESC');
        $gematit = clone $qb->andWhere('c.type = :type')->setParameter('type', Category::TYPE_GEMATIT)->orderBy('c.orderNum', 'DESC');

        return $this->renderResponse($blockContext->getTemplate(), [
            'settings'      => $blockContext->getSettings(),
            'block'         => $blockContext->getBlock(),
            'main'          => $main->getQuery()->getResult(),
            'secondary'     => $secondary->getQuery()->getResult(),
            'individual'    => $individual->getQuery()->getResult(),
            'giftBox'       => $giftBox->getQuery()->getResult(),
            'scrapers'      => $scrapers->getQuery()->getResult(),
            'gematit'       => $gematit->getQuery()->getResult(),
            'locales'       => $this->locales,
        ]);
    }
}
