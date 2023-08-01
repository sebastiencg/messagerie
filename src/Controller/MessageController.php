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
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/*Le contrôleur est défini comme une classe MessageController qui étend AbstractController.
 L'annotation #[Route] indique le préfixe d'URL pour toutes les routes définies dans ce contrôleur (/api/message).
 * */
#[Route('/api/message')]
class MessageController extends AbstractController
{
    /*Cette méthode est responsable de la gestion de la route /api/message/, qui accepte uniquement les requêtes GET.
     Elle utilise le repository MessageRepository pour récupérer tous les messages existants et les pagine en utilisant le PaginatorInterface.
    * Les résultats paginés sont renvoyés au format JSON.
     * */
    #[Route('/', name: 'app_message_index', methods: ['GET'])]
    public function index(MessageRepository $messageRepository,PaginatorInterface $paginator,Request $request): Response
    {
        $pagination = $paginator->paginate(
            $messageRepository->findAll(), /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            20 /*limit per page*/
        );
        return $this->json($pagination,200,[],['groups'=>'message:read-one']);

    }
/*Cette méthode est liée à la route /api/message/new/friend/{id}, qui accepte uniquement les requêtes POST.
 Elle permet à l'utilisateur actuel d'envoyer un nouveau message à un ami spécifié par son ID.
Elle vérifie d'abord que les deux utilisateurs sont amis (en utilisant le FriendRepository).
 Si oui, elle crée un nouvel objet Message, l'associe à l'auteur (l'utilisateur actuel) et au destinataire (l'ami spécifié), et le persiste dans l'EntityManager.
 * */
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
    /*ette méthode est liée à la route /api/message/new/groupement/{id}, qui accepte uniquement les requêtes POST.
     Elle permet à l'utilisateur actuel d'envoyer un nouveau message à un groupement spécifié par son ID.
    Avant d'envoyer le message, elle vérifie que l'utilisateur actuel est membre du groupement.
     Si oui, elle crée un nouvel objet Message, l'associe à l'auteur (l'utilisateur actuel) et au groupement spécifié, et le persiste dans l'EntityManager.
     * */
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
            return $this->json('bien envoyé aux groupe '. $groupement->getName(),200);
        }
        return $this->json('error');

    }
/*Cette méthode est liée à la route /api/message/friend/{id}, qui accepte uniquement les requêtes GET.
 Elle permet de récupérer les messages échangés entre l'utilisateur actuel et un ami spécifié par son ID.
Elle pagine les résultats en utilisant le PaginatorInterface et les renvoie au format JSON.
 * */
    #[Route('/friend/{id}', name: 'app_message_show_friend', methods: ['GET'])]
    public function showFriend(User $user,MessageRepository $messageRepository,PaginatorInterface $paginator,Request $request): Response
    {
        $pagination = $paginator->paginate(
            $messageRepository->custom1($this->getUser()->getId(),$user->getId()), /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            20 /*limit per page*/
        );

        return $this->json($pagination,200,[],['groups'=>'message:read-one']);
    }
    /*Cette méthode est liée à la route /api/message/groupement/{id}, qui accepte uniquement les requêtes GET.
     Elle permet de récupérer les messages échangés dans un groupement spécifié par son ID.
     Avant de renvoyer les messages, elle vérifie que l'utilisateur actuel est membre du groupement.
     Elle pagine ensuite les résultats en utilisant le PaginatorInterface et les renvoie au format JSON.
     * */
    #[Route('/groupement/{id}', name: 'app_message_show_groupe', methods: ['GET'])]
    public function showGroupe(Groupement $groupement,PaginatorInterface $paginator,Request $request): Response
    {
        if(in_array($this->getUser(),$groupement->getNember()->getValues())){
            $pagination = $paginator->paginate(
                $groupement->getMessages(), /* query NOT result */
                $request->query->getInt('page', 1), /*page number*/
                20 /*limit per page*/
            );
            return $this->json($pagination,200,[],['groups'=>'message:read-one'],);
        }
        return $this->json('error');
    }
/*Cette méthode est liée à la route /api/message/{id}/edit, qui accepte uniquement les requêtes PUT.
 Elle permet à l'utilisateur actuel de modifier le contenu d'un message spécifié par son ID, à condition qu'il soit l'auteur original du message.
Le contenu du message est modifié en utilisant les données fournies dans le corps de la requête au format JSON.
 * */
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
/*Cette méthode est liée à la route /api/message/{id}, qui accepte uniquement les requêtes DELETE.
Elle permet à l'utilisateur actuel de supprimer un message spécifié par son ID, à condition qu'il soit l'auteur original du message.
Le message est supprimé de la base de données.
 * */
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
