CREATE TABLE IF NOT EXISTS `mc_cartpay` (
  `id_cart` int(7) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_account` int(7) UNSIGNED DEFAULT NULL,
  `id_buyer` int(11) DEFAULT NULL,
  `session_key_cart` varchar(50) NOT NULL,
  `transmission_cart` smallint(1) NOT NULL DEFAULT '0',
  `date_register` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_cart`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

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
  `step_order` varchar(30) DEFAULT NULL,
  `info_order` text DEFAULT NULL,
  `amount_order` decimal(12,2) NOT NULL,
  `currency_order` varchar(20) NOT NULL,
  `transaction_id` varchar(40) NOT NULL,
  `payment_order` varchar(30) NOT NULL,
  `status_order` enum('paid','pending','failed') NOT NULL DEFAULT 'paid',
  `date_register` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_order`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `mc_cartpay_quotation` (
  `id_quotation` int(7) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_cart` int(7) UNSIGNED NOT NULL,
  `step_quotation` varchar(30) DEFAULT NULL,
  `info_quotation` text DEFAULT NULL,
  `amount_quotation` decimal(12,0) DEFAULT NULL,
  `currency_quotation` varchar(20) DEFAULT NULL,
  `date_register` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_quotation`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `mc_cartpay_config` (
    `id_config` smallint(3) UNSIGNED NOT NULL AUTO_INCREMENT,
    `type_order` enum('sale','quotation') NOT NULL DEFAULT 'sale',
    `quotation_enabled` smallint(1) UNSIGNED NOT NULL DEFAULT '1',
    `order_enabled` smallint(1) UNSIGNED NOT NULL DEFAULT '0',
    `bank_wire` smallint(1) NOT NULL DEFAULT '0',
    `account_owner` varchar(40) DEFAULT NULL,
    `bank_account` varchar(40) DEFAULT NULL,
    `bank_address` varchar(150) DEFAULT NULL,
    `bank_link` varchar(150) DEFAULT NULL,
    `email_config` varchar(150) DEFAULT NULL,
    `email_config_from` varchar(150) DEFAULT NULL,
    `billing_address` smallint(1) NOT NULL DEFAULT '0',
    `show_price` smallint(1) UNSIGNED NOT NULL DEFAULT '0',
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
  `street_billing` varchar(150) DEFAULT NULL,
  `postcode_billing` varchar(10) DEFAULT NULL,
  `city_billing` varchar(60) DEFAULT NULL,
  `country_billing` varchar(40) DEFAULT NULL,
  `date_register` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_buyer`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

INSERT INTO `mc_cartpay_config` (`id_config`, `type_order`, `quotation_enabled`, `order_enabled`, `bank_wire`, `account_owner`, `bank_account`, `bank_address`, `email_config`, `email_config_from`, `billing_address`, `show_price`)
VALUES (NULL, 'quotation', '1', '0', '0', NULL, NULL, NULL, NULL, NULL, '0', '0');