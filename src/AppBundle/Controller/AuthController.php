<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use AppBundle\Forms;
use AppBundle\DependencyInjection\UserManager;

class AuthController extends Controller
{
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
}
