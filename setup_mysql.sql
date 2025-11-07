-- Script de configuration MySQL pour l'API F1
-- À exécuter avec: sudo mysql < setup_mysql.sql

-- Créer la base de données
CREATE DATABASE IF NOT EXISTS f1_api CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Créer l'utilisateur applicatif
CREATE USER IF NOT EXISTS 'symfony'@'localhost' IDENTIFIED BY 'S3cretP@ss!';
CREATE USER IF NOT EXISTS 'symfony'@'127.0.0.1' IDENTIFIED BY 'S3cretP@ss!';

-- Donner les droits sur la DB
GRANT ALL PRIVILEGES ON f1_api.* TO 'symfony'@'localhost';
GRANT ALL PRIVILEGES ON f1_api.* TO 'symfony'@'127.0.0.1';

-- Forcer le plugin d'authentification classique (pour MySQL 8+)
ALTER USER 'symfony'@'localhost' IDENTIFIED WITH mysql_native_password BY 'S3cretP@ss!';
ALTER USER 'symfony'@'127.0.0.1' IDENTIFIED WITH mysql_native_password BY 'S3cretP@ss!';

FLUSH PRIVILEGES;

-- Afficher confirmation
SELECT 'Configuration MySQL terminée !' AS status;
