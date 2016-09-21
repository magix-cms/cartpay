<?php
/*
 # -- BEGIN LICENSE BLOCK ----------------------------------
 #
 # This file is part of MAGIX CMS.
 # MAGIX CMS, The content management system optimized for users
 # Copyright (C) 2008 - 2016 magix-cms.com support[at]magix-cms[point]com
 #
 # OFFICIAL TEAM :
 #
 #   * Gerits Aurelien (Author - Developer) <aurelien@magix-cms.com> <contact@aurelien-gerits.be>
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

 # You should have received a copy of the GNU General Public License
 # along with this program.  If not, see <http://www.gnu.org/licenses/>.
 #
 # -- END LICENSE BLOCK -----------------------------------

 # DISCLAIMER

 # Do not edit or add to this file if you wish to upgrade MAGIX CMS to newer
 # versions in the future. If you wish to customize MAGIX CMS for your
 # needs please refer to http://www.magix-cms.com for more information.
 */
class database_plugins_cartpay
{
	/**
	 * Checks if the tables of the plugins are installed
	 * @access protected
	 * return integer
	 */
	protected function c_show_tables(){
		$tables = array(
			'mc_plugins_cartpay',
			'mc_plugins_cartpay_config',
			'mc_plugins_cartpay_items',
			'mc_plugins_cartpay_module',
			'mc_plugins_cartpay_order',
			'mc_plugins_cartpay_tva',
			'mc_plugins_cartpay_tva_conf'
		);

		$i = 0;
		do {
			$t = magixglobal_model_db::layerDB()->showTable($tables[$i]);
			$i++;
		} while($t && $i < count($tables));

		return $t;
	}

	/**
	 * Checks if the requested table is installed
	 * @param $t
	 * @return integer
	 */
	protected function c_show_table($t){
		return magixglobal_model_db::layerDB()->showTable($t);
	}

	/**
	 * @access protected
	 * Selectionne les contacts pour le formulaire
	 */
	protected function s_contact($iso)
	{
		$sql = 'SELECT c.*
        FROM mc_plugins_contact AS c
        JOIN mc_lang AS lang ON(c.idlang = lang.idlang)
		WHERE lang.iso = :iso';
		return magixglobal_model_db::layerDB()->select($sql, array(':iso' => $iso));
	}

	/**
	 * Récupére un panier en fonction de l'id
	 * @access protected
	 * @param integer id_cart
	 * @return array
	 */
	protected function s_cart($id_cart)
	{
		$sql = ' SELECT cart.*
        FROM mc_plugins_cartpay AS cart
        WHERE cart.id_cart = :id_cart AND cart.transmission_cart = 0';
		return magixglobal_model_db::layerDB()->selectOne($sql, array(
			':id_cart' => $id_cart
		));
	}

	/**
	 * Récupére un panier déja transmis en fonction de l'id
	 * @access protected
	 * @param integer id_cart
	 * @return array
	 */
	protected function s_cart_transmitted($id_cart)
	{
		$sql = ' SELECT cart.*
        FROM mc_plugins_cartpay AS cart
        WHERE cart.id_cart = :id_cart AND cart.transmission_cart = 1';
		return magixglobal_model_db::layerDB()->selectOne($sql, array(
			':id_cart' => $id_cart
		));
	}

	/**
	 * Récupére un panier en fonction de la session
	 * @access protected
	 * @param string session_key
	 * @return array
	 */
	protected function s_cart_session($session_key)
	{
		$sql = ' SELECT cart.*
        FROM mc_plugins_cartpay AS cart
        WHERE cart.session_key_cart = :session_key AND cart.transmission_cart = 0';
		return magixglobal_model_db::layerDB()->selectOne($sql, array(
			':session_key' => $session_key
		));
	}

	/**
	 * Récupére l'id_cart
	 * @param $session_key
	 * @return array
	 */
	protected function s_idcart_session($session_key)
	{
		$sql = ' SELECT cart.id_cart
        FROM mc_plugins_cartpay AS cart
        WHERE cart.session_key_cart = :session_key AND cart.transmission_cart = 0';
		return magixglobal_model_db::layerDB()->selectOne($sql, array(
			':session_key' => $session_key
		));
	}

	/**
	 * Récupére les données du client dans le panier (nom, prénom, téléphone,...)
	 * @param $id_cart
	 * @return array
	 */
	protected function s_customer_info($id_cart)
	{
		$sql = ' SELECT cart.id_cart, cart.firstname_cart, cart.lastname_cart, cart.email_cart, cart.phone_cart,
        cart.street_cart, cart.city_cart, cart.vat_cart, cart.postal_cart, cart.country_cart,cart.message_cart
        FROM mc_plugins_cartpay AS cart
        WHERE cart.id_cart = :id_cart';
		return magixglobal_model_db::layerDB()->selectOne($sql, array(
			':id_cart' => $id_cart
		));
	}

