-- Script pour ajouter la colonne related_user_id à la table notification
ALTER TABLE notification ADD COLUMN related_user_id INT DEFAULT NULL;
