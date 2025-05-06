<?php
session_start();

if (!isset($_SESSION['username']) || !in_array('admin', (array)$_SESSION['role'])) {
    header("Location: main.php");
    exit;
}

$recipesFile = "json/recipes.json";
$recipes = file_exists($recipesFile) ? json_decode(file_get_contents($recipesFile), true) : [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $recipeIndex = $_POST['recipeIndex'];
    $action = $_POST['action'];

    if (isset($recipes[$recipeIndex])) {
        if ($action === 'validate') {
            $recipes[$recipeIndex]['validated'] = true;
        } elseif ($action === 'reject') {
            unset($recipes[$recipeIndex]);
        }

        file_put_contents($recipesFile, json_encode(array_values($recipes), JSON_PRETTY_PRINT));
        header("Location: admin_panel.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Panneau d'administration - Validation des recettes</title>
</head>
<body>
    <h1>Gestion des recettes en attente de validation</h1>

    <table border="1" cellpadding="5">
        <thead>
            <tr>
                <th>Nom de la recette</th>
                <th>Auteur</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($recipes as $index => $recipe): ?>
                <?php if (!$recipe['validated']): ?>
                    <tr>
                        <td><?= htmlspecialchars($recipe['nameFR']) ?></td>
                        <td><?= htmlspecialchars($recipe['Author']) ?></td>
                        <td>
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="recipeIndex" value="<?= $index ?>">
                                <button type="submit" name="action" value="validate">Valider</button>
                                <button type="submit" name="action" value="reject">Rejeter</button>
                            </form>
                        </td>
                    </tr>
                <?php endif; ?>
            <?php endforeach; ?>
        </tbody>
    </table>

    <br><a href="main.php">Retour à l'accueil</a>
</body>
</html>
