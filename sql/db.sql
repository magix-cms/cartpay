CREATE TABLE IF NOT EXISTS `mc_plugins_cartpay` (
  `id_cart` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `idlang` smallint(3) unsigned NOT NULL,
  `idprofil` int(7) unsigned DEFAULT NULL,
  `nbr_items_cart` smallint(3) unsigned NOT NULL,
  `session_key_cart` varchar(120) NOT NULL,
  `society_cart` varchar(45) DEFAULT NULL,
  `firstname_cart` varchar(45) DEFAULT NULL,
  `lastname_cart` varchar(45) DEFAULT NULL,
  `email_cart` varchar(45) DEFAULT NULL,
  `phone_cart` varchar(45) DEFAULT NULL,
  `street_cart` varchar(45) DEFAULT NULL,
  `city_cart` varchar(45) DEFAULT NULL,
  `postal_cart` varchar(45) DEFAULT NULL,
  `country_cart` varchar(45) DEFAULT NULL,
  `tva_cart` varchar(45) DEFAULT NULL,
  `message_cart` text,
  `transmission_cart` smallint(1) unsigned NOT NULL,
  `street_liv_cart` varchar(45) DEFAULT NULL,
  `city_liv_cart` varchar(45) DEFAULT NULL,
  `postal_liv_cart` varchar(45) DEFAULT NULL,
  `country_liv_cart` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id_cart`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `mc_plugins_cartpay_config` (
  `idconfig` smallint(1) unsigned NOT NULL AUTO_INCREMENT,
  `mail_order` varchar(125) DEFAULT NULL,
  `mail_order_from` varchar(125) DEFAULT NULL,
  `online_payment` smallint(1) unsigned NOT NULL DEFAULT '0',
  `bank_wire` smallint(1) unsigned NOT NULL DEFAULT '1',
  `hipay` smallint(1) unsigned NOT NULL DEFAULT '0',
  `ogone` smallint(1) unsigned NOT NULL DEFAULT '0',
  `shipping` smallint(1) unsigned NOT NULL DEFAULT '0',
  `account_owner` varchar(30) DEFAULT NULL,
  `contact_details` text,
  `bank_address` text,
  PRIMARY KEY (`idconfig`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

INSERT INTO `mc_plugins_cartpay_config` (`idconfig`, `mail_order`, `mail_order_from`, `online_payment`, `bank_wire`, `hipay`, `ogone`, `shipping`, `account_owner`, `contact_details`, `bank_address`) VALUES
(NULL, NULL, NULL, 0, 1, 0, 0, 0, NULL, NULL, NULL);


CREATE TABLE IF NOT EXISTS `mc_plugins_cartpay_items` (
  `id_item` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `id_cart` int(6) unsigned NOT NULL,
  `idcatalog` int(6) unsigned NOT NULL,
  `quantity_items` smallint(5) unsigned NOT NULL,
  `price_items` decimal(12,2) NOT NULL,
  PRIMARY KEY (`id_item`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `mc_plugins_cartpay_order` (
  `id_order` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `id_cart` int(6) unsigned NOT NULL,
  `transaction_id_order` varchar(45) NOT NULL,
  `amount_order` decimal(12,2) NOT NULL,
  `shipping_price_order` decimal(12,2) unsigned NOT NULL,
  `currency_order` varchar(4) NOT NULL,
  `payment_order` varchar(30) NOT NULL DEFAULT 'bank_wire',
  `date_order` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_order`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `mc_plugins_cartpay_tva` (
  `idtva` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `idtvac` smallint(3) unsigned NOT NULL,
  `country` varchar(30) NOT NULL,
  `iso` varchar(5) NOT NULL,
  PRIMARY KEY (`idtva`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `mc_plugins_cartpay_tva_conf` (
  `idtvac` smallint(3) unsigned NOT NULL AUTO_INCREMENT,
  `amount_tva` decimal(3,1) NOT NULL,
  `zone_tva` varchar(20) NOT NULL DEFAULT 'intra',
  PRIMARY KEY (`idtvac`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

INSERT INTO `mc_plugins_cartpay_tva_conf` (`idtvac`, `amount_tva`, `zone_tva`) VALUES
(1, '21.0', 'intra'),
(2, '0', 'inter');