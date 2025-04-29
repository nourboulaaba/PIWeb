// Script pour mettre à jour le compteur de notifications
document.addEventListener('DOMContentLoaded', function() {
    // Fonction pour mettre à jour le compteur de notifications
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
    
    // Mettre à jour le compteur toutes les 30 secondes
    setInterval(updateNotificationCount, 30000);
});
