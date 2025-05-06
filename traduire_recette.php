<?php
session_start();

if (!isset($_SESSION['username']) || !isset($_SESSION['role'])) {
    header("Location: main.php");
    exit;
}

$username = $_SESSION['username'];
$roles = (array)$_SESSION['role'];

if (!in_array('traducteur', $roles) && !in_array('admin', $roles)) {
    header("Location: main.php");
    exit;
}

$recipesFile = "json/recipes.json";
$recipes = file_exists($recipesFile) ? json_decode(file_get_contents($recipesFile), true) : [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $recipeIndex = intval($_POST['recipeIndex']);

    if (isset($_POST['nameEN'])) {
        $recipes[$recipeIndex]['nameEN'] = $_POST['nameEN'];
    }

    if (isset($_POST['ingredientsEN'])) {
        $recipes[$recipeIndex]['ingredientsEN'] = $_POST['ingredientsEN'];
    }

    if (isset($_POST['stepsEN'])) {
        $recipes[$recipeIndex]['stepsEN'] = $_POST['stepsEN'];
    }

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
    <style>
        table { border-collapse: collapse; width: 100%; margin-bottom: 20px; }
        td, th { border: 1px solid black; padding: 8px; vertical-align: top; }
        input[type="text"], textarea { width: 100%; }
    </style>
</head>
<body>
    <h1>Traduction des recettes</h1>

    <?php if (!empty($_SESSION['translation_success'])): ?>
        <p style="color: green;">✅ Traduction enregistrée avec succès !</p>
        <?php unset($_SESSION['translation_success']); ?>
    <?php endif; ?>

    <?php if (empty($recipes)): ?>
        <p>Aucune recette disponible.</p>
    <?php else: ?>
        <?php foreach ($recipes as $index => $recipe): ?>
            <form method="post">
                <h2>Recette : <?= htmlspecialchars($recipe['nameFR']) ?></h2>
                <input type="hidden" name="recipeIndex" value="<?= $index ?>">

                <table>
                    <tr>
                        <th>Français</th>
                        <th>Anglais</th>
                    </tr>
                    <tr>
                        <td>Nom : <?= htmlspecialchars($recipe['nameFR']) ?></td>
                        <td>
                            <?php if (empty($recipe['nameEN'])): ?>
                                <input type="text" name="nameEN" value="">
                            <?php else: ?>
                                <?= htmlspecialchars($recipe['nameEN']) ?>
                            <?php endif; ?>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            Ingrédients :
                            <ul>
                                <?php foreach ($recipe['ingredientsFR'] as $ing): ?>
                                    <li><?= htmlspecialchars($ing['quantity']) ?> <?= htmlspecialchars($ing['name']) ?> (<?= htmlspecialchars($ing['type']) ?>)</li>

                                <?php endforeach; ?>
                            </ul>
                        </td>
                        <td>
                            <?php
                            $ingredientsEN = $recipe['ingredientsEN'] ?? array_fill(0, count($recipe['ingredientsFR']), '');
                            if (count($ingredientsEN) !== count($recipe['ingredientsFR'])) {
                                $ingredientsEN = array_pad($ingredientsEN, count($recipe['ingredientsFR']), '');
                            }
                            ?>
                            <ul>
                                <?php foreach ($ingredientsEN as $i => $ingEN): ?>
                                    <li>
                                        <?php if (empty($ingEN)): ?>
                                            <input type="text" name="ingredientsEN[<?= $i ?>]" value="">
                                        <?php else: ?>
                                            <?= htmlspecialchars($ingEN) ?>
                                        <?php endif; ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            Étapes :
                            <ol>
                                <?php foreach ($recipe['stepsFR'] as $step): ?>
                                    <li><?= htmlspecialchars($step) ?></li>
                                <?php endforeach; ?>
                            </ol>
                        </td>
                        <td>
                            <?php
                            $stepsEN = $recipe['stepsEN'] ?? array_fill(0, count($recipe['stepsFR']), '');
                            if (count($stepsEN) !== count($recipe['stepsFR'])) {
                                $stepsEN = array_pad($stepsEN, count($recipe['stepsFR']), '');
                            }
                            ?>
                            <ol>
                                <?php foreach ($stepsEN as $j => $stepEN): ?>
                                    <li>
                                        <?php if (empty($stepEN)): ?>
                                            <textarea name="stepsEN[<?= $j ?>]"></textarea>
                                        <?php else: ?>
                                            <?= htmlspecialchars($stepEN) ?>
                                        <?php endif; ?>
                                    </li>
                                <?php endforeach; ?>
                            </ol>
                        </td>
                    </tr>
                </table>

                <?php if (empty($recipe['nameEN']) || array_filter($ingredientsEN, fn($v) => empty($v)) || array_filter($stepsEN, fn($v) => empty($v))): ?>
                    <button type="submit">Enregistrer les traductions</button>
                <?php else: ?>
                    <p>Toutes les traductions sont complètes pour cette recette.</p>
                <?php endif; ?>
            </form>
            <hr>
        <?php endforeach; ?>
    <?php endif; ?>

    <br><a href="main.php">Retour à l'accueil</a>
</body>
</html>
