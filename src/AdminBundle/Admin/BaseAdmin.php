<?php

namespace AdminBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin as Admin;

/**
 * Class BaseAdmin
 */
class BaseAdmin extends Admin
{
    protected function configureBatchActions(array $actions): array
    {
        $actions = parent::configureBatchActions($actions);
        unset($actions['delete']);

        return $actions;
    }

    /**
     * {@inheritdoc}
     */
    public function postPersist(object $object): void
    {
        $container = $this->getConfigurationPool()->getContainer();
        if ($container->has('search.synchronization') && $container->getParameter('search_sphinx_enabled')) {
            $container->get('search.synchronization')->insert($object);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function postUpdate(object $object): void
    {
        $container = $this->getConfigurationPool()->getContainer();
        if ($container->has('search.synchronization') && $container->getParameter('search_sphinx_enabled')) {
            $container->get('search.synchronization')->update($object);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function preRemove(object $object): void
    {
        $container = $this->getConfigurationPool()->getContainer();
        if ($container->has('search.synchronization') && $container->getParameter('search_sphinx_enabled')) {
            $container->get('search.synchronization')->delete($object);
        }
    }
}