<?php
// Chemin vers le fichier JSON contenant les recettes
$jsonFile = 'json/recipes.json';

// Vérifier si le fichier existe
if (!file_exists($jsonFile)) {
    echo json_encode(["error" => "Fichier JSON introuvable."]);
    exit;
}

// Lire le contenu du fichier JSON
$jsonData = file_get_contents($jsonFile);

// Convertir en tableau associatif
$recettes = json_decode($jsonData, true);

// Vérifier si le JSON est valide
if ($recettes === null) {
    echo json_encode(["error" => "Erreur de décodage JSON."]);
    exit;
}

// Envoyer les données en JSON
header('Content-Type: application/json');
echo json_encode($recettes);
