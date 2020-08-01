<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Tutorial;
use App\Form\Type\TutorialType;
use App\Helper\SessionHelper;
use App\Repository\TutorialRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/tutorial", name="app_tutorial_")
 */
final class TutorialController extends AbstractController
{
    private TutorialRepository $tutorialRepository;

    private SessionHelper $sessionHelper;

    public function __construct(TutorialRepository $tutorialRepository, SessionHelper $sessionHelper)
    {
        $this->tutorialRepository = $tutorialRepository;
        $this->sessionHelper = $sessionHelper;
    }

    /**
     * @Route("/view/{id}/{stepNumber}", name="view", defaults={"stepNumber" = 1})
     */
    public function view(Request $request, Tutorial $tutorial, int $stepNumber): Response
    {
        if (!$tutorial->isVisible() && !$this->isGranted('ROLE_SUPER_ADMIN')) {
            throw $this->createNotFoundException('Tutorial is supposed to be invisible.');
        }

        if (1 !== $stepNumber && (0 > $stepNumber || $tutorial->getSteps()->count() < $stepNumber)) {
            throw $this->createNotFoundException('Invalid step number.');
        }

        $step = $tutorial->getSteps()->get($stepNumber - 1);

        return $this->render('tutorial/view.html.twig', [
            'tutorial'      => $tutorial,
            'step_number'   => $stepNumber,
            'step'          => $step,
            'from_search'   => $request->query->get('fromSearch'),
            'from_category' => $request->query->get('fromCategory'),
        ]);
    }

    /**
     * @Route("/create", name="create")
     * @IsGranted("ROLE_ADMIN")
     */
    public function create(Request $request): Response
    {
        $tutorial = new Tutorial();
        $form = $this->createForm(TutorialType::class, $tutorial, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_tutorial_create'),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->tutorialRepository->save($tutorial);
            $this->sessionHelper->addFlash('success', 'tutorial.create.flash_success', ['%name%' => $tutorial->getName()]);

            return $this->redirectToRoute('app_tutorial_view', ['id' => $tutorial->getId()]);
        }

        return $this->render('tutorial/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/update/{id}", name="update")
     * @IsGranted("ROLE_ADMIN")
     */
    public function update(Tutorial $tutorial, Request $request): Response
    {
        if ($tutorial->isLocked() && !$this->isGranted('ROLE_SUPER_ADMIN')) {
            throw $this->createAccessDeniedException('Tutorial is locked for simple admins.');
        }

        $form = $this->createForm(TutorialType::class, $tutorial, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_tutorial_update', ['id' => $tutorial->getId()]),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->tutorialRepository->save($tutorial);
            $this->sessionHelper->addFlash('success', 'tutorial.update.flash_success', ['%name%' => $tutorial->getName()]);

            return $this->redirectToRoute('app_tutorial_view', ['id' => $tutorial->getId()]);
        }

        return $this->render('tutorial/update.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     * @IsGranted("ROLE_ADMIN")
     */
    public function delete(Request $request, Tutorial $tutorial): Response
    {
        if ($tutorial->isLocked() && !$this->isGranted('ROLE_SUPER_ADMIN')) {
            throw $this->createAccessDeniedException('Tutorial is locked for simple admins.');
        }

        $status = 200;

        if ($request->isMethod('delete')) {
            try {
                $result = [
                    'entity_id'   => $tutorial->getId(),
                    'entity_name' => $tutorial->getName(),
                ];
                $this->tutorialRepository->delete($tutorial);

                $this->sessionHelper->addFlash('success', 'tutorial.delete.flash_success', ['%name%' => $result['entity_name']]);
                $redirectUrl = $this->generateUrl('app_default_index');

                return $this->json(['result' => $result, 'redirect_url' => $redirectUrl], 301);
            } catch (\Exception $e) {
                $status = 400;
                $this->sessionHelper->addFlash('danger', 'content.delete.flash.error');
            }
        }

        return $this->json(['content' => $this->renderView('tutorial/delete.html.twig', [
            'tutorial' => $tutorial,
        ])], $status);
    }
}
