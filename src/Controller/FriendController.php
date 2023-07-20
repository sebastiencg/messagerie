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
        return $this->json($friendRepository->custom2($this->getUser()->getId()),200,[],['groups'=>'friend:read-one']);

    }

    #[Route('/new/{id}', name: 'app_friend_new', methods: ['GET'])]
    public function new(User $user, EntityManagerInterface $entityManager,FriendRepository $friendRepository): Response
    {
        ;
        if ($friendRepository->custom1($this->getUser()->getId(),$user->getId())){
            return $this->json('vous etes deja amis ou demande pas encore validé');
        }
        $friend = new Friend();
        $friend->setOfUser1($this->getUser());
        $friend->setOfUser2($user);
        $friend->setValidity(false);
        $entityManager->persist($friend);
        $entityManager->flush();
        return $this->json('demande d\' ami faite');
    }

    #[Route('/{id}', name: 'app_friend_delete', methods: ['DELETE'])]
    public function delete(Request $request, Friend $friend, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$friend->getId(), $request->request->get('_token'))) {
            $entityManager->remove($friend);
            $entityManager->flush();
        }

        return $this->json(' ami sup');
    }
}
