<?php

namespace AppBundle\Service;

use Doctrine\ORM\EntityManagerInterface;

class PollManager
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
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

    public function getPollsAsUser($userId)
    {
        $polls = [];
        $pollEntities = $this
            ->em
            ->getRepository("\AppBundle\Entity\Poll")
            ->findBy(array(), array('posted_at' => 'DESC'));

        foreach ($pollEntities as $poll) {
            $votes = $this
                ->em
                ->getRepository("\AppBundle\Entity\Vote")
                ->findBy(["poll_id" => $poll->getId()]);

            $answers = [];

            for ($i = 1; $i <= 5; $i++) {
                if ($poll->{"getAnswer" . $i}() !== null) {
                    $answerVotes = array_filter(
                        $votes,
                        self::filterVotes($i)
                    );
                    $selected = array_filter(
                        $answerVotes,
                        self::filterUsers($userId)
                    );
                    $answers[] = [
                        "answer" => $poll->{"getAnswer" . $i}(),
                        "votes" => sizeof($answerVotes),
                        "id" => $i,
                        "selected" => sizeof($selected) > 0
                    ];
                }
            }

            $polls[] = [
                "id" => $poll->getId(),
                "question" => $poll->getQuestion(),
                "answers" => $answers
            ];
        }

        return $polls;
    }

    public function createPoll($data, $userId)
    {
        $poll = new \AppBundle\Entity\Poll();
        $poll->setQuestion($data["question"]);
        $poll->setAnswer1($data["answer1"]);
        $poll->setAnswer2($data["answer2"]);
        $poll->setAnswer3($data["answer3"]);
        $poll->setAnswer4($data["answer4"]);
        $poll->setAnswer5($data["answer5"]);
        $poll->setUserId($userId);

        $this->em->persist($poll);
        $this->em->flush();
    }

    public function vote($userId, $poll, $item)
    {
        $pollItem = $this
            ->em
            ->getRepository("\AppBundle\Entity\Poll")
            ->find($poll);

        if ($pollItem === null) {
            return;
        }

        $vote = $this
            ->em
            ->getRepository("\AppBundle\Entity\Vote")
            ->findOneBy([
                "poll_id" => $poll,
                "user_id" => $userId
            ]);

        // Create vote if user didn't vote yet
        if ($vote === null) {
            $vote = new \AppBundle\Entity\Vote();
            $vote->setUserId($userId);
            $vote->setPollId($poll);
        }

        $vote->setVote(intval($item));

        $this->em->persist($vote);
        $this->em->flush();
    }
}
