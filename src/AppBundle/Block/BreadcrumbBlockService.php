<?php

namespace AppBundle\Block;

use AppBundle\Services\BreadcrumbService;
use Sonata\BlockBundle\Meta\Metadata;
use Sonata\BlockBundle\Block\Service\AbstractAdminBlockService;
use Sonata\BlockBundle\Block\BlockContextInterface;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;

/**
 * Class BreadcrumbBlockService
 */
class BreadcrumbBlockService extends AbstractAdminBlockService
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
    public function __construct($name, EngineInterface $templating, BreadcrumbService $breadcrumb)
    {
        parent::__construct($name, $templating);

        $this->breadcrumb = $breadcrumb;
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
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
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
