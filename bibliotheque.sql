-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : mar. 14 juil. 2026 à 18:05
-- Version du serveur : 8.2.0
-- Version de PHP : 8.2.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `bibliotheque`
--

-- --------------------------------------------------------

--
-- Structure de la table `administrateurs`
--

DROP TABLE IF EXISTS `administrateurs`;
CREATE TABLE IF NOT EXISTS `administrateurs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nom` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_creation` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `administrateurs`
--

INSERT INTO `administrateurs` (`id`, `username`, `password`, `nom`, `email`, `date_creation`) VALUES
(1, 'admin', '$2y$10$W3Prdi8BgUOT3QeHXDzl3u5GNjy7zfEQUfv/8JWnaJmf.VxRemKPq', 'Administrateur Principal', 'admin@bibliotheque.com', '2026-07-14 17:53:16'),
(2, 'biblio', '$2y$10$W3Prdi8BgUOT3QeHXDzl3u5GNjy7zfEQUfv/8JWnaJmf.VxRemKPq', 'Bibliothécaire', 'biblio@bibliotheque.com', '2026-07-14 17:53:16');

-- --------------------------------------------------------

--
-- Structure de la table `lecteurs`
--

DROP TABLE IF EXISTS `lecteurs`;
CREATE TABLE IF NOT EXISTS `lecteurs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `prenom` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `lecteurs`
--

INSERT INTO `lecteurs` (`id`, `nom`, `prenom`, `email`) VALUES
(1, 'Dupont', 'Marie', 'marie.dupont@email.com'),
(2, 'Martin', 'Jean', 'jean.martin@email.com'),
(3, 'Bernard', 'Sophie', 'sophie.bernard@email.com'),
(4, 'Petit', 'Lucas', 'lucas.petit@email.com'),
(5, 'Robert', 'Emma', 'emma.robert@email.com');

-- --------------------------------------------------------

--
-- Structure de la table `liste_lecture`
--

DROP TABLE IF EXISTS `liste_lecture`;
CREATE TABLE IF NOT EXISTS `liste_lecture` (
  `id_livre` int NOT NULL,
  `id_lecteur` int NOT NULL,
  `date_emprunt` date DEFAULT NULL,
  `date_retour` date DEFAULT NULL,
  PRIMARY KEY (`id_livre`,`id_lecteur`),
  KEY `id_lecteur` (`id_lecteur`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `liste_lecture`
--

INSERT INTO `liste_lecture` (`id_livre`, `id_lecteur`, `date_emprunt`, `date_retour`) VALUES
(4, 5, '2026-06-22', '2026-07-22'),
(5, 3, '2026-06-25', '2026-07-25');

-- --------------------------------------------------------

--
-- Structure de la table `livres`
--

DROP TABLE IF EXISTS `livres`;
CREATE TABLE IF NOT EXISTS `livres` (
  `id` int NOT NULL AUTO_INCREMENT,
  `titre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `auteur` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `maison_edition` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nombre_exemplaire` int DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `livres`
--

INSERT INTO `livres` (`id`, `titre`, `auteur`, `description`, `maison_edition`, `nombre_exemplaire`) VALUES
(1, 'Le Petit Prince', 'Antoine de Saint-Exupéry', 'Un aviateur rencontre un petit prince venu d\'un astéroïde lointain. Ensemble, ils explorent les thèmes de l\'amitié, de l\'amour et du sens de la vie.', 'Gallimard', 6),
(3, 'Harry Potter à l\'école des sorciers', 'J.K. Rowling', 'Harry Potter découvre qu\'il est un sorcier et entre à Poudlard, l\'école de magie, où il fait ses premiers pas dans un monde extraordinaire.', 'Bloomsbury', 9),
(4, 'Les Misérables', 'Victor Hugo', 'L\'histoire de Jean Valjean, un ancien forçat qui cherche la rédemption dans la France du XIXe siècle.', 'A. Lacroix, Verboeckhoven & Cie', 2),
(5, 'L\'Étranger', 'Albert Camus', 'Meursault, un employé d\'Alger, vit sa vie avec indifférence jusqu\'au jour où il commet un acte irréversible.', 'Gallimard', 4),
(6, 'Le Seigneur des Anneaux', 'J.R.R. Tolkien', 'Frodon Sacquet hérite d\'un anneau magique et doit entreprendre un périlleux voyage pour le détruire.', 'Allen & Unwin', 6);

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `liste_lecture`
--
ALTER TABLE `liste_lecture`
  ADD CONSTRAINT `liste_lecture_ibfk_1` FOREIGN KEY (`id_livre`) REFERENCES `livres` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `liste_lecture_ibfk_2` FOREIGN KEY (`id_lecteur`) REFERENCES `lecteurs` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
