CREATE DATABASE supercar;

USE supercar;

CREATE TABLE marque(
   id_marque INT,
   nom_marque VARCHAR(50),
   PRIMARY KEY(id_marque)
) ENGINE=InnoDB CHARSET=utf8mb4;

CREATE TABLE service(
   id_service INT,
   titre VARCHAR(100),
   type VARCHAR(50),
   description TEXT,
   PRIMARY KEY(id_service)
) ENGINE=InnoDB CHARSET=utf8mb4;

CREATE TABLE login(
   id_login INT,
   statut VARCHAR(50),
   login VARCHAR(50),
   password VARCHAR(255),
   PRIMARY KEY(id_login)
) ENGINE=InnoDB CHARSET=utf8mb4;

CREATE TABLE contact(
   id_contact INT,
   nom VARCHAR(100),
   prenom VARCHAR(100),
   email VARCHAR(100),
   message TEXT,
   PRIMARY KEY(id_contact)
) ENGINE=InnoDB CHARSET=utf8mb4;

CREATE TABLE voiture(
   id_voiture INT,
   modele VARCHAR(50),
   prix INT,
   annee INT,
   description TEXT,
   image_url VARCHAR(200),
   id_service INT,
   id_marque INT,
   PRIMARY KEY(id_voiture),
   FOREIGN KEY(id_service) REFERENCES service(id_service),
   FOREIGN KEY(id_marque) REFERENCES marque(id_marque)
) ENGINE=InnoDB CHARSET=utf8mb4;

CREATE TABLE client(
   id_client INT,
   nom VARCHAR(100),
   prenom VARCHAR(100),
   email VARCHAR(100),
   telephone INT,
   id_contact INT,
   id_login INT,
   PRIMARY KEY(id_client),
   FOREIGN KEY(id_contact) REFERENCES contact(id_contact),
   FOREIGN KEY(id_login) REFERENCES login(id_login)
) ENGINE=InnoDB CHARSET=utf8mb4;

CREATE TABLE demande_essai(
   id_demande INT,
   date_souhaitee DATE,
   statut VARCHAR(50),
   id_client INT,
   id_voiture INT,
   PRIMARY KEY(id_demande),
   FOREIGN KEY(id_client) REFERENCES client(id_client),
   FOREIGN KEY(id_voiture) REFERENCES voiture(id_voiture)
) ENGINE=InnoDB CHARSET=utf8mb4;