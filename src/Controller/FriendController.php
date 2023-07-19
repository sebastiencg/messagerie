<?php

namespace App\Controller;

use App\Entity\Friend;
use App\Entity\Groupement;
use App\Entity\User;
use App\Form\FriendType;
use App\Repository\FriendRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/friend')]
class FriendController extends AbstractController
{
    #[Route('/', name: 'app_friend_index', methods: ['GET'])]
    public function index(FriendRepository $friendRepository): Response
    {
        return $this->json($friendRepository->findBy([]),200,);

    }

    #[Route('/new/{id}', name: 'app_friend_new', methods: ['GET', 'POST'])]
    public function new(Request $request,User $user,SerializerInterface $serializer, EntityManagerInterface $entityManager): Response
    {
        $json = $request->getContent();
        $friend = $serializer->deserialize($json,Friend::class,'json');
        $friend->setOfUser1($this->getUser());
        $friend->setOfUser2($user);
        dd($friend);
        $entityManager->persist($friend);
        $entityManager->flush();
        return $this->json('groupe crée');
    }

    #[Route('/{id}', name: 'app_friend_show', methods: ['GET'])]
    public function show(Friend $friend): Response
    {
        return $this->render('friend/show.html.twig', [
            'friend' => $friend,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_friend_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Friend $friend, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(FriendType::class, $friend);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_friend_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('friend/edit.html.twig', [
            'friend' => $friend,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_friend_delete', methods: ['POST'])]
    public function delete(Request $request, Friend $friend, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$friend->getId(), $request->request->get('_token'))) {
            $entityManager->remove($friend);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_friend_index', [], Response::HTTP_SEE_OTHER);
    }
}
