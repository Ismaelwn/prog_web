<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Re7-log in/sign in</title>
    <link rel="stylesheet" href="css/cl.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/cl.js"></script>
    <script src="js/login.js"></script>
</head>
<body>
    <div id="form_connection" style="display: block;">
        <h2 id="h2_l">Connexion</h2>
        <button type="button" id="bmainl" onclick="window.location.href='main.php'">&times</button>
        <form id="loginform">
            <label for="username">Nom d'utilisateur</label>
            <input type="text" class="username" id="usernamel" name="username" required>
            <br>
            <label for="password">Mot de passe</label>
            <input type="password" class="passwordl" id="passwordl" name="password" required>
            <br>
            <button type="submit">Se connecter</button>
            <a href="#">Mot de passe oublié ?</a>  
            <button type="button" id="createl">Créer un compte</button>
        </form>
    </div>

    <div id="form_creer" style="display: none;">
        <h2 id="h2_c">Créer un compte</h2> 
        <button type="button" id="bmainc" onclick="window.location.href='main.php'">&times</button>
        <form id="signinform">
            <label for="Nom">Nom</label>
            <input type="text" id="nomc" name="Nom" required>
            <br>
            <label for="Prenom">Prénom</label>
            <input type="text" id="prenomc" name="Prenom" required>
            <br>
            <label for="username">Nom d'utilisateur</label>
            <input type="text" id="usernamec" name="username" required>
            <br>
            <label for="mail">Adresse mail</label>
            <input type="email" id="mailc" name="mail" required>
            <br>
            <label for="password">Mot de passe</label>
            <input type="password" id="passwordc" name="password" required>
            <br>
            <label for="role">Role</label>
            <select name="Role" required>
                <option value="cuisinier">Cuisinier</option>
                <option value="askchef">DemandeChef</option>
                <option value="asktraducteur">DemandeTraducteur</option>
            </select>
            <br>
            <button type="submit" id="submit_register">Créer le compte</button>
            <button type="button" id="createc">Vous avez déjà un compte ?</button>
        </form>

        
    </div>
</body>
</html>
