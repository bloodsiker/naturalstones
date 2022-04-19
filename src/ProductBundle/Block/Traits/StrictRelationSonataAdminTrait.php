<?php

namespace ProductBundle\Block\Traits;

use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Sonata\BlockBundle\Model\BlockInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Trait StrictRelationSonataAdminTrait
 */
trait StrictRelationSonataAdminTrait
{
    /**
     * @param FormMapper $formMapper
     * @param string     $fieldName
     * @param string     $transLabel
     *
     * @return \Symfony\Component\Form\FormBuilderInterface
     */
    protected function getAdminBuilder(FormMapper $formMapper, string $fieldName, string $transLabel = null)
    {
        if (!$this->hasAdmin($fieldName)) {
            return $formMapper->create($fieldName, TextType::class, [
                'label'     => $transLabel ?: 'product.block.fields.'.$fieldName,
                'required'  => true,
            ]);
        }

        $fieldDescription = $this
            ->admins[$fieldName]
            ->getModelManager()
            ->getNewFieldDescriptionInstance($this->admins[$fieldName]->getClass(), $fieldName);
        if (method_exists($this->admins[$fieldName], 'setAllowAllRoutes')) {
            $this->admins[$fieldName]->setAllowAllRoutes(true);
        }
        $fieldDescription->setAssociationAdmin($this->admins[$fieldName]);
        $fieldDescription->setAdmin($formMapper->getAdmin());
        $fieldDescription->setAssociationMapping([
            'fieldName' => $fieldName,
            'type'      => ClassMetadataInfo::ONE_TO_MANY,
        ]);
        $fieldDescription->setOption('translation_domain', 'ProductBundle');

        return $formMapper->create($fieldName, ModelListType::class, [
            'sonata_field_description'  => $fieldDescription,
            'btn_edit'                  => false,
            'btn_add'                   => false,
            'class'                     => $this->admins[$fieldName]->getClass(),
            'model_manager'             => $this->admins[$fieldName]->getModelManager(),
            'label'                     => $transLabel ?: 'product.block.fields.'.$fieldName,
            'required'                  => false,
        ]);
    }

    /**
     * @param BlockInterface $block
     */
    public function load(BlockInterface $block)
    {
        parent::load($block);
        $doctrine = $this->doctrine;
        foreach ($this->filterSettings($block->getSettings()) as $name => $entity) {
            if (!is_object($entity) && null !== $entity) {
                if ($this->hasAdmin($name) && $this->admins[$name]->getModelManager()) {
                    $entity = $this->admins[$name]->getObject($entity);
                } else {
                    $entity = $doctrine->getRepository($this->entities[$name])->find($entity);
                }
                $block->setSetting($name, $entity);
            }
        }
    }

    /**
     * @param BlockInterface $block
     *
     * @return void
     */
    public function prePersist(BlockInterface $block) : void
    {
        parent::prePersist($block);

        foreach ($this->filterSettings($block->getSettings()) as $name => $entity) {
            $block->setSetting($name, is_object($entity) ? $entity->getId() : $entity);
        }
    }

    /**
     * @param BlockInterface $block
     */
    public function preUpdate(BlockInterface $block)
    {
        $this->prePersist($block);
    }

    /**
     * @param array $settings
     *
     * @return array
     */
    public function filterSettings(array $settings)
    {
        $keyList = array_keys($this->entities);

        return array_filter(
            $settings,
            function (string $key) use ($keyList) {
                return in_array($key, $keyList);
            },
            ARRAY_FILTER_USE_KEY
        );
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasAdmin($name)
    {
        return isset($this->admins[$name]) && is_object($this->admins[$name]);
    }
}