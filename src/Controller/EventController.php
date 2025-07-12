<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\EventType;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Kontroler obsługujący operacje na wydarzeniach (lista, szczegóły, tworzenie, edycja, usuwanie).
 */
#[Route('/event')]
final class EventController extends AbstractController
{
    /**
     * Wyświetla listę wydarzeń z możliwością filtrowania.
     *
     * @param Request $request
     * @param EventRepository $eventRepository
     * @return Response
     */
    #[Route(name: 'app_event_index', methods: ['GET', 'POST'])]
    public function index(Request $request, EventRepository $eventRepository): Response
    {
        $form = $this->createForm(\App\Form\EventFilterType::class, null, ['method' => 'GET']);
        $form->handleRequest($request);

        $filters = $form->isSubmitted() && $form->isValid() ? $form->getData() : [];
        $events = $eventRepository->findByFilters($filters);

        return $this->render('event/index.html.twig', [
            'events' => $events,
            'filterForm' => $form->createView(),
        ]);
    }

    /**
     * Tworzy nowe wydarzenie.
     *
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/new', name: 'app_event_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $event = new Event();
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            if ($user instanceof \App\Security\AuthUserDecorator) {
                $user = $user->getUser();
            }
            $event->setCreatedByUser($user);
            $entityManager->persist($event);
            $entityManager->flush();

            return $this->redirectToRoute('app_event_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('event/new.html.twig', [
            'event' => $event,
            'form' => $form,
        ]);
    }

    /**
     * Wyświetla szczegóły wybranego wydarzenia.
     *
     * @param Event $event
     * @return Response
     */
    #[Route('/{id}', name: 'app_event_show', methods: ['GET'])]
    public function show(Event $event): Response
    {
        return $this->render('event/show.html.twig', [
            'event' => $event,
        ]);
    }

    /**
     * Edytuje wybrane wydarzenie.
     *
     * @param Request $request
     * @param Event $event
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/{id}/edit', name: 'app_event_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Event $event, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_AUTH_USER');
        $currentUser = $this->getUser();
        if ($currentUser instanceof \App\Security\AuthUserDecorator) {
            $currentUser = $currentUser->getUser();
        }
        if (!$this->isGranted('ROLE_ADMIN') && $event->getCreatedByUser()?->getId() !== $currentUser?->getId()) {
            throw $this->createAccessDeniedException('Możesz edytować tylko własne wydarzenia.');
        }
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_event_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('event/edit.html.twig', [
            'event' => $event,
            'form' => $form,
        ]);
    }

    /**
     * Usuwa wybrane wydarzenie.
     *
     * @param Request $request
     * @param Event $event
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/{id}/delete', name: 'app_event_delete', methods: ['GET','POST'])]
    public function delete(Request $request, Event $event, EntityManagerInterface $entityManager): Response
    {
        $currentUser = $this->getUser();
        if ($currentUser instanceof \App\Security\AuthUserDecorator) {
            $currentUser = $currentUser->getUser();
        }
        if (!$this->isGranted('ROLE_ADMIN') && $event->getCreatedByUser()?->getId() !== $currentUser?->getId()) {
            throw $this->createAccessDeniedException('Możesz usuwać tylko własne wydarzenia.');
        }
        $submittedToken = $request->request->get('_token');
        if (!$this->isCsrfTokenValid('delete_event' . $event->getId(), $submittedToken)) {
            throw $this->createAccessDeniedException('Nieprawidłowy token CSRF.');
        }

        $entityManager->remove($event);
        $entityManager->flush();

        return $this->redirectToRoute('app_event_index', [], Response::HTTP_SEE_OTHER);
    }
}
