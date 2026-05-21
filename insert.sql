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
('Mercedes-Benz 190 SL Roadster', 780000, 1960, 'Bon', 'thermique', 76000, true, 'Roadster classique décapotable.', '190SL(W121).jpg', NULL, 3),
-- Audi --
('Audi A3 Sportback', 350000, 2011, 'Très Bon', 'thermique', 120000, 1, 'Compacte premium polyvalente et élégante.', 'A3(8P).jpg', NULL, 4),
('Audi A4 B8', 480000, 2013, 'Excellent', 'thermique', 95000, 1, 'Berline confortable au design intemporel.', 'A4(B8).jpg', NULL, 4),
('Audi Q3', 750000, 2015, 'Bon', 'thermique', 88000, 1, 'SUV compact idéal pour la ville et la route.', 'Q3.jpg', NULL, 4),
('Audi TT 8J', 550000, 2009, 'Très Bon', 'thermique', 110000, 1, 'Coupé sportif iconique au design épuré.', 'AUDI_TT(8J).jpg', NULL, 4),
('Audi A6 C7', 620000, 2014, 'Bon', 'thermique', 140000, 1, 'Grande berline de luxe avec finitions soignées.', 'A6(C7).jpg', NULL, 4),
('Audi Q5', 580000, 2012, 'Bon', 'thermique', 155000, 1, 'SUV familial robuste et performant.', 'AUDI_Q5.jpg', NULL, 4);




-- COTE ADMIN

-- compte admin
INSERT INTO admin (identifiant, nom, prenom, motdepasse) 
VALUES ('MadisonCarter', 'Carter', 'Madison', 'didison');

-- contenu accueil 
INSERT INTO table_contenu (id, contenu) VALUES 
('brand_1_name', 'BMW'),
('brand_1_desc', 'Bien que la marque se soit diversifiee.'),
('brand_2_name', 'MERCEDES-BENZ'),
('brand_2_desc', 'L excellence et l innovation automobile allemande depuis des generations.'),
('brand_3_name', 'AUDI'),
('brand_3_desc', 'Le confort haut de gamme allie a la technologie de pointe Vorsprung durch Technik.'),
('brand_4_name', 'PORSCHE'),
('brand_4_desc', 'La performance pure et le design intemporel des voitures de sport.');


-- contenu essai
INSERT INTO essai_content (id, contenu) VALUES 
(1, 'EXPÉRIENCE ULTIME'),
(2, 'DEMANDE'),
(3, 'D\'ESSAI.'),
(4, 'Prenez les commandes de l\'exceptionnel. Choisissez votre modèle, définissez votre créneau et laissez-vous transporter par une ingénierie hors du commun.'),
(5, 'RÉSERVER MON CRÉNEAU'),
(6, 'photo_essai.jpeg');

-- contenu services
INSERT INTO services_content (element_id, content) VALUES
('service_1_img', 'vidange.webp'),
('service_1_text', 'Lubrification haute performance avec huiles synthétiques certifiées.'),
('service_1_title', 'Vidange'),
('service_2_img', 'pneux.jpg'),
('service_2_text', 'Contrôle de l\'usure et équilibrage de précision.'),
('service_2_title', 'Pneus'),
('service_3_img', 'testdiagnostic.webp'),
('service_3_text', 'Scan complet des calculateurs et mise à jour des systèmes.'),
('service_3_title', 'Diagnostic'),
('service_4_img', 'moteur.jpg'),
('service_4_text', 'Calage et réglages fins de la distribution.'),
('service_4_title', 'Moteur'),
('serv_hero_desc', 'Nos services de maintenance garantissent que chaque kilomètre reste une expérience pure et sans compromis.'),
('serv_hero_title', 'Domptez <br> <span class="text-[var(--crimson)]">l\'instinct.</span>');

REPLACE INTO contact_content (element_id, content) VALUES 
('contact_title', 'CONTACTEZ-NOUS'),
('contact_subtitle', 'Une question ou un projet d''achat ? L''équipe SuperCar est à votre entière disposition.'),
('contact_phone', '+230 403 2000'),
('contact_email', 'contact@supercar.mu'),
('contact_address', 'Cybercité, Ébène - Île Maurice');

INSERT INTO `contact_settings` (`element_id`, `content`) VALUES
('contact_title', 'Contactez-Nous'),
('contact_subtitle', 'Une question ou une demande d\'essai ? Notre équipe est à votre disposition.'),
('company_address', 'Cybercité, Ébène - Île Maurice'),
('company_phone', '+230 390 9998'),
('company_email', 'contact@supercar.mu');

-- 3. On injecte les textes par défaut pour que l'Éditeur Live ne soit pas vide au départ
INSERT INTO contact_settings (element_id, content) VALUES
('contact_title', 'PRENDRE CONTACT'),
('contact_subtitle', 'Une question ? Un projet d\'acquisition ? Notre équipe d\'experts est à votre entière disposition pour vous accompagner.'),
('company_address', 'MCCI Business School, Ébène, Maurice'),
('company_phone', '+230 467 1234'),
('company_email', 'contact@supercar.mu')
ON DUPLICATE KEY UPDATE content=VALUES(content);