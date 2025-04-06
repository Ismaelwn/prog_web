<?php
session_start();
$isConnected = isset($_SESSION["username"]);
$currentUser = $isConnected ? $_SESSION["username"] : '';
$currentRoles = [];

if ($isConnected) {
    $users = json_decode(file_get_contents('json/users.json'), true);
    foreach ($users as $user) {
        if ($user['username'] === $currentUser) {
            $currentRoles = $user['role'] ?? [];
            $userLikes = $user['likes'] ?? [];
            break;
        }
    }
}

$recipes = json_decode(file_get_contents('json/recipes.json'), true);
if (!$recipes) {
    die("Erreur lors du chargement des recettes.");
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Re7 - Recettes</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/script.js"></script>
    <script src="js/like.js"></script>
</head>
<body>
<header>
    <nav>
        <ul>
            <li><a href="#">Re7</a></li>
            <li>
                <input type="text" id="search-input" placeholder="Rechercher une recette...">
                <button id="search-btn">Rechercher</button>
            </li>
            <li><a href="#">À propos</a></li>
            <li><a href="#">Contact</a></li>
            <li><a href="favoris.php">Favoris</a></li>
            <li><button>Langue</button></li>

            <?php if ($isConnected): ?>
                <?php if (in_array('chef', $currentRoles)): ?>
                    <li><a href="valider_recettes.php">Valider des recettes</a></li>
                <?php elseif (in_array('askchef', $currentRoles)): ?>
                    <li><span>Demande de rôle chef en attente</span></li>
                <?php endif; ?>
                <?php if (in_array('traducteur', $currentRoles)): ?>
                    <li><a href="traduire_recette.php">Traduire une recette</a></li>
                <?php elseif (in_array('asktraducteur', $currentRoles)): ?>
                    <li><span>Demande de rôle traducteur en attente</span></li>
                <?php endif; ?>
                <?php if (in_array('admin', $currentRoles)): ?>
                    <li><a href="admin_panel.php">Administration</a></li>
                <?php endif; ?>

                <li class="user-menu">
                    <span class="username"><?= htmlspecialchars($_SESSION["username"]) ?> ▼</span>
                    <div class="dropdown-menu">
                    <a href="account.php">Votre compte</a>
                    <a href="profil.php">Mon profil</a>
                    <a href="support.php">Support</a>
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
    <h1>Bienvenue sur Re7</h1>
    <section>
        <h2>Nos recettes</h2>
        <div class="recipes" id="recipes-container">
            <?php foreach ($recipes as $recipe): ?>
                <?php 
                    $recipeNameFR = htmlspecialchars($recipe['nameFR'] ?? 'Nom inconnu');
                    $isLiked = $isConnected && in_array($recipeNameFR, $userLikes ?? []);
                    $likeCount = isset($recipe['likers']) ? count($recipe['likers']) : 0;

                    // Affichage des ingrédients
                    $ingredientsHTML = '';
                    if (isset($recipe['ingredients'])) {
                        if (is_array($recipe['ingredients'])) {
                            $items = [];
                            foreach ($recipe['ingredients'] as $ing) {
                                if (is_array($ing) && isset($ing['name'])) {
                                    $desc = htmlspecialchars($ing['quantity'] ?? '') . ' ' . htmlspecialchars($ing['name']);
                                    $desc = trim($desc);
                                    $items[] = $desc;
                                } elseif (is_string($ing)) {
                                    $items[] = htmlspecialchars($ing);
                                }
                            }
                            $ingredientsHTML = implode('<br>', $items);
                        } else {
                            $ingredientsHTML = nl2br(htmlspecialchars($recipe['ingredients']));
                        }
                    }

                    $instructions = isset($recipe['instructions'])
                        ? (is_array($recipe['instructions'])
                            ? implode('<br>', array_map('htmlspecialchars', $recipe['instructions']))
                            : nl2br(htmlspecialchars($recipe['instructions'])))
                        : '';
                ?>
                <div class="recipe-card">
                    <?php if (!empty($recipe['imageURL'])): ?>
                        <img src="<?= htmlspecialchars($recipe['imageURL']) ?>" alt="<?= $recipeNameFR ?>">
                    <?php else: ?>
                        <div class="no-image">Image non disponible</div>
                    <?php endif; ?>
                    <h3><?= $recipeNameFR ?></h3>
                    <p><strong>Auteur :</strong> <?= htmlspecialchars($recipe['Author'] ?? 'Auteur inconnu') ?></p>
                    <p><strong>Ingrédients :</strong><br><?= $ingredientsHTML ?></p>
                    <p><strong>Instructions :</strong><br><?= $instructions ?></p>
                    <button class="like-btn" 
                            data-recipe="<?= $recipeNameFR ?>"
                            data-liked="<?= $isLiked ? 'true' : 'false' ?>"
                            data-count="<?= $likeCount ?>">
                        <?= $isLiked ? '❤' : '♡' ?> <?= $likeCount ?>
                    </button>
                    <a href="details.php?id=<?= urlencode($recipeNameFR) ?>" class="more-btn">+ Plus</a>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
</main>

<footer>
    <p>&copy; 2025 Mon Site Web. Tous droits réservés.</p>
</footer>

<script>
    $(document).ready(function () {
        $('#search-btn').on('click', function () {
            let query = $('#search-input').val().trim();
            if (query.length === 0) return;

            $.ajax({
                url: 'search_recipes.php',
                method: 'GET',
                data: { q: query },
                success: function (data) {
                    $('#recipes-container').html(data);
                    if (typeof attachLikeHandlers === 'function') {
                        attachLikeHandlers();
                    }
                },
                error: function () {
                    alert("Erreur lors de la recherche.");
                }
            });
        });
    });
</script>

</body>
</html>
