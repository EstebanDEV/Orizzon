<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Form\SearchType;

class StatisticsController extends AbstractController
{
    /**
     * @Route("/statistics", name="statistics")
     */
    public function statistics(Request $request)
    {
        $formSearch = $this->createForm(SearchType::class);
        $formSearch->handleRequest($request);

        if ($formSearch->isSubmitted() && $formSearch->isValid()) {
            $contentSearch = urlencode($formSearch['content']->getData());

            return $this->redirectToRoute('search_global', array('content' => $contentSearch));
        }

        return $this->render('statistics/index.html.twig', ['formSearch' => $formSearch->createView()]);
    }
}
