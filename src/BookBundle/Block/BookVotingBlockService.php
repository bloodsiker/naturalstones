<?php

namespace BookBundle\Block;

use BookBundle\Entity\Book;
use BookBundle\Entity\BookVotesResult;
use Doctrine\ORM\EntityManager;
use Sonata\BlockBundle\Meta\Metadata;
use Sonata\BlockBundle\Block\Service\AbstractAdminBlockService;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class BookVotingBlockService
 */
class BookVotingBlockService extends AbstractAdminBlockService
{
    /**
     * @var RequestStack
     */
    private $request;

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * ListGenreBlockService constructor.
     *
     * @param string          $name
     * @param EngineInterface $templating
     * @param RequestStack    $request
     * @param EntityManager   $em
     */
    public function __construct($name, EngineInterface $templating, RequestStack $request, EntityManager $em)
    {
        parent::__construct($name, $templating);

        $this->request  = $request;
        $this->em = $em;
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
            'BookBundle',
            ['class' => 'fa fa-th-large']
        );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureSettings(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'book'     => null,
            'template' => 'BookBundle:Block:book_voting.html.twig',
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

        $request = $this->request->getCurrentRequest();

        $book = $blockContext->getSetting('book');
        if (!$book) {
            $bookId = $request->request->get('bookId');
            $book = $this->em->getRepository(Book::class)->find((int) $bookId);
        }

        if (!$book->getId()) {
            return new Response();
        }

        $ip = ip2long($request->server->get('REMOTE_ADDR'));

        $resultVoted = $this->em->getRepository(BookVotesResult::class)
            ->findOneBy(['ip' => $ip, 'book' => $book]);

        if ($request->isXmlHttpRequest() && $request->getMethod() === 'POST') {
            $vote = (bool) $request->request->get('vote');

            if (!$resultVoted) {
                $resultVoted = new BookVotesResult();
                $resultVoted->setBook($book);
                $resultVoted->setIp($ip);
                $resultVoted->setResultVote($vote);

                if (true === $vote) {
                    $book->setRatePlus($book->getRatePlus() + 1);
                } else {
                    $book->setRateMinus($book->getRateMinus() + 1);
                }

                $this->em->persist($book);
                $this->em->persist($resultVoted);
            } else {
                if (true === $vote && false === $resultVoted->getResultVote()) {
                    $resultVoted->setResultVote($vote);
                    $book->setRatePlus($book->getRatePlus() + 1);
                    $book->setRateMinus($book->getRateMinus() - 1);
                } elseif (false === $vote && true === $resultVoted->getResultVote()) {
                    $resultVoted->setResultVote($vote);
                    $book->setRatePlus($book->getRatePlus() - 1);
                    $book->setRateMinus($book->getRateMinus() + 1);
                }

                $this->em->persist($book);
                $this->em->persist($resultVoted);
            }

            $this->em->flush();
        }


        return $this->renderResponse($blockContext->getTemplate(), [
            'result'      => $resultVoted,
            'book'        => $book,
            'block'       => $block,
            'settings'    => array_merge($blockContext->getSettings(), $block->getSettings()),
        ], $response);
    }
}
