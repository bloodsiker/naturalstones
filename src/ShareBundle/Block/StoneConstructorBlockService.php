<?php

namespace ShareBundle\Block;

use Doctrine\Bundle\DoctrineBundle\Registry;
use ShareBundle\Entity\Stone;
use Sonata\BlockBundle\Meta\Metadata;
use Sonata\BlockBundle\Block\Service\AbstractAdminBlockService;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class StonesBlockService
 */
class StoneConstructorBlockService extends AbstractAdminBlockService
{
    /**
     * @var Registry $doctrine
     */
    protected $doctrine;

    /**
     * @var RequestStack
     */
    private $request;

    /**
     * ListGenreBlockService constructor.
     *
     * @param string          $name
     * @param EngineInterface $templating
     * @param Registry        $doctrine
     */
    public function __construct($name, EngineInterface $templating, Registry $doctrine, RequestStack $request)
    {
        parent::__construct($name, $templating);

        $this->doctrine = $doctrine;
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
            'ShareBundle',
            ['class' => 'fa fa-code']
        );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureSettings(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'show_letters' => false,
            'title'        => null,
            'template'     => 'ShareBundle:Block:stone_constructor.html.twig',
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
        $isAjax = $request->isXmlHttpRequest();
        $size = (string) $request->get('size');

        if ($isAjax) {
            $repository = $this->doctrine->getRepository(Stone::class);

            $qb = $repository->baseStoneQueryBuilder();

            $repository->filterByShowConstructor($qb);

            $stones = $qb->getQuery()->getResult();

            $result = [];
            $stonesList = [];
            foreach ($stones as $stone) {
                foreach ($stone->getStoneHasConstructor() as $constructor) {
                    if ($size === $constructor->getSize()) {
                        $result[$stone->getSlug()][] = [
                            'id' => $constructor->getId(),
                            'name' => $stone->getName(),
                            'img' => $constructor->getImage()->getPath()
                        ];

                        $stonesList[$stone->getId()] = [
                            'slug' => $stone->getSlug(),
                            'name' => $stone->getName(),
                        ];
                    }
                }
            }

            return new JsonResponse([
                'stones' => $stonesList,
                'stoneType' => $result,
            ]);
        }

        return $this->renderResponse($blockContext->getTemplate(), [
            'block'    => $block,
            'settings' => array_merge($blockContext->getSettings(), $block->getSettings()),
        ], $response);
    }
}
