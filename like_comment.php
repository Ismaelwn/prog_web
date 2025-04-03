<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION["username"])) {
    echo json_encode(['success' => false, 'message' => 'Vous devez être connecté pour aimer un commentaire.']);
    exit;
}

$currentUser = $_SESSION["username"];
$commentsFile = 'json/posts.json';
$usersFile = 'json/users.json';

// Récupérer les commentaires et utilisateurs
$comments = json_decode(file_get_contents($commentsFile), true) ?? [];
$users = json_decode(file_get_contents($usersFile), true) ?? [];

// Récupérer les données envoyées en POST
$data = json_decode(file_get_contents('php://input'), true);
$commentId = $data['commentId'] ?? '';

if (!$commentId) {
    echo json_encode(['success' => false, 'message' => 'ID de commentaire manquant.']);
    exit;
}

// Trouver le commentaire dans posts.json
$commentToLike = null;
foreach ($comments as &$comment) {
    if ($comment['id'] === $commentId) {
        $commentToLike = &$comment;
        break;
    }
}

if (!$commentToLike) {
    echo json_encode(['success' => false, 'message' => 'Commentaire non trouvé.']);
    exit;
}

// Vérifier si l'utilisateur a déjà liké ce commentaire
if (in_array($currentUser, $commentToLike['likers'])) {
    echo json_encode(['success' => false, 'message' => 'Vous avez déjà liké ce commentaire.']);
    exit;
}

// Ajouter le like
$commentToLike['likers'][] = $currentUser;
$commentToLike['likes']++;

// Sauvegarder les commentaires mis à jour dans posts.json
file_put_contents($commentsFile, json_encode($comments, JSON_PRETTY_PRINT));

// Mettre à jour les utilisateurs (ajouter l'ID du commentaire dans les posts de l'utilisateur)
foreach ($users as &$user) {
    if ($user['username'] === $currentUser) {
        if (!in_array($commentId, $user['posts'])) {
            $user['posts'][] = $commentId;  // Ajouter l'ID du commentaire aimé dans la liste des posts
        }
        break;
    }
}

// Sauvegarder les utilisateurs mis à jour dans users.json
file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT));

echo json_encode(['success' => true, 'likes' => $commentToLike['likes']]);
?>
