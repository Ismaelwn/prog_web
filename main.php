<?php
session_start();
$isConnected = isset($_SESSION["username"]);
$currentUser = $isConnected ? $_SESSION["username"] : '';

// Charger les utilisateurs pour vérifier les likes actuels
$users = [];
if ($isConnected) {
    $usersJson = file_get_contents('json/users.json');
    $users = json_decode($usersJson, true);
    $userLikes = [];
    
    // Trouver les likes de l'utilisateur actuel
    foreach ($users as $user) {
        if ($user['username'] === $currentUser) {
            $userLikes = isset($user['likes']) ? $user['likes'] : [];
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Re7 - Recettes</title>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/script.js"></script>
    <script src="js/like.js"></script>
</head>
<body>
    <header>
        <nav>
            <ul>
                <li><a href="#">Re7</a></li>
                <li><a href="#">Rechercher</a></li>
                <li><a href="#">À propos</a></li>
                <li><a href="#">Contact</a></li>  
                <li><a href="favoris.php">Favoris</a></li>  
                <li><button>Langue</button></li>

                <?php if ($isConnected): ?>
                    <li class="user-menu">
                        <span class="username"><?= htmlspecialchars($_SESSION["username"]) ?> ▼</span>
                        <div class="dropdown-menu">
                            <a href="account.php">Votre compte</a>
                            <a href="account.php">Mon profil</a> <!-- les post et les recettes à la twitter -->
                            <a href="account.php">Support</a>
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
            <div class="recipes">
                <?php foreach ($recipes as $recipe): ?>
                    <?php 
                        $recipeNameFR = htmlspecialchars($recipe['nameFR'] ?? 'Nom inconnu');
                        $recipeName = htmlspecialchars($recipe['name'] ?? 'Unknown name');
                        $isLiked = $isConnected && isset($userLikes) && in_array($recipeNameFR, $userLikes);
                        $likeCount = isset($recipe['likers']) ? count($recipe['likers']) : 0;
                    ?>
                    <div class="recipe-card">
                        <?php if (!empty($recipe['imageURL'])): ?>
                            <img src="<?= htmlspecialchars($recipe['imageURL']) ?>" alt="<?= $recipeNameFR ?>">
                        <?php else: ?>
                            <div class="no-image">Image non disponible</div>
                        <?php endif; ?>
                        <h3><?= $recipeNameFR ?></h3>
                        <p>Auteur : <?= htmlspecialchars($recipe['Author'] ?? 'Auteur inconnu') ?></p>
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

</body>
</html>
