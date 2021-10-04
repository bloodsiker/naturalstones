<?php

namespace QuizBundle\Controller;

use AppBundle\Traits\PaginationTrait;
use QuizBundle\Entity\Quiz;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use NewMedia\VarnishCacheBundle\Annotation\VarnishTag;

/**
 * Class QuizController
 */
class QuizController extends Controller
{
    use PaginationTrait;

    const QUIZ_404 = 'Quiz doesn\'t exist';

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @throws \Throwable
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     *
     * @VarnishTag(tags={
     *     {"name":"quiz.list"}
     * })
     *
     * @Cache(expires="+1 day", maxage=86400, smaxage=86400, public=true)
     */
    public function listAction(Request $request)
    {
        $breadcrumbsService = $this->get('article.breadcrumb.handler');
        $breadcrumbsService->addBreadcrumb($this->get('translator')->trans('front.quiz_online', [], 'QuizBundle'));
        $listPage = $this->getPageNumber($request->get('page'));
        if ($listPage) {
            $breadcrumbsService->addBreadcrumb([
                $request->getLocale() => $this->get('translator')->trans('front.page', [], 'QuizBundle').$listPage,
            ]);
        }

        $this->get('app.seo.updater')->doMagic(null, [
            'title' => $this->get('translator')->trans('front.meta.meta_title_text', [], 'QuizBundle').' | '.$this->get('translator')->trans('app.frontend.meta.sitename', [], 'AppBundle'),
            'description' => $this->get('translator')->trans('front.meta.meta_description_text', [], 'QuizBundle'),
            'keywords' => $this->get('translator')->trans('front.meta.meta_keywords_text', [], 'QuizBundle'),
        ]);

        return $this->render('QuizBundle::quiz_list_page.html.twig');
    }

    /**
     * @param int     $id
     * @param Request $request
     *
     * @return Response
     *
     * @throws \Exception
     *
     * @VarnishTag(tags={
     *     {"name":"quiz.show.", "request_attribute": "id"}
     * })
     *
     * @Cache(expires="+1 day", maxage=86400, smaxage=86400, public=true)
     */
    public function viewAction($id, Request $request)
    {
        $breadcrumbsService = $this->get('article.breadcrumb.handler');
        $breadcrumbsService->addBreadcrumb([
            'href' => $this->get('router')->generate('quiz_list', ['page' => null]),
            $request->getLocale() => $this->get('translator')->trans('front.quiz_online', [], 'QuizBundle'),
        ]);

        $repo = $this->container
            ->get('doctrine')
            ->getRepository(Quiz::class)
        ;

        $now = new \DateTime('now');
        $quiz = $repo->find((int) $id);
        if (!$quiz || !$quiz->getIsActive() || $quiz->getStartedAt() > $now) {
            throw $this->createNotFoundException(self::QUIZ_404);
        }

        $breadcrumbsService->addBreadcrumb([$request->getLocale() => $quiz->getTitle()]);


        $this->get('app.seo.updater')->doMagic(null, [
            'title' => $quiz->getTitle().' '.$this->get('translator')->trans('front.meta.meta_title_text_quiz', [], 'QuizBundle'),
            'description' => $this->get('translator')->trans('front.meta.meta_description_text_quiz', ['%QUIZ%' => $quiz->getTitle()], 'QuizBundle'),
            'keywords' => $quiz->getTitle().' '.$this->get('translator')->trans('front.meta.meta_keywords_text', [], 'QuizBundle'),
        ]);

        return $this->render('QuizBundle::quiz_view_page.html.twig', [
            'quiz'  => $quiz,
        ]);
    }

    /**
     * @param int $id
     *
     * @return Response
     *
     * @throws \Exception
     *
     * @VarnishTag(tags={
     *     {"name":"quiz.show.", "request_attribute": "id"}
     * })
     *
     * @Cache(expires="+1 day", maxage=86400, smaxage=86400, public=true)
     */
    public function getAction($id)
    {
        $now = new \DateTime('now');
        $quiz = $this->getQuiz($id);

        return $this->render('QuizBundle:Block:quiz_ajax_item.html.twig', [
            'quiz'  => $quiz->getId(),
            'voted' => $quiz->getFinishedAt() > $now ? false : true,
        ]);
    }

    /**
     * @param int $id
     *
     * @return Response
     *
     * @throws \Exception
     *
     * @VarnishTag(tags={
     *     {"name":"quiz.", "request_attribute": "id"}
     * })
     *
     * @Cache(expires="+1 day", maxage=86400, smaxage=86400, public=true)
     */
    public function getVotedAction($id)
    {
        $quiz = $this->getQuiz($id);

        return $this->render('QuizBundle:Block:quiz_ajax_item.html.twig', [
            'quiz'  => $quiz->getId(),
            'voted' => true,
        ]);
    }

    /**
     * @param int $id
     *
     * @return Quiz
     */
    private function getQuiz($id)
    {
        if (!$id) {
            throw $this->createNotFoundException(self::QUIZ_404);
        }

        $repo = $this->container
            ->get('doctrine')
            ->getRepository(Quiz::class);

        $now = new \DateTime('now');
        $quiz = $repo->find((int) $id);
        if (!$quiz || !$quiz->getIsActive() || $quiz->getStartedAt() > $now) {
            throw $this->createNotFoundException(self::QUIZ_404);
        }

        return $quiz;
    }
}