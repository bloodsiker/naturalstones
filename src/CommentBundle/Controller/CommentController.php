<?php

namespace CommentBundle\Controller;

use AppBundle\Controller\BaseController;
use AppBundle\Services\BreadcrumbService;
use AppBundle\Services\SeoUpdater;
use CommentBundle\Entity\Comment;
use CommentBundle\Entity\CommentVotesResult;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\Cache;
use Symfony\Contracts\Translation\TranslatorInterface;

class CommentController extends BaseController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly TranslatorInterface $translator,
        private readonly BreadcrumbService $breadcrumb,
        private readonly SeoUpdater $seoUpdater,
    ) {
    }

    #[Cache(maxage: 60, public: true)]
    public function listAction(Request $request): Response
    {
        $this->breadcrumb->addBreadcrumb(['title' => 'Відгуки']);

        $page = $this->pageSuffix($request);
        $pageDesc = $this->pageDescriptionPrefix($request);

        $this->seoUpdater->doMagic(null, [
            'title' => $this->translator->trans('frontend.meta.meta_title_reviews', [], 'AppBundle') . $page,
            'description' => $pageDesc . $this->translator->trans('frontend.meta.meta_description_reviews', [], 'AppBundle'),
            'og' => [
                'og:type' => 'website',
                'og:url' => $request->getSchemeAndHttpHost(),
            ],
        ]);

        return $this->render('@Comment/last_comments_list.html.twig');
    }

    public function commentVoteAction(Request $request): JsonResponse
    {
        if (!$request->isXmlHttpRequest() || !$request->isMethod('POST')) {
            return new JsonResponse();
        }

        $commentId = (int) $request->get('commentId');
        if (!$commentId) {
            return new JsonResponse();
        }

        $comment = $this->em->getRepository(Comment::class)->find($commentId);
        if (!$comment) {
            return new JsonResponse();
        }

        $ip = ip2long($request->server->get('REMOTE_ADDR'));
        $vote = (bool) $request->request->get('vote');
        $resultVoted = $this->em->getRepository(CommentVotesResult::class)
            ->findOneBy(['ip' => $ip, 'comment' => $comment]);

        if (!$resultVoted) {
            return $this->castInitialVote($comment, $vote, $ip);
        }

        return $this->changeExistingVote($comment, $resultVoted, $vote);
    }

    private function castInitialVote(Comment $comment, bool $vote, int $ip): JsonResponse
    {
        $vote ? $comment->increaseVote() : $comment->decreaseVote();

        $resultVoted = new CommentVotesResult();
        $resultVoted->setComment($comment);
        $resultVoted->setIp($ip);
        $resultVoted->setResultVote($vote);

        $this->em->persist($comment);
        $this->em->persist($resultVoted);
        $this->em->flush();

        return new JsonResponse([
            'count' => $comment->getRating(),
            'message' => 'Вы проголосовали за комментарий',
            'type' => 'success',
        ]);
    }

    private function changeExistingVote(Comment $comment, CommentVotesResult $resultVoted, bool $vote): JsonResponse
    {
        $message = 'Вы изменили свое решение по этому комментарию';
        $type = 'success';

        if ($vote && !$resultVoted->getResultVote()) {
            // negative → positive: reverse decrease, add increase
            $resultVoted->setResultVote($vote);
            $comment->increaseVote();
            $comment->increaseVote();
        } elseif (!$vote && $resultVoted->getResultVote()) {
            // positive → negative: reverse increase, add decrease
            $resultVoted->setResultVote($vote);
            $comment->decreaseVote();
            $comment->decreaseVote();
        } else {
            $message = 'Вы уже голосовали за этот комментарий';
            $type = 'error';
        }

        $this->em->persist($comment);
        $this->em->persist($resultVoted);
        $this->em->flush();

        return new JsonResponse([
            'count' => $comment->getRating(),
            'message' => $message,
            'type' => $type,
        ]);
    }

    private function pageSuffix(Request $request): string
    {
        return $request->get('page')
            ? ' | ' . $this->translator->trans('frontend.page', [], 'AppBundle') . $request->get('page', 1)
            : '';
    }

    private function pageDescriptionPrefix(Request $request): string
    {
        return $request->get('page')
            ? $this->translator->trans('frontend.page', [], 'AppBundle') . $request->get('page', 1) . ' |'
            : '';
    }
}