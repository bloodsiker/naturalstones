<?php

namespace AppBundle\Block;

use Doctrine\ORM\EntityManager;
use ProductBundle\Entity\Category;
use Sonata\BlockBundle\Meta\Metadata;
use Sonata\BlockBundle\Block\Service\AbstractAdminBlockService;
use Sonata\BlockBundle\Block\BlockContextInterface;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;

/**
 * Class MenuBlockService
 */
class MenuBlockService extends AbstractAdminBlockService
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * HeaderBlockService constructor.
     *
     * @param string          $name
     * @param EngineInterface $templating
     * @param EntityManager   $em
     */
    public function __construct($name, EngineInterface $templating, EntityManager $em)
    {
        parent::__construct($name, $templating);

        $this->em = $em;
    }

    /**
     * @param null $code
     *
     * @return Metadata
     */
    public function getBlockMetadata($code = null)
    {
        return new Metadata(
            $this->getName(),
            (!is_null($code) ? $code : $this->getName()),
            false,
            'AppBundle',
            ['class' => 'fa fa-th-large']
        );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureSettings(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'template'  => 'AppBundle:Block:menu.html.twig',
            'type'      => 'header',
        ]);
    }

    /**
     * @param BlockContextInterface $blockContext
     * @param Response|null         $response
     *
     * @return Response
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        if (!$blockContext->getBlock()->getEnabled()) {
            return new Response();
        }

        $repository = $this->em->getRepository(Category::class);

        $qb = $repository->baseCategoryQueryBuilder();

        $results = $qb->getQuery()->getResult();

        return $this->renderResponse($blockContext->getTemplate(), [
            'settings'      => $blockContext->getSettings(),
            'block'         => $blockContext->getBlock(),
            'categories'    => $results
        ]);
    }
}
