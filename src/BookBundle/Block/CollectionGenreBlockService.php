<?php

namespace BookBundle\Block;

use BookBundle\Entity\BookCollection;
use Doctrine\Bundle\DoctrineBundle\Registry;
use AppBundle\Block\AbstractEditableBlockService;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Twig\Environment;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class CollectionGenreBlockService
 */
class CollectionGenreBlockService extends AbstractEditableBlockService
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
    public function __construct(Environment $twig, Registry $doctrine)
    {
        parent::__construct($twig);

        $this->doctrine = $doctrine;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureSettings(OptionsResolver $resolver): void    {
        $resolver->setDefaults([
            'template'      => '@Book/Block/collection_genres.html.twig',
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
    public function execute(BlockContextInterface $blockContext, ?Response $response = null): Response    {
        $block = $blockContext->getBlock();

        if (!$block->getEnabled()) {
            return new Response();
        }

        $repository = $this->doctrine->getRepository(BookCollection::class);

        $genres = $repository->getGenresCollection();

        return $this->renderResponse($blockContext->getTemplate(), [
            'genres'      => $genres,
            'block'       => $block,
            'settings'    => array_merge($blockContext->getSettings(), $block->getSettings()),
        ], $response);
    }
}
