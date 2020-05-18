<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;
use App\Entity\Message;
use App\Entity\UserEvent;
use App\Form\SettingsType;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Form\SearchType;

class MessageController extends AbstractController
{
    /**
     * @Route("/messages", name="messages")
     */
    public function messages(?UserInterface $user, Request $request)
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

        return $this->render('messages/index.html.twig', ['formSearch' => $formSearch->createView(), 'oneEvent' => $oneEvent,
        'topParts' => $topParts,
        'topOrgas' => $topOrgas]);
    }

    /**
     * @Route("/messages/{username}", name="chat_user")
     */
    public function chatUser(string $username, ?UserInterface $user, Request $request)
    {
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('index');
        }

        $oneEvent = $this->getDoctrine()->getManager()->getRepository(UserEvent::class)->oneEvent($user->getId(), time());

        $topParts = $this->getDoctrine()->getManager()->getRepository(User::class)->classementPart();
        $topOrgas = $this->getDoctrine()->getManager()->getRepository(User::class)->classementOrga();
        
        $userSelect = $this->getDoctrine()->getManager()->getRepository(User::class)->findUserByUsername($username);
        if ($userSelect == null) {
            return $this->redirectToRoute('home');
        }

        if ($userSelect->getId() == $user->getId() ) {
            return $this->redirectToRoute('home');
        }

        $messages = $this->getDoctrine()->getManager()->getRepository(Message::class)->findMessagesLength($user->getId(), $userSelect->getId()); 

        $formSearch = $this->createForm(SearchType::class);
        $formSearch->handleRequest($request);

        if ($formSearch->isSubmitted() && $formSearch->isValid()) {
            $contentSearch = urlencode($formSearch['content']->getData());

            return $this->redirectToRoute('search_global', array('content' => $contentSearch));
        }

        return $this->render('messages/chat_user.html.twig', ['userSelect' => $userSelect, 'formSearch' => $formSearch->createView(), 'messages' => $messages, 'oneEvent' => $oneEvent,
        'topParts' => $topParts,
        'topOrgas' => $topOrgas]);
    }
}
