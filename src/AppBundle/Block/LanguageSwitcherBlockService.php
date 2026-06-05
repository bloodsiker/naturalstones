<?php

namespace AppBundle\Block;

use Sonata\BlockBundle\Block\BlockContextInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Twig\Environment;

class LanguageSwitcherBlockService extends AbstractEditableBlockService
{
    public const TYPE_MAIN = 'main';
    public const TYPE_FOOTER = 'footer';

    protected ?Request $request;

    /**
     * @param list<string> $locales
     */
    public function __construct(
        Environment $twig,
        RequestStack $requestStack,
        private readonly array $locales,
    ) {
        parent::__construct($twig);
        $this->request = $requestStack->getCurrentRequest();
    }

    public function configureSettings(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'is_mobile' => false,
            'type' => self::TYPE_MAIN,
            'template' => '@App/Block/language_switcher.html.twig',
        ]);
    }

    public function execute(BlockContextInterface $blockContext, ?Response $response = null): Response
    {
        $block = $blockContext->getBlock();
        if (!$block->getEnabled()) {
            return new Response();
        }

        return $this->renderResponse($blockContext->getTemplate(), [
            'current_locale' => $this->request->getLocale(),
            'locales' => $this->locales,
            'block' => $block,
            'settings' => array_merge($blockContext->getSettings(), $block->getSettings()),
        ], $response);
    }
}
