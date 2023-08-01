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

/*Le contrôleur est défini comme une classe ImageController qui étend AbstractController.
 L'annotation #[Route] indique le préfixe d'URL pour toutes les routes définies dans ce contrôleur (/api).
 * */
#[Route('/api')]
class ImageController extends AbstractController
{
    /*Cette méthode est liée à deux routes différentes. La première est /api/image/upload/friend/{id} et la seconde est /api/image/upload/groupement/{id}. Elle accepte des requêtes POST pour télécharger des images associées à un ami ou à un groupement spécifié par son ID.

La méthode effectue les actions suivantes :

Elle crée une nouvelle entité Image pour stocker les informations de l'image téléchargée.
Elle récupère le fichier d'image à partir de la requête et l'associe à l'entité Image.
Elle persiste l'entité Image dans l'EntityManager.
En fonction de la route actuelle, elle crée également une nouvelle entité Message pour stocker les détails du message associé à l'image.
Elle associe le message à l'auteur (l'utilisateur actuel) et au destinataire (soit l'ami spécifié, soit le groupement spécifié).
Elle persiste également l'entité Message dans l'EntityManager.
Enfin, elle renvoie une réponse JSON contenant des informations sur l'image téléchargée, notamment son ID et son URL.
L'URL de l'image est générée en utilisant le UploaderHelper fourni par le bundle VichUploader pour accéder à l'image téléchargé
     * */
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
