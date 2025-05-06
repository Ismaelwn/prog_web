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
?>

<!DOCTYPE html>
<html lang="<?= $_SESSION['lang'] == 'fr' ? 'fr' : 'en' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $_SESSION['lang'] == 'fr' ? 'Re7 - Connexion/Inscription' : 'Re7 - Log in/Sign up' ?></title>
    <link rel="stylesheet" href="css/cl.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/cl.js"></script>
    <script src="js/login.js"></script>
</head>
<body>
    <!-- Sélecteur de langue -->
    

    <div id="form_connection" style="display: block;">
        <h2 id="h2_l"><?= $_SESSION['lang'] == 'fr' ? 'Connexion' : 'Log in' ?></h2>
        <button type="button" id="bmainl" onclick="window.location.href='main.php'">&times</button>
        <form id="loginform">
            <label for="username"><?= $_SESSION['lang'] == 'fr' ? 'Nom d\'utilisateur' : 'Username' ?></label>
            <input type="text" class="username" id="usernamel" name="username" required>
            <br>
            <label for="password"><?= $_SESSION['lang'] == 'fr' ? 'Mot de passe' : 'Password' ?></label>
            <input type="password" class="passwordl" id="passwordl" name="password" required>
            <br>
            <button type="submit"><?= $_SESSION['lang'] == 'fr' ? 'Se connecter' : 'Log in' ?></button>
            <a href="#"><?= $_SESSION['lang'] == 'fr' ? 'Mot de passe oublié ?' : 'Forgot password?' ?></a>  
            <button type="button" id="createl"><?= $_SESSION['lang'] == 'fr' ? 'Créer un compte' : 'Create an account' ?></button>
        </form>
    </div>

    <div id="form_creer" style="display: none;">
        <h2 id="h2_c"><?= $_SESSION['lang'] == 'fr' ? 'Créer un compte' : 'Create an account' ?></h2> 
        <button type="button" id="bmainc" onclick="window.location.href='main.php'">&times</button>
        <form id="signinform">
            <label for="Nom"><?= $_SESSION['lang'] == 'fr' ? 'Nom' : 'Last Name' ?></label>
            <input type="text" id="nomc" name="Nom" required>
            <br>
            <label for="Prenom"><?= $_SESSION['lang'] == 'fr' ? 'Prénom' : 'First Name' ?></label>
            <input type="text" id="prenomc" name="Prenom" required>
            <br>
            <label for="username"><?= $_SESSION['lang'] == 'fr' ? 'Nom d\'utilisateur' : 'Username' ?></label>
            <input type="text" id="usernamec" name="username" required>
            <br>
            <label for="mail"><?= $_SESSION['lang'] == 'fr' ? 'Adresse mail' : 'Email address' ?></label>
            <input type="email" id="mailc" name="mail" required>
            <br>
            <label for="password"><?= $_SESSION['lang'] == 'fr' ? 'Mot de passe' : 'Password' ?></label>
            <input type="password" id="passwordc" name="password" required>
            <br>
            <label for="role"><?= $_SESSION['lang'] == 'fr' ? 'Rôle' : 'Role' ?></label>
            <select name="Role" required>
                <option value="cuisinier"><?= $_SESSION['lang'] == 'fr' ? 'Cuisinier' : 'Cook' ?></option>
            </select>
            <br>
            <button type="submit" id="submit_register"><?= $_SESSION['lang'] == 'fr' ? 'Créer le compte' : 'Create Account' ?></button>
            <button type="button" id="createc"><?= $_SESSION['lang'] == 'fr' ? 'Vous avez déjà un compte ?' : 'Already have an account?' ?></button>
        </form>
    </div>
</body>
</html>
