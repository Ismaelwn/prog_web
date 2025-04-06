<?php
session_start();

if (!isset($_SESSION['username']) || !isset($_SESSION['role'])) {
    header("Location: main.php");
    exit;
}

$roles = (array)$_SESSION['role'];
if (!in_array('traducteur', $roles) && !in_array('admin', $roles)) {
    header("Location: main.php");
    exit;
}

$recipesFile = "json/recipes.json";
$recipes = file_exists($recipesFile) ? json_decode(file_get_contents($recipesFile), true) : [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nameFR = $_POST['nameFR'];
    $nameEN = trim($_POST['nameEN']);

    foreach ($recipes as &$recipe) {
        if ($recipe['nameFR'] === $nameFR) {
            $recipe['nameEN'] = $nameEN;
            break;
        }
    }
    unset($recipe);

    file_put_contents($recipesFile, json_encode($recipes, JSON_PRETTY_PRINT));
    $_SESSION['translation_success'] = true;
    header("Location: traduire_recette.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Traduire une recette</title>
</head>
<body>
    <h1>Traduction des recettes</h1>

    <?php if (!empty($_SESSION['translation_success'])): ?>
        <p style="color: green;">✅ Traduction enregistrée avec succès !</p>
        <?php unset($_SESSION['translation_success']); ?>
    <?php endif; ?>

    <?php
    $nonTraduites = array_filter($recipes, function ($r) {
        return empty($r['nameEN']);
    });

    if (empty($nonTraduites)): ?>
        <p>Toutes les recettes ont été traduites.</p>
    <?php else: ?>
        <?php foreach ($nonTraduites as $recipe): ?>
            <form method="post" style="margin-bottom: 20px;">
                <strong><?= htmlspecialchars($recipe['nameFR']) ?></strong><br>
                <input type="hidden" name="nameFR" value="<?= htmlspecialchars($recipe['nameFR']) ?>">
                <label for="nameEN">Nom en anglais :</label>
                <input type="text" name="nameEN" required>
                <button type="submit">Traduire</button>
            </form>
        <?php endforeach; ?>
    <?php endif; ?>

    <br><a href="main.php">Retour à l'accueil</a>
</body>
</html>
