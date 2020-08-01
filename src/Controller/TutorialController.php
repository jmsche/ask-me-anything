<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Tutorial;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/tutorial", name="app_tutorial_")
 */
final class TutorialController extends AbstractController
{
    /**
     * @Route("/view/{id}", name="view")
     */
    public function view(Request $request, Tutorial $tutorial): Response
    {
        if (!$tutorial->isVisible() && !$this->isGranted('ROLE_SUPER_ADMIN')) {
            throw $this->createNotFoundException('Tutorial is supposed to be invisible.');
        }

        return $this->render('tutorial/view.html.twig', [
            'tutorial'      => $tutorial,
            'from_search'   => $request->query->get('fromSearch'),
            'from_category' => $request->query->get('fromCategory'),
        ]);
    }
}
