
    $(document).ready(function () {
        $('#search-btn').on('click', function () {
            let query = $('#search-input').val().trim();
            if (query.length === 0) return;
           
            $.ajax({
                url: 'search_recipes.php',
                method: 'GET',
                data: { q: query },
                success: function (data) {
                    $('#recipes-container').html(data);
                    if (typeof attachLikeHandlers === 'function') {
                        attachLikeHandlers();
                    }
                },
                error: function () {
                    alert("Erreur lors de la recherche.");
                }
            });
        });
    });
