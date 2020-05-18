<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;
use App\Entity\Event;
use App\Entity\Post;
use App\Form\SearchType;
use App\Entity\UserEvent;
use Symfony\Component\Security\Core\User\UserInterface;

class SearchController extends AbstractController
{
    /**
     * @Route("/search/{content}", name="search_global", requirements={"content"=".+"})
     */
    public function searchGlobal(string $content, Request $request, ?UserInterface $user) {
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('index');
        }

        $oneEvent = $this->getDoctrine()->getManager()->getRepository(UserEvent::class)->oneEvent($user->getId(), time());

        $topParts = $this->getDoctrine()->getManager()->getRepository(User::class)->classementPart();
        $topOrgas = $this->getDoctrine()->getManager()->getRepository(User::class)->classementOrga();
        
        $formSearch = $this->createForm(SearchType::class);
        $formSearch->handleRequest($request);

        $content_decode = urldecode($content);
        $content_decode = preg_replace("/\s+/", " ", $content_decode);

        if ($formSearch->isSubmitted() && $formSearch->isValid()) {
         $contentSearch = urlencode($formSearch['content']->getData());

         return $this->redirectToRoute('search_global', array('content' => $contentSearch));
     }
        
    return $this->render('search/index.html.twig', array('result' => $content_decode, 'formSearch' => $formSearch->createView(), 'oneEvent' => $oneEvent,
        'topParts' => $topParts,
        'topOrgas' => $topOrgas));
     }
}
