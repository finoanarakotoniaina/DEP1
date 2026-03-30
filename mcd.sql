CREATE DATABASE supercar;

USE supercar;

CREATE TABLE marque(
   id_marque INT AUTO_INCREMENT,
   nom_marque VARCHAR(50),
   PRIMARY KEY(id_marque)
) ENGINE=InnoDB CHARSET=utf8mb4;

CREATE TABLE service(
   id_service INT AUTO_INCREMENT,
   titre VARCHAR(100),
   type VARCHAR(50),
   description TEXT,
   PRIMARY KEY(id_service)
) ENGINE=InnoDB CHARSET=utf8mb4;

CREATE TABLE login(
   id_login INT AUTO_INCREMENT,
   login VARCHAR(50),
   password VARCHAR(255),
   PRIMARY KEY(id_login)
) ENGINE=InnoDB CHARSET=utf8mb4;

CREATE TABLE contact(
   id_contact INT AUTO_INCREMENT,
   nom VARCHAR(100),
   prenom VARCHAR(100),
   email VARCHAR(100),
   message TEXT,
   PRIMARY KEY(id_contact)
) ENGINE=InnoDB CHARSET=utf8mb4;

CREATE TABLE voiture(
   id_voiture INT AUTO_INCREMENT,
   modele VARCHAR(50),
   prix INT,
   annee INT,
   etat VARCHAR (25),
   type VARCHAR (25),
   kilometrage INT,
   disponibilite BOOLEAN DEFAULT TRUE,
   description TEXT,
   image_url VARCHAR(200),
   id_service INT,
   id_marque INT,
   PRIMARY KEY(id_voiture),
   FOREIGN KEY(id_service) REFERENCES service(id_service),
   FOREIGN KEY(id_marque) REFERENCES marque(id_marque)
) ENGINE=InnoDB CHARSET=utf8mb4;

CREATE TABLE client(
   id_client INT AUTO_INCREMENT,
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
   id_demande INT AUTO_INCREMENT,
   date_souhaitee DATE,
   statut VARCHAR(50),
   id_client INT,
   id_voiture INT,
   PRIMARY KEY(id_demande),
   FOREIGN KEY(id_client) REFERENCES client(id_client),
   FOREIGN KEY(id_voiture) REFERENCES voiture(id_voiture)
) ENGINE=InnoDB CHARSET=utf8mb4;


-- insertion des marques
INSERT INTO marque (nom_marque) VALUES
('Porsche'), ('BMW'), ('Mercedes');




-- insertion des voitures --
INSERT INTO voiture (modele, prix, annee, description, image_url, id_service, id_marque) VALUES

-- Porsche, BMW, Mercedes --
('Porsche 928', 45000, 1982, 'Grand Tourisme à moteur V8 avant...', 'Porsche_928.jfif', NULL, 1),
('Porsche 912', 65000, 1965, 'Variante plus légère de la 911...', 'Porsche_912.jfif', NULL, 1),
('Porsche 930 (911 Turbo)', 180000, 1975, 'La première 911 Turbo de série...', 'Porsche_930.jfif', NULL, 1),
('Porsche 550 Spyder', 4500000, 1953, 'Légende de la course automobile...', 'Porsche_550_Spyder.jfif', NULL, 1),
('Porsche 911 Carrera RS 2.7', 600000, 1973, 'Icône absolue avec son aileron...', 'Porsche_911.jfif', NULL, 1),
('Porsche 356 Speedster', 350000, 1955, 'Le cabriolet puriste par excellence...', 'Porsche_356.jfif', NULL, 1),

('M3 E30', 85000, 1989, 'Légende du DTM en rouge brillant, équipée de jantes BBS classiques.', 'bmwE30.jpg', NULL, 2),
('Série 3 E46', 12000, 2003, 'Berline élégante et intemporelle, l\'équilibre parfait entre confort et sportivité.', 'bmwE46.jpg', NULL, 2),
('Série 6 E63', 25000, 2007, 'Grand coupé de luxe au design distinctif, idéal pour les longs trajets.', 'bmwE63.jpg', NULL, 2),
('X5 E53', 15000, 2004, 'Le premier SUV de la marque, ici en version phase 2 avec un look robuste.', 'bmwX5.jpg', NULL, 2),
('Z1', 60000, 1990, 'Cabriolet futuriste célèbre pour ses portières escamotables verticalement.', 'bmwZ1.jpg', NULL, 2),
('Z3 Coupé', 45000, 1999, 'Le fameux \"Shooting Brake\" au design audacieux et aux ailes larges.', 'bmwZ3.jpg', NULL, 2),

('Mercedes-Benz S 500 (W140)', 15000, 1997, 'La \"Cathédrale\", symbole du luxe absolu des années 90, moteur V8.', 'Mercedes-Benz_S_500_(W140).jfif', NULL, 3),
('Mercedes-Benz 280 SL Pagode', 120000, 1968, 'Icône d\'élégance avec son toit rigide concave caractéristique.', 'Mercedes-Benz_280_SL_Pagode_(W113).jfif', NULL, 3),
('Mercedes-Benz 450 SLC (C107)', 35000, 1978, 'Coupé de luxe vintage, parfait mélange de confort et de style classique.', 'Mercedes-Benz_280_SE_3.5_Coupé_(W111).png', NULL, 3),
('Mercedes-Benz 300 SL Gullwing', 1500000, 1955, 'Légende mondiale avec ses portières papillon et son moteur mythique.', 'Mercedes-Benz_300_SL_Gullwing_(W198).jfif', NULL, 3),
('Mercedes-Benz S 350 (W221)', 12000, 2007, 'Berline de luxe moderne offrant un confort et une technologie de pointe.', 'Mercedes-Benz_S_350_(W221).jfif', NULL, 3),
('Mercedes-Benz 190 SL Roadster', 110000, 1960, 'Roadster classique décapotable, petit frère de la 300 SL.', 'Mercedes-Benz_190_SL_(W121).jfif', NULL, 3);