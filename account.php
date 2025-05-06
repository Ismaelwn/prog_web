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

if (!isset($_SESSION["username"])) {
    header("Location: main.php");
    exit;
}

$username = $_SESSION["username"];
$usersFile = "json/users.json";
$requestsFile = "json/request.json";

$users = json_decode(file_get_contents($usersFile), true);
$currentUser = null;

// Récupérer les infos de l'utilisateur
foreach ($users as &$user) {
    if ($user["username"] === $username) {
        $currentUser = &$user;
        break;
    }
}

if (!$currentUser) {
    die("Utilisateur non trouvé.");
}

$success = $error = '';

// Traitement du changement d'email
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["new_email"])) {
    $newEmail = trim($_POST["new_email"]);
    if (filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
        $currentUser["email"] = $newEmail;
        file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT));
        $success = ($_SESSION['lang'] == 'fr') ? "Adresse email mise à jour." : "Email address updated.";
    } else {
        $error = ($_SESSION['lang'] == 'fr') ? "Adresse email invalide." : "Invalid email address.";
    }
}

// Traitement de la demande de rôle
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["role_request"])) {
    $requestedRole = $_POST["role_request"];
    $validRoles = ["chef", "traducteur"];

    if (in_array($requestedRole, $validRoles)) {
        $requests = file_exists($requestsFile) ? json_decode(file_get_contents($requestsFile), true) : [];

        // éviter les doublons
        $alreadyRequested = false;
        foreach ($requests as $req) {
            if ($req["username"] === $username && $req["role"] === $requestedRole) {
                $alreadyRequested = true;
                break;
            }
        }

        if (!$alreadyRequested) {
            $requests[] = [
                "username" => $username,
                "role" => $requestedRole,
                "timestamp" => time()
            ];
            file_put_contents($requestsFile, json_encode($requests, JSON_PRETTY_PRINT));
            $success = ($_SESSION['lang'] == 'fr') ? "Demande pour le rôle \"$requestedRole\" envoyée." : "Request for the \"$requestedRole\" role sent.";
        } else {
            $error = ($_SESSION['lang'] == 'fr') ? "Vous avez déjà fait une demande pour ce rôle." : "You have already requested this role.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="<?= $_SESSION['lang'] == 'fr' ? 'fr' : 'en' ?>">
<head>
    <meta charset="UTF-8">
    <title><?= ($_SESSION['lang'] == 'fr') ? 'Mon compte' : 'My Account' ?> - Re7</title>
</head>
<body>
    <h1><?= ($_SESSION['lang'] == 'fr') ? 'Mon compte' : 'My Account' ?></h1>

    <?php if ($success): ?><p style="color:green"><?= htmlspecialchars($success) ?></p><?php endif; ?>
    <?php if ($error): ?><p style="color:red"><?= htmlspecialchars($error) ?></p><?php endif; ?>

    <p><strong><?= ($_SESSION['lang'] == 'fr') ? 'Nom d\'utilisateur :' : 'Username :' ?></strong> <?= htmlspecialchars($username) ?></p>
    <p><strong><?= ($_SESSION['lang'] == 'fr') ? 'Prénom :' : 'First Name :' ?></strong> <?= htmlspecialchars($currentUser["prenom"] ?? ($_SESSION['lang'] == 'fr' ? "Non renseigné" : "Not provided")) ?></p>
    <p><strong><?= ($_SESSION['lang'] == 'fr') ? 'Nom :' : 'Last Name :' ?></strong> <?= htmlspecialchars($currentUser["nom"] ?? ($_SESSION['lang'] == 'fr' ? "Non renseigné" : "Not provided")) ?></p>
    <p><strong><?= ($_SESSION['lang'] == 'fr') ? 'Email :' : 'Email :' ?></strong> <?= htmlspecialchars($currentUser["mail"] ?? ($_SESSION['lang'] == 'fr' ? "Non renseigné" : "Not provided")) ?></p>
    <p><strong><?= ($_SESSION['lang'] == 'fr') ? 'Statut :' : 'Status :' ?></strong> <?= htmlspecialchars(implode(', ', $currentUser["role"] ?? [($_SESSION['lang'] == 'fr' ? "Non défini" : "Not defined")])) ?></p>

    <h2><?= ($_SESSION['lang'] == 'fr') ? 'Modifier l\'adresse email' : 'Change Email Address' ?></h2>
    <form method="post">
        <input type="email" name="new_email" placeholder="<?= ($_SESSION['lang'] == 'fr') ? 'Nouvel email' : 'New email' ?>" required>
        <button type="submit"><?= ($_SESSION['lang'] == 'fr') ? 'Mettre à jour' : 'Update' ?></button>
    </form>

    <h2><?= ($_SESSION['lang'] == 'fr') ? 'Demander un nouveau rôle' : 'Request a New Role' ?></h2>
    <form method="post">
        <select name="role_request">
            <option value="chef"><?= ($_SESSION['lang'] == 'fr') ? 'Chef' : 'Chef' ?></option>
            <option value="traducteur"><?= ($_SESSION['lang'] == 'fr') ? 'Traducteur' : 'Translator' ?></option>
        </select>
        <button type="submit"><?= ($_SESSION['lang'] == 'fr') ? 'Faire une demande' : 'Make a Request' ?></button>
    </form>

    <br><a href="main.php"><?= ($_SESSION['lang'] == 'fr') ? 'Retour à l\'accueil' : 'Back to Home' ?></a>
</body>
</html>
