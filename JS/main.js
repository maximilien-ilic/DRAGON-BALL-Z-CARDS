///SLIDER.JS
var swiper = new Swiper(".mySwiper", {
    spaceBetween: 30,
    centeredSlides: true,
    autoplay: {
      delay: 2500,
      disableOnInteraction: false,
    },
    pagination: {
      el: ".swiper-pagination",
      clickable: true,
    },
    navigation: {
      nextEl: ".swiper-button-next",
      prevEl: ".swiper-button-prev",
    },
  });

// Sélectionner les éléments
const modeToggle = document.querySelector('#mode-toggle');
const modeIcon = document.querySelector('#mode-icon');
const iconCompte = document.querySelector('#icon-compte');
const iconRecherche = document.querySelector('#icon-recherche');
const body = document.body;
        
 // Variable pour suivre l'état actuel
let isDarkMode = false;
        
// Fonction pour changer de mode
modeToggle.addEventListener('click', function() {
    if (isDarkMode) {
        // Passer au mode clair
        body.classList.remove('dark-mode');
        body.classList.add('light-mode');
        modeIcon.src = "assets/lune.png";
        modeIcon.alt = "passer en mode sombre";
                
        // Changer les icônes en version sombre
        iconCompte.src = "assets/utilisateur.png";
        iconRecherche.src = "assets/chercher.png";
    } else {
        // Passer au mode sombre
        body.classList.remove('light-mode');
        body.classList.add('dark-mode');
        modeIcon.src = "assets/soleil.png";
        modeIcon.alt = "passer en mode clair";
                
        // Changer les icônes en version claire
        iconCompte.src = "assets/utilisateur-blanc.png";
        iconRecherche.src = "assets/chercher-blanc.png";
    }
            
    // Inverser l'état
    isDarkMode = !isDarkMode;
});

document.getElementById('menu-toggle').addEventListener('click', function() {
  document.getElementById('nav-menu').classList.toggle('active');
});