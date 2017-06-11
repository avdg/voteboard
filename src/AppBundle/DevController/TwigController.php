<?php

namespace AppBundle\DevController;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use AppBundle\Forms;

class TwigController extends Controller
{
    /**
     * @Route("/_twig/home", name="homepagedemo")
     */
    public function homeAction(Request $request)
    {
        $poll = json_decode(file_get_contents(__DIR__ . "/../Resources/Data/ExamplePoll.json"));

        return $this->render('default/index.html.twig', [
            "polls" => $poll,
            "user" => null
        ]);
    }

    /**
     * @Route("/_twig/registration", name="registrationdemo")
     */
    public function registerAction(Request $request)
    {
        $form = $this->createForm(Forms\Registration::class);
        $form->handleRequest($request);

        return $this->render("default/register.html.twig", [
            "form" => $form->createView()
        ]);
    }

    /**
     * @Route("/_twig/login", name="logindemo")
     */
    public function loginAction(Request $request)
    {
        $form = $this->createForm(Forms\Login::class);
        $form->handleRequest($request);

        return $this->render("default/login.html.twig", [
            "form" => $form->createView()
        ]);
    }

    /**
     * @Route("/_twig/create", name="createpolldemo")
     */
    public function createPollAction(Request $request)
    {
        $form = $this->createForm(Forms\Poll::class);
        $form->handleRequest($request);

        return $this->render("default/poll.html.twig", [
            "form" => $form->createView()
        ]);
    }
}
