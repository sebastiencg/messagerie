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


/*Le contrôleur est défini comme une classe GroupementController qui étend AbstractController.
L'annotation #[Route] indique le préfixe d'URL pour toutes les routes définies dans ce contrôleur (/api/groupement).
 * */
#[Route('/api/groupement')]
class GroupementController extends AbstractController
{
    /*Cette méthode est responsable de la gestion de la route /api/groupement/, qui accepte uniquement les requêtes GET.
     Elle utilise le repository GroupementRepository pour récupérer tous les groupements existants et les renvoie au format JSON.
     * */
    #[Route('/', name: 'app_groupement_index', methods: ['GET'])]
    public function index(GroupementRepository $groupementRepository): Response
    {
        return $this->json($groupementRepository->findAll(),200,[],['groups'=>'groupement:read-one']);
    }
/*Cette méthode est associée à la route /api/groupement/new, qui accepte uniquement les requêtes POST.
 Elle permet à l'utilisateur actuel de créer un nouveau groupement en fournissant les détails du groupement dans le corps de la requête au format JSON.
Le groupement est enregistré en tant qu'entité Groupement dans la base de données.
 * */
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
    /*Cette méthode est liée à la route /api/groupement/number/new/{id}, qui accepte uniquement les requêtes POST.
     Elle permet à l'utilisateur actuel d'ajouter un nouveau membre (utilisateur) au groupement spécifié par son ID.
     Le nouvel utilisateur est ajouté en tant qu'entité Validity dans la base de données.
     * */
    #[Route('/number/new/{id}', name: 'app_groupement_new_number', methods: ['POST'])]
    public function newNumber(Groupement $groupement,Request $request,UserRepository $userRepository, EntityManagerInterface $entityManager,SerializerInterface $serializer): Response
    {
        $json = $request->getContent();
        $user = $serializer->deserialize($json,User::class,'json');
        $user=$userRepository->findOneBy(["username"=>$user->getUsername()]);
        if($user){
            $validity = new Validity();
            $validity->setGroupe($groupement);
            $validity->setOfUser($user);
            $validity->setValidity(false);
            $entityManager->persist($validity);
            $entityManager->flush();
            return $this->json('demande envoyer');
        }

        return $this->json('error');
    }

    /*Cette méthode est liée à la route /api/groupement/number/delete/{id}, qui accepte uniquement les requêtes POST.
    Elle permet à l'utilisateur actuel de supprimer un membre (utilisateur) du groupement spécifié par son ID.
     L'utilisateur est supprimé du groupement enregistré dans la base de données.
     * */
    #[Route('/number/delete/{id}', name: 'app_groupement_delete_number', methods: ['POST'])]
    public function deleteNumber(Request $request,UserRepository $userRepository, EntityManagerInterface $entityManager,SerializerInterface $serializer,Groupement $groupement): Response
    {
        $json = $request->getContent();
        $user = $serializer->deserialize($json,User::class,'json');
        $user=$userRepository->findOneBy(["username"=>$user->getUsername()]);
        if($user&&$this->getUser()==$groupement->getMaster()){
            $groupement->removeNember($user);
            $entityManager->persist($groupement);
            $entityManager->flush();
            return $this->json('membre sup');
        }

        return $this->json('error');
    }
    /*Cette méthode est liée à la route /api/groupement/{id}, qui accepte uniquement les requêtes GET.
    Elle permet de récupérer les détails d'un groupement spécifique en fonction de son ID et de les renvoyer au format JSON.
     * */
    #[Route('/{id}', name: 'app_groupement_show', methods: ['GET'])]
    public function show(Groupement $groupement): Response
    {
        return $this->json($groupement,200,[],['groups'=>'groupement:read-all']);

    }
/*Cette méthode est liée à la route /api/groupement/{id}/edit, qui accepte uniquement les requêtes PUT.
Elle permet à l'utilisateur actuel (le "maître" du groupement) de modifier les détails d'un groupement spécifique en fonction de son ID.
 Les modifications sont enregistrées dans la base de données.
 * */
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
    /*Cette méthode est liée à la route /api/groupement/{id}, qui accepte uniquement les requêtes DELETE.
     Elle permet à l'utilisateur actuel (le "maître" du groupement) de supprimer un groupement spécifique en fonction de son ID.
    Le groupement est supprimé de la base de données.
     * */
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