	/**
	 * Get all cart elements
	 * @param $id_cart
	 * @param $ext
	 * @return array
	 */
	protected function s_cart_items($id_cart,$ext = array())
	{
		$joint = '';
		$sj = '';
		$pre = 'items';
		/*if(is_array($ext)) {
			foreach ($ext as $i => $j) {
				$joint .= " LEFT JOIN `".$j['t']."` AS `t$i` ON $pre.".$j['k']." = t$i.".$j['k'];
				$pre = "t$i";
				$sj .= ",t$i.*";
			}
		}*/
		$sql = "SELECT items.*, p.idproduct, catalog.urlcatalog, catalog.titlecatalog, catalog.idlang, catalog.imgcatalog, p.idclc, p.idcls,
        catalog.price,c.pathclibelle, s.pathslibelle, lang.iso$sj
		FROM mc_plugins_cartpay_items AS items
		LEFT JOIN mc_catalog_product AS p ON(p.idcatalog = items.idcatalog)
		LEFT JOIN mc_catalog AS catalog ON ( catalog.idcatalog = p.idcatalog )
		LEFT JOIN mc_catalog_c AS c ON ( c.idclc = p.idclc )
		LEFT JOIN mc_catalog_s AS s ON ( s.idcls = p.idcls )
		$joint
		LEFT JOIN mc_lang AS lang ON ( catalog.idlang = lang.idlang )
        WHERE items.id_cart = :id_cart
        GROUP BY items.id_item";

		return magixglobal_model_db::layerDB()->select($sql, array(
			':id_cart' => $id_cart
		));
	}

	/**
	 * Get the last item added into the cart
	 * @param $id_cart
	 * @return array
	 */
	protected function s_last_cart_item($id_cart)
	{
		$sql = "SELECT id_item, idcatalog
				FROM mc_plugins_cartpay_items
        		WHERE id_cart = :id_cart
        		GROUP BY idcatalog
        		ORDER BY id_item DESC
        		LIMIT 0,1";
		return magixglobal_model_db::layerDB()->selectOne($sql, array(
			':id_cart' => $id_cart
		));
	}

	/**
	 * Récupére tous les élémetns du panier
	 * @param $id_item
	 * @return array
	 */
	protected function s_cart_item_one($id_item)
	{
		$sql = 'SELECT items.*
        FROM mc_plugins_cartpay_items AS items
        WHERE id_item = :id_item';
		return magixglobal_model_db::layerDB()->selectOne($sql, array(
			':id_item' => $id_item
		));
	}

	/**
	 * @param $id_cart
	 * @param $idcatalog
	 * @return array
	 */
	protected function s_cart_item_catalog($id_cart, $idcatalog)
	{
		$sql = 'SELECT items.*
        FROM mc_plugins_cartpay_items AS items
        WHERE idcatalog = :idcatalog AND id_cart = :id_cart';
		return magixglobal_model_db::layerDB()->selectOne($sql, array(
			':id_cart' => $id_cart,
			':idcatalog' => $idcatalog
		));
	}

	/**
	 * Récupére les informations nécessaire pour affichage titre + liens produit
	 * @param $idcatalog
	 * @return array
	 */
	protected function s_catalog_product($idcatalog)
	{
		$sql = 'SELECT p.idproduct, catalog.urlcatalog, catalog.titlecatalog, catalog.idlang, p.idclc, p.idcls, catalog.price,c.pathclibelle, s.pathslibelle, lang.iso
		FROM mc_catalog_product AS p
		LEFT JOIN mc_catalog AS catalog ON ( catalog.idcatalog = p.idcatalog )
		LEFT JOIN mc_catalog_c AS c ON ( c.idclc = p.idclc )
		LEFT JOIN mc_catalog_s AS s ON ( s.idcls = p.idcls )
		LEFT JOIN mc_lang AS lang ON ( catalog.idlang = lang.idlang )
		WHERE catalog.idcatalog = :idcatalog';
		return magixglobal_model_db::layerDB()->selectOne($sql, array(
			':idcatalog' => $idcatalog
		));
	}

	/**
	 * Récupére les informations nécessaire pour affichage titre + liens produit
	 * @param $idcatalog
	 * @return array
	 */
	protected function s_catalog_price($idcatalog)
	{
		$sql = 'SELECT catalog.price
		FROM mc_catalog AS catalog
		WHERE catalog.idcatalog = :idcatalog';
		return magixglobal_model_db::layerDB()->selectOne($sql, array(
			':idcatalog' => $idcatalog
		));
	}

