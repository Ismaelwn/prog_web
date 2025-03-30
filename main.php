<?php
// Charger les recettes depuis recipes.json
$recipes = json_decode(file_get_contents('json/recipes.json'), true);

// Vérifier si le fichier a bien été chargé
if (!$recipes) {
    die("Erreur lors du chargement des recettes.");
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Re7 - Recettes</title>
    <link rel="stylesheet" href="css/main.css">
</head>
<body>
    <header>
        <nav>
            <ul>
                <li><a href="#">Re7</a></li>
                <li><a href="#">Rechercher</a></li>
                <li><a href="#">À propos</a></li>
                <li><a href="#">Contact</a></li>  
                <li><a href="#">Favoris</a></li>  
                <li><button>Langue</button></li>
                <li><a href="create_login.php">Se connecter</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <h1>Bienvenue sur Re7</h1>
        <section>
            <h2>Nos recettes</h2>
            <div class="recipes">
    <?php foreach ($recipes as $recipe): ?>
        <div class="recipe-card">
            <!-- Vérifie si l'URL de l'image existe avant d'afficher -->
            <?php if (isset($recipe['imageURL']) && !empty($recipe['imageURL'])): ?>
                <img src="<?= htmlspecialchars($recipe['imageURL']) ?>" alt="Image de <?= htmlspecialchars($recipe['nameFR'] ?? 'Recette inconnue') ?>">
            <?php else: ?>
                <!-- Affiche un carré blanc si l'image est manquante -->
                <div class="no-image">Image non disponible</div>
            <?php endif; ?>

            <!-- Vérification de la clé "nameFR" -->
            <h3><?= htmlspecialchars($recipe['nameFR'] ?? $recipe['name'] ?? 'Nom de recette inconnu') ?></h3>

            <!-- Vérification de la clé "Author" -->
            <p>Auteur : <?= htmlspecialchars($recipe['Author'] ?? 'Auteur inconnu') ?></p>

            <button class="like-btn">Like</button>
            <a href="details.php?id=<?= urlencode($recipe['nameFR'] ?? $recipe['name']) ?>" class="more-btn">+ Plus</a>
        </div>
    <?php endforeach; ?>
</div>

        </section>
    </main>
    <footer>
        <p>&copy; 2025 Mon Site Web. Tous droits réservés.</p>
    </footer>
</body>
</html>
