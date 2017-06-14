<?php

namespace AppBundle\Service;

use Doctrine\ORM\EntityManagerInterface;

class PollManager
{
    private $em;

    private $pollQuery = <<<QUERY
SELECT poll.*,
    SUM(CASE WHEN vote.vote = 1 THEN 1 ELSE 0 END) AS votes_1,
    SUM(CASE WHEN vote.vote = 2 THEN 1 ELSE 0 END) AS votes_2,
    SUM(CASE WHEN vote.vote = 3 THEN 1 ELSE 0 END) AS votes_3,
    SUM(CASE WHEN vote.vote = 4 THEN 1 ELSE 0 END) AS votes_4,
    SUM(CASE WHEN vote.vote = 5 THEN 1 ELSE 0 END) AS votes_5,
    MAX(CASE WHEN vote.user_id = :user_id THEN vote.vote ELSE 0 END) AS vote_user
FROM poll LEFT JOIN vote on poll.id = vote.poll_id
GROUP BY poll.id;
QUERY;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getPollsAsUser($userId)
    {
        $polls = [];

        $prepStat = $this->em->getConnection()->prepare($this->pollQuery);
        $prepStat->execute(["user_id" => $userId]);

        $results = $prepStat->fetchAll();

        foreach ($results as $result) {
            $answers = [];

            for ($i = 1; $i <= 5; $i++) {
                if ($result["answer_" . $i] === null) {
                    continue;
                }

                $answers[] = [
                    "answer" => $result["answer_" . $i],
                    "votes" => $result["votes_" . $i],
                    "id" => $i,
                    "selected" => $result["vote_user"] == $i
                ];
            }

            $polls[] = [
                "id" => $result["id"],
                "question" => $result["question"],
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
