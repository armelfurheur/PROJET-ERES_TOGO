-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : dim. 19 oct. 2025 à 15:54
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `eres_db`
--

-- --------------------------------------------------------

--
-- Structure de la table `actions_correctrices`
--

CREATE TABLE `actions_correctrices` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `description` text NOT NULL,
  `date_limite` date NOT NULL,
  `statut` enum('en_cours','terminee') NOT NULL DEFAULT 'en_cours',
  `anomalie_id` bigint(20) UNSIGNED NOT NULL,
  `responsable_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `anomalies`
--

CREATE TABLE `anomalies` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `rapporte_par` varchar(255) NOT NULL,
  `departement` varchar(255) NOT NULL,
  `localisation` varchar(255) NOT NULL,
  `datetime` datetime NOT NULL,
  `statut` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `action` text NOT NULL,
  `preuve` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `anomalies`
--

INSERT INTO `anomalies` (`id`, `rapporte_par`, `departement`, `localisation`, `datetime`, `statut`, `description`, `action`, `preuve`, `created_at`, `updated_at`) VALUES
(1, 'astrid', 'Administratif', 'l;mml;', '2025-10-16 17:24:39', 'precaution', 'ml;;l', ';ml;ml', 'preuves/DSxBWXs6Rxe01Z4L1JDgMsAuN4AGAFJKa9tiE2px.jpg', '2025-10-16 17:24:57', '2025-10-16 17:24:57'),
(27, 'astrid', 'Administratif', 'l;mml;', '2025-10-16 17:24:39', 'precaution', 'ml;;l', ';ml;ml', 'preuves/icU7lONA3Hn8Gv073SZx7OBd4DVnVzfcF3kd6avX.jpg', '2025-10-16 17:25:33', '2025-10-16 17:25:33'),
(28, 'astrid', 'Administratif', 'l;mml;', '2025-10-16 17:24:39', 'precaution', 'ml;;l', ';ml;ml', 'preuves/xKRdcg4NBmk2HmfjKv2jashBcNhzoC9kj8ZZP8g7.jpg', '2025-10-16 17:25:34', '2025-10-16 17:25:34'),
(29, 'astrid', 'Administratif', 'l;mml;', '2025-10-16 17:24:39', 'precaution', 'ml;;l', ';ml;ml', 'preuves/3EekNhD4iXLVzfQ6x99kr6FLZJ6kAEDViQl24Mel.jpg', '2025-10-16 17:25:34', '2025-10-16 17:25:34'),
(30, 'astrid', 'Administratif', 'l;mml;', '2025-10-16 17:24:39', 'precaution', 'ml;;l', ';ml;ml', 'preuves/Gf2eYjx7jRX0APNuGQ7Hs8XSm79cne2Mv1aJxpAA.jpg', '2025-10-16 17:25:39', '2025-10-16 17:25:39'),
(31, 'astrid', 'Administratif', 'l;mml;', '2025-10-16 17:24:39', 'precaution', 'ml;;l', ';ml;ml', 'preuves/UdxNa4dHvS2qPyCvUTHjdHeuuFhrfkmLqZ7xYm7P.jpg', '2025-10-16 17:27:22', '2025-10-16 17:27:22'),
(32, 'astrid', 'Administratif', 'l;mml;', '2025-10-16 17:24:39', 'precaution', 'ml;;l', ';ml;ml', 'preuves/2ByP6iiPXKiszf2Dyz2h7h5IlLGe6q6L9flKRji1.jpg', '2025-10-16 17:27:36', '2025-10-16 17:27:36'),
(33, 'astrid', 'Administratif', 'l;mml;', '2025-10-16 17:24:39', 'precaution', 'ml;;l', ';ml;ml', 'preuves/zukUamo56k8OPzX2jt6grjuNlyQryU5fH89FVV7E.jpg', '2025-10-16 17:28:12', '2025-10-16 17:28:12'),
(34, 'astrid', 'Administratif', 'l;mml;', '2025-10-16 17:24:39', 'precaution', 'ml;;l', ';ml;ml', 'preuves/pVZRYRNIZf9YhDn7v3oPW8H6KvaxOb727QMhQzmP.jpg', '2025-10-16 17:28:33', '2025-10-16 17:28:33'),
(35, 'astrid', 'Administratif', 'l;mml;', '2025-10-16 17:24:39', 'precaution', 'ml;;l', ';ml;ml', 'preuves/oagF1tqLEh5xLAwhtK2LCk7wgtgIJfBezEmn7x8W.jpg', '2025-10-16 17:28:47', '2025-10-16 17:28:47'),
(36, 'astrid', 'Administratif', 'l;mml;', '2025-10-16 17:24:39', 'precaution', 'ml;;l', ';ml;ml', 'preuves/8V9aj5bgzl4L8xcgSec9ClshzxHQ2Zoz3xa3Vhta.jpg', '2025-10-16 17:29:22', '2025-10-16 17:29:22'),
(37, 'astrid', 'Administratif', 'l;mml;', '2025-10-16 17:24:39', 'precaution', 'ml;;l', ';ml;ml', 'preuves/ys9w3vsJBCNsI88Qi1UCwzAwMjhbBrw9gr4FuQgS.jpg', '2025-10-16 17:29:47', '2025-10-16 17:29:47'),
(38, 'astrid', 'Administratif', 'l;mml;', '2025-10-16 17:24:39', 'precaution', 'ml;;l', ';ml;ml', 'preuves/ojD03uyuZDZXhCbPeAjmHRBdJLrQr6H7EdVIgC8v.jpg', '2025-10-16 17:30:07', '2025-10-16 17:30:07'),
(39, 'astrid', 'Administratif', 'l;mml;', '2025-10-16 17:24:39', 'precaution', 'ml;;l', ';ml;ml', 'preuves/3VoDkJNxYNJbnsXJL689AHc23BdLWWnDPv1V3qzi.jpg', '2025-10-16 17:30:20', '2025-10-16 17:30:20'),
(40, 'astrid', 'Administratif', 'l;mml;', '2025-10-16 17:24:39', 'precaution', 'ml;;l', ';ml;ml', 'preuves/5CoxGcmXEZqp9TlcUKf4QxfrbThognBRN7lLkV4X.jpg', '2025-10-16 17:30:51', '2025-10-16 17:30:51'),
(41, 'astrid', 'Administratif', 'l;mml;', '2025-10-16 17:24:39', 'precaution', 'ml;;l', ';ml;ml', 'preuves/Ho8KCoH13sn2wRBleDDm4P85H1wZmar7E7oyVh2S.jpg', '2025-10-16 17:31:11', '2025-10-16 17:31:11'),
(42, 'astrid', 'Administratif', 'l;mml;', '2025-10-16 17:24:39', 'precaution', 'ml;;l', ';ml;ml', 'preuves/7iZusK5HJFmG9mGgYb3OpGnDqBS4r28c7Oii3krs.jpg', '2025-10-16 17:31:26', '2025-10-16 17:31:26'),
(43, 'astrid', 'Administratif', 'l;mml;', '2025-10-16 17:24:39', 'precaution', 'ml;;l', ';ml;ml', 'preuves/tviOR0CxgiZfEGRJmQHFz1dBL6CeWX1dVQXjECWm.jpg', '2025-10-16 17:31:28', '2025-10-16 17:31:28'),
(44, 'astrid', 'Administratif', 'l;mml;', '2025-10-16 17:24:39', 'precaution', 'ml;;l', ';ml;ml', 'preuves/DS9eN6LaWsKXFz90nvtlSqnVv3n62NBCJqQKrDBr.jpg', '2025-10-16 17:32:00', '2025-10-16 17:32:00'),
(45, 'astrid', 'Administratif', 'l;mml;', '2025-10-16 17:24:39', 'precaution', 'ml;;l', ';ml;ml', 'preuves/x1JGXWXXOPUApy9eLMJI5oh1s2NySPe8TrAcLGBH.jpg', '2025-10-16 17:32:08', '2025-10-16 17:32:08'),
(46, 'astrid', 'Administratif', 'l;mml;', '2025-10-16 17:24:39', 'precaution', 'ml;;l', ';ml;ml', 'preuves/NoQcm1FOB3uRks2jEmrs35Tp7eXva1j34EDdrOiG.jpg', '2025-10-16 17:32:10', '2025-10-16 17:32:10'),
(47, 'astrid', 'Administratif', 'l;mml;', '2025-10-16 17:24:39', 'precaution', 'ml;;l', ';ml;ml', 'preuves/9i7CGlaKLbxO5sTL7z9Z9IjRkwLjYAF6f3qo9nXo.jpg', '2025-10-16 17:32:15', '2025-10-16 17:32:15'),
(48, 'astrid', 'Administratif', 'l;mml;', '2025-10-16 17:24:39', 'precaution', 'ml;;l', ';ml;ml', 'preuves/16P4hlIyizxcrNtFYtYPh22lkzSYtcnHUCubJ8Zt.jpg', '2025-10-16 17:32:18', '2025-10-16 17:32:18'),
(50, 'astrid', 'Administratif', 'll;', '2025-10-16 17:41:58', 'precaution', 'ùm;ùm;', 'ùmùm;', 'preuves/6PCuRg8Ujk6D6sJtKm7IDxEinm2Kb8AgsrNiTRj8.jpg', '2025-10-16 17:42:21', '2025-10-16 17:42:21'),
(51, 'astrid', 'Administratif', 'll;', '2025-10-16 17:41:58', 'precaution', 'ùm;ùm;', 'ùmùm;', 'preuves/DTVWIB68jauvMH6bsmLlRsOabyHzwXLdCre6FOeo.jpg', '2025-10-16 17:42:22', '2025-10-16 17:42:22'),
(52, 'astrid', 'Administratif', 'll;', '2025-10-16 17:41:58', 'precaution', 'ùm;ùm;', 'ùmùm;', 'preuves/51E3Md61WbqocJL36wWdINZXDVPiW6BZklkEvsly.jpg', '2025-10-16 17:42:23', '2025-10-16 17:42:23'),
(53, 'astrid', 'Administratif', 'll;', '2025-10-16 17:41:58', 'precaution', 'ùm;ùm;', 'ùmùm;', 'preuves/6YRrPaQTCW7wwYZm7TJGRIXa1j6BLrOTIF6lUE7f.jpg', '2025-10-16 17:42:24', '2025-10-16 17:42:24'),
(54, 'astrid', 'Administratif', 'll;', '2025-10-16 17:41:58', 'precaution', 'ùm;ùm;', 'ùmùm;', 'preuves/DiSeAjwVWz5qsx5xcGDTBpq2V2eo78YII7SgPisw.jpg', '2025-10-16 17:42:24', '2025-10-16 17:42:24'),
(55, 'astrid', 'Administratif', 'll;', '2025-10-16 17:41:58', 'precaution', 'ùm;ùm;', 'ùmùm;', 'preuves/T12sp1yh3ZV4eoy1W5yS5dRy3Gvi15fe69dDrwV1.jpg', '2025-10-16 17:42:25', '2025-10-16 17:42:25'),
(56, 'astrid', 'Administratif', 'll;', '2025-10-16 17:41:58', 'precaution', 'ùm;ùm;', 'ùmùm;', 'preuves/0MrgjiFG4BmDynBHebrhMreq8RsihzqHy5ICbYDt.jpg', '2025-10-16 17:42:26', '2025-10-16 17:42:26'),
(57, 'astrid', 'Administratif', 'll;', '2025-10-16 17:41:58', 'precaution', 'ùm;ùm;', 'ùmùm;', 'preuves/ueW7kpf9MhWZ16gkmIPOSy5pBDqsO9lzRILYj4zI.jpg', '2025-10-16 17:42:26', '2025-10-16 17:42:26'),
(58, 'astrid', 'Administratif', 'll;', '2025-10-16 17:41:58', 'precaution', 'ùm;ùm;', 'ùmùm;', 'preuves/W46Y7QPpnHODlZaEMPamQzDoCSF1qx4D7ZG14p7r.jpg', '2025-10-16 17:42:27', '2025-10-16 17:42:27'),
(59, 'astrid', 'Administratif', 'll;', '2025-10-16 17:41:58', 'precaution', 'ùm;ùm;', 'ùmùm;', 'preuves/ZcRorpqcq75aUMb10P8ro3dSRtlTHHbAZmgoq54l.jpg', '2025-10-16 17:42:27', '2025-10-16 17:42:27'),
(60, 'astrid', 'Administratif', 'll;', '2025-10-16 17:41:58', 'precaution', 'ùm;ùm;', 'ùmùm;', 'preuves/M6lyeGhqxqqsLReZXOViebSCXFEqOUgcAm04mzLW.jpg', '2025-10-16 17:42:28', '2025-10-16 17:42:28'),
(61, 'AYO junior', 'Technique', 'fff', '2025-10-16 21:04:08', 'arret', 'fcfgft', 'ffd', 'preuves/XZeGNduiYqRZuZB5l0HxS5eB59y6PCMGYwE8ftMn.jpg', '2025-10-16 21:04:58', '2025-10-16 21:04:58'),
(62, 'AYO junior', 'Technique', 'fff', '2025-10-16 21:04:08', 'arret', 'fcfgft', 'ffd', 'preuves/1xnHuVMmgYKiObDiJqqLykOTHT5Wn2WNYoamoSpd.jpg', '2025-10-16 21:04:59', '2025-10-16 21:04:59'),
(63, 'AYO junior', 'Technique', 'fff', '2025-10-16 21:04:08', 'arret', 'fcfgft', 'ffd', 'preuves/bv5rlQKHXmQZfYjLj3kqwXfRQ6uAV8XL2XL0wejj.jpg', '2025-10-16 21:05:00', '2025-10-16 21:05:00'),
(64, 'AYO junior', 'Technique', 'fff', '2025-10-16 21:04:08', 'arret', 'fcfgft', 'ffd', 'preuves/G9ON3AjyiU2mMXk3PiXYtUn5YIF0Z0RcLQvRdsU0.jpg', '2025-10-16 21:05:01', '2025-10-16 21:05:01'),
(65, 'AYO junior', 'Technique', 'fff', '2025-10-16 21:04:08', 'arret', 'fcfgft', 'ffd', 'preuves/407IJooY91WgljDZzVcHPm7lgv8fVdsklcnz1IoQ.jpg', '2025-10-16 21:05:02', '2025-10-16 21:05:02'),
(66, 'AYO junior', 'Technique', 'fff', '2025-10-16 21:04:08', 'arret', 'fcfgft', 'ffd', 'preuves/OJ38XzSbnX6BEU3nm7JRwuPz7i3LAxtmZH8IVd2T.jpg', '2025-10-16 21:05:22', '2025-10-16 21:05:22'),
(67, 'AYO junior', 'Technique', 'fff', '2025-10-16 21:04:08', 'arret', 'fcfgft', 'ffd', 'preuves/VfKcbtyh7y0kTzGUs5f0RLnq1d0B7W5pYvKZ4muy.jpg', '2025-10-16 21:05:24', '2025-10-16 21:05:24'),
(68, 'AYO junior', 'Technique', 'fff', '2025-10-16 21:04:08', 'arret', 'fcfgft', 'ffd', 'preuves/Aa3bnuOO3ZHtHGWh8ZMqVE548rgYE8ANO0Sq9Cdc.jpg', '2025-10-16 21:05:24', '2025-10-16 21:05:24'),
(69, 'AYO junior', 'Technique', 'dsfg', '2025-10-16 21:06:04', 'continuer', 'cfgf', 'x', 'preuves/ZTaSIaxzEA2XzCTRjLxU21AuMUPjTsm9IAKguzvI.jpg', '2025-10-16 21:06:23', '2025-10-16 21:06:23'),
(70, 'AYO junior', 'Technique', 'dsfg', '2025-10-16 21:06:04', 'continuer', 'cfgf', 'x', 'preuves/YgQGr0rZx9mbNDCcZpUaXUjIVWRJGmVgR8K4hHsa.jpg', '2025-10-16 21:06:24', '2025-10-16 21:06:24'),
(71, 'AYO junior', 'Technique', 'dsfg', '2025-10-16 21:06:04', 'continuer', 'cfgf', 'x', 'preuves/WzQPNmNTjKUbaHcrGTL4xqnUyP4oSH3Q7akkKTIA.jpg', '2025-10-16 21:06:26', '2025-10-16 21:06:26'),
(72, 'AYO junior', 'Technique', 'dsfg', '2025-10-16 21:06:04', 'continuer', 'cfgf', 'x', 'preuves/uQWv0QhfVBasA1WY3XY4X9p8SgLsscJV89YrOj9D.jpg', '2025-10-16 21:06:26', '2025-10-16 21:06:26'),
(73, 'AYO junior', 'Technique', 'dsfg', '2025-10-16 21:06:04', 'continuer', 'cfgf', 'x', 'preuves/gcn0OcP3qV6bpra9tWvoea8owuFnCjDEYoB2twIv.jpg', '2025-10-16 21:06:29', '2025-10-16 21:06:29'),
(74, 'AYO junior', 'Technique', 'dsfg', '2025-10-16 21:06:04', 'continuer', 'cfgf', 'x', 'preuves/YhSJApEb8GFfQgqbcPAq8O7PF1ryjXKOXs3Xpu85.jpg', '2025-10-16 21:06:29', '2025-10-16 21:06:29'),
(75, 'AYO junior', 'Technique', 'dsfg', '2025-10-16 21:06:04', 'continuer', 'cfgf', 'x', 'preuves/Rn1jQHOslrVP7VMddre42S02U4ox56bIAPl7wakf.jpg', '2025-10-16 21:06:32', '2025-10-16 21:06:32'),
(76, 'AYO junior', 'Technique', 'dsfg', '2025-10-16 21:06:04', 'continuer', 'cfgf', 'x', 'preuves/lHObWzFmzTIWCHF6XfnpLdOW3qrvFLybg3TRC18h.jpg', '2025-10-16 21:06:33', '2025-10-16 21:06:33'),
(77, 'AYO junior', 'Technique', 'dsfg', '2025-10-16 21:06:04', 'continuer', 'cfgf', 'x', 'preuves/GWWVcKv3MzhRdCBrzfFzZD3YFB1fQYyXGWaxt54Y.jpg', '2025-10-16 21:06:34', '2025-10-16 21:06:34'),
(78, 'AYO junior', 'Technique', 'dsfg', '2025-10-16 21:06:04', 'continuer', 'cfgf', 'x', 'preuves/QjkVgDbvbHhJx4SHvMMyEohZxBKp4ShoD3wKb8Mu.jpg', '2025-10-16 21:06:35', '2025-10-16 21:06:35'),
(79, 'AYO junior', 'Technique', 'dsfg', '2025-10-16 21:06:04', 'continuer', 'cfgf', 'x', 'preuves/JM089koDfozd76pun1A1iBRaocDwkSpRrPF0MaV3.jpg', '2025-10-16 21:06:36', '2025-10-16 21:06:36'),
(80, 'AYO junior', 'Technique', 'dsfg', '2025-10-16 21:06:04', 'continuer', 'cfgf', 'x', 'preuves/8amV1dI35Q705ekDOblXsh8FxouMqH6KQctENxMs.jpg', '2025-10-16 21:06:37', '2025-10-16 21:06:37'),
(81, 'AYO junior', 'Technique', 'dsfg', '2025-10-16 21:06:04', 'continuer', 'cfgf', 'x', 'preuves/UlMkVn9Bj3z9xMq9tnCOf4sBpaVE4kBe2JwSodmH.jpg', '2025-10-16 21:06:38', '2025-10-16 21:06:38'),
(82, 'AYO junior', 'Technique', 'dsfg', '2025-10-16 21:06:04', 'continuer', 'cfgf', 'x', 'preuves/vzVwPUEWfPbuQ16Xhe6YidyUoTt7X5LRmmD6EaJ1.jpg', '2025-10-16 21:06:42', '2025-10-16 21:06:42'),
(83, 'AYO junior', 'Technique', 'dsfg', '2025-10-16 21:06:04', 'continuer', 'cfgf', 'x', 'preuves/Q9avFWx3L5uCFEdDdYd45hMW7iX2GkCB6a0IBjHd.jpg', '2025-10-16 21:06:45', '2025-10-16 21:06:45'),
(84, 'AYO junior', 'Technique', 'dsfg', '2025-10-16 21:06:04', 'continuer', 'cfgf', 'x', 'preuves/HzSTF3c0kOpaeITJ0l6XbwYwybsQYwfld4mN7ebb.jpg', '2025-10-16 21:06:46', '2025-10-16 21:06:46'),
(85, 'AYO junior', 'Technique', 'dsfg', '2025-10-16 21:06:04', 'continuer', 'cfgf', 'x', 'preuves/gEZgS6O2DI9NjEuQ5yJjdszdeAMHCjKlZDd9F6e0.jpg', '2025-10-16 21:06:47', '2025-10-16 21:06:47'),
(86, 'AYO junior', 'Technique', 'dsfg', '2025-10-16 21:06:04', 'continuer', 'cfgf', 'x', 'preuves/iKGq3M4JgdogvUIAksyxMV7Vz2mcjuQ9oPpjDTzs.jpg', '2025-10-16 21:06:49', '2025-10-16 21:06:49'),
(87, 'AYO junior', 'Technique', 'sd', '2025-10-16 21:09:32', 'precaution', 'ccdccd', 'xscs', 'preuves/iPVEuaBlICUkpQTJ2OIVhWuYskQ94SbNG8L55Rx4.jpg', '2025-10-16 21:10:01', '2025-10-16 21:10:01'),
(88, 'AYO junior', 'Technique', 'sd', '2025-10-16 21:09:32', 'precaution', 'ccdccd', 'xscs', 'preuves/gU9pvPwco4PzjtQkZF6JyZOED5MGHHUUBGjrUHTy.jpg', '2025-10-16 21:10:03', '2025-10-16 21:10:03'),
(89, 'AYO junior', 'Technique', 'sd', '2025-10-16 21:09:32', 'precaution', 'ccdccd', 'xscs', 'preuves/574TBZ7BsYZuEq1azY3Ucv6Zr5X7YYQwoujpaZ9T.jpg', '2025-10-16 21:10:03', '2025-10-16 21:10:03'),
(90, 'AYO junior', 'Technique', 'sd', '2025-10-16 21:09:32', 'precaution', 'ccdccd', 'xscs', 'preuves/n6VWV039tZzWW5qatvfcUDYnM0v2ccncNe7dvSrb.jpg', '2025-10-16 21:10:04', '2025-10-16 21:10:04'),
(91, 'AYO junior', 'Technique', 'sd', '2025-10-16 21:09:32', 'precaution', 'ccdccd', 'xscs', 'preuves/DMFfif3UPrcmfRANtDRHlOqu6j1cskTu9FufuXNK.jpg', '2025-10-16 21:10:04', '2025-10-16 21:10:04'),
(92, 'astrid', 'Administratif', 'dplfdp', '2025-10-17 10:32:17', 'arret', 'pk', 'pk', 'preuves/rc0VvZO4rrVVCIODQO8EjdisoR3M5sPeW6TGAuU8.jpg', '2025-10-17 10:32:35', '2025-10-17 10:32:35'),
(93, 'AYO junior', 'Technique', 'pont bascule', '2025-10-18 14:52:31', 'continuer', 'ampoule defectueuse', 'rapporter', 'preuves/jv774wbUozezDzbRm4JJkmZuZ9hnD56QvC5igZWN.jpg', '2025-10-18 14:53:29', '2025-10-18 14:53:29'),
(94, 'AYO junior', 'Technique', 'pont bascule', '2025-10-18 14:52:31', 'continuer', 'ampoule defectueuse', 'rapporter', 'preuves/8VMZFkATwYTKlqIZ7Fipp3Rd2xGqfQhI0jotz03o.jpg', '2025-10-18 14:53:31', '2025-10-18 14:53:31'),
(95, 'AYO junior', 'Technique', 'pont bascule', '2025-10-18 14:52:31', 'continuer', 'ampoule defectueuse', 'rapporter', 'preuves/YtGD7JyPhC7AbK5HhhnhZfH2ZDj01X0LxVF09krG.jpg', '2025-10-18 14:53:33', '2025-10-18 14:53:33');

-- --------------------------------------------------------

--
-- Structure de la table `departements`
--

CREATE TABLE `departements` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nom` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(2, '2025_09_28_000555_create_sessions_table', 1),
(3, '2025_09_28_050000_create_users_table', 1),
(4, '2025_09_28_050400_create_departements_table', 1),
(5, '2025_09_28_050500_create_anomalies_table', 1),
(6, '2025_09_29_115015_create_actions_correctrices_table', 1),
(7, '2025_09_29_115119_create_rapports_table', 1),
(8, '2025_10_03_000000_create_password_resets_table', 2),
(9, '2025_10_16_174459_add_user_id_to_anomalies_table', 3);

