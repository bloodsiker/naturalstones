<?php

namespace AppBundle\Controller;

use Sonata\BlockBundle\Block\BlockContextManagerInterface;
use Sonata\BlockBundle\Block\BlockRendererInterface;
use Sonata\BlockBundle\Model\Block;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BlockController extends BaseController
{
    public function __construct(
        private readonly BlockContextManagerInterface $blockContextManager,
        private readonly BlockRendererInterface $blockRenderer,
    ) {
    }

    public function ajaxAction(Request $request): ?Response
    {
        $block = new Block();
        $block->setId($request->attributes->get('blockId'));
        $block->setType($request->attributes->get('blockType'));
        $block->setEnabled(true);

        return $this->blockRenderer->render($this->blockContextManager->get($block));
    }
}