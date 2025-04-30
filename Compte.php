<?php
///connexion base de donnée
require_once("connexion.php");


///redirection si deja connecté
if(isset($_SESSION["iduser"])) {
    header("location:profil.php");
}

if ($_POST) {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    
    if ($email && $password) {
        // Utiliser une requête préparée pour éviter les injections SQL
        $stmt = $pdo->prepare("SELECT * FROM connexion WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user["password"])) {
            // Connexion réussie
            $_SESSION["iduser"] = $user["iduser"];
            $_SESSION["email"] = $user["email"];
            
            header("location:profil.php");
            exit;
        } else {
            echo "La connexion a échoué !";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="stylecompte.css">
    <link rel="icon" type="image/png" href="assets/logo.png">
    <title>Dragon Ball Z CARDS</title>
</head>
<body>
    <!--html form connexion-->
    <div class="container">
        <div class="dragon-ball"></div>
        <div class="dragon-ball"></div>
        
        <div>
            <a class="logo" href="index.html"></a>
        </div>
        
        <h1>CONNEXION</h1>
        
        <div id="errorContainer">
            <ul id="errorList"></ul>
        </div>
        
        <div id="successMessage">
            Inscription réussie ! Vous allez être redirigé...
        </div>
        <?php if (!isset($_SESSION["iduser"])) { ?>
        <form method="POST"  >
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="votre@email.com">
            </div>
            

            
            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" placeholder="Votre mot de passe">
            </div>
            
            <button type="submit" >connexion</button>
            <button><a href="creaCompte.php" style="text-decoration: none; color: white;">Créer un compte !</a></button>
        </form>
        <?php } ?>
    </div>