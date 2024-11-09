<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Form\Type\UserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Translation\TranslatableMessage;

#[Route('/user', name: 'app_user_')]
#[IsGranted('ROLE_SUPER_ADMIN')]
final class UserController extends AbstractController
{
    public function __construct(
        private UserRepository $userRepository,
    ) {}

    #[Route('', name: 'index')]
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $this->userRepository->findAll(),
        ]);
    }

    #[Route('/create', name: 'create')]
    public function create(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_user_create'),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userRepository->save($user);
            $this->addFlash('success', new TranslatableMessage('user.create.flash_success', ['%name%' => $user->getUsername()]));

            return $this->redirectToRoute('app_user_index');
        }

        return $this->render('user/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/update/{id}', name: 'update')]
    public function update(Request $request, User $user): Response
    {
        $form = $this->createForm(UserType::class, $user, [
            'method' => 'POST',
            'action' => $this->generateUrl('app_user_update', ['id' => $user->getId()]),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userRepository->save($user);
            $this->addFlash('success', new TranslatableMessage('user.update.flash_success', ['%name%' => $user->getUsername()]));

            return $this->redirectToRoute('app_user_index');
        }

        return $this->render('user/update.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/delete/{id}', name: 'delete')]
    public function delete(Request $request, User $user): Response
    {
        $status = 200;

        if ($request->isMethod('delete')) {
            try {
                $result = [
                    'entity_id'   => $user->getId(),
                    'entity_name' => $user->getUsername(),
                ];
                $this->userRepository->delete($user);

                $this->addFlash('success', new TranslatableMessage('user.delete.flash_success', ['%name%' => $result['entity_name']]));
                $redirectUrl = $request->headers->get('referer');

                return $this->json(['result' => $result, 'redirect_url' => $redirectUrl], 301);
            } catch (\Exception $e) {
                $status = 400;
                $this->addFlash('danger', new TranslatableMessage('content.delete.flash.error'));
            }
        }

        return $this->json(['content' => $this->renderView('user/delete.html.twig', [
            'user' => $user,
        ])], $status);
    }
}
