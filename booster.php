<?php
// Couleurs pour les races
$raceColors = [
    'Saiyan' => '#FF4500', 'Human' => '#1E90FF', 'Namekian' => '#32CD32', 
    'Android' => '#4682B4', 'Majin' => '#FF1493', 'Frieza Race' => '#9932CC', 
    'God' => '#FFD700', 'Core Person' => '#8B4513', 'Demon' => '#800000', 'Dragon' => '#006400'
];

// Recherche
$searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';

// Récupération des données API
$characters = [];
try {
    for ($page = 1; $page <= 10; $page++) {
        $response = file_get_contents('https://dragonball-api.com/api/characters?page=' . $page);
        if ($response === false) break;
        
        $data = json_decode($response, true);
        if (!isset($data['items'])) break;
        
        $characters = array_merge($characters, $data['items']);
        
        if (isset($data['meta']['totalPages']) && $page >= $data['meta']['totalPages']) break;
    }
} catch (Exception $e) {
    $characters = [];
}

shuffle($characters);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>booster</title>
    <link rel="stylesheet" href="CSS/styleBooster.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <link rel="icon" type="image/png" href="assets/logo.png">
</head>
<body>
    <div class="container">
        <header>
            <a class="logo" href="index.html"></a>
            <h1>Dragon Ball Z Cards - Boosters</h1>
            <p> Ouvre un paquet de carte toutes les 10 secondes !</p>
        </header>
        <main>
            <img class="flex margin" src="images/4570118001696.jpg" alt="paquet de carte">
            <button class="flex button1" id="open">Ouvrir</button>
            <div id="timerMessage" style="display:none; margin-top:10px;">
                Vous devez attendre <span id="countdown">120</span> secondes avant de pouvoir ouvrir un nouveau booster.
            </div>

            <div class="modal-Containner" id="modal_Containner">
                <div class="modal">
                    <p>VOICI VOTRE BOOSTER<p>
                    <div class="flexWrap">
                        <?php foreach (array_slice($characters, 0, 5) as $i => $char): ?>
                            <div class="card">
                                <div class="card-image">
                                    <?php if ($char['image']): ?>
                                        <img src="<?= htmlspecialchars($char['image']) ?>" alt="<?= htmlspecialchars($char['name']) ?>">
                                    <?php else: ?>
                                        <div style="carte2">
                                            <?= htmlspecialchars($char['name']) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="card-info">
                                    <div class="card-name"><?= htmlspecialchars($char['name']) ?></div>
                                    <?php if ($char['race']): ?>
                                        <span class="race-badge" style="background:<?= $raceColors[$char['race']] ?? '#8A2BE2' ?>">
                                            <?= htmlspecialchars($char['race']) ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button id="close">fermer</button>
                </div>
            </div>
        </main>
    </div>
<script>
    const open = document.getElementById('open');
    const close = document.getElementById('close');
    const modal_Containner = document.getElementById('modal_Containner');
    const timerMessage = document.getElementById('timerMessage');
    const countdownElement = document.getElementById('countdown');
    
    const temps = 10;
    
    // Vérifier si un temps de dernière ouverture est enregistré
    const lastOpenTime = localStorage.getItem('lastBoosterOpenTime');
    const currentTime = Math.floor(Date.now() / 1000);
    
    if (lastOpenTime) {
        const timeSinceLastOpen = currentTime - parseInt(lastOpenTime);
        const timeRemaining = temps - timeSinceLastOpen;
        
        if (timeRemaining > 0) {
            // Désactiver le bouton et afficher le compte à rebours
            open.disabled = true;
            timerMessage.style.display = 'block';
            startCountdown(timeRemaining);
        }
    }
    
    function startCountdown(seconds) {
        let remaining = seconds;
        countdownElement.textContent = remaining;
        
        const interval = setInterval(() => {
            remaining--;
            countdownElement.textContent = remaining;
            
            if (remaining <= 0) {
                clearInterval(interval);
                open.disabled = false;
                timerMessage.style.display = 'none';
                localStorage.removeItem('lastBoosterOpenTime');
                
                // Recharger la page pour reshuffle les cartes
                location.reload();
            }
        }, 1000);
    }
    
    open.addEventListener('click', () => {
        // Enregistrer l'heure actuelle
        const currentTime = Math.floor(Date.now() / 1000);
        localStorage.setItem('lastBoosterOpenTime', currentTime.toString());
        
        // Désactiver le bouton et démarrer le compte à rebours
        open.disabled = true;
        timerMessage.style.display = 'block';
        startCountdown(temps);
        
        // Afficher le modal
        modal_Containner.classList.add('show');
    });
    
    close.addEventListener('click', () => { 
        modal_Containner.classList.remove('show'); 
    });
</script>
</body>
</html>