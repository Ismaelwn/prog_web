<?php
/*
// Vérifier si les clés existent dans $_GET avant de les utiliser
$prenom = isset($_GET["prenomc"]) ? $_GET["prenomc"] : null;
$nom = isset($_GET["nomc"]) ? $_GET["nomc"] : null;
//$password = isset($_GET["mdp"]) ? $_GET["mdp"] : null; 
//$mail = isset($_GET["mail"]) ? $_GET["mail"] : null;
$username = isset($_GET["usernamec"]) ? $_GET["usernamec"] : null; //ok*/

$prenom = $_GET["prenomc"];
$nom = $_GET["nomc"];
$username = $_GET["usernamec"];
print($prenom);
print($nom)
/*
// Créer un tableau avec les données de l'utilisateur
$data = array(
    "nom" => $nom,
    "prenom" => $prenom,
    //"email" => $mail,
    "username" => $username
    //"mdp" => $password
);

// Convertir le tableau en JSON
$json_data = json_encode($data, JSON_PRETTY_PRINT);

// Écrire dans le fichier JSON
file_put_contents("json/users.json", $json_data);
*/
// Message de confirmation
//echo "Fichier JSON créé avec succès !";



?>
