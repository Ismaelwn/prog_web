<?php
session_start();

if (!isset($_SESSION['username']) || !isset($_SESSION['role'])) {
    header("Location: main.php");
    exit;
}

$username = $_SESSION['username'];
$roles = (array)$_SESSION['role'];

if (!in_array('traducteur', $roles) && !in_array('admin', $roles) && !in_array('chef', $roles)) {
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
    if (isset($_POST['nameFR'])) {
        $recipes[$recipeIndex]['nameFR'] = $_POST['nameFR'];
    }

    if (isset($_POST['ingredientsEN'])) {
        $recipes[$recipeIndex]['ingredientsEN'] = $_POST['ingredientsEN'];
    }
    if (isset($_POST['ingredientsFR'])) {
        foreach ($_POST['ingredientsFR'] as $i => $val) {
            $recipes[$recipeIndex]['ingredientsFR'][$i]['name'] = $val;
        }
    }

    if (isset($_POST['stepsEN'])) {
        $recipes[$recipeIndex]['stepsEN'] = $_POST['stepsEN'];
    }
    if (isset($_POST['stepsFR'])) {
        $recipes[$recipeIndex]['stepsFR'] = $_POST['stepsFR'];
    }

    file_put_contents($recipesFile, json_encode($recipes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
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
            <?php
                $isAuthor = ($username === $recipe['Author']);
                $canEditAll = in_array('admin', $roles) || (in_array('chef', $roles) && $isAuthor);

                $ingredientsEN = $recipe['ingredientsEN'] ?? array_fill(0, count($recipe['ingredientsFR']), '');
                if (count($ingredientsEN) !== count($recipe['ingredientsFR'])) {
                    $ingredientsEN = array_pad($ingredientsEN, count($recipe['ingredientsFR']), '');
                }

                $stepsEN = $recipe['stepsEN'] ?? array_fill(0, count($recipe['stepsFR']), '');
                if (count($stepsEN) !== count($recipe['stepsFR'])) {
                    $stepsEN = array_pad($stepsEN, count($recipe['stepsFR']), '');
                }
            ?>
            <form method="post">
                <h2>Recette : <?= htmlspecialchars($recipe['nameFR']) ?> (Auteur: <?= htmlspecialchars($recipe['Author']) ?>)</h2>
                <input type="hidden" name="recipeIndex" value="<?= $index ?>">

                <table>
                    <tr>
                        <th>Français</th>
                        <th>Anglais</th>
                    </tr>
                    <tr>
                        <td>
                            <?php
                                $canEditNameFR = $canEditAll || (
                                    (in_array('traducteur', $roles) || in_array('chef', $roles)) &&
                                    empty($recipe['nameFR']) && !empty($recipe['nameEN'])
                                );
                            ?>
                            Nom :
                            <?php if ($canEditNameFR): ?>
                                <input type="text" name="nameFR" value="<?= htmlspecialchars($recipe['nameFR'] ?? '') ?>">
                            <?php else: ?>
                                <?= htmlspecialchars($recipe['nameFR']) ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php
                                $canEditNameEN = $canEditAll || (
                                    (in_array('traducteur', $roles) || in_array('chef', $roles)) &&
                                    empty($recipe['nameEN']) && !empty($recipe['nameFR'])
                                );
                            ?>
                            Nom :
                            <?php if ($canEditNameEN): ?>
                                <input type="text" name="nameEN" value="<?= htmlspecialchars($recipe['nameEN'] ?? '') ?>">
                            <?php else: ?>
                                <?= htmlspecialchars($recipe['nameEN']) ?>
                            <?php endif; ?>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            Ingrédients :
                            <ul>
                                <?php foreach ($recipe['ingredientsFR'] as $i => $ing): ?>
                                    <?php
                                        $canEditIngredientFR = $canEditAll || (
                                            (in_array('traducteur', $roles) || in_array('chef', $roles)) &&
                                            empty($ing['name']) && !empty($ingredientsEN[$i])
                                        );
                                    ?>
                                    <li>
                                        <?= htmlspecialchars($ing['quantity']) ?>
                                        <?php if ($canEditIngredientFR): ?>
                                            <input type="text" name="ingredientsFR[<?= $i ?>]" value="<?= htmlspecialchars($ing['name'] ?? '') ?>">
                                        <?php else: ?>
                                            <?= htmlspecialchars($ing['name']) ?>
                                        <?php endif; ?>
                                        (<?= htmlspecialchars($ing['type']) ?>)
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </td>
                        <td>
                            <ul>
                                <?php foreach ($ingredientsEN as $i => $ingEN): ?>
                                    <?php
                                        $canEditIngredientEN = $canEditAll || (
                                            (in_array('traducteur', $roles) || in_array('chef', $roles)) &&
                                            empty($ingEN) && !empty($recipe['ingredientsFR'][$i]['name'])
                                        );
                                    ?>
                                    <li>
                                        <?= htmlspecialchars($recipe['ingredientsFR'][$i]['quantity']) ?>
                                        <?php if ($canEditIngredientEN): ?>
                                            <input type="text" name="ingredientsEN[<?= $i ?>]" value="<?= htmlspecialchars($ingEN) ?>">
                                        <?php else: ?>
                                            <?= htmlspecialchars($ingEN) ?>
                                        <?php endif; ?>
                                        (<?= htmlspecialchars($recipe['ingredientsFR'][$i]['type']) ?>)
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            Étapes :
                            <ol>
                                <?php foreach ($recipe['stepsFR'] as $j => $stepFR): ?>
                                    <?php
                                        $canEditStepFR = $canEditAll || (
                                            (in_array('traducteur', $roles) || in_array('chef', $roles)) &&
                                            empty($stepFR) && !empty($stepsEN[$j])
                                        );
                                    ?>
                                    <li>
                                        <?php if ($canEditStepFR): ?>
                                            <textarea name="stepsFR[<?= $j ?>]"><?= htmlspecialchars($stepFR) ?></textarea>
                                        <?php else: ?>
                                            <?= htmlspecialchars($stepFR) ?>
                                        <?php endif; ?>
                                    </li>
                                <?php endforeach; ?>
                            </ol>
                        </td>
                        <td>
                            <ol>
                                <?php foreach ($stepsEN as $j => $stepEN): ?>
                                    <?php
                                        $canEditStepEN = $canEditAll || (
                                            (in_array('traducteur', $roles) || in_array('chef', $roles)) &&
                                            empty($stepEN) && !empty($recipe['stepsFR'][$j])
                                        );
                                    ?>
                                    <li>
                                        <?php if ($canEditStepEN): ?>
                                            <textarea name="stepsEN[<?= $j ?>]"><?= htmlspecialchars($stepEN) ?></textarea>
                                        <?php else: ?>
                                            <?= htmlspecialchars($stepEN) ?>
                                        <?php endif; ?>
                                    </li>
                                <?php endforeach; ?>
                            </ol>
                        </td>
                    </tr>
                </table>

                <button type="submit">Enregistrer les traductions</button>
            </form>
            <hr>
        <?php endforeach; ?>
    <?php endif; ?>

    <br><a href="main.php">Retour à l'accueil</a>
</body>
</html>
