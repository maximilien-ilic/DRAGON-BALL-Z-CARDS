<?php

require_once("connexionCarte.php");

// Pour la création de carte
if ($_POST && !isset($_POST['update'])) {
    $Nom = $_POST["Nom"];
    $Race = $_POST["Race"];
    $KI = $_POST["KI"];
    
    try {
        $stmt = $pdo->prepare("INSERT INTO carte (Nom, Race, KI) 
    VALUES( :Nom, :Race, :KI)");
    
        $stmt->execute([
            "Nom" => $Nom,
            "Race" => $Race,
            "KI" => $KI,
        ]);
        
        $message='La carte a bien été créée';
        echo '<script type="text/javascript">window.alert("'.$message.'");</script>';
    
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
}

// Pour la mise à jour de carte
if (isset($_POST['update'])) {
    $idCarte = $_POST["idCarte"];
    $Nom = $_POST["Nom"];
    $Race = $_POST["Race"];
    $KI = $_POST["KI"];
    
    try {
        $stmt = $pdo->prepare("UPDATE carte SET Nom = :Nom, Race = :Race, KI = :KI WHERE idCarte = :idCarte");
    
        $stmt->execute([
            "idCarte" => $idCarte,
            "Nom" => $Nom,
            "Race" => $Race,
            "KI" => $KI,
        ]);
        
        $message='La carte a bien été modifiée';
        echo '<script type="text/javascript">window.alert("'.$message.'");</script>';
    
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
}

// Pour la suppression de carte
if(isset($_GET['action']) && $_GET['action'] == 'delete') {
    $idCarte = $_GET['idCarte'];

    try {
        $stmt = $pdo->prepare("DELETE FROM carte WHERE idCarte = :idCarte");

        $stmt->execute([
            "idCarte" => $idCarte,
        ]);

        $message='La carte a bien été suprimée';
        echo '<script type="text/javascript">window.alert("'.$message.'");</script>';
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
}

// Variable pour stocker les infos d'une carte à modifier
$carteToModify = null;

// Pour récupérer les informations d'une carte à modifier
if(isset($_GET['action']) && $_GET['action'] == 'modify') {
    $idCarte = $_GET['idCarte'];

    try {
        $stmt = $pdo->prepare("SELECT * FROM carte WHERE idCarte = :idCarte");

        $stmt->execute([
            "idCarte" => $idCarte,
        ]);

        $carteToModify = $stmt->fetch(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
}


$stmt = $pdo->query("SELECT * FROM carte"); 
$cartes = $stmt->fetchAll(PDO::FETCH_ASSOC);

if(!isset($_SESSION["iduser"])) {
    header("location:Compte.php");
}

if(isset($_GET["action"]) && $_GET["action"] == "deconnexion") {
    // je vide ma session
    unset($_SESSION["iduser"]);
    unset($_SESSION["email"]);
    header("location:Compte.php"); // redirection sans paramètre
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <style>
    @import url('https://fonts.googleapis.com/css2?family=Bangers&display=swap');


    /* STYLE PAGE */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        background-color:rgb(238, 228, 243);
        font-family: 'Arial', sans-serif;
        color: #333;
        line-height: 1.6;
        padding: 20px;
        position: relative;
    }

  
    h1 {
        font-family: 'Bangers', cursive;
        color: #530C6D;
        text-align: center;
        font-size: 2.5rem;
        margin-bottom: 30px;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        letter-spacing: 2px;
    }

    /* Connection Info */
    body > p {
        background-color: #333;
        color: white;
        padding: 10px;
        margin-bottom: 20px;
        border-radius: 5px;
        font-weight: bold;
    }

    /* Table Styles */
    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 30px;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.15);
        background-color: rgba(255, 255, 255, 0.9);
        border-radius: 8px;
        overflow: hidden;
    }

    thead {
        background-color: #530C6D;

        color: white;
    }

    th, td {
        padding: 12px 15px;
        text-align: left;
        border: none;
    }

    th {
        font-family: 'Bangers', cursive;
        font-size: 1.2rem;
        letter-spacing: 1px;
        font-weight: normal;
    }

    tbody tr {
        border-bottom: 1px solid #dddddd;
    }

    tbody tr:nth-of-type(even) {
        background-color: rgba(242, 242, 242, 0.8);
    }

    tbody tr:last-of-type {
        border-bottom: 2px solid #530C6D;
    }

    /* Links in Table */
    table a {
        text-decoration: none;
        color: #333;
        padding: 5px 10px;
        border-radius: 4px;
        font-weight: bold;
        transition: all 0.3s ease;
        display: inline-block;
    }

    table a[href*="delete"] {
        background-color: #ff4136;
        color: white;
    }

    table a[href*="modify"] {
        background-color: #0074D9;
        color: white;
    }

    table a:hover {
        transform: scale(1.1);
        box-shadow: 0 0 5px rgba(0, 0, 0, 0.3);
    }

    /* Form Styles */
    form {
        background-color: rgba(255, 255, 255, 0.9);
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.15);
        max-width: 600px;
        margin: 0 auto 30px;
        border: 2px solid #530C6D;
    }

    form h2 {
        font-family: 'Bangers', cursive;
        color: #530C6D;
        text-align: center;
        margin-bottom: 20px;
    }

    form label {
        display: block;
        margin: 10px 0 5px;
        font-weight: bold;
        color: #333;
        font-family: 'Bangers', cursive;
        font-size: 1.2rem;
    }

    form input[type="text"],
    form input[type="number"] {
        width: 100%;
        padding: 10px;
        margin-bottom: 15px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 1rem;
    }

    form input[type="submit"] {
        background-color: #530C6D;
        color: white;
        border: none;
        padding: 12px 20px;
        cursor: pointer;
        font-size: 1rem;
        border-radius: 4px;
        font-weight: bold;
        font-family: 'Bangers', cursive;
        letter-spacing: 1px;
        transition: all 0.3s ease;
        display: block;
        margin: 20px auto 0;
    }

    form input[type="submit"]:hover {
        background-color: #530C6D;
        transform: scale(1.05);
        box-shadow: 0 0 10px rgba(111, 13, 80, 0.5);
    }

    /* BUTTON DECONEXION */
    a[href*="deconnexion"] {
        display: block;
        text-align: center;
        background-color: #333;
        color: white;
        text-decoration: none;
        padding: 10px 15px;
        border-radius: 4px;
        font-weight: bold;
        margin: 20px auto;
        max-width: 200px;
        transition: all 0.3s ease;
    }

    a[href*="deconnexion"]:hover {
        background-color: #555;
        transform: scale(1.05);
    }



    /* Responsive Styles */
    @media (max-width: 768px) {
        table {
            display: block;
            overflow-x: auto;
        }
        
        body {
            padding: 10px;
        }
        
        h1 {
            font-size: 2rem;
        }
    }
    </style>
    <?php
        echo "Vous êtes connecté avec l'adresse email " . $_SESSION["email"];
    ?>
    
    <h1>Mes Cartes DRAGON BALL Z en BDD</h1>

    <table border="1">
        <thead>
            <th>Nom</th>
            <th>Race</th>
            <th>KI</th>
            <th>Supprimer</th>
            <th>Modifier</th>
        </thead>    

        <tbody>
            <?php
            foreach ($cartes as $key => $carte) {
                echo "<tr>";
                echo "<td>" . $carte["Nom"] . "</td>";
                echo "<td>" . $carte["Race"] . "</td>";
                echo "<td>" . $carte["KI"] . "</td>";
                echo "<td> <a href='?idCarte=". $carte["idCarte"] . "&action=delete'> Supprimer </a> </td>";
                echo "<td> <a href='?idCarte=". $carte["idCarte"] . "&action=modify'> Modifier </a> </td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>

    <br>
    <br>
    
    <?php if($carteToModify): ?>
    <!-- Formulaire de modification -->
    <form method="POST">
        <h2>Modifier la carte</h2>
        
        <input type="hidden" name="idCarte" value="<?php echo $carteToModify['idCarte']; ?>">
        
        <label for="Nom">Nom:</label>
        <input type="text" name="Nom" id="Nom" value="<?php echo $carteToModify['Nom']; ?>">
        
        <label for="Race">Race:</label>
        <input type="text" name="Race" id="Race" value="<?php echo $carteToModify['Race']; ?>">
        
        <label for="KI">KI:</label>
        <input type="number" name="KI" id="KI" value="<?php echo $carteToModify['KI']; ?>">
        
        <input type="submit" name="update" value="Modifier la carte">
    </form>
    <?php else: ?>
    <!-- Formulaire d'ajout -->
    <form method="POST">
        <h2>Ajouter une carte</h2>
        
        <label for="Nom">Nom:</label>
        <input type="text" name="Nom" id="Nom" placeholder="Nom">
        
        <label for="Race">Race:</label>
        <input type="text" name="Race" id="Race" placeholder="Race">
        
        <label for="KI">KI:</label>
        <input type="number" name="KI" id="KI">
        
        <input type="submit" value="Créer carte">
    </form>
    <?php endif; ?>
    
    <a href="?action=deconnexion">Se déconnecter</a>

    <div class="dragon-ball"></div>
</body>
</html>