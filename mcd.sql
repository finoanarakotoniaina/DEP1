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
('Porsche'), ('BMW'), ('Mercedes'), ('Audi');



-- insertion voiture --
INSERT INTO voiture (modele, prix, annee, etat, type, kilometrage, disponibilite, description, image_url, id_service, id_marque) VALUES

-- Porsche --
('Porsche 928', 450000, 1982, 'Bon', 'thermique', 145000, true, 'Grand Tourisme à moteur V8 avant.', 'Porsche_928.jfif', NULL, 1),
('Porsche 912', 550000, 1965, 'Excellent', 'thermique', 92000, true, 'Variante plus légère de la 911.', 'Porsche_912.jfif', NULL, 1),
('Porsche 930 (911 Turbo)', 850000, 1975, 'Collection', 'thermique', 68000, true, 'La première 911 Turbo de série.', 'Porsche_930.jfif', NULL, 1),
('Porsche 550 Spyder', 1000000, 1953, 'Concours', 'thermique', 15000, true, 'Légende de la course automobile (Réplique ou prestige).', 'Porsche_550_Spyder.jfif', NULL, 1),
('Porsche 911 Carrera RS 2.7', 950000, 1973, 'Exceptionnel', 'thermique', 52000, true, 'Icône absolue avec son aileron queue de canard.', 'Porsche_911.jfif', NULL, 1),
('Porsche 356 Speedster', 750000, 1955, 'Restauré', 'thermique', 41000, true, 'Le cabriolet puriste par excellence.', 'Porsche_356.jfif', NULL, 1),

-- BMW --
('M3 E30', 650000, 1989, 'Très Bon', 'thermique', 112000, true, 'Légende du DTM en rouge brillant.', 'bmwE30.jpg', NULL, 2),
('Série 3 E46', 220000, 2003, 'Bon', 'thermique', 178000, true, 'Berline élégante et intemporelle.', 'bmwE46.jpg', NULL, 2),
('Série 6 E63', 380000, 2007, 'Très Bon', 'thermique', 135000, true, 'Grand coupé de luxe au design distinctif.', 'bmwE63.jpg', NULL, 2),
('X5 E53', 280000, 2004, 'Moyen', 'thermique', 215000, true, 'Le premier SUV de la marque.', 'bmwX5.jpg', NULL, 2),
('Z1', 520000, 1990, 'Excellent', 'thermique', 54000, true, 'Cabriolet futuriste aux portes escamotables.', 'bmwZ1.jpg', NULL, 2),
('Z3 Coupé', 420000, 1999, 'Bon', 'thermique', 105000, true, 'Le fameux "Shooting Brake".', 'bmwZ3.jpg', NULL, 2),

-- Mercedes-Benz --
('Mercedes-Benz S 500 (W140)', 350000, 1997, 'Excellent', 'thermique', 155000, true, 'La "Cathédrale", symbole du luxe absolu.', 'S500(W140).jpeg', NULL, 3),
('Mercedes-Benz 280 SL Pagode', 880000, 1968, 'Collection', 'thermique', 82000, true, 'Icône d\'élégance avec son toit rigide concave.', '280SLPagode(W113).webp', NULL, 3),
('Mercedes-Benz 450 SLC (C107)', 450000, 1978, 'Bon', 'thermique', 142000, true, 'Coupé de luxe vintage.', '280SE3.5Coupé(W111).jpeg', NULL, 3),
('Mercedes-Benz 300 SL Gullwing', 1000000, 1955, 'Exceptionnel', 'thermique', 32000, true, 'Légende mondiale avec ses portières papillon.', '300SLGullwing(W198).jpeg', NULL, 3),
('Mercedes-Benz S 350 (W221)', 320000, 2007, 'Bon', 'thermique', 198000, true, 'Berline de luxe moderne offrant un grand confort.', 'S350(W221).jpg', NULL, 3),
('Mercedes-Benz 190 SL Roadster', 780000, 1960, 'Bon', 'thermique', 76000, true, 'Roadster classique décapotable.', '190SL(W121).jpg', NULL, 3);

-- Audi --
('Audi A3 Sportback', 350000, 2011, 'Très Bon', 'thermique', 120000, 1, 'Compacte premium polyvalente et élégante.', 'A3(8P).jpg', NULL, 4),
('Audi A4 B8', 480000, 2013, 'Excellent', 'thermique', 95000, 1, 'Berline confortable au design intemporel.', 'A4(B8).jpg', NULL, 4),
('Audi Q3', 750000, 2015, 'Bon', 'thermique', 88000, 1, 'SUV compact idéal pour la ville et la route.', 'Q3.jpg', NULL, 4),
('Audi TT 8J', 550000, 2009, 'Très Bon', 'thermique', 110000, 1, 'Coupé sportif iconique au design épuré.', 'AUDI_TT(8J).jpg', NULL, 4),
('Audi A6 C7', 620000, 2014, 'Bon', 'thermique', 140000, 1, 'Grande berline de luxe avec finitions soignées.', 'A6(C7).jpg', NULL, 4),
('Audi Q5', 580000, 2012, 'Bon', 'thermique', 155000, 1, 'SUV familial robuste et performant.', 'AUDI_Q5.jpg', NULL, 4);