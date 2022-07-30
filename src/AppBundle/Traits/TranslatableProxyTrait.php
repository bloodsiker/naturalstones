<?php

namespace AppBundle\Traits;

/**
 * Trait for magic method __call. Should be used inside entity, that needs to be translated.
 */
trait TranslatableProxyTrait
{
    /**
     * @param string $method
     * @param array  $arguments
     *
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        if (!empty($arguments[0]) && is_array($arguments[0]) && array_key_exists('locale', $arguments[0])) {
            $this->setCurrentLocale($arguments[0]['locale']);
        }

        return $this->proxyCurrentLocaleTranslation($method, $arguments);
    }
}
