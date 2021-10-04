<?php

namespace OrderBundle\Block;

use Doctrine\Bundle\DoctrineBundle\Registry;
use OrderBundle\Entity\OrderBoard;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Sonata\BlockBundle\Meta\Metadata;
use Sonata\BlockBundle\Block\Service\AbstractAdminBlockService;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ListOrderBoardBlockService
 */
class ListOrderBoardBlockService extends AbstractAdminBlockService
{
    /**
     * @var Registry $doctrine
     */
    protected $doctrine;

    /**
     * ListGenreBlockService constructor.
     *
     * @param string          $name
     * @param EngineInterface $templating
     * @param Registry        $doctrine
     */
    public function __construct($name, EngineInterface $templating, Registry $doctrine)
    {
        parent::__construct($name, $templating);

        $this->doctrine = $doctrine;
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
            'OrderBundle',
            ['class' => 'fa fa-code']
        );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureSettings(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'items_count' => 20,
            'page'        => 1,
            'status'      => null,
            'template'    => 'OrderBundle:Block:orders_board_list.html.twig',
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

        $limit = $blockContext->getSetting('items_count');
        $page = $blockContext->getSetting('page');

        $repository = $this->doctrine->getRepository(OrderBoard::class);

        $qb = $repository->baseOrderBoardQueryBuilder();

        if ($blockContext->getSetting('status')) {
            $repository->filterByStatus($qb, $blockContext->getSetting('status'));
        }

        $paginator = new Pagerfanta(new DoctrineORMAdapter($qb, true, false));
        $paginator->setAllowOutOfRangePages(true);
        $paginator->setMaxPerPage((int) $limit);
        $paginator->setCurrentPage((int) $page);

        return $this->renderResponse($blockContext->getTemplate(), [
            'orders'    => $paginator,
            'block'     => $block,
            'settings'  => array_merge($blockContext->getSettings(), $block->getSettings()),
        ], $response);
    }
}
