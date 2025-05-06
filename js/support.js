$(document).ready(function() {
    $('.accept-btn, .reject-btn').click(function() {
        const button = $(this);
        const action = button.hasClass('accept-btn') ? 'accept' : 'reject';
        const username = button.data('username');
        const role = button.data('role');
        const index = button.data('index');

        $.post('support_handler.php', { username: username, role: role, action: action })
            .done(function(response) {
                if (response.success) {
                    alert('Requête traitée avec succès.');
                    $('#row-' + index).remove();
                    if ($('#request-table tr').length === 1) { // uniquement l'en-tête restant
                        $('#request-table').after('<p>Aucune requête en attente.</p>');
                        $('#request-table').remove();
                    }
                } else {
                    alert('Erreur: ' + response.message);
                }
            })
            .fail(function() {
                alert('Erreur de communication avec le serveur.');
            });
    });
});
