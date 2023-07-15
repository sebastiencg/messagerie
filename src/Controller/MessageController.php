<?php

namespace App\Controller;

use App\Entity\Message;
use App\Form\MessageType;
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

    #[Route('/new', name: 'app_message_new', methods: [ 'POST'])]
    public function new(Request $request,EntityManagerInterface $entityManager, SerializerInterface $serializer): Response
    {

        $json = $request->getContent();
        $message = $serializer->deserialize($json,Message::class,'json');
        $message->setCreatedAt(new \DateTimeImmutable());
        $message->setAuthor($this->getUser());
        $entityManager->persist($message);
        $entityManager->flush();
        return $this->json('bien envoyé');
    }

    #[Route('/{id}', name: 'app_message_show', methods: ['GET'])]
    public function show(Message $message): Response
    {
        return $this->json($message,200,[],['groups'=>'message:read-one']);
    }

    #[Route('/{id}/edit', name: 'app_message_edit', methods: ['PUT'])]
    public function edit(Request $request, Message $message, EntityManagerInterface $entityManager,SerializerInterface $serializer ): Response
    {
        $json = $request->getContent();
        $update = $serializer->deserialize($json,Message::class,'json');
        $message->setContent($update->getContent());
        $message->setAuthor($this->getUser());
        $entityManager->persist($message);
        $entityManager->flush();
        return $this->json('bien modifié');
    }

    #[Route('/{id}', name: 'app_message_delete', methods: ['DELETE'])]
    public function delete(Request $request, Message $message, EntityManagerInterface $entityManager): Response
    {
        if ($message) {
            $entityManager->remove($message);
            $entityManager->flush();
        }
        return $this->json('bien supprimé');
    }
}
