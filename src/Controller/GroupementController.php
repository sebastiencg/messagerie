<?php

namespace App\Controller;

use App\Entity\Groupement;
use App\Form\GroupementType;
use App\Repository\GroupementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/groupement')]
class GroupementController extends AbstractController
{
    #[Route('/', name: 'app_groupement_index', methods: ['GET'])]
    public function index(GroupementRepository $groupementRepository): Response
    {
        return $this->render('groupement/index.html.twig', [
            'groupements' => $groupementRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_groupement_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $groupement = new Groupement();
        $form = $this->createForm(GroupementType::class, $groupement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($groupement);
            $entityManager->flush();

            return $this->redirectToRoute('app_groupement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('groupement/new.html.twig', [
            'groupement' => $groupement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_groupement_show', methods: ['GET'])]
    public function show(Groupement $groupement): Response
    {
        return $this->render('groupement/show.html.twig', [
            'groupement' => $groupement,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_groupement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Groupement $groupement, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(GroupementType::class, $groupement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_groupement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('groupement/edit.html.twig', [
            'groupement' => $groupement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_groupement_delete', methods: ['POST'])]
    public function delete(Request $request, Groupement $groupement, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$groupement->getId(), $request->request->get('_token'))) {
            $entityManager->remove($groupement);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_groupement_index', [], Response::HTTP_SEE_OTHER);
    }
}
