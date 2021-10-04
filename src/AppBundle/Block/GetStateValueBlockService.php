<?php

namespace AppBundle\Block;

use AppBundle\Services\SaveStateValue;
use Sonata\BlockBundle\Meta\Metadata;
use Sonata\BlockBundle\Block\Service\AbstractAdminBlockService;
use Sonata\BlockBundle\Block\BlockContextInterface;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;

/**
 * Class GetStateValueBlockService
 */
class GetStateValueBlockService extends AbstractAdminBlockService
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
    public function __construct($name, EngineInterface $templating)
    {
        parent::__construct($name, $templating);
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
        $resolver->setDefaults(array(
            'template'  => 'AppBundle:Block:get_state_value.html.twig',
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
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
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
