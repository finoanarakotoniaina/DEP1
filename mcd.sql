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



-- insertion voiture --
INSERT INTO voiture (modele, prix, annee, etat, type, kilometrage, disponibilite, description, image_url, id_service, id_marque) VALUES

-- Porsche --
('Porsche 928', 45000, 1982, 'Bon', 'thermique', 130000, true, 'Grand Tourisme à moteur V8 avant, un classique du design transaxle.', 'Porsche_928.jfif', NULL, 1),
('Porsche 912', 65000, 1965, 'Excellent', 'thermique', 85000, true, 'Variante plus légère de la 911 équipée du moteur 4 cylindres de la 356.', 'Porsche_912.jfif', NULL, 1),
('Porsche 930 (911 Turbo)', 180000, 1975, 'Collection', 'thermique', 45000, true, 'La première 911 Turbo de série, une bête brute de puissance.', 'Porsche_930.jfif', NULL, 1),
('Porsche 550 Spyder', 4500000, 1953, 'Concours', 'thermique', 12000, true, 'Légende de la course automobile, célèbre pour sa légèreté extrême.', 'Porsche_550_Spyder.jfif', NULL, 1),
('Porsche 911 Carrera RS 2.7', 600000, 1973, 'Exceptionnel', 'thermique', 55000, true, 'Icône absolue avec son aileron queue de canard emblématique.', 'Porsche_911.jfif', NULL, 1),
('Porsche 356 Speedster', 350000, 1955, 'Restauré', 'thermique', 32000, true, 'Le cabriolet puriste par excellence, symbole de la naissance de la marque.', 'Porsche_356.jfif', NULL, 1),

-- BMW --
('M3 E30', 85000, 1989, 'Très Bon', 'thermique', 115000, true, 'Légende du DTM en rouge brillant, équipée de jantes BBS classiques.', 'bmwE30.jpg', NULL, 2),
('Série 3 E46', 12000, 2003, 'Bon', 'thermique', 185000, true, 'Berline élégante et intemporelle, l\'équilibre parfait entre confort et sportivité.', 'bmwE46.jpg', NULL, 2),
('Série 6 E63', 25000, 2007, 'Bon', 'thermique', 140000, true, 'Grand coupé de luxe au design distinctif, idéal pour les longs trajets.', 'bmwE63.jpg', NULL, 2),
('X5 E53', 15000, 2004, 'Moyen', 'thermique', 220000, true, 'Le premier SUV de la marque, ici en version phase 2 avec un look robuste.', 'bmwX5.jpg', NULL, 2),
('Z1', 60000, 1990, 'Excellent', 'thermique', 65000, true, 'Cabriolet futuriste célèbre pour ses portières escamotables verticalement.', 'bmwZ1.jpg', NULL, 2),
('Z3 Coupé', 45000, 1999, 'Très Bon', 'thermique', 95000, true, 'Le fameux "Shooting Brake" au design audacieux et aux ailes larges.', 'bmwZ3.jpg', NULL, 2),

-- Mercedes-Benz --
('Mercedes-Benz S 500 (W140)', 15000, 1997, 'Bon', 'thermique', 190000, true, 'La "Cathédrale", symbole du luxe absolu des années 90, moteur V8.', 'S500(W140).jpeg', NULL, 3),
('Mercedes-Benz 280 SL Pagode', 120000, 1968, 'Collection', 'thermique', 78000, true, 'Icône d\'élégance avec son toit rigide concave caractéristique.', '280SLPagode(W113).webp', NULL, 3),
('Mercedes-Benz 450 SLC (C107)', 35000, 1978, 'Bon', 'thermique', 155000, true, 'Coupé de luxe vintage, parfait mélange de confort et de style classique.', '280SE3.5Coupé(W111).jpeg', NULL, 3),
('Mercedes-Benz 300 SL Gullwing', 1500000, 1955, 'Exceptionnel', 'thermique', 25000, true, 'Légende mondiale avec ses portières papillon et son moteur mythique.', '300SLGullwing(W198).jpeg', NULL, 3),
('Mercedes-Benz S 350 (W221)', 12000, 2007, 'Bon', 'thermique', 210000, true, 'Berline de luxe moderne offrant un confort et une technologie de pointe.', 'S350(W221).jpg', NULL, 3),
('Mercedes-Benz 190 SL Roadster', 110000, 1960, 'Restauré', 'thermique', 82000, true, 'Roadster classique décapotable, petit frère de la 300 SL.', '190SL(W121).jpg', NULL, 3);

