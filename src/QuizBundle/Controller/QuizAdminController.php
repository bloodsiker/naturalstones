<?php

namespace QuizBundle\Controller;

use AdminBundle\Controller\CRUDController as Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class QuizAdminController
 */
class QuizAdminController extends Controller
{
    /**
     * Redirect the user depend on this choice.
     *
     * @param object $object
     *
     * @return RedirectResponse
     */
    protected function redirectTo($object)
    {
        $request = $this->getRequest();
        $adminQuiz = $this->container->get('sonata.admin.quiz');
        $url = $adminQuiz->generateUrl('list');

        if ($object->getQuizHasAnswer()->first()) {
            $url = $adminQuiz->generateUrl('edit', [
                'id' => $object->getQuizHasAnswer()->first()->getQuiz()->getId(),
            ]);
        }

        if (null !== $request->get('btn_create_and_create')) {
            $params = array();
            if ($this->admin->hasActiveSubClass()) {
                $params['subclass'] = $request->get('subclass');
            }
            $url = $this->admin->generateUrl('create', $params);
        }

        return new RedirectResponse($url);
    }

    /**
     * Return url image by answer ID
     *
     * @param int $id
     *
     * @return JsonResponse
     */
    public function previewAction(int $id)
    {
        if (null === $id) {
            return;
        }

        $em = $this->getDoctrine()->getManager();
        $quizAnswer = $em->getRepository('QuizBundle:QuizAnswer')
            ->findOneBy(['id' => $id]);

        $src = null !== $quizAnswer->getImage()
            ? $this->get('liip_imagine.cache.manager')->getBrowserPath($quizAnswer->getImage(), 'admin_preview')
            : '/bundles/admin/images/preview_placeholder.png';

        return new JsonResponse(['src' => $src]);
    }
}
