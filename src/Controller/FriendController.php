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
/*
 * Le contrôleur est défini comme une classe FriendController qui étend AbstractController.
 *  L'annotation #[Route] indique le préfixe d'URL pour toutes les routes définies dans ce contrôleur (/api/friend).
 */
#[Route('/api/friend')]
class FriendController extends AbstractController
{
    /*
     * Cette méthode est responsable de la gestion de la route /api/friend/, qui accepte uniquement les requêtes GET.
     *  Elle utilise le repository FriendRepository pour récupérer des informations sur les amis d'un utilisateur en particulier ($this->getUser()->getId()).
     *  Les résultats sont ensuite renvoyés au format JSON.
     * */
    #[Route('/', name: 'app_friend_index', methods: ['GET'])]
    public function index(FriendRepository $friendRepository): Response
    {
        return $this->json($friendRepository->custom2($this->getUser()->getId()),200,[],['groups'=>'friend:read-one']);

    }

    /*
     * Cette méthode est responsable de la gestion de la route /api/friend/new/{id}, qui accepte uniquement les requêtes GET.
     * Elle permet à un utilisateur de créer une nouvelle demande d'ami avec un autre utilisateur (spécifié par son ID).
     *  Si la demande d'ami n'existe pas déjà, elle est enregistrée en tant que nouvelle entité Friend.
     * */
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

    /*Cette méthode gère la route /api/friend/request/, qui accepte uniquement les requêtes GET.
     Elle permet de récupérer les demandes d'amitié en attente pour l'utilisateur actuel ($this->getUser()).
     * */
    #[Route('/request/', name: 'app_friend_request', methods: ['GET'])]
    public function request(FriendRepository $friendRepository): Response
    {
        return $this->json($friendRepository->findBy(["ofUser2"=>$this->getUser(),"validity"=>false]),200,[],['groups'=>'friend:read-one']);
    }

    /*Cette méthode est associée à la route /api/friend/request/valid/{id}, qui accepte uniquement les requêtes GET.
    Elle permet à l'utilisateur actuel d'accepter une demande d'ami spécifique envoyée par un autre utilisateur (spécifié par son ID).
     Elle change la validité de la demande d'amitié enregistrée en base de données pour marquer la demande comme acceptée.
     * */
    #[Route('/request/valid/{id}', name: 'app_friend_request_valid', methods: ['GET'])]
    public function requestValid(User $user,FriendRepository $friendRepository, EntityManagerInterface $entityManager): Response
    {
        $verification1=$friendRepository->findOneBy(["ofUser1"=>$user,"ofUser2"=>$this->getUser(),"validity"=>false]);
        $verification2=$friendRepository->findOneBy(["ofUser1"=>$this->getUser(),"ofUser2"=>$user,"validity"=>false]);

        if($verification1){
            $verification1->setValidity(true);
            $entityManager->persist($verification1);
            $entityManager->flush();
            return $this->json("accepté",200);
        }
        if($verification2){
            $verification1->setValidity(true);
            $entityManager->persist($verification2);
            $entityManager->flush();
            return $this->json("accepté",200);
        }

        return $this->json("error",200);
    }
    /*Cette méthode est liée à la route /api/friend/request/denied/{id}, qui accepte uniquement les requêtes DELETE.
     Elle permet à l'utilisateur actuel de refuser une demande d'ami spécifique envoyée par un autre utilisateur (spécifié par son ID).
     La demande d'amitié est supprimée de la base de données.
     * */
    #[Route('/request/denied/{id}', name: 'app_friend_request_denied', methods: ['DELETE'])]
    public function requestDenied(User $user,FriendRepository $friendRepository,EntityManagerInterface $entityManager): Response
    {
        $verification=$friendRepository->findOneBy(["ofUser1"=>$user,"ofUser2"=>$this->getUser(),"validity"=>false]);
        if($verification){
            $entityManager->remove($verification);
            $entityManager->flush();        }

        return $this->json("accepté",200);
    }
    /*Cette méthode est associée à la route /api/friend/{id}, qui accepte uniquement les requêtes DELETE.
    Elle permet à l'utilisateur actuel de supprimer une amitié existante avec un autre utilisateur (spécifié par son ID).
     * */
    #[Route('/{id}', name: 'app_friend_delete', methods: ['DELETE'])]
    public function delete(Request $request, Friend $friend, EntityManagerInterface $entityManager): Response
    {
        if ($friend->getOfUser1() === $this->getUser() || $friend->getOfUser2()=== $this->getUser()) {
            $entityManager->remove($friend);
            $entityManager->flush();
            return $this->json(' ami sup');

        }
        return $this->json(' error');
    }
}
