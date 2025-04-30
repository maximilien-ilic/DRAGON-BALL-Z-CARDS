<?php
require_once("connexion.php");///connexion base de donnée
?>

<!--html session-->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>

    <h1>Connexion</h1>

    <?php if(!isset($_SESSION["iduser"]) ) { ?>
        <form method="POST">

            <label for="email">Email:</label>
            <input type="text" name="email" id="email" placeholder="Email">


            <label for="password">Mot de passe:</label>
            <input type="password" name="password" id="password" placeholder="Mot de passe">

            <input type="submit" value="Connexion">

        </form>
    
    <?php } else { ?>

        <a href="?action=deconnexion">Se déconnecter</a>

    <?php } ?>
    
</body>

</html>