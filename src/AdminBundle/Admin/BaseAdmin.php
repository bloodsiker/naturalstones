<?php

namespace AdminBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin as Admin;

/**
 * Class BaseAdmin
 */
class BaseAdmin extends Admin
{
    /**
     * @return array
     */
    public function getBatchActions()
    {
        $actions = parent::getBatchActions();
        unset($actions['delete']);

        return $actions;
    }

    /**
     * {@inheritdoc}
     */
    public function postPersist($object)
    {
        $container = $this->getConfigurationPool()->getContainer();
        if ($container->has('search.synchronization') && $container->getParameter('search_sphinx_enabled')) {
            $container->get('search.synchronization')->insert($object);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function postUpdate($object)
    {
        $container = $this->getConfigurationPool()->getContainer();
        if ($container->has('search.synchronization') && $container->getParameter('search_sphinx_enabled')) {
            $container->get('search.synchronization')->update($object);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function preRemove($object)
    {
        $container = $this->getConfigurationPool()->getContainer();
        if ($container->has('search.synchronization') && $container->getParameter('search_sphinx_enabled')) {
            $container->get('search.synchronization')->delete($object);
        }
    }
}