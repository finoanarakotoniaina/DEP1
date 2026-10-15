INSERT INTO marque (nom_marque) VALUES
('Porsche'), ('BMW'), ('Mercedes'), ('Audi');


INSERT INTO voiture 
    (modele, prix, annee, etat, type, kilometrage, disponibilite, description, image_url, id_marque) 
VALUES
-- Porsche (id_marque = 1)
('Porsche 928',                 450000,  1982, 'Bon',         'thermique', 145000, TRUE, 'Grand Tourisme à moteur V8 avant.',                     'Porsche_928.jfif',       1),
('Porsche 912',                 550000,  1965, 'Excellent',   'thermique',  92000, TRUE, 'Variante plus légère de la 911.',                       'Porsche_912.jfif',       1),
('Porsche 930 (911 Turbo)',     850000,  1975, 'Collection',  'thermique',  68000, TRUE, 'La première 911 Turbo de série.',                       'Porsche_930.jfif',       1),
('Porsche 550 Spyder',         1000000,  1953, 'Concours',    'thermique',  15000, TRUE, 'Légende de la course automobile (Réplique ou prestige).','Porsche_550_Spyder.jfif', 1),
('Porsche 911 Carrera RS 2.7',  950000,  1973, 'Exceptionnel','thermique',  52000, TRUE, 'Icône absolue avec son aileron queue de canard.',       'Porsche_911.jfif',       1),
('Porsche 356 Speedster',       750000,  1955, 'Restauré',    'thermique',  41000, TRUE, 'Le cabriolet puriste par excellence.',                  'Porsche_356.jfif',       1),

-- BMW (id_marque = 2)
('M3 E30',                      650000,  1989, 'Très Bon',    'thermique', 112000, TRUE, 'Légende du DTM en rouge brillant.',                     'bmwE30.jpg',             2),
('Série 3 E46',                 220000,  2003, 'Bon',         'thermique', 178000, TRUE, 'Berline élégante et intemporelle.',                     'bmwE46.jpg',             2),
('Série 6 E63',                 380000,  2007, 'Très Bon',    'thermique', 135000, TRUE, 'Grand coupé de luxe au design distinctif.',             'bmwE63.jpg',             2),
('X5 E53',                      280000,  2004, 'Moyen',       'thermique', 215000, TRUE, 'Le premier SUV de la marque.',                          'bmwX5.jpg',              2),
('Z1',                          520000,  1990, 'Excellent',   'thermique',  54000, TRUE, 'Cabriolet futuriste aux portes escamotables.',          'bmwZ1.jpg',              2),
('Z3 Coupé',                    420000,  1999, 'Bon',         'thermique', 105000, TRUE, 'Le fameux "Shooting Brake".',                           'bmwZ3.jpg',              2),

-- Mercedes-Benz (id_marque = 3)
('Mercedes-Benz S 500 (W140)',     350000, 1997, 'Excellent',   'thermique', 155000, TRUE, 'La "Cathédrale", symbole du luxe absolu.',              'S500(W140).jpeg',              3),
('Mercedes-Benz 280 SL Pagode',    880000, 1968, 'Collection',  'thermique',  82000, TRUE, 'Icône d\'élégance avec son toit rigide concave.',       '280SLPagode(W113).webp',       3),
('Mercedes-Benz 450 SLC (C107)',   450000, 1978, 'Bon',         'thermique', 142000, TRUE, 'Coupé de luxe vintage.',                                '280SE3.5Coupé(W111).jpeg',     3),
('Mercedes-Benz 300 SL Gullwing', 1000000, 1955, 'Exceptionnel','thermique',  32000, TRUE, 'Légende mondiale avec ses portières papillon.',         '300SLGullwing(W198).jpeg',     3),
('Mercedes-Benz S 350 (W221)',     320000, 2007, 'Bon',         'thermique', 198000, TRUE, 'Berline de luxe moderne offrant un grand confort.',     'S350(W221).jpg',               3),
('Mercedes-Benz 190 SL Roadster',  780000, 1960, 'Bon',         'thermique',  76000, TRUE, 'Roadster classique décapotable.',                       '190SL(W121).jpg',              3),

