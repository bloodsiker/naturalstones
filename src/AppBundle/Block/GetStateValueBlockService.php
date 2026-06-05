<?php

namespace AppBundle\Block;

use AppBundle\Services\SaveStateValue;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Twig\Environment;

class GetStateValueBlockService extends AbstractEditableBlockService
{
    private ?SaveStateValue $saveStateService = null;

    public function __construct(Environment $twig)
    {
        parent::__construct($twig);
    }

    public function setSaveStateService(SaveStateValue $saveStateService): void
    {
        $this->saveStateService = $saveStateService;
    }

    public function configureSettings(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'template' => '@App/Block/get_state_value.html.twig',
            'key' => null,
        ]);
    }

    /**
     * @throws \Exception
     */
    public function execute(BlockContextInterface $blockContext, ?Response $response = null): Response
    {
        if (!$blockContext->getBlock()->getEnabled()) {
            return new Response();
        }

        $settings = $blockContext->getSettings();
        $value = !empty($settings['key']) && $this->saveStateService
            ? ($this->saveStateService->getValue($settings['key']) ?: null)
            : null;

        return $this->renderResponse($blockContext->getTemplate(), [
            'value' => $value,
            'settings' => $blockContext->getSettings(),
            'block' => $blockContext->getBlock(),
        ]);
    }
}