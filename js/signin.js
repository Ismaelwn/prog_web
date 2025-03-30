const registerForm = document.getElementById("form_creer");    

// Validation du formulaire d'inscription avant envoi
$("#signinform").submit(function(e) {
    e.preventDefault(); // Empêche la soumission du formulaire/raffraichissement de la page
    
    var userData = {
        nom: $("#nomc").val(),
        prenom: $("#prenomc").val(),
        username: $("#usernamec").val(),
        mail: $("#mailc").val(),
        password: $("#passwordc").val(),
        role: $("select[name='Role']").val()
    };

    // Vérification de la disponibilité du nom d'utilisateur
    usernameEstDisponible(userData.username, function(usernameDisponible) {
        if (!usernameDisponible) {
            alert("Le nom d'utilisateur est déjà pris. Veuillez en choisir un autre.");
            return; // Arrête l'exécution si le nom d'utilisateur est déjà pris
        }

        // Vérification de la disponibilité de l'email
        emailEstDisponible(userData.mail, function(emailDisponible) {
            if (!emailDisponible) {
                alert("L'email est déjà utilisé. Veuillez en choisir un autre.");
                return; // Arrête l'exécution si l'email est déjà pris
            }

            // Vérification de la validité du mot de passe
            if (!verifierMotDePasse(userData.password)) {
                alert("Le mot de passe doit contenir au moins 8 caractères, une lettre majuscule, une lettre minuscule et un caractère spécial.");
                return; // Arrête l'exécution si le mot de passe n'est pas valide
            }

            // Si les données sont valides, envoie-les au serveur sans geler la page
            $.ajax({
                method: "GET",
                url: "service.php",
                data: userData,
            }).done(function(response) {
                    // Message de succès sans arrêter l'interaction avec la page
                    alert("Compte créé avec succès !");
                    console.log(response);

                    // Passer à la page de login après la création du compte
                    loginForm.style.display = "block";
                    registerForm.style.display = "none"; // Cacher le formulaire d'inscription
                    
                    // Vider les champs du formulaire d'inscription
                    $("#nomc").val('');
                    $("#prenomc").val('');
                    $("#usernamec").val('');
                    $("#mailc").val('');
                    $("#passwordc").val('');
                    $("select[name='Role']").val('cuisinier'); // Remettre la valeur par défaut du rôle
                })
                .fail(function(error) {
                    // En cas d'erreur, afficher un message sans bloquer la page
                    console.log(error);
                    alert("Erreur lors de la création du compte.");
                });
        });
    });
});

// Vérifier la disponibilité du nom d'utilisateur
function usernameEstDisponible(username, callback) {
    $.getJSON("json/users.json", function(users) {
        let existe = users.some(user => user.username === username);
        callback(!existe); // Appelle le callback avec true si le username est disponible
    });
}

// Vérifier la disponibilité de l'email
function emailEstDisponible(email, callback) {
    $.getJSON("json/users.json", function(users) {
        let existe = users.some(user => user.mail === email);
        callback(!existe); // Appelle le callback avec true si l'email est disponible
    });
}

// Vérifier la validité du mot de passe (min 8 caractères, 1 majuscule, 1 minuscule, 1 caractère spécial)
function verifierMotDePasse(password) {
    var regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_\.]).{8,}$/;
    return regex.test(password); // Retourne true si le mot de passe respecte les conditions
}


function creerCompte() {
    var userData = {
        nom: $("#nomc").val(),
        prenom: $("#prenomc").val(),
        username: $("#usernamec").val(),
        mail: $("#mailc").val(),
        password: $("#passwordc']").val(),
        role: $("select[name='Role']").val()
    };
    $.ajax({
            method: "GET",
            url: "service.php",
            data: userData,
        }).done(function(response) {
            alert("Compte créé avec succès !");
            console.log(response);
        }).fail(function(error) {
            console.log(error);
            alert("Erreur lors de la création du compte.");
        });
    };