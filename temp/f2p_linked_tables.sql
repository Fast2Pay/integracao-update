-- Adminer 4.4.0 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `f2p_linked_tables`;
CREATE TABLE `f2p_linked_tables` (
  `id_table` int(11) NOT NULL,
  `cpf` varchar(15) NOT NULL,
  `rg` varchar(15) NOT NULL,
  `date` date NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `valor` float NOT NULL,
  `valor_desconto` float NOT NULL DEFAULT '0',
  `valor_servico` float NOT NULL DEFAULT '0',
  `valor_subtotal` float NOT NULL DEFAULT '0',
  `nrdocumento` varchar(255) NOT NULL,
  `cobranca` tinyint(1) NOT NULL DEFAULT '0',
  `caixa` tinyint(1) NOT NULL DEFAULT '0',
  `json_pagamento` text,
  `exit` tinyint(1) DEFAULT '0',
  `printed` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`nrdocumento`),
  KEY `rg` (`rg`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- 2018-05-07 12:27:11
