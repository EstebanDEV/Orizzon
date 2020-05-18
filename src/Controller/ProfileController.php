<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;
use App\Entity\UserEvent;
use App\Entity\Township;
use App\Form\SettingsType;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Form\SearchType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Services\FileUploader;

class ProfileController extends AbstractController
{
    /**
     * @Route("/profile/{username}", name="profile")
     */
    public function profile(string $username, ?UserInterface $user, Request $request)
    {
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('index');
        }

        $oneEvent = $this->getDoctrine()->getManager()->getRepository(UserEvent::class)->oneEvent($user->getId(), time());

        $topParts = $this->getDoctrine()->getManager()->getRepository(User::class)->classementPart();
        $topOrgas = $this->getDoctrine()->getManager()->getRepository(User::class)->classementOrga();

        $userProfile = $this->getDoctrine()->getManager()->getRepository(User::class)->findUserByUsername($username);
        if ($userProfile == null) {
            return $this->redirectToRoute('home');
        }

        $formSearch = $this->createForm(SearchType::class);
        $formSearch->handleRequest($request);

        if ($formSearch->isSubmitted() && $formSearch->isValid()) {
            $contentSearch = urlencode($formSearch['content']->getData());

            return $this->redirectToRoute('search_global', array('content' => $contentSearch));
        }

