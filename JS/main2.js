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
  
    // variables
    let email = document.querySelector('#email');
    let username = document.querySelector('#username');
    let password = document.querySelector('#password');
    let password2 = document.querySelector('#password2');
  
    let errors = [];
  
    // fonction pour valider et mettre un fond rouge ou vert en cas de success ou erreur
    function validité(field, isValid) {
      if (isValid) {
        field.classList.remove('error');
        field.classList.add('success');
      } else {
        field.classList.remove('success');
        field.classList.add('error');
      }
    }
  
    // test pseudo
    if (username.value.length < 6) {
      errors.push("Le pseudo doit contenir au moins 6 caractères.");
      validité(username, false);
    } else {
      validité(username, true);
    }
  
    // test Email
    if (email.value.trim() === '' || !email.value.includes('@')) {
      errors.push("L'email doit être valide.");
      validité(email, false);
    } else {
      validité(email, true);
    }
  
    // prerequis mot de passe
    let passCheck = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[-+_!@#$%^&*.,?]).{10,}$/;
    if (!passCheck.test(password.value)) {
      errors.push("Le mot de passe doit contenir au moins 10 caractères, une lettre majuscule, une lettre minuscule, un chiffre et un symbole.");
      validité(password, false);
    } else {
      validité(password, true);
    }
  
    // mot de passe confirmé
    if (password.value !== password2.value) {
      errors.push("Les mots de passe ne correspondent pas.");
      validité(password2, false);
    } else {
      validité(password2, true);
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
      // Ici vous pourriez ajouter une redirection ou un appel AJAX
    }
  });