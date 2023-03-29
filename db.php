<?php
/*
 # -- BEGIN LICENSE BLOCK ----------------------------------
 #
 # This file is part of MAGIX CMS.
 # MAGIX CMS, The content management system optimized for users
 # Copyright (C) 2008 - 2018 magix-cms.com support[at]magix-cms[point]com
 #
 # OFFICIAL TEAM :
 #
 #   * Gerits Aurelien (Author - Developer) <aurelien@magix-cms.com>
 #
 # Redistributions of files must retain the above copyright notice.
 # This program is free software: you can redistribute it and/or modify
 # it under the terms of the GNU General Public License as published by
 # the Free Software Foundation, either version 3 of the License, or
 # (at your option) any later version.
 #
 # This program is distributed in the hope that it will be useful,
 # but WITHOUT ANY WARRANTY; without even the implied warranty of
 # MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 # GNU General Public License for more details.
 #
 # You should have received a copy of the GNU General Public License
 # along with this program.  If not, see <http://www.gnu.org/licenses/>.
 #
 # -- END LICENSE BLOCK -----------------------------------
 #
 # DISCLAIMER
 #
 # Do not edit or add to this file if you wish to upgrade MAGIX CMS to newer
 # versions in the future. If you wish to customize MAGIX CMS for your
 # needs please refer to http://www.magix-cms.com for more information.
 */
class plugins_cartpay_db
{
	/**
	 * @param $config
	 * @param bool $params
	 * @return mixed|null
	 * @throws Exception
	 */
	public function fetchData($config, $params = false)
	{
		if (!is_array($config)) return '$config must be an array';

		$sql = '';

		if ($config['context'] === 'all') {
			switch ($config['type']) {
				case 'carts_account':
                    $limit = '';
                    if($config['offset']) {
                        $limit = ' LIMIT 0, '.$config['offset'];
                        if(isset($config['page']) && $config['page'] > 1) {
                            $limit = ' LIMIT '.(($config['page'] - 1) * $config['offset']).', '.$config['offset'];
                        }
                    }
					$cond = '';

					if(isset($config['search'])) {
						if(is_array($config['search'])) {
							$nbc = 0;
							foreach ($config['search'] as $key => $q) {
								if($q !== '') {
									$cond .= 'AND ';//!$nbc ? ' WHERE ' : 'AND ';
									switch ($key) {
										case 'id_cart':
										//case 'active_ac':
											$cond .= 'a.'.$key.' = '.$q.' ';
											break;
										case 'email':
                                        $cond .= "(CASE 
                                              WHEN a.id_account IS NOT NULL THEN a.email_ac
                                              WHEN b.id_buyer IS NOT NULL THEN b.email_buyer
                                              END) LIKE '%".$q."%' ";
											break;
                                        case 'firstname':
                                            $cond .= "(CASE 
                                              WHEN a.id_account IS NOT NULL THEN a.firstname_ac
                                              WHEN b.id_buyer IS NOT NULL THEN b.firstname_buyer
                                              END) LIKE '%".$q."%' ";
                                            break;
                                        case 'lastname':
                                            $cond .= "(CASE 
                                              WHEN a.id_account IS NOT NULL THEN a.lastname_ac
                                              WHEN b.id_buyer IS NOT NULL THEN b.lastname_buyer
                                              END) LIKE '%".$q."%' ";
                                            break;
										case 'date_register':
											$dateFormat = new component_format_date();
											$q = $dateFormat->date_to_db_format($q);
											$cond .= "cart.".$key." LIKE '%".$q."%' ";
											break;
									}
									$nbc++;
								}
							}
						}
					}

