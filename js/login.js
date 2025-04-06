document.addEventListener("DOMContentLoaded", function () {
    $("#loginform").submit(function (e) {
        e.preventDefault(); // Empêche l'envoi classique

        const userData = {
            username: $("#usernamel").val(),
            password: $("#passwordl").val()
        };

        $.get("login_handler.php", userData)
            .done(function (response) {
                console.log(response); // Affiche la réponse dans la console

                if (response.message === "Connexion réussie !") {
                    window.location.href = "main.php";
                } else if (response.error) {
                    alert(response.error);
                }
            })
            .fail(function (xhr) {
                console.error(xhr);
                alert("Erreur lors de la tentative de connexion.");
            });
    });
});
