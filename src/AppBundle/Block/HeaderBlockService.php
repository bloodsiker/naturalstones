<?php

namespace AppBundle\Block;

use Doctrine\ORM\EntityManager;
use Sonata\BlockBundle\Block\Service\AbstractBlockService;
use Sonata\BlockBundle\Block\BlockContextInterface;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Twig\Environment;

/**
 * Class HeaderBlockService
 */
class HeaderBlockService extends AbstractBlockService
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
            'template'  => 'AppBundle:Block:header.html.twig',
            'class'     => null,
        ]);
    }

    /**
     * @param BlockContextInterface $blockContext
     * @param Response|null         $response
     *
     * @return Response
     */
    public function execute(BlockContextInterface $blockContext, ?Response $response = null): Response    {
        if (!$blockContext->getBlock()->getEnabled()) {
            return new Response();
        }

        return $this->renderResponse($blockContext->getTemplate(), [
            'settings'      => $blockContext->getSettings(),
            'block'         => $blockContext->getBlock(),
        ]);
    }
}
