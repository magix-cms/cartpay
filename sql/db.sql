INSERT INTO `mc_admin_access` (`id_role`, `id_module`, `view`, `append`, `edit`, `del`, `action`)
  SELECT 1, m.id_module, 1, 1, 1, 1, 1 FROM mc_module AS m WHERE name = 'cartpay';

TRUNCATE `mc_cartpay_config`;

INSERT INTO `mc_cartpay_config` (`id_config`, `type_order`, `bank_wire`, `account_owner`, `bank_account`, `bank_address`, `email_config`, `email_config_from`)
VALUES (NULL, 'sale', '0', NULL, NULL, NULL, NULL, NULL);