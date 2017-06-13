<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use AppBundle\Forms;
use AppBundle\Service\UserManager;
use AppBundle\Service\PollManager;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(
        Request $request,
        UserManager $userManager,
        PollManager $pollManager
    ) {
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
            'polls' => $pollManager->getPollsAsUser($userManager->getUserId()),
            'user' => $request->getSession()->get("user")
        ]);
    }

    /**
     * @Route("/registration", name="registration")
     */
    public function registrationAction(
        Request $request,
        UserManager $userManager
    ) {
        if (!$userManager->isGuest()) {
            return $this->redirectToRoute("homepage");
        }

        $form = $this->createForm(Forms\Registration::class);
        $form->handleRequest($request);

        if ($request->getMethod() == 'POST' &&
            $form->isSubmitted() &&
            $form->isValid()
        ) {
            $userManager->createUser($form->getData());
            return $this->redirectToRoute("homepage");
        }

        return $this->render('default/register.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/login", name="login")
     */
    public function loginAction(
        Request $request,
        userManager $userManager
    ) {
        if (!$userManager->isGuest()) {
            return $this->redirectToRoute("homepage");
        }

        $form = $this->createForm(Forms\Login::class);
        $form->handleRequest($request);

        if ($request->getMethod() == 'POST' &&
            $form->isSubmitted() &&
            $form->isValid() &&
            $userManager->loginUser($form->getData())
        ) {
            return $this->redirectToRoute("homepage");
        }

        return $this->render("default/login.html.twig", [
            "form" => $form->createView()
        ]);
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logoutAction(
        UserManager $userManager
    ) {
        $userManager->logoutUser();

        return $this->redirectToRoute("homepage");
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
