-- CRÉATION DE LA BASE DE DONNÉES : supercar

CREATE DATABASE IF NOT EXISTS supercar
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE supercar;


-- TABLES DE RÉFÉRENCE

CREATE TABLE marque (
    id_marque   INT AUTO_INCREMENT PRIMARY KEY,
    nom_marque  VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE login (
    id_login    INT AUTO_INCREMENT PRIMARY KEY,
    login       VARCHAR(100) NOT NULL UNIQUE,
    password    VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- CÔTÉ CLIENT

CREATE TABLE contact_client (
    id_contact  INT AUTO_INCREMENT PRIMARY KEY,
    nom         VARCHAR(100) NOT NULL,
    prenom      VARCHAR(100) NOT NULL,
    email       VARCHAR(150) NOT NULL,
    message     TEXT NOT NULL,
    date_envoi  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE client (
    id_client   INT AUTO_INCREMENT PRIMARY KEY,
    nom         VARCHAR(100) NOT NULL,
    prenom      VARCHAR(100) NOT NULL,
    email       VARCHAR(150) NOT NULL UNIQUE,
    telephone   VARCHAR(20),
    id_login    INT,
    date_inscription    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_login) REFERENCES login(id_login)
        ON DELETE SET NULL
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE voiture (
    id_voiture      INT AUTO_INCREMENT PRIMARY KEY,
    modele          VARCHAR(100) NOT NULL,
    prix            INT NOT NULL,
    annee           INT NOT NULL,
    etat            VARCHAR(30),
    type            VARCHAR(30),
    kilometrage     INT,
    disponibilite   BOOLEAN DEFAULT TRUE,
    description     TEXT,
    archive         BOOLEAN DEFAULT FALSE,
    image_url       VARCHAR(255),
    id_marque       INT,
    FOREIGN KEY (id_marque) REFERENCES marque(id_marque)
        ON DELETE SET NULL
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE demande_essai (
    id_demande      INT AUTO_INCREMENT PRIMARY KEY,
    date_souhaitee  DATE NOT NULL,
    heure_souhaitee TIME,
    mobile          VARCHAR(20),
    statut          VARCHAR(50) DEFAULT 'en attente',
    id_client       INT NOT NULL,
    id_voiture      INT,
    FOREIGN KEY (id_client) REFERENCES client(id_client)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    FOREIGN KEY (id_voiture) REFERENCES voiture(id_voiture)
        ON DELETE SET NULL
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- CÔTÉ ADMIN

CREATE TABLE admin (
    id_admin     INT AUTO_INCREMENT PRIMARY KEY,
    identifiant  VARCHAR(50) NOT NULL UNIQUE,
    nom          VARCHAR(50) NOT NULL,
    prenom       VARCHAR(50) NOT NULL,
    motdepasse   VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- CONTENUS DYNAMIQUES (éditables via l'admin)

CREATE TABLE home_content (
    element_id  VARCHAR(50) PRIMARY KEY,
    content     TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE services_content (
    element_id  VARCHAR(50) PRIMARY KEY,
    content     TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE essai_content (
    element_id  INT AUTO_INCREMENT PRIMARY KEY,
    content     TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE contact_content (
    element_id  VARCHAR(50) PRIMARY KEY,
    content     TEXT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;