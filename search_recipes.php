<?php
session_start();
$isConnected = isset($_SESSION["username"]);
$currentUser = $isConnected ? $_SESSION["username"] : '';

$query = isset($_GET['q']) ? strtolower(trim($_GET['q'])) : '';

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

$recipes = json_decode(file_get_contents('json/recipes.json'), true);
if (!$recipes) {
    die("Erreur lors du chargement des recettes.");
}

$filtered = array_filter($recipes, function ($recipe) use ($query) {
    $nameFR = strtolower($recipe['nameFR'] ?? '');
    $nameEN = strtolower($recipe['nameEN'] ?? '');
    return str_contains($nameFR, $query) || str_contains($nameEN, $query);
});
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Résultats de recherche - Re7</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/like.js"></script>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header>
    <nav>
        <ul>
            <li><a href="main.php">← Retour à l'accueil</a></li>
            <li><strong>Résultats de recherche</strong></li>
            <?php if ($isConnected): ?>
                <li class="user-menu">
                    <span class="username"><?= htmlspecialchars($currentUser) ?> ▼</span>
                    <div class="dropdown-menu">
                        <a href="account.php">Votre compte</a>
                        <a href="logout.php">Se déconnecter</a>
                    </div>
                </li>
            <?php else: ?>
                <li><a href="create_login.php">Se connecter</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>

<main>
    <h1>Résultats pour : "<?= htmlspecialchars($query) ?>"</h1>

    <div class="recipes" id="recipes-container">
        <?php if (empty($filtered)): ?>
            <p>Aucune recette trouvée.</p>
        <?php else: ?>
            <?php foreach ($filtered as $recipe): ?>
                <?php
                    $recipeNameFR = htmlspecialchars($recipe['nameFR'] ?? 'Nom inconnu');
                    $isLiked = in_array($recipeNameFR, $userLikes);
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
                        <img src="<?= htmlspecialchars($recipe['imageURL']) ?>" alt="<?= $recipeNameFR ?>">
                    <?php else: ?>
                        <div class="no-image">Image non disponible</div>
                    <?php endif; ?>

                    <h3><?= $recipeNameFR ?></h3>
                    <p><strong>Auteur :</strong> <?= htmlspecialchars($recipe['Author'] ?? 'Auteur inconnu') ?></p>
                    <?php if ($ingredientsHTML): ?>
                        <p><strong>Ingrédients :</strong><br><?= $ingredientsHTML ?></p>
                    <?php endif; ?>
                    <?php if ($instructionsHTML): ?>
                        <p><strong>Instructions :</strong><br><?= $instructionsHTML ?></p>
                    <?php endif; ?>
                    <?php if ($allergenes): ?>
                        <p><strong>Allergènes :</strong><br><?= $allergenes ?></p>
                    <?php endif; ?>

                    <button class="like-btn" 
                            data-recipe="<?= $recipeNameFR ?>"
                            data-liked="<?= $isLiked ? 'true' : 'false' ?>"
                            data-count="<?= $likeCount ?>">
                        <?= $isLiked ? '❤' : '♡' ?> <?= $likeCount ?>
                    </button>
                    <a href="details.php?id=<?= urlencode($recipeNameFR) ?>" class="more-btn">+ Plus</a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</main>

<footer>
    <p>&copy; 2025 Re7. Tous droits réservés.</p>
</footer>

<script>
    $(document).ready(function () {
        if (typeof attachLikeHandlers === 'function') {
            attachLikeHandlers();
        }
    });
</script>
</body>
</html>
