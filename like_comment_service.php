<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['username'])) {
    echo json_encode(['success' => false, 'error' => 'Utilisateur non connecté']);
    exit;
}

$username = $_SESSION['username'];
$commentId = $_GET['commentId'] ?? null;

if (!$commentId) {
    echo json_encode(['success' => false, 'error' => 'ID du commentaire manquant']);
    exit;
}

// Chemins vers les fichiers
$commentsPath = 'json/comments.json';
$usersPath = 'json/users.json';

$comments = json_decode(file_get_contents($commentsPath), true);
$users = json_decode(file_get_contents($usersPath), true);

// Trouver l'utilisateur
$userIndex = null;
foreach ($users as $i => $user) {
    if ($user['username'] === $username) {
        $userIndex = $i;
        break;
    }
}

if ($userIndex === null) {
    echo json_encode(['success' => false, 'error' => 'Utilisateur non trouvé']);
    exit;
}

// Initialiser le champ comments_likes s’il n’existe pas
if (!isset($users[$userIndex]['comments_likes']) || !is_array($users[$userIndex]['comments_likes'])) {
    $users[$userIndex]['comments_likes'] = [];
}

$commentIndex = null;
foreach ($comments as $i => $comment) {
    if ($comment['id'] === $commentId) {
        $commentIndex = $i;
        break;
    }
}

if ($commentIndex === null) {
    echo json_encode(['success' => false, 'error' => 'Commentaire introuvable']);
    exit;
}

// Initialiser le champ likes s’il n’existe pas
if (!isset($comments[$commentIndex]['likes']) || !is_array($comments[$commentIndex]['likes'])) {
    $comments[$commentIndex]['likes'] = [];
}

$userLikes = &$users[$userIndex]['comments_likes'];
$commentLikes = &$comments[$commentIndex]['likes'];

if (in_array($username, $commentLikes)) {
    // Retirer le like
    $commentLikes = array_values(array_diff($commentLikes, [$username]));
    $userLikes = array_values(array_diff($userLikes, [$commentId]));
    $action = 'unlike';
} else {
    // Ajouter le like
    $commentLikes[] = $username;
    $userLikes[] = $commentId;
    $commentLikes = array_values(array_unique($commentLikes));
    $userLikes = array_values(array_unique($userLikes));
    $action = 'like';
}

// Sauvegarde
file_put_contents($commentsPath, json_encode($comments, JSON_PRETTY_PRINT));
file_put_contents($usersPath, json_encode($users, JSON_PRETTY_PRINT));

echo json_encode([
    'success' => true,
    'action' => $action,
    'likes' => count($commentLikes)
]);
