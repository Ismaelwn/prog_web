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

$isConnected = isset($_SESSION["username"]);
$currentUser = $isConnected ? $_SESSION["username"] : '';
$userLikes = [];

if ($isConnected) {
    $users = json_decode(file_get_contents('json/users.json'), true);
    foreach ($users as $user) {
        if ($user['username'] === $currentUser) {
            $userLikes = $user['likes'] ?? [];
            break;
        }
    }
}

$query = isset($_GET['q']) ? strtolower(trim($_GET['q'])) : '';

$recipes = json_decode(file_get_contents('json/recipes.json'), true);
if (!$recipes) {
    die("Erreur lors du chargement des recettes.");
}

// Filtrer les recettes en fonction de la langue et du texte de recherche
$filtered = array_filter($recipes, function ($recipe) use ($query) {
    $name = ($_SESSION['lang'] == 'fr') ? strtolower($recipe['nameFR'] ?? '') : strtolower($recipe['name'] ?? '');
    return str_contains($name, $query);
});
?>

<!DOCTYPE html>
<html lang="<?= $_SESSION['lang'] == 'fr' ? 'fr' : 'en' ?>">
<head>
    <meta charset="UTF-8">
    <title><?= ($_SESSION['lang'] == 'fr') ? 'Résultats de recherche - Re7' : 'Search Results - Re7' ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/like.js"></script>
</head>
<body>
<header>
    <nav>
        <ul>
            
            
            <!-- Formulaire de sélection de langue -->
            <form method="POST" action="search_recipes.php">
                <select name="lang" onchange="this.form.submit()">
                    <option value="fr" <?= $_SESSION['lang'] == 'fr' ? 'selected' : '' ?>>Français</option>
                    <option value="en" <?= $_SESSION['lang'] == 'en' ? 'selected' : '' ?>>English</option>
                </select>
            </form>

        </ul>
    </nav>
</header>

<main>
    <section>
        <h1><?= ($_SESSION['lang'] == 'fr') ? 'Résultats pour : "' . htmlspecialchars($query) . '"' : 'Results for: "' . htmlspecialchars($query) . '"' ?></h1>

        <div class="recipes" id="recipes-container">
            <?php if (empty($filtered)): ?>
                <p><?= ($_SESSION['lang'] == 'fr') ? "Aucune recette trouvée." : "No recipes found." ?></p>
            <?php else: ?>
                <?php foreach ($filtered as $recipe): ?>
                    <?php
                        $recipeName = ($_SESSION['lang'] == 'fr') ? htmlspecialchars($recipe['nameFR'] ?? 'Nom inconnu') : htmlspecialchars($recipe['name'] ?? 'Unknown name');
                        $isLiked = in_array($recipeName, $userLikes);
                        $likeCount = isset($recipe['likers']) ? count($recipe['likers']) : 0;

                        // Ingrédients
                        $ingredientsHTML = '';
                        if (isset($recipe['ingredients'])) {
                            if (is_array($recipe['ingredients'])) {
                                $items = [];
                                foreach ($recipe['ingredients'] as $ing) {
                                    if (is_array($ing) && isset($ing['name'])) {
                                        $desc = htmlspecialchars($ing['quantity'] ?? '') . ' ' . htmlspecialchars($ing['name']);
                                        $items[] = trim($desc);
                                    } elseif (is_string($ing)) {
                                        $items[] = htmlspecialchars($ing);
                                    }
                                }
                                $ingredientsHTML = implode('<br>', $items);
                            } else {
                                $ingredientsHTML = nl2br(htmlspecialchars($recipe['ingredients']));
                            }
                        }

                        // Instructions
                        $instructionsHTML = '';
                        if (isset($recipe['instructions'])) {
                            if (is_array($recipe['instructions'])) {
                                $instructionsHTML = implode('<br>', array_map('htmlspecialchars', $recipe['instructions']));
                            } else {
                                $instructionsHTML = nl2br(htmlspecialchars($recipe['instructions']));
                            }
                        }

                        // Allergènes
                        $allergenes = '';
                        if (!empty($recipe['allergenes'])) {
                            $allergenes = is_array($recipe['allergenes'])
                                ? implode(', ', array_map('htmlspecialchars', $recipe['allergenes']))
                                : htmlspecialchars($recipe['allergenes']);
                        }
                    ?>
                    <div class="recipe-card">
                        <?php if (!empty($recipe['imageURL'])): ?>
                            <img src="<?= htmlspecialchars($recipe['imageURL']) ?>" alt="<?= $recipeName ?>">
                        <?php else: ?>
                            <div class="no-image"><?= ($_SESSION['lang'] == 'fr') ? 'Image non disponible' : 'Image not available' ?></div>
                        <?php endif; ?>

                        <h3><?= $recipeName ?></h3>
                        <p><strong><?= ($_SESSION['lang'] == 'fr') ? 'Auteur :' : 'Author :' ?></strong> <?= htmlspecialchars($recipe['Author'] ?? 'Auteur inconnu') ?></p>
                        <?php if ($ingredientsHTML): ?>
                            <p><strong><?= ($_SESSION['lang'] == 'fr') ? 'Ingrédients :' : 'Ingredients :' ?></strong><br><?= $ingredientsHTML ?></p>
                        <?php endif; ?>
                        <?php if ($instructionsHTML): ?>
                            <p><strong><?= ($_SESSION['lang'] == 'fr') ? 'Instructions :' : 'Instructions :' ?></strong><br><?= $instructionsHTML ?></p>
                        <?php endif; ?>
                        <?php if ($allergenes): ?>
                            <p><strong><?= ($_SESSION['lang'] == 'fr') ? 'Allergènes :' : 'Allergens :' ?></strong><br><?= $allergenes ?></p>
                        <?php endif; ?>

                        <button class="like-btn" 
                                data-recipe="<?= $recipeName ?>"
                                data-liked="<?= $isLiked ? 'true' : 'false' ?>"
                                data-count="<?= $likeCount ?>">
                            <?= $isLiked ? '❤' : '♡' ?> <?= $likeCount ?>
                        </button>
                        <a href="details.php?id=<?= urlencode($recipeName) ?>" class="more-btn"><?= ($_SESSION['lang'] == 'fr') ? '+ Plus' : '+ More' ?></a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>
</main>



<script>
    $(document).ready(function () {
        if (typeof attachLikeHandlers === 'function') {
            attachLikeHandlers();
        }
    });
</script>

</body>
</html>
