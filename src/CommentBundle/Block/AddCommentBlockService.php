<?php

namespace CommentBundle\Block;

use BookBundle\Entity\Book;
use CommentBundle\Entity\Comment;
use Doctrine\ORM\EntityManager;
use Sonata\BlockBundle\Meta\Metadata;
use Sonata\BlockBundle\Block\Service\AbstractAdminBlockService;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class AddCommentBlockService
 */
class AddCommentBlockService extends AbstractAdminBlockService
{
    const FORM_TEMPLATE = 'CommentBundle:Block:comment_form.html.twig';
    const AJAX_COMMENT_TEMPLATE = 'CommentBundle:Block:ajax_comment.html.twig';

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
            'book'     => null,
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
            $book = $this->em->getRepository(Book::class)->find((int) $request->get('bookId'));

            $comment = new Comment();
            $comment->setBook($book);
            $comment->setUserName($request->get('name'));
            $comment->setUserEmail($request->get('email'));
            $comment->setComment($request->get('comment'));

            $this->em->persist($comment);
            $this->em->flush();
        }


        return $this->renderResponse($request->isXmlHttpRequest() ? self::AJAX_COMMENT_TEMPLATE : $blockContext->getTemplate(), [
            'book'      => $blockContext->getSetting('book'),
            'comment'   => $comment ?? null,
            'block'     => $block,
            'settings'  => array_merge($blockContext->getSettings(), $block->getSettings()),
        ], $response);
    }
}
