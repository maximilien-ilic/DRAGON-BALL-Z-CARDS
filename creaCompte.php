<?php
// Initialiser les variables pour éviter les erreurs
$email = isset($_POST["email"]) ? $_POST["email"] : "";
$password = isset($_POST["password"]) ? $_POST["password"] : "";

require_once("connexion.php");

if($_POST) {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    
    $hashed_password = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    
    $sql = "INSERT INTO connexion (email, password) VALUES(:email, :password)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'email' => $email,
        'password' => $hashed_password
    ]);
    // Rediriger vers la même page avec un paramètre
    header("Location: " . $_SERVER['PHP_SELF'] . "?success=1");
    exit;
}

// Vérifier si on vient d'une soumission réussie
$success = isset($_GET['success']) && $_GET['success'] == '1';
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
    <!--formulaire de creation de compte-->
    <div class="container">
        <div class="dragon-ball"></div>
        <div class="dragon-ball"></div>
        
        <div>
            <a class="logo" href="index.html"></a>
        </div>
        
        <h1>Créer un compte</h1>
        
        <div id="errorContainer">
            <ul id="errorList"></ul>
        </div>
        
        <div id="successMessage">
            Inscription réussie ! Vous allez être redirigé...
        </div>
        
        <form method="POST" id="registerForm" >
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="votre@email.com" value="<?php echo htmlspecialchars($email); ?>">
            </div>
            
            <div class="form-group">
                <label for="username">Pseudo</label>
                <input type="text" id="username" name="username" placeholder="Votre pseudo">
                <p class="requirements">Minimum 6 caractères</p>
            </div>
            
            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" placeholder="Votre mot de passe">
                <p class="requirements">10 caractères minimum, majuscule, minuscule, chiffre et symbole</p>
            </div>
            
            <div class="form-group">
                <label for="password2">Confirmez le mot de passe</label>
                <input type="password" id="password2" name="password2" placeholder="Confirmez votre mot de passe">
            </div>
            
            <button type="submit">S'INSCRIRE</button>
        </form>
    </div>
    <script>
        //transfere variable php a js
        var serverData = <?php echo json_encode([
            'email' => $email,
            'password' => $password
        ]); ?>;
    
        document.querySelector('#registerForm').addEventListener('submit', function (event) {
            event.preventDefault();
            
            // Reset message d'erreur
            let errorContainer = document.querySelector('#errorContainer');
            let errorList = document.querySelector('#errorList');
            errorList.innerHTML = '';
            errorContainer.style.display = 'none';
        
            // Reset message de success
            let successMessage = document.querySelector('#successMessage');
            successMessage.style.display = 'none';
        
            // Récupérer les éléments DOM pour pouvoir leur appliquer des styles
            let emailField = document.querySelector('#email');
            let usernameField = document.querySelector('#username');
            let passwordField = document.querySelector('#password');
            let password2Field = document.querySelector('#password2');
            
            // Utiliser les valeurs PHP pour la validation
            let email = serverData.email || emailField.value;
            let username = usernameField.value;
            let password = serverData.password || passwordField.value;
            let password2 = password2Field.value;
        
            let errors = [];
        
            // fonction pour valider et mettre un fond rouge ou vert en cas de success ou erreur
            function validité(field, isValid) {
                if (field && field.classList) {
                    if (isValid) {
                        field.classList.remove('error');
                        field.classList.add('success');
                    } else {
                        field.classList.remove('success');
                        field.classList.add('error');
                    }
                }
            }
        
            // test pseudo
            if (username.length < 6) {
                errors.push("Le pseudo doit contenir au moins 6 caractères.");
                validité(usernameField, false);
            } else {
                validité(usernameField, true);
            }
        
            // test Email
            if (email.trim() === '' || !email.includes('@')) {
                errors.push("L'email doit être valide.");
                validité(emailField, false);
            } else {
                validité(emailField, true);
            }
        
            // prerequis mot de passe
            let passCheck = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[-+_!@#$%^&*.,?]).{10,}$/;
            if (!passCheck.test(password)) {
                errors.push("Le mot de passe doit contenir au moins 10 caractères, une lettre majuscule, une lettre minuscule, un chiffre et un symbole.");
                validité(passwordField, false);
            } else {
                validité(passwordField, true);
            }
        
            // mot de passe confirmé
            if (password !== password2) {
                errors.push("Les mots de passe ne correspondent pas.");
                validité(password2Field, false);
            } else {
                validité(password2Field, true);
            }
        
            // erreur ou success
            if (errors.length > 0) {
                errors.forEach(error => {
                    let li = document.createElement('li');
                    li.textContent = error;
                    errorList.appendChild(li);
                });
                errorContainer.style.display = 'block';
            } else {
                successMessage.style.display = 'block';
                console.log("Formulaire envoyé avec succès !");
                // Soumettre le formulaire si tout est valide
                this.submit();
                // Attendre 1 seconde puis rediriger
                var delayInMilliseconds = 5000; //1 second
                setTimeout(function() {
                    window.location.href = "index.html";
                }, delayInMilliseconds);
            }
        });
        <?php if($success): ?>
        // Si le paramètre success est présent, afficher le message puis rediriger
        document.getElementById('successMessage').style.display = 'block';
        setTimeout(function() {
            window.location.href = "Compte.php";
        }, 1000); // 1000 ms = 1 seconde
        <?php endif; ?>
    </script>
</body>
</html>