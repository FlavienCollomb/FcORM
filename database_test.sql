SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Base de données: `database_test`
--
CREATE DATABASE IF NOT EXISTS `database_test` DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;
USE `database_test`;

-- --------------------------------------------------------

--
-- Structure de la table `test`
--

DROP TABLE IF EXISTS `test`;
CREATE TABLE IF NOT EXISTS `test` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(15) COLLATE utf8_bin NOT NULL,
  `lib` varchar(150) COLLATE utf8_bin NOT NULL,
  `test_type_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `test_type_id` (`test_type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=3 ;

--
-- Contenu de la table `test`
--

INSERT INTO `test` (`id`, `code`, `lib`, `test_type_id`) VALUES
(1, 'code1', 'Code 1 pour test', 1),
(2, 'code2', 'Code 2 pour test', 2);

-- --------------------------------------------------------

--
-- Structure de la table `test_type`
--

DROP TABLE IF EXISTS `test_type`;
CREATE TABLE IF NOT EXISTS `test_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=3 ;

--
-- Contenu de la table `test_type`
--

INSERT INTO `test_type` (`id`, `name`) VALUES
(1, 'Type 1'),
(2, 'Type 2');

--
-- Contraintes pour les tables exportées
--

--
-- Contraintes pour la table `test`
--
ALTER TABLE `test`
  ADD CONSTRAINT `test_ibfk_1` FOREIGN KEY (`test_type_id`) REFERENCES `test_type` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
