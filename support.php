<?php
session_start();

// Protection + vérification admin
if (!isset($_SESSION['username']) || !isset($_SESSION['role']) || !is_array($_SESSION['role']) || !in_array('admin', $_SESSION['role'])) {
    header("Location: main.php");
    exit;
}

// Charger les requêtes
$requests = json_decode(file_get_contents('json/request.json'), true);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Gestion des requêtes</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="support.js"></script>
</head>
<body>
    <h1>Gestion des requêtes</h1>

    <?php if (empty($requests)): ?>
        <p>Aucune requête en attente.</p>
    <?php else: ?>
        <table id="request-table">
            <tr><th>Utilisateur</th><th>Rôle demandé</th><th>Action</th></tr>
            <?php foreach ($requests as $index => $req): ?>
                <tr id="row-<?= $index ?>">
                    <td><?= htmlspecialchars($req['username']) ?></td>
                    <td><?= htmlspecialchars($req['role']) ?></td>
                    <td>
                        <button class="accept-btn" data-index="<?= $index ?>" data-username="<?= htmlspecialchars($req['username']) ?>" data-role="<?= htmlspecialchars($req['role']) ?>">Accepter</button>
                        <button class="reject-btn" data-index="<?= $index ?>" data-username="<?= htmlspecialchars($req['username']) ?>" data-role="<?= htmlspecialchars($req['role']) ?>">Refuser</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>

    <p><a href="main.php">Retour</a></p>
</body>
</html>
