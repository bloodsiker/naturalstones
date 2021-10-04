<?php

namespace QuizBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;

/**
 * Class QuizHasAnswer
 *
 * @ORM\Entity
 * @ORM\Table(name="quiz_quiz_has_answer")
 */
class QuizHasAnswer
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer", options={"unsigned"=true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var \QuizBundle\Entity\Quiz
     *
     * @ORM\ManyToOne(targetEntity="QuizBundle\Entity\Quiz", inversedBy="quizHasAnswer")
     * @ORM\JoinColumn(name="quiz_id", referencedColumnName="id", nullable=false)
     */
    protected $quiz;

    /**
     * @var \QuizBundle\Entity\QuizAnswer
     *
     * @ORM\ManyToOne(targetEntity="QuizBundle\Entity\QuizAnswer", inversedBy="quizHasAnswer")
     * @ORM\JoinColumn(name="answer_id", referencedColumnName="id", nullable=false)
     */
    protected $answer;

    /**
     * @var int
     *
     * @ORM\Column(name="order_num", type="integer", nullable=false, options={"default": 1})
     */
    protected $orderNum;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set orderNum.
     *
     * @param int $orderNum
     *
     * @return QuizHasAnswer
     */
    public function setOrderNum($orderNum)
    {
        $this->orderNum = $orderNum;

        return $this;
    }

    /**
     * Get orderNum.
     *
     * @return int
     */
    public function getOrderNum()
    {
        return $this->orderNum;
    }

    /**
     * Set quiz.
     *
     * @param \QuizBundle\Entity\Quiz $quiz
     *
     * @return QuizHasAnswer
     */
    public function setQuiz(\QuizBundle\Entity\Quiz $quiz)
    {
        $this->quiz = $quiz;

        return $this;
    }

    /**
     * Get quiz.
     *
     * @return \QuizBundle\Entity\Quiz
     */
    public function getQuiz()
    {
        return $this->quiz;
    }

    /**
     * Set answer.
     *
     * @param \QuizBundle\Entity\QuizAnswer $answer
     *
     * @return QuizHasAnswer
     */
    public function setAnswer(\QuizBundle\Entity\QuizAnswer $answer)
    {
        $this->answer = $answer;

        return $this;
    }

    /**
     * Get answer.
     *
     * @return \QuizBundle\Entity\QuizAnswer
     */
    public function getAnswer()
    {
        return $this->answer;
    }
}
