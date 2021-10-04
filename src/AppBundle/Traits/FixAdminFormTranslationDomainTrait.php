<?php

namespace AppBundle\Traits;

/**
 * Class FixAdminFormTranslationDomainTrait
 *
 * @package AppBundle\Traits
 */
trait FixAdminFormTranslationDomainTrait
{
    /**
     * {@inheritdoc}
     */
    public function setTranslationDomain($translationDomain)
    {
        $this->translationDomain = $translationDomain;
        $this->formOptions['translation_domain'] = $translationDomain;
    }

}