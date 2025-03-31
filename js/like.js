$(document).ready(function() {
    // Fonction pour mettre à jour l'apparence du bouton
    function updateLikeButton(button, isLiked, likeCount) {
        if (isLiked) {
            button.addClass('liked');
            button.html(`❤ ${likeCount}`);
        } else {
            button.removeClass('liked');
            button.html(`♡ ${likeCount}`);
        }
    }
    
    // Initialiser les boutons de like
    $('.like-btn').each(function() {
        const button = $(this);
        const recipe = button.data('recipe');
        const isLiked = button.data('liked') === true;  // Utiliser un booléen pour 'liked'
        const likeCount = parseInt(button.data('count') || 0);
        
        updateLikeButton(button, isLiked, likeCount);
    });
    
    // Gérer le clic sur le bouton like
    $('.like-btn').click(function(e) {
        e.preventDefault();
        
        const button = $(this);
        const recipe = button.data('recipe');
        
        // Vérifier si l'utilisateur est connecté
        $.get('check_session.php', function(response) {
            if (response.logged_in) {
                // L'utilisateur est connecté, procéder avec le like
                $.get('like_service.php', { recipe: recipe }, function(data) {
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
                alert('Veuillez vous connecter pour aimer une recette.');
                window.location.href = 'create_login.php';  // Rediriger vers la page de connexion
            }
        });
    });
});
