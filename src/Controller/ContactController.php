<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\ContactMessage;
use App\Form\Type\ContactMessageType;
use App\Helper\SessionHelper;
use App\Repository\ContactMessageRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/contact", name="app_contact_")
 */
final class ContactController extends AbstractController
{
    private ContactMessageRepository $repository;

    private SessionHelper $sessionHelper;

    public function __construct(ContactMessageRepository $repository, SessionHelper $sessionHelper)
    {
        $this->repository = $repository;
        $this->sessionHelper = $sessionHelper;
    }

    /**
     * @Route("", name="index")
     * @IsGranted("ROLE_SUPER_ADMIN")
     */
    public function index(): Response
    {
        return $this->render('contact/index.html.twig', [
            'messages' => $this->repository->findAll(),
        ]);
    }

    /**
     * @Route("/view/{id}", name="view")
     * @IsGranted("ROLE_SUPER_ADMIN")
     */
    public function view(ContactMessage $message): Response
    {
        $this->repository->markAsRead($message);

        return $this->render('contact/view.html.twig', [
            'message' => $message,
        ]);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request): Response
    {
        $message = new ContactMessage();
        $form = $this->createForm(ContactMessageType::class, $message, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_contact_create'),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->repository->save($message);
            $this->sessionHelper->addFlash('success', 'contact.create.flash_success');

            return $this->redirectToRoute('app_default_index');
        }

        return $this->render('contact/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     * @IsGranted("ROLE_SUPER_ADMIN")
     */
    public function delete(Request $request, ContactMessage $message): Response
    {
        $status = 200;

        if ($request->isMethod('delete')) {
            try {
                $result = [
                    'entity_id'   => $message->getId(),
                    'entity_name' => $message->getAuthor(),
                ];
                $this->repository->delete($message);

                $this->sessionHelper->addFlash('success', 'contact.delete.flash_success', ['%name%' => $result['entity_name']]);
                $redirectUrl = $request->headers->get('referer');

                return $this->json(['result' => $result, 'redirect_url' => $redirectUrl], 301);
            } catch (\Exception $e) {
                $status = 400;
                $this->sessionHelper->addFlash('danger', 'content.delete.flash.error');
            }
        }

        return $this->json(['content' => $this->renderView('contact/delete.html.twig', [
            'message' => $message,
        ])], $status);
    }
}
