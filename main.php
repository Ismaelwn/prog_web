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
    <title><?= ($_SESSION['lang'] == 'fr') ? 'Re7 - Recettes' : 'Re7 - Recipes' ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/script.js"></script>
    <script src="js/like.js"></script>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header>
    <nav>
        <ul>
            <li><a href="#">Re7</a></li>
            <li>
                <input type="text" id="search-input" placeholder="<?= ($_SESSION['lang'] == 'fr') ? 'Rechercher une recette...' : 'Search for a recipe...'; ?>">
                <button id="search-btn"><?= ($_SESSION['lang'] == 'fr') ? 'Rechercher' : 'Search' ?></button>
            </li>
            <li><a href="apropos.php"><?= ($_SESSION['lang'] == 'fr') ? 'À propos' : 'About' ?></a></li>
            <li><a href="favoris.php"><?= ($_SESSION['lang'] == 'fr') ? 'Favoris' : 'Favorites' ?></a></li>

            <!-- Formulaire de sélection de langue -->
            <form method="POST" action="main.php">
                <select name="lang" onchange="this.form.submit()">
                    <option value="fr" <?= $_SESSION['lang'] == 'fr' ? 'selected' : '' ?>>Français</option>
                    <option value="en" <?= $_SESSION['lang'] == 'en' ? 'selected' : '' ?>>English</option>
                </select>
            </form>

            <?php if ($isConnected): ?>
                <?php if (in_array('chef', $currentRoles)): ?>
                    <li><a href="valider_recettes.php"><?= ($_SESSION['lang'] == 'fr') ? 'Valider des recettes' : 'Validate Recipes' ?></a></li>
                <?php elseif (in_array('askchef', $currentRoles)): ?>
                    <li><span><?= ($_SESSION['lang'] == 'fr') ? 'Demande de rôle chef en attente' : 'Chef role request pending' ?></span></li>
                <?php endif; ?>
                <?php if (in_array('traducteur', $currentRoles)): ?>
                    <li><a href="traduire_recette.php"><?= ($_SESSION['lang'] == 'fr') ? 'Traduire une recette' : 'Translate a Recipe' ?></a></li>
                <?php elseif (in_array('asktraducteur', $currentRoles)): ?>
                    <li><span><?= ($_SESSION['lang'] == 'fr') ? 'Demande de rôle traducteur en attente' : 'Translator role request pending' ?></span></li>
                <?php endif; ?>
                <?php if (in_array('admin', $currentRoles)): ?>
                    <li><a href="admin_panel.php"><?= ($_SESSION['lang'] == 'fr') ? 'Administration' : 'Admin Panel' ?></a></li>
                <?php endif; ?>

                <li class="user-menu">
                    <span class="username"><?= htmlspecialchars($_SESSION["username"]) ?> ▼</span>
                    <div class="dropdown-menu">
                    <a href="account.php"><?= ($_SESSION['lang'] == 'fr') ? 'Votre compte' : 'Your Account' ?></a>
                    <a href="profil.php"><?= ($_SESSION['lang'] == 'fr') ? 'Mon profil' : 'My Profile' ?></a>
                    <a href="logout.php"><?= ($_SESSION['lang'] == 'fr') ? 'Se déconnecter' : 'Log out' ?></a>
                </div>
                </li>
            <?php else: ?>
                <li><a href="create_login.php"><?= ($_SESSION['lang'] == 'fr') ? 'Se connecter' : 'Log in' ?></a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>

<main>
    <section>
        <div class="recipes" id="recipes-container">
            <?php foreach ($recipes as $recipe): ?>
                <?php 
                    $recipeName = $_SESSION['lang'] == 'fr' ? htmlspecialchars($recipe['nameFR'] ?? 'Nom inconnu') : htmlspecialchars($recipe['name'] ?? 'Unknown name');
                    $isLiked = $isConnected && in_array($recipeName, $userLikes ?? []);
                    $likeCount = isset($recipe['likers']) ? count($recipe['likers']) : 0;
                ?>
                <div class="recipe-card">
                    <?php if (!empty($recipe['imageURL'])): ?>
                        <img src="<?= htmlspecialchars($recipe['imageURL']) ?>" alt="<?= $recipeName ?>">
                    <?php else: ?>
                        <div class="no-image"><?= ($_SESSION['lang'] == 'fr') ? 'Image non disponible' : 'Image not available' ?></div>
                    <?php endif; ?>
                    <h3><?= $recipeName ?></h3>
                    <p><strong><?= ($_SESSION['lang'] == 'fr') ? 'Auteur :' : 'Author :' ?></strong> <?= htmlspecialchars($recipe['Author'] ?? 'Auteur inconnu') ?></p>
                    <button class="like-btn" 
                            data-recipe="<?= $recipeName ?>"
                            data-liked="<?= $isLiked ? 'true' : 'false' ?>"
                            data-count="<?= $likeCount ?>">
                        <?= $isLiked ? '❤' : '♡' ?> <?= $likeCount ?>
                    </button>
                    <a href="details.php?id=<?= urlencode($recipeName) ?>" class="more-btn">
                        <?php 
                            echo ($_SESSION['lang'] == 'fr') ? "+ Plus" : "+ More";
                        ?>
                    </a>
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
