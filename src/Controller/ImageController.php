<?php

namespace App\Controller;

use App\Entity\Groupement;
use App\Entity\Image;
use App\Entity\Message;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

#[Route('/api')]
class ImageController extends AbstractController
{
    #[Route('/image/upload/friend/{id}', name: 'app_image_friend')]
    #[Route('/image/upload/groupement/{id}', name: 'app_image_groupement')]
    public function imageFriend(User $user,Groupement $groupement,UploaderHelper $helper,Request $request, EntityManagerInterface $manager): Response
    {
        $image = new Image();
        $file = $request->files->get('image');
        $image->setImageFile($file);
        $manager->persist($image);
        //--------------------
        $message=new Message();
        $message->setImage($image);
        $message->setContent($image->getImageName());
        $message->setAuthor($this->getUser());
        $message->setCreatedAt(new \DateTimeImmutable());
        if ($request->attributes->get('_route')==="app_image_friend"){
            $message->setRecipient($user);
        }
        else{
            $message->setGroupement($groupement);
        }

        $manager->persist($message);
        //-----------------------
        $manager->flush();
        $response = [
            "message"=>"bravo pour ton upload",
            "idImage"=>$image->getId(),
            "image"=>"https://localhost:8000".$helper->asset($image)

        ];

        return $this->json($response,200);    }
   }
