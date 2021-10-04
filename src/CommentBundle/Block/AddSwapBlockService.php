<?php

namespace CommentBundle\Block;

use CommentBundle\Entity\Swap;
use Doctrine\ORM\EntityManager;
use Sonata\BlockBundle\Meta\Metadata;
use Sonata\BlockBundle\Block\Service\AbstractAdminBlockService;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class AddSwapBlockService
 */
class AddSwapBlockService extends AbstractAdminBlockService
{
    const FORM_TEMPLATE = 'CommentBundle:Block:swap_form.html.twig';
    const AJAX_SWAP_TEMPLATE = 'CommentBundle:Block:ajax_swap.html.twig';

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var RequestStack
     */
    private $request;

    /**
     * ListGenreBlockService constructor.
     *
     * @param string          $name
     * @param EngineInterface $templating
     * @param EntityManager   $em
     * @param RequestStack    $request
     */
    public function __construct($name, EngineInterface $templating, EntityManager $em, RequestStack $request)
    {
        parent::__construct($name, $templating);

        $this->em = $em;
        $this->request = $request;
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
            'CommentBundle',
            ['class' => 'fa fa-code']
        );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureSettings(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'template' => self::FORM_TEMPLATE,
        ]);
    }

    /**
     * @param BlockContextInterface $blockContext
     * @param Response|null         $response
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        $block = $blockContext->getBlock();

        if (!$block->getEnabled()) {
            return new Response();
        }

        $request = $this->request->getCurrentRequest();

        if ($request->isXmlHttpRequest() && $request->getMethod() === 'POST') {

            $swap = new Swap();
            $swap->setUserName($request->get('name'));
            $swap->setUserEmail($request->get('email'));
            $swap->setContent($request->get('content'));

            $this->em->persist($swap);
            $this->em->flush();
        }


        return $this->renderResponse($request->isXmlHttpRequest() ? self::AJAX_SWAP_TEMPLATE : $blockContext->getTemplate(), [
            'swap'     => $swap ?? null,
            'block'    => $block,
            'settings' => array_merge($blockContext->getSettings(), $block->getSettings()),
        ], $response);
    }
}
