<?php
session_start();

// Vérifier si une langue a été sélectionnée
if (isset($_POST['lang'])) {
    $_SESSION['lang'] = $_POST['lang'];  // Enregistrer la langue choisie dans la session
} else {
    // Si la langue n'est pas définie, utiliser la langue par défaut
    if (!isset($_SESSION['lang'])) {
        $_SESSION['lang'] = 'fr';  // Langue par défaut : français
    }
}

$isConnected = isset($_SESSION["username"]);
$currentUser = $isConnected ? $_SESSION["username"] : '';
$currentRoles = [];

if ($isConnected) {
    $users = json_decode(file_get_contents('json/users.json'), true);
    foreach ($users as $user) {
        if ($user['username'] === $currentUser) {
            $currentRoles = $user['role'] ?? [];
            $userLikes = $user['likes'] ?? [];
            break;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= ($_SESSION['lang'] == 'fr') ? 'À propos - Projet Gestion de Recettes' : 'About - Recipe Management Project' ?></title>
</head>
<body>
<header>
    <nav>
        <ul>
            <li><a href="main.php">Re7</a></li>
            <li><a href="apropos.php"><?= ($_SESSION['lang'] == 'fr') ? 'À propos' : 'About' ?></a></li>
            <li><a href="favoris.php"><?= ($_SESSION['lang'] == 'fr') ? 'Favoris' : 'Favorites' ?></a></li>
            <li><button>Langue</button></li>

            <!-- Formulaire de sélection de langue -->
            <form method="POST" action="apropos.php">
                <select name="lang" onchange="this.form.submit()">
                    <option value="fr" <?= $_SESSION['lang'] == 'fr' ? 'selected' : '' ?>>Français</option>
                    <option value="en" <?= $_SESSION['lang'] == 'en' ? 'selected' : '' ?>>English</option>
                </select>
            </form>

            <?php if ($isConnected): ?>
                <?php if (in_array('chef', $currentRoles)): ?>
                    <li><a href="valider_recettes.php"><?= ($_SESSION['lang'] == 'fr') ? 'Valider des recettes' : 'Validate Recipes' ?></a></li>
                <?php elseif (in_array('askchef', $currentRoles)): ?>
                    <li><span><?= ($_SESSION['lang'] == 'fr') ? 'Demande de rôle chef en attente' : 'Chef role request pending' ?></span></li>
                <?php endif; ?>
                <?php if (in_array('traducteur', $currentRoles)): ?>
                    <li><a href="traduire_recette.php"><?= ($_SESSION['lang'] == 'fr') ? 'Traduire une recette' : 'Translate a Recipe' ?></a></li>
                <?php elseif (in_array('asktraducteur', $currentRoles)): ?>
                    <li><span><?= ($_SESSION['lang'] == 'fr') ? 'Demande de rôle traducteur en attente' : 'Translator role request pending' ?></span></li>
                <?php endif; ?>
                <?php if (in_array('admin', $currentRoles)): ?>
                    <li><a href="admin_panel.php"><?= ($_SESSION['lang'] == 'fr') ? 'Administration' : 'Admin Panel' ?></a></li>
                <?php endif; ?>

                <li class="user-menu">
                    <span class="username"><?= htmlspecialchars($_SESSION["username"]) ?> ▼</span>
                    <div class="dropdown-menu">
                    <a href="account.php"><?= ($_SESSION['lang'] == 'fr') ? 'Votre compte' : 'Your Account' ?></a>
                    <a href="profil.php"><?= ($_SESSION['lang'] == 'fr') ? 'Mon profil' : 'My Profile' ?></a>
                    <a href="logout.php"><?= ($_SESSION['lang'] == 'fr') ? 'Se déconnecter' : 'Log out' ?></a>
                </div>
                </li>
            <?php else: ?>
                <li><a href="create_login.php"><?= ($_SESSION['lang'] == 'fr') ? 'Se connecter' : 'Log in' ?></a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>

<h1><?= ($_SESSION['lang'] == 'fr') ? 'À propos du projet' : 'About the project' ?></h1>

<div class="section">
    <h2><?= ($_SESSION['lang'] == 'fr') ? 'Objectifs' : 'Objectives' ?></h2>
    <p><?= ($_SESSION['lang'] == 'fr') ? 'Ce site web a été conçu dans le cadre du projet de Programmation Web 2024-2025 de l’Université Paris-Saclay. Il vise à <strong>centraliser, gérer et diffuser des recettes culinaires</strong> en assurant leur accessibilité en <em>français</em> et en <em>anglais</em>. Notre plateforme s’appuie sur un fichier JSON structuré, enrichi dynamiquement par les contributions des utilisateurs selon leur rôle spécifique.' : 'This website was created as part of the Web Programming project 2024-2025 at Paris-Saclay University. It aims to <strong>centralize, manage and distribute culinary recipes</strong>, ensuring their accessibility in <em>French</em> and <em>English</em>. Our platform relies on a structured JSON file, dynamically enriched by user contributions based on their specific role.' ?></p>
</div>

<div class="section">
    <h2><?= ($_SESSION['lang'] == 'fr') ? 'Fonctionnalités principales' : 'Main Features' ?></h2>
    <ul>
        <li><?= ($_SESSION['lang'] == 'fr') ? 'Gestion des utilisateurs avec rôles différenciés : <strong>cuisiniers</strong>, <strong>chefs</strong>, <strong>traducteurs</strong> et <strong>administrateur</strong>.' : 'User management with differentiated roles: <strong>chefs</strong>, <strong>cooks</strong>, <strong>translators</strong>, and <strong>administrator</strong>.' ?></li>
        <li><?= ($_SESSION['lang'] == 'fr') ? 'Ajout, modification, traduction et validation des recettes.' : 'Add, modify, translate, and validate recipes.' ?></li>
        <li><?= ($_SESSION['lang'] == 'fr') ? 'Interaction communautaire : commentaires, photos, votes (❤️).' : 'Community interaction: comments, photos, votes (❤️).' ?></li>
        <li><?= ($_SESSION['lang'] == 'fr') ? 'Recherche multicritère et affichage dynamique des recettes selon des filtres (vegan, sans gluten, statut de validation).' : 'Multicriteria search and dynamic recipe display with filters (vegan, gluten-free, validation status).' ?></li>
        <li><?= ($_SESSION['lang'] == 'fr') ? 'Interface bilingue avec bascule instantanée entre le français et l’anglais.' : 'Bilingual interface with instant switching between French and English.' ?></li>
    </ul>
</div>

<div class="section">
    <h2><?= ($_SESSION['lang'] == 'fr') ? 'Approche technique' : 'Technical Approach' ?></h2>
    <p><?= ($_SESSION['lang'] == 'fr') ? 'Le site repose sur une architecture <strong>web monopage</strong>, minimisant les rechargements pour une expérience utilisateur fluide. Les données sont stockées et manipulées sous forme de fichiers JSON, garantissant leur portabilité et leur extensibilité. Un accent particulier a été mis sur la <strong>validation des entrées</strong> et la <strong>gestion sécurisée des sessions utilisateurs</strong>.' : 'The website relies on a <strong>single-page</strong> architecture, minimizing reloads for a smooth user experience. The data is stored and processed in JSON files, ensuring portability and scalability. Special attention has been given to <strong>input validation</strong> and <strong>secure user session management</strong>.' ?></p>
</div>

<div class="section">
    <h2><?= ($_SESSION['lang'] == 'fr') ? 'Perspectives et extensions' : 'Future Directions and Extensions' ?></h2>
    <p><?= ($_SESSION['lang'] == 'fr') ? 'Des fonctionnalités futures incluront l’ajout d’unités de mesure normalisées, une gestion enrichie des ingrédients via des tables relationnelles, et l’intégration d’une API externe pour importer des recettes supplémentaires. Ces évolutions visent à renforcer l’interopérabilité et la granularité des données culinaires hébergées sur la plateforme.' : 'Future features will include the addition of standardized measurement units, enhanced ingredient management via relational tables, and the integration of an external API to import additional recipes. These developments aim to enhance interoperability and data granularity on the platform.' ?></p>
</div>

<footer>
    <?= ($_SESSION['lang'] == 'fr') ? 'Réalisé par [Votre Nom] dans le cadre du projet de Programmation Web 2024-2025, Université Paris-Saclay.' : 'Created by [Your Name] as part of the Web Programming project 2024-2025, Paris-Saclay University.' ?>
</footer>

</body>
</html>
