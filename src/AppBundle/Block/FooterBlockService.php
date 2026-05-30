<?php

namespace AppBundle\Block;

use AppBundle\Block\AbstractEditableBlockService;
use Sonata\BlockBundle\Block\BlockContextInterface;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class FooterBlockService
 */
class FooterBlockService extends AbstractEditableBlockService
{

    /**
     * @param OptionsResolver $resolver
     */
    public function configureSettings(OptionsResolver $resolver): void    {
        $resolver->setDefaults(array(
            'template' => '@App/Block/footer.html.twig',
        ));
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

        return $this->renderResponse($blockContext->getTemplate(), array(
            'settings'  => $blockContext->getSettings(),
            'block'     => $blockContext->getBlock(),
        ));
    }
}
