<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Category;
use App\Form\Type\CategoryType;
use App\Helper\SessionHelper;
use App\Repository\CategoryRepository;
use App\Repository\TutorialRepository;
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

    private SessionHelper $sessionHelper;

    private TutorialRepository $tutorialRepository;

    public function __construct(CategoryRepository $categoryRepository, SessionHelper $sessionHelper, TutorialRepository $tutorialRepository)
    {
        $this->categoryRepository = $categoryRepository;
        $this->sessionHelper = $sessionHelper;
        $this->tutorialRepository = $tutorialRepository;
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
        return $this->render('category/view.html.twig', [
            'categories' => $this->categoryRepository->findAll(),
            'category'   => $category,
            'tutorials'  => $this->tutorialRepository->findByCategory($category),
        ]);
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
            $this->categoryRepository->save($category);
            $this->sessionHelper->addFlash('success', 'category.create.flash_success', ['%name%' => $category->getName()]);

            return $this->redirectToRoute('app_category_index');
        }

        return $this->render('category/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/update/{id}", name="update")
     * @IsGranted("ROLE_SUPER_ADMIN")
     */
    public function update(Request $request, Category $category): Response
    {
        $form = $this->createForm(CategoryType::class, $category, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_category_update', ['id' => $category->getId()]),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->categoryRepository->save($category);
            $this->sessionHelper->addFlash('success', 'category.update.flash_success', ['%name%' => $category->getName()]);

            return $this->redirectToRoute('app_category_index');
        }

        return $this->render('category/update.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     * @IsGranted("ROLE_SUPER_ADMIN")
     */
    public function delete(Request $request, Category $category): Response
    {
        $status = 200;

        if ($request->isMethod('delete')) {
            try {
                $result = [
                    'entity_id'   => $category->getId(),
                    'entity_name' => $category->getName(),
                ];
                $this->categoryRepository->delete($category);

                $this->sessionHelper->addFlash('success', 'category.delete.flash_success', ['%name%' => $result['entity_name']]);
                $redirectUrl = $request->headers->get('referer');

                return $this->json(['result' => $result, 'redirect_url' => $redirectUrl], 301);
            } catch (\Exception $e) {
                $status = 400;
                $this->sessionHelper->addFlash('danger', 'content.delete.flash.error');
            }
        }

        return $this->json(['content' => $this->renderView('category/delete.html.twig', [
            'category' => $category,
        ])], $status);
    }
}
