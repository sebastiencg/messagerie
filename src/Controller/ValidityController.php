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

class ValidityController extends AbstractController
{
    #[Route('/', name: 'app_validity_index', methods: ['GET'])]
    public function index(ValidityRepository $validityRepository): Response
    {
        return $this->json($validityRepository->findOneBy(['ofUser'=>$this->getUser()]),200,[],['groups'=>'groupement:read-one']);
    }
    #[Route('/{id}', name: 'app_validity_valid', methods: ['GET'])]
    public function valid(ValidityRepository $validityRepository,Validity $validity): Response
    {
        if ($validity->getOfUser() ===$this->getUser()){
            $validity->getGroupe()->addNember($validity->getOfUser());
        }
        return $this->json("groupe accepte",200,);
    }
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
