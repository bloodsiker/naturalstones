<?php

namespace MainImageBundle\Block;

use Doctrine\ORM\EntityManager;
use MainImageBundle\Entity\MainImage;
use Sonata\BlockBundle\Block\Service\AbstractBlockService;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Twig\Environment;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class MainImageBlockService
 */
class MainImageBlockService extends AbstractBlockService
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * MainImageBlockService constructor.
     *
     * @param string          $name
     * @param EngineInterface $templating
     * @param EntityManager   $em
     */
    public function __construct(Environment $twig, EntityManager $em)
    {
        parent::__construct($twig);

        $this->em = $em;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureSettings(OptionsResolver $resolver): void    {
        $resolver->setDefaults([
            'template' => '@MainImage/Block/main_image.html.twig',
        ]);
    }

    /**
     * @param BlockContextInterface $blockContext
     * @param Response|null         $response
     *
     * @return Response
     */
    public function execute(BlockContextInterface $blockContext, ?Response $response = null): Response    {
        $block = $blockContext->getBlock();

        if (!$block->getEnabled()) {
            return new Response();
        }

        $repository = $this->em->getRepository(MainImage::class);

        $mainImage = $repository->findOneBy(['isActive' => true], ['orderNum' => 'ASC']);

        return $this->renderResponse($blockContext->getTemplate(), [
            'mainImage' => $mainImage,
            'block'     => $block,
            'settings'  => array_merge($blockContext->getSettings(), $block->getSettings()),
        ], $response);
    }
}
