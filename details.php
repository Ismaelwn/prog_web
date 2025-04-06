<?php
session_start();
$isConnected = isset($_SESSION["username"]);
$currentUser = $isConnected ? $_SESSION["username"] : '';

$users = [];
if ($isConnected) {
    $usersJson = file_get_contents('json/users.json');
    $users = json_decode($usersJson, true);
    $userPosts = [];

    foreach ($users as $user) {
        if ($user['username'] === $currentUser) {
            $userPosts = $user['posts'] ?? [];
            break;
        }
    }
}

$recipes = json_decode(file_get_contents('json/recipes.json'), true);
$recipeId = $_GET['id'] ?? '';
$recipe = null;

if ($recipeId) {
    foreach ($recipes as $r) {
        if (htmlspecialchars($r['nameFR']) === $recipeId) {
            $recipe = $r;
            break;
        }
    }
}

if (!$recipe) {
    die("Recette non trouvée.");
}

$lang = $_GET['lang'] ?? 'fr';

$commentsFile = 'json/posts.json'; 
$comments = json_decode(file_get_contents($commentsFile), true) ?? [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'])) {
    $newComment = [
        'id' => uniqid('comment_'),
        'recipeId' => $recipe['nameFR'],
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

    $comments = array_filter($comments, fn($c) => $c['id'] !== $commentIdToDelete);
    $comments = array_values($comments);
    file_put_contents($commentsFile, json_encode($comments, JSON_PRETTY_PRINT));

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
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Détails de la recette</title>
    <script src="js/like_com.js"></script>
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
                        <a href="logout.php">Se déconnecter</a>
                    </div>
                </li>
            <?php else: ?>
                <li><a href="create_login.php">Se connecter</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>

<h1><?= htmlspecialchars($recipe['nameFR']) ?></h1>
<p><strong>Auteur :</strong> <?= htmlspecialchars($recipe['Author'] ?? 'Auteur inconnu') ?></p>

<?php if (!empty($recipe['imageURL'])): ?>
    <img src="<?= htmlspecialchars($recipe['imageURL']) ?>" alt="<?= htmlspecialchars($recipe['nameFR']) ?>" style="max-width: 100%; height: auto;">
<?php else: ?>
    <p>Image non disponible</p>
<?php endif; ?>

<h2>Détails de la recette</h2>

<?php if (!empty($recipe['description'])): ?>
    <p><strong>Description :</strong><br><?= nl2br(htmlspecialchars($recipe['description'])) ?></p>
<?php endif; ?>

<?php if (!empty($recipe['ingredients'])): ?>
    <p><strong>Ingrédients :</strong></p>
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
    <p><strong>Instructions :</strong><br><?= nl2br(htmlspecialchars($recipe['instructions'])) ?></p>
<?php endif; ?>

<?php if (!empty($recipe['allergenes'])): ?>
    <p><strong>Allergènes :</strong><br><?= nl2br(htmlspecialchars(is_array($recipe['allergenes']) ? implode(", ", $recipe['allergenes']) : $recipe['allergenes'])) ?></p>
<?php endif; ?>

<?php if (!empty($recipe['nameEN'])): ?>
    <p><strong>Nom en anglais :</strong> <?= htmlspecialchars($recipe['nameEN']) ?></p>
<?php endif; ?>

<?php if ($isConnected): ?>
    <h2>Ajouter un commentaire</h2>
    <form method="POST" enctype="multipart/form-data">
        <textarea name="comment" placeholder="Écrivez votre commentaire..." required></textarea><br>
        <input type="file" name="commentImage"><br>
        <button type="submit">Ajouter</button>
    </form>
<?php else: ?>
    <p>Vous devez être connecté pour commenter.</p>
<?php endif; ?>

<h2>Commentaires</h2>
<?php foreach ($comments as $comment): ?>
    <?php if ($comment['recipeId'] === $recipe['nameFR']): ?>
        <div class="comment">
            <p><strong>@<?= htmlspecialchars($comment['user']) ?></strong></p>
            <p><?= date('d/m/Y à H:i', $comment['timestamp']) ?></p>
            <p><?= htmlspecialchars($comment['text']) ?></p>
            <?php if ($comment['image']): ?>
                <img src="img/uploads/<?= htmlspecialchars($comment['image']) ?>" alt="Image du commentaire" style="max-width: 200px;">
            <?php endif; ?>
            <button class="like-btn" data-comment="<?= htmlspecialchars($comment['id']) ?>">❤ <?= $comment['likes'] ?></button>

            <?php if ($comment['user'] === $currentUser): ?>
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="delete_comment" value="<?= htmlspecialchars($comment['id']) ?>">
                    <button type="submit" onclick="return confirm('Supprimer ce commentaire ?')" style="color:red;">Supprimer</button>
                </form>
            <?php endif; ?>
        </div>
    <?php endif; ?>
<?php endforeach; ?>

<a href="javascript:history.back()" style="display:inline-block;margin-top:20px;padding:10px 20px;background:#555;color:white;text-decoration:none;border-radius:5px;">Retour</a>

</body>
</html>
