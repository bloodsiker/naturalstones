<?php

namespace QuizBundle\Command;

use ArticleBundle\Entity\Article;
use ArticleBundle\Listener\ArticleListener;
use QuizBundle\Entity\Quiz;
use QuizBundle\Entity\QuizAnswer;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

/**
 * Class FixResultsPercentsCommand
 */
class FixResultsPercentsCommand extends ContainerAwareCommand
{
    /**
     * Doctrine manager
     */
    private $em;

    /**
     * Configure console command arguments
     */
    protected function configure()
    {
        $this
            ->setName('quiz:results:fix-percents')
            ->setDescription('Fix quiz results percents')
        ;
    }

    /**
     * Command execute
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return bool
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->em = $this->getContainer()->get('doctrine')->getManager();

        $quizList = $this->em->getRepository('QuizBundle:Quiz')->findAll();

        /** @var Quiz $quiz */
        foreach ($quizList as $quiz) {
            $quizAnswersCounterSum = 0;
            $quizAnswersCounter = [];

            foreach ($quiz->getQuizHasAnswer() as $item) {
                /** @var QuizAnswer $answer */
                $answer = $item->getAnswer();
                $quizAnswersCounter[$answer->getId()] = $answer->getCounter();
                $quizAnswersCounterSum += $answer->getCounter();
            }

            foreach ($quiz->getQuizHasAnswer() as $item) {
                /** @var QuizAnswer $answer */
                $answer = $item->getAnswer();
                $answer->setCounter($quizAnswersCounter[$answer->getId()]);

                if ($quizAnswersCounterSum > 0) {
                    echo $quiz->getId().' ('.$answer->getId().'): '.$quizAnswersCounter[$answer->getId()].' / '.$quizAnswersCounterSum."\n";
                    $answer->setPercent($quizAnswersCounter[$answer->getId()] * 100 / $quizAnswersCounterSum);

                    $this->em->persist($answer);
                }
            }

            $this->getContainer()->get('new_media.varnish.invalidation.extension')->commandHandle($quiz);
        }

        return true;
    }
}
