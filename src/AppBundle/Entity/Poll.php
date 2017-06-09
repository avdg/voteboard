<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="poll")
 */
class Poll
{
    /**
     * @ORM\Column(type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private $user_id;

    /**
     * @ORM\Column(type="string", length=140, nullable=false)
     */
    private $question;

    /**
     * @ORM\Column(type="string", length=32, nullable=false)
     */
    private $answer_1;

    /**
     * @ORM\Column(type="string", length=32, nullable=false)
     */
    private $answer_2;

    /**
     * @ORM\Column(type="string", length=32, nullable=true)
     */
    private $answer_3;

    /**
     * @ORM\Column(type="string", length=32, nullable=true)
     */
    private $answer_4;

    /**
     * @ORM\Column(type="string", length=32, nullable=true)
     */
    private $answer_5;

    /**
     * @ORM\Column(type="datetime")
     */
    private $posted_at;

    /**
     * @ORM\Column(type="boolean", nullable=false, options={"default": false})
     */
    private $closed = false;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->posted_at = new \DateTime();
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
     * Set userId
     *
     * @param integer $userId
     *
     * @return Poll
     */
    public function setUserId($userId)
    {
        $this->user_id = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return integer
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * Set question
     *
     * @param string $question
     *
     * @return Poll
     */
    public function setQuestion($question)
    {
        $this->question = $question;

        return $this;
    }

    /**
     * Get question
     *
     * @return string
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * Set answer1
     *
     * @param string $answer1
     *
     * @return Poll
     */
    public function setAnswer1($answer1)
    {
        $this->answer_1 = $answer1;

        return $this;
    }

    /**
     * Get answer1
     *
     * @return string
     */
    public function getAnswer1()
    {
        return $this->answer_1;
    }

    /**
     * Set answer2
     *
     * @param string $answer2
     *
     * @return Poll
     */
    public function setAnswer2($answer2)
    {
        $this->answer_2 = $answer2;

        return $this;
    }

    /**
     * Get answer2
     *
     * @return string
     */
    public function getAnswer2()
    {
        return $this->answer_2;
    }

    /**
     * Set answer3
     *
     * @param string $answer3
     *
     * @return Poll
     */
    public function setAnswer3($answer3)
    {
        $this->answer_3 = $answer3;

        return $this;
    }

    /**
     * Get answer3
     *
     * @return string
     */
    public function getAnswer3()
    {
        return $this->answer_3;
    }

    /**
     * Set answer4
     *
     * @param string $answer4
     *
     * @return Poll
     */
    public function setAnswer4($answer4)
    {
        $this->answer_4 = $answer4;

        return $this;
    }

    /**
     * Get answer4
     *
     * @return string
     */
    public function getAnswer4()
    {
        return $this->answer_4;
    }

    /**
     * Set answer5
     *
     * @param string $answer5
     *
     * @return Poll
     */
    public function setAnswer5($answer5)
    {
        $this->answer_5 = $answer5;

        return $this;
    }

    /**
     * Get answer5
     *
     * @return string
     */
    public function getAnswer5()
    {
        return $this->answer_5;
    }

    /**
     * Set postedAt
     *
     * @param \DateTime $postedAt
     *
     * @return Poll
     */
    public function setPostedAt($postedAt)
    {
        $this->posted_at = $postedAt;

        return $this;
    }

    /**
     * Get postedAt
     *
     * @return \DateTime
     */
    public function getPostedAt()
    {
        return $this->posted_at;
    }

    /**
     * Set closed
     *
     * @param boolean $closed
     *
     * @return Poll
     */
    public function setClosed($closed)
    {
        $this->closed = $closed;

        return $this;
    }

    /**
     * Get closed
     *
     * @return boolean
     */
    public function getClosed()
    {
        return $this->closed;
    }
}
