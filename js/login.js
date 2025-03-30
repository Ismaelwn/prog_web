document.addEventListener("DOMContentLoaded", function () {
    const loginForm = document.getElementById("form_connection");

    // Lorsque le formulaire de connexion est soumis
    $("#loginform").submit(function(e) {
        e.preventDefault();  // Empêche la soumission classique du formulaire

        var userData = {
            username: $("#usernamel").val(),
            password: $("#passwordl").val()
        };

        // Envoi de la requête AJAX pour vérifier la connexion
        $.get("service2.php", userData)
            .done(function(response) {
                console.log(response); // Vérifie ce que renvoie le serveur

                // Vérifie si une erreur est renvoyée
                if (response.error) {
                    alert("Erreur de connexion : " + response.error);
                } else {
                    alert("Connexion réussie !");
                    
                    
                }
            })
            .fail(function(error) {
                console.log(error);
                alert("Erreur lors de la tentative de connexion.");
            });
    });
});
