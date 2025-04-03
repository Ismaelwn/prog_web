<?php
if (!isset($_GET['id'])) {
    die("Recette non trouvée.");
}

$recipeNameFR = urldecode($_GET['id']);

// Charger les recettes depuis le fichier JSON
$recipes = json_decode(file_get_contents('json/recipes.json'), true);
if (!$recipes) {
    die("Erreur lors du chargement des recettes.");
}

// Rechercher la recette correspondante
$recipe = null;
foreach ($recipes as $r) {
    if ($r['nameFR'] == $recipeNameFR) {
        $recipe = $r;
        break;
    }
}

if (!$recipe) {
    die("Recette non trouvée.");
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recette - <?= htmlspecialchars($recipe['nameFR']) ?></title>
</head>
<body>
    <header>
        <nav>
            <ul>
                <li><a href="index.php">Retour à l'accueil</a></li>
                <li><a href="favoris.php">Favoris</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <h1><?= htmlspecialchars($recipe['nameFR']) ?></h1>
        <div class="recipe-detail">
            <?php if (!empty($recipe['imageURL'])): ?>
                <img src="<?= htmlspecialchars($recipe['imageURL']) ?>" alt="<?= htmlspecialchars($recipe['nameFR']) ?>" />
            <?php else: ?>
                <div class="no-image">Image non disponible</div>
            <?php endif; ?>
            <p><strong>Auteur :</strong> <?= htmlspecialchars($recipe['Author']) ?></p>
            <p><strong>Ingrédients :</strong> <?= htmlspecialchars($recipe['ingredients']) ?></p>
            <p><strong>Instructions :</strong> <?= htmlspecialchars($recipe['instructions']) ?></p>
        </div>

        <h2>Commentaires</h2>
        <div class="comments">
            <!-- Espace pour les commentaires -->
            <textarea placeholder="Laissez un commentaire..."></textarea>
            <button>Ajouter le commentaire</button>
        </div>
    </main>

    <footer>
        <p>&copy; 2025 Mon Site Web. Tous droits réservés.</p>
    </footer>
</body>
</html>
