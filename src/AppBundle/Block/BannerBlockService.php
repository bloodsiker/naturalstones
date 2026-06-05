<?php

namespace AppBundle\Block;

use Sonata\AdminBundle\Form\FormMapper;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Form\Mapper\FormMapper as BlockFormMapper;
use Sonata\BlockBundle\Meta\Metadata;
use Sonata\BlockBundle\Meta\MetadataInterface;
use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\Form\Type\ImmutableArrayType;
use Sonata\Form\Validator\ErrorElement;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotNull;

class BannerBlockService extends AbstractEditableBlockService
{
    public const BANNER_PREMIUM_1 = 'banner-premium1-index';
    public const BANNER_PREMIUM_2 = 'banner-premium2-index';

    public function configureSettings(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'banner' => null,
            'template' => '@App/Block/banner.html.twig',
        ]);
    }

    public function validateBlock(ErrorElement $errorElement, BlockInterface $block): void
    {
        $errorElement
            ->with('settings[banner]')
                ->addConstraint(new NotNull())
            ->end();
    }

    public function buildEditForm(FormMapper $formMapper, BlockInterface $block): void
    {
        $formMapper->add('settings', ImmutableArrayType::class, [
            'translation_domain' => 'AppBundle',
            'label' => false,
            'keys' => [
                ['banner', ChoiceType::class, [
                    'label' => 'app.block.fields.type_banner',
                    'required' => false,
                    'choices' => [
                        'app.block.fields.premium_banner1' => self::BANNER_PREMIUM_1,
                        'app.block.fields.premium_banner2' => self::BANNER_PREMIUM_2,
                    ],
                ]],
            ],
        ]);
    }

    public function configureEditForm(BlockFormMapper $form, BlockInterface $block): void
    {
        $this->buildEditForm($form, $block);
    }

    public function configureCreateForm(BlockFormMapper $form, BlockInterface $block): void
    {
        $this->configureEditForm($form, $block);
    }

    public function validate(ErrorElement $errorElement, BlockInterface $block): void
    {
        $this->validateBlock($errorElement, $block);
    }

    public function getMetadata(): MetadataInterface
    {
        return new Metadata('Banner', null, null, 'AppBundle', [
            'class' => 'fa fa-image',
        ]);
    }

    /**
     * @throws \Exception
     */
    public function execute(BlockContextInterface $blockContext, ?Response $response = null): Response
    {
        $block = $blockContext->getBlock();
        if (!$block->getEnabled()) {
            return new Response();
        }

        return $this->renderResponse($blockContext->getTemplate(), [
            'banner' => $block->getSetting('banner'),
            'block' => $block,
            'settings' => array_merge($blockContext->getSettings(), $block->getSettings()),
        ], $response);
    }
}