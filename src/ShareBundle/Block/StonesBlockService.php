<?php

namespace ShareBundle\Block;

use Doctrine\Bundle\DoctrineBundle\Registry;
use ShareBundle\Entity\Stone;
use Sonata\BlockBundle\Meta\Metadata;
use Sonata\BlockBundle\Block\Service\AbstractAdminBlockService;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class StonesBlockService
 */
class StonesBlockService extends AbstractAdminBlockService
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
            'items_count'  => 200,
            'is_main'      => false,
            'show_letters' => false,
            'letter'       => null,
            'zodiac'       => null,
            'view_all'     => false,
            'title'        => null,
            'template'     => 'ShareBundle:Block:stones.html.twig',
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

        $repository = $this->doctrine->getRepository(Stone::class);

        $limit = $blockContext->getSetting('items_count');

        $qb = $repository->baseStoneQueryBuilder();

        if ($blockContext->getSetting('is_main')) {
            $repository->filterByShowMain($qb);
        }

        if ($blockContext->getSetting('zodiac')) {
            $repository->filterByZodiac($qb, $blockContext->getSetting('zodiac'));
        }

        if ($blockContext->getSetting('letter')) {
            $repository->filterByLetter($qb, $blockContext->getSetting('letter'));
        }

        $stones = $qb->setFirstResult(0)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

//        dump($stones);die;

        if ($blockContext->getSetting('show_letters')) {
            $letterStones = [];
            foreach ($repository->uniqLetterByStone() as $value) {
                $letterStones[$value[1]] = [];
            }

            foreach ($stones as $stone) {
                $first = mb_substr($stone->getName(), 0, 1);
                if (array_key_exists($first, $letterStones)) {
                    $letterStones[$first][] = $stone;
                }
            }
        }

        return $this->renderResponse($blockContext->getTemplate(), [
            'letters'  => $letterStones ?? [],
            'stones'   => $stones,
            'block'    => $block,
            'settings' => array_merge($blockContext->getSettings(), $block->getSettings()),
        ], $response);
    }
}