-- --------------------------------------------------------

--
-- Structure de la table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `password_resets`
--

INSERT INTO `password_resets` (`email`, `token`, `created_at`) VALUES
('sam@gmail.com', '$2y$10$eCtSvTSUcOsLIP9eNNkQEuanwKVUM96rufF5CPQXw5eI.oWyruciO', '2025-10-03 23:05:10'),
('luxe9152@gmail.com', '$2y$10$8A.BuhoTe7sFm7MV8Sd8E.0aWyhKJb2MDr6opdAldZIzjpTIcnET6', '2025-10-05 21:06:50');

-- --------------------------------------------------------

--
-- Structure de la table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `rapports`
--

CREATE TABLE `rapports` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `titre` varchar(255) NOT NULL,
  `contenu` text NOT NULL,
  `periode` varchar(255) DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `sessions`
--

CREATE TABLE `sessions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `department` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `department`, `created_at`, `updated_at`) VALUES
(3, 'KOMBATE Yendoukoa', 'luigsarmel@gmail.com', NULL, '$2y$10$ZkadoP77AMuekdrBfSHfUePLu1OM7VhSpu2eQg07bGtMS3k7DHolq', NULL, 'Pont Bascule', '2025-10-05 06:42:25', '2025-10-05 06:42:25'),
(10, 'astrid', 'astrid@gmail.com', NULL, '$2y$10$ITnne5YZEdRRB7yg4MTlo.OT0QD8PhCeuBvAz0Uuq00frI8Ummzsq', NULL, 'Administratif', '2025-10-07 06:16:42', '2025-10-07 06:16:42'),
(11, 'ADAMAH', 'holahomes@gmail.com', NULL, '$2y$10$njf8uaDpQRef0qyfcMTo1.fVB.3H6l.kpR53M1ozzeArR8.hEi7bq', NULL, 'Technique', '2025-10-07 08:19:33', '2025-10-07 08:34:11'),
(12, 'KOKOU', 'kokou@erestogo.com', NULL, '$2y$10$2VqwzPFAPw6IZTDnBB2Gk.cP2t5f3qnaDp0I.WA.xhn5HfM/mod82', NULL, 'Technique', '2025-10-07 08:55:19', '2025-10-07 08:55:19'),
(13, 'AKOU afi', 'afi@gmail.com', NULL, '$2y$10$f5fa03IEW01j.7uhwQKHTOl6IkchZkYJ3gY.1HoZ6pZOVuy4YNYnO', NULL, 'Administratif', '2025-10-07 16:54:50', '2025-10-07 16:54:50'),
(14, 'AYO junior', 'junior@gmail.com', NULL, '$2y$10$w4oexhC5kmvLtVm7i8TkH.Ow9IRI6/Hb7sBVvR4wsqwE9CF8NatrS', NULL, 'Technique', '2025-10-16 15:59:33', '2025-10-16 15:59:33'),
(15, 'ayivon mathieu', 'mat@gmail.com', NULL, '$2y$10$0/No6YlWzuz9WWfdbT.wZuNEigqGrlCqenpVoTLq3k1O6F7D00QS.', NULL, 'Logistique', '2025-10-17 08:09:13', '2025-10-17 08:09:13');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `actions_correctrices`
--
ALTER TABLE `actions_correctrices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `actions_correctrices_anomalie_id_foreign` (`anomalie_id`),
  ADD KEY `actions_correctrices_responsable_id_foreign` (`responsable_id`);

--
-- Index pour la table `anomalies`
--
ALTER TABLE `anomalies`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `departements`
--
ALTER TABLE `departements`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Index pour la table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Index pour la table `rapports`
--
ALTER TABLE `rapports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `rapports_created_by_foreign` (`created_by`);

--
-- Index pour la table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `actions_correctrices`
--
ALTER TABLE `actions_correctrices`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `anomalies`
--
ALTER TABLE `anomalies`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=96;

--
-- AUTO_INCREMENT pour la table `departements`
--
ALTER TABLE `departements`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT pour la table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `rapports`
--
ALTER TABLE `rapports`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `sessions`
--
ALTER TABLE `sessions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `actions_correctrices`
--
ALTER TABLE `actions_correctrices`
  ADD CONSTRAINT `actions_correctrices_anomalie_id_foreign` FOREIGN KEY (`anomalie_id`) REFERENCES `anomalies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `actions_correctrices_responsable_id_foreign` FOREIGN KEY (`responsable_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `rapports`
--
ALTER TABLE `rapports`
  ADD CONSTRAINT `rapports_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