-- Audi (id_marque = 4)
('Audi A3 Sportback', 350000, 2011, 'Très Bon', 'thermique', 120000, TRUE, 'Compacte premium polyvalente et élégante.',             'A3(8P).jpg',     4),
('Audi A4 B8',        480000, 2013, 'Excellent','thermique',  95000, TRUE, 'Berline confortable au design intemporel.',             'A4(B8).jpg',     4),
('Audi Q3',           750000, 2015, 'Bon',      'thermique',  88000, TRUE, 'SUV compact idéal pour la ville et la route.',          'Q3.jpg',         4),
('Audi TT 8J',        550000, 2009, 'Très Bon', 'thermique', 110000, TRUE, 'Coupé sportif iconique au design épuré.',               'AUDI_TT(8J).jpg',4),
('Audi A6 C7',        620000, 2014, 'Bon',      'thermique', 140000, TRUE, 'Grande berline de luxe avec finitions soignées.',       'A6(C7).jpg',     4),
('Audi Q5',           580000, 2012, 'Bon',      'thermique', 155000, TRUE, 'SUV familial robuste et performant.',                   'AUDI_Q5.jpg',    4);


-- Compte administrateur
INSERT INTO admin (identifiant, nom, prenom, motdepasse) 
VALUES ('MadisonCarter', 'Carter', 'Madison', 'didison');


INSERT INTO home_content (element_id, content) VALUES 
('titre_nos_modeles',       'NOS '),
('titre_nos_modeles_rouge', 'MODÈLES'),
('brand_1_name', 'BMW'),
('brand_1_desc', 'Bien que la marque se soit diversifiee.'),
('brand_2_name', 'MERCEDES-BENZ'),
('brand_2_desc', 'L''excellence et l''innovation automobile allemande depuis des generations.'),
('brand_3_name', 'AUDI'),
('brand_3_desc', 'Le confort haut de gamme allie a la technologie de pointe Vorsprung durch Technik.'),
('brand_4_name', 'PORSCHE'),
('brand_4_desc', 'La performance pure et le design intemporel des voitures de sport.');


INSERT INTO essai_content (element_id, content) VALUES 
(1, 'EXPÉRIENCE ULTIME'),
(2, 'DEMANDE'),
(3, 'D''ESSAI.'),
(4, 'Prenez les commandes de l''exceptionnel. Choisissez votre modèle, définissez votre créneau et laissez-vous transporter par une ingénierie hors du commun.'),
(5, 'RÉSERVER MON CRÉNEAU'),
(6, 'photo_essai.webp'),
(7,  'Configurateur<br>d''essai'),
(8,  'Mes demandes en cours'),
(9,  'Aucune demande en cours pour le moment.'),
(10, 'Confirmer la demande'),
(11, 'Limite de 3 demandes atteinte'),
(12, 'AUCUN MODÈLE SÉLECTIONNÉ');


INSERT INTO services_content (element_id, content) VALUES
('service_1_img',   'vidange.webp'),
('service_1_text',  'Lubrification haute performance avec huiles synthétiques certifiées.'),
('service_1_title', 'Vidange'),
('service_2_img',   'pneux.jpg'),
('service_2_text',  'Contrôle de l''usure et équilibrage de précision.'),
('service_2_title', 'Pneus'),
('service_3_img',   'testdiagnostic.webp'),
('service_3_text',  'Scan complet des calculateurs et mise à jour des systèmes.'),
('service_3_title', 'Diagnostic'),
('service_4_img',   'moteur.jpg'),
('service_4_text',  'Calage et réglages fins de la distribution.'),
('service_4_title', 'Moteur'),
('serv_hero_desc',  'Nos services de maintenance garantissent que chaque kilomètre reste une expérience pure et sans compromis.'),
('serv_hero_title', 'Domptez <br> <span class="title-accent">l''instinct.</span>');


INSERT INTO contact_content (element_id, content) VALUES
('titre_principal',     'NOUS CONTACTER'),
('image_supercar',      'photo_contact.webp'),
('email_contact',       'contact@supercar.com'),
('tel_contact',         '+33 1 23 45 67 89'),
('siege_contact',       'Paris, France'),
('responsable_contact', 'Jean Dupont'),
('map_iframe',          'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2624.9916256937595!2d2.2922926156743895!3d48.85837360868869!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47e66e2964e34e2d%3A0x8ddca9ee380ef7e0!2sTour%20Eiffel!5e0!3m2!1sfr!2sfr!4v1647891234567!5m2!1sfr!2sfr');
