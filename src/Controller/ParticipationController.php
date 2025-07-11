<?php

namespace App\Controller;

use App\Entity\Participation;
use App\Entity\Reminder;
use App\Form\ParticipationType;
use App\Repository\ParticipationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Event;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
#[Route('/participation')]
final class ParticipationController extends AbstractController
{
    #[Route(name: 'app_participation_index', methods: ['GET'])]
    public function index(ParticipationRepository $participationRepository): Response
    {
        return $this->render('participation/index.html.twig', [
            'participations' => $participationRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_participation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $participation = new Participation();
        $form = $this->createForm(ParticipationType::class, $participation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($participation);
            $entityManager->flush();

            return $this->redirectToRoute('app_participation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('participation/new.html.twig', [
            'participation' => $participation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_participation_show', methods: ['GET'])]
    public function show(Participation $participation): Response
    {
        return $this->render('participation/show.html.twig', [
            'participation' => $participation,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_participation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Participation $participation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ParticipationType::class, $participation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_participation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('participation/edit.html.twig', [
            'participation' => $participation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_participation_delete', methods: ['POST'])]
    public function delete(Request $request, Participation $participation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$participation->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($participation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_participation_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/join/{eventId}', name: 'app_participation_join', methods: ['POST'])]
    public function join(
        int $eventId,
        EntityManagerInterface $em,
        Request $request,
        CsrfTokenManagerInterface $csrfTokenManager
    ): RedirectResponse {
        $user = $this->getUser();
        if ($user instanceof \App\Security\AuthUserDecorator) {
            $user = $user->getUser();
        }
        if (!$user) {
            $this->addFlash('danger', 'Musisz być zalogowany, aby dołączyć.');
            return $this->redirectToRoute('app_login');
        }

        $submittedToken = $request->request->get('_token');
        if (!$csrfTokenManager->isTokenValid(new \Symfony\Component\Security\Csrf\CsrfToken('join_event' . $eventId, $submittedToken))) {
            throw $this->createAccessDeniedException('Nieprawidłowy token CSRF.');
        }

        $event = $em->getRepository(Event::class)->find($eventId);
        if (!$event) {
            $this->addFlash('danger', 'Wydarzenie nie istnieje.');
            return $this->redirectToRoute('app_event_index');
        }

        // Sprawdź, czy użytkownik już jest uczestnikiem
        foreach ($event->getParticipations() as $participation) {
            if ($participation->getParticipant() === $user) {
                $this->addFlash('info', 'Już jesteś uczestnikiem tego wydarzenia.');
                return $this->redirectToRoute('app_event_show', ['id' => $eventId]);
            }
        }

        $participation = new Participation();
        $participation->setEvent($event);
        $participation->setParticipant($user);
        $participation->setJoinedAt(new \DateTimeImmutable());

        $em->persist($participation);



        // Dodaj przypomnienie
        $reminder = new Reminder();
        $reminder->setRecipient($user);
        $reminder->setEvent($event);
        // Możesz ustawić sentAt na null, bo mail jeszcze nie został wysłany
        $reminder->setRemindAt(new \DateTimeImmutable()); // <-- ustaw datę przypomnienia
        $reminder->setSent(false);

        $em->persist($reminder);

        $em->flush();

        $this->addFlash('success', 'Dołączono do wydarzenia!');
        return $this->redirectToRoute('app_event_show', ['id' => $eventId]);
    }


    #[Route('/leave/{eventId}', name: 'app_participation_leave', methods: ['POST'])]
    public function leave(
        int $eventId,
        EntityManagerInterface $em,
        Request $request,
        CsrfTokenManagerInterface $csrfTokenManager
    ): RedirectResponse {
        $user = $this->getUser();
        if ($user instanceof \App\Security\AuthUserDecorator) {
            $user = $user->getUser();
        }
        if (!$user) {
            $this->addFlash('danger', 'Musisz być zalogowany, aby opuścić wydarzenie.');
            return $this->redirectToRoute('app_login');
        }

        $submittedToken = $request->request->get('_token');
        if (!$csrfTokenManager->isTokenValid(new \Symfony\Component\Security\Csrf\CsrfToken('leave_event' . $eventId, $submittedToken))) {
            throw $this->createAccessDeniedException('Nieprawidłowy token CSRF.');
        }

        $event = $em->getRepository(\App\Entity\Event::class)->find($eventId);
        if (!$event) {
            $this->addFlash('danger', 'Wydarzenie nie istnieje.');
            return $this->redirectToRoute('app_event_index');
        }

        foreach ($event->getParticipations() as $participation) {
            if ($participation->getParticipant() === $user) {
                $em->remove($participation);
                $em->flush();
                $this->addFlash('success', 'Opuściłeś wydarzenie.');
                return $this->redirectToRoute('app_event_show', ['id' => $eventId]);
            }
        }

        $this->addFlash('info', 'Nie jesteś uczestnikiem tego wydarzenia.');
        return $this->redirectToRoute('app_event_show', ['id' => $eventId]);
    }
}
