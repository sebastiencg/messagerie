# Documentation de l'API

L'API que vous avez développée propose plusieurs fonctionnalités pour gérer des amis, des groupements, des messages, et des demandes de groupement. Voici la description de chaque méthode exposée par votre API :

### Gestion des Amis

1. **Obtenir la liste d'amis d'un utilisateur**
   - Route : `/api/friend/`
   - Méthode HTTP : GET
   - Description : Récupère les informations sur les amis de l'utilisateur actuel (connecté) en renvoyant les données au format JSON.

2. **Créer une demande d'ami**
   - Route : `/api/friend/new/{id}`
   - Méthode HTTP : GET
   - Description : Permet à l'utilisateur actuel de créer une nouvelle demande d'ami avec un autre utilisateur spécifié par son ID. Si la demande d'ami n'existe pas déjà, elle est enregistrée en tant que nouvelle entité "Friend".

3. **Obtenir les demandes d'amitié en attente**
   - Route : `/api/friend/request/`
   - Méthode HTTP : GET
   - Description : Récupère les demandes d'amitié en attente pour l'utilisateur actuel (connecté) et les renvoie au format JSON.

4. **Accepter une demande d'ami**
   - Route : `/api/friend/request/valid/{id}`
   - Méthode HTTP : GET
   - Description : Permet à l'utilisateur actuel d'accepter une demande d'ami spécifique envoyée par un autre utilisateur. Cela change la validité de la demande d'amitié enregistrée en base de données pour la marquer comme acceptée.

5. **Refuser une demande d'ami**
   - Route : `/api/friend/request/denied/{id}`
   - Méthode HTTP : DELETE
   - Description : Permet à l'utilisateur actuel de refuser une demande d'ami spécifique envoyée par un autre utilisateur. La demande d'amitié est supprimée de la base de données.

6. **Supprimer une amitié existante**
   - Route : `/api/friend/{id}`
   - Méthode HTTP : DELETE
   - Description : Permet à l'utilisateur actuel de supprimer une amitié existante avec un autre utilisateur spécifié par son ID.

### Gestion des Groupements

7. **Obtenir la liste des groupements**
   - Route : `/api/groupement/`
   - Méthode HTTP : GET
   - Description : Utilise le repository GroupementRepository pour récupérer tous les groupements existants et les renvoie au format JSON.

8. **Créer un nouveau groupement**
   - Route : `/api/groupement/new`
   - Méthode HTTP : POST
   - Description : Permet à l'utilisateur actuel de créer un nouveau groupement en fournissant les détails du groupement dans le corps de la requête au format JSON. Le groupement est enregistré en tant qu'entité "Groupement" dans la base de données.
   - Exemple de corps de requête au format JSON :{"name": "nom_groupe"}


9. **Ajouter un nouveau membre au groupement**
   - Route : `/api/groupement/number/new/{id}`
   - Méthode HTTP : POST
   - Description : Permet à l'utilisateur actuel d'ajouter un nouveau membre (utilisateur) au groupement spécifié par son ID. Le nouvel utilisateur est ajouté en tant qu'entité "Validity" dans la base de données.
   - Exemple de corps de requête au format JSON :{"username": "nom_utilisateur"}

10. **Supprimer un membre du groupement**
    - Route : `/api/groupement/number/delete/{id}`
    - Méthode HTTP : POST
    - Description : Permet à l'utilisateur actuel de supprimer un membre (utilisateur) du groupement spécifié par son ID. L'utilisateur est supprimé du groupement enregistré dans la base de données.
   - Exemple de corps de requête au format JSON :{"username": "nom_utilisateur"}

11. **Obtenir les détails d'un groupement spécifique**
    - Route : `/api/groupement/{id}`
    - Méthode HTTP : GET
    - Description : Permet de récupérer les détails d'un groupement spécifique en fonction de son ID et de les renvoyer au format JSON.

12. **Modifier les détails d'un groupement**
    - Route : `/api/groupement/{id}/edit`
    - Méthode HTTP : PUT
    - Description : Permet à l'utilisateur actuel (le "maître" du groupement) de modifier les détails d'un groupement spécifique en fonction de son ID. Les modifications sont enregistrées dans la base de données.
   - Exemple de corps de requête au format JSON :{"username": "nom_groupe"}

13. **Supprimer un groupement**
    - Route : `/api/groupement/{id}`
    - Méthode HTTP : DELETE
    - Description : Permet à l'utilisateur actuel (le "maître" du groupement) de supprimer un groupement spécifique en fonction de son ID. Le groupement est supprimé de la base de données.

### Gestion des Images

14. **Télécharger une image associée à un ami ou un groupement**
    - Route : `/api/image/upload/friend/{id}` ou `/api/image/upload/groupement/{id}`
    - Méthode HTTP : POST
    - Description : Cette méthode permet de télécharger des images associées à un ami ou un groupement spécifié par son ID. Elle crée une nouvelle entité "Image" pour stocker les informations de l'image téléchargée, récupère le fichier d'image à partir de la requête, et l'associe à l'entité "Image". En fonction de la route, elle crée également une nouvelle entité "Message" pour stocker les détails du message associé à l'image, l'associe à l'auteur (l'utilisateur actuel) et au destinataire (ami ou groupement spécifié), puis persiste les entités dans l'EntityManager. Enfin, elle renvoie une réponse JSON contenant des informations sur l'image téléchargée, notamment son ID et son URL.

