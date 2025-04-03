<?php
session_start();

// Charger les commentaires
$commentsFile = 'json/posts.json'; 
$comments = json_decode(file_get_contents($commentsFile), true) ?? [];

// Vérifier que la requête est une demande POST avec l'ID du commentaire
$data = json_decode(file_get_contents('php://input'), true);
if (isset($data['commentId'])) {
    $commentId = $data['commentId'];

    // Trouver et mettre à jour le commentaire avec l'ID correspondant
    foreach ($comments as &$comment) {
        if ($comment['id'] === $commentId) {
            $comment['likes']++;
            break;
        }
    }

    // Sauvegarder les commentaires mis à jour
    file_put_contents($commentsFile, json_encode($comments, JSON_PRETTY_PRINT));

    // Répondre avec succès
    echo json_encode(['success' => true]);
} else {
    // Si l'ID du commentaire n'est pas fourni, retourner une erreur
    echo json_encode(['success' => false, 'message' => 'Comment ID missing']);
}
