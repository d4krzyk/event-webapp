<?php

namespace App\Controller;

use App\Entity\Location;
use App\Form\LocationType;
use App\Repository\LocationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Kontroler obsługujący operacje CRUD na lokalizacjach wydarzeń.
 */
#[Route('/location')]
final class LocationController extends AbstractController
{
    /**
     * Wyświetla listę wszystkich lokalizacji.
     *
     * @param LocationRepository $locationRepository Repozytorium lokalizacji
     * @return Response Odpowiedź HTTP z widokiem listy lokalizacji
     */
    #[Route(name: 'app_location_index', methods: ['GET'])]
    public function index(LocationRepository $locationRepository): Response
    {
        return $this->render('location/index.html.twig', [
            'locations' => $locationRepository->findAll(),
        ]);
    }

    /**
     * Tworzy nową lokalizację.
     *
     * @param Request $request Obiekt żądania HTTP
     * @param EntityManagerInterface $entityManager Menedżer encji Doctrine
     * @return Response Odpowiedź HTTP z formularzem lub przekierowaniem
     */
    #[Route('/new', name: 'app_location_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $location = new Location();
        $form = $this->createForm(LocationType::class, $location);
        $form->handleRequest($request);

        // Pobierz event_id z query params, np. /location/new?event_id=5
        $eventId = $request->query->get('event_id');



        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($location);
            $entityManager->flush();

            return $this->redirectToRoute('app_location_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('location/new.html.twig', [
            'location' => $location,
            'form' => $form,
            'event_id' => $eventId,
        ]);
    }

    /**
     * Wyświetla szczegóły wybranej lokalizacji.
     *
     * @param Location $location Wybrana lokalizacja
     * @return Response Odpowiedź HTTP z widokiem szczegółów
     */
    #[Route('/{id}', name: 'app_location_show', methods: ['GET'])]
    public function show(Location $location): Response
    {
        return $this->render('location/show.html.twig', [
            'location' => $location,
        ]);
    }

    /**
     * Edytuje istniejącą lokalizację.
     *
     * @param Request $request Obiekt żądania HTTP
     * @param Location $location Edytowana lokalizacja
     * @param EntityManagerInterface $entityManager Menedżer encji Doctrine
     * @return Response Odpowiedź HTTP z formularzem lub przekierowaniem
     */
    #[Route('/{id}/edit', name: 'app_location_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Location $location, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_AUTH_USER');
        $form = $this->createForm(LocationType::class, $location);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_location_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('location/edit.html.twig', [
            'location' => $location,
            'form' => $form,
        ]);
    }

    /**
     * Usuwa wybraną lokalizację.
     *
     * @param Request $request Obiekt żądania HTTP
     * @param Location $location Usuwana lokalizacja
     * @param EntityManagerInterface $entityManager Menedżer encji Doctrine
     * @return Response Przekierowanie po usunięciu
     */
    #[Route('/{id}', name: 'app_location_delete', methods: ['POST'])]
    public function delete(Request $request, Location $location, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_AUTH_USER');
        if ($this->isCsrfTokenValid('delete'.$location->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($location);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_location_index', [], Response::HTTP_SEE_OTHER);
    }
}
