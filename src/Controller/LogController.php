<?php

declare(strict_types=1);

namespace App\Controller;

use App\Helper\SessionHelper;
use App\Repository\LogRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/log", name="app_log_")
 * @IsGranted("ROLE_SUPER_ADMIN")
 */
final class LogController extends AbstractController
{
    private LogRepository $repository;

    private SessionHelper $sessionHelper;

    public function __construct(LogRepository $repository, SessionHelper $sessionHelper)
    {
        $this->repository = $repository;
        $this->sessionHelper = $sessionHelper;
    }

    /**
     * @Route("", name="index")
     */
    public function index(): Response
    {
        return $this->render('log/index.html.twig', [
            'logs' => $this->repository->findAll(),
        ]);
    }

    /**
     * @Route("/delete", name="delete")
     */
    public function delete(): Response
    {
        $this->repository->deleteAllData();
        $this->sessionHelper->addFlash('success', 'log.delete.flash_success');

        return $this->redirectToRoute('app_log_index');
    }
}
