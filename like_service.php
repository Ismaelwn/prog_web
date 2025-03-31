<?php
session_start();  // Démarrer la session pour accéder à $_SESSION['username']
header('Content-Type: application/json; charset=utf-8');

// Vérification si l'utilisateur est connecté
if (!isset($_SESSION['username'])) {
    echo json_encode(['error' => 'Vous devez être connecté pour aimer une recette.']);
    exit;
}

// Récupération des paramètres
if (!isset($_GET['recipe']) || empty($_GET['recipe'])) {
    echo json_encode(['error' => 'Nom de recette manquant.']);
    exit;
}

$username = $_SESSION['username'];
$recipeName = $_GET['recipe'];

// Chargement des fichiers JSON
$usersFile = 'json/users.json';
$recipesFile = 'json/recipes.json';

if (!file_exists($usersFile) || !file_exists($recipesFile)) {
    echo json_encode(['error' => 'Fichiers de données introuvables.']);
    exit;
}

$users = json_decode(file_get_contents($usersFile), true);
$recipes = json_decode(file_get_contents($recipesFile), true);

if (!is_array($users) || !is_array($recipes)) {
    echo json_encode(['error' => 'Erreur de lecture des données.']);
    exit;
}

// Recherche de l'utilisateur
$userIndex = -1;
foreach ($users as $index => $user) {
    if ($user['username'] === $username) {
        $userIndex = $index;
        break;
    }
}

if ($userIndex === -1) {
    echo json_encode(['error' => 'Utilisateur non trouvé.']);
    exit;
}

// Recherche de la recette
$recipeIndex = -1;
foreach ($recipes as $index => $recipe) {
    if ($recipe['nameFR'] === $recipeName || $recipe['name'] === $recipeName) {
        $recipeIndex = $index;
        break;
    }
}

if ($recipeIndex === -1) {
    echo json_encode(['error' => 'Recette non trouvée.']);
    exit;
}

// Vérification si l'utilisateur a déjà aimé cette recette
$alreadyLiked = false;
if (isset($users[$userIndex]['likes']) && is_array($users[$userIndex]['likes'])) {
    $alreadyLiked = in_array($recipeName, $users[$userIndex]['likes']);
}

// Si l'utilisateur a déjà aimé, on supprime le like, sinon on l'ajoute
if ($alreadyLiked) {
    // Retirer le like de l'utilisateur
    $users[$userIndex]['likes'] = array_values(array_filter($users[$userIndex]['likes'], function($item) use ($recipeName) {
        return $item !== $recipeName;
    }));
    
    // Retirer l'utilisateur des likers de la recette
    if (isset($recipes[$recipeIndex]['likers']) && is_array($recipes[$recipeIndex]['likers'])) {
        $recipes[$recipeIndex]['likers'] = array_values(array_filter($recipes[$recipeIndex]['likers'], function($item) use ($username) {
            return $item !== $username;
        }));
    }
    
    $action = 'unlike';
} else {
    // Ajouter le like pour l'utilisateur
    if (!isset($users[$userIndex]['likes']) || !is_array($users[$userIndex]['likes'])) {
        $users[$userIndex]['likes'] = [];
    }
    $users[$userIndex]['likes'][] = $recipeName;
    
    // Ajouter l'utilisateur aux likers de la recette
    if (!isset($recipes[$recipeIndex]['likers']) || !is_array($recipes[$recipeIndex]['likers'])) {
        $recipes[$recipeIndex]['likers'] = [];
    }
    $recipes[$recipeIndex]['likers'][] = $username;
    
    $action = 'like';
}

// Sauvegarde des modifications
file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT));
file_put_contents($recipesFile, json_encode($recipes, JSON_PRETTY_PRINT));

// Renvoyer le nombre total de likes pour cette recette
$likeCount = isset($recipes[$recipeIndex]['likers']) ? count($recipes[$recipeIndex]['likers']) : 0;

echo json_encode([
    'success' => true, 
    'action' => $action, 
    'likes' => $likeCount,
    'recipe' => $recipeName
]);
?>