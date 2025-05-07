<?php
session_start();

if (!isset($_SESSION['username']) || !in_array('admin', (array)$_SESSION['role'])) {
    header("Location: main.php");
    exit;
}

$usersFile = "json/users.json";
$users = file_exists($usersFile) ? json_decode(file_get_contents($usersFile), true) : [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $newRole = $_POST['role'];

    foreach ($users as &$user) {
        if ($user['username'] === $username) {
            // Ajouter le nouveau rôle si ce n'est pas déjà présent
            if (!in_array($newRole, $user['role'])) {
                $user['role'][] = $newRole;
                $user['role'] = array_unique($user['role']);
            }

            // Supprimer le rôle de demande (ask*) si présent
            if (in_array('ask' . $newRole, $user['role'])) {
                $user['role'] = array_diff($user['role'], ['ask' . $newRole]);
            }

            break;
        }
    }
    unset($user);

    file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT));
    header("Location: admin_panel.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Panneau d'administration</title>
    <link rel="stylesheet" href="css/style3.css">
</head>
<body>
    <h1>Gestion des utilisateurs</h1>

    <table border="1" cellpadding="5">
        <thead>
            <tr>
                <th>Nom d'utilisateur</th>
                <th>Rôles actuels</th>
                <th>Ajouter un rôle</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= htmlspecialchars($user['username']) ?></td>
                    <td><?= htmlspecialchars(implode(", ", $user['role'])) ?></td>
                    <td>
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="username" value="<?= htmlspecialchars($user['username']) ?>">
                            <select name="role" required>
                                <option value="">-- Choisir un rôle --</option>
                                <option value="chef">Chef</option>
                                <option value="traducteur">Traducteur</option>
                            </select>
                            <button type="submit">Ajouter</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <br><a href="main.php">Retour à l'accueil</a>
</body>
</html>