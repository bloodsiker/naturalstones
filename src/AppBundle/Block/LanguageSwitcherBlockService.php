<?php

namespace AppBundle\Block;

use AppBundle\Block\AbstractEditableBlockService;
use Sonata\BlockBundle\Block\BlockContextInterface;

use Twig\Environment;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class LanguageSwitcherBlockService
 */
class LanguageSwitcherBlockService extends AbstractEditableBlockService
{
    const TYPE_MAIN   = 'main';
    const TYPE_FOOTER = 'footer';

    /**
     * @var null|\Symfony\Component\HttpFoundation\Request
     */
    protected $request;

    /**
     * @var array
     */
    private $locales;

    /**
     * @param string          $name
     * @param EngineInterface $templating
     * @param RequestStack    $requestStack
     * @param array           $locales
     */
    public function __construct(Environment $twig, RequestStack $requestStack, $locales)
    {
        parent::__construct($twig);

        $this->request = $requestStack->getCurrentRequest();
        $this->locales = $locales;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureSettings(OptionsResolver $resolver): void    {
        $resolver->setDefaults([
            'is_mobile' => false,
            'type'      => self::TYPE_MAIN,
            'template'  => '@App/Block/language_switcher.html.twig',
        ]);
    }

    /**
     * @param BlockContextInterface $blockContext
     * @param Response|null         $response
     *
     * @return Response
     */
    public function execute(BlockContextInterface $blockContext, ?Response $response = null): Response    {
        $block = $blockContext->getBlock();

        if (!$block->getEnabled()) {
            return new Response();
        }

        $currentLocale = $this->request->getLocale();

//        usort($this->locales, function ($a, $b) use ($currentLocale) {
//            if ($a === $currentLocale) {
//                return -1;
//            } else {
//                return 1;
//            }
//        });

        return $this->renderResponse($blockContext->getTemplate(), [
            'current_locale'    => $currentLocale,
            'locales'           => $this->locales,
            'block'             => $block,
            'settings'          => array_merge($blockContext->getSettings(), $block->getSettings()),
        ], $response);
    }
}
