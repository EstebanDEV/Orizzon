<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Security;

class SecurityController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function login(Request $request, AuthenticationUtils $authUtils)
    {
        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute("home");
        }
       
        // get the login error if there is one
        $error = $authUtils->getLastAuthenticationError();

        $registerMail = "";
        unset($_SESSION['user']['type']);

        if (isset($_SESSION['register_mail'])) {
            $registerMail = $_SESSION['register_mail'];
        }

        return $this->render('site/index.html.twig', array(
            'register_mail' => $registerMail,
            'error'         => $error,
        ));
    }

    /**
     * @Route("/check_last_login", name="check_last_login")
     */
    public function checkLastLogin(Request $request, ?UserInterface $user)
    {
        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            
            $user->setLastLogin(new \DateTime());
            $user->setIpLastLogin($_SERVER["REMOTE_ADDR"]);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
    
            return $this->redirectToRoute("home");
        }

        return $this->redirectToRoute("home");
    }
}
