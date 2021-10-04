<?php

namespace PageBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class PageBundle
 */
class PageBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'SonataPageBundle';
    }
}