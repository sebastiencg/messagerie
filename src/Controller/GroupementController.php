<?php

namespace App\Controller;

use App\Entity\Groupement;
use App\Entity\Message;
use App\Entity\User;
use App\Entity\Validity;
use App\Form\GroupementType;
use App\Repository\GroupementRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/groupement')]
class GroupementController extends AbstractController
{
    #[Route('/', name: 'app_groupement_index', methods: ['GET'])]
    public function index(GroupementRepository $groupementRepository): Response
    {
        return $this->json($groupementRepository->findAll(),200,[],['groups'=>'groupement:read-one']);
    }

    #[Route('/new', name: 'app_groupement_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager,SerializerInterface $serializer): Response
    {
        $json = $request->getContent();
        $groupe = $serializer->deserialize($json,Groupement::class,'json');
        $groupe->setCreatedAt(new \DateTimeImmutable());
        $groupe->setMaster($this->getUser());
        $groupe->setMember($this->getUser());
        $entityManager->persist($groupe);
        $entityManager->flush();
        return $this->json('groupe crée');
    }
    #[Route('/number/new/{id}', name: 'app_groupement_new_number', methods: ['POST'])]
    public function newNumber(Validity $validity,Request $request,UserRepository $userRepository, EntityManagerInterface $entityManager,SerializerInterface $serializer,Groupement $groupement): Response
    {
        $json = $request->getContent();
        $user = $serializer->deserialize($json,User::class,'json');
        $user=$userRepository->findOneBy(["username"=>$user->getUsername()]);
        if($user){
            $validity->setGroupe($groupement);
            $validity->setOfUser($user);
            $validity->isValidity(false);
            $entityManager->persist($validity);
            $entityManager->flush();
            return $this->json('demande envoyer');
        }

        return $this->json('error');
    }

    #[Route('/number/delete/{id}', name: 'app_groupement_delete_number', methods: ['POST'])]
    public function deleteNumber(Request $request,UserRepository $userRepository, EntityManagerInterface $entityManager,SerializerInterface $serializer,Groupement $groupement): Response
    {
        $json = $request->getContent();
        $user = $serializer->deserialize($json,User::class,'json');
        $user=$userRepository->findOneBy(["username"=>$user->getUsername()]);
        if($user){
            $groupement->removeNember($user);
            $entityManager->persist($groupement);
            $entityManager->flush();
            return $this->json('membre sup');
        }

        return $this->json('error');
    }
    #[Route('/{id}', name: 'app_groupement_show', methods: ['GET'])]
    public function show(Groupement $groupement): Response
    {
        return $this->json($groupement,200,[],['groups'=>'groupement:read-all']);

    }

    #[Route('/{id}/edit', name: 'app_groupement_edit', methods: ['PUT'])]
    public function edit(Request $request, Groupement $groupement,SerializerInterface $serializer, EntityManagerInterface $entityManager): Response
    {
        if($this->getUser() === $groupement->getMaster()){
            $json = $request->getContent();
            $update = $serializer->deserialize($json,Groupement::class,'json');
            $groupement->setName($update->getName());
            $entityManager->persist($groupement);
            $entityManager->flush();
            return $this->json('bien modifié');
        }
        return $this->json('error');

    }

    #[Route('/{id}', name: 'app_groupement_delete', methods: ['DELETE'])]
    public function delete(Groupement $groupement, EntityManagerInterface $entityManager): Response
    {
        {
            if ($groupement->getMaster() ===$this->getUser()) {
                $entityManager->remove($groupement);
                $entityManager->flush();
            }
            return $this->json('bien supprimé');
        }
    }
}
