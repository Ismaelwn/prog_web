document.addEventListener("DOMContentLoaded", function () {
    const createAccountBtn = document.getElementById("createl");
    const backToLoginBtn = document.getElementById("createc");
    const loginForm = document.getElementById("form_connection");
    const registerForm = document.getElementById("form_creer");

    // Lorsqu'on clique sur "Créer un compte", on passe au formulaire d'inscription
    createAccountBtn.addEventListener("click", function (event) {
        event.preventDefault(); // Empêche le formulaire de soumettre
        loginForm.style.display = "none";
        registerForm.style.display = "block";
    });

    // Lorsqu'on clique sur "Retour à la connexion", on revient au formulaire de connexion
    backToLoginBtn.addEventListener("click", function (event) {
        event.preventDefault(); // Empêche le bouton de soumettre un formulaire
        registerForm.style.display = "none";
        loginForm.style.display = "block";
    });
});




        