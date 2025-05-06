<?php
session_start();

if (!isset($_SESSION['username']) || !isset($_SESSION['role'])) {
    header("Location: main.php");
    exit;
}

$roles = (array)$_SESSION['role'];
if (!in_array('chef', $roles)) {
    header("Location: main.php");
    exit;
}

$recipesFile = "json/recipes.json";
$recipes = file_exists($recipesFile) ? json_decode(file_get_contents($recipesFile), true) : [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nameFR = trim($_POST['nameFR']);
    $name = trim($_POST['name']);
    $ingredientsFR = array_filter(array_map('trim', explode("\n", $_POST['ingredientsFR'])));
    $ingredients = array_filter(array_map('trim', explode("\n", $_POST['ingredients'])));
    $stepsFR = array_filter(array_map('trim', explode("\n", $_POST['stepsFR'])));
    $steps = array_filter(array_map('trim', explode("\n", $_POST['steps'])));
    $timers = array_map('trim', explode(",", $_POST['timers']));
    $imageURL = trim($_POST['imageURL']);
    $allergenes = array_filter(array_map('trim', explode(',', $_POST['allergenes'])));

    $exists = false;
    foreach ($recipes as $r) {
        if (strtolower($r['nameFR']) === strtolower($nameFR)) {
            $exists = true;
            break;
        }
    }

    if ($exists) {
        $error = "Une recette avec ce nom existe déjà.";
    } elseif ($nameFR && $name && $ingredients && $steps) {
        $newRecipe = [
            "nameFR" => $nameFR,
            "name" => $name,
            "ingredientsFR" => $ingredientsFR,
            "ingredients" => $ingredients,
            "stepsFR" => $stepsFR,
            "steps" => $steps,
            "timers" => $timers,
            "imageURL" => $imageURL,
            "allergenes" => $allergenes,
            "Author" => $_SESSION['username'],
            "validated" => in_array("chef", $roles),
            "likers" => [],
            "commentaire" => [],
        ];

        $recipes[] = $newRecipe;
        file_put_contents($recipesFile, json_encode($recipes, JSON_PRETTY_PRINT));
        $success = "Recette ajoutée avec succès !";
    } else {
        $error = "Tous les champs obligatoires doivent être remplis.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter une recette</title>
</head>
<body>
    <h1>Soumettre une nouvelle recette</h1>

    <?php if (!empty($success)): ?>
        <p style="color: green;"><?= htmlspecialchars($success) ?></p>
    <?php elseif (!empty($error)): ?>
        <p style="color: red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="post">
        <label for="nameFR">Nom de la recette (Français) :</label><br>
        <input type="text" name="nameFR" required><br><br>

        <label for="name">Nom de la recette (Anglais) :</label><br>
        <input type="text" name="name" required><br><br>

        <label for="ingredientsFR">Ingrédients (Français) :</label><br>
        <textarea name="ingredientsFR" rows="5" cols="50" required></textarea><br><br>

        <label for="ingredients">Ingrédients (Anglais) :</label><br>
        <textarea name="ingredients" rows="5" cols="50" required></textarea><br><br>

        <label for="stepsFR">Étapes (Français) :</label><br>
        <textarea name="stepsFR" rows="5" cols="50" required></textarea><br><br>

        <label for="steps">Étapes (Anglais) :</label><br>
        <textarea name="steps" rows="5" cols="50" required></textarea><br><br>

        <label for="timers">Timers (en secondes, séparés par des virgules) :</label><br>
        <input type="text" name="timers" placeholder="ex : 0, 5, 1, 1, 0, 10" required><br><br>

        <label for="imageURL">URL de l'image :</label><br>
        <input type="text" name="imageURL" required><br><br>

        <label for="allergenes">Allergènes (séparés par des virgules) :</label><br>
        <input type="text" name="allergenes" placeholder="ex : gluten, œufs, lait"><br><br>

        <button type="submit">Ajouter la recette</button>
    </form>

    <br><a href="main.php">Retour à l'accueil</a>
</body>
</html>
