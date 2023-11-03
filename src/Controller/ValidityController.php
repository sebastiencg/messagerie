<?php

namespace App\Controller;

use App\Entity\Groupement;
use App\Entity\Validity;
use App\Repository\ValidityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
#[Route('/api/groupement/validity')]
/*Le code commence par définir un espace de noms pour le contrôleur (App\Controller) et importe différentes classes utilisées dans ce contrôleur,
 telles que les entités (Groupement, Validity), le repository ValidityRepository, l'EntityManagerInterface, le Response, et l'annotation Route.
 * */
class ValidityController extends AbstractController
{
    /*Cette méthode est responsable de la gestion de la route /api/groupement/validity/show, qui accepte uniquement les requêtes GET.
     Elle utilise le repository ValidityRepository pour récupérer la demande de groupement associée à l'utilisateur actuel (l'utilisateur connecté) et la renvoie au format JSON.
     * */
    #[Route('/show', name: 'app_validity_index', methods: ['GET'])]
    public function index(ValidityRepository $validityRepository): Response
    {
        return $this->json($validityRepository->findBy(['ofUser'=>$this->getUser()]),200,[],['groups'=>'groupement:read-one']);

    }
    /*Cette méthode est responsable de la gestion de la route /api/groupement/validity/{id}, qui accepte uniquement les requêtes GET.
     Elle permet à l'utilisateur actuel de valider la demande de groupement spécifiée par son ID (paramètre de la route).
     Si l'utilisateur actuel est l'utilisateur associé à la demande de groupement, cette méthode ajoute cet utilisateur au groupe en utilisant la méthode addNember de l'entité Groupement.
     * */
    #[Route('/{id}', name: 'app_validity_valid', methods: ['GET'])]
    public function valid(ValidityRepository $validityRepository,Validity $validity ,EntityManagerInterface $entityManager): Response
    {
        if ($validity->getOfUser() ===$this->getUser()){
            $validity->getGroupe()->addNember($validity->getOfUser());
            $entityManager->remove($validity);
            $entityManager->flush();

        }
        return $this->json("groupe accepte",200,);
    }
    /*Cette méthode est responsable de la gestion de la route /api/groupement/validity/{id}, qui accepte uniquement les requêtes DELETE.
    Elle permet à l'utilisateur actuel de refuser la demande de groupement spécifiée par son ID (paramètre de la route).
    Si l'utilisateur actuel est l'utilisateur associé à la demande de groupement, cette méthode supprime l'entité Validity associée à la demande de groupement.
     * */
    #[Route('/{id}', name: 'app_validity_delete', methods: ['DELETE'])]
    public function denied(Validity $validity,EntityManagerInterface $entityManager): Response
    {
        if ($validity->getOfUser() ===$this->getUser()){
            $entityManager->remove($validity);
            $entityManager->flush();
        }
        return $this->json("groupe supprimer",200,);
    }
}
