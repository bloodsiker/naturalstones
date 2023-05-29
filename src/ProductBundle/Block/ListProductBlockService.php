<?php

namespace ProductBundle\Block;

use ProductBundle\Block\Traits\HomepageDefaultMethodTrait;
use ProductBundle\Block\Traits\StrictRelationSonataAdminTrait;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use ProductBundle\Entity\Category;
use ProductBundle\Entity\Product;
use ShareBundle\Entity\Tag;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\BlockBundle\Meta\Metadata;
use Sonata\BlockBundle\Block\Service\AbstractAdminBlockService;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\CoreBundle\Form\Type\ImmutableArrayType;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ListProductBlockService
 */
class ListProductBlockService extends AbstractAdminBlockService
{
    use StrictRelationSonataAdminTrait,
        HomepageDefaultMethodTrait;

    const SIMILAR_LIST = 'ProductBundle:Block:similar_list.html.twig';
    const BUY_WITH_LIST = 'ProductBundle:Block:buy_with_list.html.twig';
    const TEMPLATE_AJAX  = 'ProductBundle:Block:large_list_ajax.html.twig';

    const CATEGORY_NABORI = 5;

    /**
     * @var RequestStack $requestStack
     */
    protected $requestStack;

    /**
     * ListGenreBlockService constructor.
     *
     * @param string          $name
     * @param EngineInterface $templating
     * @param RequestStack    $requestStack
     */
    public function __construct($name, EngineInterface $templating, RequestStack $requestStack)
    {
        parent::__construct($name, $templating);

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
            'title'            => null,
            'title_url'        => null,
            'list_type'        => null,
            'items_count'      => 20,
            'page'             => 1,
            'filter'           => false,
            'category'         => null,
            'category_slug'    => null,
            'tag'              => null,
            'colour'           => null,
            'stone'            => null,
            'who'              => null,
            'discount'         => false,
            'random'           => false,
            'exclude_ids'      => null,
            'show_paginator'   => false,
            'ajax_paginator'   => false,
            'template'         => 'ProductBundle:Block:large_list.html.twig',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function buildEditForm(FormMapper $formMapper, BlockInterface $block)
    {
        $formMapper->add('settings', ImmutableArrayType::class, [
            'translation_domain' => 'ProductBundle',
            'label' => false,
            'keys' => [
                ['title', TextType::class, [
                    'label'     => 'product.block.fields.title',
                    'required'  => false,
                ], ],
                ['items_count', TextType::class, [
                    'label'     => 'product.block.fields.items_count',
                    'required'  => false,
                ], ],
                [$this->getAdminBuilder($formMapper, 'category'), null, [
                    'required' => false,
                ], ],
                [$this->getAdminBuilder($formMapper, 'tag'), null, [
                    'required' => false,
                ], ],
                [$this->getAdminBuilder($formMapper, 'stone'), null, [
                    'required' => false,
                ], ],
                [$this->getAdminBuilder($formMapper, 'colour'), null, [
                    'required' => false,
                ], ],
                ['who', ChoiceType::class, [
                    'label'     => 'product.block.fields.who',
                    'required'  => false,
                    'choices'   => array_merge(['' => ''], array_flip(Product::$whois)),
                ], ],
                ['discount', CheckboxType::class, [
                    'label'     => 'product.block.fields.discount',
                    'required'  => false,
                ], ],
                ['random', CheckboxType::class, [
                    'label'     => 'product.block.fields.random',
                    'required'  => false,
                ], ],
            ],
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
        $isAjax = $request->isXmlHttpRequest();

        $limit = (int) $blockContext->getSetting('items_count');
        $page = $isAjax ? $request->get('page') : $blockContext->getSetting('page');

        $repository = $this->doctrine->getRepository(Product::class);
        $repositoryCategory = $this->doctrine->getRepository(Category::class);

        $qb = $repository->baseProductQueryBuilder();

        if ($blockContext->getSetting('category_slug')) {
            $category = $repositoryCategory->findOneBy(['slug' => $blockContext->getSetting('category_slug')]);
            if ($category) {
                $repository->filterByCategory($qb, $category);
            }
        }

        if ($blockContext->getSetting('category')) {
            $repository->filterByCategory($qb, $blockContext->getSetting('category'));
        }

        if ($blockContext->getSetting('tag')) {
            $repository->filterByTag($qb, $blockContext->getSetting('tag'));
        }

        if ($blockContext->getSetting('who')) {
            $repository->filterByWho($qb, $blockContext->getSetting('who'));
        }

        if ($blockContext->getSetting('colour')) {
            $repository->filterByColour($qb, $blockContext->getSetting('colour'));
        }

        if ($blockContext->getSetting('stone')) {
            $repository->filterByStone($qb, $blockContext->getSetting('stone'));
        }

        if ($blockContext->getSetting('exclude_ids')) {
            $repository->filterExclude($qb, $blockContext->getSetting('exclude_ids'));
        }

        if ($blockContext->getSetting('discount')) {
            $repository->filterByDiscount($qb);
        }

        if ($blockContext->getSetting('random')) {
            $repository->filterByRand($qb);
        }

        if ($blockContext->getSetting('filter')) {
            $maxPriceQb = $minPriceQb = clone $qb;
            $maxPrice = $maxPriceQb->resetDQLPart('select')->select('MAX(p.price) as max')->getQuery()->getSingleResult();
            $minPrice = $minPriceQb->resetDQLPart('select')->select('MIN(p.price) as min')->getQuery()->getSingleResult();

            $stonesQb = clone $qb;
            $productStones = $stonesQb->getQuery()->getResult();
            $stoneArray = [];
            $colourArray = [];
            foreach ($productStones as $productStone) {
                $stones = $productStone->getStones()->getValues();
                $colours = $productStone->getColours()->getValues();
                foreach ($stones as $stone) {
                    if (!array_key_exists($stone->getId(), $stoneArray)) {
                        $stoneArray[$stone->getId()] = $stone;
                    }
                }
                foreach ($colours as $colour) {
                    if (!array_key_exists($colour->getId(), $colourArray)) {
                        $colourArray[$colour->getId()] = $colour;
                    }
                }
            }
        }


        if ($request->get('min_price')) {
            $qb->andWhere('p.price >= :minPrice')->setParameter('minPrice', $request->get('min_price'));
        }

        if ($request->get('max_price')) {
            $qb->andWhere('p.price <= :maxPrice')->setParameter('maxPrice', $request->get('max_price'));
        }

        if ($request->get('sort')) {
            $qb->resetDQLPart('orderBy')->orderBy('p.price', $request->get('sort'));
        }

        if ($request->get('stone')) {
            $repository->filterByStones($qb, explode(',', $request->get('stone')));
        }

        if ($request->get('colour')) {
            $repository->filterByColours($qb, explode(',', $request->get('colour')));
        }

        $paginator = new Pagerfanta(new QueryAdapter($qb, true, false));
        $paginator->setAllowOutOfRangePages(true);
        $paginator->setMaxPerPage((int) $limit);
        $paginator->setCurrentPage((int) $page);

        $template = !is_null($blockContext->getSetting('list_type'))
            ? $blockContext->getSetting('list_type') : $blockContext->getTemplate();

        return $this->renderResponse($request->isXmlHttpRequest() ? self::TEMPLATE_AJAX : $template, [
            'products'  => $paginator,
            'maxPrice'  => $maxPrice['max'] ?? 0,
            'minPrice'  => $minPrice['min'] ?? 0,
            'stones'    => $stoneArray ?? [],
            'colours'   => $colourArray ?? [],
            'block'     => $block,
            'settings'  => array_merge($blockContext->getSettings(), $block->getSettings()),
        ], $response);
    }
}
