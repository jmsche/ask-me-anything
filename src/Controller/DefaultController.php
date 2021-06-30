<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\TutorialRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(name="app_default_")
 */
final class DefaultController extends AbstractController
{
    public function __construct(
        private CategoryRepository $categoryRepository,
        private TutorialRepository $tutorialRepository,
    ) {
    }

    /**
     * @Route("", name="index")
     */
    public function index(): Response
    {
        return $this->render('default/index.html.twig', [
            'categories' => $this->categoryRepository->findAll(),
        ]);
    }

    /**
     * @Route("/search", name="search")
     */
    public function search(Request $request): Response
    {
        $query = $request->query->get('q');
        $tutorials = !empty($query) ? $this->tutorialRepository->search($query) : [];

        return $this->render('default/search.html.twig', [
            'query'     => $query,
            'tutorials' => $tutorials,
        ]);
    }
}
