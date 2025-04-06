<?php
session_start();

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
        $success = "Adresse email mise à jour.";
    } else {
        $error = "Adresse email invalide.";
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
            $success = "Demande pour le rôle \"$requestedRole\" envoyée.";
        } else {
            $error = "Vous avez déjà fait une demande pour ce rôle.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon compte</title>
</head>
<body>
    <h1>Mon compte</h1>

    <?php if ($success): ?><p style="color:green"><?= htmlspecialchars($success) ?></p><?php endif; ?>
    <?php if ($error): ?><p style="color:red"><?= htmlspecialchars($error) ?></p><?php endif; ?>

    <p><strong>Nom d'utilisateur :</strong> <?= htmlspecialchars($username) ?></p>
    <p><strong>Prénom :</strong> <?= htmlspecialchars($currentUser["prenom"] ?? "Non renseigné") ?></p>
    <p><strong>Nom :</strong> <?= htmlspecialchars($currentUser["nom"] ?? "Non renseigné") ?></p>
    <p><strong>Email :</strong> <?= htmlspecialchars($currentUser["email"] ?? "Non renseigné") ?></p>
    <p><strong>Statut :</strong> <?= htmlspecialchars(implode(', ', $currentUser["role"] ?? ["Non défini"])) ?></p>

    <h2>Modifier l'adresse email</h2>
    <form method="post">
        <input type="email" name="new_email" placeholder="Nouvel email" required>
        <button type="submit">Mettre à jour</button>
    </form>

    <h2>Demander un nouveau rôle</h2>
    <form method="post">
        <select name="role_request">
            <option value="chef">Chef</option>
            <option value="traducteur">Traducteur</option>
        </select>
        <button type="submit">Faire une demande</button>
    </form>

    <br><a href="main.php">Retour à l'accueil</a>
</body>
</html>
