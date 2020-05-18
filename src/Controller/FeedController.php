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
use Symfony\Component\Security\Core\User\UserInterface;

class FeedController extends AbstractController
{
    /**
     * @Route("/home", name="home")
     */
    public function home(Request $request, FileUploader $fileUploader, ?UserInterface $user)
    {
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('index');
        }

        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        $formSearch = $this->createForm(SearchType::class);
        $formSearch->handleRequest($request);

        if ($formSearch->isSubmitted() && $formSearch->isValid()) {
            $contentSearch = urlencode($formSearch['content']->getData());

            return $this->redirectToRoute('search_global', array('content' => $contentSearch));
        }

        $oneEvent = $this->getDoctrine()->getManager()->getRepository(UserEvent::class)->oneEvent($user->getId(), time());

        $topParts = $this->getDoctrine()->getManager()->getRepository(User::class)->classementPart();
        $topOrgas = $this->getDoctrine()->getManager()->getRepository(User::class)->classementOrga();

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form['image']->getData() == "" && $form['content']->getData() == "") {
                $request->getSession()->getFlashBag()->add('error', "Veuillez insérer du contenu dans votre publication.");
                return $this->redirectToRoute('home');
            }

            /** @var UploadedFile $postFile */
            $postFile = $form['image']->getData();

            if ($postFile) {
                $postFileName = $fileUploader->upload($postFile);
                $post->setImage($postFileName);
            }

            $em = $this->getDoctrine()->getManager();
            $post->setAuthor($user);
            $em->persist($post);
            $em->flush();

            $request->getSession()->getFlashBag()->add('success', "Votre publication a bien été enregistré.");
            return $this->redirectToRoute('home');
        }
        return $this->render('site/home.html.twig', [
            'form' => $form->createView(),
            'formSearch' => $formSearch->createView(),
            'oneEvent' => $oneEvent,
            'topParts' => $topParts,
            'topOrgas' => $topOrgas
        ]);
    }
}
