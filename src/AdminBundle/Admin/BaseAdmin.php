<?php

namespace AdminBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class BaseAdmin extends AbstractAdmin
{
    private ?object $searchSynchronization = null;
    private bool $searchSphinxEnabled = false;
    private ?TokenStorageInterface $tokenStorage = null;

    public function setSearchSynchronization(?object $searchSynchronization = null): void
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

    public function postPersist(object $object): void
    {
        if ($this->isSearchSynchronizationEnabled()) {
            $this->searchSynchronization->insert($object);
        }
    }

    public function postUpdate(object $object): void
    {
        if ($this->isSearchSynchronizationEnabled()) {
            $this->searchSynchronization->update($object);
        }
    }

    public function preRemove(object $object): void
    {
        if ($this->isSearchSynchronizationEnabled()) {
            $this->searchSynchronization->delete($object);
        }
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

    protected function getAdminUser(): ?object
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