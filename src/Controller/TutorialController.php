<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Tutorial;
use App\Form\Type\TutorialType;
use App\Repository\LogRepository;
use App\Repository\TutorialRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\TranslatableMessage;

/**
 * @Route("/tutorial", name="app_tutorial_")
 */
final class TutorialController extends AbstractController
{
    public function __construct(
        private TutorialRepository $tutorialRepository,
        private LogRepository $logRepository,
    ) {
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
        $this->logRepository->create($request, $tutorial, $step);

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
            $this->addFlash('success', new TranslatableMessage('tutorial.create.flash_success', ['%name%' => $tutorial->getName()]));

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
        $this->secure($tutorial);

        $form = $this->createForm(TutorialType::class, $tutorial, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_tutorial_update', ['id' => $tutorial->getId()]),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->tutorialRepository->save($tutorial);
            $this->addFlash('success', new TranslatableMessage('tutorial.update.flash_success', ['%name%' => $tutorial->getName()]));

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
        $this->secure($tutorial);

        $status = 200;

        if ($request->isMethod('delete')) {
            try {
                $result = [
                    'entity_id'   => $tutorial->getId(),
                    'entity_name' => $tutorial->getName(),
                ];
                $this->tutorialRepository->delete($tutorial);

                $this->addFlash('success', new TranslatableMessage('tutorial.delete.flash_success', ['%name%' => $result['entity_name']]));
                $redirectUrl = $this->generateUrl('app_default_index');

                return $this->json(['result' => $result, 'redirect_url' => $redirectUrl], 301);
            } catch (\Exception $e) {
                $status = 400;
                $this->addFlash('danger', new TranslatableMessage('content.delete.flash.error'));
            }
        }

        return $this->json(['content' => $this->renderView('tutorial/delete.html.twig', [
            'tutorial' => $tutorial,
        ])], $status);
    }

    /**
     * @Route("/lock/{id}", name="lock")
     * @IsGranted("ROLE_ADMIN")
     */
    public function lock(Tutorial $tutorial): Response
    {
        $this->secure($tutorial);
        if ($tutorial->isLocked()) {
            throw new BadRequestHttpException('Tutorial is already locked.');
        }

        $tutorial->setLocked(true);
        $this->tutorialRepository->save($tutorial);
        $this->addFlash('success', new TranslatableMessage('tutorial.lock.flash_success'));

        return $this->redirectToRoute('app_tutorial_view', ['id' => $tutorial->getId()]);
    }

    /**
     * @Route("/unlock/{id}", name="unlock")
     * @IsGranted("ROLE_ADMIN")
     */
    public function unlock(Tutorial $tutorial): Response
    {
        $this->secure($tutorial);
        if (!$tutorial->isLocked()) {
            throw new BadRequestHttpException('Tutorial is already unlocked.');
        }

        $tutorial->setLocked(false);
        $this->tutorialRepository->save($tutorial);
        $this->addFlash('success', new TranslatableMessage('tutorial.unlock.flash_success'));

        return $this->redirectToRoute('app_tutorial_view', ['id' => $tutorial->getId()]);
    }

    /**
     * @Route("/set-visible/{id}", name="set_visible")
     * @IsGranted("ROLE_ADMIN")
     */
    public function setVisible(Tutorial $tutorial): Response
    {
        $this->secure($tutorial);
        if ($tutorial->isVisible()) {
            throw new BadRequestHttpException('Tutorial is already visible.');
        }

        $tutorial->setVisible(true);
        $this->tutorialRepository->save($tutorial);
        $this->addFlash('success', new TranslatableMessage('tutorial.set_visibility.flash_success'));

        return $this->redirectToRoute('app_tutorial_view', ['id' => $tutorial->getId()]);
    }

    /**
     * @Route("/set-invisible/{id}", name="set_invisible")
     * @IsGranted("ROLE_ADMIN")
     */
    public function setInvisible(Tutorial $tutorial): Response
    {
        $this->secure($tutorial);
        if (!$tutorial->isVisible()) {
            throw new BadRequestHttpException('Tutorial is already invisible.');
        }

        $tutorial->setVisible(false);
        $this->tutorialRepository->save($tutorial);
        $this->addFlash('success', new TranslatableMessage('tutorial.set_visibility.flash_success'));

        return $this->redirectToRoute('app_tutorial_view', ['id' => $tutorial->getId()]);
    }

    private function secure(Tutorial $tutorial): void
    {
        if ($tutorial->isLocked() && !$this->isGranted('ROLE_SUPER_ADMIN')) {
            throw $this->createAccessDeniedException('Tutorial is locked for simple admins.');
        }
    }
}
