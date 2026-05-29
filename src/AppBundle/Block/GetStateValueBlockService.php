<?php

namespace AppBundle\Block;

use AppBundle\Services\SaveStateValue;
use Sonata\BlockBundle\Block\Service\AbstractBlockService;
use Sonata\BlockBundle\Block\BlockContextInterface;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Twig\Environment;

/**
 * Class GetStateValueBlockService
 */
class GetStateValueBlockService extends AbstractBlockService
{
    /**
     * @var SaveStateValue
     */
    private $saveStateService;

    /**
     * GetStateValueBlockService constructor.
     *
     * @param string          $name
     * @param EngineInterface $templating
     */
    public function __construct(Environment $twig)
    {
        parent::__construct($twig);
    }

    /**
     * @param SaveStateValue $saveStateService
     *
     * @return void
     */
    public function setSaveStateService(SaveStateValue $saveStateService)
    {
        $this->saveStateService = $saveStateService;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureSettings(OptionsResolver $resolver): void    {
        $resolver->setDefaults(array(
            'template'  => '@App/Block/get_state_value.html.twig',
            'key' => null,
        ));
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

        $settings = $blockContext->getSettings();
        if (!empty($settings['key'])) {
            $value = $this->saveStateService->getValue($settings['key']) ?: null;
        } else {
            $value = null;
        }

        return $this->renderResponse($blockContext->getTemplate(), array(
            'value'     => $value,
            'settings'  => $blockContext->getSettings(),
            'block'     => $blockContext->getBlock(),
        ));
    }
}
