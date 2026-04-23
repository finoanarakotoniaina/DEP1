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
 

// COTE ADMIN 

CREATE TABLE admin(
   id_admin INT(50),
   identifiant VARCHAR(50) NOT NULL UNIQUE,
   nom VARCHAR(50) NOT NULL,
   prenom VARCHAR(50) NOT NULL,
   motdepasse VARCHAR(255) NOT NULL,
   PRIMARY KEY(id_admin)
);



-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 09, 2026 at 08:00 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `supercar`
--

-- --------------------------------------------------------

--
-- Table structure for table `home_content`
--

CREATE TABLE `home_content` (
  `element_id` varchar(50) NOT NULL,
  `content` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `home_content`
--

INSERT INTO `home_content` (`element_id`, `content`) VALUES
('brand_1_desc', 'Bien que la marque se soit diveifvehber'),
('brand_1_name', 'BMW'),
('hero_subtitle', 'Votre partenaire de confiance à l\'Île Maurice pour l\'acquisition de véhicules d\'exception importés d\'Allemagne, du Japon et des USA.'),
('hero_title', '\r\n                    \r\n                    \r\n                    L\'ÉLÉGANCE<br> <span class=\"text-[var(--crimson)]\">CERTIFIEE</span>'),
('hero_video', 'mercedes.mp4');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `home_content`
--
ALTER TABLE `home_content`
  ADD PRIMARY KEY (`element_id`);
COMMIT;



CREATE TABLE essai_content (
    id INT PRIMARY KEY,
    contenu TEXT
);

INSERT INTO essai_content (id, contenu) VALUES 
(1, 'EXPÉRIENCE ULTIME'),
(2, 'DEMANDE'),
(3, "D'ESSAI."),
(4, 'Prenez les commandes de l\'exceptionnel. Choisissez votre modèle, définissez votre créneau et laissez-vous transporter par une ingénierie hors du commun.'),
(5, 'RÉSERVER MON CRÉNEAU'),
(6, 'photo_essai.jpeg');
