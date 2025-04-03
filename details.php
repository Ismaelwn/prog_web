<?php
session_start();
$isConnected = isset($_SESSION["username"]);
$currentUser = $isConnected ? $_SESSION["username"] : '';

// Charger les utilisateurs pour vérifier les likes actuels
$users = [];
if ($isConnected) {
    $usersJson = file_get_contents('json/users.json');
    $users = json_decode($usersJson, true);
    $userPosts = [];

    // Trouver les posts de l'utilisateur actuel
    foreach ($users as $user) {
        if ($user['username'] === $currentUser) {
            $userPosts = isset($user['posts']) ? $user['posts'] : [];
            break;
        }
    }
}

$recipes = json_decode(file_get_contents('json/recipes.json'), true);
$recipeId = isset($_GET['id']) ? $_GET['id'] : '';
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

// Vérification de la langue de l'utilisateur (par défaut en français)
$lang = isset($_GET['lang']) ? $_GET['lang'] : 'fr'; // Par défaut, on affiche la version française

// Gestion des commentaires
$commentsFile = 'json/posts.json'; 
$comments = json_decode(file_get_contents($commentsFile), true) ?? [];

// Ajouter un nouveau commentaire
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'])) {
    $newComment = [
        'id' => uniqid('comment_'),  // Génère un ID unique pour le commentaire
        'recipeId' => $recipe['nameFR'], // L'ID de la recette à laquelle appartient le commentaire
        'user' => $currentUser,
        'text' => htmlspecialchars($_POST['comment']),
        'image' => isset($_FILES['commentImage']) ? $_FILES['commentImage']['name'] : null,
        'likers' => [],
        'timestamp' => time() // Timestamp de la création du commentaire
    ];
    
    // Sauvegarder l'image si elle est téléchargée
    if ($newComment['image']) {
        $imagePath = 'img/uploads/' . basename($newComment['image']);
        move_uploaded_file($_FILES['commentImage']['tmp_name'], $imagePath);
    }

    $comments[] = $newComment;
    file_put_contents($commentsFile, json_encode($comments, JSON_PRETTY_PRINT));

    // Ajouter l'ID du commentaire dans les posts de l'utilisateur
    foreach ($users as &$user) {
        if ($user['username'] === $currentUser) {
            $user['posts'][] = $newComment['id'];
            break;
        }
    }
    file_put_contents('json/users.json', json_encode($users, JSON_PRETTY_PRINT));
}

// Suppression d'un commentaire
if (isset($_POST['delete_comment'])) {
    $commentIdToDelete = $_POST['delete_comment'];

    // Supprimer le commentaire correspondant à l'ID
    $comments = array_filter($comments, function ($comment) use ($commentIdToDelete) {
        return $comment['id'] !== $commentIdToDelete;
    });

    // Réindexer l'array
    $comments = array_values($comments);

    // Sauvegarder les commentaires mis à jour dans le fichier JSON
    file_put_contents($commentsFile, json_encode($comments, JSON_PRETTY_PRINT));

    // Supprimer l'ID du commentaire des posts de l'utilisateur
    foreach ($users as &$user) {
        if ($user['username'] === $currentUser) {
            $user['posts'] = array_filter($user['posts'], function ($postId) use ($commentIdToDelete) {
                return $postId !== $commentIdToDelete;
            });
            break;
        }
    }
    file_put_contents('json/users.json', json_encode($users, JSON_PRETTY_PRINT));

    // Rediriger pour éviter un double envoi de formulaire
    header('Location: ' . $_SERVER['REQUEST_URI']);
    exit;
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recette - Détails</title>
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
                        <a href="account.php">Mon profil</a>
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

<h1><?= htmlspecialchars($recipe['nameFR']) ?></h1>
<p>Auteur : <?= htmlspecialchars($recipe['Author'] ?? 'Auteur inconnu') ?></p>

<?php if (!empty($recipe['imageURL'])): ?>
    <img src="<?= htmlspecialchars($recipe['imageURL']) ?>" alt="<?= htmlspecialchars($recipe['nameFR']) ?>" style="max-width: 100%; height: auto;">
<?php else: ?>
    <p>Image non disponible</p>
<?php endif; ?>

<!-- Formulaire pour ajouter un commentaire -->
<?php if ($isConnected): ?>
    <h2>Ajouter un commentaire</h2>
    <form method="POST" enctype="multipart/form-data">
        <textarea name="comment" placeholder="Écrivez votre commentaire..." required></textarea><br>
        <input type="file" name="commentImage"><br>
        <button type="submit">Ajouter</button>
    </form>
<?php else: ?>
    <p>Vous devez être connecté pour ajouter un commentaire.</p>
<?php endif; ?>

<!-- Affichage des commentaires -->
<h2>Commentaires</h2>
<?php
foreach ($comments as $comment):
    if ($comment['recipeId'] === $recipe['nameFR']):
        // Formatage de la date et de l'heure
        $formattedDate = date('d/m/Y à H:i', $comment['timestamp']);
?>
    <div class="comment">
    <p><strong>@<?= htmlspecialchars($comment['user']) ?></strong></p>
    <p><?= $formattedDate ?> :</p>
    <p><?= htmlspecialchars($comment['text']) ?></p>
        <?php if ($comment['image']): ?>
            <img src="img/uploads/<?= htmlspecialchars($comment['image']) ?>" alt="Image du commentaire" style="max-width: 200px;">
        <?php endif; ?>
        <button class="like-btn" data-comment="<?= htmlspecialchars($comment['id']) ?>">❤ <?= $comment['likes'] ?></button>
        
        <!-- Bouton Supprimer, visible seulement pour l'utilisateur qui a écrit le commentaire -->
        <?php if ($comment['user'] === $currentUser): ?>
            <form method="POST" style="display: inline;">
                <input type="hidden" name="delete_comment" value="<?= htmlspecialchars($comment['id']) ?>">
                <button type="submit" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce commentaire ?')" style="color: red;">Supprimer</button>
            </form>
        <?php endif; ?>
    </div>
<?php 
    endif;
endforeach;
?>


<!-- Bouton retour -->
<a href="javascript:history.back()" style="display: inline-block; margin-top: 20px; padding: 10px 20px; background-color: #555; color: white; text-decoration: none; border-radius: 5px;">Retour</a>

</body>
</html>
