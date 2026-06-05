<?php

namespace CommentBundle\Block;

use AppBundle\Block\AbstractEditableBlockService;
use CommentBundle\Entity\Comment;
use Doctrine\ORM\EntityManagerInterface;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Twig\Environment;

class AddCommentBlockService extends AbstractEditableBlockService
{
    public const FORM_TEMPLATE = '@Comment/Block/comment_form.html.twig';
    public const AJAX_COMMENT_TEMPLATE = '@Comment/Block/ajax_comment.html.twig';

    public function __construct(
        Environment $twig,
        private readonly EntityManagerInterface $em,
        private readonly RequestStack $request,
    ) {
        parent::__construct($twig);
    }

    public function configureSettings(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'template' => self::FORM_TEMPLATE,
        ]);
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     */
    public function execute(BlockContextInterface $blockContext, ?Response $response = null): Response
    {
        $block = $blockContext->getBlock();
        if (!$block->getEnabled()) {
            return new Response();
        }

        $request = $this->request->getCurrentRequest();
        $comment = null;

        if ($request->isXmlHttpRequest() && 'POST' === $request->getMethod()) {
            $comment = new Comment();
            $comment->setUserName($request->get('name'));
            $comment->setUserEmail($request->get('email'));
            $comment->setComment($request->get('comment'));

            $this->em->persist($comment);
            $this->em->flush();
        }

        return $this->renderResponse(
            $request->isXmlHttpRequest() ? self::AJAX_COMMENT_TEMPLATE : $blockContext->getTemplate(),
            [
                'comment' => $comment,
                'block' => $block,
                'settings' => array_merge($blockContext->getSettings(), $block->getSettings()),
            ],
            $response
        );
    }
}