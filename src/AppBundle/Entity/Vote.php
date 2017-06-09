<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="vote",
 *     indexes=@ORM\Index(
 *         name="poll_id_index",
 *         columns="poll_id"
 *     )
 * )
 */
class Vote
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
     * @ORM\Column(type="integer", nullable=false)
     */
    private $poll_id;

    /**
     * @ORM\Column(type="smallint", nullable=false)
     */
    private $vote;

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
     * @return Vote
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
     * Set pollId
     *
     * @param integer $pollId
     *
     * @return Vote
     */
    public function setPollId($pollId)
    {
        $this->poll_id = $pollId;

        return $this;
    }

    /**
     * Get pollId
     *
     * @return integer
     */
    public function getPollId()
    {
        return $this->poll_id;
    }

    /**
     * Set vote
     *
     * @param integer $vote
     *
     * @return Vote
     */
    public function setVote($vote)
    {
        $this->vote = $vote;

        return $this;
    }

    /**
     * Get vote
     *
     * @return integer
     */
    public function getVote()
    {
        return $this->vote;
    }

    /**
     * Vote filter
     *
     * @return boolean True if item votes for requested vote item
     */
    public function filterVotes($vote)
    {
        return function ($item) use ($vote) {
            return $item->getVote() === $vote;
        };
    }

    /**
     * User filter
     *
     * @return boolean True if vote came from the requested user
     */
    public function filterUsers($userId)
    {
        return function ($item) use ($userId) {
            return $item->getUserId() === $userId;
        };
    }
}
