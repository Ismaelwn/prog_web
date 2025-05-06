<?php
session_start();

// Vérifier si une langue a été sélectionnée
if (isset($_POST['lang'])) {
    $_SESSION['lang'] = $_POST['lang'];  // Enregistrer la langue choisie dans la session
} else {
    // Si la langue n'est pas définie, utiliser la langue par défaut
    if (!isset($_SESSION['lang'])) {
        $_SESSION['lang'] = 'fr';  // Langue par défaut : français
    }
}

if (!isset($_GET['id'])) {
    die($_SESSION['lang'] == 'fr' ? "Recette non trouvée." : "Recipe not found.");
}

$recipeNameFR = urldecode($_GET['id']);

// Charger les recettes depuis le fichier JSON
$recipes = json_decode(file_get_contents('json/recipes.json'), true);
if (!$recipes) {
    die($_SESSION['lang'] == 'fr' ? "Erreur lors du chargement des recettes." : "Error loading recipes.");
}

// Rechercher la recette correspondante
$recipe = null;
foreach ($recipes as $r) {
    if ($_SESSION['lang'] == 'fr' && $r['nameFR'] == $recipeNameFR) {
        $recipe = $r;
        break;
    }
    if ($_SESSION['lang'] == 'en' && $r['name'] == $recipeNameFR) {
        $recipe = $r;
        break;
    }
}

if (!$recipe) {
    die($_SESSION['lang'] == 'fr' ? "Recette non trouvée." : "Recipe not found.");
}

// Charger les commentaires
$comments = json_decode(file_get_contents('json/comments.json'), true);
if (!$comments) {
    $comments = [];
}

// Filtrer les commentaires associés à la recette
$recipeComments = [];
foreach ($comments as $comment) {
    if ($comment['recipeId'] == $recipe[$_SESSION['lang'] == 'fr' ? 'nameFR' : 'name']) {
        $recipeComments[] = $comment;
    }
}
?>

<!DOCTYPE html>
<html lang="<?= $_SESSION['lang'] == 'fr' ? 'fr' : 'en' ?>">
<head>
    <meta charset="UTF-8">
    <title>Recette - <?= htmlspecialchars($recipe[$_SESSION['lang'] == 'fr' ? 'nameFR' : 'name']) ?></title>
</head>
<body>
    <header>
        <nav>
            <ul>
                <li><a href="main.php"><?= ($_SESSION['lang'] == 'fr') ? 'Retour à l\'accueil' : 'Back to Home' ?></a></li>
                <li><a href="favoris.php"><?= ($_SESSION['lang'] == 'fr') ? 'Favoris' : 'Favorites' ?></a></li>
            </ul>
        </nav>
    </header>

    <main>
        <h1><?= htmlspecialchars($recipe[$_SESSION['lang'] == 'fr' ? 'nameFR' : 'name']) ?></h1>
        <div class="recipe-detail">
            <?php if (!empty($recipe['imageURL'])): ?>
                <img src="<?= htmlspecialchars($recipe['imageURL']) ?>" alt="<?= htmlspecialchars($recipe[$_SESSION['lang'] == 'fr' ? 'nameFR' : 'name']) ?>" />
            <?php else: ?>
                <div class="no-image"><?= ($_SESSION['lang'] == 'fr') ? 'Image non disponible' : 'Image not available' ?></div>
            <?php endif; ?>
            <p><strong><?= ($_SESSION['lang'] == 'fr') ? 'Auteur :' : 'Author :' ?></strong> <?= htmlspecialchars($recipe['Author'] ?? ($_SESSION['lang'] == 'fr' ? 'Auteur inconnu' : 'Unknown author')) ?></p>
            
            <p><strong><?= ($_SESSION['lang'] == 'fr') ? 'Ingrédients :' : 'Ingredients :' ?></strong><br>
                <?= htmlspecialchars($recipe['ingredients'] ?? ($_SESSION['lang'] == 'fr' ? 'Ingrédients non disponibles' : 'Ingredients not available')) ?>
            </p>

            <p><strong><?= ($_SESSION['lang'] == 'fr') ? 'Instructions :' : 'Instructions :' ?></strong><br>
                <?= htmlspecialchars($recipe['instructions'] ?? ($_SESSION['lang'] == 'fr' ? 'Instructions non disponibles' : 'Instructions not available')) ?>
            </p>
        </div>

        <h2><?= ($_SESSION['lang'] == 'fr') ? 'Commentaires' : 'Comments' ?></h2>
        <div class="comments">
            <!-- Affichage des commentaires -->
            <?php if (empty($recipeComments)): ?>
                <p><?= ($_SESSION['lang'] == 'fr') ? 'Aucun commentaire pour cette recette.' : 'No comments for this recipe.' ?></p>
            <?php else: ?>
                <?php foreach ($recipeComments as $comment): ?>
                    <div class="comment">
                        <p><strong><?= htmlspecialchars($comment['user']) ?></strong> - <?= date('d/m/Y H:i', $comment['timestamp']) ?></p>
                        <p><?= htmlspecialchars($comment['text']) ?></p>
                        <?php if (!empty($comment['image'])): ?>
                            <img src="<?= htmlspecialchars('images/'.$comment['image']) ?>" alt="Image de l'utilisateur" />
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <!-- Formulaire pour ajouter un commentaire -->
            <textarea placeholder="<?= ($_SESSION['lang'] == 'fr') ? 'Laissez un commentaire...' : 'Leave a comment...'; ?>"></textarea>
            <button><?= ($_SESSION['lang'] == 'fr') ? 'Ajouter le commentaire' : 'Add Comment' ?></button>
        </div>
    </main>

    <footer>
        <p>&copy; 2025 Mon Site Web. <?= ($_SESSION['lang'] == 'fr') ? 'Tous droits réservés.' : 'All rights reserved.' ?></p>
    </footer>
</body>
</html>
