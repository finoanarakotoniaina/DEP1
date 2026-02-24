CREATE TABLE marque_(
   id_marque VARCHAR(50),
   nom_marque VARCHAR(50),
   PRIMARY KEY(id_marque)
);

CREATE TABLE service(
   id_service VARCHAR(50),
   titre VARCHAR(50),
   type VARCHAR(50),
   description VARCHAR(50),
   PRIMARY KEY(id_service)
);

CREATE TABLE login(
   id_login VARCHAR(50),
   statut VARCHAR(50),
   login VARCHAR(50),
   password VARCHAR(50),
   PRIMARY KEY(id_login)
);

CREATE TABLE contact(
   id_contact VARCHAR(50),
   email VARCHAR(50),
   tel VARCHAR(50),
   nom VARCHAR(50),
   prenom VARCHAR(50),
   PRIMARY KEY(id_contact)
);

CREATE TABLE voiture(
   id_voiture VARCHAR(50),
   modele VARCHAR(50),
   prix VARCHAR(50),
   annee_ VARCHAR(50),
   description VARCHAR(50),
   image_url VARCHAR(50),
   id_service VARCHAR(50),
   id_marque VARCHAR(50) NOT NULL,
   PRIMARY KEY(id_voiture),
   FOREIGN KEY(id_service) REFERENCES service(id_service),
   FOREIGN KEY(id_marque) REFERENCES marque_(id_marque)
);

CREATE TABLE client_(
   id_client VARCHAR(50),
   nom VARCHAR(50),
   prenom VARCHAR(50),
   email VARCHAR(50),
   telephone VARCHAR(50),
   id_contact VARCHAR(50) NOT NULL,
   id_login VARCHAR(50),
   PRIMARY KEY(id_client),
   FOREIGN KEY(id_contact) REFERENCES contact(id_contact),
   FOREIGN KEY(id_login) REFERENCES login(id_login)
);

CREATE TABLE demande_essai(
   id_demande VARCHAR(50),
   date_souhaitee DATE,
   statut VARCHAR(50),
   id_client VARCHAR(50) NOT NULL,
   id_voiture VARCHAR(50) NOT NULL,
   PRIMARY KEY(id_demande),
   FOREIGN KEY(id_client) REFERENCES client_(id_client),
   FOREIGN KEY(id_voiture) REFERENCES voiture(id_voiture)
);
