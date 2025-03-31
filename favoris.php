<?php
session_start();

// Vérifier si l'utilisateur est connecté
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

// Filtrer les recettes que l'utilisateur a likées
$likedRecipes = [];
foreach ($recipes as $recipe) {
    if (in_array($recipe['name'], $userLikes) || in_array($recipe['nameFR'], $userLikes)) {
        $likedRecipes[] = $recipe;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Favoris - Re7</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/script.js"></script>
    <script src="js/like.js"></script>
</head>
<body>
    <header>
        <nav>
            <ul>
                <li><a href="main.php">Re7</a></li>
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
        <h1>Mes recettes favorites</h1>
        <?php if (empty($likedRecipes)): ?>
            <p>Aucune recette n'est ajoutée à vos favoris.</p>
        <?php else: ?>
            <div class="recipes">
                <?php foreach ($likedRecipes as $recipe): ?>
                    <div class="recipe-card">
                        <?php if (!empty($recipe['imageURL'])): ?>
                            <img src="<?= htmlspecialchars($recipe['imageURL']) ?>" alt="<?= htmlspecialchars($recipe['nameFR']) ?>">
                        <?php else: ?>
                            <div class="no-image">Image non disponible</div>
                        <?php endif; ?>
                        <h3><?= htmlspecialchars($recipe['nameFR']) ?></h3>
                        <p>Auteur : <?= htmlspecialchars($recipe['Author'] ?? 'Auteur inconnu') ?></p>
                        <button class="like-btn" 
                                data-recipe="<?= htmlspecialchars($recipe['nameFR']) ?>"
                                data-liked="true" 
                                data-count="<?= isset($recipe['likers']) ? count($recipe['likers']) : 0 ?>">
                            ❤ <?= isset($recipe['likers']) ? count($recipe['likers']) : 0 ?>
                        </button>
                        <a href="details.php?id=<?= urlencode($recipe['name']) ?>" class="more-btn">+ Plus</a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>

    <footer>
        <p>&copy; 2025 Mon Site Web. Tous droits réservés.</p>
    </footer>

</body>
</html>
