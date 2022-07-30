<?php

namespace TranslationBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class TranslationBundle
 */
class TranslationBundle extends Bundle
{
    /**
     * @return string
     */
    public function getParent()
    {
        return 'IbrowsSonataTranslationBundle';
    }
}
