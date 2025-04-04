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
            console.log(response); // Vérifiez la réponse du serveur
            if (response.message === "Connexion réussie !") {
                // Redirection vers la page après connexion réussie
                window.location.href = "main.php";  // Rediriger vers la page d'accueil après connexion
            }
        })
        .fail(function(error) {
            console.log(error);
            alert("Erreur lors de la tentative de connexion.");
        });
    });
});
