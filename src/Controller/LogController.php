<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\LogRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Translation\TranslatableMessage;

#[Route('/log', name: 'app_log_')]
#[IsGranted('ROLE_SUPER_ADMIN')]
final class LogController extends AbstractController
{
    public function __construct(
        private LogRepository $repository,
    ) {}

    #[Route('', name: 'index')]
    public function index(): Response
    {
        return $this->render('log/index.html.twig', [
            'logs' => $this->repository->findAll(),
        ]);
    }

    #[Route('/delete', name: 'delete')]
    public function delete(): Response
    {
        $this->repository->deleteAllData();
        $this->addFlash('success', new TranslatableMessage('log.delete.flash_success'));

        return $this->redirectToRoute('app_log_index');
    }
}
