<?php

namespace CommentBundle\Controller;

use CommentBundle\Entity\Comment;
use CommentBundle\Entity\CommentVotesResult;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;

/**
 * Class CommentController
 */
class CommentController extends Controller
{
    /**
     * @param Request $request
     *
     * @return Response
     *
     * @Cache(maxage=60, public=true)
     */
    public function listAction(Request $request)
    {
        $page = $request->get('page') ? " | " . $this->get('translator')->trans('frontend.page', [], 'AppBundle') .$request->get('page', 1) : null;
        $pageDesc = $request->get('page') ? $this->get('translator')->trans('frontend.page', [], 'AppBundle') . $request->get('page', 1) . " |" : null;
        $breadcrumb = $this->get('app.breadcrumb');
        $breadcrumb->addBreadcrumb(['title' => 'Відгуки']);

        $this->get('app.seo.updater')->doMagic(null, [
            'title' => $this->get('translator')->trans('frontend.meta.meta_title_reviews', [], 'AppBundle').$page,
            'description' => $pageDesc . $this->get('translator')->trans('frontend.meta.meta_description_reviews', [], 'AppBundle'),
            'og' => [
                'og:type' => 'website',
                'og:url' => $request->getSchemeAndHttpHost(),
            ],
        ]);

        return $this->render('CommentBundle::last_comments_list.html.twig');
    }

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @Cache(maxage=60, public=true)
     */
    public function swapBookAction(Request $request)
    {
        $page = $request->get('page') ? " | Страница {$request->get('page', 1)}" : null;
        $pageDesc = $request->get('page') ? "Страница {$request->get('page', 1)} |" : null;
        $breadcrumb = $this->get('app.breadcrumb');
        $breadcrumb->addBreadcrumb(['title' => 'Обмен книгами']);

        $this->get('app.seo.updater')->doMagic(null, [
            'title' => 'Обмен книгами | TopBook.com.ua - скачать книги бесплатно и без регистрации в fb2, epub, pdf, txt форматах'.$page,
            'description' => "{$pageDesc} Чтобы попросить книгу, укажите название книги и автора.",
            'keywords' => 'название, книги, автора, без регистрации, попросить, топбук',
            'og' => [
                'og:url' => $request->getSchemeAndHttpHost(),
            ],
        ]);

        return $this->render('CommentBundle::swap_list.html.twig');
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function commentVoteAction(Request $request)
    {
        $commentId = $request->get('commentId');
        if ($commentId) {
            $em = $this->get('doctrine.orm.entity_manager');
            $repository = $em->getRepository(Comment::class);
            $comment = $repository->find((int) $commentId);
            if ($comment) {
                if ($request->isXmlHttpRequest() && $request->getMethod() === 'POST') {
                    $ip = ip2long($request->server->get('REMOTE_ADDR'));
                    $vote = (bool) $request->request->get('vote');
                    $resultVoted = $em->getRepository(CommentVotesResult::class)
                        ->findOneBy(['ip' => $ip, 'comment' => $comment]);
                    if (!$resultVoted) {
                        if (true === $vote) {
                            $comment->increaseVote();
                        } else {
                            $comment->decreaseVote();
                        }

                        $resultVoted = new CommentVotesResult();
                        $resultVoted->setComment($comment);
                        $resultVoted->setIp($ip);
                        $resultVoted->setResultVote($vote);

                        $em->persist($comment);
                        $em->persist($resultVoted);
                        $em->flush();

                        return new JsonResponse([
                            'count' => $comment->getRating(),
                            'message' => 'Вы проголосовали за комментарий',
                            'type' => 'success',
                        ]);
                    } else {
                        $message = 'Вы изменили свое решение по этому комментарию';
                        $type = 'success';
                        if (true === $vote && false === $resultVoted->getResultVote()) {
                            $resultVoted->setResultVote($vote);
                            $comment->increaseVote();
                            $comment->increaseVote();
                        } elseif (false === $vote && true === $resultVoted->getResultVote()) {
                            $resultVoted->setResultVote($vote);
                            $comment->decreaseVote();
                            $comment->decreaseVote();
                        } else {
                            $message = 'Вы уже голосовали за этот комментарий';
                            $type = 'error';
                        }

                        $em->persist($comment);
                        $em->persist($resultVoted);
                        $em->flush();

                        return new JsonResponse([
                            'count' => $comment->getRating(),
                            'message' => $message,
                            'type' => $type,
                        ]);
                    }
                }
            }
        }

        return new JsonResponse();
    }
}