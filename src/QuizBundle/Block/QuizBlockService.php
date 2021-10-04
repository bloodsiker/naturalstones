<?php

namespace QuizBundle\Block;

use Doctrine\ORM\EntityManager;
use QuizBundle\Entity\Quiz;
use QuizBundle\Entity\QuizAnswer;
use QuizBundle\Entity\QuizResult;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;

use Sonata\CoreBundle\Model\Metadata;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Block\Service\AbstractAdminBlockService;

/**
 * Class QuizBlockService
 */
class QuizBlockService extends AbstractAdminBlockService
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var RequestStack
     */
    private $request;

    /**
     * QuizListBlockService constructor.
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
        $this->request  = $request;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureSettings(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'template' => 'QuizBundle:Block:quiz_item.html.twig',
        ]);
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
            'QuizBundle',
            ['class' => 'fa fa-question-circle']
        );
    }

    /**
     * @param BlockContextInterface $blockContext
     * @param Response|null         $response
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        $block = $blockContext->getBlock();
        if (!$block->getEnabled()) {
            return new Response();
        }

        $request = $this->request->getCurrentRequest();

        if ($request->isXmlHttpRequest()) {
            $quizId = $request->request->get('quiz');
            $quiz = $this->em
                ->getRepository(Quiz::class)
                ->find((int) $quizId)
            ;
        } else {
            $quiz = $this->em->getRepository(Quiz::class)->findOneBy(['isActive' => true], ['id' => 'DESC']);
        }

        if (!$quiz) {
            return new Response();
        }

        $ip = ip2long($request->server->get('REMOTE_ADDR'));

        $answers = array_map(
            'intval',
            explode(',', $request->request->get('answers', null))
        );

        $result = $this->em
            ->getRepository(QuizResult::class)
            ->findOneBy([
                'ip'    => $ip,
                'quiz'  => $quiz,
            ])
        ;

        $voted = $result ? true : false;

        if ($request->isXmlHttpRequest() && $request->getMethod() === 'POST' && $answers) {

            if (!$result) {
                $quiz->setVotedCount($quiz->getVotedCount()+1);
                $this->em->persist($quiz);

                $quizAnswersCounterSum = 0;
                $quizAnswersCounter = [];
                foreach ($quiz->getQuizHasAnswer() as $item) {
                    /** @var QuizAnswer $answer */
                    $answer = $item->getAnswer();
                    $quizAnswersCounter[$answer->getId()] = $answer->getCounter();
                    $quizAnswersCounterSum += $answer->getCounter();

                    if (in_array($answer->getId(), $answers)) {
                        ++$quizAnswersCounter[$answer->getId()];
                        ++$quizAnswersCounterSum;
                    }
                }

                foreach ($quiz->getQuizHasAnswer() as $item) {
                    $answer = $item->getAnswer();

                    $answer->setCounter($quizAnswersCounter[$answer->getId()]);
                    $answer->setPercent($quizAnswersCounter[$answer->getId()]*100/$quizAnswersCounterSum);

                    $this->em->persist($answer);

                    if (in_array($answer->getId(), $answers)) {
                        $result = new QuizResult();
                        $result->setIp($ip);
                        $result->setQuiz($quiz);
                        $result->setAnswer($answer);

                        $this->em->persist($result);
                    }
                }

                $this->em->flush();
            }

            $voted = true;
        }

        $quizAnswers = $quiz->getQuizHasAnswer()->getValues();
        usort($quizAnswers, function ($a, $b) {
            return -1 * ($a->getAnswer()->getPercent() <=> $b->getAnswer()->getPercent());
        });

        return $this->renderResponse($blockContext->getTemplate(), [
            'settings'    => array_merge($blockContext->getSettings(), $block->getSettings()),
            'block'       => $block,
            'quiz'        => $quiz,
            'voted'       => $voted,
            'quizAnswers' => $quizAnswers,
            'blockID'     => 'quiz_'.$quiz->getId(),
        ], $response);
    }
}
