<?php
header('Content-Type: application/json');

if (!isset($_POST['username'], $_POST['role'], $_POST['action'])) {
    echo json_encode(['success' => false, 'message' => 'Paramètres manquants.']);
    exit;
}

$username = $_POST['username'];
$role = $_POST['role'];
$action = $_POST['action'];

$requestFile = 'json/request.json';
$userFile = 'json/users.json';

// Charger les JSON
$requests = json_decode(file_get_contents($requestFile), true);
$users = json_decode(file_get_contents($userFile), true);

// Supprimer la requête
$requests = array_filter($requests, function($req) use ($username, $role) {
    return !($req['username'] === $username && $req['role'] === $role);
});
$requests = array_values($requests); // Réindexer

// Enregistrer les requêtes mises à jour
file_put_contents($requestFile, json_encode($requests, JSON_PRETTY_PRINT));

if ($action === 'accept') {
    // Ajouter le rôle à l'utilisateur
    foreach ($users as &$user) {
        if ($user['username'] === $username) {
            if (!in_array($role, $user['role'])) {
                $user['role'][] = $role;
            }
            break;
        }
    }
    file_put_contents($userFile, json_encode($users, JSON_PRETTY_PRINT));
}

echo json_encode(['success' => true]);
exit;
?>