	/**
	 * Récupére un panier en fonction de la session
	 * @access protected
	 * @param string session_key
	 * @return array
	 */
	protected function count_cart_items($id_cart)
	{
		$sql = ' SELECT count(items.id_item) as total
        FROM mc_plugins_cartpay_items AS items
        WHERE id_cart = :id_cart';
		return magixglobal_model_db::layerDB()->selectOne($sql, array(
			':id_cart' => $id_cart
		));
	}

	/**
	 * Ajoute un prix en db
	 * @access protected
	 * return array
	 */
	protected function i_cart_items($id_cart, $idcatalog, $quantity, $price)
	{
		$sql = 'INSERT INTO mc_plugins_cartpay_items (id_cart,idcatalog,quantity_items,price_items)
        VALUE (:id_cart,:idcatalog,:quantity_item,:price_item)';
		return magixglobal_model_db::layerDB()->insert($sql, array(
			':id_cart' => $id_cart,
			':idcatalog' => $idcatalog,
			':quantity_item' => $quantity,
			':price_item' => $price
		));
	}

	/**
	 * Ajoute un prix en db
	 * @access protected
	 * return array
	 */
	protected function i_cart_session($idlang, $session_key)
	{
		$sql = 'INSERT INTO mc_plugins_cartpay (idlang,nbr_items_cart,transmission_cart,session_key_cart)
        VALUE (:idlang,0,0,:session_key)';
		return magixglobal_model_db::layerDB()->insert($sql, array(
			':idlang' => $idlang,
			':session_key' => $session_key
		));
	}

    /**
     * Insertion d'un payement dans la table order
     * @param $id_cart
     * @param $transaction_id_order
     * @param $amount_order
     * @param $shipping_price_order
     * @param $currency_order
     * @param  $payment_order
     */
    protected function i_cart_order($id_cart, $transaction_id_order, $amount_order, $shipping_price_order, $currency_order, $payment_order)
    {
        $sql = 'INSERT INTO mc_plugins_cartpay_order (id_cart,transaction_id_order,amount_order,shipping_price_order,currency_order, payment_order)
        VALUE (:id_cart,:transaction_id_order,:amount_order,:shipping_price_order,:currency_order, :payment_order)';
        return magixglobal_model_db::layerDB()->insert($sql, array(
            ':id_cart' => $id_cart,
            ':transaction_id_order' => $transaction_id_order,
            ':amount_order' => $amount_order,
            ':shipping_price_order' => $shipping_price_order,
            ':currency_order' => $currency_order,
            ':payment_order' => $payment_order
        ));
    }

	/**
	 * Ajoute une demande de réservation
	 * @access protected
	 * return array
	 */
	protected function i_booking($idcatalog, $idprofil, $quantity)
	{
		$sql = 'INSERT INTO mc_plugins_booking (idprofil,idcatalog,quantity_bk)
        VALUE (:idprofil,:idcatalog,:quantity_bk)';
		return magixglobal_model_db::layerDB()->insert($sql, array(
			':idprofil' => $idprofil,
			':idcatalog' => $idcatalog,
			':quantity_bk' => $quantity
		));
	}

	/**
	 * @access protected
	 * Mise à jour du nombre d'éléments dans le panier
	 * @param integer $id_cart
	 * @param integer $nbr_items
	 */
	protected function u_cart_items($id_cart, $nbr_items)
	{
		$sql = 'UPDATE mc_plugins_cartpay SET
          nbr_items_cart=:nbr_items_cart
          WHERE id_cart=:id_cart';
		magixglobal_model_db::layerDB()->update($sql,
			array(
				':id_cart' => $id_cart,
				':nbr_items_cart' => $nbr_items
			)
		);
	}

	/**
	 * @access protected
	 * Mise à jour du prix d'un item
	 * @param integer $id_item
	 * @param integer $price_item
	 */
	protected function u_cart_item_price($id_item, $price_item)
	{
		$sql = 'UPDATE mc_plugins_cartpay_items SET
          price_items=:price_item
          WHERE id_item=:id_item';
		magixglobal_model_db::layerDB()->update($sql,
			array(
				':id_item' => $id_item,
				':price_item' => $price_item
			)
		);
	}

	/**
	 * @access protected
	 * Mise à jour de la quantité d'un item
	 * @param integer $id_item
	 * @param integer $quantity_items
	 */
	protected function u_cart_item_qty($id_item, $quantity_items)
	{
		$sql = 'UPDATE mc_plugins_cartpay_items SET
          quantity_items=:quantity_items
          WHERE id_item=:id_item';
		magixglobal_model_db::layerDB()->update($sql,
			array(
				':id_item' => $id_item,
				':quantity_items' => $quantity_items
			)
		);
	}

	/**
	 * @param $id_item
	 * @param $attr
	 */
	protected function u_cart_item_attr($id_item, $attr)
	{
		$sql = 'UPDATE mc_plugins_cartpay_items SET
          idattr=:idattr
          WHERE id_item=:id_item';
		magixglobal_model_db::layerDB()->update($sql,
			array(
				':id_item' => $id_item,
				':idattr' => $attr
			)
		);
	}

