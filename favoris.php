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

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION["username"])) {
    // Si l'utilisateur n'est pas connecté, redirigez-le vers la page de connexion
    header("Location: create_login.php");
    exit(); // Assurez-vous que le script s'arrête après la redirection
}

$isConnected = isset($_SESSION["username"]);
$currentUser = $isConnected ? $_SESSION["username"] : '';
$userLikes = [];

// Charger les utilisateurs pour vérifier les likes actuels
if ($isConnected) {
    $users = json_decode(file_get_contents('json/users.json'), true);
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
    <title><?= ($_SESSION['lang'] == 'fr') ? 'Favoris - Re7' : 'Favorites - Re7' ?></title>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/script.js"></script>
    <script src="js/like.js"></script>
</head>
<body>
    <header>
        <nav>
            <ul>
                <li><a href="main.php">Re7</a></li>
                <li><a href="apropos.php"><?= ($_SESSION['lang'] == 'fr') ? 'À propos' : 'About' ?></a></li>
                <li><a href="favoris.php"><?= ($_SESSION['lang'] == 'fr') ? 'Favoris' : 'Favorites' ?></a></li>  
                

                <!-- Formulaire de sélection de langue -->
                <form method="POST" action="favoris.php">
                    <select name="lang" onchange="this.form.submit()">
                        <option value="fr" <?= $_SESSION['lang'] == 'fr' ? 'selected' : '' ?>>Français</option>
                        <option value="en" <?= $_SESSION['lang'] == 'en' ? 'selected' : '' ?>>English</option>
                    </select>
                </form>

                <?php if ($isConnected): ?>
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
        <h1><?= ($_SESSION['lang'] == 'fr') ? 'Mes recettes favorites' : 'My Favorite Recipes' ?></h1>
        <?php if (empty($likedRecipes)): ?>
            <p><?= ($_SESSION['lang'] == 'fr') ? "Aucune recette n'est ajoutée à vos favoris." : "No recipes added to your favorites." ?></p>
        <?php else: ?>
            <div class="recipes">
                <?php foreach ($likedRecipes as $recipe): ?>
                    <div class="recipe-card">
                        <?php if (!empty($recipe['imageURL'])): ?>
                            <img src="<?= htmlspecialchars($recipe['imageURL']) ?>" alt="<?= htmlspecialchars($recipe['nameFR']) ?>">
                        <?php else: ?>
                            <div class="no-image"><?= ($_SESSION['lang'] == 'fr') ? 'Image non disponible' : 'Image not available' ?></div>
                        <?php endif; ?>
                        <h3><?= htmlspecialchars($recipe['nameFR']) ?></h3>
                        <p><strong><?= ($_SESSION['lang'] == 'fr') ? 'Auteur :' : 'Author :' ?></strong> <?= htmlspecialchars($recipe['Author'] ?? 'Auteur inconnu') ?></p>
                        <button class="like-btn" 
                                data-recipe="<?= htmlspecialchars($recipe['nameFR']) ?>"
                                data-liked="true" 
                                data-count="<?= isset($recipe['likers']) ? count($recipe['likers']) : 0 ?>">
                            ❤ <?= isset($recipe['likers']) ? count($recipe['likers']) : 0 ?>
                        </button>
                        <a href="details.php?id=<?= urlencode($recipe['name']) ?>" class="more-btn"><?= ($_SESSION['lang'] == 'fr') ? '+ Plus' : '+ More' ?></a>
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
