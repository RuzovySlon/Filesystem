-- Adminer 4.2.5 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `filesystem`;
CREATE TABLE `filesystem` (
  `hash` varchar(32) COLLATE utf8_czech_ci NOT NULL COMMENT 'MD5(path)',
  `path` text COLLATE utf8_czech_ci NOT NULL COMMENT 'Path',
  `lft` int(11) NOT NULL COMMENT 'Tree left index',
  `rgt` int(11) NOT NULL COMMENT 'Tree right index',
  `dpt` int(11) DEFAULT NULL COMMENT 'Tree depth',
  `parent` varchar(32) COLLATE utf8_czech_ci DEFAULT NULL COMMENT 'Tree parent',
  `storage` varchar(255) COLLATE utf8_czech_ci NOT NULL COMMENT 'Storage',
  UNIQUE KEY `hsh` (`hash`),
  KEY `prt` (`parent`),
  CONSTRAINT `filesystem_ibfk_1` FOREIGN KEY (`parent`) REFERENCES `filesystem` (`hash`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


-- 2016-08-29 13:29:24