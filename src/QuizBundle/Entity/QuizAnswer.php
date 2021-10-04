<?php

namespace QuizBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Class QuizAnswer
 *
 * @ORM\Entity
 * @ORM\Table(name="quiz_quiz_answer")
 * @ORM\HasLifecycleCallbacks
 *
 * @Vich\Uploadable
 */
class QuizAnswer
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
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $title;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $link;

    /**
     * @var float
     *
     * @ORM\Column(type="decimal", precision=6, scale=2, nullable=false, options={"default": 0.00})
     */
    protected $percent;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=false, options={"unsigned"=true, "default": 0})
     */
    protected $counter;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="\QuizBundle\Entity\QuizHasAnswer", mappedBy="answer", cascade={"all"}, orphanRemoval=true)
     * @ORM\OrderBy({"orderNum" = "ASC"})
     */
    protected $quizHasAnswer;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->percent  = 0;
        $this->counter  = 0;
    }

    /**
     * "String" representation of class
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getTitle();
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
     * Set title.
     *
     * @param string|null $title
     *
     * @return $this
     */
    public function setTitle($title = null)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title.
     *
     * @return string|null
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set percent.
     *
     * @param string $percent
     *
     * @return QuizAnswer
     */
    public function setPercent($percent)
    {
        $this->percent = $percent;

        return $this;
    }

    /**
     * Get percent.
     *
     * @return string
     */
    public function getPercent()
    {
        return $this->percent;
    }

    /**
     * Set link.
     *
     * @param string|null $link
     *
     * @return QuizAnswer
     */
    public function setLink($link = null)
    {
        $this->link = $link;

        return $this;
    }

    /**
     * Get link.
     *
     * @return string|null
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * Set counter.
     *
     * @param int $counter
     *
     * @return QuizAnswer
     */
    public function setCounter($counter)
    {
        $this->counter = $counter;

        return $this;
    }

    /**
     * Get counter.
     *
     * @return int
     */
    public function getCounter()
    {
        return $this->counter;
    }

    /**
     * Add quizHasAnswer.
     *
     * @param \QuizBundle\Entity\QuizHasAnswer $quizHasAnswer
     *
     * @return QuizAnswer
     */
    public function addQuizHasAnswer(\QuizBundle\Entity\QuizHasAnswer $quizHasAnswer)
    {
        $this->quizHasAnswer[] = $quizHasAnswer;

        return $this;
    }

    /**
     * Remove quizHasAnswer.
     *
     * @param \QuizBundle\Entity\QuizHasAnswer $quizHasAnswer
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeQuizHasAnswer(\QuizBundle\Entity\QuizHasAnswer $quizHasAnswer)
    {
        return $this->quizHasAnswer->removeElement($quizHasAnswer);
    }

    /**
     * Get quizHasAnswer.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getQuizHasAnswer()
    {
        return $this->quizHasAnswer;
    }
}
