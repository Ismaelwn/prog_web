<?php
session_start();
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
    $nameFR = htmlspecialchars($recipe['nameFR'] ?? 'Nom inconnu');
    $image = !empty($recipe['imageURL']) ? '<img src="' . htmlspecialchars($recipe['imageURL']) . '" alt="' . $nameFR . '">' : '<div class="no-image">Image non disponible</div>';
    echo "<div class='recipe-card'>
            $image
            <h3>$nameFR</h3>
            <p><strong>Auteur :</strong> " . htmlspecialchars($recipe['Author'] ?? 'Auteur inconnu') . "</p>
            <a href='details.php?id=" . urlencode($nameFR) . "' class='more-btn'>+ Plus</a>
          </div>";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon profil - Re7</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header>
    <nav>
        <ul>
            <li><a href="main.php">Accueil</a></li>
            <li><a href="account.php">Mon compte</a></li>
            <li><a href="logout.php">Se déconnecter</a></li>
        </ul>
    </nav>
</header>

<main>
    <h1>Mon profil</h1>

    <?php if (in_array('cuisinier', $currentRoles)): ?>
        <h2>Recettes likées</h2>
        <div class="recipes">
            <?php foreach ($recipes as $recipe): ?>
                <?php if (in_array($recipe['nameFR'], $userLikes)) renderRecipeCard($recipe); ?>
            <?php endforeach; ?>
        </div>

        <h2>Recettes commentées</h2>
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
                    echo "<a href='details.php?id=" . urlencode($recipe['nameFR']) . "' class='more-btn'>+ Plus</a>";
                    echo "<p><strong>Vos commentaires :</strong></p>";
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
        <h2>Recettes créées</h2>
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
