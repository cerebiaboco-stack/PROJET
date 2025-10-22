-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mer. 15 oct. 2025 à 19:12
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `banquedesang`
--

-- --------------------------------------------------------

--
-- Structure de la table `banque`
--

CREATE TABLE `banque` (
  `IdBanque` int(11) NOT NULL,
  `NomBanque` varchar(100) NOT NULL,
  `Adresse` varchar(255) DEFAULT NULL,
  `Responsable` varchar(100) DEFAULT NULL,
  `StockTotal` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `banque`
--

INSERT INTO `banque` (`IdBanque`, `NomBanque`, `Adresse`, `Responsable`, `StockTotal`) VALUES
(1, 'Banque adjo', 'Cotonou', 'Dr Ayovi', 23),
(3, 'Banque Ariwo', 'Calavi', 'Dr Adjovi', 45);

-- --------------------------------------------------------

--
-- Structure de la table `cave`
--

CREATE TABLE `cave` (
  `IdCave` int(11) NOT NULL,
  `Idbanque` int(11) NOT NULL,
  `NomCave` varchar(100) NOT NULL,
  `Temperature` decimal(5,2) DEFAULT NULL,
  `TypeCave` varchar(50) DEFAULT NULL,
  `Responsable` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `demande`
--

CREATE TABLE `demande` (
  `IdDemande` int(11) NOT NULL,
  `DateDemande` date NOT NULL,
  `GroupeSanguin` varchar(5) NOT NULL,
  `Quantite` int(11) DEFAULT NULL,
  `Statut` varchar(50) DEFAULT NULL,
  `IdBanque` int(11) DEFAULT NULL,
  `IdMedecin` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `hopital`
--

CREATE TABLE `hopital` (
  `IdHopital` int(11) NOT NULL,
  `Nom` varchar(100) NOT NULL,
  `Adresse` varchar(255) DEFAULT NULL,
  `Ville` varchar(100) DEFAULT NULL,
  `Contact` varchar(50) DEFAULT NULL,
  `Responsable` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `hopital`
--

INSERT INTO `hopital` (`IdHopital`, `Nom`, `Adresse`, `Ville`, `Contact`, `Responsable`) VALUES
(1, 'CNHU', 'Cotonou', 'Cotonou', '41171988', 'Cérébia'),
(2, 'CHUZ', 'Kowégbo', 'Cotonou', '41171988', 'Dr AZONVI'),
(3, 'Bon Samaritin', 'Agla', 'Cotonou', '41121557', 'Dr AZOVIWE');

-- --------------------------------------------------------

--
-- Structure de la table `medecin`
--

CREATE TABLE `medecin` (
  `IdMedecin` int(11) NOT NULL,
  `IdHopital` int(11) NOT NULL,
  `Nom` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `Specialite` varchar(100) DEFAULT NULL,
  `Contact` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `medecin`
--

INSERT INTO `medecin` (`IdMedecin`, `IdHopital`, `Nom`, `email`, `Specialite`, `Contact`) VALUES
(2, 1, 'ertyucvbn', 'dodo@gmail.com', 'azertyu', 'sdfgh');

-- --------------------------------------------------------

--
-- Structure de la table `notifications`
--

CREATE TABLE `notifications` (
  `IdNotification` int(11) NOT NULL,
  `IdUsers` int(11) NOT NULL,
  `Message` varchar(255) NOT NULL,
  `Type` varchar(20) DEFAULT 'info',
  `DateCreation` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `poche`
--

CREATE TABLE `poche` (
  `IdPoche` int(11) NOT NULL,
  `Volume` decimal(5,2) DEFAULT NULL,
  `GroupeSanguin` varchar(5) NOT NULL,
  `DateCollecte` date DEFAULT NULL,
  `DatePeremption` date DEFAULT NULL,
  `IdCave` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `IdUsers` int(11) NOT NULL,
  `MotDePasse` varchar(255) NOT NULL,
  `Role` varchar(50) DEFAULT NULL,
  `Email` varchar(100) DEFAULT NULL,
  `DateCreation` timestamp NULL DEFAULT NULL,
  `IdMedecin` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`IdUsers`, `MotDePasse`, `Role`, `Email`, `DateCreation`, `IdMedecin`) VALUES
(26, '$2y$10$1JcO3nHmT.DS7k9iTQPgDenb6lX01AoXI6hjyS1nHXXdkdRVW2Wp6', 'administrateur', 'Cerebiaboco@gmail.com', '2025-10-15 13:13:26', NULL),
(27, '$2y$10$Y2Z9YKODDl8LnbMK5RmaVeTdbxEEE.e7a1EzYUfXGYu9kMSiws986', 'medecin', 'Freudsergio@gmail.com', '2025-10-15 13:14:53', NULL);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `banque`
--
ALTER TABLE `banque`
  ADD PRIMARY KEY (`IdBanque`);

--
-- Index pour la table `cave`
--
ALTER TABLE `cave`
  ADD PRIMARY KEY (`IdCave`),
  ADD KEY `fk_banq_banq` (`Idbanque`);

--
-- Index pour la table `demande`
--
ALTER TABLE `demande`
  ADD PRIMARY KEY (`IdDemande`),
  ADD KEY `IdBanque` (`IdBanque`),
  ADD KEY `IdMedecin` (`IdMedecin`);

--
-- Index pour la table `hopital`
--
ALTER TABLE `hopital`
  ADD PRIMARY KEY (`IdHopital`);

--
-- Index pour la table `medecin`
--
ALTER TABLE `medecin`
  ADD PRIMARY KEY (`IdMedecin`),
  ADD KEY `fk_banq_hop` (`IdHopital`);

--
-- Index pour la table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`IdNotification`);

--
-- Index pour la table `poche`
--
ALTER TABLE `poche`
  ADD PRIMARY KEY (`IdPoche`),
  ADD KEY `IdCave` (`IdCave`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`IdUsers`),
  ADD KEY `fk_user_medecin` (`IdMedecin`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `banque`
--
ALTER TABLE `banque`
  MODIFY `IdBanque` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `cave`
--
ALTER TABLE `cave`
  MODIFY `IdCave` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `demande`
--
ALTER TABLE `demande`
  MODIFY `IdDemande` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `hopital`
--
ALTER TABLE `hopital`
  MODIFY `IdHopital` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `medecin`
--
ALTER TABLE `medecin`
  MODIFY `IdMedecin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `IdNotification` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `poche`
--
ALTER TABLE `poche`
  MODIFY `IdPoche` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `IdUsers` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `cave`
--
ALTER TABLE `cave`
  ADD CONSTRAINT `fk_banq_banq` FOREIGN KEY (`Idbanque`) REFERENCES `banque` (`IdBanque`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `demande`
--
ALTER TABLE `demande`
  ADD CONSTRAINT `demande_ibfk_1` FOREIGN KEY (`IdBanque`) REFERENCES `banque` (`IdBanque`),
  ADD CONSTRAINT `demande_ibfk_2` FOREIGN KEY (`IdMedecin`) REFERENCES `medecin` (`IdMedecin`);

--
-- Contraintes pour la table `medecin`
--
ALTER TABLE `medecin`
  ADD CONSTRAINT `fk_banq_hop` FOREIGN KEY (`IdHopital`) REFERENCES `hopital` (`IdHopital`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `poche`
--
ALTER TABLE `poche`
  ADD CONSTRAINT `poche_ibfk_1` FOREIGN KEY (`IdCave`) REFERENCES `cave` (`IdCave`);

--
-- Contraintes pour la table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_user_medecin` FOREIGN KEY (`IdMedecin`) REFERENCES `medecin` (`IdMedecin`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
