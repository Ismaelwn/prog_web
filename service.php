<?php
header('Content-Type: application/json; charset=utf-8');

$file = "json/users.json";

// Vérifier si les données sont envoyées via GET
if (isset($_GET["nom"]) && isset($_GET["prenom"]) && isset($_GET["username"]) && isset($_GET["mail"]) && isset($_GET["password"]) && isset($_GET["role"])) {
    
    $username = $_GET["username"];
    
    // Charger les utilisateurs existants
    if (file_exists($file)) {
        $jsonData = json_decode(file_get_contents($file), true);
        if (!is_array($jsonData)) {
            $jsonData = [];  // Si le fichier est corrompu ou incorrect, on initialise un tableau vide
        }
    } else {
        $jsonData = [];
    }

    // Vérifier si le pseudo est déjà pris
    foreach ($jsonData as $user) {
        if ($user["username"] === $username) {
            echo json_encode(["error" => "Le nom d'utilisateur est déjà pris."]);
            die();
        }
    }

    // Ajouter "cuisinier" au rôle, même si un autre rôle est sélectionné
    $roleSelectionne = $_GET["role"];
    $roleList = array_unique(["cuisinier", $roleSelectionne]); // Assure que "cuisinier" est toujours présent

    // Stockage sous forme de dictionnaire (tableau associatif en PHP)
    $data = [
        "nom" => $_GET["nom"],
        "prenom" => $_GET["prenom"],
        "username" => $username,
        "password" => $_GET["password"],
        "mail" => $_GET["mail"],
        "role" => $roleList  // Stocké sous forme de liste avec "cuisinier" toujours inclus
    ];

    // Ajouter le nouvel utilisateur à la liste existante
    $jsonData[] = $data;

    // Sauvegarde des nouvelles données dans le fichier JSON
    file_put_contents($file, json_encode($jsonData, JSON_PRETTY_PRINT));

    echo json_encode(["message" => "Utilisateur créé avec succès"]);
    die();
} else {
    echo json_encode(["error" => "Données incomplètes"]);
    die();
}
?>