        return $this->render('profile/index.html.twig', ['userProfile' => $userProfile, 'formSearch' => $formSearch->createView(), 'oneEvent' => $oneEvent,
        'topParts' => $topParts,
        'topOrgas' => $topOrgas]);
    }

    /**
     * @Route("/profile/settings/general", name="profile_settings_general")
     */
    public function profileSettings(Request $request, ?UserInterface $user, FileUploader $fileUploader)
    {
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('index');
        }

        $oneEvent = $this->getDoctrine()->getManager()->getRepository(UserEvent::class)->oneEvent($user->getId(), time());

        $topParts = $this->getDoctrine()->getManager()->getRepository(User::class)->classementPart();
        $topOrgas = $this->getDoctrine()->getManager()->getRepository(User::class)->classementOrga();
        
        $form = $this->createForm(SettingsType::class, $user);
        $form->handleRequest($request);

        $formSearch = $this->createForm(SearchType::class);
        $formSearch->handleRequest($request);

        if ($formSearch->isSubmitted() && $formSearch->isValid()) {
            $contentSearch = urlencode($formSearch['content']->getData());

            return $this->redirectToRoute('search_global', array('content' => $contentSearch));
        }

        if($form->isSubmitted() && $form->isValid())
        {
            /** @var UploadedFile $postFile */
            $userImage = $form['image']->getData();

            if ($userImage) {
                $userImageName = $fileUploader->upload2($userImage);
                $user->setImage($userImageName);
            }

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

            if ($user->getType() == "0") {
                if (empty($_POST['firstname']) || empty($_POST['lastname'])) {
                    $this->addFlash('error', "Merci d'entrer un nom et un prénom.");
                    return $this->redirectToRoute('profile_settings_general');
                } else if (strlen($_POST['firstname']) > 20 || strlen($_POST['lastname']) > 20) {
                    $this->addFlash('error', 'Pas plus de 20 caractères.');
                    return $this->redirectToRoute('profile_settings_general');
                } else if ($cp == 0) { 
                    $this->addFlash('error', 'Code postal invalide.');
                    return $this->redirectToRoute('profile_settings_general');
                } else if (!preg_match( "#^([a-zA-ZàáâäçéèêëîïôùûüÿÀÂÄÇÉÈÊËÎÏÔÙÛÜŸ \-\']+)$#Ui" , $_POST['firstname'])) {
                    $this->addFlash('error', 'Prénom invalide.');
                    return $this->redirectToRoute('profile_settings_general');
                } else if (!preg_match( "#^([a-zA-ZàáâäçéèêëîïôùûüÿÀÂÄÇÉÈÊËÎÏÔÙÛÜŸ \-\']+)$#Ui" , $_POST['lastname'])) {
                    $this->addFlash('error', 'Nom invalide.');
                    return $this->redirectToRoute('profile_settings_general');
                } else {
                    $user->setLatitude($latitude);
                    $user->setLongitude($longitude);

                    $em = $this->getDoctrine()->getManager();
                    $em->persist($user);
                    $em->flush();
                }
            }  
            if ($user->getType() == "1") {
                if (empty($_POST['society_name'])) {
                    $this->addFlash('error', "Merci d'entrer un nom d'organisation.");
                    return $this->redirectToRoute('profile_settings_general');
                } else if (strlen($_POST['society_name']) > 50) {
                        $this->addFlash('error', 'Pas plus de 20 caractères.');
                        return $this->redirectToRoute('profile_settings_general');
                } else if ($cp == 0) { 
                    $this->addFlash('error', 'Code postal invalide.');
                    return $this->redirectToRoute('profile_settings_general');
                } else {
                    $user->setLatitude($latitude);
                    $user->setLongitude($longitude);

                    $em = $this->getDoctrine()->getManager();
                    $em->persist($user);
                    $em->flush();
                }
            }

            if ($user->getType() == "0") {
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

            if ($user->getType() == "1") {
                $name = trim(htmlspecialchars($_POST['society_name']));
                $name = preg_replace("/\s+/", " ", $name);

                $user->setName($name);
    
                $em->persist($user);
                $em->flush();
            }

            $request->getSession()->getFlashBag()->add('success', "Votre informations ont été mises à jour.");

            return $this->redirectToRoute('profile', array('username' => $this->getUser()->getUsername()));
        }

        return $this->render('profile/settings.html.twig', [
            'form' => $form->createView(),
            'formSearch' => $formSearch->createView(), 'oneEvent' => $oneEvent,
            'topParts' => $topParts,
            'topOrgas' => $topOrgas
        ]);
    }

    /**
     * @Route("/profile/{username}/followers", name="profile_followers")
     */
    public function profileFollowers(string $username, ?UserInterface $user, Request $request)
    {
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('index');
        }

        $oneEvent = $this->getDoctrine()->getManager()->getRepository(UserEvent::class)->oneEvent($user->getId(), time());

        $topParts = $this->getDoctrine()->getManager()->getRepository(User::class)->classementPart();
        $topOrgas = $this->getDoctrine()->getManager()->getRepository(User::class)->classementOrga();

        $userProfile = $this->getDoctrine()->getManager()->getRepository(User::class)->findUserByUsername($username);
        if ($userProfile == null) {
            return $this->redirectToRoute('home');
        }

        $formSearch = $this->createForm(SearchType::class);
        $formSearch->handleRequest($request);

        if ($formSearch->isSubmitted() && $formSearch->isValid()) {
            $contentSearch = urlencode($formSearch['content']->getData());

            return $this->redirectToRoute('search_global', array('content' => $contentSearch));
        }

        return $this->render('profile/followers.html.twig', ['userProfile' => $userProfile, 'formSearch' => $formSearch->createView(), 'oneEvent' => $oneEvent,
        'topParts' => $topParts,
        'topOrgas' => $topOrgas]);
    }

    /**
     * @Route("/profile/{username}/followings", name="profile_followings")
     */
    public function profileFollowings(string $username, ?UserInterface $user, Request $request)
    {
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('index');
        }

        $oneEvent = $this->getDoctrine()->getManager()->getRepository(UserEvent::class)->oneEvent($user->getId(), time());

        $topParts = $this->getDoctrine()->getManager()->getRepository(User::class)->classementPart();
        $topOrgas = $this->getDoctrine()->getManager()->getRepository(User::class)->classementOrga();

        $userProfile = $this->getDoctrine()->getManager()->getRepository(User::class)->findUserByUsername($username);
        if ($userProfile == null) {
            return $this->redirectToRoute('home');
        }

        $formSearch = $this->createForm(SearchType::class);
        $formSearch->handleRequest($request);

        if ($formSearch->isSubmitted() && $formSearch->isValid()) {
            $contentSearch = urlencode($formSearch['content']->getData());

            return $this->redirectToRoute('search_global', array('content' => $contentSearch));
        }

        return $this->render('profile/followings.html.twig', ['userProfile' => $userProfile, 'formSearch' => $formSearch->createView(), 'oneEvent' => $oneEvent,
        'topParts' => $topParts,
        'topOrgas' => $topOrgas]);
    }
}
