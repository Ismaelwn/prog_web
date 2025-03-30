$(document).ready(function(){
    $(".user-menu").click(function(event){
        event.stopPropagation(); // Empêche la fermeture immédiate
        $(".dropdown-menu").toggle(); // Afficher / cacher le menu
    });

    $(document).click(function(event) {
        if (!$(event.target).closest('.user-menu').length) {
            $(".dropdown-menu").hide();  // Ferme le menu si on clique ailleurs
        }
    });
});
