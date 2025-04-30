<?php
// Définir les couleurs pour les races
$raceColors = [
    'Saiyan' => '#FF4500',
    'Human' => '#1E90FF',
    'Namekian' => '#32CD32',
    'Android' => '#4682B4',
    'Majin' => '#FF1493',
    'Frieza Race' => '#9932CC',
    'God' => '#FFD700',
    'Core Person' => '#8B4513',
    'Demon' => '#800000',
    'Dragon' => '#006400',
    // Ajoutez d'autres races au besoin
];

// Récupérer le terme de recherche s'il existe
$searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';

// Récupérer les données de l'API Dragon Ball avec pagination
$characters = [];
try {
    // Paramètres pour la pagination
    $page = 1;
    $hasMorePages = true;
    
    // Boucle pour récupérer toutes les pages
    while ($hasMorePages) {
        // URL de l'API avec pagination
        $apiUrl = 'https://dragonball-api.com/api/characters?page=' . $page;
        
        // Configuration de la requête
        $options = [
            'http' => [
                'header' => "Accept: application/json\r\n",
                'method' => 'GET',
                'timeout' => 30,
            ]
        ];
        $context = stream_context_create($options);
        
        // Exécuter la requête
        $response = file_get_contents($apiUrl, false, $context);
        
        // Si la requête échoue, sortir de la boucle
        if ($response === false) {
            throw new Exception("Impossible de se connecter à l'API à la page " . $page);
        }
        
        // Décoder la réponse JSON
        $data = json_decode($response, true);
        
        // Vérifier la structure de la réponse
        if (!isset($data['items']) || !is_array($data['items'])) {
            throw new Exception("Format de réponse invalide à la page " . $page);
        }
        
        // Ajouter les personnages de cette page au tableau global
        $characters = array_merge($characters, $data['items']);
        
        // Vérifier s'il y a plus de pages
        if (isset($data['meta']) && isset($data['meta']['totalPages'])) {
            // Si nous avons atteint la dernière page, arrêter la boucle
            if ($page >= $data['meta']['totalPages']) {
                $hasMorePages = false;
            } else {
                // Sinon, passer à la page suivante
                $page++;
            }
        } else {
            // Si nous ne pouvons pas déterminer le nombre total de pages, arrêter la boucle
            $hasMorePages = false;
        }
        
        if ($page > 10) {  // Maximum 10 pages
            $hasMorePages = false;
        }
    }
    
    // Filtrer les personnages si un terme de recherche est spécifié
    if (!empty($searchTerm) && !empty($characters)) {
        $filteredCharacters = [];
        foreach ($characters as $character) {
            // Vérifier si le nom du personnage contient le terme de recherche (insensible à la casse)
            if (stripos($character['name'], $searchTerm) !== false) {
                $filteredCharacters[] = $character;
            }
        }
        $characters = $filteredCharacters;
    }
    
} catch (Exception $e) {
    // En cas d'erreur, $characters pourrait contenir des résultats partiels
    // ou être vide si l'erreur est survenue à la première page
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="assets/logo.png">
    <title>DRAGON BALL Z CARDS</title>    
    <style>
        /*styles de base */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            background: linear-gradient(135deg, #663399, #9932CC);
            background-attachment: fixed;
            padding: 15px;
            color: #fff;
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            line-height: 1.6;
        }

        /* Container principal  */
        .container {
            max-width: 1200px;
            width: 100%;
            margin: 0 auto;
            padding: 0 10px;
        }

        /* Logo centré */
        .logo {
            width: 100px;
            height: 100px;
            display: block;
            margin: 0 auto 15px;
            background: url("assets/logo.png") no-repeat center/contain;
        }
        
        /* Style de l'en-tête */
        header {
            text-align: center;
            margin-bottom: 30px;
            padding: 20px 15px;
            background-color: rgba(75, 0, 130, 0.7);
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        h1 {
            font-size: clamp(1.5rem, 5vw, 2.5rem);
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }

        header p {
            font-size: clamp(0.9rem, 3vw, 1.1rem);
        }

        /* Zone de recherche responsive */
        .search-container {
            margin: 20px 0;
            text-align: center;
            width: 100%;
        }

        .search-form {
            display: flex;
            max-width: 600px;
            width: 100%;
            margin: 0 auto;
            flex-wrap: wrap;
        }

        .search-input {
            flex: 1 1 200px;
            min-width: 0;
            padding: 12px 15px;
            font-size: 1rem;
            border: none;
            border-radius: 5px 0 0 5px;
            outline: none;
        }

        .search-button {
            padding: 12px 20px;
            background-color: #9370DB;
            color: white;
            border: none;
            border-radius: 0 5px 5px 0;
            cursor: pointer;
            font-weight: bold;
            white-space: nowrap;
        }

        /* Pour les très petits écrans, ajuster les coins pour empiler les éléments */
        @media (max-width: 360px) {
            .search-input {
                border-radius: 5px 5px 0 0;
                width: 100%;
            }
            
            .search-button {
                width: 100%;
                border-radius: 0 0 5px 5px;
            }
        }

        .search-button:hover {
            background-color: #8A2BE2;
        }

        .reset-search {
            display: inline-block;
            margin-top: 10px;
            color: #D8BFD8;
            text-decoration: none;
            font-size: clamp(0.8rem, 2vw, 1rem);
        }

        /* Grille de personnages responsive */
        .character-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: clamp(15px, 3vw, 25px);
            margin-bottom: 30px;
            width: 100%;
        }

        /* Pour les très petits écrans, 1 carte par ligne */
        @media (max-width: 350px) {
            .character-grid {
                grid-template-columns: 1fr;
            }
        }

        .character-card {
            background-color: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            color: #333;
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .character-image {
            background: linear-gradient(135deg, #9370DB, #8A2BE2);
            padding: 20px;
            text-align: center;
            height: 250px; /* Hauteur réduite et fixe pour plus de cohérence */
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .character-image img {
            max-width: 100%;
            max-height: 210px;
            width: auto;
            height: auto;
            object-fit: contain;
            transition: transform 0.3s ease;
        }

        .character-image img:hover {
            transform: scale(1.05);
        }

        .character-info {
            padding: 15px;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }

        .character-id {
            font-size: 0.85rem;
            color: #666;
        }

        .character-name {
            font-size: clamp(1.2rem, 3vw, 1.5rem);
            font-weight: bold;
            margin: 5px 0 10px;
            color: #4B0082;
            word-break: break-word;
        }

        .race-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            color: white;
            margin-bottom: 10px;
            font-weight: bold;
            align-self: flex-start;
        }

        .character-stats {
            display: flex;
            justify-content: space-between;
            border-top: 1px solid #ddd;
            padding-top: 10px;
            margin-top: auto;
            font-size: 0.9rem;
        }

        .character-description {
            font-size: 0.85rem;
            color: #444;
            margin-bottom: 10px;
            line-height: 1.4;
            max-height: 60px;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Pied de page */
        .footer {
            text-align: center;
            margin-top: 30px;
            padding: 20px 15px;
            background-color: rgba(75, 0, 130, 0.7);
            border-radius: 10px;
            font-size: clamp(0.8rem, 2vw, 1rem);
        }
        
        .footer a {
            color: #D8BFD8;
            text-decoration: none;
            font-weight: bold;
        }
        
        /* Messages et notifications */
        .error-message, .no-results {
            background-color: rgba(75, 0, 130, 0.8);
            color: white;
            padding: 20px 15px;
            border-radius: 10px;
            text-align: center;
            margin: 20px 0;
            width: 100%;
        }

        .characters-count {
            background-color: rgba(75, 0, 130, 0.6);
            color: white;
            padding: 10px;
            text-align: center;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: clamp(0.85rem, 2vw, 1rem);
        }

        .no-results {
            grid-column: 1 / -1;
        }
        
        /* Éviter le débordement du texte */
        h2, p {
            overflow-wrap: break-word;
            word-wrap: break-word;
        }
        
        /* Animation de chargement pour améliorer l'UX */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        .character-card {
            animation: fadeIn 0.5s ease-in-out;
        }

        .logo:hover {
            transform: translateY(-4px);
            transition: 0.2s;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <a class="logo" href="index.html"></a>
            <h1>Dragon Ball Z - Personnages</h1>
            <p>Découvrez les personnages de l'univers Dragon Ball</p>
        </header>

        <!-- Barre de recherche -->
        <div class="search-container">
            <form class="search-form" method="GET" action="">
                <input type="text" name="search" class="search-input" placeholder="Rechercher un personnage..." value="<?php echo htmlspecialchars($searchTerm); ?>">
                <button type="submit" class="search-button">Rechercher</button>
            </form>
            <?php if (!empty($searchTerm)): ?>
                <a href="?" class="reset-search">Effacer la recherche</a>
            <?php endif; ?>
        </div>

        <?php if (!empty($characters)): ?>
            <div class="characters-count">
                <?php echo count($characters); ?> personnage(s) trouvé(s)
                <?php if (!empty($searchTerm)): ?>
                    pour "<?php echo htmlspecialchars($searchTerm); ?>"
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="character-grid">
            <?php if (empty($characters)): ?>
                <?php if (!empty($searchTerm)): ?>
                    <!-- Message si aucun résultat pour la recherche -->
                    <div class="no-results">
                        <h2>Aucun personnage trouvé</h2>
                        <p>Aucun personnage ne correspond à votre recherche "<?php echo htmlspecialchars($searchTerm); ?>".</p>
                    </div>
                <?php else: ?>
                    <!-- Message si pas de personnages disponibles -->
                    <div class="error-message">
                        <h2>Impossible de charger les personnages</h2>
                        <p>L'API Dragon Ball semble être indisponible pour le moment.</p>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <!-- Affichage des personnages -->
                <?php foreach ($characters as $character): ?>
                    <div class="character-card">
                        <!-- Image du personnage -->
                        <div class="character-image">
                            <?php if (!empty($character['image'])): ?>
                                <img src="<?php echo htmlspecialchars($character['image']); ?>" 
                                     alt="<?php echo htmlspecialchars($character['name']); ?>">
                            <?php else: ?>
                                <div style="width:150px;height:150px;background:#9370DB;color:white;display:flex;justify-content:center;align-items:center;border-radius:50%;font-size:clamp(0.8rem, 3vw, 1rem);padding:10px;text-align:center;">
                                    <?php echo htmlspecialchars($character['name']); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Informations sur le personnage -->
                        <div class="character-info">
                            <!-- ID du personnage -->
                            <?php if (!empty($character['id'])): ?>
                                <div class="character-id">#<?php echo str_pad($character['id'], 3, '0', STR_PAD_LEFT); ?></div>
                            <?php endif; ?>
                            
                            <!-- Nom du personnage -->
                            <h2 class="character-name"><?php echo htmlspecialchars($character['name']); ?></h2>
                            
                            <!-- Race du personnage -->
                            <?php if (!empty($character['race'])): ?>
                                <span class="race-badge" style="background-color: <?php echo $raceColors[$character['race']] ?? '#8A2BE2'; ?>">
                                    <?php echo htmlspecialchars($character['race']); ?>
                                </span>
                            <?php endif; ?>
                            
                            <!-- Description du personnage -->
                            <?php if (!empty($character['description'])): ?>
                                <div class="character-description">
                                    <?php echo htmlspecialchars($character['description']); ?>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Statistiques du personnage -->
                            <?php if (!empty($character['ki']) || !empty($character['maxKi'])): ?>
                                <div class="character-stats">
                                    <?php if (!empty($character['ki'])): ?>
                                        <span>Ki: <?php echo htmlspecialchars($character['ki']); ?></span>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($character['maxKi'])): ?>
                                        <span>Max Ki: <?php echo htmlspecialchars($character['maxKi']); ?></span>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="footer">
            <p>Données fournies par <a href="https://web.dragonball-api.com/" target="_blank">Dragon Ball API</a></p>
        </div>
    </div>
</body>
</html>