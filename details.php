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

// Charger les recettes depuis le fichier JSON
$recipes = json_decode(file_get_contents('json/recipes.json'), true);
$recipeId = $_GET['id'] ?? '';
$recipe = null;

if ($recipeId) {
    foreach ($recipes as $r) {
        if ($_SESSION['lang'] == 'fr' && htmlspecialchars($r['nameFR']) === $recipeId) {
            $recipe = $r;
            break;
        }
        if ($_SESSION['lang'] == 'en' && htmlspecialchars($r['name']) === $recipeId) {
            $recipe = $r;
            break;
        }
    }
}

if (!$recipe) {
    die($_SESSION['lang'] == 'fr' ? "Recette non trouvée." : "Recipe not found.");
}

// Charger les commentaires
$commentsFile = 'json/posts.json';
$comments = json_decode(file_get_contents($commentsFile), true) ?? [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'])) {
    $newComment = [
        'id' => uniqid('comment_'),
        'recipeId' => $recipe[$_SESSION['lang'] == 'fr' ? 'nameFR' : 'name'],
        'user' => $currentUser,
        'text' => htmlspecialchars($_POST['comment']),
        'image' => isset($_FILES['commentImage']) ? $_FILES['commentImage']['name'] : null,
        'likers' => [],
        'likes' => 0,
        'timestamp' => time()
    ];
    
    if ($newComment['image']) {
        $imagePath = 'img/uploads/' . basename($newComment['image']);
        move_uploaded_file($_FILES['commentImage']['tmp_name'], $imagePath);
    }

    $comments[] = $newComment;
    file_put_contents($commentsFile, json_encode($comments, JSON_PRETTY_PRINT));

    // Mettre à jour les posts de l'utilisateur
    $users = json_decode(file_get_contents('json/users.json'), true);
    foreach ($users as &$user) {
        if ($user['username'] === $currentUser) {
            $user['posts'][] = $newComment['id'];
            break;
        }
    }
    file_put_contents('json/users.json', json_encode($users, JSON_PRETTY_PRINT));
}

