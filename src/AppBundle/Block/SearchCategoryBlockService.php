<?php

namespace AppBundle\Block;

use Doctrine\ORM\EntityManager;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use ProductBundle\Entity\Category;
use ProductBundle\Entity\Product;
use ProductBundle\Entity\ProductSearchHistory;
use Sonata\BlockBundle\Meta\Metadata;
use Sonata\BlockBundle\Block\Service\AbstractAdminBlockService;
use Sonata\BlockBundle\Block\BlockContextInterface;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;

/**
 * Class SearchCategoryBlockService
 */
class SearchCategoryBlockService extends AbstractAdminBlockService
{
    const DEFAULT_TEMPLATE = 'AppBundle:search_category/Block:large_list.html.twig';

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var RequestStack
     */
    private $request;

    /**
     * HeaderBlockService constructor.
     *
     * @param string          $name
     * @param EngineInterface $templating
     * @param EntityManager   $em
     * @param RequestStack    $request
     */
    public function __construct($name, EngineInterface $templating, EntityManager $em, RequestStack $request)
    {
        parent::__construct($name, $templating);

        $this->em = $em;
        $this->request = $request;
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
            'list_type'      => null,
            'search'         => null,
            'items_count'    => 40,
            'template'       => self::DEFAULT_TEMPLATE,
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

        $request = $this->request->getCurrentRequest();

        $search = $blockContext->getSetting('search')
            ? $blockContext->getSetting('search') : $request->get('search');

        $resultByCategory = [];

        if ($search) {
            $repository = $this->em->getRepository(Product::class);
            $repositoryCategory = $this->em->getRepository(Category::class);

            $qb = $repository->baseProductQueryBuilder();
            $qb = $repository->filterByLocale($qb, $search);
            $qb->orderBy('p.views', 'DESC');

            $result = $qb->getQuery()->getResult();

            /** @var Product $item */
            foreach ($result as $item) {
                $resultByCategory[$item->getCategory()->getId()][] = $item;
            }

            $resultDetails = [];
            foreach ($resultByCategory as $categoryId => $itemByCategory) {
                $category = $repositoryCategory->find($categoryId);
                $count = count($itemByCategory);
                $resultDetails[$categoryId]['sort'] = $category->getOrderNum();
                $resultDetails[$categoryId]['category'] = $category;
                $resultDetails[$categoryId]['count'] = $count;
                if ($count >= 4) {
                    $resultDetails[$categoryId]['products'] = array_slice($itemByCategory, 0, 4);
                } else {
                    $resultDetails[$categoryId]['products'] = $itemByCategory;
                }
            }

            usort($resultDetails, function ($a, $b) {
                return -1 * ($a['sort'] <=> $b['sort']);
            });

            $ip = $request->server->get('REMOTE_ADDR');
            $history = new ProductSearchHistory();
            $history->setSearch($search);
            $history->setIp($ip);

            $this->em->persist($history);
            $this->em->flush();
        }

        $template = !is_null($blockContext->getSetting('list_type'))
            ? $blockContext->getSetting('list_type') : $blockContext->getTemplate();

        return $this->renderResponse($template, [
            'result'      => $resultDetails ?? [],
            'search'      => $search,
            'block'       => $block,
            'settings'    => array_merge($blockContext->getSettings(), $block->getSettings()),
        ]);
    }
}
