<?php

namespace QuizBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Quiz
 *
 * @ORM\Entity(repositoryClass="QuizBundle\Entity\QuizRepository")
 * @ORM\Table(name="quiz_quiz")
 * @ORM\HasLifecycleCallbacks
 */
class Quiz
{
    /**
     * Const modes
     */
    const VOTE_SINGLE_MODE      = 1;
    const VOTE_MULTIPLE_MODE    = 2;

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
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    protected $isActive;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $createdAt;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=false, options={"unsigned"=true, "default": 0})
     */
    protected $votedCount;

    /**
     * @var bool
     *
     * @ORM\Column(type="smallint", columnDefinition="TINYINT(2) UNSIGNED DEFAULT 0 NOT NULL")
     */
    protected $votedType;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="QuizBundle\Entity\QuizHasAnswer",
     *     mappedBy="quiz", cascade={"all"}, orphanRemoval=true
     * )
     * @ORM\OrderBy({"orderNum" = "ASC"})
     */
    protected $quizHasAnswer;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->votedCount       = 0;
        $this->isActive         = true;
        $this->votedType        = self::VOTE_SINGLE_MODE;
        $this->createdAt        = new \DateTime('now');
        $this->quizHasAnswer    = new ArrayCollection();
    }

    /**
     * "String" representation of class
     *
     * @return string
     */
    public function __toString()
    {
        $length = 150;
        $title = (string) $this->getTitle();

        if (mb_strlen($title) >= $length) {
            return strtok(wordwrap($title, $length, "...\n"), "\n");
        }

        return $title;
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
     * @return array
     */
    public static function getVotedMode()
    {
        return [
            self::VOTE_SINGLE_MODE      => 'single',
            self::VOTE_MULTIPLE_MODE    => 'multiple',
        ];
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
     * Set isActive.
     *
     * @param bool $isActive
     *
     * @return Quiz
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive.
     *
     * @return bool
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return Quiz
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt.
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set votedType.
     *
     * @param int $votedType
     *
     * @return Quiz
     */
    public function setVotedType($votedType)
    {
        $this->votedType = $votedType;

        return $this;
    }

    /**
     * Get votedType.
     *
     * @return int
     */
    public function getVotedType()
    {
        return $this->votedType;
    }

    /**
     * Set votedCount.
     *
     * @param int $votedCount
     *
     * @return Quiz
     */
    public function setVotedCount($votedCount)
    {
        $this->votedCount = $votedCount;

        return $this;
    }

    /**
     * Get votedCount.
     *
     * @return int
     */
    public function getVotedCount()
    {
        return $this->votedCount;
    }

    /**
     * Add quizHasAnswer.
     *
     * @param \QuizBundle\Entity\QuizHasAnswer $quizHasAnswer
     *
     * @return Quiz
     */
    public function addQuizHasAnswer(\QuizBundle\Entity\QuizHasAnswer $quizHasAnswer)
    {
        $quizHasAnswer->setQuiz($this);
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
