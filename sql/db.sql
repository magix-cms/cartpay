CREATE TABLE IF NOT EXISTS `mc_cartpay` (
  `id_cart` int(7) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_account` int(7) UNSIGNED DEFAULT NULL,
  `session_key_cart` varchar(50) NOT NULL,
  `transmission_cart` smallint(1) NOT NULL DEFAULT '0',
  `date_register` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_cart`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `mc_cartpay_content` (
  `id_content` int(7) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_cart` int(7) UNSIGNED NOT NULL,
  `id_lang` smallint(3) UNSIGNED NOT NULL,
  `content_cart` text,
  `date_register` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_content`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `mc_cartpay_items` (
  `id_items` int(7) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_cart` int(7) UNSIGNED NOT NULL,
  `id_product` int(7) UNSIGNED NOT NULL,
  `quantity` smallint(3) UNSIGNED NOT NULL,
  `date_register` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_items`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `mc_cartpay_order` (
  `id_order` int(7) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_cart` int(7) UNSIGNED NOT NULL,
  `amount_order` decimal(12,0) NOT NULL,
  `currency_order` varchar(20) NOT NULL,
  `transaction_id` varchar(40) NOT NULL,
  `payment_order` varchar(30) NOT NULL,
  `status_order` enum('paid','pending','failed') NOT NULL DEFAULT 'paid',
  `date_register` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_order`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `mc_cartpay_quotation` (
  `id_quot` int(7) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_cart` int(7) UNSIGNED NOT NULL,
  `date_register` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_quot`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `mc_cartpay_config` (
  `id_config` smallint(3) UNSIGNED NOT NULL AUTO_INCREMENT,
  `type_order` enum('sale','quotation') NOT NULL DEFAULT 'sale',
  `bank_wire` smallint(1) NOT NULL DEFAULT '0',
  `account_owner` varchar(40) DEFAULT NULL,
  `bank_account` varchar(40) DEFAULT NULL,
  `bank_address` varchar(150) DEFAULT NULL,
  `email_config` varchar(150) DEFAULT NULL,
  `email_config_from` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`id_config`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `mc_cartpay_buyer` (
  `id_buyer` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `email_buyer` varchar(150) DEFAULT NULL,
  `lastname_buyer` varchar(40) DEFAULT NULL,
  `firstname_buyer` varchar(40) DEFAULT NULL,
  `company_buyer` varchar(50) DEFAULT NULL,
  `phone_buyer` varchar(45) DEFAULT NULL,
  `vat_buyer` varchar(50) DEFAULT NULL,
  `date_register` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_buyer`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

TRUNCATE `mc_cartpay_config`;

INSERT INTO `mc_cartpay_config` (`id_config`, `type_order`, `bank_wire`, `account_owner`, `bank_account`, `bank_address`, `email_config`, `email_config_from`)
VALUES (NULL, 'sale', '0', NULL, NULL, NULL, NULL, NULL);

INSERT INTO `mc_admin_access` (`id_role`, `id_module`, `view`, `append`, `edit`, `del`, `action`)
  SELECT 1, m.id_module, 1, 1, 1, 1, 1 FROM mc_module AS m WHERE name = 'cartpay';