if (isset($_POST['delete_comment'])) {
    $commentIdToDelete = $_POST['delete_comment'];

    // Supprimer le commentaire
    $comments = array_filter($comments, fn($c) => $c['id'] !== $commentIdToDelete);
    $comments = array_values($comments);
    file_put_contents($commentsFile, json_encode($comments, JSON_PRETTY_PRINT));

    // Supprimer le commentaire des posts de l'utilisateur
    $users = json_decode(file_get_contents('json/users.json'), true);
    foreach ($users as &$user) {
        if ($user['username'] === $currentUser) {
            $user['posts'] = array_filter($user['posts'], fn($id) => $id !== $commentIdToDelete);
            break;
        }
    }
    file_put_contents('json/users.json', json_encode($users, JSON_PRETTY_PRINT));
    header('Location: ' . $_SERVER['REQUEST_URI']);
    exit;
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
                <li>
                    <!-- Formulaire de sélection de langue -->
                    <form method="POST" action="details.php?id=<?= urlencode($recipe[$_SESSION['lang'] == 'fr' ? 'nameFR' : 'name']) ?>">
                        <select name="lang" onchange="this.form.submit()">
                            <option value="fr" <?= $_SESSION['lang'] == 'fr' ? 'selected' : '' ?>>Français</option>
                            <option value="en" <?= $_SESSION['lang'] == 'en' ? 'selected' : '' ?>>English</option>
                        </select>
                    </form>
                </li>
            </ul>
        </nav>
    </header>

    <h1><?= htmlspecialchars($recipe[$_SESSION['lang'] == 'fr' ? 'nameFR' : 'name']) ?></h1>
    <p><strong><?= ($_SESSION['lang'] == 'fr') ? 'Auteur :' : 'Author :' ?></strong> <?= htmlspecialchars($recipe['Author'] ?? ($_SESSION['lang'] == 'fr' ? 'Auteur inconnu' : 'Unknown author')) ?></p>

    <?php if (!empty($recipe['imageURL'])): ?>
        <img src="<?= htmlspecialchars($recipe['imageURL']) ?>" alt="<?= htmlspecialchars($recipe[$_SESSION['lang'] == 'fr' ? 'nameFR' : 'name']) ?>" style="max-width: 100%; height: auto;">
    <?php else: ?>
        <p><?= ($_SESSION['lang'] == 'fr') ? 'Image non disponible' : 'Image not available' ?></p>
    <?php endif; ?>

    <h2><?= ($_SESSION['lang'] == 'fr') ? 'Détails de la recette' : 'Recipe Details' ?></h2>

    <?php if (!empty($recipe['description'])): ?>
        <p><strong><?= ($_SESSION['lang'] == 'fr') ? 'Description :' : 'Description :' ?></strong><br><?= nl2br(htmlspecialchars($recipe['description'])) ?></p>
    <?php endif; ?>

    <?php if (!empty($recipe['ingredients'])): ?>
        <p><strong><?= ($_SESSION['lang'] == 'fr') ? 'Ingrédients :' : 'Ingredients :' ?></strong></p>
        <ul>
            <?php
            $ingredients = is_array($recipe['ingredients']) ? $recipe['ingredients'] : explode("\n", $recipe['ingredients']);
            foreach ($ingredients as $ingredient):
                if (is_string($ingredient)):
            ?>
            <li><?= htmlspecialchars($ingredient) ?></li>
            <?php endif; endforeach; ?>
        </ul>
    <?php endif; ?>

    <?php if (!empty($recipe['instructions'])): ?>
        <p><strong><?= ($_SESSION['lang'] == 'fr') ? 'Instructions :' : 'Instructions :' ?></strong><br><?= nl2br(htmlspecialchars($recipe['instructions'])) ?></p>
    <?php endif; ?>

    <?php if (!empty($recipe['allergenes'])): ?>
        <p><strong><?= ($_SESSION['lang'] == 'fr') ? 'Allergènes :' : 'Allergens :' ?></strong><br><?= nl2br(htmlspecialchars(is_array($recipe['allergenes']) ? implode(", ", $recipe['allergenes']) : $recipe['allergenes'])) ?></p>
    <?php endif; ?>

    <?php if ($isConnected): ?>
        <h2><?= ($_SESSION['lang'] == 'fr') ? 'Ajouter un commentaire' : 'Add a Comment' ?></h2>
        <form method="POST" enctype="multipart/form-data">
            <textarea name="comment" placeholder="<?= ($_SESSION['lang'] == 'fr') ? 'Écrivez votre commentaire...' : 'Write your comment...'; ?>" required></textarea><br>
            <input type="file" name="commentImage"><br>
            <button type="submit"><?= ($_SESSION['lang'] == 'fr') ? 'Ajouter' : 'Add' ?></button>
        </form>
    <?php else: ?>
        <p><?= ($_SESSION['lang'] == 'fr') ? 'Vous devez être connecté pour commenter.' : 'You must be logged in to comment.' ?></p>
    <?php endif; ?>

    <h2><?= ($_SESSION['lang'] == 'fr') ? 'Commentaires' : 'Comments' ?></h2>
    <?php foreach ($comments as $comment): ?>
        <?php if ($comment['recipeId'] === $recipe[$_SESSION['lang'] == 'fr' ? 'nameFR' : 'name']): ?>
            <div class="comment">
                <p><strong>@<?= htmlspecialchars($comment['user']) ?></strong></p>
                <p><?= date('d/m/Y à H:i', $comment['timestamp']) ?></p>
                <p><?= htmlspecialchars($comment['text']) ?></p>
                <?php if ($comment['image']): ?>
                    <img src="img/uploads/<?= htmlspecialchars($comment['image']) ?>" alt="Image du commentaire" style="max-width: 200px;">
                <?php endif; ?>
                <?php if ($comment['user'] === $currentUser): ?>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="delete_comment" value="<?= htmlspecialchars($comment['id']) ?>">
                        <button type="submit" onclick="return confirm('Supprimer ce commentaire ?')" style="color:red;"><?= ($_SESSION['lang'] == 'fr') ? 'Supprimer' : 'Delete' ?></button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>

    <a href="main.php" style="display:inline-block;margin-top:20px;padding:10px 20px;background:#555;color:white;text-decoration:none;border-radius:5px;"><?= ($_SESSION['lang'] == 'fr') ? 'Retour' : 'Back' ?></a>
</body>
</html>
