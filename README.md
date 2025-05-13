# PIDEV - Application de Gestion RH

> Projet académique développé en équipe de 6 personnes dans le cadre de la formation à l’Esprit School of Engineering (Année 2024-2025).

## 📑 Description

PIDEV est une application web de gestion des ressources humaines permettant de gérer les utilisateurs, emplois, recrutements, services, formations, missions et contrats. Chaque membre de l'équipe développe un module indépendant avec des fonctionnalités CRUD, des APIs REST et des traitements métier avancés.

---

## 🛠️ Technologies

- **Backend** : Symfony 6.4 (PHP)
- **Services** : Java 11+
- **Base de données** : MySQL / PostgreSQL
- **Frontend** : Twig, JavaScript, AJAX
- **Outils** : Composer, Maven, Git, Postman

---

## 🗂️ Structure du projet

```
/src
  /UserBundle
  /JobBundle
  /RecruitmentBundle
  /ServiceBundle
  /TrainingBundle
  /ContractBundle
/public
/config
/tests
README.md
```

---

## 📦 Modules et Fonctionnalités

### 👤 Gestion Utilisateur
- CRUD complet
- API : Auth Google, reset password avec captcha, mailing après connexion
- Métier avancé : reconnaissance faciale (Face ID), recherche, filtres, export XML des employés

### 💼 Gestion Emploi
- CRUD + API Job List
- Génération de QR Code
- Mailing automatique
- Recherche avancée, statistiques, filtres, export PDF, pagination

### 🧠 Gestion Recrutement
- CRUD + API ATS
- Decision maker
- Envoi de SMS
- Recherche avancée, statistiques, filtres, export PDF, pagination

### 🏢 Gestion Service
- CRUD + API Gemini + mailing
- Calcul du solde de congé
- Recherche par mot-clé, tri, pagination
- Intégration d’un calendrier

### 🎓 Gestion Formation
- API de paiement en ligne
- Bundle d’avis/notation des formations
- Téléchargement de certificats en PDF
- Recherche, tri, pagination

### 📄 Missions & Contrats
- API mailing + génération PDF
- Recherche et tri par critère

---

## 🚀 Installation

1. **Cloner le projet**
```bash
git clone https://github.com/votre-utilisateur/projet-pidev.git
cd projet-pidev
```

2. **Installer Symfony**
```bash
composer install
```

3. **Configurer la base de données**
```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

4. **Démarrer le serveur Symfony**
```bash
symfony serve
```

5. **Compiler et démarrer les services Java**
```bash
cd services
mvn clean install
```

---

## 📬 Utilisation

- Accès local : `http://localhost:8000`
- Authentification Google : `/login/google`
- API disponibles : `/api/users`, `/api/jobs`, `/api/recruitment`, etc.

---

## 🤝 Contribution

1. Forkez le dépôt
2. Créez une branche : `feature/ma-fonctionnalité`
3. Commitez vos changements : `git commit -m "Ajout : nouvelle fonctionnalité"`
4. Pushez : `git push origin feature/ma-fonctionnalité`
5. Créez une Pull Request

---

## 📄 Licence

Ce projet est sous licence MIT. Voir le fichier [LICENSE](LICENSE) pour plus de détails.

---

## 👥 Équipe

- 👤 Membre 1 – Gestion Utilisateur
- 👤 Membre 2 – Gestion Emploi
- 👤 Membre 3 – Gestion Recrutement
- 👤 Membre 4 – Gestion Service
- 👤 Membre 5 – Gestion Formation
- 👤 Membre 6 – Missions & Contrats
