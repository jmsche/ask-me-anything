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
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/step", name="app_step_")
 * @IsGranted("ROLE_ADMIN")
 */
final class StepController extends AbstractController
{
    public function __construct(
        private StepRepository $stepRepository,
        private SessionHelper $sessionHelper,
    ) {
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

    /**
     * @Route("/update/{id}", name="update")
     */
    public function update(Step $step, Request $request): Response
    {
        $this->secure($step);

        $form = $this->createForm(StepType::class, $step, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_step_update', ['id' => $step->getId()]),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->stepRepository->save($step);
            $this->sessionHelper->addFlash('success', 'step.update.flash_success');

            return $this->redirectToRoute('app_tutorial_view', ['id' => $step->getTutorial()->getId(), 'stepNumber' => $step->getNumber()]);
        }

        return $this->render('step/update.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(Request $request, Step $step): Response
    {
        $this->secure($step);

        $status = 200;

        if ($request->isMethod('delete')) {
            try {
                $result = [
                    'entity_id'   => $step->getId(),
                    'entity_name' => $step->getNumber(),
                ];
                $tutorialId = $step->getTutorial()->getId();
                $this->stepRepository->delete($step);

                $this->sessionHelper->addFlash('success', 'step.delete.flash_success', ['%number%' => $result['entity_name']]);
                $redirectUrl = $this->generateUrl('app_tutorial_view', ['id' => $tutorialId]);

                return $this->json(['result' => $result, 'redirect_url' => $redirectUrl], 301);
            } catch (\Exception $e) {
                $status = 400;
                $this->sessionHelper->addFlash('danger', 'content.delete.flash.error');
            }
        }

        return $this->json(['content' => $this->renderView('step/delete.html.twig', [
            'step' => $step,
        ])], $status);
    }

    /**
     * @Route("/move-prev/{id}", name="move_prev")
     */
    public function movePrev(Step $step): Response
    {
        $this->secure($step);

        if (1 === $step->getNumber()) {
            throw new BadRequestHttpException('Step is already first.');
        }

        $number = $step->getNumber();
        $prevStep = $step->getTutorial()->getSteps()->get($number - 2);
        $this->stepRepository->invertSteps($step, $prevStep);
        $this->sessionHelper->addFlash('success', 'step.move.flash_success');

        return $this->redirectToRoute('app_tutorial_view', [
            'id'         => $step->getTutorial()->getId(),
            'stepNumber' => $number - 1,
        ]);
    }

    /**
     * @Route("/move-next/{id}", name="move_next")
     */
    public function moveNext(Step $step): Response
    {
        $this->secure($step);

        if ($step->getNumber() === $step->getTutorial()->getSteps()->count()) {
            throw new BadRequestHttpException('Step is already last.');
        }

        $number = $step->getNumber();
        $nextStep = $step->getTutorial()->getSteps()->get($number);
        $this->stepRepository->invertSteps($step, $nextStep);
        $this->sessionHelper->addFlash('success', 'step.move.flash_success');

        return $this->redirectToRoute('app_tutorial_view', [
            'id'         => $step->getTutorial()->getId(),
            'stepNumber' => $number + 1,
        ]);
    }

    /**
     * @Route("/upload-file", name="upload_file")
     */
    public function uploadFile(Request $request): Response
    {
        try {
            /* @var \Symfony\Component\HttpFoundation\File\UploadedFile $uploadedFile */
            $uploadedFile = $request->files->get('upload');
            if ($uploadedFile->getError()) {
                throw new \Exception($uploadedFile->getErrorMessage());
            }
            $uploadsDir = __DIR__ . '/../../public/uploads/';
            if (!is_dir($uploadsDir) && !mkdir($uploadsDir, 0777, true) && !is_dir($uploadsDir)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $uploadsDir));
            }

            $file = $uploadedFile->move($uploadsDir, uniqid('', false) . '.' . $uploadedFile->guessExtension());

            return $this->json([
                'uploaded' => 1,
                'fileName' => $file->getFilename(),
                'url'      => '/uploads/' . $file->getFilename(),
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'uploaded' => 0,
                'error'    => ['message' => $e->getMessage()],
            ]);
        }
    }

    private function secure(Step $step): void
    {
        if ($step->getTutorial()->isLocked() && !$this->isGranted('ROLE_SUPER_ADMIN')) {
            throw $this->createAccessDeniedException('Tutorial is locked for simple admins.');
        }
    }
}
