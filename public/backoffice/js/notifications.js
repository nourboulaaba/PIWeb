// Gestion des notifications
document.addEventListener("DOMContentLoaded", function () {
  // Initialiser Bootstrap Dropdowns
  var dropdownElementList = [].slice.call(
    document.querySelectorAll(".dropdown-toggle")
  );
  var dropdownList = dropdownElementList.map(function (dropdownToggleEl) {
    return new bootstrap.Dropdown(dropdownToggleEl);
  });

  // Forcer l'initialisation du dropdown de notifications
  var notificationsDropdown = document.getElementById("notificationsDropdown");
  if (notificationsDropdown) {
    new bootstrap.Dropdown(notificationsDropdown);
  }

  // Fonction pour marquer une notification comme lue
  function markAsRead(id, button) {
    fetch("/notifications/mark-as-read/" + id, {
      method: "POST",
      headers: {
        "X-Requested-With": "XMLHttpRequest",
      },
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          // Mettre à jour l'interface
          if (button) {
            button.disabled = true;
          }
          const listItem = document.querySelector(
            '.notification-list .list-group-item[data-id="' + id + '"]'
          );
          if (listItem) {
            listItem.classList.remove("unread");
          }

          // Mettre à jour le compteur de notifications
          updateNotificationCount();
        }
      })
      .catch((error) => console.error("Erreur:", error));
  }

  // Fonction pour marquer toutes les notifications comme lues
  function markAllAsRead() {
    fetch("/notifications/mark-all-as-read", {
      method: "POST",
      headers: {
        "X-Requested-With": "XMLHttpRequest",
      },
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          // Mettre à jour l'interface
          document.querySelectorAll(".mark-read-btn").forEach((btn) => {
            btn.disabled = true;
          });
          document
            .querySelectorAll(".notification-list .list-group-item")
            .forEach((item) => {
              item.classList.remove("unread");
            });

          // Mettre à jour le compteur de notifications
          updateNotificationCount();
        }
      })
      .catch((error) => console.error("Erreur:", error));
  }

  // Fonction pour mettre à jour le compteur de notifications
  function updateNotificationCount() {
    fetch("/notifications/count")
      .then((response) => response.json())
      .then((data) => {
        const badge = document.querySelector(".notification-badge");
        if (badge) {
          if (data.count > 0) {
            badge.textContent = data.count;
            badge.style.display = "inline-block";
          } else {
            badge.style.display = "none";
          }
        }
      })
      .catch((error) => console.error("Erreur:", error));
  }

  // Ajouter les écouteurs d'événements
  document.querySelectorAll(".mark-read-btn").forEach((button) => {
    button.addEventListener("click", function (e) {
      e.preventDefault();
      const id = this.getAttribute("data-id");
      markAsRead(id, this);
    });
  });

  const markAllReadBtn = document.getElementById("mark-all-read");
  if (markAllReadBtn) {
    markAllReadBtn.addEventListener("click", function (e) {
      e.preventDefault();
      markAllAsRead();
    });
  }

  // Mettre à jour le compteur de notifications au chargement de la page
  updateNotificationCount();

  // Ajouter un gestionnaire d'événements pour le clic sur l'icône de notification
  const notificationsDropdown = document.getElementById(
    "notificationsDropdown"
  );
  if (notificationsDropdown) {
    notificationsDropdown.addEventListener("click", function (e) {
      // Créer manuellement un événement de clic pour ouvrir le dropdown
      const dropdownInstance = bootstrap.Dropdown.getInstance(
        notificationsDropdown
      );
      if (dropdownInstance) {
        dropdownInstance.toggle();
      } else {
        new bootstrap.Dropdown(notificationsDropdown).toggle();
      }
    });
  }
});
