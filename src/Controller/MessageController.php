<?php

namespace App\Controller;

use App\Entity\Groupement;
use App\Entity\Message;
use App\Entity\User;
use App\Form\MessageType;
use App\Repository\FriendRepository;
use App\Repository\GroupementRepository;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/message')]
class MessageController extends AbstractController
{
    #[Route('/', name: 'app_message_index', methods: ['GET'])]
    public function index(MessageRepository $messageRepository): Response
    {
        return $this->json($messageRepository->findAll(),200,[],['groups'=>'message:read-one']);

    }

    #[Route('/new/friend/{id}', name: 'app_message_new_friend', methods: [ 'POST'])]
    public function newFriend(User $user,Request $request,FriendRepository $friendRepository,EntityManagerInterface $entityManager, SerializerInterface $serializer): Response
    {

        $friend=$friendRepository->custom1($this->getUser()->getId(),$user->getId());
        if($friend[0]->isValidity()){
            $json = $request->getContent();
            $message = $serializer->deserialize($json,Message::class,'json');
            $message->setCreatedAt(new \DateTimeImmutable());
            $message->setAuthor($this->getUser());
            $message->setRecipient($user);
            $entityManager->persist($message);
            $entityManager->flush();
            return $this->json('bien envoyé a '.$user->getUsername());
        }
        return $this->json('vous etes pas ami avec '.$user->getUsername());

    }
    #[Route('/new/groupement/{id}', name: 'app_message_new_groupe', methods: [ 'POST'])]
    public function newGroupement(Groupement$groupement,Request $request,EntityManagerInterface $entityManager, SerializerInterface $serializer): Response
    {
        if(in_array($this->getUser(),$groupement->getNember()->getValues())){
            $json = $request->getContent();
            $message = $serializer->deserialize($json,Message::class,'json');
            $message->setCreatedAt(new \DateTimeImmutable());
            $message->setAuthor($this->getUser());
            $message->setGroupement($groupement);
            $entityManager->persist($message);
            $entityManager->flush();
            return $this->json('bien envoyé aux groupe '. $groupement->getName());
        }
        return $this->json('error');

    }

    #[Route('/{id}', name: 'app_message_show', methods: ['GET'])]
    public function show(Message $message): Response
    {
        return $this->json($message,200,[],['groups'=>'message:read-one']);
    }

    #[Route('/{id}/edit', name: 'app_message_edit', methods: ['PUT'])]
    public function edit(Request $request, Message $message, EntityManagerInterface $entityManager,SerializerInterface $serializer ): Response
    {
        if ($message->getAuthor() === $this->getUser()){
            $json = $request->getContent();
            $update = $serializer->deserialize($json,Message::class,'json');
            $message->setContent($update->getContent());
            $message->setAuthor($this->getUser());
            $entityManager->persist($message);
            $entityManager->flush();
            return $this->json('bien modifié');
        }
        return $this->json('error');

    }

    #[Route('/{id}', name: 'app_message_delete', methods: ['DELETE'])]
    public function delete(Request $request, Message $message, EntityManagerInterface $entityManager): Response
    {
        if ($message->getAuthor() ===$this->getUser()) {
            $entityManager->remove($message);
            $entityManager->flush();
        }
        return $this->json('bien supprimé');
    }
}
