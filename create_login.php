<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Re7-log in/sign in</title>
    <link rel="stylesheet" href="css/cl.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/cl.js"></script>
    <script>
      /*
        function getname(username){
          $.ajax({
        method: "GET",
        url: "service.php",
        data: {
            "usernamec": username,
        }
    }).done(function(e) {
        console.log(e)
    }).fail(function(e) {
        console.log(e);
    });
    }*/
    
    function getPrenom(Prenom){
        $.ajax({
      method: "GET",
      url: "service.php",
      data: {
          "prenomc": Prenom
      }
}).done(function(e) {
  console.log(e)
  
}).fail(function(e) {
  console.log(e);
});
}
   /*     
function getNom(Nom){
    $.ajax({
  method: "GET",
  url: "service.php",
  data: {
      "nomc": Nom
  }
}).done(function(e) {
  console.log(e)
}).fail(function(e) {
  console.log(e);
})
}
*/
    </script>
    </head>

<body>
    <div id="form_connection" style="display: block;">
            <h2 id="h2_l">Connexion</h2>
            <form id="loginform">
                <label for="username">Nom d'utilisateur</label>
                <input type="text" class="username" id="usernamel" name="username" required >
                <br>
                <label for="password">Mot de passe</label>
                <input type="password" class="password" id="passwordl" name="password" required>
                <br>
                <button type="submit">Se connecter</button>
                <a href="#">Mot de passe oublié ?</a>  
                <button id="createl" >Créer un compte</button>
            </form>
        </div>

        <div id="form_creer" style="display: none;">
            <h2 id="h2_c">Créer un compte</h2>
            <form id="signinform">
                <label for="Nom">Nom</label>
                <input type="text" id="nomc" name="Nom" required>
                <br>
                <label for="Prenom">Prénom</label>
                <input type="text" id="prenomc" name="Prenom" onchange ="getPrenom(this.value)" required>
                <br>
                <label for="username">Nom d'utilisateur</label>
                <input type="text" class="username" id="usernamec" name="username" required>
                <br>
                <label for="mail">Adresse mail</label>
                <input type="text" class="mail" id="mailc" name="mail" required>
                <br>
                <label for="password">Mot de passe</label>
                <input type="password"  class="password" name="password" required>
                <br>
                <label for="password">Confirmer votre Mot de passe</label>
                <input type="password" class="password" name="password" required>
                <br>
                <button id="buttoncreer" type="submit">Créer le compte</button>
                <button id="createc" >Vous avez déja un compte ?</button>
            </form>
    </div>

    
        
    <footer>
        <p>&copy; 2025 Mon Site Web. Tous droits réservés.</p>
    </footer>
</body>
</html>