### Gestion des Messages

15. **Obtenir la liste des messages**
    - Route : `/api/message/`
    - Méthode HTTP : GET
    - Description : Utilise le repository MessageRepository pour récupérer tous les messages existants, les pagine en utilisant le PaginatorInterface, et les renvoie au format JSON.

16. **Envoyer un message à un ami**
    - Route : `/api/message/new/friend/{id}`
    - Méthode HTTP : POST
    - Description : Permet à l'utilisateur actuel d'envoyer un nouveau message à un ami spécifié par son ID. Avant d'envoyer le message, elle vérifie que les deux utilisateurs sont amis (en utilisant le FriendRepository). Si oui, elle crée un nouvel objet "Message", l'associe à l'auteur (l'utilisateur actuel) et au destinataire (ami spécifié), puis le persiste dans l'EntityManager.
   - Exemple de corps de requête au format JSON :{"content": "coucou"}

17. **Envoyer un message à

 un groupement**
    - Route : `/api/message/new/groupement/{id}`
    - Méthode HTTP : POST
    - Description : Permet à l'utilisateur actuel d'envoyer un nouveau message à un groupement spécifié par son ID. Avant d'envoyer le message, elle vérifie que l'utilisateur actuel est membre du groupement. Si oui, elle crée un nouvel objet "Message", l'associe à l'auteur (l'utilisateur actuel) et au groupement spécifié, puis le persiste dans l'EntityManager.

18. **Obtenir les messages échangés avec un ami**
    - Route : `/api/message/friend/{id}`
    - Méthode HTTP : GET
    - Description : Permet de récupérer les messages échangés entre l'utilisateur actuel et un ami spécifié par son ID. Elle pagine les résultats en utilisant le PaginatorInterface et les renvoie au format JSON.

19. **Obtenir les messages échangés dans un groupement**
    - Route : `/api/message/groupement/{id}`
    - Méthode HTTP : GET
    - Description : Permet de récupérer les messages échangés dans un groupement spécifié par son ID. Avant de renvoyer les messages, elle vérifie que l'utilisateur actuel est membre du groupement. Elle pagine ensuite les résultats en utilisant le PaginatorInterface et les renvoie au format JSON.

20. **Modifier le contenu d'un message**
    - Route : `/api/message/{id}/edit`
    - Méthode HTTP : PUT
    - Description : Permet à l'utilisateur actuel de modifier le contenu d'un message spécifié par son ID, à condition qu'il soit l'auteur original du message. Le contenu du message est modifié en utilisant les données fournies dans le corps de la requête au format JSON.

21. **Supprimer un message**
    - Route : `/api/message/{id}`
    - Méthode HTTP : DELETE
    - Description : Permet à l'utilisateur actuel de supprimer un message spécifié par son ID, à condition qu'il soit l'auteur original du message. Le message est supprimé de la base de données.

### Gestion des Validités de Demande de Groupement

22. **Obtenir la demande de groupement associée à l'utilisateur actuel**
    - Route : `/api/groupement/validity/show`
    - Méthode HTTP : GET
    - Description : Utilise le repository ValidityRepository pour récupérer la demande de groupement associée à l'utilisateur actuel (l'utilisateur connecté) et la renvoie au format JSON.

23. **Valider une demande de groupement**
    - Route : `/api/groupement/validity/{id}`
    - Méthode HTTP : GET
    - Description : Permet à l'utilisateur actuel de valider la demande de groupement spécifiée par son ID. Si l'utilisateur actuel est l'utilisateur associé à la demande de groupement, cette méthode ajoute cet utilisateur au groupe en utilisant la méthode `addMember` de l'entité "Groupement".

24. **Refuser une demande de groupement**
    - Route : `/api/groupement/validity/{id}`
    - Méthode HTTP : DELETE
    - Description : Permet à l'utilisateur actuel de refuser la demande de groupement spécifiée par son ID. Si l'utilisateur actuel est l'utilisateur associé à la demande de groupement, cette méthode supprime l'entité "Validity" associée à la demande de groupement.
      
### Gestion des utilisateurs

25. **Obtenir un token (login)**
    - Route : `/api/login_check`
    - Méthode HTTP : POST
    - Description : Cette route est utilisée pour authentifier un utilisateur en vérifiant les informations de connexion fournies. Lorsqu'un utilisateur tente de se connecter à l'application, les identifiants (nom d'utilisateur et mot de passe) sont envoyés au serveur via cette route. Le serveur vérifie ensuite ces informations par rapport à sa base de données d'utilisateurs.
    - Exemple de corps de requête au format JSON :{"username": "username","password":"password"}

26. **Obtenir un token rafraichi**
    - Route : `/api/token/refresh`
    - Méthode HTTP : POST
    - Description : Description : Cette route permet à un utilisateur de demander le rafraîchissement de son jeton d'authentification. Les jetons d'authentification sont utilisés pour maintenir la session de l'utilisateur actif et sécurisée. Lorsqu'un utilisateur se connecte à l'application, il reçoit un jeton d'accès (access token) qui a une durée de validité limitée. Le jeton d'actualisation (refresh token) est utilisé pour obtenir un nouveau jeton d'accès une fois que le jeton actuel a expiré.
    - Exemple de corps de requête au format JSON :{"token": "token"}
   
    26. **creer un utilisateur**
    - Route : `/register`
