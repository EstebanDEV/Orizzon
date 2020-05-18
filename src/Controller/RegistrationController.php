<?php

namespace App\Controller;
 
use App\Form\UserType;
use App\Entity\User;
use App\Entity\Society; 
use App\Entity\Individual;
use App\Entity\Township;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
 
class RegistrationController extends AbstractController
{
    /**
     * @Route("/register/step1", name="user_registration_before")
     */
    public function registerBefore(Request $request)
    {
        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute("home");
        }

        if (isset($_POST['type'])) {
            if ($_POST['type'] == "0" || $_POST['type'] == "1") {
                $_SESSION['user']['type'] = $_POST['type'];
                return $this->redirectToRoute('user_registration');
            } else {
                $this->addFlash('error', 'Erreur! Merci de recommencer votre sélection.');
            }
        }
        
        return $this->render('registration/before.html.twig');
    }

    /**
     * @Route("/register/step2", name="user_registration")
     */
    public function registerAction(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {  
        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute("home");
        }
        
        if (!isset($_SESSION['user']['type'])) {
            return $this->redirectToRoute('index');
        }

        // création du formulaire
        $user = new User();
        // instancie le formulaire avec les contraintes par défaut, + la contrainte registration pour que la saisie du mot de passe soit obligatoire
        $form = $this->createForm(UserType::class, $user,[
           'validation_groups' => array('User', 'registration'),
        ]);        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form['postalCode']->getData()[0] == "0") {
                str_replace($form['postalCode']->getData()[0], "", $form['postalCode']->getData());
            }
            $townships = $this->getDoctrine()->getManager()->getRepository(Township::class)->findAll();
            $cp = 0;
            $latitude = "";
            $longitude = "";
            foreach ($townships as $township) {
                if ($township->getPostalCode() == $form['postalCode']->getData()) {
                    if ($cp == 0) {
                        $cp = 1;
                        $latitude = $township->getLatitude();
                        $longitude = $township->getLongitude();
                    }
                }
            }

            if ($_SESSION['user']['type'] == "0") {
                if (empty($_POST['firstname']) || empty($_POST['lastname'])) {
                    $this->addFlash('error', "Merci d'entrer un nom et un prénom.");
                    return $this->redirectToRoute('user_registration');
                } else if (strlen($_POST['firstname']) > 20 || strlen($_POST['lastname']) > 20) {
                    $this->addFlash('error', 'Pas plus de 20 caractères.');
                    return $this->redirectToRoute('user_registration');
                } else if ($cp == 0) { 
                    $this->addFlash('error', 'Code postal invalide.');
                    return $this->redirectToRoute('user_registration');
                } else if (!preg_match( "#^([a-zA-ZàáâäçéèêëîïôùûüÿÀÂÄÇÉÈÊËÎÏÔÙÛÜŸ \-\']+)$#Ui" , $_POST['firstname'])) {
                    $this->addFlash('error', 'Prénom invalide.');
                    return $this->redirectToRoute('user_registration');
                } else if (!preg_match( "#^([a-zA-ZàáâäçéèêëîïôùûüÿÀÂÄÇÉÈÊËÎÏÔÙÛÜŸ \-\']+)$#Ui" , $_POST['lastname'])) {
                    $this->addFlash('error', 'Nom invalide.');
                    return $this->redirectToRoute('user_registration');
                } else {
                    // Encode le mot de passe
                    $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
                    $user->setPassword($password);
                    $user->setType($_SESSION['user']['type']);
                    $user->setLatitude($latitude);
                    $user->setLongitude($longitude);

                    $_SESSION['register_mail'] = $user->getEmail();

                    // Enregistre le membre en base
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($user);
                    $em->flush();
                }
            }  
            if ($_SESSION['user']['type'] == "1") {
                if (empty($_POST['society_name'])) {
                    $this->addFlash('error', "Merci d'entrer un nom d'organisation.");
                    return $this->redirectToRoute('user_registration');
                } else if (strlen($_POST['society_name']) > 50) {
                        $this->addFlash('error', 'Pas plus de 20 caractères.');
                        return $this->redirectToRoute('user_registration');
                } else if ($cp == 0) { 
                    $this->addFlash('error', 'Code postal invalide.');
                    return $this->redirectToRoute('user_registration');
                } else {
                    // Encode le mot de passe
                    $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
                    $user->setPassword($password);
                    $user->setType($_SESSION['user']['type']);
                    $user->setLatitude($latitude);
                    $user->setLongitude($longitude);

                    $_SESSION['register_mail'] = $user->getEmail();

                    // Enregistre le membre en base
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($user);
                    $em->flush();
                }
            }

            if ($_SESSION['user']['type'] == "0") {
                $firstname = htmlspecialchars(ucfirst($_POST['firstname']));
                $firstname = trim(preg_replace("/\s+/", " ", $firstname));
                $lastname = htmlspecialchars(ucfirst($_POST['lastname']));
                $lastname = trim(preg_replace("/\s+/", " ", $lastname));

                $user->setName($firstname.' '.$lastname);
                $user->setFirstname($firstname);
                $user->setLastname($lastname);
    
                $em->persist($user);
                $em->flush();
            }

            if ($_SESSION['user']['type'] == "1") {
                $name = trim(htmlspecialchars($_POST['society_name']));
                $name = preg_replace("/\s+/", " ", $name);

                $user->setName($name);
    
                $em->persist($user);
                $em->flush();
            }

            $this->addFlash('success', 'Votre compte a été créé avec succès. Veuillez maintenant vous connecter.');
            return $this->redirectToRoute('index');
        }
 
        return $this->render(
            'registration/register.html.twig',
            array('form' => $form->createView(),
                  'type' => $_SESSION['user']['type'])
        );
    }
}