	/**
	 * Mise à jour des informations du surfeur
	 * @param $id_cart
	 * @param null $idprofil
	 * @param $firstname
	 * @param $lastname
	 * @param $email
	 * @param $phone
	 * @param $street
	 * @param $city
	 * @param $tva
     * @param $company
	 * @param $postal
	 * @param $country
	 * @param $message
	 * @param $street_liv
	 * @param $city_liv
	 * @param $postal_liv
	 * @param $country_liv
	 */
	protected function u_cart_customer_infos($id_cart, $idprofil = null, $firstname, $lastname, $email, $phone, $street, $city, $vat, $company, $postal, $country, $message, $street_liv, $city_liv, $postal_liv, $country_liv, $lastname_liv, $firstname_liv){
		$sql = 'UPDATE mc_plugins_cartpay SET
          idprofil=:idprofil, firstname_cart=:firstname_cart, lastname_cart=:lastname_cart, email_cart=:email_cart, phone_cart=:phone_cart,
          street_cart=:street_cart, city_cart=:city_cart, vat_cart=:vat_cart, company_cart=:company_cart, postal_cart=:postal_cart, country_cart=:country_cart, message_cart=:message_cart,
          street_liv_cart=:street_liv_cart, lastname_liv_cart=:lastname_liv_cart, firstname_liv_cart=:firstname_liv_cart, city_liv_cart=:city_liv_cart,
          postal_liv_cart=:postal_liv_cart, country_liv_cart=:country_liv_cart
          WHERE id_cart=:id_cart';
		magixglobal_model_db::layerDB()->update($sql,
			array(
				':id_cart' => $id_cart,
				':idprofil' => $idprofil,
				':firstname_cart' => $firstname,
				':lastname_cart' => $lastname,
				':email_cart' => $email,
				':phone_cart' => $phone,
				':street_cart' => $street,
				':city_cart' => $city,
				':vat_cart' => $vat,
                ':company_cart' => $company,
				':postal_cart' => $postal,
				':country_cart' => $country,
				':message_cart' => $message,
				':street_liv_cart' => $street_liv,
				':firstname_liv_cart' => $firstname_liv,
				':lastname_liv_cart' => $lastname_liv,
				':city_liv_cart' => $city_liv,
				':postal_liv_cart' => $postal_liv,
				':country_liv_cart' => $country_liv
			)
		);
	}

//u_cart_infos($id_cart,$firstname,$lastname,$email,$phone,$street,$city,$postal,$country,$message)
	/**
	 * @access protected
	 * Mise à jour du statu de l'envois du panier (transmission_cart)
	 * @param integer $id_cart
	 * @param bool $val_transmission [0,1]
	 */
	protected function u_transmission_cart($id_cart, $val_transmission)
	{
		$sql = 'UPDATE mc_plugins_cartpay SET
          transmission_cart=:transmission_cart
          WHERE id_cart=:id_cart';
		magixglobal_model_db::layerDB()->update($sql,
			array(
				':id_cart' => $id_cart,
				':transmission_cart' => $val_transmission
			)
		);
	}

