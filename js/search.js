$(document).ready(function () {
    // Action déclenchée au clic sur le bouton de recherche
    $('#search-btn').on('click', function () {
        lancerRecherche();
    });

    // Action déclenchée lorsqu'on appuie sur Entrée dans le champ texte
    $('#search-input').on('keypress', function (e) {
        if (e.which === 13) {
            lancerRecherche();
        }
    });

    function lancerRecherche() {
        const query = $('#search-input').val().trim();
        if (query.length === 0) return;

        $.ajax({
            url: 'search_recipes.php',
            method: 'GET',
            data: { q: query },
            success: function (data) {
                $('#recipes-container').html(data);
                if (typeof attachLikeHandlers === 'function') {
                    attachLikeHandlers(); // Recharge les événements de like si définis
                }
            },
            error: function () {
                alert("Erreur lors de la recherche.");
            }
        });
    }
});
