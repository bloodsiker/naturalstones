<?php

namespace PageBundle\Block;

use Doctrine\Bundle\DoctrineBundle\Registry;
use PageBundle\Entity\SiteVariable;
use Sonata\BlockBundle\Block\Service\AbstractBlockService;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Twig\Environment;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class SiteVariableBlockService
 */
class SiteVariableBlockService extends AbstractBlockService
{
    /**
     * @var Registry $doctrine
     */
    protected $doctrine;

    /**
     * SiteVariableBlockService constructor.
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
            'placement' => null,
            'template'  => 'PageBundle:Block:site_variable.html.twig',
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

        $variables = $this->doctrine
            ->getRepository(SiteVariable::class)
            ->findVariables($blockContext->getSetting('placement'));

        return $this->renderResponse($blockContext->getTemplate(), [
            'variables' => $variables,
            'block'     => $block,
            'settings'  => array_merge($blockContext->getSettings(), $block->getSettings()),
        ], $response);
    }
}
