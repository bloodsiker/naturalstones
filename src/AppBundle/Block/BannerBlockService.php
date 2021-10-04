<?php

namespace AppBundle\Block;

use Sonata\AdminBundle\Form\FormMapper;
use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\CoreBundle\Form\Type\ImmutableArrayType;
use Sonata\CoreBundle\Validator\ErrorElement;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Sonata\BlockBundle\Meta\Metadata;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Block\Service\AbstractAdminBlockService;
use Symfony\Component\Validator\Constraints\NotNull;

/**
 * Class BannerBlockService
 */
class BannerBlockService extends AbstractAdminBlockService
{
    const BANNER_PREMIUM_1 = 'banner-premium1-index';
    const BANNER_PREMIUM_2 = 'banner-premium2-index';

    /**
     * @param OptionsResolver $resolver
     */
    public function configureSettings(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'banner'   => null,
            'template' => 'AppBundle:Block:banner.html.twig',
        ]);
    }

    /**
     * @param null $code
     *
     * @return Metadata
     */
    public function getBlockMetadata($code = null)
    {
        return new Metadata(
            $this->getName(),
            (!is_null($code) ? $code : $this->getName()),
            false,
            'AppBundle',
            ['class' => 'fa fa-newspaper-o']
        );
    }

    /**
     * @param ErrorElement   $errorElement
     * @param BlockInterface $block
     */
    public function validateBlock(ErrorElement $errorElement, BlockInterface $block)
    {
        $errorElement
            ->with('settings[banner]')
                ->addConstraint(new NotNull())
            ->end()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function buildEditForm(FormMapper $formMapper, BlockInterface $block)
    {
        $formMapper->add('settings', ImmutableArrayType::class, [
            'translation_domain' => 'AppBundle',
            'label' => false,
            'keys' => [
                ['banner', ChoiceType::class, [
                    'label'    => 'app.block.fields.type_banner',
                    'required' => false,
                    'choices'  => [
                        'app.block.fields.premium_banner1' => self::BANNER_PREMIUM_1,
                        'app.block.fields.premium_banner2' => self::BANNER_PREMIUM_2,
                    ],
                ],
                ],
            ],
        ]);
    }

    /**
     * @param BlockContextInterface $blockContext
     * @param Response|null         $response
     *
     * @return Response
     *
     * @throws \Exception
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        $block = $blockContext->getBlock();

        if (!$block->getEnabled()) {
            return new Response();
        }

        return $this->renderResponse($blockContext->getTemplate(), [
            'banner'  => $block->getSetting('banner'),
            'block'    => $blockContext->getBlock(),
            'settings' => array_merge($blockContext->getSettings(), $block->getSettings()),
        ], $response);
    }
}
