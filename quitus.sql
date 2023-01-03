--
-- Base de données : `quitus`
--
CREATE DATABASE quitus;
-- --------------------------------------------------------

--
-- Structure de la table `demande_quitus`
--

CREATE TABLE `demande_quitus` (
  `code` bigint(20) NOT NULL,
  `uid` varchar(250) NOT NULL,
  `date_limite` datetime NOT NULL,
  `code_validation` varchar(13) DEFAULT NULL,
  `date_telechargement` datetime DEFAULT NULL,
  `nb_telechargement` int(11) NOT NULL DEFAULT 0,
  `erreur` varchar(2048) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

--
-- Index pour la table `demande_quitus`
--
ALTER TABLE `demande_quitus`
  ADD PRIMARY KEY (`code`);
