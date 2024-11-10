-- phpMyAdmin SQL Dump
-- version 4.4.15.1
-- http://www.phpmyadmin.net
--
-- Počítač: md407.wedos.net:3306
-- Vytvořeno: Ned 10. lis 2024, 13:19
-- Verze serveru: 10.4.31-MariaDB-log
-- Verze PHP: 5.4.23

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Databáze: `d237642_main`
--

-- --------------------------------------------------------

--
-- Struktura tabulky `files_alba_rosa_file_storage`
--

CREATE TABLE IF NOT EXISTS `files_alba_rosa_file_storage` (
  `id_file` int(11) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `code` varchar(255) NOT NULL,
  `fileName` varchar(255) NOT NULL,
  `startDate` datetime NOT NULL DEFAULT current_timestamp(),
  `endDate` date NOT NULL,
  `state` enum('0','1') NOT NULL DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Vypisuji data pro tabulku `files_alba_rosa_file_storage`
--

INSERT INTO `files_alba_rosa_file_storage` (`id_file`, `id_user`, `code`, `fileName`, `startDate`, `endDate`, `state`) VALUES
(1, NULL, '4gsCxChMD7SynHDoBnO5', 'makeitmeme_4brbk.gif', '2024-09-06 16:37:56', '2024-09-22', '0'),
(2, NULL, 'lTU9TNVdEJOs2rVwdqsN', 'makeitmeme_4jK7W.gif', '2024-09-06 16:44:58', '2024-09-19', '0'),
(3, NULL, 'XZMyGOI8CRU5YAf4w8At', 'makeitmeme_4brbk.gif', '2024-09-06 17:06:25', '2024-09-14', '0'),
(4, NULL, 'Mf8uCQo92NTGaKwE2rwB', 'main.go', '2024-09-06 17:21:49', '2024-09-21', '0'),
(5, NULL, 'Mf8uCQo92NTGaKwE2rwB', '', '2024-09-06 17:21:49', '2024-09-21', '0'),
(6, NULL, '8Up6Ke885PDN35M2Uk6u', 'main.go', '2024-09-06 17:26:02', '2024-09-27', '0'),
(7, NULL, 'OzKsu1mePKtqInJGQrT6', '-deer- shikanoko nokonoko koshitantan -deer-.zip', '2024-09-06 17:27:33', '2024-09-08', '0'),
(8, NULL, 'jUjq1vEnUDJUxtFgyTkO', '20240911_183438.jpg', '2024-09-11 19:31:44', '2024-09-11', '0'),
(9, NULL, 'jUjq1vEnUDJUxtFgyTkO', '20240911_183440.jpg', '2024-09-11 19:31:44', '2024-09-11', '0'),
(10, NULL, 'jUjq1vEnUDJUxtFgyTkO', '20240911_183445.jpg', '2024-09-11 19:31:44', '2024-09-11', '0'),
(11, NULL, 'jUjq1vEnUDJUxtFgyTkO', '20240911_183451.jpg', '2024-09-11 19:31:44', '2024-09-11', '0'),
(12, NULL, 'jUjq1vEnUDJUxtFgyTkO', '20240911_183457.jpg', '2024-09-11 19:31:44', '2024-09-11', '0'),
(13, NULL, 'jUjq1vEnUDJUxtFgyTkO', '20240911_183458.jpg', '2024-09-11 19:31:44', '2024-09-11', '0'),
(14, NULL, 'jUjq1vEnUDJUxtFgyTkO', '20240911_183500.jpg', '2024-09-11 19:31:44', '2024-09-11', '0'),
(15, NULL, 'jUjq1vEnUDJUxtFgyTkO', '20240911_183501.jpg', '2024-09-11 19:31:44', '2024-09-11', '0'),
(16, NULL, 'scvBsYa2DGcHmGotaX31', 'padělky.pdf', '2024-09-12 14:39:33', '2024-12-12', '0'),
(17, NULL, 'scvBsYa2DGcHmGotaX31', 'padělky.pdf', '2024-09-12 14:39:33', '2024-12-12', '0'),
(18, NULL, 'ErVnzsLHFj6SrXLfRhzT', 'řičanská kunda.pdf', '2024-09-12 19:40:55', '2024-12-12', '0'),
(20, NULL, '0PAhlP9sVIvwjqIDlYBE', 'fa_vvtop_2471216.pdf', '2024-10-10 07:36:12', '2024-10-11', '0'),
(21, NULL, '0PAhlP9sVIvwjqIDlYBE', '', '2024-10-10 07:36:12', '2024-10-11', '0'),
(22, NULL, 'fa_vtop', 'fa_vvtop_2471216.pdf', '2024-10-10 07:36:29', '2024-10-11', '0'),
(23, NULL, 'fa_vtop', 'ELE TABULKY KOVU.xls', '2024-10-10 08:08:53', '2024-10-11', '0'),
(24, NULL, 'fa_vtop', 'Den_dobrych_skutku_20241015_202815_0000.png', '2024-10-17 05:33:42', '2024-10-18', '0');

-- --------------------------------------------------------

--
-- Struktura tabulky `gamehub_private_alba_rosa`
--

CREATE TABLE IF NOT EXISTS `gamehub_private_alba_rosa` (
  `idgamehub` int(11) NOT NULL,
  `icon` varchar(256) DEFAULT NULL,
  `name` varchar(256) DEFAULT NULL,
  `buttons` varchar(9999) DEFAULT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Vypisuji data pro tabulku `gamehub_private_alba_rosa`
--

INSERT INTO `gamehub_private_alba_rosa` (`idgamehub`, `icon`, `name`, `buttons`, `description`) VALUES
(4, '<i class="fa-solid fa-game-console-handheld"></i>', 'Poppy Playtime! (All chapters)', '<button class="project-button" onclick="window.open(''../sn0wix/poppy-playtime.zip'', ''_blank'');"><i class="fa-solid fa-up-right-from-square"></i>ZIP</button><button class="project-button" onclick="window.open(''https://ko-fi.com/sn0wix'', ''_blank'');"><i class="fa-solid fa-sack-dollar"></i>Donate</button><button class="project-button" onclick="window.open(''../sn0wix/poppy-playtime.txt'', ''_blank'');"><img src="https://alba-rosa.cz/assets/txt-icon.png"> Docs</button>', 'Ever wanted to work at a toy factory? Well, now you can. But nothing comes without a catch...'),
(5, '<i class="fa-solid fa-game-console-handheld"></i>', 'Choo-Choo Charles!', '<button class="project-button" onclick="window.open(''../sn0wix/choo-choo-charles.zip'', ''_blank'');"><i class="fa-solid fa-up-right-from-square"></i>ZIP</button><button class="project-button" onclick="window.open(''https://ko-fi.com/sn0wix'', ''_blank'');"><i class="fa-solid fa-sack-dollar"></i>Donate</button><button class="project-button" onclick="window.open(''../sn0wix/choo-choo-charles.txt'', ''_blank'');"><img src="https://alba-rosa.cz/assets/txt-icon.png"> Docs</button>', 'A spider-like locomotive, that hunts you down across an island with a secret story, that you have to uncover yourself... \n'),
(6, '<i class="fa-solid fa-game-console-handheld"></i>', 'Raft', '<button class="project-button" onclick="window.open(''../sn0wix/Raft.v1.09.zip'', ''_blank'');"><i class="fa-solid fa-up-right-from-square"></i>ZIP</button>\r\n<button class="project-button" onclick="window.open(''https://ko-fi.com/sn0wix'', ''_blank'');"><i class="fa-solid fa-sack-dollar"></i>Donate</button>\r\n<button class="project-button" onclick="window.open(''../sn0wix/pRaft.v1.09.txt'', ''_blank'');"><img src="https://alba-rosa.cz/assets/txt-icon.png"> Docs</button>', 'A utopian world that you explore with your own raft.'),
(7, '<i class="fa-solid fa-game-console-handheld"></i>', 'Portal', '<button class="project-button" onclick="window.open(''../sn0wix/portal.zip'', ''_blank'');"><i class="fa-solid fa-up-right-from-square"></i>ZIP</button>\r\n<button class="project-button" onclick="window.open(''https://ko-fi.com/sn0wix'', ''_blank'');"><i class="fa-solid fa-sack-dollar"></i>Donate</button>\r\n<button class="project-button" onclick="window.open(''../sn0wix/portal.txt'', ''_blank'');"><img src="https://alba-rosa.cz/assets/txt-icon.png"> Docs</button>', 'Portal puzzle game.');

-- --------------------------------------------------------

--
-- Struktura tabulky `gamehub_public_alba_rosa`
--

CREATE TABLE IF NOT EXISTS `gamehub_public_alba_rosa` (
  `idgamehub` int(11) NOT NULL,
  `icon` varchar(256) DEFAULT NULL,
  `name` varchar(256) DEFAULT NULL,
  `github` varchar(256) DEFAULT NULL,
  `web` varchar(256) DEFAULT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Vypisuji data pro tabulku `gamehub_public_alba_rosa`
--

INSERT INTO `gamehub_public_alba_rosa` (`idgamehub`, `icon`, `name`, `github`, `web`, `description`) VALUES
(1, '&#9839;', 'Tic Tac Toe!', 'https://github.com/matkolo1/tictactoe/', 'https://matkolo1-tictactoe.glitch.me/', 'Basic Tic Tac Toe game with changeable size of the grid.'),
(2, '<i class="far fa-square"></i>', 'Purpix!', 'https://github.com/matkolo1/purpix/', '../purpix/', 'If you like semi-coding, you can try this one.'),
(3, '<i class="fa fa-hand-pointer-o"></i>', 'Popclicker!', 'https://github.com/pascaljura/poclicker/', '../popclicker/', 'You know cookie clicker? Try this one with our own characters.');

-- --------------------------------------------------------

--
-- Struktura tabulky `logins_alba_rosa_purpix`
--

CREATE TABLE IF NOT EXISTS `logins_alba_rosa_purpix` (
  `idlogins` int(11) NOT NULL,
  `idusers` int(11) DEFAULT NULL,
  `time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Vypisuji data pro tabulku `logins_alba_rosa_purpix`
--

INSERT INTO `logins_alba_rosa_purpix` (`idlogins`, `idusers`, `time`) VALUES
(1, 19, '2024-04-17 16:21:57'),
(2, 17, '2024-05-05 12:38:22'),
(3, 31, '2024-06-11 09:01:17'),
(4, 17, '2024-06-11 10:44:34'),
(5, 27, '2024-06-14 07:19:53'),
(6, 17, '2024-06-21 08:50:41'),
(7, 34, '2024-07-06 10:11:41'),
(8, 34, '2024-07-06 10:13:02'),
(9, 18, '2024-08-20 14:29:36'),
(10, 48, '2024-08-25 08:25:01'),
(11, 59, '2024-10-18 17:12:58'),
(12, 17, '2024-10-24 16:54:05'),
(13, 17, '2024-10-24 16:59:54');

-- --------------------------------------------------------

--
-- Struktura tabulky `logouts_alba_rosa_purpix`
--

CREATE TABLE IF NOT EXISTS `logouts_alba_rosa_purpix` (
  `idlogouts` int(11) NOT NULL,
  `idusers` int(11) DEFAULT NULL,
  `time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Vypisuji data pro tabulku `logouts_alba_rosa_purpix`
--

INSERT INTO `logouts_alba_rosa_purpix` (`idlogouts`, `idusers`, `time`) VALUES
(1, 34, '2024-07-06 10:12:06');

-- --------------------------------------------------------

--
-- Struktura tabulky `other_alba_rosa`
--

CREATE TABLE IF NOT EXISTS `other_alba_rosa` (
  `idother` int(11) NOT NULL,
  `icon` varchar(256) DEFAULT NULL,
  `name` varchar(256) DEFAULT NULL,
  `github` varchar(256) DEFAULT NULL,
  `web` varchar(256) DEFAULT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Vypisuji data pro tabulku `other_alba_rosa`
--

INSERT INTO `other_alba_rosa` (`idother`, `icon`, `name`, `github`, `web`, `description`) VALUES
(4, '<i class="fa-solid fa-qrcode"></i>', 'Qr code!', 'https://github.com/pascaljura/qr-code/', '../qr-code/', 'A simple QR code generator for all of your needs.');

-- --------------------------------------------------------

--
-- Struktura tabulky `other_alba_rosa_parlament`
--

CREATE TABLE IF NOT EXISTS `other_alba_rosa_parlament` (
  `idother` int(11) NOT NULL,
  `text` longtext NOT NULL,
  `aktivni` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Vypisuji data pro tabulku `other_alba_rosa_parlament`
--

INSERT INTO `other_alba_rosa_parlament` (`idother`, `text`, `aktivni`) VALUES
(1, '        <div style="display: flex;" id="footer-text">\n            <div class=" button-container">\n            Web vytvořil: Jiří Boucník<br>\n                Grafiku vytvořil: Marcel Mikula<br>\n                Grafiku upravila: Sarah Buchtová\n            </div>\n        </div>', 1),
(2, '        <div class="table-heading">\n  <b>&#x1F499;・Aktuálně・2024/2025</b>\n       </div>\n<div id="poster">\n         <a href="./assets/img/plakat_2.png" target="_blank">\n                <img src="./assets/img/plakat_2.png" id="responsive-image">\n            </a>\n            <a href="./assets/img/plakat_0.png" target="_blank">\n                <img src="./assets/img/plakat_0.png" id="responsive-image">\n            </a>\n       \n            <a href="./assets/img/plakat_1.png" target="_blank">\n                <img src="./assets/img/plakat_1.png" id="responsive-image">\n            </a>\n        </div>', 1),
(3, '        <div class="table-heading">\n  <b>&#x1F499;・Aktuálně・2024/2025</b>\n       </div>\n<div id="poster">\n         <a href="../assets/img/plakat_2.png" target="_blank">\n                <img src="../assets/img/plakat_2.png" id="responsive-image">\n            </a>\n            <a href="../assets/img/plakat_0.png" target="_blank">\n                <img src="../assets/img/plakat_0.png" id="responsive-image">\n            </a>\n       \n            <a href="../assets/img/plakat_1.png" target="_blank">\n                <img src="../assets/img/plakat_1.png" id="responsive-image">\n            </a>\n        </div>', 1);

-- --------------------------------------------------------

--
-- Struktura tabulky `photos_alba_rosa_popclicker`
--

CREATE TABLE IF NOT EXISTS `photos_alba_rosa_popclicker` (
  `idphotos` int(11) NOT NULL,
  `idusers` int(11) NOT NULL,
  `photos_1` longblob NOT NULL,
  `photos_2` longblob NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `school_alba_rosa`
--

CREATE TABLE IF NOT EXISTS `school_alba_rosa` (
  `idschool` int(11) NOT NULL,
  `icon` varchar(256) DEFAULT NULL,
  `name` varchar(256) DEFAULT NULL,
  `github` varchar(256) DEFAULT NULL,
  `web` varchar(256) DEFAULT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Vypisuji data pro tabulku `school_alba_rosa`
--

INSERT INTO `school_alba_rosa` (`idschool`, `icon`, `name`, `github`, `web`, `description`) VALUES
(1, '<img src="https://icons.iconarchive.com/icons/fa-team/fontawesome/128/FontAwesome-Egg-icon.png" width="25" height="25">', 'Egg Hunt!', 'https://github.com/pascaljura/egg-hunt/', 'https://velikonoce.sspbrno.cz/', 'Programme for the Easter event at Purkiňka High School in Brno from the school Parliament'),
(2, '<i class="fa-solid fa-bezier-curve"></i>', 'Bezier''s curve!', 'https://github.com/matkolo1/bezier/', '../bezier/', 'A simple program to calculate a bezier''s curve with unlimited points.'),
(3, '<i class="fa-solid fa-chalkboard-user"></i>', 'Parlament!', 'https://github.com/pascaljura/parlament', '../parlament/', 'Exclusive site for our school parliament to store all records.');

-- --------------------------------------------------------

--
-- Struktura tabulky `users_alba_rosa`
--

CREATE TABLE IF NOT EXISTS `users_alba_rosa` (
  `idusers` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `gamehub_private_access` enum('0','1') DEFAULT '0',
  `parlament_access` enum('0','1') DEFAULT '0',
  `purpix_level_1` int(11) NOT NULL DEFAULT 96,
  `purpix_level_2` int(11) NOT NULL DEFAULT 69,
  `purpix_level_3` int(11) NOT NULL DEFAULT 69,
  `purpix_level_4` int(11) NOT NULL DEFAULT 69,
  `purpix_level_5` int(11) NOT NULL DEFAULT 69,
  `purpix_level_6` int(11) NOT NULL DEFAULT 69,
  `purpix_level_7` int(11) NOT NULL DEFAULT 69,
  `purpix_level_8` int(11) NOT NULL DEFAULT 69,
  `purpix_level_9` int(11) NOT NULL DEFAULT 69,
  `popclicker_score` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=66 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `users_alba_rosa`
--

INSERT INTO `users_alba_rosa` (`idusers`, `email`, `username`, `password`, `gamehub_private_access`, `parlament_access`, `purpix_level_1`, `purpix_level_2`, `purpix_level_3`, `purpix_level_4`, `purpix_level_5`, `purpix_level_6`, `purpix_level_7`, `purpix_level_8`, `purpix_level_9`, `popclicker_score`) VALUES
(17, 'boucnik.jiri@gmail.com', 'Jiří Boucník', '$2y$10$WPWG/zy0b8uTLZLoGudAXuHeWEeDkUQXP8PF0aObhi2/vdlaO5Wlu', '0', '1', 96, 69, 69, 69, 69, 69, 69, 69, 69, 0),
(23, 'evitastrouth9963@outlook.com', 'QXbBkuawmtypYiV', '$2y$10$pJJ7VQD0TPeSWeBybT.ZBuqDYqTvMgwlS.GgRFgx1xe/NoaXgAmjq', '0', '0', 96, 69, 69, 69, 69, 69, 69, 69, 69, 0),
(25, 'matnipp8@gmail.com', 'HevyHellcat ', '$2y$10$SYmlpl/tiyylX1ba2wdJEOEQBPcQHb1WMxGgBblHxORJnMMRNRJwG', '0', '0', 96, 69, 69, 69, 69, 69, 69, 69, 69, 0),
(27, 'nonym2000@post.cz', 'Honza', '$2y$10$stmdZXXYptwzsZvTBUMV1O6yr340jfnI1/8SLh1wSxWDWEsjzqiL2', '0', '0', 96, 69, 69, 69, 69, 69, 69, 69, 69, 0),
(28, 'jeff.brown2938@yahoo.com', 'OGeEyjqhL', '$2y$10$29Gxgp5c8T.ovczgaZq/IeSEVzcAPzaKzvaWEuVZDlNH4ugluKCay', '0', '0', 96, 69, 69, 69, 69, 69, 69, 69, 69, 0),
(30, 'henrygeizx5912@gmail.com', 'CnWVzSQwLmNDr', '$2y$10$o/LaxfYW3MZ1Q0G846Hqf.v5.aF1hEefGCnXnE/dKEcdmqW9POy9u', '0', '0', 96, 69, 69, 69, 69, 69, 69, 69, 69, 0),
(32, 'christopher63hendersonhs1@outlook.com', 'FweDOsJGAbymHni', '$2y$10$NJgjmmtkjMtKGGyvzHRqxuE8Oscuap4kxHuLtjIPGdbhI1uImbMGa', '0', '0', 96, 69, 69, 69, 69, 69, 69, 69, 69, 0),
(33, 'stephen77rosevhn@outlook.com', 'NILFqcKTkroJe', '$2y$10$kvvO3pl5Ze68V9/cHOv8D.MP50TTcLvsGZoWozo8xODOHufa1bJsC', '0', '0', 96, 69, 69, 69, 69, 69, 69, 69, 69, 0),
(34, 'lukasJ@alba-rosa.cz', 'LukasJ', '$2y$10$DyjrONQQgv1KCxEjKwbjsuI/hzif.zWHDannHg45u7yWCDrAVDsqW', '0', '0', 96, 69, 69, 69, 69, 69, 69, 69, 69, 0),
(35, 'sedlacekmatej21@gmail.com', 'Mates', '$2y$10$Tj3zid/k085gXykOMsa90.AEL9vOTjS2HV0F.tVgXy6lBQVy8y4H.', '0', '0', 96, 69, 69, 69, 69, 69, 69, 69, 69, 0),
(36, 'rukinapingitz@outlook.com', 'VOMbUPZatozT', '$2y$10$WNAch53MgfhVVn5Kzi5FhOBO3Ffwl4GBn8zZyA0SPpv6JgQVKbB7.', '0', '0', 96, 69, 69, 69, 69, 69, 69, 69, 69, 0),
(37, 'lukas.filipek10@gmail.com', 'Lukas', '$2y$10$FXX.LtSw1nKzVRwf2gUPY.0U3OPaTPJLAmGHLg94msR2sQbUEz5We', '0', '0', 96, 69, 69, 69, 69, 69, 69, 69, 69, 0),
(38, 'good.samantha2811@yahoo.com', 'UhcHDJEmpIPOQ', '$2y$10$7o1Uy8NwPE/0gFhFlT4ii.Ua8ucZZjYpIchCs2NqfCJcmfMgO1.8W', '0', '0', 96, 69, 69, 69, 69, 69, 69, 69, 69, 0),
(40, 'rwhiteheadlr49@gmail.com', 'BYliEHNJMVoQSTx', '$2y$10$W1zvEMtAuawFolXV5rreJO2AvWu/VZhxJ80JCaYBT55wInVgV..pi', '0', '0', 96, 69, 69, 69, 69, 69, 69, 69, 69, 0),
(41, 'danny_whitecfir@outlook.com', 'LsaBSNQG', '$2y$10$ucpxz46MLu6CeS2iwHoKCeA06f8Gvxb.QkYR37AQTzrkUcwICz.wS', '0', '0', 96, 69, 69, 69, 69, 69, 69, 69, 69, 0),
(42, 'hyksleihl2001@gmail.com', 'CKRGHZlfWYnACz', '$2y$10$hv8W4sftnUxZUAYc.wzfaOudWRI08sdMF4aPthx8DJSQ7LSRTO0qi', '0', '0', 96, 69, 69, 69, 69, 69, 69, 69, 69, 0),
(43, 'tbeasleymd33@gmail.com', 'EsdtWQZy', '$2y$10$cSwVqN.HkrvWjyj4P3RzYeAORj/crwO4Cvs66VvuaCbjUWAs9GIoe', '0', '0', 96, 69, 69, 69, 69, 69, 69, 69, 69, 0),
(44, 'steflini340@gmail.com', 'DvYseAmZ', '$2y$10$g0Ex0vC9YqeGFkBkW.HEYOemv.7Q7aWs5do73IeOoHUr2Igibi8Fy', '0', '0', 96, 69, 69, 69, 69, 69, 69, 69, 69, 0),
(46, 'grovermasonmi28@gmail.com', 'MdvRoKPE', '$2y$10$oElvu2rRWrdy4H0t2X0Jn.L5lwgnINRhD/snGHpmN3p6u9z8lSHjO', '0', '0', 96, 69, 69, 69, 69, 69, 69, 69, 69, 0),
(47, 'MerriHermann612@aol.com', 'PjZAJloEFkNV', '$2y$10$K9NR/T0Lq/LH8mEO93pU7eHHlATtNj9EBnh7kCTqrlYGqwRJC2jUe', '0', '0', 96, 69, 69, 69, 69, 69, 69, 69, 69, 0),
(48, 'FilipkovaVe@seznam.cz', 'Veronika', '$2y$10$1XvZAEPJU3yuwiSvktafleV0GedZ8Tfw/A/pNkzZ0UA8ilh.AY39G', '0', '0', 96, 69, 69, 69, 69, 69, 69, 69, 69, 0),
(49, 'robertsg_cooleydi@outlook.com', 'WTYfIMdOz', '$2y$10$jIeLdDQd1a1lswFaI45QvulLiaA41QhQOorgHWoKAl8BmMDfbYG5q', '0', '0', 96, 69, 69, 69, 69, 69, 69, 69, 69, 0),
(51, 'gaylenv_whittonmw@outlook.com', 'QQAZdXLz', '$2y$10$FQUVWug9d3xL1R3CamyHJuvButGnomvBKE7K8dIYEyRMTHMO2rSmK', '0', '0', 96, 69, 69, 69, 69, 69, 69, 69, 69, 0),
(52, 'codegemjava@gmail.com', 'TestDoNotBan', '$2y$10$.hYiG36pY4w5Oq.DfYn2DuCEmbIJs4lz1Ope4AwALWp5NnF9B0JVy', '0', '0', 96, 69, 69, 69, 69, 69, 69, 69, 69, 0),
(53, 'frank62bordersfpx@outlook.com', 'IBfaMprsmVLuT', '$2y$10$ZB3rC.Kt/xnfcHgWQ2NZCuMipcKNgvQ0kIRuYYW5ACNK2EMixLYgK', '0', '0', 96, 69, 69, 69, 69, 69, 69, 69, 69, 0),
(54, 'robert_grayzadv@outlook.com', 'HwifEsLpzYGCg', '$2y$10$HG71DD3KGrCjUXeaSB1JYurL893MrbALPc6v/RSfJinBfBF3iGRN6', '0', '0', 96, 69, 69, 69, 69, 69, 69, 69, 69, 0),
(55, 'johnxj_rainwaterkj@outlook.com', 'AFjQWHJmsyO', '$2y$10$pvOLpEqR8AvWa35gNGv9Yuv0QNWP7n74kOEVfPDtEWcZ6uNflgKSO', '0', '0', 96, 69, 69, 69, 69, 69, 69, 69, 69, 0),
(56, 'nchandlerdw24@gmail.com', 'OVTcSCyZg', '$2y$10$mFrF8qd0d27depzdNx0.9./F/f6dJSReaGDeIPcpuLprEQmbtoFZy', '0', '0', 96, 69, 69, 69, 69, 69, 69, 69, 69, 0),
(57, 'biok_amusi17@yahoo.com', 'SztANOtZQ', '$2y$10$189kQBffL382LdetCkYCqOhFyiY.Xz0YkXKeUJhqs4whwv9Asf/YG', '0', '0', 96, 69, 69, 69, 69, 69, 69, 69, 69, 0),
(58, 'stevensdorrisco1980@gmail.com', 'FloebifaMotK', '$2y$10$D8I4pXcPKpMEhjVXsa/U4edQcDtbrfSs9Df2o0ZZ6YqMGB8Q5nm4i', '0', '0', 96, 69, 69, 69, 69, 69, 69, 69, 69, 0),
(59, 'jonik19837@avzong.com', 'Sigma', '$2y$10$uQehL/8l77XfTHbMit2pn.6gWz2mytkXhoYNrF7OYJ4Hqx3Qm1OFi', '0', '0', 96, 69, 69, 69, 69, 69, 69, 69, 69, 0),
(60, 'levydell5947@gmail.com', 'LxnjuvlJKO', '$2y$10$XpX5QRytzzrInygO/YacfOz6PImLsBCPaEpJGUGPwBKXLYgnf8DJS', '0', '0', 96, 69, 69, 69, 69, 69, 69, 69, 69, 0),
(61, 'almirabeckerdu@gmail.com', 'XxHXJGMg', '$2y$10$77hhSWCb5rzUjjqodBz/P.VvkFs9shkbqHF3NRjaFYaDpM4ClXvNq', '0', '0', 96, 69, 69, 69, 69, 69, 69, 69, 69, 0),
(62, 'kynjoaggrlryvx@yahoo.com', 'SlnGnnGPZOchUJ', '$2y$10$P7FYiDwUwUuZv.tnlOLL1Oqy9JJAPRouueJXf8HWN51b5aVg0viQq', '0', '0', 96, 69, 69, 69, 69, 69, 69, 69, 69, 0),
(64, 'maninnodemo@yahoo.com', 'MdEsIdkfq', '$2y$10$9kyR24I8kDUBnZ5itPvO7Ov3zzH7l/yv1j.7Pv3YgRBIlDUUYn52q', '0', '0', 96, 69, 69, 69, 69, 69, 69, 69, 69, 0),
(65, 'vanek.fanda@centrum.cz', 'František Vaněk', '$2y$10$cYniwL.XO2O/76gOxBU4zuGb5SjdksdY0BQzh0sBk4gVHUA7rO3dK', '0', '1', 96, 69, 69, 69, 69, 69, 69, 69, 69, 0);

-- --------------------------------------------------------

--
-- Struktura tabulky `zapis_alba_rosa_parlament`
--

CREATE TABLE IF NOT EXISTS `zapis_alba_rosa_parlament` (
  `idzapis` int(11) NOT NULL,
  `idusers` int(11) DEFAULT 1,
  `datum` date NOT NULL,
  `cislo_dokumentu` varchar(255) DEFAULT NULL,
  `zapis` longtext NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=69 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Vypisuji data pro tabulku `zapis_alba_rosa_parlament`
--

INSERT INTO `zapis_alba_rosa_parlament` (`idzapis`, `idusers`, `datum`, `cislo_dokumentu`, `zapis`) VALUES
(1, 17, '2023-11-01', '18.02.34', '//Čtvrtletní schůze s panem ředitelem//=**Návrhy pro zlepšení chodu školy ze strany studentů:**=- Nabíječky=-- Projekt úspěšný, požadavek na zvýšení počtu nabíječek.=- Automatické propustky při zrušené výuce=-- Nelze, systém není propojený s EduPage.=- Sodobary=-- Výměna sodobarů v ostatních patrech, aby byly jako ve čtvrtém =patře=-- Sodobary jsou pronajaté, záleží na domluvě s pronajímateli.=- Zrcadlo na chodbě=-- Skleněné ne, ale alternativa hliníkové folie je možná, vytipovat více =možností na jejich umístění.=- Beseda s hejtmanem JMK=-- Je možná, záleží na domluvě studentů.=- Slevové kupóny do kantýny=-- Lze vyzkoušet, kupóny by musely mít nějaký systém (aby nedošlo =k jejich zfalšování). Nutné prodiskutovat s provozovatelem, jako =návrh i s učiteli ekonomiky.=- Lepší káva v Delikomatu=-- V automatech není káva, jedná se pouze o kávovinovou směs. Lze =se poptat, ale cena by byla vyšší. Z důvodu umístění automatu ve =škole by však nešlo o kávu. =- Stížnost na chování personálu jako uklízečky a kuchařky=-- Individuální případy se musí řešit přes třídního učitele. Tolerantnější =přístup ze strany kuchařek především na konci směny bude řešen =přes správu budov.=- Halloween=-- Různé pohledy ze strany učitelů na masky studentů ve výuce o=Učitel nemá právo kohokoliv kárat za to, jak je oblečen, konkrétní =případy řešit přes třídního učitele.=- Potíže se školním internetem a Wi-Fi=-- Škola je propojená s VUT, tam problém není, problém je v =metalických rozvodech, výměna za optické kabely stojí mezi 3-5 =miliony. Aktuálně se řeší tepelný výměník. Příští prázdniny se teprve =bude řešit internet a ty následující serverové řízení.=- Omezení Wi-Fi v hodinách, nefunguje ve výuce=-- Omezeno, aby žáci nebyli na sociálních sítích. Učitelé mají ve výuce =zadávat jen takovou práci, aby byla v danou chvíli technicky =zvládnutelná. =-- Pokud to i přesto učitel vyžaduje, konfrontovat, řešit s TU nebo =s ŘŠ. =- Rozbité rozhlasy a jiná technika v učebnách=-- Na sharepointu je systém pro zadávání požadavků, je třeba učitele =na technický problém upozornit. V PC učebnách existuje i papírová =forma, která by měla být pravidelně kontrolována a požadavky =řešeny přes IT servis.=- Sportovní zařízení=-- Minulý rok byla velká investice do posilovny =-- Rekonstrukce venkovního areálu se pohybuje okolo 15 milionů, finančně =je to nyní pro školu neúnosné. =-- Jednou za rok prochází areál bezpečnostní kontrolou, opravují se pouze =nutné záležitosti.=**Diskuze:**=- Obor kybernetika - odešel učitel maturitního předmětu (obava studentů o =maturitní zkoušku)=-- Obava není na místě, učitelé stihnou látku probrat. S novými obory =se mění ŠVP, upravují se maturitní požadavky. =- Změna učitele češtiny ve třetím ročníku, obava o čtvrtý maturitní ročník=(každý učí jiným způsobem)=-- Učitelé vědí, co potřebujete k maturitě, přístup může mít každý jiný. =- Dlouhodobá absence paní učitelky Altrichterové.=-- Zajištěn odborný zástup.=- Noví učitelé neměli dostatek informací k chodu školy=-- Učitelé prochází adaptací, dvouletou průpravou a různými semináři.=- Požární poplach – noví učitelé nevěděli, co mají dělat. =-- Zkoušku požárního poplachu je třeba opakovat za účelem hladkého =průběhu. Další zkouška bude na jaře.=-- Informace k požárnímu poplachu jsou uvedené na nástěnkách. =- Revize elektro zařízení.=-- Četnost revizí záleží na poptávce, obvykle dvakrát ročně. =- 1. patro – na WC pípá poplach.=-- Nahlásit panu Šístkovi.=- Špatné časy na PC v učebnách.=-- Řešit s Petrem Čížkem.=- Co škola umožňuje zletilým žákům?=-- Škola má povinnost zajistit vždy dohled nad všemi studenty, i těmi =zletilými.'),
(2, 17, '2023-10-03', '18.02.34', '**Volby do funkcí školního parlamentu**=- Zapisovatel: Ondra Šteffan (náhradník Kristýna Karaivanova) – vždy =po setkání rychlá kontrola, ten se odešle na Messenger, GODE =vytiskne a Adam Abbod dá na nástěnky a zveřejní se na Instagramu=**Adaptační program Parlamentu – návrhy:**=- Laser game=- Zoo=- Bowling=- Společná venkovní akce=**Halloween**=- Zařizuje: Kristýna Karaivanova (31.10.2023)=**Nabíječky **=- Plakát roznosit po škole + dát na nástěnky=**Nábor členů do školního Parlamentu (Týká se pouze zatím prváků):**=- Čtvrtek 05.10.2023=- V časech 08:00 – 10:45 a 10:55 – 13:30=- 2 skupiny:=-- Martin Sedlář L3, Adam Abbod V3A + Eli (náhradník)=-- Ondra Šteffan V4B, Kristýna Karaivanova S4B'),
(3, 17, '2023-11-17', '18.02.34', '**Zhodnocení akce Halloween**=- Bonbóny uspěly=- Účast větší než minulý rok=**Nové výbory parlamentu**=- Nový výbor IT=- Spojka mezi námi a panem Čížkem=- Adam Abbod=- Nový výbor komunikace (vyjednávání)=- Spojka mezi učiteli a námi=- Bohuslav a Jakub=**Stanovení plánu akcí**=- Bude k dispozici na teams=- Řeší výbor plánování akcí=- Do 14.11.=**Pozvání pana hejtmana**=- Beseda 50-100 lidí=- V řešení omezený počet míst =- Formou přihlášení=**Zrcadlo**=- Musí se najít místo=**Časy na počítačích**=- Řeší výbor IT=**Den otevřených dveří**=- Zájemci na dobrovolníky =- Řeší Martin s paní Klobásovou=**Purples**=- Zájemci na dobrovolníky=- Řeší Eli s Purplesem=**Rozhlas**=- Jen důležité akce (odhlasováno)=- Řeší Sarah=**Stále v řešení:**=- Mikuláš =- Promyslet, kdo by chtěl jít=- Logo Parlamentu=-- Bude nápis Purkyňova (odhlasováno)=- Slevy v kantýně'),
(4, 17, '2023-10-10', '18.02.34', '**Halloween**=- Skupinová fotka v outfitech=- návrh data - 31.10.=- Halloween bude jako minulý rok, o velké přestávce bude fotka, může být =soutěž, abychom přilákali více lidí=**Adaptační program**=- Návrhy – Bowling, Zoo, venkovní akce=- Hlasování – Bowling =**Výbory**=- Odhlasovaní lidé: =- Vedoucí marketingu – Eli Dvorcova=- Předseda – Richard Skoumal=- Místopředseda – Martin Sedlář=- Sběr nápadů – Adam Abbod=- Zapisovatel – Ondřej Šteffan=- Organizátoři akcí – Kristýna Karaivanova, Eli Dvorcova=**Majáles**=- Zajímavé pro parlament=- Je potřeba najít lidi co to organizovali minulý rok=**Mikiny pro parlament**=- Je potřeba návrh=- Bude se to nosit?=- Typ mikiny?=**Logo parlamentu**=- Změna=- Kačenka zůstává=- Barvy se mění na barvy školy (odstíny modré) =**Plán na tento školní rok**=- Je potřeba odevzdat vedení co se má stát'),
(5, 17, '2023-12-12', '18.02.34', '**Tři králové**=- Potřeba další lidi=- Chodí po třídách, vybírají se peníze na charitu=- Angažování:=-- Kačmařík=-- Kropáček=-- Pokorná=-- Němec=-- Buchtová=-- Brandeis=-- Němeček=- Termín: pátek 05.01.2024=**Vánoční program**=- Termín: pátek 22.12.2023=- Alternativa k třídnímu programu=- Místnosti:=-- Kino: Buchtová, také nahoře=-- Klub kávy a her: Karaivanova, U51=-- Deskové hry: Tesař, před aulou=- Dodatek: Volejbal=- Paní učitelka vyřeší veřejnou zprávu, třídní učitelé kontaktují =organizátory=**Výlet za odměnu na konci roku pro 10 nejaktivnějších členů parlamentu**=- Bítov=**Mikiny Parlamentu**=- Ano, chceme=- Propagační materiál a krásná vzpomínky=- Kapuce, klokan, unisex střih, malé logo, černá=- Pracovní tým: Pokorná, Buchtová=**Moderátor příští schůze**=- Elie'),
(6, 17, '2023-11-14', '18.02.34', '**Úkoly z minulého zasedání**=- Místo na zrcadlo: bude dále projednávání=- Slevy v kantýně: v běhu =**Střídání moderátorů schůzí**=- Schváleno=**Nabíječky**=- Čeká se na revizi=**Logo parlamentu**=- Schválena nová verze=**Vlastní znělka před hlášením parlamentu**=**Rozhovory**=- Sedlář, Tesař=**Nové podměty**=- Vysadit dveře u schodů=- Posezení v krátké chodbě ve 4. Patře =**Plán parlamentu na školní rok**=**Mikuláš**=- Barek, Karaivanova, Matula, Abbod, Sokolová, Kropáček'),
(7, 17, '2023-10-17', '18.02.34', '**MS Teams**=- Oficiální komunikační kanál=- Úpravy a přidání docházky, nápadů na řešení atd.=**Multimediální den**=- Odměna za aktivní účast v parlamentu=- Žáci ve skupině určené na to=**Logo**=- Návrhy nového loga parlamentu=- Hlasování na messengeru=**Halloween**=- Organizační záležitosti=- Výzdoba=- Instagramové hlasování=**Nabíječky**=- Osvědčily se a plánuje se navýšení jejich počtu=- Dokoupení nabíječek=**Discord**=- Aktualizace rolí a textových kanálů=Plán školy (kabinety učitelů)=- Jednoduchý a přehledný plán, kde jsou kabinety učitelů=- Grafická mapa na nástěnku + web=**Grafik**=- Vytváření plakátů a další práce, potřeba více lidí=- Nábor grafiků u učitelů=- Známka navíc za plakát (pro grafiky)=**Ostatní**=- Nové vybavení venku=- Martinova motivační řeč=- Propustky při zrušených hodinách=- Málo viditelné nástěnky=- Tiché nebo nefunkční rozhlasy=- Stránky parlamentu (web)=- Hlasování o mikinách'),
(8, 17, '2023-12-19', '18.02.34', '**Řešení vánočního dne**=- Místnosti=- Dozory=- Hlášení tříd a jednotlivců u MAPA=**Tři králové**=- 1. skupina: Kačmařík. Buchtová, Němec=- 2. skupina: Brandejs, Pokorná, Abbod=- Náhradníci: Kropáček'),
(9, 17, '2023-10-24', '18.02.34', '**Adaptační program parlamentu**=- Řešení programu=**Schůze s panem ředitelem**=- 01.11.2023 ve středu=**Návrhy studentů**=- Občas písničky místo zvonění=-- (odhlasováno valnou většinou, 24/27)=- Propustky ve studovně=-- Pravděpodobně ne=- Lepší/ silnější Wifi=-- Problém s velkým počtem lidí, byla by potřeba výměna celé sítě a v =hodině je limitována=- Purkyňka Merch ano/ne=-- Odhlasováno důkladnější probrání (20/27)=- Automatické propustky=- Opravit hodiny =-- Ve třídách i na počítačích=- Zrcadlo na chodbě=-- Bylo by tam logo=**Halloween**=- Výzdoba, příspěvek a příběh na Instagram=**Časopis**=- Lidé by si to nečetli=- Mnoho práce=**Logo parlamentu**=- Sbírání návrhů, odhlasuje se na příští schůzi'),
(10, 17, '2023-09-25', '18.02.34', '**Přivítání, docházka**=**Doplnění členů školního Parlamentu + správa komunikačních platforem (oficiální komunikační platformou jsou Teamsy, neoficiální je Messenger)**=**V příštích setkáních proběhnou volby do funkcí školního Parlamentu **=**Četnost setkání + termín + celková doba trvání setkání – 1 × týdně, úterý, vždy od 14 hodin (většinou studovna), doba setkání bude vždy maximálně do 15 hodin**=**Naplánovat setkání s ŘŠ**=**Naplánovat adaptační program**=**Vytvoření plánu školního Parlamentu na školní rok – podání návrhů:**=- Místo zvonění písničky=- Tematické dny (historický, formální…)=- Prohození oborů (výuka – na jeden den)=- Halloween=- Valentýn=- Společné snídaně=- No backpack day=- Podcast – různé rozhovory=- Sportovní dny=- Listování=- Autorské čtení=- Barevný týden=- Ples=- Výměna učitel/žák=- Tři králové=- Kvízy=- Trička'),
(11, 17, '2023-11-28', '18.02.34', '**Moderátoři parlamentní schůze**=- Příští schůze Kropáček=**Mikuláš**=- Vytvoření rozvrhu, organizace: Martin Němec=**Prohození oborů**=- Plánuje Kačmařík=**Tři králové**=- 5. 1. 2024=- Plánuje Kačmařík=**Den učitelů**=- Výměna učitel <-> žák=- Březen=**Zrcadlo nebude**=**Vánoční program**=- Místo programu se třídou=-- Různé místnosti:=-- VR=-- Konzole=-- Filmy=-- Kafe=-- a další=- 21. 12. 2023'),
(12, 17, '2023-10-31', '18.02.34', '**Adaptační program parlamentu**=- Zhodnocení=**Poznámky na ředitele, schůze 01. 11. 2023**=- Chování zaměstnanců=- Kvalitnější káva=- Pozvat hejtmana (Richard)=- Automatické propustky při zrušené výuce=- Funkčnost rozhlasů=- Zrcadlo na chodbě=- Aktualizace sifonu=**Web parlamentu**=- Zápisy ze schůzí=**Nové volby na vedení parlamentu**=- Místopředseda: Martin =- Místo místopředseda: Eli'),
(42, 17, '2024-01-09', '18.02.34', '**Mikiny**=- Co, jak a proč. =- Do kdy, cena=- Opravdu mikiny? Nebo trika?=**Schůze s ředitelem**=- Co na ní. Řešení podmětů=**Výlet za odměnu**=- Kam, počet lidí a obecné zahájení řešení=**Nápad Ellie**=- Zmínění projektu=**Valentýn**=- Kdo bude stříhat srdíčka a jak to celé bude'),
(43, 17, '2024-01-23', '18.02.34', '**Valentýn**=- Kde budou schránky, jak budou upevněny?=- Jak to bude probíhat a co je potřeba nachystat?=**Mikiny**=- Velikost a pozice loga'),
(44, 17, '2024-01-30', '18.02.34', '**Dořešení akce Valentýn**=- Sbíraní valentýnek středa 31. 1. – úterý 13. 2.=- Roznášení valentýnek 14.2.=**Setkání s panem ředitelem**=- Student se poptají ve třídách na dotazy na pana ředitele=- Sběr podnětů – Adam Abbod=**Mikiny**=- Přijdou nové upravené vzorky=- Budou se muset zjistit velikosti=**Project**=- V řešení se školním psychologem=- Více přenést mezi studenty=- Budou se řešit plakáty, prozatím návrhy=**Debatní kroužek**=- Prezentování, vystupování před lidmi, udržení pozornosti diváků, …=- Vedoucí pan učitel Dušek=- Zjištění zájmu=- Nejlépe cca 20 lidí=- Pravděpodobný termín úterý=- Projednání s panem ředitelem na čtvrtletním setkání'),
(45, 17, '2024-02-13', '18.02.34', '**Podměty pro ředitele**=- Stěny mezi pisoáry=- Funkční zámky na záchody=- Testy online mimo počítačové učebny=- Mikrovlnky před obědem =- Stránky na telefonu (jejich obsah je jiný než na PC)=- Debatní kroužek =- Posezení na chodbách ve 4. patře=- Automat na kávu ve 2. patře=**Mikiny a trička**=- Značení loga=**Valentýn**=- Vyhodnocení akce a příprava na rozdávání'),
(46, 17, '2024-02-20', '18.02.34', '**Zvolení osoby na psaní zápisů**=**Zhodnocení akce Valentýn**=**Řešení návrhu prohození učitele s žákem**=- Má to smysl? Budou s tím souhlasit učitelé?=**Velikonoce**=- Řeší Martin Němec =- Jaké budou hádanky? A jaká budou místa?=- Datum 27. 3.'),
(47, 17, '2024-02-27', '18.02.34', '**Velikonoce**=- Stále v řešení místa na QR kódy=- Ceny za první tři výherní místa=- Plakát'),
(48, 17, '2024-03-12', '18.02.34', '**Velikonoce**=- Řešení aplikace a vše okolo=- Dořešení míst na QR kódy=- Dodatečné úpravy plakátu=- Dořešení nápověd na místa '),
(49, 17, '2024-03-26', '18.02.34', '**Velikonoce**=- Roznesení QR kódů=- Rozhlášení rozhlasem a EduPage=-- Kdo bude hlásit=- Stránka hotová=-- Vzhled dortu=- Vyhlášení výherců=-- Výhry=-- Kdo a kde bude předávat ceny=**Krajský Studentský sněm**=- Řád parlamentu=**Výlet pro parlament**=- Informace z loňského roku=- Kdo, kam, kdy a na jak dlouho by se jelo=- 2. učitel=**Parlament merch**=- Kolik lidí a kdo chce=- Cena'),
(51, 17, '2024-04-02', '18.02.34', '**Velikonoce**=- Zprávy výhercům=-- Rozhlas=- Předání výher =-- Sborovna 9:40 3.4. (musí být přítomen učitel)=- Nekompatibilita ios=**Setkání s ředitelem**=- Podněty od žáků=- Uzavírání známek pro čtvrťáky: důvod=**Mikiny** =- Uzavření výběru=**No backpack day**=- Omezení na velikost aktovky, 9.5.=**Výlet za odměnu**=- 1 nebo 2 dny=- Bítov=- kolem 21.6.=**Moderátor na 8.4.**'),
(52, 17, '2023-11-07', '18.02.34', '**Zhodnocení akce Halloween**=- Bonbóny uspěly=- Účast větší než minulý rok=**Nové výbory parlamentu**=- Nový výbor IT=- Spojka mezi námi a panem Čížkem=- Adam Abbod=- Nový výbor komunikace (vyjednávání)=- Spojka mezi učiteli a námi=- Bohuslav a Jakub=**Stanovení plánu akcí**=- Bude k dispozici na teams=- Řeší výbor plánování akcí=- Do 14.11.=**Pozvání pana hejtmana **=- Beseda 50-100 lidí=- V řešení omezený počet míst =- Formou přihlášení=**Zrcadlo**=- Musí se najít místo=**Časy na počítačích**=- Řeší výbor IT=**Den otevřených dveří**		=- Zájemci na dobrovolníky =- Řeší Martin s paní Klobásovou=**Purples**=- Zájemci na dobrovolníky=- Řeší Eli s Purplesem=**Rozhlas**=- Jen důležité akce (odhlasováno)=- Řeší Sarah=**Stále v řešení:**=- Mikuláš =- Promyslet, kdo by chtěl jít=- Logo Parlamentu=- Bude nápis Purkyňova (odhlasováno)=- Slevy v kantýně'),
(53, 17, '2024-04-09', '18.02.34', '**Schůzka s panem ředitelem**=- 16.4.=- Podměty pro pana ředitele=-- No Backpack Day =-- Známkování DÚ=-- Žíněnky v tělocvičně=-- Rohová sedačka u kačenek=-- Zpětná vazba z minule=**No Backpack Day**=- Omezení rozměrů=-- max. 0.7 x 0.7 x 0.7 m =-- *Pokud vlastníte platný řidičský průkaz třídy C jsou povoleny i větší rozměry. **Nutno doložit***=**Participativní projekt na zvelebení školy**=- Návrhy studentů na úpravu školy skrze formulář=- Výhra 30 000Kč na realizaci projektu=- Autor vítězného návrhu bude odměněn=- Podněty:=-- Posezení na parapetu 4.p=-- Výběh pro studenty=**Parlament merch**=- Výběr peněz, oznámení ceny =**Moderátor příští schůze**=- Sarah Buchtová='),
(54, 17, '2024-04-16', '18.02.34', '//Čtvrtletní schůze s panem ředitelem//=**Přivítání a úvodní slovo pana ředitele**=**Příští týden od pondělí do pátku proběhne hodnocení vzdělávacího procesu, vše bude anonymní, přes Google dotazník, bude na to vyčleněna 1 vyuč. hodina**=**Návrhy pro zlepšení chodu školy ze strany studentů:**=- Změna posunu uzavření klasifikace pro 4. ročníky =-- Pan ředitel je na služební cestě, nebyl by schopen zajistit zákonné podepsání vysvědčení=- NO BACKPACK DAY (plánováno na 9.5.) =- Ano, akce se může uskutečnit, avšak pouze za předpokladu omezení velikosti různých náhrad školní tašky =- Stížnost na zadávání DÚ =-- Vzdělávací systém probíhá ve škole, vše je ale na dohodě vyučujícího a studenta=-- Student nesmí být hodnocen stupněm 5 za práci, kterou neudělal=- Požadavek na novou sedačku ve druhém patře =-- Možná ano, je zde jednání s firmou Thermo Fisher; chovejme se ale k sedačce slušně =-- Kolem 1300 studentů je opravdu velká zátěž=- Parapety ve čtvrtém patře -- možnost je nějak zpevnit, aby se na nich dalo sedět -- ne, nejsou koncipovány k sezení=- Žíněnky v TV + stroje v TV (obnova) =-- Říct vyučujícím TEV, aby zadali požadavek dál=- TSP seminář zde na SŠ =-- Toto zajišťují samy VŠ (např. MUNI), SŠ má za cíl připravit na maturitu=- Ekonomika =-- Již ve třetím ročníku vypracováváme maturitní otázky, které se ale příští rok změní (vývoj ekonomiky) =-- Studentka se domluví se svojí třídou, jak budou dále s vyučujícím (popř. TU) komunikovat =- EXPO =-- Nemohla by škola zajistit ubytování? Brzké ranní vstávání (i ve 2:00 hod.), špatná doprava na D1 =-- SRP by mohlo příští rok zajistit ubytování (přenocování v místě) a zeptat se paní uč. Tomanové, paní zástupkyně Führlingerové=- Praktická maturita =-- Ve čtvrtém ročníku je hodně teorie, která se probírá ve velmi krátkém časovém rozmezí =-- Nejde nějaká aktualizace školních programů? Když žák nastoupí do určitého školního programu, musí s ním i maturovat. Ale postupně dochází k aktualizacím jednotlivých oborů. Neučíte se pro maturitu, ale pro budoucí profesi. Případné dotazy řešit s paní učitelkou Vávrovou. =- Proč se učíme cizí jazyky?= Jsme součástí EU + další rozvoj osobnosti=- Proč maturujeme z EKO?= Viz program ŠVP=- Opětovné řešení problematiky online testů mimo výuku =- Bude dotazník Hodnocení vzdělávacího procesu i pro čtvrté ročníky?=-- Až příští rok. Maturanti mohou pomoci s tvorbou otázek.=- Fiktivní firma =-- příští rok bude víc studentů, jak to bude s organizací? Organizaci si zajišťují vyučující EKO. =- Proč nesmím jako žák prvního ročníku jet na lyžařský výcvik se snowboardem?=-- Zeptat se vyučujících TEV, bezpečnost především.='),
(55, 17, '2024-04-23', '18.02.34', '**No Backpack Day**=- Rozměry jako v MHD=- Focení=**Rozpočet 30 000Kč na zvelebení školy**=- Rozeslání formulářů na návrhy=- Výtěžný návrh bude odměněn částkou 1000Kč=- Návrh musí být rozumný a přijatelný vedením školy=**Správa sociálních sítí**=- Vedení: Kačmařík=**Pomoc seniorům na Střední škole informatiky, poštovnictví a finančnictví Čichnova**=- Akce organizovaná parlamentem na Střední škole informatiky, poštovnictví a finančnictví Čichnova=- Chtějí pomoc od 2 členů našeho parlamentu=- Buchtová, Kačmařík'),
(57, 17, '2024-04-30', '18.02.34', '**Proběhlá akce pro důchodce na Čichnově**=- Pomáhaly důchodcům s internetem =- Různé srazy po Brně=- Různé přednášky žáků o aplikacích apod.=- Možnost uskutečnit podobné akce i u nás na škole=- Možnost žáků dělat prezentace pro důchodce=**Participativní projekt**=- Formulář vytvořen=- Plakátky rozneseny po škole=- Formuláře otevřeny do konce Listopadu=**Schůzka s ředitelem školy**=- Co se řešilo=**Loučení s Paní učitelkou Machačovou**=- Odchází na mateřskou dovolenou=**Moderátor příští schůzky**=- Sarah Buchtová'),
(58, 17, '2024-05-07', '18.02.34', '**Krajský sněm**=- Rozdělení do výborů=- Moudrá síť (pomoc důchodcům na internetu)=- Volby do evropského parlamentu=- Hra Kahoot=- 16.5. sraz s Hejtmanem na Střední škole informatiky, poštovnictví a finančnictví Čichnova=**Spolupráce s parlamentem na Střední škole informatiky, poštovnictví a finančnictví Čichnova**=- Soutěže, Akce=-- Sportovní u nás=-- Besední a Esport u nich=**Participativní projekt na zvelebení školy**=- Počet odpovědí: 0=- Žáci mají podvědomí o tom díky plakátkům=**Výlet parlamentu**=- 20.-21.6. přes noc=- Hrad Bítov a okolí=- 15 lidí=**Moderátor příští sezení**=- Anežka Macháčková'),
(59, 17, '2024-05-14', '18.02.34', '**Zhodnocení No Backpack Day**=- Malá účast=- Žádné foto=- Malá propagace=- Problém v omezení rozměru=**Schůze s hejtmanem**=- Kdo půjde=- Slušnější oblečení=**Výlet parlamentu**=- Kdo jede=- 20.-21.6.=- Kde budeme jíst=**Letní program na konec roku**=- Sport, Konzole, Promítání, Piknik s tvořivou dílnou, Venkovní hry=**Větší propagace parlamentu**=- Více nástěnek/stojany=**Moderátor příští schůzi**=- Kropáček Jakub'),
(60, 17, '2024-06-04', '18.02.34', '**Setkání s panem hejtmanem**=- Nikdo si nepřipravil otázky=- Mluví více než náš ředitel=- Byli jsme oblečeni slušněji než žáci Střední školy informatiky, poštovnictví a finančnictví Čichnova=- Příští rok se uskuteční u nás=**Výlet školního Parlamentu**=- Původní termín 20.6. (6:30) - 21.6. 14:00=- Pasohlávky=- Program:=-- AquaLand=-- Nové mlýny=- S sebou: =-- Stolní/karetní hry=**Letní program**=- 27.6.=- Program: =-- Káva, konzole=-- Sport=-- Šerm=- Propagace'),
(61, 17, '2024-06-11', '18.02.34', '**Parlament výlet**=- **Den 1**=- 6:00 sraz Brno hl.n (vestibul)=- Brno **Pálava**=-- Děvičky=- Pálava=> **Lednice**=-- Burger=-- Prohlídka zámku a okolí=-- Minaret=- Lednice => **Pasohlávky**=-- Ubytování=- **Den 2**=- Volná zábava=- Pasohlávky => **Brno**=**Letní program**=- Klub kávy a přátel bude promítat i filmy=- Upoutávky=**Program na Čichnově**=- Den lidských práv=-- Workshopy, přednášky=- Bližší informace později=**Memorandum s parlamentem na Střední škole informatiky, poštovnictví a finančnictví Čichnova**=- Je v pořádku=- Podpis'),
(62, 17, '2024-09-19', '18.02.34', '**Čas schůze**=- úterý 8.h / pátek 7.h=**Jak to bude s volbou předsedy**=**O náboru prvních ročníků do školního Parlamentu**=**Dořešení nabíječek**'),
(63, 17, '2024-09-24', '18.02.34', '**Demokraticky zvolen nový předseda**=- Jiří Boucník, L2=**Nástěnky**=**Rada mladších**=**První schůzka školního Parlamentu**=**Nábor prváků**=**Pravidelné schůzky paramentu**=**Revizor nabíječek**=- Patrik Brandejs'),
(64, 17, '2024-10-01', '18.02.34', '**Nový předseda**: Jiří Boucník (ve spolupráci s Adam Abbod)=**Zapisovatel**: Matěj Kořalka, František Vaněk=**Sociální sítě**: Roman Kačmařík=**Nástěnkářka**: Sarah Buchtová=**Nábor prváků** =- Rozvrh a trasy pro 4 skupiny=- Co říct o náboru=- Slušnost a mluvit pravdu=- 2.10. čtvrtek 1. a 2. vyučovací hodinu=- Popis práce, něco o akcích, odměny=**Nabíječky** =- 8.10. revize=- Štítky a kontroly=- Nepůjčovat učitelům=- Samostatné nekombinované kabely=- Zodpovídá: Patrik Brandejs=**Plán akcí**=- Nábor prváků=- Adapťák=- Halloween=- Zimní program=- 3 králové=- Valentýn=- Letní program=- Výlet za odměnu=- Akce sport=- Participativní projekt na zvelebení školy=- Spolupráce s Střední školou informatiky, poštovnictví a finančnictví Čichnova'),
(65, 17, '2024-10-08', '18.02.34', '**Plán akcí**=- Adapťák=- Haloween=- Mikuláš=- Zimní program=- 3 králové=- Valentýn=- Letní program=- Výlet za odměnu=- Participativní projekt=- Sportovní akce=- Spolupráce se střední školou Čichnova=**Další akce**=- Modrá síť (Důchodci na Čichnově)=--Říjen - Listopad=**Halloween**=- Zařizuje: Macháčková, Buchtová=- 31.10.=- Potřeba zařídit odměny za obleky, plakátky a celkovou organizaci a propagaci=**Adapťák**=- Asi 22.10.=- Bowling=**Dělení do sekcí**=- Nástěnky:=-- Buchtová=- Zápisy:=-- Kořalka, Vaněk=- Nabíječky:=-- Brandejs=- Sociální sítě:=-- Kačmařík=- Grafika:=-- Boucník, Macháčková=- Rozvrhy=-- Němec, Pokorná=**Zmínění diskuzního kroužku**=- O kroužku=- Co se tam děje'),
(66, 17, '2024-10-15', '18.02.34', '**Zakázka na dřevěné diáře pro školu**=- Na první stranu hlášky používané na škole=- Parlament vymyslí hlášky a slova =- Do týdne donést návrhy=- Propagace pomocí zástupců=**Messenger skupina**=- Přidat nováčky=**Adaptační program**=- Bowling Brno=- 22. 10. úterý v 9:30 =- Placené školou místo vyučování=**Schůze s panem ředitelem**=- 5.11. 8. vyučovací hodinu=**Moudrá síť**=- Pomoc technicky negramotným lidem=- Někdy v listopadu=- Pomoc z řad našeho parlamentu=- Přednášky mohou být i od nás=-- Jak využívat různé aplikace=- Otázky budou směřované i na nás=**Sbírka na den dobrých skutků**=- Výbava pro handicapované=- 1. skupina=-- Kačmarčík =-- Kropáček=-- Šmarda=- 2. skupina=-- Macháčková=-- Hudcová=-- Munclinger=- Říct informace o projektu=- Středa 23. 10.=**Rada mladších**=- Přednášky, mezigenerační psychologie=- 16. 10. Středa=**Participativní rozpočet**=- 30 000,- na zvelebení školy=- Návrhy od studentů=- Plakátky po škole'),
(67, 17, '2024-11-05', '18.02.34', '//Školení od Pana Ředitele doc. RNDr. Aleš Ruda, Ph.D., MBA//=**Dotazník ohledně vyučování**=- Informace o přípravě na život ve školství a výuce=- Kritéria a informace v dotazníku: =-- Učitel by na začátku hodiny měl vysvětlit cíl hodiny a na konci vyučovací hodiny říct, jak se žákům povedlo tohoto cíle dosáhnout=-- Učitel by se měl chovat partnersky až přátelsky k žákům a naopak=-- I předměty které se nevztahují k oboru/tématu nás připravují na život=-- Do vyučování by se měly zakomponovat aktivity kde žáci spolupracují/pomáhají si a  připravují je na život=-- Vyučující by měl vysvětlit učivo tak aby ho většina žáků pochopila a neodkazovala se na samostudium=-- Učitelé by měli zakomponovat měkké dovednosti do vyučování (týmová práce, kreativita)=-- Žáci by se měli učit nejen na písemky, ale i do budoucna=-- Učitel by měl ověřovat znalosti žáků více způsoby=- Dotazník je pouze doplněk, protože všechny otázky nemusejí být relevantní=- Při opakovaným špatným hodnocení nebo při více podněty od žáků se vedení školy samo podívá na případ=**Otázky od studentů**=- Zábrany mezi pisoáry: dle možnosti místa mezi pisoáry=- Sifon na každém patře: podle rozvodů vody a SRPŠ=-- Možnost firmy "Lokni"=- Učitelé dělají ze svého předmětu nejpotřebnější věc oproti potřebám žáků=- Renovace školních sítí:=-- V procesu schvalování a předpoklad že o prázdninách se uskuteční=-- Výměna příslušenství k sítím: v příštích letech=- Výpadky systému EduPage: mezi 9-11 hodinou jsou systémy přetížené'),
(68, 65, '2024-11-01', '18.02.35', '- dotazník na hodnocení vzdělávacího procesu a učitelů - připomínky/úpravy a dovysvětlení =- sdružení rodičů =- Místo setkání - pravděpodobně aula==');

--
-- Klíče pro exportované tabulky
--

--
-- Klíče pro tabulku `files_alba_rosa_file_storage`
--
ALTER TABLE `files_alba_rosa_file_storage`
  ADD PRIMARY KEY (`id_file`);

--
-- Klíče pro tabulku `gamehub_private_alba_rosa`
--
ALTER TABLE `gamehub_private_alba_rosa`
  ADD PRIMARY KEY (`idgamehub`);

--
-- Klíče pro tabulku `gamehub_public_alba_rosa`
--
ALTER TABLE `gamehub_public_alba_rosa`
  ADD PRIMARY KEY (`idgamehub`);

--
-- Klíče pro tabulku `logins_alba_rosa_purpix`
--
ALTER TABLE `logins_alba_rosa_purpix`
  ADD PRIMARY KEY (`idlogins`);

--
-- Klíče pro tabulku `logouts_alba_rosa_purpix`
--
ALTER TABLE `logouts_alba_rosa_purpix`
  ADD PRIMARY KEY (`idlogouts`);

--
-- Klíče pro tabulku `other_alba_rosa`
--
ALTER TABLE `other_alba_rosa`
  ADD PRIMARY KEY (`idother`);

--
-- Klíče pro tabulku `other_alba_rosa_parlament`
--
ALTER TABLE `other_alba_rosa_parlament`
  ADD PRIMARY KEY (`idother`);

--
-- Klíče pro tabulku `school_alba_rosa`
--
ALTER TABLE `school_alba_rosa`
  ADD UNIQUE KEY `idschool` (`idschool`),
  ADD KEY `idschool_2` (`idschool`);

--
-- Klíče pro tabulku `users_alba_rosa`
--
ALTER TABLE `users_alba_rosa`
  ADD PRIMARY KEY (`idusers`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Klíče pro tabulku `zapis_alba_rosa_parlament`
--
ALTER TABLE `zapis_alba_rosa_parlament`
  ADD PRIMARY KEY (`idzapis`);

--
-- AUTO_INCREMENT pro tabulky
--

--
-- AUTO_INCREMENT pro tabulku `files_alba_rosa_file_storage`
--
ALTER TABLE `files_alba_rosa_file_storage`
  MODIFY `id_file` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=25;
--
-- AUTO_INCREMENT pro tabulku `gamehub_private_alba_rosa`
--
ALTER TABLE `gamehub_private_alba_rosa`
  MODIFY `idgamehub` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT pro tabulku `gamehub_public_alba_rosa`
--
ALTER TABLE `gamehub_public_alba_rosa`
  MODIFY `idgamehub` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT pro tabulku `logins_alba_rosa_purpix`
--
ALTER TABLE `logins_alba_rosa_purpix`
  MODIFY `idlogins` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=14;
--
-- AUTO_INCREMENT pro tabulku `logouts_alba_rosa_purpix`
--
ALTER TABLE `logouts_alba_rosa_purpix`
  MODIFY `idlogouts` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT pro tabulku `other_alba_rosa`
--
ALTER TABLE `other_alba_rosa`
  MODIFY `idother` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT pro tabulku `school_alba_rosa`
--
ALTER TABLE `school_alba_rosa`
  MODIFY `idschool` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT pro tabulku `users_alba_rosa`
--
ALTER TABLE `users_alba_rosa`
  MODIFY `idusers` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=66;
--
-- AUTO_INCREMENT pro tabulku `zapis_alba_rosa_parlament`
--
ALTER TABLE `zapis_alba_rosa_parlament`
  MODIFY `idzapis` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=69;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
