<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

// Vérifier si l'utilisateur est connecté
if (isset($_SESSION['username']) && !empty($_SESSION['username'])) {
    echo json_encode(['logged_in' => true, 'username' => $_SESSION['username']]);
} else {
    echo json_encode(['logged_in' => false]);
}
?>