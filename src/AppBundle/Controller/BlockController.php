<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sonata\BlockBundle\Model\Block;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class BlockController
 */
class BlockController extends Controller
{
//     * @Cache(maxage=600, public=true)

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

        $blockContext = $this->get('sonata.block.context_manager.default')->get($block);

        return $this->get('sonata.block.renderer.default')->render($blockContext);
    }
}
