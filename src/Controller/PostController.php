<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\User;
use App\Entity\UserEvent;
use App\Form\PostType;
use App\Form\SearchType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Services\FileUploader;
use Mgilet\NotificationBundle\Manager\NotificationManager;
use Symfony\Component\Security\Core\User\UserInterface;

class PostController extends AbstractController
{
    /**
     * @Route("/{username}/post/{id}", name="post")
     */
    public function post(Request $request, FileUploader $fileUploader, ?UserInterface $user, string $username, string $id)
    {
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('index');
        }

        $post = $this->getDoctrine()->getManager()->getRepository(Post::class)->find($id);
        $author = $this->getDoctrine()->getManager()->getRepository(User::class)->findUserByUsername($username);

        if ($post == "" || $author == "") {
            return $this->redirectToRoute('home');
        }

        $postSelect = $this->getDoctrine()->getManager()->getRepository(Post::class)->findPost($id, $author->getId());

        if ($postSelect == null) {
            return $this->redirectToRoute('home');
        }

        $formSearch = $this->createForm(SearchType::class);
        $formSearch->handleRequest($request);

        if ($formSearch->isSubmitted() && $formSearch->isValid()) {
            $contentSearch = urlencode($formSearch['content']->getData());

            return $this->redirectToRoute('search_global', array('content' => $contentSearch));
        }

        $oneEvent = $this->getDoctrine()->getManager()->getRepository(UserEvent::class)->oneEvent($user->getId(), time());

        $topParts = $this->getDoctrine()->getManager()->getRepository(User::class)->classementPart();
        $topOrgas = $this->getDoctrine()->getManager()->getRepository(User::class)->classementOrga();

        return $this->render('post/index.html.twig', [
            'formSearch' => $formSearch->createView(),
            'post' => $postSelect,
            'oneEvent' => $oneEvent,
            'topParts' => $topParts,
            'topOrgas' => $topOrgas
        ]);
    }
}
