<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Kontroler obsługujący operacje CRUD na kategoriach wydarzeń.
 */
#[Route('/category')]
final class CategoryController extends AbstractController
{
    /**
     * Wyświetla listę wszystkich kategorii.
     *
     * @param CategoryRepository $categoryRepository Repozytorium kategorii
     * @return Response Odpowiedź HTTP z widokiem listy kategorii
     */
    #[Route(name: 'app_category_index', methods: ['GET'])]
    public function index(CategoryRepository $categoryRepository): Response
    {
        return $this->render('category/index.html.twig', [
            'categories' => $categoryRepository->findAll(),
        ]);
    }

    /**
     * Tworzy nową kategorię.
     *
     * @param Request $request Obiekt żądania HTTP
     * @param EntityManagerInterface $entityManager Menedżer encji Doctrine
     * @return Response Odpowiedź HTTP z formularzem lub przekierowaniem
     */
    #[Route('/new', name: 'app_category_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($category);
            $entityManager->flush();

            return $this->redirectToRoute('admin_dashboard', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('category/new.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    /**
     * Wyświetla szczegóły wybranej kategorii.
     *
     * @param Category $category Wybrana kategoria
     * @return Response Odpowiedź HTTP z widokiem szczegółów
     */
    #[Route('/{id}', name: 'app_category_show', methods: ['GET'])]
    public function show(Category $category): Response
    {
        return $this->render('category/show.html.twig', [
            'category' => $category,
        ]);
    }

    /**
     * Edytuje istniejącą kategorię.
     *
     * @param Request $request Obiekt żądania HTTP
     * @param Category $category Edytowana kategoria
     * @param EntityManagerInterface $entityManager Menedżer encji Doctrine
     * @return Response Odpowiedź HTTP z formularzem lub przekierowaniem
     */
    #[Route('/{id}/edit', name: 'app_category_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Category $category, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('admin_dashboard', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('category/edit.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    /**
     * Usuwa wybraną kategorię.
     *
     * @param Request $request Obiekt żądania HTTP
     * @param Category $category Usuwana kategoria
     * @param EntityManagerInterface $entityManager Menedżer encji Doctrine
     * @return Response Przekierowanie po usunięciu
     */
    #[Route('/{id}/delete', name: 'app_category_delete', methods: ['GET','POST'])]
    public function delete(Request $request, Category $category, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        if ($this->isCsrfTokenValid('delete'.$category->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($category);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_dashboard', [], Response::HTTP_SEE_OTHER);
    }
}
