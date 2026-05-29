<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sonata\BlockBundle\Block\BlockContextManagerInterface;
use Sonata\BlockBundle\Block\BlockRendererInterface;
use Sonata\BlockBundle\Model\Block;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class BlockController
 */
class BlockController extends BaseController
{
//     * @Cache(maxage=600, public=true)
    /**
     * @var BlockContextManagerInterface
     */
    private $blockContextManager;

    /**
     * @var BlockRendererInterface
     */
    private $blockRenderer;

    public function __construct(BlockContextManagerInterface $blockContextManager, BlockRendererInterface $blockRenderer)
    {
        $this->blockContextManager = $blockContextManager;
        $this->blockRenderer = $blockRenderer;
    }

    /**
     * @param Request $request
     *
     * @return null|\Symfony\Component\HttpFoundation\Response
     *
     */
    public function ajaxAction(Request $request)
    {
        $blockId = $request->attributes->get('blockId');
        $blockType = $request->attributes->get('blockType');

        $block = new Block();
        $block->setId($blockId);
        $block->setType($blockType);
        $block->setEnabled(true);

        $blockContext = $this->blockContextManager->get($block);

        return $this->blockRenderer->render($blockContext);
    }
}
