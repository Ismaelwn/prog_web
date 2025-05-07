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

if (!$isConnected) {
    header("Location: create_login.php");
    exit;
}

$users = json_decode(file_get_contents('json/users.json'), true);
$recipes = json_decode(file_get_contents('json/recipes.json'), true);
$comments = json_decode(file_get_contents('json/posts.json'), true);

$currentUserData = null;
foreach ($users as $user) {
    if ($user['username'] === $currentUser) {
        $currentUserData = $user;
        break;
    }
}

$currentRoles = $currentUserData['role'] ?? [];
$userLikes = $currentUserData['likes'] ?? [];
$userPosts = $currentUserData['posts'] ?? [];

function renderRecipeCard($recipe) {
    $name = $_SESSION['lang'] == 'fr' ? htmlspecialchars($recipe['nameFR'] ?? 'Nom inconnu') : htmlspecialchars($recipe['name'] ?? 'Unknown name');
    $image = !empty($recipe['imageURL']) ? '<img src="' . htmlspecialchars($recipe['imageURL']) . '" alt="' . $name . '">' : '<div class="no-image">Image non disponible</div>';
    echo "<div class='recipe-card'>
            $image
            <h3>$name</h3>
            <p><strong>" . ($_SESSION['lang'] == 'fr' ? 'Auteur' : 'Author') . " :</strong> " . htmlspecialchars($recipe['Author'] ?? ($_SESSION['lang'] == 'fr' ? 'Auteur inconnu' : 'Unknown author')) . "</p>
            <a href='details.php?id=" . urlencode($name) . "' class='more-btn'>+ " . ($_SESSION['lang'] == 'fr' ? 'Plus' : 'More') . "</a>
          </div>";
}
?>

<!DOCTYPE html>
<html lang="<?= $_SESSION['lang'] == 'fr' ? 'fr' : 'en' ?>">
<head>
    <meta charset="UTF-8">
    <title><?= ($_SESSION['lang'] == 'fr' ? 'Mon profil' : 'My Profile') ?> - Re7</title>
    <link rel="stylesheet" href="css/style6.css">
    
</head>
<body>
<header>
    <nav>
        <ul>
            <li><a href="main.php"><?= ($_SESSION['lang'] == 'fr' ? 'Accueil' : 'Home') ?></a></li>
            <li><a href="account.php"><?= ($_SESSION['lang'] == 'fr' ? 'Mon compte' : 'My Account') ?></a></li>
            <form method="POST" >
                <select name="lang" onchange="this.form.submit()">
                    <option value="fr" <?= $_SESSION['lang'] == 'fr' ? 'selected' : '' ?>>Français</option>
                    <option value="en" <?= $_SESSION['lang'] == 'en' ? 'selected' : '' ?>>English</option>
                </select>
            </form>
            <li><a href="logout.php"><?= ($_SESSION['lang'] == 'fr' ? 'Se déconnecter' : 'Log out') ?></a></li>
        </ul>
    </nav>
</header>

<main>
    <h1><?= ($_SESSION['lang'] == 'fr' ? 'Mon profil' : 'My Profile') ?></h1>

    <?php if (in_array('cuisinier', $currentRoles)): ?>
        <h2><?= ($_SESSION['lang'] == 'fr' ? 'Recettes likées' : 'Liked Recipes') ?></h2>
        <div class="recipes">
            <?php foreach ($recipes as $recipe): ?>
                <?php if (in_array($recipe['nameFR'], $userLikes)) renderRecipeCard($recipe); ?>
            <?php endforeach; ?>
        </div>

        <h2><?= ($_SESSION['lang'] == 'fr' ? 'Recettes commentées' : 'Commented Recipes') ?></h2>
        <div class="recipes">
            <?php 
            $commentedRecipeNames = [];
            $userCommentsByRecipe = [];

            foreach ($comments as $comment) {
                if (in_array($comment['id'], $userPosts)) {
                    $commentedRecipeNames[] = $comment['recipeId'];
                    $userCommentsByRecipe[$comment['recipeId']][] = $comment;
                }
            }

            foreach ($recipes as $recipe) {
                if (in_array($recipe['nameFR'], $commentedRecipeNames)) {
                    echo "<div class='recipe-card'>";
                    echo "<h3>" . htmlspecialchars($recipe['nameFR']) . "</h3>";
                    echo "<a href='details.php?id=" . urlencode($recipe['nameFR']) . "' class='more-btn'>+ " . ($_SESSION['lang'] == 'fr' ? 'Plus' : 'More') . "</a>";
                    echo "<p><strong>" . ($_SESSION['lang'] == 'fr' ? 'Vos commentaires' : 'Your comments') . " :</strong></p>";
                    foreach ($userCommentsByRecipe[$recipe['nameFR']] as $com) {
                        $date = date('d/m/Y à H:i', $com['timestamp']);
                        $text = htmlspecialchars($com['text']);
                        echo "<p><em>$date</em> — $text</p>";
                    }
                    echo "</div>";
                }
            }
            ?>
        </div>

    <?php elseif (in_array('chef', $currentRoles)): ?>
        <h2><?= ($_SESSION['lang'] == 'fr' ? 'Recettes créées' : 'Created Recipes') ?></h2>
        <div class="recipes">
            <?php foreach ($recipes as $recipe): ?>
                <?php if ($recipe['Author'] === $currentUser) renderRecipeCard($recipe); ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>

<footer>
    <p>&copy; 2025 Re7. Tous droits réservés.</p>
</footer>
</body>
</html>