					$sql = 'SELECT
 								cart.id_cart,
 								(CASE 
 								  WHEN a.id_account IS NOT NULL THEN a.email_ac
 								  WHEN b.id_buyer IS NOT NULL THEN b.email_buyer
 								  END) AS email,
                                (CASE 
 								  WHEN a.id_account IS NOT NULL THEN a.firstname_ac
 								  WHEN b.id_buyer IS NOT NULL THEN b.firstname_buyer
 								  END) AS firstname,
 								(CASE 
 								  WHEN a.id_account IS NOT NULL THEN a.lastname_ac
 								  WHEN b.id_buyer IS NOT NULL THEN b.lastname_buyer
 								  END) AS lastname,
 								(CASE 
 								  WHEN o.id_cart IS NOT NULL THEN "sale"
 								  WHEN q.id_cart IS NOT NULL THEN "quotation"
 								  END) AS type_cart,
                                count(items.id_items) AS nbr_product,
                                SUM(items.quantity) AS nbr_quantity,
 								cart.date_register
							FROM `mc_cartpay` as cart
							LEFT JOIN `mc_account` as a USING (id_account)
							LEFT JOIN `mc_cartpay_buyer` as b USING (id_buyer)
							JOIN `mc_cartpay_items` as items USING (id_cart)
							LEFT JOIN `mc_cartpay_order` as o USING (id_cart)
							LEFT JOIN `mc_cartpay_quotation` as q USING (id_cart)
							WHERE cart.transmission_cart = 1 
							/*JOIN `mc_lang` as l ON(a.id_lang = l.id_lang)*/' . $cond.
                            ' GROUP BY cart.id_cart DESC'.$limit;
					break;
                case 'carts':
                    $limit = '';
                    if ($config['offset']) {
                        $limit = ' LIMIT 0, ' . $config['offset'];
                        if (isset($config['page']) && $config['page'] > 1) {
                            $limit = ' LIMIT ' . (($config['page'] - 1) * $config['offset']) . ', ' . $config['offset'];
                        }
                    }
                    $cond = '';

                    if(isset($config['search'])) {
                        if(is_array($config['search'])) {
                            $nbc = 0;
                            foreach ($config['search'] as $key => $q) {
                                if($q !== '') {
                                    $cond .= 'AND ';//!$nbc ? ' WHERE ' : 'AND ';
                                    switch ($key) {
                                        case 'id_cart':
                                            //case 'active_ac':
                                            $cond .= 'cart.'.$key.' = '.$q.' ';
                                            break;
                                        case 'email':
                                        case 'firstname':
                                        case 'lastname':
                                            $cond .= "b.".$key."_buyer LIKE '%".$q."%' ";
                                            break;
                                        case 'date_register':
                                            $dateFormat = new component_format_date();
                                            $q = $dateFormat->date_to_db_format($q);
                                            $cond .= "cart.".$key." LIKE '%".$q."%' ";
                                            break;
                                    }
                                    $nbc++;
                                }
                            }
                        }
                    }

