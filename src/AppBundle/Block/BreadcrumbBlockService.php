<?php

namespace AppBundle\Block;

use AppBundle\Services\BreadcrumbService;
use Sonata\BlockBundle\Block\Service\AbstractBlockService;
use Sonata\BlockBundle\Block\BlockContextInterface;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Twig\Environment;

/**
 * Class BreadcrumbBlockService
 */
class BreadcrumbBlockService extends AbstractBlockService
{
    /**
     * @var BreadcrumbService
     */
    private $breadcrumb;

    /**
     * GetStateValueBlockService constructor.
     *
     * @param string            $name
     * @param EngineInterface   $templating
     * @param BreadcrumbService $breadcrumb
     */
    public function __construct(Environment $twig, BreadcrumbService $breadcrumb)
    {
        parent::__construct($twig);

        $this->breadcrumb = $breadcrumb;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureSettings(OptionsResolver $resolver): void    {
        $resolver->setDefaults([
            'template'  => 'AppBundle:Block:breadcrumb.html.twig',
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
        if (!$blockContext->getBlock()->getEnabled()) {
            return new Response();
        }

        return $this->renderResponse($blockContext->getTemplate(), array(
            'breadcrumbs' => $this->breadcrumb->getBreadcrumb(),
            'settings'    => $blockContext->getSettings(),
            'block'       => $blockContext->getBlock(),
        ));
    }
}
