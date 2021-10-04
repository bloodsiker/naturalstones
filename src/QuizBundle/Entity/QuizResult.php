<?php

namespace QuizBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class QuizResult
 *
 * @ORM\Entity
 * @ORM\Table(name="quiz_quiz_result")
 * @ORM\HasLifecycleCallbacks
 */
class QuizResult
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
     * @var int
     *
     * @ORM\ManyToOne(targetEntity="QuizBundle\Entity\Quiz")
     * @ORM\JoinColumn(name="quiz_id", referencedColumnName="id", nullable=true, onDelete="cascade")
     */
    protected $quiz;

    /**
     * @var int
     *
     * @ORM\ManyToOne(targetEntity="QuizBundle\Entity\QuizAnswer")
     * @ORM\JoinColumn(name="answer_id", referencedColumnName="id", nullable=true, onDelete="cascade")
     */
    protected $answer;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=400, nullable=true)
     */
    protected $comment;

    /**
     * @var int
     *
     * @ORM\Column(type="bigint", nullable=true, options={"unsigned"=true})
     */
    protected $ip;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $votedAt;

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        $this->votedAt = new \DateTime('now');
    }

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
     * Set comment.
     *
     * @param string $comment
     *
     * @return QuizResult
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment.
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set ip.
     *
     * @param int|null $ip
     *
     * @return QuizResult
     */
    public function setIp($ip = null)
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * Get ip.
     *
     * @return int|null
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Set votedAt.
     *
     * @param \DateTime $votedAt
     *
     * @return QuizResult
     */
    public function setVotedAt($votedAt)
    {
        $this->votedAt = $votedAt;

        return $this;
    }

    /**
     * Get votedAt.
     *
     * @return \DateTime
     */
    public function getVotedAt()
    {
        return $this->votedAt;
    }

    /**
     * Set quiz.
     *
     * @param \QuizBundle\Entity\Quiz|null $quiz
     *
     * @return QuizResult
     */
    public function setQuiz(\QuizBundle\Entity\Quiz $quiz = null)
    {
        $this->quiz = $quiz;

        return $this;
    }

    /**
     * Get quiz.
     *
     * @return \QuizBundle\Entity\Quiz|null
     */
    public function getQuiz()
    {
        return $this->quiz;
    }

    /**
     * Set answer.
     *
     * @param \QuizBundle\Entity\QuizAnswer|null $answer
     *
     * @return QuizResult
     */
    public function setAnswer(\QuizBundle\Entity\QuizAnswer $answer = null)
    {
        $this->answer = $answer;

        return $this;
    }

    /**
     * Get answer.
     *
     * @return \QuizBundle\Entity\QuizAnswer|null
     */
    public function getAnswer()
    {
        return $this->answer;
    }
}