                    $sql = 'SELECT * FROM (
                                SELECT
                                    cart.id_cart,
                                    b.email_buyer AS email,
                                    b.firstname_buyer AS firstname,
                                    b.lastname_buyer AS lastname,
                                    (CASE 
                                      WHEN o.id_cart IS NOT NULL THEN "sale"
                                      WHEN q.id_cart IS NOT NULL THEN "quotation"
                                      END) AS type_cart,
                                    count(items.id_items) AS nbr_product,
                                    SUM(items.quantity) AS nbr_quantity,
                                    o.status_order,
                                    cart.date_register
                                FROM `mc_cartpay` as cart
                                JOIN `mc_cartpay_buyer` as b ON (b.id_buyer = cart.id_buyer)
                                JOIN `mc_cartpay_items` as items ON (cart.id_cart = items.id_cart)
                                LEFT JOIN `mc_cartpay_order` as o ON (cart.id_cart = o.id_cart)
                                LEFT JOIN `mc_cartpay_quotation` as q ON (cart.id_cart = q.id_cart)
                                WHERE cart.transmission_cart = 1 AND cart.id_account IS NULL ' . $cond.
                        ' GROUP BY cart.id_cart ORDER BY cart.id_cart DESC) carts WHERE carts.id_cart IS NOT NULL AND carts.id_cart > 0
                          '.$limit;//GROUP BY carts.id_cart
                    break;
                case 'product':
                    $sql = 'SELECT item.id_items,item.quantity,p.price_p,c.name_p
                            FROM mc_cartpay_items AS item 
                            JOIN mc_catalog_product AS p ON(item.id_product = p.id_product)
                            JOIN mc_catalog_product_content AS c ON ( p.id_product = c.id_product )
							JOIN mc_lang AS lang ON ( c.id_lang = lang.id_lang )
                            WHERE c.id_lang = :default_lang AND item.id_cart = :id';
                    break;
                case 'catalog':
					$sql = 'SELECT 
       							item.id_items,
       							c.id_cat,
								cat.name_cat, 
								cat.url_cat, 
								p.id_product, 
								p.reference_p, 
								p.price_p, 
								pc.name_p, 
								pc.longname_p, 
								pc.resume_p, 
								pc.content_p, 
								pc.url_p, 
								pc.id_lang,
								lang.iso_lang, 
								pc.last_update, 
								img.name_img,
								COALESCE(imgc.alt_img, pc.longname_p, pc.name_p) as alt_img,
								COALESCE(imgc.title_img, imgc.alt_img, pc.longname_p, pc.name_p) as title_img,
								COALESCE(imgc.caption_img, imgc.title_img, imgc.alt_img, pc.longname_p, pc.name_p) as caption_img,
								pc.seo_title_p,
								pc.seo_desc_p
                            FROM mc_cartpay_items AS item 
                            JOIN mc_catalog_product AS p ON(item.id_product = p.id_product)
                            JOIN mc_catalog_product_content AS pc ON ( p.id_product = pc.id_product )
							JOIN mc_catalog catalog ON(catalog.id_product = p.id_product and catalog.default_c = 1)
							JOIN mc_catalog_cat AS c ON ( catalog.id_cat = c.id_cat )
							JOIN mc_catalog_cat_content AS cat ON ( c.id_cat = cat.id_cat and pc.id_lang = cat.id_lang)
							LEFT JOIN mc_catalog_product_img img ON (p.id_product = img.id_product AND img.default_img = 1)
							LEFT JOIN mc_catalog_product_img_content imgc ON (img.id_img = imgc.id_img and pc.id_lang = imgc.id_lang)
							JOIN mc_lang AS lang ON ( pc.id_lang = lang.id_lang )
                            WHERE pc.id_lang = :default_lang 
						  	AND item.id_cart = :id
						  	ORDER BY item.date_register';
                    /*$sql = 'SELECT
								item.id_items,
								item.quantity,
								p.price_p,
								pc.name_p,
								img.name_img,
								c.id_cat,
								cat.name_cat,
								cat.url_cat,
								p.id_product,
								pc.name_p,
								pc.url_p,
								pc.id_lang,
								lang.iso_lang
                    		FROM mc_cartpay_items AS item 
                            JOIN mc_catalog AS c ON ( item.id_product = c.id_product )
                    		JOIN mc_catalog_cat_content AS cat ON ( c.id_cat = cat.id_cat )
                    		JOIN mc_catalog_product AS p ON ( c.id_product = p.id_product )
                    		JOIN mc_catalog_product_content AS pc ON ( p.id_product = pc.id_product )
                    		LEFT JOIN mc_catalog_product_img AS img ON (img.id_product = p.id_product and img.default_img = 1)
                        	LEFT JOIN mc_catalog_product_img_content AS ic ON (img.id_img = ic.id_img AND ic.id_lang = pc.id_lang)
                    		JOIN mc_lang AS lang ON ( pc.id_lang = lang.id_lang )
                    		WHERE pc.id_lang = :default_lang AND item.id_cart = :id GROUP BY item.id_items';*/
                    break;
				case 'account_cart_items':
					$sql = 'SELECT * FROM `mc_cartpay_items` WHERE id_cart = :id';
					break;
			}

			return $sql ? component_routing_db::layer()->fetchAll($sql, $params) : null;
		}
		elseif ($config['context'] === 'one') {
			switch ($config['type']) {
                case 'account':
                    $sql = 'SELECT *
							FROM `mc_account` as a
							JOIN `mc_account_address` as aa USING(`id_account`)
							JOIN `mc_lang` as l USING(`id_lang`)
							WHERE `id_account` = :id';
                    break;
                case 'buyer':
                    $sql = 'SELECT 
       							b.*
							FROM `mc_cartpay` c  
							LEFT JOIN `mc_cartpay_buyer` b using (id_buyer)
							WHERE c.id_cart = :id';
                    break;
				case 'config':
					$sql = 'SELECT * FROM `mc_cartpay_config` ORDER BY id_config DESC LIMIT 0,1';
					break;
				case 'cart_account':
					$sql = 'SELECT cart.id_cart,
                                cart.transmission_cart,
                                (CASE 
 								  WHEN a.id_account IS NOT NULL THEN a.email_ac
 								  WHEN b.id_buyer IS NOT NULL THEN b.email_buyer
 								  END) AS email,
                                (CASE 
 								  WHEN a.id_account IS NOT NULL THEN a.firstname_ac
 								  WHEN b.id_buyer IS NOT NULL THEN b.firstname_buyer
 								  END) AS firstname,
 								(CASE 
 								  WHEN a.id_account IS NOT NULL THEN a.lastname_ac
 								  WHEN b.id_buyer IS NOT NULL THEN b.lastname_buyer
 								  END) AS lastname,
 								o.*,q.id_quotation,
                          (CASE 
                          WHEN o.id_cart IS NOT NULL THEN "sale"
                          WHEN q.id_cart IS NOT NULL THEN "quotation"
                          END) AS type_cart,
                          (CASE 
                          WHEN o.id_cart IS NOT NULL THEN o.date_register
                          WHEN q.id_cart IS NOT NULL THEN q.date_register
                          END) AS date_cart 
							FROM `mc_cartpay` as cart
							LEFT JOIN `mc_account` as a USING(`id_account`)
							LEFT JOIN `mc_cartpay_buyer` as b USING (id_buyer)
							LEFT JOIN `mc_cartpay_order` as o USING (id_cart)
							LEFT JOIN `mc_cartpay_quotation` as q ON (cart.id_cart = q.id_cart)
							WHERE cart.id_cart = :id';
					break;
                case 'cart':
                    $sql = 'SELECT cart.id_cart,
                                cart.transmission_cart,
                                b.email_buyer AS email,
 								b.firstname_buyer AS firstname,
 								b.lastname_buyer AS lastname,
 								o.*,q.id_quotation,
                          (CASE 
                          WHEN o.id_cart IS NOT NULL THEN "sale"
                          WHEN q.id_cart IS NOT NULL THEN "quotation"
                          END) AS type_cart,
                          (CASE 
                          WHEN o.id_cart IS NOT NULL THEN o.date_register
                          WHEN q.id_cart IS NOT NULL THEN q.date_register
                          END) AS date_cart 
							FROM `mc_cartpay` as cart
							LEFT JOIN `mc_cartpay_buyer` as b USING (id_buyer)
							LEFT JOIN `mc_cartpay_order` as o USING (id_cart)
							LEFT JOIN `mc_cartpay_quotation` as q ON (cart.id_cart = q.id_cart)
							WHERE cart.id_cart = :id AND cart.id_account IS NULL';
                    break;
				case 'session':
					/*$sql = 'SELECT *
							FROM mc_cartpay
							WHERE session_key_cart = :session_key_cart
							AND transmission_cart = 0';*/
					$sql = 'SELECT *
							FROM mc_cartpay
							WHERE session_key_cart = :session_key_cart';
					break;
				case 'account_session':
					$sql = "SELECT * 
							FROM `mc_cartpay`
							WHERE id_account = :id
							AND id_cart NOT IN (
								SELECT id_cart FROM mc_cartpay_order WHERE status_order = 'paid' OR status_order = 'failed'
							)
							AND transmission_cart = 0
							ORDER BY id_cart DESC
							LIMIT 1";
					break;
                case 'product':
                    $sql = 'SELECT *
							FROM mc_cartpay_items
							WHERE id_cart = :id AND id_product = :id_product';
                    break;
				case 'product_price':
					$sql = 'SELECT price_p FROM `mc_catalog_product` WHERE id_product = :id';
					break;
				case 'item':
					$sql = 'SELECT 
								item.id_items,
								item.quantity,
								catalog.* ,
								cat.name_cat, 
								cat.url_cat, 
								p.*, 
								pc.name_p, 
								pc.longname_p, 
								pc.resume_p, 
								pc.content_p, 
								pc.url_p, 
								pc.id_lang,
								lang.iso_lang, 
								pc.last_update, 
								img.name_img,
								COALESCE(imgc.alt_img, pc.longname_p, pc.name_p) as alt_img,
								COALESCE(imgc.title_img, imgc.alt_img, pc.longname_p, pc.name_p) as title_img,
								COALESCE(imgc.caption_img, imgc.title_img, imgc.alt_img, pc.longname_p, pc.name_p) as caption_img,
								pc.seo_title_p,
								pc.seo_desc_p
                            FROM mc_cartpay_items AS item 
                            JOIN mc_catalog_product AS p ON(item.id_product = p.id_product)
                            JOIN mc_catalog_product_content AS pc ON ( p.id_product = pc.id_product )
							JOIN mc_catalog catalog ON(catalog.id_product = p.id_product and catalog.default_c = 1)
							JOIN mc_catalog_cat AS c ON ( catalog.id_cat = c.id_cat )
							JOIN mc_catalog_cat_content AS cat ON ( c.id_cat = cat.id_cat and pc.id_lang = cat.id_lang)
							LEFT JOIN mc_catalog_product_img img ON (p.id_product = img.id_product AND img.default_img = 1)
							LEFT JOIN mc_catalog_product_img_content imgc ON (img.id_img = imgc.id_img and pc.id_lang = imgc.id_lang)
							JOIN mc_lang AS lang ON ( pc.id_lang = lang.id_lang )
                            WHERE pc.id_lang = :default_lang 
						  	AND item.id_cart = :id
							AND item.id_product = :id_product
						  	ORDER BY item.date_register';
					break;
				case 'idFromIso':
					$sql = 'SELECT `id_lang` FROM `mc_lang` WHERE `iso_lang` = :iso';
					break;
                case 'countProduct':
                    $sql = 'SELECT count(id_items) AS nbr
							FROM mc_cartpay_items
							WHERE id_cart = :id';
                    break;
				case 'order':
				case 'quotation':
					$sql = 'SELECT * FROM `mc_cartpay_'.$config['type'].'` WHERE id_cart = :id_cart ORDER BY date_register DESC LIMIT 0,1';
                    break;
                case 'countCart':
                    $sql = 'SELECT count(id_cart) AS order_num FROM mc_cartpay 
                    WHERE id_cart BETWEEN 1 AND :id AND transmission_cart = 1';
                    break;
			}

			return $sql ? component_routing_db::layer()->fetch($sql, $params) : null;
		}
	}

	/**
	 * @param $config
	 * @param array $params
	 * @return bool|string
	 */
	public function insert($config, $params = array())
	{
		if (!is_array($config)) return '$config must be an array';

		$sql = '';

		switch ($config['type']) {
            case 'buyer':
                $id_cart = $params['id_cart'];
                unset($params['id_cart']);
                $queries = array(
                    array('request' => 'INSERT INTO `mc_cartpay_buyer` (`email_buyer`, `lastname_buyer`, `firstname_buyer`, `company_buyer`, `phone_buyer`, `vat_buyer`, `date_register`) 
                    VALUES (:email_buyer, :lastname_buyer, :firstname_buyer, :company_buyer, :phone_buyer, :vat_buyer, NOW())', 'params' => $params),
                    array('request' => 'SET @buyer_id = LAST_INSERT_ID()', 'params' => array()),
                    array('request' => 'UPDATE `mc_cartpay` SET `id_buyer` = @buyer_id WHERE `mc_cartpay`.`id_cart` = :id_cart', 'params' => array('id_cart'=>$id_cart))
                );

                try {
                    component_routing_db::layer()->transaction($queries);
                    return true;
                }
                catch (Exception $e) {
                    return 'Exception reçue : '.$e->getMessage();
                }
                break;
			case 'session':
				$sql = 'INSERT INTO `mc_cartpay` (`id_account`,`session_key_cart`)
						VALUES (:id_account,:session_key_cart)';
				break;
            case 'product':
                $sql = 'INSERT INTO `mc_cartpay_items` (id_cart,id_product,quantity)
						VALUES (:id_cart,:id_product,:quantity)';
                break;
            case 'quotation':
                $sql = 'INSERT INTO `mc_cartpay_quotation` (id_cart, step_quotation, amount_quotation, currency_quotation)
						VALUES (:id_cart, :step_quotation, :amount_quotation, :currency_quotation)';
                break;
			case 'order':
				$sql = 'INSERT INTO `mc_cartpay_order` (id_cart, step_order, amount_order, currency_order, transaction_id, payment_order, status_order)
						VALUES (:id_cart, :step_order, :amount_order, :currency_order, :transaction_id, :payment_order, :status_order)';
				break;
		}

		if($sql === '') return 'Unknown request asked';

		try {
			component_routing_db::layer()->insert($sql,$params);
			return true;
		}
		catch (Exception $e) {
			return 'Exception reçue : '.$e->getMessage();
		}
	}

	/**
	 * @param $config
	 * @param array $params
	 * @return bool|string
	 */
	public function update($config, $params = array())
	{
		if (!is_array($config)) return '$config must be an array';

		$sql = '';

		switch ($config['type']) {
			case 'config':
				$sql = 'UPDATE `mc_cartpay_config`
						SET 
							`quotation_enabled` = :quotation_enabled,
							`order_enabled` = :order_enabled,
							`bank_wire` = :bank_wire,
							`account_owner` = :account_owner,
							`bank_account` = :bank_account,
							`bank_address` = :bank_address,
							`bank_link` = :bank_link,
							`email_config` = :email_config,
							`email_config_from` = :email_config_from,
							`billing_address` = :billing_address,
							`show_price` = :show_price
						WHERE id_config = :id';
				break;
            case 'product':
                $sql = 'UPDATE `mc_cartpay_items` 
                SET 
                  quantity = :quantity
                WHERE `id_cart` = :id_cart AND id_items = :id_items';
                break;
			case 'buyer':
				$sql = "UPDATE `mc_cartpay_buyer`
						SET `firstname_buyer` = :firstname_buyer,
							`lastname_buyer` = :lastname_buyer,
							`email_buyer` = :email_buyer,
							`phone_buyer` = :phone_buyer,
							`company_buyer` = :company_buyer,
							`vat_buyer` = :vat_buyer
						WHERE `id_buyer` = :id";
				break;
			case 'cart_account':
				$sql = "UPDATE `mc_cartpay`
						SET `id_account` = :id_account
						WHERE `id_cart` = :id_cart";
				break;
            case 'session':
                $sql = 'UPDATE `mc_cartpay`
						SET `id_account` = :id_account
						WHERE `id_cart` = :id';
                break;
            case 'status':
                $sql = 'UPDATE `mc_cartpay`
						SET `transmission_cart` = :tc
						WHERE `id_cart` = :id';
                break;
            case 'status_order':
                $sql = 'UPDATE `mc_cartpay_order`
						SET `status_order` = :status_order
						WHERE `id_cart` = :id';
                break;
            case 'billing':
            case 'billing_address':
                $sql = 'UPDATE `mc_cartpay_buyer`
						SET `street_billing` = :street_billing,
						`postcode_billing` = :postcode_billing,
						`city_billing` = :city_billing,
						`country_billing` = :country_billing
						WHERE `id_buyer` = :id';
                break;
			case 'order_payment':
				$sql = "UPDATE `mc_cartpay_order` SET payment_order = :payment_order WHERE id_order = :id";
				break;
			case 'order_step':
			case 'quotation_step':
			    $type = substr($config['type'], 0, -5);
				$sql = 'UPDATE `mc_cartpay_'.$type.'` SET step_'.$type.' = :step WHERE id_'.$type.' = :id';
				break;
			case 'order_info':
			case 'quotation_info':
			    $type = substr($config['type'], 0, -5);
				$sql = 'UPDATE `mc_cartpay_'.$type.'` SET info_'.$type.' = :info WHERE id_'.$type.' = :id';
				break;
		}

		if($sql === '') return 'Unknown request asked';

		try {
			component_routing_db::layer()->update($sql,$params);
			return true;
		}
		catch (Exception $e) {
			return 'Exception reçue : '.$e->getMessage();
		}
	}

	/**
	 * @param $config
	 * @param array $params
	 * @return bool|string
	 */
	public function delete($config, $params = array())
	{
		if (!is_array($config)) return '$config must be an array';
		$sql = '';

		switch ($config['type']) {
			case 'account':
				$sql = 'DELETE FROM `mc_account` 
						WHERE `id_account` IN ('.$params['id'].')';
				$params = array();
				break;
			case 'session':
				$sql = 'DELETE FROM `mc_account_session`
						WHERE `id_session` = :id_session';
				break;
			case 'lastSessions':
				$sql = 'DELETE FROM `mc_account_session`
						WHERE TO_DAYS(DATE_FORMAT(NOW(), "%Y%m%d")) - TO_DAYS(DATE_FORMAT(last_modified_session, "%Y%m%d")) > :limit';
				break;
			case 'currentSession':
				$sql = 'DELETE FROM `mc_account_session`
						WHERE `id_account` = :id_account';
				break;
			case 'product':
				$sql = 'DELETE FROM `mc_cartpay_items` WHERE id_cart = :id_cart AND id_items = :id_items';
				break;
		}

		if($sql === '') return 'Unknown request asked';

		try {
			component_routing_db::layer()->delete($sql,$params);
			return true;
		}
		catch (Exception $e) {
			return 'Exception reçue : '.$e->getMessage();
		}
	}
}