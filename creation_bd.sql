-- Création de la base
CREATE DATABASE supercar;
USE supercar;

-- Tables de references
CREATE TABLE marque(
   id_marque INT AUTO_INCREMENT PRIMARY KEY,
   nom_marque VARCHAR(50)
) ENGINE=InnoDB CHARSET=utf8mb4;

CREATE TABLE service(
   id_service INT AUTO_INCREMENT PRIMARY KEY,
   titre VARCHAR(100),
   type VARCHAR(50),
   description TEXT
) ENGINE=InnoDB CHARSET=utf8mb4;

CREATE TABLE login(
   id_login INT AUTO_INCREMENT PRIMARY KEY,
   login VARCHAR(50),
   password VARCHAR(255)
) ENGINE=InnoDB CHARSET=utf8mb4;



-- CÔTÉ CLIENT
CREATE TABLE contact(
   id_contact INT AUTO_INCREMENT PRIMARY KEY,
   nom VARCHAR(100),
   prenom VARCHAR(100),
   email VARCHAR(100),
   message TEXT
) ENGINE=InnoDB CHARSET=utf8mb4;

CREATE TABLE client(
   id_client INT AUTO_INCREMENT PRIMARY KEY,
   nom VARCHAR(100),
   prenom VARCHAR(100),
   email VARCHAR(100),
   telephone VARCHAR(20),
   id_contact INT,
   id_login INT,
   FOREIGN KEY(id_contact) REFERENCES contact(id_contact),
   FOREIGN KEY(id_login) REFERENCES login(id_login)
) ENGINE=InnoDB CHARSET=utf8mb4;

CREATE TABLE voiture(
   id_voiture INT AUTO_INCREMENT PRIMARY KEY,
   modele VARCHAR(50),
   prix INT,
   annee INT,
   etat VARCHAR(25),
   type VARCHAR(25),
   kilometrage INT,
   disponibilite BOOLEAN DEFAULT TRUE,
   description TEXT,
   image_url VARCHAR(200),
   id_service INT,
   id_marque INT,
   FOREIGN KEY(id_service) REFERENCES service(id_service),
   FOREIGN KEY(id_marque) REFERENCES marque(id_marque)
) ENGINE=InnoDB CHARSET=utf8mb4;

CREATE TABLE demande_essai(
   id_demande INT AUTO_INCREMENT PRIMARY KEY,
   date_souhaitee DATE,
   heure_souhaitee TIME,
   mobile VARCHAR(20),
   statut VARCHAR(50) DEFAULT 'en attente',
   id_client INT,
   id_voiture INT,
   FOREIGN KEY(id_client) REFERENCES client(id_client),
   FOREIGN KEY(id_voiture) REFERENCES voiture(id_voiture)
) ENGINE=InnoDB CHARSET=utf8mb4;



-- CÔTÉ ADMIN
CREATE TABLE admin(
   id_admin INT AUTO_INCREMENT PRIMARY KEY,
   identifiant VARCHAR(50) NOT NULL UNIQUE,
   nom VARCHAR(50) NOT NULL,
   prenom VARCHAR(50) NOT NULL,
   motdepasse VARCHAR(255) NOT NULL
) ENGINE=InnoDB CHARSET=utf8mb4;

CREATE TABLE home_content (
  element_id VARCHAR(50) PRIMARY KEY,
  content TEXT
) ENGINE=InnoDB CHARSET=utf8mb4;

CREATE TABLE services_content (
  element_id VARCHAR(50) PRIMARY KEY,
  content TEXT
) ENGINE=InnoDB CHARSET=utf8mb4;

CREATE TABLE essai_content (
    id INT AUTO_INCREMENT PRIMARY KEY,
    contenu TEXT
) ENGINE=InnoDB CHARSET=utf8mb4;

