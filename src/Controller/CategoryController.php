<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Category;
use App\Form\Type\CategoryType;
use App\Repository\CategoryRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/category", name="app_category_")
 */
final class CategoryController extends AbstractController
{
    private CategoryRepository $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @Route("", name="index")
     * @IsGranted("ROLE_SUPER_ADMIN")
     */
    public function index(): Response
    {
        return $this->render('category/index.html.twig', [
            'categories' => $this->categoryRepository->findAll(),
        ]);
    }

    /**
     * @Route("/view/{slug}", name="view")
     */
    public function view(Category $category): Response
    {
        return $this->render('category/view.html.twig');
    }

    /**
     * @Route("/create", name="create")
     * @IsGranted("ROLE_SUPER_ADMIN")
     */
    public function create(Request $request): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_category_create'),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

        }

        return $this->render('category/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/update/{id}", name="update")
     * @IsGranted("ROLE_SUPER_ADMIN")
     */
    public function update(Category $category): Response
    {
        return $this->render('category/update.html.twig');
    }

    /**
     * @Route("/delete/{id}", name="delete")
     * @IsGranted("ROLE_SUPER_ADMIN")
     */
    public function delete(Category $category): Response
    {
        return $this->render('category/delete.html.twig');
    }
}
