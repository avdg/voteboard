<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use AppBundle\Forms;
use AppBundle\DependencyInjection\UserManager;
use AppBundle\DependencyInjection\PollManager;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(
        UserManager $userManager,
        PollManager $pollManager
    ) {
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
            'polls' => $pollManager->getPollsAsUser($userManager->getUserId()),
            'user' => $userManager->getCurrentUser()
        ]);
    }

    /**
     * @Route("/poll/{pollId}", name="showPoll")
     */
    public function showPollAction(
        UserManager $userManager,
        PollManager $pollManager,
        $pollId
    ) {
        $poll = $pollManager->getSinglePollAsUser($pollId, $userManager->getUserId());

        if (sizeof($poll) === 0) {
            throw $this->createNotFoundException("Poll does not exist");
        }

        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
            'polls' => $poll,
            'user' => $userManager->getCurrentUser()
        ]);
    }

    /**
     * @Route("/create", name="createpoll")
     */
    public function createPollAction(
        Request $request,
        UserManager $userManager,
        PollManager $pollManager
    ) {
        if ($userManager->isGuest()) {
            return $this->redirectToRoute("homepage");
        }

        $form = $this->createForm(Forms\Poll::class);
        $form->handleRequest($request);

        if ($request->getMethod() == 'POST' &&
            $form->isSubmitted() &&
            $form->isValid()
        ) {
            $pollManager->createPoll($form->getData(), $userManager->getUserId());
            return $this->redirectToRoute("homepage");
        }

        return $this->render("default/poll.html.twig", [
            "form" => $form->createView()
        ]);
    }

    /**
     * @Route("/vote/{poll}/{item}", defaults = {"poll": null, "item": null})
     */
    public function voteAction(
        Request $request,
        userManager $userManager,
        PollManager $pollManager,
        $poll,
        $item
    ) {
        if (!$userManager->isGuest() &&
            $this->isCsrfTokenValid('_token', $request->query->get("_token", null)) &&
            ctype_digit($poll) &&
            ctype_digit($item)
        ) {
            $pollManager->vote($userManager->getUserId(), $poll, $item);
        }

        return $this->redirectToRoute("homepage");
    }
}
