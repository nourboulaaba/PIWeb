// Script spécifique pour gérer le dropdown des notifications
document.addEventListener('DOMContentLoaded', function() {
    // Sélectionner les éléments
    const notificationToggle = document.getElementById('notificationsDropdown');
    const notificationMenu = document.querySelector('.notification-dropdown-menu');
    
    if (notificationToggle && notificationMenu) {
        // Fonction pour afficher/masquer le menu
        function toggleNotificationMenu(event) {
            event.preventDefault();
            event.stopPropagation();
            
            // Toggle la classe 'show' sur le menu
            notificationMenu.classList.toggle('show');
            
            // Mettre à jour l'attribut aria-expanded
            const isExpanded = notificationMenu.classList.contains('show');
            notificationToggle.setAttribute('aria-expanded', isExpanded);
            
            // Si le menu est ouvert, ajouter un écouteur de clic sur le document
            if (isExpanded) {
                setTimeout(function() {
                    document.addEventListener('click', closeMenuOnClickOutside);
                }, 10);
            } else {
                document.removeEventListener('click', closeMenuOnClickOutside);
            }
        }
        
        // Fonction pour fermer le menu si on clique en dehors
        function closeMenuOnClickOutside(event) {
            if (!notificationMenu.contains(event.target) && !notificationToggle.contains(event.target)) {
                notificationMenu.classList.remove('show');
                notificationToggle.setAttribute('aria-expanded', 'false');
                document.removeEventListener('click', closeMenuOnClickOutside);
            }
        }
        
        // Ajouter l'écouteur d'événement au bouton
        notificationToggle.addEventListener('click', toggleNotificationMenu);
        
        // Empêcher la fermeture du menu lorsqu'on clique à l'intérieur
        notificationMenu.addEventListener('click', function(event) {
            // Ne pas propager le clic sauf pour les boutons de marquage comme lu
            if (!event.target.classList.contains('mark-read-btn')) {
                event.stopPropagation();
            }
        });
    }
    
    // Mettre à jour le compteur de notifications
    function updateNotificationCount() {
        fetch('/notifications/count')
            .then(response => response.json())
            .then(data => {
                const badge = document.querySelector('.notification-badge');
                if (badge) {
                    if (data.count > 0) {
                        badge.textContent = data.count;
                        badge.style.display = 'inline-block';
                    } else {
                        badge.style.display = 'none';
                    }
                }
            })
            .catch(error => console.error('Erreur:', error));
    }
    
    // Mettre à jour le compteur au chargement
    updateNotificationCount();
});
