// Script très simple pour le menu déroulant personnalisé
document.addEventListener('DOMContentLoaded', function() {
    // Sélectionner les éléments
    const dropdownToggle = document.getElementById('customNotificationToggle');
    const dropdown = document.getElementById('customNotificationDropdown');
    
    if (dropdownToggle && dropdown) {
        // Fonction pour basculer l'état du menu
        function toggleDropdown(event) {
            event.preventDefault();
            event.stopPropagation();
            dropdown.classList.toggle('open');
        }
        
        // Fonction pour fermer le menu si on clique en dehors
        function closeDropdownOnClickOutside(event) {
            if (!dropdown.contains(event.target)) {
                dropdown.classList.remove('open');
            }
        }
        
        // Ajouter l'écouteur d'événement au bouton
        dropdownToggle.addEventListener('click', toggleDropdown);
        
        // Ajouter l'écouteur d'événement au document
        document.addEventListener('click', closeDropdownOnClickOutside);
    }
    
    // Mettre à jour le compteur de notifications
    function updateNotificationCount() {
        fetch('/notifications/count')
            .then(response => response.json())
            .then(data => {
                const badge = document.querySelector('.custom-notification-badge');
                if (badge) {
                    if (data.count > 0) {
                        badge.textContent = data.count;
                        badge.style.display = 'block';
                    } else {
                        badge.style.display = 'none';
                    }
                }
            })
            .catch(error => console.error('Erreur:', error));
    }
    
    // Mettre à jour le compteur au chargement
    updateNotificationCount();
    
    // Mettre à jour le compteur toutes les 30 secondes
    setInterval(updateNotificationCount, 30000);
});
