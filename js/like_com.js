$(document).ready(function() {
    // Fonction pour mettre à jour l'apparence du bouton de like
    function updateLikeButton(button, isLiked, likeCount) {
        if (isLiked) {
            button.addClass('liked');
            button.html(`❤ ${likeCount}`);
        } else {
            button.removeClass('liked');
            button.html(`♡ ${likeCount}`);
        }
    }

    // Initialiser les boutons de like pour les commentaires
    $('.like-btn').each(function() {
        const button = $(this);
        const commentId = button.data('comment-id');  // ID du commentaire
        const isLiked = button.data('liked') === true;  // Utiliser un booléen pour savoir si c'est aimé
        const likeCount = parseInt(button.data('count') || 0);  // Nombre de likes

        updateLikeButton(button, isLiked, likeCount);
    });

    // Gérer le clic sur le bouton de like pour un commentaire
    $('.like-btn').click(function(e) {
        e.preventDefault();

        const button = $(this);
        const commentId = button.data('comment-id');  // ID du commentaire
        
        // Vérifier si l'utilisateur est connecté
        $.get('check_session.php', function(response) {
            if (response.logged_in) {
                // L'utilisateur est connecté, procéder avec le like
                $.get('like_comment_service.php', { commentId: commentId }, function(data) {
                    if (data.success) {
                        updateLikeButton(button, data.action === 'like', data.likes);
                        button.data('liked', data.action === 'like' ? true : false);
                        button.data('count', data.likes);
                    } else {
                        alert(data.error || 'Une erreur est survenue.');
                    }
                }).fail(function() {
                    alert('Erreur de communication avec le serveur.');
                });
            } else {
                // L'utilisateur n'est pas connecté, rediriger vers la page de connexion
                alert('Veuillez vous connecter pour aimer un commentaire.');
                window.location.href = 'create_login.php';  // Rediriger vers la page de connexion
            }
        });
    });
});
