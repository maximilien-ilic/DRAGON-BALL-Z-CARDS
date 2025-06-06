<?php
///connexion base de donnée
require_once("connexion.php"); // Le session_start() doit être dans ce fichier

///redirection si deja connecté
if(isset($_SESSION["iduser"])) {
    header("location:profil.php");
    exit;
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
            $error = "La connexion a échoué ! Email ou mot de passe incorrect.";
        }
    } else {
        $error = "Veuillez remplir tous les champs.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="assets/logo.png">
    <title>Dragon Ball Z CARDS - Connexion</title>
    <link rel="stylesheet" href="CSS/compte.css">
</head>
<body>
    <div class="container">
        <div class="logo">
            <a href="index.html"><img src="assets/logo.png" alt="Dragon Ball Z Cards"></a>
        </div>
        
        <h1>CONNEXION</h1>
        
        <?php if (isset($error)): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div id="successMessage">
            Inscription réussie ! Vous allez être redirigé...
        </div>
        
        <?php if (!isset($_SESSION["iduser"])): ?>
        <form method="POST">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="votre@email.com" required>
            </div>
            
            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" placeholder="Votre mot de passe" required>
            </div>
            
            <button type="submit">Connexion</button>
            <button type="button" class="btn-secondary">
                <a href="creaCompte.php" style="text-decoration: none; color: white;">Créer un compte</a>
            </button>
        </form>
        <?php endif; ?>
    </div>
</body>
</html>