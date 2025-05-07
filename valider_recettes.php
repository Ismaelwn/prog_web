<?php
session_start();

if (!isset($_SESSION['username']) || !isset($_SESSION['role']) ||
    (!in_array('admin', (array)$_SESSION['role']) && !in_array('chef', (array)$_SESSION['role']))) {
    header("Location: main.php");
    exit;
}

$recipesFile = "json/recipes.json";
$recipes = file_exists($recipesFile) ? json_decode(file_get_contents($recipesFile), true) : [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['validate'])) {
    $recipeName = $_POST['validate'];
    $found = false;

    foreach ($recipes as &$recipe) {
        if ($recipe['nameFR'] === $recipeName) {
            $recipe['validated'] = true;
            $found = true;
            break;
        }
    }
    unset($recipe);

    if ($found) {
        file_put_contents($recipesFile, json_encode($recipes, JSON_PRETTY_PRINT));
        $_SESSION['validation_success'] = $recipeName;
    } else {
        $_SESSION['validation_error'] = "Recette introuvable.";
    }

    header("Location: valider_recettes.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Valider des recettes</title>
    <link rel="stylesheet" href="css/style7.css">
</head>
<body>
    <h1>Recettes à valider</h1>

    <?php if (!empty($_SESSION['validation_success'])): ?>
        <p style="color: green;">✅ Recette validée : <?= htmlspecialchars($_SESSION['validation_success']) ?></p>
        <?php unset($_SESSION['validation_success']); ?>
    <?php elseif (!empty($_SESSION['validation_error'])): ?>
        <p style="color: red;">❌ <?= htmlspecialchars($_SESSION['validation_error']) ?></p>
        <?php unset($_SESSION['validation_error']); ?>
    <?php endif; ?>

    <?php
    $nonValidees = array_filter($recipes, function ($r) {
        return empty($r['validated']);
    });

    if (empty($nonValidees)): ?>
        <p>Toutes les recettes ont été validées.</p>
    <?php else: ?>
        <?php foreach ($nonValidees as $recipe): ?>
            <div style="margin-bottom: 20px;">
                <h2><?= htmlspecialchars($recipe['nameFR']) ?></h2>
                <p>Auteur : <?= htmlspecialchars($recipe['Author'] ?? 'Inconnu') ?></p>
                <form method="post">
                    <input type="hidden" name="validate" value="<?= htmlspecialchars($recipe['nameFR']) ?>">
                    <button type="submit">Valider</button>
                </form>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <br><a href="main.php">Retour à l'accueil</a>
</body>
</html>
