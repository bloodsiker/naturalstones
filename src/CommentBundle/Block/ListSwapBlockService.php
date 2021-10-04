<?php

namespace CommentBundle\Block;

use CommentBundle\Entity\Swap;
use Doctrine\ORM\EntityManager;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Sonata\BlockBundle\Meta\Metadata;
use Sonata\BlockBundle\Block\Service\AbstractAdminBlockService;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ListSwapBlockService
 */
class ListSwapBlockService extends AbstractAdminBlockService
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * ListGenreBlockService constructor.
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
            'CommentBundle',
            ['class' => 'fa fa-code']
        );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureSettings(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'items_count'    => 20,
            'page'           => 1,
            'template'       => 'CommentBundle:Block:swap_list.html.twig',
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
        $block = $blockContext->getBlock();

        if (!$block->getEnabled()) {
            return new Response();
        }

        $limit = (int) $blockContext->getSetting('items_count');
        $page = (int) $blockContext->getSetting('page');

        $repository = $this->em->getRepository(Swap::class);

        $qb = $repository->createQueryBuilder('s');
        $qb
            ->where('s.isActive = 1')
            ->orderBy('s.createdAt', 'DESC');

        $results = new Pagerfanta(new DoctrineORMAdapter($qb, true, false));
        $results->setAllowOutOfRangePages(true);
        $results->setMaxPerPage($limit);
        $results->setCurrentPage($page);


        return $this->renderResponse($blockContext->getTemplate(), [
            'swaps'    => $results,
            'block'     => $block,
            'settings'  => array_merge($blockContext->getSettings(), $block->getSettings()),
        ], $response);
    }
}
