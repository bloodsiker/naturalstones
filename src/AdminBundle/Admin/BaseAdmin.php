<?php

namespace AdminBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin as Admin;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class BaseAdmin
 */
class BaseAdmin extends Admin
{
    private $searchSynchronization;

    private bool $searchSphinxEnabled = false;

    private ?TokenStorageInterface $tokenStorage = null;

    public function setSearchSynchronization($searchSynchronization = null): void
    {
        $this->searchSynchronization = $searchSynchronization;
    }

    public function setSearchSphinxEnabled(bool $searchSphinxEnabled): void
    {
        $this->searchSphinxEnabled = $searchSphinxEnabled;
    }

    public function setTokenStorage(TokenStorageInterface $tokenStorage): void
    {
        $this->tokenStorage = $tokenStorage;
    }

    protected function configureFormOptions(array &$formOptions): void
    {
        parent::configureFormOptions($formOptions);

        if (!isset($formOptions['translation_domain'])) {
            $formOptions['translation_domain'] = $this->getTranslationDomain();
        }
    }

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
        if ($this->isSearchSynchronizationEnabled()) {
            $this->searchSynchronization->insert($object);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function postUpdate(object $object): void
    {
        if ($this->isSearchSynchronizationEnabled()) {
            $this->searchSynchronization->update($object);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function preRemove(object $object): void
    {
        if ($this->isSearchSynchronizationEnabled()) {
            $this->searchSynchronization->delete($object);
        }
    }

    protected function getAdminUser()
    {
        if (!$this->tokenStorage || !$this->tokenStorage->getToken()) {
            return null;
        }

        return $this->tokenStorage->getToken()->getUser();
    }

    private function isSearchSynchronizationEnabled(): bool
    {
        return $this->searchSphinxEnabled && null !== $this->searchSynchronization;
    }
}