	/**
	 * @access protected
	 * Supprime un élément du panier
	 * @param integer $id_item
	 * */
	protected function d_item_cart($id_item)
	{
		$sql = array('DELETE FROM mc_plugins_cartpay_items
		WHERE id_item = ' . $id_item);
		magixglobal_model_db::layerDB()->transaction($sql);
	}

	/**
	 * Return complete data by id_cart
	 * @param $id_cart
	 * @return array
	 */
	protected function s_complete_data($id_cart,$ext = array())
	{
		$joint = '';
		$grp = '';
		$sj = '';
		$pre = 'items';
		if(is_array($ext)) {
			foreach ($ext as $i => $j) {
				$joint .= " LEFT JOIN `".$j['t']."` AS `t$i` ON $pre.".$j['k']." = t$i.".$j['k'];
				$pre = "t$i";
				$sj .= ",t$i.*";

				if (isset($j['group'])) {}
			}
		}

		$sql = 'SELECT ord.id_cart,ord.id_order,ord.transaction_id_order,ord.shipping_price_order,ord.amount_order,ord.payment_order,ord.date_order,
        p.*,CATALOG_LIST_ID,CATALOG_LIST_NAME,CATALOG_LIST_QUANTITY,CATALOG_LIST_PRICE,conf.amount_tva
        FROM mc_plugins_cartpay_order AS ord
        JOIN mc_plugins_cartpay AS p ON(ord.id_cart=p.id_cart)
        LEFT OUTER JOIN (
            SELECT catalog.idcatalog,items.id_cart,catalog.titlecatalog,
            GROUP_CONCAT( CAST(items.idcatalog AS CHAR) ORDER BY items.id_item SEPARATOR "|" ) AS CATALOG_LIST_ID,
            GROUP_CONCAT( CAST(items.quantity_items AS CHAR) ORDER BY items.id_item SEPARATOR "|" ) AS CATALOG_LIST_QUANTITY,
            GROUP_CONCAT( CAST(items.price_items AS CHAR) ORDER BY items.id_item SEPARATOR "|" ) AS CATALOG_LIST_PRICE,
            GROUP_CONCAT( catalog.titlecatalog ORDER BY items.id_item SEPARATOR "|" ) AS CATALOG_LIST_NAME
            FROM mc_catalog AS catalog
            JOIN mc_plugins_cartpay_items as items ON(items.idcatalog = catalog.idcatalog)
            GROUP BY items.id_cart
        ) rel_cat ON ( rel_cat.id_cart= p.id_cart)
        JOIN mc_plugins_cartpay_tva AS t ON(p.country_cart = t.country)
        JOIN mc_plugins_cartpay_tva_conf AS conf ON(t.idtvac=conf.idtvac)
        WHERE p.id_cart = :id_cart';
		return magixglobal_model_db::layerDB()->selectOne($sql, array(
			':id_cart' => $id_cart
		));
	}

	/**
	 * @param $idprofil
	 * @return array
	 */
	protected function s_profil_data($idprofil)
	{
		$sql = 'SELECT ord.id_cart,ord.id_order,ord.transaction_id_order,ord.shipping_price_order,ord.amount_order,ord.payment_order,ord.date_order,
        p.*,CATALOG_LIST_ID,CATALOG_LIST_NAME,CATALOG_LIST_QUANTITY,CATALOG_LIST_PRICE,conf.amount_tva
        FROM mc_plugins_cartpay_order AS ord
        JOIN mc_plugins_cartpay AS p ON(ord.id_cart=p.id_cart)
        LEFT OUTER JOIN (
            SELECT catalog.idcatalog,items.id_cart,catalog.titlecatalog,
            GROUP_CONCAT( CAST(items.idcatalog AS CHAR) ORDER BY items.id_item SEPARATOR "|" ) AS CATALOG_LIST_ID,
            GROUP_CONCAT( CAST(items.quantity_items AS CHAR) ORDER BY items.id_item SEPARATOR "|" ) AS CATALOG_LIST_QUANTITY,
            GROUP_CONCAT( CAST(items.price_items AS CHAR) ORDER BY items.id_item SEPARATOR "|" ) AS CATALOG_LIST_PRICE,
            GROUP_CONCAT( catalog.titlecatalog ORDER BY items.id_item SEPARATOR "|" ) AS CATALOG_LIST_NAME
            FROM mc_catalog AS catalog
            JOIN mc_plugins_cartpay_items as items ON(items.idcatalog = catalog.idcatalog)
            GROUP BY items.id_cart
        ) rel_cat ON ( rel_cat.id_cart= p.id_cart)
        JOIN mc_plugins_cartpay_tva AS t ON(p.country_cart = t.country)
        JOIN mc_plugins_cartpay_tva_conf AS conf ON(t.idtvac=conf.idtvac)
        WHERE p.idprofil = :idprofil
        GROUP BY p.id_cart ORDER BY p.id_cart DESC';
		return magixglobal_model_db::layerDB()->select($sql, array(
			':idprofil' => $idprofil
		));
	}

	/**
	 * @param $idprofil
	 * @param $idbooking
	 * @return array
	 */
	protected function s_booking_info($idprofil, $idbooking)
	{
		$sql = 'SELECT DISTINCT bk.idbooking,bk.quantity_bk,bk.date_bk,pr.idprofil,pr.lastname_pr,pr.firstname_pr,pr.email_pr,catalog.titlecatalog
        FROM mc_plugins_booking AS bk
        JOIN mc_catalog_product AS p ON ( bk.idcatalog = p.idcatalog )
        JOIN mc_plugins_profil AS pr ON(bk.idprofil = pr.idprofil)
        JOIN mc_catalog AS catalog ON ( catalog.idcatalog = p.idcatalog )
        LEFT JOIN mc_catalog_c AS c ON ( c.idclc = p.idclc )
        LEFT JOIN mc_catalog_s AS s ON ( s.idcls = p.idcls )
        LEFT JOIN mc_lang AS lang ON ( catalog.idlang = lang.idlang )
        WHERE bk.idprofil = :idprofil AND bk.idbooking = :idbooking';
		return magixglobal_model_db::layerDB()->selectOne($sql, array(
			':idprofil' => $idprofil,
			':idbooking' => $idbooking
		));
	}

	/**
	 * @return array
	 */
	protected function fetchConfig()
	{
		$query = "SELECT *
                      FROM mc_plugins_cartpay_config";
		return magixglobal_model_db::layerDB()->selectOne($query);
	}

	/**
	 * Retourne la configuration de la TVA de base
	 * @param $data
	 * @return array
	 */
	protected function fetchTva($data,$controller)
	{
		if (is_array($data)) {
			// Si retourne tous les enregistrements ou un seul
			if (array_key_exists('fetch', $data)) {
				$fetch = $data['fetch'];
			} else {
				$fetch = 'all';
			}
			// Defini le context (configuration, les pays)
			if (array_key_exists('context', $data)) {
				$context = $data['context'];
			} else {
				$context = 'config';
			}

			if ($controller == 'public') {
				if ($fetch == 'all') {
					if ($context == 'config') {
						// Configuration
						$query = "SELECT *
                      FROM mc_plugins_cartpay_tva_conf";
						return magixglobal_model_db::layerDB()->select($query);
					} elseif ($context == 'country') {
						// Liste des pays avec la zone, tva, etc
						$query = "SELECT t.*,conf.zone_tva,conf.amount_tva
                      FROM mc_plugins_cartpay_tva AS t
                      JOIN mc_plugins_cartpay_tva_conf AS conf ON(t.idtvac=conf.idtvac)
                      ORDER BY t.country ASC";
						return magixglobal_model_db::layerDB()->select($query);
					}
				} elseif ($fetch == 'one') {
					if ($context == 'config') {
						$query = "SELECT t.*,conf.zone_tva,conf.amount_tva
                      FROM mc_plugins_cartpay_tva AS t
                      JOIN mc_plugins_cartpay_tva_conf AS conf ON(t.idtvac=conf.idtvac)
                      WHERE t.country = :country";
						return magixglobal_model_db::layerDB()->selectOne($query, array(':country' => $data['country']));
					}
				}
			} elseif ($controller == 'admin') {
				if ($fetch == 'all') {
					if ($context == 'config') {
						// Configuration
						$query = "SELECT *
                      FROM mc_plugins_cartpay_tva_conf";
						return magixglobal_model_db::layerDB()->select($query);
					} elseif ($context == 'country') {
						// Liste des pays avec la zone, tva, etc
						$query = "SELECT t.*,conf.zone_tva,conf.amount_tva
                      FROM mc_plugins_cartpay_tva AS t
                      JOIN mc_plugins_cartpay_tva_conf AS conf ON(t.idtvac=conf.idtvac)
                      ORDER BY conf.zone_tva DESC,t.country ASC";
						return magixglobal_model_db::layerDB()->select($query);
					}
				} elseif ($fetch == 'one') {
					if ($context == 'config') {
						$query = "SELECT *
                      FROM mc_plugins_cartpay_tva_conf WHERE
                      zone_tva=:zone_tva";
						return magixglobal_model_db::layerDB()->selectOne($query, array(':zone_tva' => $data['zone_tva']));
					}
				}
			}
		}
	}

	/**
	 * @return array
	 */
	protected function s_count_cart_order(){
		$sql = 'SELECT count(ord.id_order) AS total
		FROM mc_plugins_cartpay_order AS ord';
		return magixglobal_model_db::layerDB()->selectOne($sql);
	}

	/**
	 * @return array
	 */
	protected function s_count_cart_order_currentDate(){
		$sql = 'SELECT count(ord.id_order) AS total
		FROM mc_plugins_cartpay_order AS ord
		WHERE DATE_FORMAT(date_order, "%Y%m%d") = DATE_FORMAT(NOW(), "%Y%m%d")';
		return magixglobal_model_db::layerDB()->selectOne($sql);
	}

	/**
	 * @return array
	 */
	protected function s_count_cart(){
		$sql = 'SELECT count(p.id_cart) AS total
		FROM mc_plugins_cartpay AS p';
		return magixglobal_model_db::layerDB()->selectOne($sql);
	}

	/*protected function s_cart_order($limit=5,$max=null,$offset=null){
		$limit = $limit ? ' LIMIT '.$max : '';
		$offset = !empty($offset) ? ' OFFSET '.$offset: '';
		$sql='SELECT ord.id_cart,ord.id_order,ord.transaction_id_order,ord.shipping_price_order,ord.amount_order,ord.payment_order,ord.currency_order,ord.date_order,
		p.*,CATALOG_LIST_NAME,CATALOG_LIST_QUANTITY,CATALOG_LIST_PRICE
		FROM mc_plugins_cartpay_order AS ord
		JOIN mc_plugins_cartpay AS p ON(ord.id_cart=p.id_cart)
		LEFT OUTER JOIN (
			SELECT catalog.idcatalog,items.id_cart,catalog.titlecatalog,
			GROUP_CONCAT( CAST(items.quantity_items AS CHAR) ORDER BY items.id_item SEPARATOR "|" ) AS CATALOG_LIST_QUANTITY,
			GROUP_CONCAT( CAST(items.price_items AS CHAR) ORDER BY items.id_item SEPARATOR "|" ) AS CATALOG_LIST_PRICE,
			GROUP_CONCAT( catalog.titlecatalog ORDER BY items.id_item SEPARATOR "|" ) AS CATALOG_LIST_NAME
			FROM mc_catalog AS catalog
			JOIN mc_plugins_cartpay_items as items ON(items.idcatalog = catalog.idcatalog)
			GROUP BY items.id_cart
		) rel_cat ON ( rel_cat.id_cart= p.id_cart)
		ORDER BY ord.date_order DESC'.$limit.$offset;
		return magixglobal_model_db::layerDB()->select($sql);
	}*/

	/**
	 * @param $data
	 * @return array
	 */
	protected function fetchOrder($data){
		if(is_array($data)) {
			// Si retourne tous les enregistrements ou un seul
			if (array_key_exists('fetch', $data)) {
				$fetch = $data['fetch'];
			} else {
				$fetch = 'all';
			}
			if (array_key_exists('limit', $data)) {
				$limit_clause = null;
				if (is_int($data['limit'])) {
					$limit_clause = ' LIMIT ' . $data['limit'];
				}
			}
			if (array_key_exists('offset', $data)) {
				$offset_clause = null;
				if(!empty($data['offset'])){
					$offset_clause = ' OFFSET '.$data['offset'];
				}
			}
			$offset = !empty($offset) ? ' OFFSET '.$offset: '';
			if($fetch == 'all') {
				if (array_key_exists('offset', $data)) {
					$query="SELECT ord.id_cart,ord.id_order,ord.transaction_id_order,ord.shipping_price_order,ord.amount_order,ord.payment_order,
                    ord.currency_order,ord.date_order,
                    lang.iso,conf.amount_tva,
                            p.*,CATALOG_LIST_NAME,CATALOG_LIST_QUANTITY,CATALOG_LIST_PRICE
                            FROM mc_plugins_cartpay_order AS ord
                            JOIN mc_plugins_cartpay AS p ON(ord.id_cart=p.id_cart)
                            LEFT OUTER JOIN (
                                SELECT catalog.idcatalog,items.id_cart,catalog.titlecatalog,
                                GROUP_CONCAT( CAST(items.quantity_items AS CHAR) ORDER BY items.id_item SEPARATOR '|' ) AS CATALOG_LIST_QUANTITY,
                                GROUP_CONCAT( CAST(items.price_items AS CHAR) ORDER BY items.id_item SEPARATOR '|' ) AS CATALOG_LIST_PRICE,
                                GROUP_CONCAT( catalog.titlecatalog ORDER BY items.id_item SEPARATOR '|' ) AS CATALOG_LIST_NAME
                                FROM mc_catalog AS catalog
                                JOIN mc_plugins_cartpay_items as items ON(items.idcatalog = catalog.idcatalog)
                                GROUP BY items.id_cart
                            ) rel_cat ON ( rel_cat.id_cart= p.id_cart)
                            JOIN mc_plugins_cartpay_tva AS t ON(p.country_cart = t.country)
                            JOIN mc_plugins_cartpay_tva_conf AS conf ON(t.idtvac=conf.idtvac)
                            JOIN mc_lang AS lang ON(p.idlang = lang.idlang)
                            ORDER BY ord.date_order DESC
                            {$limit_clause}
                            {$offset_clause}";
					return magixglobal_model_db::layerDB()->select($query);
				}
			}elseif($fetch == 'count'){
				$query = 'SELECT count(ord.id_order) AS total
		          FROM mc_plugins_cartpay_order AS ord';
				return magixglobal_model_db::layerDB()->selectOne($query);
			}
		}
	}
	
	/**
	 * @param $data
	 */
	protected function insert($data){
		if(is_array($data)){
			if (array_key_exists('fetch', $data)) {
				$fetch = $data['fetch'];
			} else {
				$fetch = 'config';
			}
			if($fetch == 'config') {
				$sql = 'INSERT INTO mc_plugins_cartpay_config (mail_order,mail_order_from,profil,online_payment,bank_wire,hipay,ogone,shipping,account_owner,contact_details,bank_address)
		        VALUE(:mail_order,:mail_order_from,:profil,:online_payment,:bank_wire,:hipay,:ogone,:shipping,:account_owner,:contact_details,:bank_address)';
				magixglobal_model_db::layerDB()->insert($sql,
					array(
						':mail_order'        => $data['mail_order'],
						':mail_order_from'   => $data['mail_order_from'],
						':online_payment'    => $data['online_payment'],
						':profil'            => $data['profil'],
						':bank_wire'         => $data['bank_wire'],
						':hipay'             => $data['hipay'],
						':ogone'             => $data['ogone'],
						':shipping'          => $data['shipping'],
						':account_owner'     => $data['account_owner'],
						':contact_details'   => $data['contact_details'],
						':bank_address'      => $data['bank_address']
					)
				);
			}elseif($fetch == 'tvac') {
				$sql = 'INSERT INTO mc_plugins_cartpay_tva_conf (amount_tva,zone_tva)
		        VALUE(:amount_tva,:zone_tva)';
				magixglobal_model_db::layerDB()->insert($sql,
					array(
						':amount_tva'    => $data['amount_tva'],
						':zone_tva'      => $data['zone_tva']
					)
				);
			}elseif($fetch == 'tva') {
				$sql = 'INSERT INTO mc_plugins_cartpay_tva (iso,country,idtvac)
		        VALUE(:iso,:country,:idtvac)';
				magixglobal_model_db::layerDB()->insert($sql,
					array(
						':iso'    => $data['iso'],
						':country'      => $data['country'],
						':idtvac'      => $data['idtvac']
					)
				);
			}
		}
	}

	/**
	 * @param $data
	 */
	protected function uData($data){
		if(is_array($data)){
			if (array_key_exists('fetch', $data)) {
				$fetch = $data['fetch'];
			} else {
				$fetch = 'config';
			}
			if($fetch == 'config') {
				$sql = 'UPDATE mc_plugins_cartpay_config
                SET mail_order=:mail_order,mail_order_from=:mail_order_from,profil=:profil,online_payment=:online_payment,bank_wire=:bank_wire,hipay=:hipay,ogone=:ogone,shipping=:shipping,
                account_owner=:account_owner,contact_details=:contact_details,bank_address=:bank_address
                WHERE idconfig=:edit';
				magixglobal_model_db::layerDB()->update($sql,
					array(
						':edit'             => $data['edit'],
						':mail_order'        => $data['mail_order'],
						':mail_order_from'   => $data['mail_order_from'],
						':profil'            => $data['profil'],
						':online_payment'    => $data['online_payment'],
						':bank_wire'         => $data['bank_wire'],
						':hipay'             => $data['hipay'],
						':ogone'             => $data['ogone'],
						':shipping'          => $data['shipping'],
						':account_owner'     => $data['account_owner'],
						':contact_details'   => $data['contact_details'],
						':bank_address'      => $data['bank_address']
					));
			}elseif($fetch == 'tvac') {
				$sql = 'UPDATE mc_plugins_cartpay_tva_conf
                SET amount_tva=:amount_tva,zone_tva=:zone_tva
                WHERE idtvac=:edit';
				magixglobal_model_db::layerDB()->update($sql,
					array(
						':edit'          => $data['edit'],
						':amount_tva'    => $data['amount_tva'],
						':zone_tva'      => $data['zone_tva']
					));
			}
		}
	}

	/**
	 * @param $remove_tva
	 */
	protected function delete($remove_tva){
		$sql = 'DELETE FROM mc_plugins_cartpay_tva WHERE idtva = :remove';
		magixglobal_model_db::layerDB()->delete($sql,
			array(
				':remove'   =>  $remove_tva
			)
		);
	}

	// --- Modules
	/**
	 * Get mod registration
	 * @param $name
	 * @return array
	 */
	protected function g_mod($name)
	{
		$query = "SELECT * FROM `mc_plugins_cartpay_module` WHERE module_name = :mname";

		return magixglobal_model_db::layerDB()->selectOne($query,array(':mname' => $name));
	}

	/**
	 * Register cartpay module
	 * @param $name
	 * @param $active
	 */
	protected function register_module($name,$active)
	{
		$query = "INSERT INTO `mc_plugins_cartpay_module` (module_name,active) VALUES (:mname,:active)";

		magixglobal_model_db::layerDB()->insert($query,array(':mname' => $name,':active' => $active));
	}

	/**
	 * Update register cartpay module
	 * @param $name
	 * @param $active
	 */
	protected function u_register_module($name,$active)
	{
		$query = "UPDATE `mc_plugins_cartpay_module` SET active = :active WHERE module_name = :mname";

		magixglobal_model_db::layerDB()->update($query,array(':mname' => $name,':active' => $active));
	}

	/**
	 * Unregister cartpay module
	 * @param $name
	 */
	protected function unregister_module($name)
	{
		$query = "DELETE FROM `mc_plugins_cartpay_module` WHERE module_name = :mname";

		magixglobal_model_db::layerDB()->delete($query,array(':mname' => $name));
	}

	/**
	 * Get all active modules
	 * @return array
	 */
	protected function g_module()
	{
		$query = "SELECT * FROM `mc_plugins_cartpay_module` WHERE `active` = 1";

		return magixglobal_model_db::layerDB()->select($query);
	}
}
?>