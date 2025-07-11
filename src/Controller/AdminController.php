<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\LocationRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Form\EditUserType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\EventRepository;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Form\EventType;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'admin_dashboard')]
    public function dashboard(CategoryRepository $categoryRepo, UserRepository $userRepo, LocationRepository $locationRepo, EventRepository $eventRepo): Response
    {
        return $this->render('admin/dashboard.html.twig', [
            'categories' => $categoryRepo->findAll(),
            'users' => $userRepo->findAll(),
            'events' => $eventRepo->findAll(),
            'locations' => $locationRepo->findAll(),
        ]);
    }
    #[Route('/admin/user/{id}/edit', name: 'admin_user_edit')]
    public function editUser(
        int $id,
        UserRepository $userRepo,
        Request $request,
        EntityManagerInterface $em
    ): Response {
        $user = $userRepo->find($id);
        if (!$user) {
            throw $this->createNotFoundException('Użytkownik nie istnieje.');
        }

        $form = $this->createForm(EditUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Użytkownik zaktualizowany.');
            return $this->redirectToRoute('admin_dashboard');
        }

        return $this->render('admin/edit_user.html.twig', [
            'editUserForm' => $form->createView(),
            'user' => $user,
        ]);
    }

    #[Route('/admin/user/{id}/delete', name: 'admin_user_delete', methods: ['POST'])]
    public function deleteUser(
        int $id,
        UserRepository $userRepo,
        Request $request,
        EntityManagerInterface $em,
        CsrfTokenManagerInterface $csrfTokenManager
    ): RedirectResponse {
        $user = $userRepo->find($id);
        if (!$user) {
            throw $this->createNotFoundException('Użytkownik nie istnieje.');
        }

        $submittedToken = $request->request->get('_token');
        if (!$csrfTokenManager->isTokenValid(new \Symfony\Component\Security\Csrf\CsrfToken('delete_user' . $user->getId(), $submittedToken))) {
            throw $this->createAccessDeniedException('Nieprawidłowy token CSRF.');
        }

        $em->remove($user);
        $em->flush();

        $this->addFlash('success', 'Użytkownik usunięty.');
        return $this->redirectToRoute('admin_dashboard');
    }
    #[Route('/admin/event/{id}/edit', name: 'admin_event_edit')]
    public function editEvent(
        int $id,
        EventRepository $eventRepo,
        Request $request,
        EntityManagerInterface $em
    ): Response {
        $event = $eventRepo->find($id);
        if (!$event) {
            throw $this->createNotFoundException('Wydarzenie nie istnieje.');
        }

        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Wydarzenie zaktualizowane.');
            return $this->redirectToRoute('admin_dashboard');
        }

        return $this->render('admin/edit_event.html.twig', [
            'editEventForm' => $form->createView(),
            'event' => $event,
        ]);
    }




}
