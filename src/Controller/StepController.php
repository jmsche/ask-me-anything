<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Step;
use App\Entity\Tutorial;
use App\Form\Type\StepType;
use App\Helper\SessionHelper;
use App\Repository\StepRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/step", name="app_step_")
 * @IsGranted("ROLE_ADMIN")
 */
final class StepController extends AbstractController
{
    private StepRepository $stepRepository;

    private SessionHelper $sessionHelper;

    public function __construct(StepRepository $stepRepository, SessionHelper $sessionHelper)
    {
        $this->stepRepository = $stepRepository;
        $this->sessionHelper = $sessionHelper;
    }

    /**
     * @Route("/create/{id}", name="create")
     */
    public function create(Request $request, Tutorial $tutorial): Response
    {
        if ($tutorial->isLocked() && !$this->isGranted('ROLE_SUPER_ADMIN')) {
            throw $this->createAccessDeniedException('Tutorial is locked for simple admins.');
        }

        $step = new Step($tutorial);
        if (null !== $lastStep = $tutorial->getLastStep()) {
            $step->setWeight($lastStep->getWeight() + 1);
        }

        $form = $this->createForm(StepType::class, $step, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_step_create', ['id' => $tutorial->getId()]),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->stepRepository->save($step);
            $this->sessionHelper->addFlash('success', 'step.create.flash_success');

            return $this->redirectToRoute('app_tutorial_view', [
                'id'         => $tutorial->getId(),
                'stepNumber' => $tutorial->getSteps()->count() + 1,
            ]);
        }

        return $this->render('step/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
