<?php

namespace ProductBundle\Block\Traits;

use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Sonata\BlockBundle\Model\BlockInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;

trait StrictRelationSonataAdminTrait
{
    protected function getAdminBuilder(FormMapper $formMapper, string $fieldName, ?string $transLabel = null): FormBuilderInterface
    {
        $label = $transLabel ?: 'product.block.fields.' . $fieldName;

        if (!$this->hasAdmin($fieldName)) {
            return $formMapper->create($fieldName, TextType::class, [
                'label' => $label,
                'required' => true,
            ]);
        }

        $admin = $this->admins[$fieldName];
        $fieldDescription = $admin->createFieldDescription($fieldName, [
            'translation_domain' => 'ProductBundle',
        ]);

        if (method_exists($admin, 'setAllowAllRoutes')) {
            $admin->setAllowAllRoutes(true);
        }

        $fieldDescription->setAssociationAdmin($admin);
        $fieldDescription->setAdmin($formMapper->getAdmin());

        return $formMapper->create($fieldName, ModelListType::class, [
            'sonata_field_description' => $fieldDescription,
            'btn_edit' => false,
            'btn_add' => false,
            'class' => $admin->getClass(),
            'model_manager' => $admin->getModelManager(),
            'label' => $label,
            'required' => false,
        ]);
    }

    public function load(BlockInterface $block): void
    {
        parent::load($block);

        foreach ($this->filterSettings($block->getSettings()) as $name => $entity) {
            if (is_object($entity) || null === $entity) {
                continue;
            }

            $resolved = ($this->hasAdmin($name) && $this->admins[$name]->getModelManager())
                ? $this->admins[$name]->getObject($entity)
                : $this->doctrine->getRepository($this->entities[$name])->find($entity);

            $block->setSetting($name, $resolved);
        }
    }

    public function prePersist(BlockInterface $block): void
    {
        parent::prePersist($block);

        foreach ($this->filterSettings($block->getSettings()) as $name => $entity) {
            $block->setSetting($name, is_object($entity) ? $entity->getId() : $entity);
        }
    }

    public function preUpdate(BlockInterface $block): void
    {
        $this->prePersist($block);
    }

    /**
     * @param array<string, mixed> $settings
     *
     * @return array<string, mixed>
     */
    public function filterSettings(array $settings): array
    {
        $keyList = array_keys($this->entities);

        return array_filter(
            $settings,
            static fn (string $key) => in_array($key, $keyList, true),
            ARRAY_FILTER_USE_KEY
        );
    }

    public function hasAdmin(string $name): bool
    {
        return isset($this->admins[$name]) && is_object($this->admins[$name]);
    }
}