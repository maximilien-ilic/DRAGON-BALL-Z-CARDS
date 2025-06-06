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
    
    // Filtrage par recherche
    if ($searchTerm) {
        $characters = array_filter($characters, function($char) use ($searchTerm) {
            return stripos($char['name'], $searchTerm) !== false;
        });
    }
} catch (Exception $e) {
    $characters = [];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dragon Ball Z Cards</title>
    <link rel="stylesheet" href="CSS/carte.css">

</head>
<body>
    <div class="container">
        <header>
            <a class="logo" href="index.html"></a>
            <h1>Dragon Ball Z - Personnages</h1>
            <p>Découvrez les personnages de l'univers Dragon Ball</p>
        </header>

        <form class="search-form" method="GET">
            <input type="text" name="search" placeholder="Rechercher..." value="<?= htmlspecialchars($searchTerm) ?>">
            <button type="submit">Rechercher</button>
        </form>

        <?php if ($searchTerm): ?>
            <p style="text-align:center; margin-bottom:20px;">
                <a href="?" style="color:#9370DB;">Effacer la recherche</a>
            </p>
        <?php endif; ?>

        <div class="grid">
            <?php if (empty($characters)): ?>
                <div class="error">
                    <h2><?= $searchTerm ? 'Aucun résultat' : 'Erreur de chargement' ?></h2>
                    <p><?= $searchTerm ? "Aucun personnage trouvé pour \"$searchTerm\"" : "Impossible de charger les données" ?></p>
                </div>
            <?php else: ?>
                <?php foreach ($characters as $i => $char): ?>
                    <div class="card" onclick="openModal(<?= $i ?>)">
                        <div class="card-image">
                            <?php if ($char['image']): ?>
                                <img src="<?= htmlspecialchars($char['image']) ?>" alt="<?= htmlspecialchars($char['name']) ?>">
                            <?php else: ?>
                                <div style="width:100px;height:100px;background:#fff;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#9370DB;font-weight:bold;">
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
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal" id="modal" onclick="closeModal(event)">
        <div class="modal-content">
            <div class="modal-header">
                <img id="modalImg" class="modal-image" src="" alt="">
                <h2 id="modalName"></h2>
                <p id="modalId"></p>
            </div>
            <div class="modal-body">
                <div class="modal-section" id="raceSection">
                    <span id="modalRace" class="race-badge"></span>
                </div>
                <div class="modal-section" id="descSection">
                    <h3>Description</h3>
                    <p id="modalDesc"></p>
                </div>
                <div class="modal-section" id="statsSection">
                    <h3>Statistiques</h3>
                    <div class="stats">
                        <div class="stat" id="kiStat">
                            <div>Ki</div>
                            <strong id="modalKi"></strong>
                        </div>
                        <div class="stat" id="maxKiStat">
                            <div>Max Ki</div>
                            <strong id="modalMaxKi"></strong>
                        </div>
                    </div>
                </div>
                <button class="close-btn" onclick="closeModal()">Fermer</button>
            </div>
        </div>
    </div>

    <script>
        const characters = <?= json_encode($characters) ?>;
        const raceColors = <?= json_encode($raceColors) ?>;

        function openModal(i) {
            const c = characters[i];
            
            document.getElementById('modalImg').src = c.image || '';
            document.getElementById('modalName').textContent = c.name || '';
            document.getElementById('modalId').textContent = c.id ? `#${String(c.id).padStart(3, '0')}` : '';
            
            // Race
            const raceEl = document.getElementById('modalRace');
            const raceSec = document.getElementById('raceSection');
            if (c.race) {
                raceEl.textContent = c.race;
                raceEl.style.background = raceColors[c.race] || '#8A2BE2';
                raceSec.style.display = 'block';
            } else {
                raceSec.style.display = 'none';
            }
            
            // Description
            const descEl = document.getElementById('modalDesc');
            const descSec = document.getElementById('descSection');
            if (c.description) {
                descEl.textContent = c.description;
                descSec.style.display = 'block';
            } else {
                descSec.style.display = 'none';
            }
            
            // Stats
            const statsSec = document.getElementById('statsSection');
            const kiStat = document.getElementById('kiStat');
            const maxKiStat = document.getElementById('maxKiStat');
            
            let hasStats = false;
            
            if (c.ki) {
                document.getElementById('modalKi').textContent = c.ki;
                kiStat.style.display = 'block';
                hasStats = true;
            } else {
                kiStat.style.display = 'none';
            }
            
            if (c.maxKi) {
                document.getElementById('modalMaxKi').textContent = c.maxKi;
                maxKiStat.style.display = 'block';
                hasStats = true;
            } else {
                maxKiStat.style.display = 'none';
            }
            
            statsSec.style.display = hasStats ? 'block' : 'none';
            
            document.getElementById('modal').classList.add('show');
        }

        function closeModal(e) {
            if (!e || e.target.id === 'modal') {
                document.getElementById('modal').classList.remove('show');
            }
        }
    </script>
</body>
</html>