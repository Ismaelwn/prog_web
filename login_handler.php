<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

$usersFile = "json/users.json";

if (!isset($_GET["username"], $_GET["password"])) {
    echo json_encode(["error" => "Champs manquants."]);
    exit;
}

$username = $_GET["username"];
$password = $_GET["password"];

// Vérifie que le fichier existe
if (!file_exists($usersFile)) {
    echo json_encode(["error" => "Aucun utilisateur enregistré."]);
    exit;
}

$users = json_decode(file_get_contents($usersFile), true);

foreach ($users as $user) {
    if ($user["username"] === $username && $user["password"] === $password) {
        $_SESSION["username"] = $username;

        // Ici on stocke TOUS les rôles dans la session
        $roles = is_array($user["role"]) ? $user["role"] : [$user["role"]];
        $_SESSION["role"] = $roles;

        echo json_encode(["message" => "Connexion réussie !", "username" => $username, "roles" => $roles]);
        exit;
    }
}

echo json_encode(["error" => "Identifiants incorrects."]);
exit;
