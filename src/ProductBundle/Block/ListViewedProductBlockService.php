<?php

namespace ProductBundle\Block;

use Doctrine\Bundle\DoctrineBundle\Registry;
use ProductBundle\Entity\Product;
use Sonata\BlockBundle\Meta\Metadata;
use Sonata\BlockBundle\Block\Service\AbstractAdminBlockService;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ListViewedProductBlockService
 */
class ListViewedProductBlockService extends AbstractAdminBlockService
{
    const TEMPLATE_DEFAULT = 'ProductBundle:Block:viewed_list.html.twig';
    const TEMPLATE_AJAX  = 'ProductBundle:Block:viewed_list_ajax.html.twig';

    /**
     * @var Registry $doctrine
     */
    protected $doctrine;

    /**
     * @var RequestStack $requestStack
     */
    protected $requestStack;

    /**
     * ListGenreBlockService constructor.
     *
     * @param string          $name
     * @param EngineInterface $templating
     * @param Registry        $doctrine
     * @param RequestStack    $requestStack
     */
    public function __construct($name, EngineInterface $templating, Registry $doctrine, RequestStack $requestStack)
    {
        parent::__construct($name, $templating);

        $this->doctrine = $doctrine;
        $this->requestStack = $requestStack;
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
            'ProductBundle',
            ['class' => 'fa fa-th-large']
        );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureSettings(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'items_count'      => 7,
            'class'            => 'section',
            'template'         => self::TEMPLATE_DEFAULT,
        ]);
    }

    /**
     * @param BlockContextInterface $blockContext
     * @param Response|null         $response
     *
     * @return Response
     *
     * @throws \Exception
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        $block = $blockContext->getBlock();

        if (!$block->getEnabled()) {
            return new Response();
        }

        $request = $this->requestStack->getCurrentRequest();
        $limit = (int) $blockContext->getSetting('items_count');

        if ($request->isXmlHttpRequest() ) {
            $repository = $this->doctrine->getRepository(Product::class);
            $ids = $request->get('ids');

            $qb = $repository->baseProductQueryBuilder();

            $qb->where('p.id IN (:ids)')
                ->setParameter('ids', $ids)
                ->resetDQLPart('orderBy')
                ->groupBy('p.productGroup');

            $result = $qb->setFirstResult(0)
                ->setMaxResults($limit)
                ->getQuery()
                ->getResult();

            usort($result, function($a, $b) use ($ids) {
                $sort = array_flip($ids);

                return $sort[$a->getId()] > $sort[$b->getId()];
            });
        }

        return $this->renderResponse($request->isXmlHttpRequest() ? self::TEMPLATE_AJAX : $blockContext->getTemplate(), [
            'products'  => $result ?? [],
            'block'     => $block,
            'settings'  => array_merge($blockContext->getSettings(), $block->getSettings()),
        ], $response);
    }
}
