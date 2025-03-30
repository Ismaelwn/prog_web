<?php
header('Content-Type: application/json; charset=utf-8');

$file = "json/users.json";


// Vérifier si les données de connexion sont envoyées via GET
if (isset($_GET["username"]) && isset($_GET["password"])) {
    
    $username = $_GET["username"];
    $password = $_GET["password"];

    // Charger les utilisateurs existants
    if (file_exists($file)) {
        $jsonData = json_decode(file_get_contents($file), true);
        if (!is_array($jsonData)) {
            $jsonData = [];  // Si le fichier est corrompu ou incorrect, on initialise un tableau vide
        }
    } else {
        echo json_encode(["error" => "Aucun utilisateur trouvé."]);
        die();
    }

    // Vérifier si le nom d'utilisateur existe et si le mot de passe est correct
    foreach ($jsonData as $user) {
        if ($user["username"] === $username) {
            if ($user["password"] === $password) {
                // Connexion réussie
                echo json_encode(["message" => "Connexion réussie !"]);
                die();
            } else {
                echo json_encode(["error" => "Mot de passe incorrect."]);
                die();
            }
        }
    }

    // Si aucun utilisateur avec ce nom n'est trouvé
    echo json_encode(["error" => "Nom d'utilisateur inconnu."]);
} else {
    echo json_encode(["error" => "Données incomplètes."]);
}
?>
