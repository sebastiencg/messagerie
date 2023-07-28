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
    #[Route('/request/', name: 'app_friend_request', methods: ['GET'])]
    public function request(FriendRepository $friendRepository): Response
    {
        return $this->json($friendRepository->findBy(["ofUser2"=>$this->getUser(),"validity"=>false]),200,[],['groups'=>'friend:read-one']);
    }
    #[Route('/request/valid/{id}', name: 'app_friend_request_valid', methods: ['GET'])]
    public function requestValid(User $user,FriendRepository $friendRepository): Response
    {
        $verification=$friendRepository->findOneBy(["ofUser1"=>$user,"ofUser2"=>$this->getUser(),"validity"=>false]);
        if($verification){
            $verification->setValidity(true);
        }

        return $this->json("accepté",200);
    }
    #[Route('/request/denied/{id}', name: 'app_friend_request_denied', methods: ['DELETE'])]
    public function requestDenied(User $user,FriendRepository $friendRepository,EntityManagerInterface $entityManager): Response
    {
        $verification=$friendRepository->findOneBy(["ofUser1"=>$user,"ofUser2"=>$this->getUser(),"validity"=>false]);
        if($verification){
            $entityManager->remove($verification);
            $entityManager->flush();        }

        return $this->json("accepté",200);
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
