<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;
use App\Entity\Event;
use App\Entity\UserEvent;
use App\Form\SettingsType;
use App\Form\EventType;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Form\SearchType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Services\FileUploader;

class EventController extends AbstractController
{
    /**
     * @Route("/events", name="events")
     */
    public function events(?UserInterface $user, Request $request)
    {
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('index');
        }

        $oneEvent = $this->getDoctrine()->getManager()->getRepository(UserEvent::class)->oneEvent($user->getId(), time());

        $topParts = $this->getDoctrine()->getManager()->getRepository(User::class)->classementPart();
        $topOrgas = $this->getDoctrine()->getManager()->getRepository(User::class)->classementOrga();

        $formSearch = $this->createForm(SearchType::class);
        $formSearch->handleRequest($request);

        if ($formSearch->isSubmitted() && $formSearch->isValid()) {
            $contentSearch = urlencode($formSearch['content']->getData());

            return $this->redirectToRoute('search_global', array('content' => $contentSearch));
        }

        return $this->render('events/list.html.twig', ['formSearch' => $formSearch->createView(), 'oneEvent' => $oneEvent,
        'topParts' => $topParts,
        'topOrgas' => $topOrgas]);
    }

    /**
     * @Route("/{username}/events", name="myevents")
     */
    public function myEvents(?UserInterface $user, Request $request, string $username)
    {
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('index');
        }

        $oneEvent = $this->getDoctrine()->getManager()->getRepository(UserEvent::class)->oneEvent($user->getId(), time());

        $topParts = $this->getDoctrine()->getManager()->getRepository(User::class)->classementPart();
        $topOrgas = $this->getDoctrine()->getManager()->getRepository(User::class)->classementOrga();

        $userEvent = $this->getDoctrine()->getManager()->getRepository(User::class)->findUserByUsername($username);

        if ($userEvent == null) {
            return $this->redirectToRoute('home');
        }

        $formSearch = $this->createForm(SearchType::class);
        $formSearch->handleRequest($request);

        if ($formSearch->isSubmitted() && $formSearch->isValid()) {
            $contentSearch = urlencode($formSearch['content']->getData());

            return $this->redirectToRoute('search_global', array('content' => $contentSearch));
        }

        return $this->render('events/my_events.html.twig', ['userEvent' => $userEvent, 'formSearch' => $formSearch->createView(), 'oneEvent' => $oneEvent,
        'topParts' => $topParts,
        'topOrgas' => $topOrgas]);
    }

    /**
     * @Route("/event/{id}", name="profile_event")
     */
    public function profileEvent(?UserInterface $user, Request $request, string $id)
    {
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('index');
        }

        $oneEvent = $this->getDoctrine()->getManager()->getRepository(UserEvent::class)->oneEvent($user->getId(), time());

        $topParts = $this->getDoctrine()->getManager()->getRepository(User::class)->classementPart();
        $topOrgas = $this->getDoctrine()->getManager()->getRepository(User::class)->classementOrga();

        $event = $this->getDoctrine()->getManager()->getRepository(Event::class)->find($id);
        //$owner = $this->getDoctrine()->getManager()->getRepository(User::class)->findUserByUsername($username);

        if ($event == "") {
            return $this->redirectToRoute('home');
        }

        $eventSelect = $this->getDoctrine()->getManager()->getRepository(Event::class)->eventProfile($id);

        if ($eventSelect == null) {
            return $this->redirectToRoute('home');
        }

        $formSearch = $this->createForm(SearchType::class);
        $formSearch->handleRequest($request);

        if ($formSearch->isSubmitted() && $formSearch->isValid()) {
            $contentSearch = urlencode($formSearch['content']->getData());

            return $this->redirectToRoute('search_global', array('content' => $contentSearch));
        }

        return $this->render('events/index.html.twig', ['formSearch' => $formSearch->createView(), 'eventSelect' => $eventSelect, 'oneEvent' => $oneEvent,
        'topParts' => $topParts,
        'topOrgas' => $topOrgas]);
    }

    /**
     * @Route("/create/event", name="create_event")
     */
    public function createEvent(?UserInterface $user, Request $request, FileUploader $fileUploader)
    {
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('index');
        }

        $verifCreate = time() - 259200;

        if ($user->getLastEvent() != "") {
            if ($user->getLastEvent()->getTimestamp() > $verifCreate) {
                $this->addFlash('error', "Vous ne pouvez pas créer plus d'un événement à moins de 3 jours d'intervalle.");
                return $this->redirectToRoute('events');
            }
        }

        $oneEvent = $this->getDoctrine()->getManager()->getRepository(UserEvent::class)->oneEvent($user->getId(), time());   

        $topParts = $this->getDoctrine()->getManager()->getRepository(User::class)->classementPart();
        $topOrgas = $this->getDoctrine()->getManager()->getRepository(User::class)->classementOrga();

        $event = new Event();

        $form = $this->createForm(EventType::class, $event);        
        $form->handleRequest($request);

        $formSearch = $this->createForm(SearchType::class);
        $formSearch->handleRequest($request);

        if ($formSearch->isSubmitted() && $formSearch->isValid()) {
            $contentSearch = urlencode($formSearch['content']->getData());

            return $this->redirectToRoute('search_global', array('content' => $contentSearch));
        }

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $postFile */
            $eventImage = $form['image']->getData();

            if ($eventImage) {
                $eventImageName = $fileUploader->upload3($eventImage);
                $event->setImage($eventImageName);
            }
 
            if (isset($_POST['map'])) {
                if ($_POST['map'] != "0" && $_POST['map'] != "1") {
                    $this->addFlash('error', "Une erreur est survenue. Merci de recommencer.");
                    return $this->redirectToRoute('create_event');
                } else {
                    $event->setUser($user);
                    $event->setMap($_POST['map']);

                    $em = $this->getDoctrine()->getManager();
                    $em->persist($event);
                    $em->flush();

                    $user->setLastEvent(new \DateTime());
                    $user->setScore($user->getScore() + 15);
                    $em->persist($user);
                    $em->flush();
                }
            } else {
                $this->addFlash('error', "Voulez-vous afficher cet événement sur la carte?");
                return $this->redirectToRoute('create_event');
            }

            $this->addFlash('success', "L'événement a été créé avec succès.");
            return $this->redirectToRoute('profile_event', array('id' => $event->getId()));
        }

        return $this->render('events/create.html.twig', ['form' => $form->createView(), 'formSearch' => $formSearch->createView(), 'oneEvent' => $oneEvent,
        'topParts' => $topParts,
        'topOrgas' => $topOrgas]);
    }

    /**
     * @Route("/event/edit/{id}", name="edit_event")
     */
    public function editEvent(?UserInterface $user, Request $request, FileUploader $fileUploader, string $id)
    {
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('index');
        }

        $oneEvent = $this->getDoctrine()->getManager()->getRepository(UserEvent::class)->oneEvent($user->getId(), time());

        $topParts = $this->getDoctrine()->getManager()->getRepository(User::class)->classementPart();
        $topOrgas = $this->getDoctrine()->getManager()->getRepository(User::class)->classementOrga();
        
        $event = $this->getDoctrine()->getManager()->getRepository(Event::class)->find($id);

        if ($event == "") {
            return $this->redirectToRoute('home');
        }

        if ($event->getUser() != $user) {
            return $this->redirectToRoute('home');
        }

        $form = $this->createForm(EventType::class, $event);        
        $form->handleRequest($request);

        $formSearch = $this->createForm(SearchType::class);
        $formSearch->handleRequest($request);

        if ($formSearch->isSubmitted() && $formSearch->isValid()) {
            $contentSearch = urlencode($formSearch['content']->getData());

            return $this->redirectToRoute('search_global', array('content' => $contentSearch));
        }

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $postFile */
            $eventImage = $form['image']->getData();

            if ($eventImage) {
                $eventImageName = $fileUploader->upload3($eventImage);
                $event->setImage($eventImageName);
            }
 
            if (isset($_POST['map'])) {
                if ($_POST['map'] != "0" && $_POST['map'] != "1") {
                    $this->addFlash('error', "Une erreur est survenue. Merci de recommencer.");
                    return $this->redirectToRoute('edit_event', array('id' => $id));
                } else {
                    $event->setMap($_POST['map']);
    
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($event);
                    $em->flush();
                }
            } else {
                $this->addFlash('error', "Voulez-vous afficher cet événement sur la carte?");
                return $this->redirectToRoute('edit_event', array('id' => $id));
            }

            $this->addFlash('success', "L'événement a été édité avec succès.");
            return $this->redirectToRoute('profile_event', array('id' => $id));
        }

        return $this->render('events/edit.html.twig', ['form' => $form->createView(), 'formSearch' => $formSearch->createView(), 'event' => $event, 'oneEvent' => $oneEvent,
        'topParts' => $topParts,
        'topOrgas' => $topOrgas]);
    }
}
