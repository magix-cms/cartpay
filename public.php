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
require_once('db/cartpay.php');
class plugins_cartpay_public extends database_plugins_cartpay {
    /**
     * @var frontend_controller_plugins
     */
    protected $template,$modelSystem, $module, $activeMods;
    public $pstring1,$pstring2;
    /**
     * @var string
     */
    public $add_cart,$delete_item,$get_nbr_items,$get_price_items,$get_amount_to_pay,$idprofil_session,
        $shipping_price,$json_cart,$item_to_delete,$id_cart_to_send,$devis_to_send,$booking,$payment,$tva_amount,$idcatalog,$booking_quantity,$tva_country;
    public $item_qty,$quantity_qty;
    public $item_attr,$attr;
    public $logo_perso,$logo;
    public $mod, $action;
    /**
     *
     */
    function __construct(){
        if(magixcjquery_filter_request::isGet('add_cart')){
            $this->add_cart = magixcjquery_form_helpersforms::inputNumeric($_GET['add_cart']);
        }
        if(magixcjquery_filter_request::isGet('delete_item')){
            $this->delete_item = magixcjquery_form_helpersforms::inputNumeric($_GET['delete_item']);
        }
        if(magixcjquery_filter_request::isGet('get_nbr_items')){
            $this->get_nbr_items = magixcjquery_form_helpersforms::inputClean($_GET['get_nbr_items']);
        }
        if(magixcjquery_filter_request::isGet('get_price_items')){
            $this->get_price_items = magixcjquery_form_helpersforms::inputClean($_GET['get_price_items']);
        }
        if(magixcjquery_filter_request::isGet('get_amount_to_pay')){
            $this->get_amount_to_pay = magixcjquery_form_helpersforms::inputNumeric($_GET['get_amount_to_pay']);
        }
        if(magixcjquery_filter_request::isGet('json_cart')){
            $this->json_cart = magixcjquery_form_helpersforms::inputNumeric($_GET['json_cart']);
        }
        if(magixcjquery_filter_request::isPost('item_to_delete')){
            $this->item_to_delete = magixcjquery_form_helpersforms::inputNumeric($_POST['item_to_delete']);
        }
        if(magixcjquery_filter_request::isPost('id_cart_to_send')){
            $this->id_cart_to_send = magixcjquery_form_helpersforms::inputClean($_POST['id_cart_to_send']);
        }
        if(magixcjquery_filter_request::isGet('send_devis')){
            $this->devis_to_send = magixcjquery_form_helpersforms::inputClean($_GET['send_devis']);
        }
        if(magixcjquery_filter_request::isGet('booking')){
            $this->booking = magixcjquery_form_helpersforms::inputClean($_GET['booking']);
        }
        if(magixcjquery_filter_request::isGet('pstring1')){
            $this->pstring1 = magixcjquery_form_helpersforms::inputClean($_GET['pstring1']);
        }
        if(magixcjquery_filter_request::isPost('idcatalog')){
            $this->idcatalog = magixcjquery_form_helpersforms::inputNumeric($_POST['idcatalog']);
        }
        if(magixcjquery_filter_request::isPost('booking_quantity')){
            $this->booking_quantity = magixcjquery_form_helpersforms::inputNumeric($_POST['booking_quantity']);
        }
        if(magixcjquery_filter_request::isPost('quantity_qty')){
         $this->quantity_qty = magixcjquery_form_helpersforms::inputNumeric($_POST['quantity_qty']);
        }
        if(magixcjquery_filter_request::isPost('attr')){
         $this->attr = magixcjquery_form_helpersforms::inputNumeric($_POST['attr']);
        }
        if(magixcjquery_filter_request::isPost('item_qty')){
            $this->item_qty = magixcjquery_form_helpersforms::inputNumeric($_POST['item_qty']);
        }
        if(magixcjquery_filter_request::isPost('item_attr')){
            $this->item_attr = magixcjquery_form_helpersforms::inputNumeric($_POST['item_attr']);
        }
        if(magixcjquery_filter_request::isGet('pstring2')){
            $this->pstring2 = magixcjquery_form_helpersforms::inputClean($_GET['pstring2']);
        }
        //
        if(magixcjquery_filter_request::isPost('tva_country')){
            $this->tva_country = magixcjquery_form_helpersforms::inputClean($_POST['tva_country']);
        }
        //IMAGE
        if(isset($_FILES['logo_perso']["name"])){
            $this->logo_perso = magixcjquery_url_clean::rplMagixString($_FILES['logo_perso']["name"]);
        }
        if(magixcjquery_filter_request::isPost('logo')){
            $this->logo = magixcjquery_form_helpersforms::inputClean($_POST['logo']);
        }
        if(magixcjquery_filter_request::isSession('idprofil')){
            $this->idprofil_session = magixcjquery_form_helpersforms::inputNumeric($_SESSION['idprofil']);
        }
        // template
        $this->template = new frontend_controller_plugins;
        //Frais de transport
        $this->shipping_price = $this->template->getTplVars('shipping');
        //Frais de transport offert au dessus du montant
        $this->free_shipping_amount = '0.00';
        //Montant de la TVA
        $this->tva_amount = 00;
        $this->modelSystem = new magixglobal_model_system();
        // Module
        if(class_exists('plugins_cartpay_module')) {
            $this->module = new plugins_cartpay_module();
        }
        if(magixcjquery_filter_request::isGet('mod')){
            $this->mod = magixcjquery_form_helpersforms::inputClean($_GET['mod']);
        }
        if(magixcjquery_filter_request::isGet('action')){
            $this->action = magixcjquery_form_helpersforms::inputClean($_GET['action']);
        }
    }
    /**
     * Retourne le message de notification
     * @param $type
     * @param bool $display
     */
    private function getNotify($type,$display = true){
        $this->template->assign('message',$type);
        if($display){
            $this->template->display('message.tpl');
        }else{
            $fetch = $this->template->fetch('message.tpl');
            $this->template->assign('statut_message',$fetch);
        }
    }

    /**
     * Charge le fichier de configuration de base de smarty
     * @access private
     */
    private function _loadConfigVars(){
        frontend_controller_plugins::create()->configLoad();
    }
    /* ################# CONFIG ###################*/
    /**
     * Retourne le tableau des données de configuration
     * @return array
     */
    private function getConfigData(){
        $config = parent::fetchConfig();
        return $config;
    }
    /* ################ TVA ##################*/
    /**
     * @param $row
     * @return array
     */
    private function setItemsTvaData ($row)
    {

        $data = null;
        $newData = array();
        foreach($row as $key => $value){
            $newData[$key]['idtva'] = $value['idtva'];
            $newData[$key]['idtvac'] = $value['idtvac'];
            $newData[$key]['country'] = $value['country'];
            $newData[$key]['iso'] = $value['iso'];
            $newData[$key]['zone'] = $value['zone_tva'];
            $newData[$key]['amount'] = $value['amount_tva'];
        }
        return $newData;
    }
    /**
     * @param $max
     * @return array
     */
    private function getItemsTvaData($dataTva){
        $data = parent::fetchTva(
            $dataTva,'public'
        );
        return $this->setItemsTvaData($data);
    }

    private function getItemTvaData($dataTva){
        $data = parent::fetchTva(
            $dataTva,'public'
        );
        return $data;
    }

	protected function add_new_item($id_cart,$idcatalog,$quantity,$current_price)
	{
		parent::i_cart_items($id_cart,$idcatalog,$quantity,$current_price);
		$lastItem = parent::s_last_cart_item($id_cart);

		if(!empty($this->activeMods)) {
			foreach ($this->activeMods as $name => $mod) {
				if(method_exists($mod,'add_cart_item')) {
					$mod->add_cart_item($lastItem['id_item'],$_POST);
				}
			}
		}
    }

    /**
     * Retourne les variables de configuration
     * @access private
     */
    /**private function _getVars(){
        $loadConfig = frontend_controller_plugins::create();
        $loadConfig->configLoad();
        return $loadConfig->getConfigVars();
    }*/

    /**
     * Ajoute un élément au panier
     * @access private
     * @param $values
     * @param $session_key
     */
    private function add_item_cart($values,$session_key){
        if(is_array($values)){
            $attr = null;
            //récupération des valeurs postes pour insertion et récupération DB
            $idcatalog = $values['idcatalog'];
            $quantity = $values['product_quantity'];
            $current_price =  $values['product_price'];
            $attr = isset($values['attr']) ? $values['attr'] : null;
            $lang = frontend_db_lang::s_id_current_lang(frontend_model_template::current_Language());
            //récupération des donnée panier en fonction le clé de session
            $data_cart = parent::s_cart_session($session_key);
            if ($data_cart == null){
                parent::i_cart_session($lang['idlang'],$session_key);
                $data_cart = parent::s_cart_session($session_key);
            }
            $id_cart = $data_cart['id_cart'];
            //Vérifie si l'item est déjà dans le panier
            $v_item = parent::s_cart_item_catalog($id_cart,$idcatalog);

			if($v_item['idcatalog'] == null){
				$this->add_new_item($id_cart,$idcatalog,$quantity,$current_price);
			} else {
				$exist = true;
				if(!empty($this->activeMods)) {
					foreach ($this->activeMods as $name => $mod) {
						if(method_exists($mod,'exist_item')) {
							$exist = $mod->exist_item($v_item['id_item'],$_POST);
						}
					}
				}
				if ($exist) {
					parent::u_cart_item_qty($v_item['id_item'],$v_item['quantity_items']+$quantity);
				} else {
					$this->add_new_item($id_cart,$idcatalog,$quantity,$current_price);
				}
			}

            //Mise à jour du calcul d'items dans le panier
            $count_items = parent::count_cart_items($id_cart);
            parent::u_cart_items($id_cart,$count_items['total']);
        }
    }

    /**
     * Retourne le nombre d'éléments dans un panier suivant clée de session
     * @access private
     * @param $session_key
     * @param $create
     */
    public function load_cart_data($session_key,$create){
        $data_cart = parent::s_cart_session($session_key);
        $amount_to_pay = 0;
        if($data_cart != null){
            //récupération id_panier, mis à null si pas de produits lié
            $id_cart = $data_cart['id_cart'];
            $data_items_cart = parent::s_cart_items($id_cart);
            $id_cart = $data_items_cart != null ? $id_cart : 'null';
            //récupération du montant total de la commande
            $amount_to_pay = ($id_cart != null) ? $this->load_cart_amount($id_cart) : 0;
            $amount_to_pay = $amount_to_pay['amount_to_pay'];
            $cart_amount = $this->load_cart_amount($id_cart);
            
            $shipping =  $cart_amount['shipping_ttc'];
            if($data_cart['country_cart']!=null){
                $tva = $this->getItemTvaData(
                    array(
                        'fetch'=>'one',
                        'context'=>'config',
                        'country'=>$data_cart['country_cart']
                    )
                );
                $calculate_tva = $tva['amount_tva'];
            }else{
                $calculate_tva = $this->tva_amount ;
            }

            // formate la TVA avant le calcule
            $tva_amount = floatval('1.'.sprintf("%.02d", $calculate_tva));
            $tax_amount = $cart_amount['amount_products'] - ($cart_amount['amount_products']/ $tva_amount);

            //$amount_pay_with_tax = ($amount_to_pay-$shipping);//$tax_amount+$cart_amount['ammount_products'];
            $amount_pay_with_tax = $cart_amount['amount_products'];
            //Assignation des coordonnée
            if($this->pstring1 === 'payment'){
                if(class_exists('plugins_ogone_public')) {
                    //@todo Revoir la partie ogone
                    $ogone = new plugins_ogone_public();
                    $lang = frontend_model_template::current_Language() . '_' . strtoupper(frontend_model_template::current_Language());
                    $ogoneProcess = $ogone->getData(
                        array(
                            'plugin'    =>  'cartpay',
                            'formSubmitImageUrl'=> '/plugins/ogone/img/ogone-'.frontend_model_template::current_Language().'.png',
                            'transaction'   =>  $data_cart['id_cart'],
                            'lastname'      =>  $data_cart['lastname_cart'],
                            'firstname'     =>  $data_cart['firstname_cart'],
                            'email'         =>  $data_cart['email_cart'],
                            'language'=>    $lang,
                            'amount'=>    number_format($amount_pay_with_tax, 2, '', ''),
                            'COMPLUS'=>   'module=cartpay&idprofil='.$data_cart['idprofil']."&shipping=".$shipping,
                        )
                    );
                    $create->assign('ogoneProcess',$ogoneProcess);
                    //"&amount_profil=".$cart_amount['amount_profil']
                }

                if(class_exists('plugins_hipay_public')){
                    $hipay = new plugins_hipay_public();
                    $hipayProcess = $hipay->getData(
                        array(
                            'plugin'    =>  'cartpay',
                            'key'       =>  $session_key,
                            'order'     =>  $id_cart,
                            'amount'    =>  $amount_pay_with_tax,
                            'shipping'  =>  $shipping,
                            'locale'    =>  $tva['iso'],
                            'customerEmail'=> $data_cart['email_cart']
                        )
                    );

                    $create->assign('hipayProcess',$hipayProcess);
                }


            }
            $assign_exclude = array(
                'amount_to_pay','id_cart','nbr_items_cart','session_key_cart'
            );
            foreach($data_cart as $key => $val){
                if( !(array_search($key,$assign_exclude) ) ){
                    $create->assign($key,$val);
                }
            }
        }else{
            $id_cart = 'null';
        }

        $create->assign('id_cart',$id_cart);
        $create->assign('amount_order',$amount_pay_with_tax);
    }
    /**
     * Retourne le nombre d'éléments dans un panier suivant clée de session
     * @access private
     * @param array values
     */
    public function load_cart_nbr_items($session_key){
        $data_cart = parent::s_cart_session($session_key);
        $nbr_items_cart = $data_cart['nbr_items_cart'];
        return $nbr_items_cart;
    }

    /**
     * Retourne le prix du panier suivant clée de session
     * @access private
     * @param array values
     * @return string
     */
    public function load_cart_price_items($session_key){
        $idcart = parent::s_idcart_session($session_key);
        $amount_products = '0.00';
        $data_cart = parent::s_cart_items($idcart['id_cart']);
        if ($data_cart != null){
            foreach($data_cart as $item){
                $price_item = $item['price_items'];
                $quantity_item = $item['quantity_items'];
                $price = $price_item*$quantity_item;
                $amount_products += $price;
            }
        }
        return number_format($amount_products, 2, '.', '');
    }

    /**
     * Récupère la TVA et la formate
     * @param $comp_data
     * @return float
     */
    public function calculate_tva($id_cart)
    {
        $comp_data = parent::s_customer_info($id_cart);

        // *** Récupération taux TVA
        if(isset($this->tva_country)) {
            $tva = $this->getItemTvaData(
                array(
                    'fetch' => 'one',
                    'context' => 'config',
                    'country' => $this->tva_country
                )
            );
            $calculate_tva = $tva['amount_tva'];
        } elseif($comp_data['country_cart'] != null) {
            $tva = $this->getItemTvaData(
                array(
                    'fetch' => 'one',
                    'context' => 'config',
                    'country' => $comp_data['country_cart']
                )
            );
            $calculate_tva = $tva['amount_tva'];
        } else {
            $calculate_tva = $this->tva_amount;
        }

        return $calculate_tva;
    }

    /**
     * Retourne le prix total à payer (prix des produits + taxes 21% + shipping)
     * Sous forme de table ('amount_products', 'amount_tax', 'shipping', 'amount_to_pay')
     * Avec vérification dans le cas ou le prix encodé serait différent du prix du catalog
     * Update le prix si nécessaire
     * @access private
     * @param int id_cart
     * @return array
     */
    public function load_cart_amount($id_cart){
        $amount_to_pay = null;
        $amount_products = '0.00';
        $amount_product_hvat = '0.00';
        $quantity_total = '0';
        $promo_amount = 0;
        $profil_amount = 0;
        $amount_tva = 0;
        $shipping_ttc = 0;
        $impact = array();
        $data_cart = parent::s_cart_items($id_cart);
        $getConfigCart = $this->getConfigData();
        $tva_rate = $this->calculate_tva($id_cart);
        // Formate la TVA
        $tva = 1 + (floatval($tva_rate) / 100);
        //$tva = floatval('1.' . sprintf("%.02d", $tva_rate));

        /**
         * --- Cart Amount
         */
        if ($data_cart != null){
            foreach($data_cart as $item){
                $price_catalog = parent::s_catalog_price($item['idcatalog']);
                $price_catalog = $price_catalog['price'];
                $price_item = $item['price_items'];
                $quantity_item = $item['quantity_items'];

                /*if($item['weight'] != null){
                    $weight = ($quantity_item*$item['weight']);
                }else{
                    $weight = '0';
                }*/
                if ($price_item != $price_catalog){
                    //update et réasigne
                    //parent::u_cart_item_price($item['id_item'],$price_catalog);
                    parent::u_cart_item_price($item['id_item'],$price_item);
                    $price = ($price_catalog*$quantity_item);
                }else{
                    $price = ($price_item*$quantity_item);
                }
                $amount_products += $price;
                $quantity_total += $quantity_item;
            }

            $amount_product_hvat = floatval($amount_products) / $tva;
            $amount_tva = $amount_tva + round(($amount_products - $amount_product_hvat),2);
        }


        /*
        $member = new plugins_profil_public();
        if(isset($_SESSION['idprofil'])){
            $profilData = $member->setAccountData($_SESSION['idprofil']);
        }
        if(isset($_SESSION['idprofil'])){
            $country = $profilData['country'];
        }else{
            $country = 'BE';
        }
        $shipping = null;
        if(isset($_POST['period_ship'])){
            $period_ship = $_POST['period_ship'];
        }else{
            $period_ship = 'day';
        }

        $dataShip = $collectionShipping->getItemData($country,$quantity_total,$period_ship);

        $shipping = $dataShip['price'];*/
        //$shipping_tva = number_format($dataShip['price']*floatval('0.21'), 2, '.', '');
        //$shipping_ttc = $shipping+$shipping_tva;

        /**
         * --- Cashback system
         */
        if(class_exists('plugins_cashback_public')) {
            $collectionCashback = new plugins_cashback_public();
            if (isset($_POST['v_code'])) {
                $promo_amount = $collectionCashback->selectPromo($_POST['v_code']);
            } else {
                $promo_amount = 0;
            }
            /*if(isset($_POST['profil_cashback'])){
                $setTotalProfil = $collectionCashback->setTotalProfil();
                $profil_amount = $setTotalProfil['sumount'];
            }else{
                $profil_amount = 0;
            }*/
        }

        /**
         * --- Total amount
         */
        $total = $amount_products;

        if($total <= $promo_amount || $total <= $profil_amount) {
            $amount_to_pay = $total;
        } elseif($total <= ($promo_amount + $profil_amount)) {
            $amount_to_pay = $total;
        } else {
            //$amount_to_pay =  $total - $promo_amount - $profil_amount;
            $amount_to_pay =  $total - $promo_amount;
        }

        /**
         * --- Pass through active mods and search after cart impact
         */
        if(!empty($this->activeMods)) {
            foreach ($this->activeMods as $name => $mod) {
                if(method_exists($mod,'cart_impact')) {
                    $impact[$name] = $mod->cart_impact($getConfigCart,$amount_products,$tva);
                    foreach($impact[$name]['update'] as $var => $upd) {
                        switch ($var) {
                            case 'amount_tva':
                                $amount_tva = $amount_tva + $upd;
                                break;
                            case 'total':
                                $amount_to_pay = $amount_to_pay + $upd;
                                break;
                        }
                    }

                    unset($impact[$name]['update']);
                }
            }
        }
        //$shipping = ($amount_products < $this->free_shipping_amount) ? $this->shipping_price : '0.00';

        /**
         * retourne un tableau de données formatée
         */
        $prices = array (
            'amount_hvat'       => number_format($amount_product_hvat, 2, '.', ''),
            'amount_products'   => number_format($amount_products, 2, '.', ''),
            'amount_vat'        => number_format($amount_tva, 2, '.', ''),
            'tax_rate'          => $tva_rate,
            'amount_promo'      => number_format($promo_amount, 2, '.', ''),
            'amount_profil'     => number_format($profil_amount, 2, '.', ''),
            'amount_to_pay'     => number_format($amount_to_pay, 2, '.', ''),
            'quantity_total'    => $quantity_total,
            'shipping_ttc'      => $shipping_ttc
        );

        if(isset($impact) && !empty($impact)) {
            foreach ($impact as $name => $var) {
                $prices = $prices + $var;
            }
        }
        return $prices;
    }

    /**
     * @param $row
     * @return array
     */
    private function setItemCartData($row){
        $data = null;
        $newData = array();
        $ModelImagepath     =   new magixglobal_model_imagepath();
        $ModelTemplate      =   new frontend_model_template();

        foreach($row as $key => $value){
            $urlProduct = magixglobal_model_rewrite::filter_catalog_product_url(
                $value['iso'],
                $value['pathclibelle'],
                $value['idclc'],
                $value['pathslibelle'],
                $value['idcls'],
                $value['urlcatalog'],
                $value['idproduct'],
                true
            );
            $newData[$key]['id_item']         = $value['id_item'];
            $newData[$key]['idcatalog']       = $value['idcatalog'];
            $newData[$key]['titlecatalog']    = $value['titlecatalog'];
            if($value['imgcatalog']) {
                $newData[$key]['imgSrc']   = array(
                    'small'  =>
                        $ModelImagepath->filterPathImg(
                            array(
                                'filtermod' =>  'catalog',
                                'img'       =>  'mini/'.$value['imgcatalog'],
                                'levelmod'  =>  ''
                            )
                        ),
                    'medium'    =>
                        $ModelImagepath->filterPathImg(
                            array(
                                'filtermod' =>  'catalog',
                                'img'       =>  'medium/'.$value['imgcatalog'],
                                'levelmod'  =>  ''
                            )
                        ),
                    'large' =>
                        $ModelImagepath->filterPathImg(
                            array(
                                'filtermod' =>  'catalog',
                                'img'       =>  'product/'.$value['imgcatalog'],
                                'levelmod'  =>  ''
                            )
                        )
                );
            }
            $newData[$key]['imgSrc']['default']   =
                $ModelImagepath->filterPathImg(
                    array(
                        'img'=>'skin/'.
                            $ModelTemplate->frontendTheme()->themeSelected().
                            '/img/catalog/product-default.png'
                    )
                );
            $newData[$key]['imgcatalog']      = $value['imgcatalog'];
            $newData[$key]['urlproduct']      = $urlProduct;
            $newData[$key]['quantity']        = $value['quantity_items'];
            $newData[$key]['price']           = $value['price_items'];
            $newData[$key]['idattr']          = $value['idattr'];
            if($value['idattr'] && $value['idattr'] != null) {
                $newData[$key]['title_attr'] = $value['title_attribute'];
                $newData[$key]['idgroup']    = $value['idgroup'];
            }
            //$newData[$key]['fixed_costs']     = $value['fixed_costs'];
            //$newData[$key]['weight']          = !is_null($value['weight']) ? ($value['weight']*$value['quantity_items']) : null;
            //$newData[$key]['marking_costs']   = !is_null($value['marking_costs']) ? $value['marking_costs']/*($value['quantity_items']*$value['marking_costs'])*/ : null;
            $newData[$key]['price_products']  = ($value['price_items']);
            $newData[$key]['sub_amount']      = number_format(($value['quantity_items']*$value['price_items']), 2, '.', '');
        }
        return $newData;
    }

    /**
     * Retourne un tableau des produits dans le panier
     * @param $id_cart
     * @return array
     */
    private function getItemCartData($id_cart){
        $dataCart = parent::s_cart($id_cart);

		$ext = array();
		if(!empty($this->activeMods)) {
			foreach ($this->activeMods as $name => $mod) {
				if(method_exists($mod,'slct_ext')) {
					$joint = $mod->slct_ext();
					$ext = $ext + $joint;
				}
			}
		}
		$dataItem = parent::s_cart_items($dataCart['id_cart'],$ext);

        //if (key_exists('attribute',$this->activeMods)) {
        //    $this->template->assign('attribute',true);
        //    $dataItem = parent::s_cart_items_attr($dataCart['id_cart']);
        //} else {
        //    $dataItem = parent::s_cart_items($dataCart['id_cart']);
        //}

        return $this->setItemCartData($dataItem);
    }

    /**
     * Retourne un tableau des données de prix et calcul du montant total
     * @param $id_cart
     * @return array
     */
    private function getItemPriceData($id_cart){
        $Dataprices = $this->load_cart_amount($id_cart);
        return $Dataprices;
    }

    /**
     * Retourne le nombre d'élémetns dans le panier
     * @param $session_key
     */
    private  function load_cart_nbr_item($session_key){
        $data_cart = parent::s_cart_session($session_key);
        if ($data_cart != null){
            echo $data_cart['nbr_items_cart'];
        }else{
            echo '0';
        }
    }

    /**
     * Retourne le prix du panier
     * @param $session_key
     */
    private  function load_cart_price_item($session_key){
        $idcart = parent::s_idcart_session($session_key);
        $amount_products = '';
        $data_cart = parent::s_cart_items($idcart['id_cart']);
        if ($data_cart != null){
            foreach($data_cart as $item){
                $price_item = $item['price_items'];
                $quantity_item = $item['quantity_items'];
                $price = $price_item*$quantity_item;
                $amount_products += $price;
            }
            echo number_format($amount_products, 2, '.', '');
        }else {
            echo '0.00';
        }

    }

    /**
     * Validation des champs avant l'envoi et met a jour les données du client
     * @param $id_cart
     * @param $create
     */
    private function validate_cart($id_cart,$create){
        if ($id_cart != 'null'){
            //Controle des champs obligatoire, arrêt du script si null ou = à ''
            $data_validate = array(
                'lastname_cart','firstname_cart','email_cart','street_cart','city_cart','postal_cart','country_cart'
            );
            foreach($data_validate as $input){
                if (!($_POST[$input]) OR $_POST[$input] == null OR $_POST[$input] == ''){
                    $this->getNotify('empty',false);
                    return;
                }
            }

            //Nettoyage des variable $_POST et injection dans colonne du panier
            $id_cart    =   magixcjquery_form_helpersforms::inputNumeric($id_cart);
            $lastname  =   magixcjquery_form_helpersforms::inputClean($_POST['lastname_cart']);
            $firstname   =   magixcjquery_form_helpersforms::inputClean($_POST['firstname_cart']);
            $email      =   magixcjquery_form_helpersforms::inputClean($_POST['email_cart']);
            $phone      =   magixcjquery_form_helpersforms::inputClean($_POST['phone_cart']);
            $street     =   magixcjquery_form_helpersforms::inputClean($_POST['street_cart']);
            $city       =   magixcjquery_form_helpersforms::inputClean($_POST['city_cart']);
            $postal     =   magixcjquery_form_helpersforms::inputClean($_POST['postal_cart']);
            $country    =   magixcjquery_form_helpersforms::inputClean($_POST['country_cart']);
            $tva        =   magixcjquery_form_helpersforms::inputClean($_POST['tva_cart']);
            $society    =   magixcjquery_form_helpersforms::inputClean($_POST['society_cart']);
            $message    =   $_POST['message_cart'] != null ? magixcjquery_form_helpersforms::inputClean($_POST['message_cart']) : '';

            //$adressliv  =   $_POST['adressliv'] != null ? magixcjquery_form_helpersforms::inputClean($_POST['adressliv']) : '';
            $lastname_liv  =   magixcjquery_form_helpersforms::inputClean($_POST['lastname_liv_cart']);
            $firstname_liv  =   magixcjquery_form_helpersforms::inputClean($_POST['firstname_liv_cart']);
            $street_liv  =   magixcjquery_form_helpersforms::inputClean($_POST['street_liv_cart']);
            $city_liv  =   magixcjquery_form_helpersforms::inputClean($_POST['city_liv_cart']);
            $postal_liv  =   magixcjquery_form_helpersforms::inputClean($_POST['postal_liv_cart']);
            $country_liv  =   magixcjquery_form_helpersforms::inputClean($_POST['country_liv_cart']);
            if(magixcjquery_filter_request::isSession('idprofil')){
                $idprofil = magixcjquery_form_helpersforms::inputNumeric($_SESSION['idprofil']);
            }else{
                $idprofil = null;
            }

            // Enregistrer les données du formulaire en DB
            parent::u_cart_customer_infos($id_cart,$idprofil,$firstname,$lastname,$email,$phone,$street,$city,$tva,$postal,$country,$message,$street_liv,$city_liv,$postal_liv,$country_liv,$lastname_liv
,$firstname_liv);
        }

    }

    /**
     * Titre ou sujet du mail de commande
     * @param $create
     * @return string
     */
    private function setTitleMail($create){
        $about = new plugins_about_public();
        $collection = $about->getData();
        $subject = $create->getConfigVars('subject_mail');
        $website = $collection['name'];
        return sprintf($subject,$website);
    }

    /**
     * Tableau complet de la commande pour le formatage du mail
     * @param $row
     * @return array
     */
    public function setItemOrderData ($row)
    {

        $data = null;
        $newData = array();
        // formate la TVA avant le calcule
        //$tva_amount = floatval('1.'.sprintf("%.02d", $row['amount_tva']));
        $tva_amount = 1 + (floatval($row['amount_tva']) / 100);
        $newData['tax_amount'] = round($row['amount_order'] - ($row['amount_order']/ $tva_amount),2);
        $shipping_tax = round($row['shipping_price_order'] - ($row['shipping_price_order']/ $tva_amount),2);

        $newData['id_cart'] = $row['id_cart'];
        $newData['id_order'] = $row['id_order'];
        $newData['shipping_price_order'] = $row['shipping_price_order'];
        $newData['shipping_htva'] = number_format(($row['shipping_price_order']/ $tva_amount), 2, '.', '');
        $newData['amount_order'] = $row['amount_order'];
        $newData['payment_order'] = $row['payment_order'];
        $newData['date_order'] = $row['date_order'];
        $newData['nbr_items_cart'] = $row['nbr_items_cart'];
        $newData['session_key_cart'] = $row['session_key_cart'];
        $newData['lastname_cart'] = $row['lastname_cart'];
        $newData['firstname_cart'] = $row['firstname_cart'];
        $newData['email_cart'] = $row['email_cart'];
        $newData['phone_cart'] = $row['phone_cart'];
        $newData['street_cart'] = $row['street_cart'];
        $newData['city_cart'] = $row['city_cart'];
        $newData['postal_cart'] = $row['postal_cart'];
        $newData['country_cart'] = $row['country_cart'];
        $newData['tva_cart'] = $row['tva_cart'];
        $newData['message_cart'] = $row['message_cart'];
        $newData['transmission_cart'] = $row['transmission_cart'];
        $newData['lastname_liv_cart'] = $row['lastname_liv_cart'];
        $newData['firstname_liv_cart'] = $row['firstname_liv_cart'];
        $newData['street_liv_cart'] = $row['street_liv_cart'];
        $newData['city_liv_cart'] = $row['city_liv_cart'];
        $newData['postal_liv_cart'] = $row['postal_liv_cart'];
        $newData['country_liv_cart'] = $row['country_liv_cart'];
        $newData['amount_tva'] = $row['amount_tva'];
        $newData['amount_tax'] = number_format(($newData['tax_amount'] + $shipping_tax), 2, '.', '');
        $newData['tax_amount'] = number_format($newData['tax_amount'], 2, '.', '');

        $catalog = array();
        $catalog = array_map(
            null,
            explode('|', $row['CATALOG_LIST_ID']),
            explode('|', $row['CATALOG_LIST_NAME']),
            explode('|', $row['CATALOG_LIST_QUANTITY']),
            explode('|', $row['CATALOG_LIST_PRICE']),
            explode('|', $row['CATALOG_LIST_ATTR'])
        );
        foreach($catalog as $key => $value){
            $newData['catalog'][$key]['CATALOG_LIST_ID'] = $value[0];
            $newData['catalog'][$key]['CATALOG_LIST_NAME'] = $value[1];
            $newData['catalog'][$key]['CATALOG_LIST_QUANTITY'] = $value[2];
            $newData['catalog'][$key]['CATALOG_LIST_PRICE'] = $value[3];
            if($value[4] != null && key_exists('attribute',$this->activeMods)) {
                $attr = $this->activeMods['attribute']->g_attr($value[4]);
                $newData['catalog'][$key]['CATALOG_LIST_ATTR'] = $attr['title_attribute'];
            }
        }
        return $newData;
    }

    /**
     * Setting du mail de commande
     * @param $row
     * @param $create
     * @return mixed
     */
    public function setCartData($row,$create,$testmail=false){
       // parse_str($_POST['COMPLUS']);
        $create->assign('getCartData',$this->setItemOrderData($row));
        if($testmail){
            $create->display('mail/admin.tpl');
        }else{
            return $create->fetch('mail/admin.tpl');
        }

    }

    public function getProcessOrder($create){
        $getConfigData = $this->getConfigData();
        if($getConfigData['online_payment'] === '1'){
            if($getConfigData['ogone'] === '1'){
                if(class_exists('plugins_ogone_public')) {
                    if (isset($_POST['COMPLUS'])) {
                        parse_str($_POST['COMPLUS']);
                        $currency_order = 'EUR';
                        $transid = magixglobal_model_cryptrsa::uuid_generator();
                        $id_cart = $_POST['orderID'];
                        $shipping_amount = $shipping;
                        $amount = $_POST['amount'];
                        parent::i_cart_order($id_cart,$transid,$amount,$shipping_amount,$currency_order,'ogone');
                        parent::u_transmission_cart($id_cart,1);
                        $this->sendOrder($id_cart, $create);
                    }
                }else{
                    parent::u_transmission_cart($this->id_cart_to_send,1);
                }
            }
            if($getConfigData['hipay'] === '1'){
                if(class_exists('plugins_hipay_public')){
                    if (isset($_POST['xml'])) {
                        $hipay = new plugins_hipay_public();
                        $data = $hipay->getProcess();
                        if($data){
                            $operation = $data['operation'];
                            $merchantdatas = $data['merchantdatas'];
                            $id_cart = $data['order'];
                            $shipping_amount = $data['shipping'];
                            $transid = $data['transaction'];
                            $amount = $data['amount'];
                            $currency_order = $data['currency'];
                            $idformerchant = $data['idformerchant'];
                            $date = $data['date'];
                            $time = $data['time'];
                            $emailClient = $data['email'];
                            $status = $data['status'];
                            if($operation == 'authorization'){
                                parent::i_cart_order($id_cart,$transid,$amount,$shipping_amount,$currency_order,'hipay');
                                parent::u_transmission_cart($id_cart,1);
                            }elseif($operation == 'capture'){
                                $this->sendOrder($id_cart, $create);
                            }elseif($operation == 'reject'){
                                $core_mail = new magixglobal_model_mail('mail');
                                //@todo Rendre global la configuration
                                $message = $core_mail->body_mail(
                                    'Erreur de payement',
                                    array($getConfigData['mail_order']),
                                    array($getConfigData['mail_order_from']),
                                    "Test\n
                                    operation=$operation\n
                                    status=$status\n
                                    date=$date\n
                                    time=$time\n
                                    id_cart=$id_cart\n
                                    shipping_amount=$shipping_amount\n
                                    transaction_id=$transid\n
                                    amount=$amount\n
                                    currency=$currency_order\n
                                    idformerchant=$idformerchant\n
                                    merchantdatas=" . $merchantdatas . "\n
                                    emailClient=$emailClient\n",
                                    false
                                );
                                $core_mail->batch_send_mail($message);
                            }
                        }
                    }
                }else{
                    parent::u_transmission_cart($this->id_cart_to_send,1);
                }
            }

        } else{
            parent::u_transmission_cart($this->id_cart_to_send,1);
        }
    }
    /**
     * Envoi du formulaire de commande sur base de la méthode (payment/devis)
     * @param $id_cart
     * @param $create
     * @param bool $testmail
     */
    public function sendOrder($id_cart,$create,$testmail=false){
        //if($this->getProcessOrder($params)){
            $data = parent::s_complete_data($id_cart);
            if($data != null){
                $email_customer = $data['email_cart'];
                if($testmail){
                    $itemData = $this->setCartData($data,$create,true);
                }else{
                    $itemData = $this->setCartData($data,$create,false);
                    //récupération des e-mail pour envois
                    $core_mail = new magixglobal_model_mail('mail');
                    //@todo Rendre global la configuration
                    $getConfigData = $this->getConfigData();
                    $lotsOfRecipients = array($getConfigData['mail_order']);
                    foreach ($lotsOfRecipients as $recipient){
                        $message = $core_mail->body_mail(
                            self::setTitleMail($create),
                            array($email_customer),
                            array($recipient),
                            $itemData,
                            false
                        );
                        $core_mail->batch_send_mail($message);
                    }
                    $msgClient = $core_mail->body_mail(
                        self::setTitleMail($create),
                        array($getConfigData['mail_order_from']),
                        array($email_customer),
                        $itemData,
                        false
                    );
                    $core_mail->batch_send_mail($msgClient);
                }
            }
        //}
    }
    /*
     *
     * Réservation d'un produit
     * @todo Cette partie ne sera disponible qu'avec le module profil ?
     *
     * */
    /**
     * Tableau complet de la commande pour le formatage du mail
     * @param $row
     * @return array
     */
    private function setItemBookingData ($row)
    {

        $data = null;
        $newData = array();
        $newData['idbooking'] = $row['idbooking'];
        $newData['lastname'] = $row['lastname_pr'];
        $newData['firstname'] = $row['firstname_pr'];
        $newData['email'] = $row['email_pr'];
        $newData['product'] = $row['titlecatalog'];
        $newData['quantity'] = $row['quantity_bk'];
        $newData['date'] = $row['date_bk'];

        return $newData;
    }

    /**
     * @throws Exception
     */
    private function addBooking(){
        parent::i_booking($this->idcatalog,$this->idprofil_session,$this->booking_quantity);
    }
    /**
     * titre du booking
     */
    private function setBookingMail(){
        $subject = $this->template->getConfigVars('booking_title_mail');
        $website = self::WEBSITENAME;
        return sprintf($subject,$website);
    }

    /**
     * @param $row
     * @return string
     * @throws Exception
     */
    private function setBookingData($row){
        $this->template->assign('getBookingData',$this->setItemBookingData($row));
        if(isset($_GET['testbooking'])){
            $this->template->display('mail/booking.tpl');
        }else{
            return $this->template->fetch('mail/booking.tpl');
        }
    }

    /**
     * @param bool $testmail
     */
    private function getBookingData($testmail = false){
        if($testmail) {
            $data = parent::s_booking_info(1,1);
        }else{
            $this->addBooking();
            $data = parent::s_booking_info($this->idprofil_session,magixglobal_model_db::layerDB()->lastInsert());
        }
        if($data != null){
            if($testmail){
                $itemData = $this->setBookingData($data);
            }else{
                $itemData = $this->setBookingData($data);
                $email_customer = $data['email_pr'];
                //récupération des e-mail pour envois
                $core_mail = new magixglobal_model_mail('mail');
                $getConfigData = $this->getConfigData();
                $lotsOfRecipients = array($getConfigData['mail_order']);
                foreach ($lotsOfRecipients as $recipient){
                    $message = $core_mail->body_mail(
                        self::setBookingMail(),
                        array($email_customer),
                        array($recipient),
                        $itemData,
                        false
                    );
                    $core_mail->batch_send_mail($message);
                }
                $msgClient = $core_mail->body_mail(
                    self::setBookingMail(),
                    array($getConfigData['mail_order_from']),
                    array($email_customer),
                    $itemData,
                    false
                );
                $core_mail->batch_send_mail($msgClient);
            }
        }
    }
    /**
     * Suppression d'un élément du panier
     * @access private
     * @param $id_item
     * @param $create
     */
    public function delete_item_cart($id_item,$create){
        if(isset($id_item) != null){
            //récupération des donnée de l'item
            $data_item = parent::s_cart_item_one($id_item);
            //assignation de l'identifiant panier
            $id_cart = $data_item['id_cart'];
            //suppression item

            parent::d_item_cart($id_item);
            //calcul et Mise à jour du nombre d'éléments dans le panier
            $count_items = parent::count_cart_items($id_cart);
            $count_items =($count_items != null)? $count_items : 0;
            parent::u_cart_items($id_cart,$count_items['total']);
        }
    }

    /**
     * Mise a jour de la quantité
     */
    public function update_quantity_item(){
        if(isset($this->quantity_qty)){
            parent::u_cart_item_qty($this->item_qty,$this->quantity_qty);
        }
    }

    /**
     * Mise a jour de la quantité
     */
    public function update_attr_item(){
        if(isset($this->attr)){
            parent::u_cart_item_attr($this->item_attr,$this->attr);
        }
    }
    /**
     * @param $row
     * @return array
     */
    public function setItemsOrderData ($row)
    {

        $data = null;
        $newData = array();
        $catalog = array();
        foreach($row as $key => $value){
            // formate la TVA avant le calcule
            $tva_amount = 1 + (floatval($value['amount_tva']) / 100);
            $newData[$key]['tax_amount'] = round($value['amount_order'] - ($value['amount_order']/ $tva_amount),2);
            $shipping_tax = round($value['shipping_price_order'] - ($value['shipping_price_order']/ $tva_amount),2);

            $newData[$key]['id_cart'] = $value['id_cart'];
            $newData[$key]['id_order'] = $value['id_order'];
            $newData[$key]['shipping_price_order'] = $value['shipping_price_order'];
            $newData[$key]['shipping_htva'] = number_format(($value['shipping_price_order']/ $tva_amount), 2, '.', '');
            $newData[$key]['amount_order'] = $value['amount_order'];
            $newData[$key]['payment_order'] = $value['payment_order'];
            $newData[$key]['date_order'] = $value['date_order'];
            $newData[$key]['nbr_items_cart'] = $value['nbr_items_cart'];
            $newData[$key]['session_key_cart'] = $value['session_key_cart'];
            $newData[$key]['ref_cart'] = $value['ref_cart'];
            $newData[$key]['lastname_cart'] = $value['lastname_cart'];
            $newData[$key]['firstname_cart'] = $value['firstname_cart'];
            $newData[$key]['email_cart'] = $value['email_cart'];
            $newData[$key]['phone_cart'] = $value['phone_cart'];
            $newData[$key]['street_cart'] = $value['street_cart'];
            $newData[$key]['city_cart'] = $value['city_cart'];
            $newData[$key]['postal_cart'] = $value['postal_cart'];
            $newData[$key]['country_cart'] = $value['country_cart'];
            $newData[$key]['tva_cart'] = $value['tva_cart'];
            $newData[$key]['message_cart'] = $value['message_cart'];
            $newData[$key]['transmission_cart'] = $value['transmission_cart'];
            $newData[$key]['lastname_liv_cart'] = $value['lastname_liv_cart'];
            $newData[$key]['firstname_liv_cart'] = $value['firstname_liv_cart'];
            $newData[$key]['street_liv_cart'] = $value['street_liv_cart'];
            $newData[$key]['city_liv_cart'] = $value['city_liv_cart'];
            $newData[$key]['postal_liv_cart'] = $value['postal_liv_cart'];
            $newData[$key]['amount_tva'] = $value['amount_tva'];
            $newData[$key]['amount_tax'] = number_format(($newData[$key]['tax_amount'] + $shipping_tax), 2, '.', '');
            $newData[$key]['tax_amount'] = number_format($newData[$key]['tax_amount'], 2, '.', '');

            $catalog[$key]['catalog'] = array_map(
                null,
                explode('|', $value['CATALOG_LIST_ID']),
                explode('|', $value['CATALOG_LIST_NAME']),
                explode('|', $value['CATALOG_LIST_QUANTITY']),
                explode('|', $value['CATALOG_LIST_PRICE']),
                explode('|', $value['CATALOG_LIST_ATTR'])
            );
            foreach($catalog[$key]['catalog'] as $key1 => $value1){
                $newData[$key]['catalog'][$key1]['CATALOG_LIST_ID'] = $value1[0];
                $newData[$key]['catalog'][$key1]['CATALOG_LIST_NAME'] = $value1[1];
                $newData[$key]['catalog'][$key1]['CATALOG_LIST_QUANTITY'] = $value1[2];
                $newData[$key]['catalog'][$key1]['CATALOG_LIST_PRICE'] = $value1[3];
                if($value1[4] != null && key_exists('attribute',$this->activeMods)) {
                    $attr = $this->activeMods['attribute']->g_attr($value1[4]);
                    $newData['catalog'][$key1]['CATALOG_LIST_ATTR'] = $attr['title_attribute'];
                }
            }
        }
        return $newData;
    }
    /**
     * @param $idprofil
     * @return array
     */
    public function setProfilOrder($idprofil){
        $data = parent::s_profil_data($idprofil);
        if($data != null){
            return $this->setItemsOrderData($data);
        }
    }
    /**
     *
     * Execute le plugin dans la partie public
     */
    public function run(){
        if(magixcjquery_filter_request::isSession('key_cart')){
            $session_key = $_SESSION['key_cart'];
        }else {
            $session_key = null;
        }

		if(isset($this->module)) {
			$this->activeMods = $this->module->load_module(false);
		}

        //Chargement des données de traduction
        $this->_loadConfigVars();
        $create = frontend_controller_plugins::create();
        $header= new magixglobal_model_header();
        if (isset( $this->mod) && isset($this->action)) {
            $cartMod = $this->activeMods[$this->mod];
            $params = array('params' => $_GET['params'], 'controller' => $this->template);
            call_user_func(array($cartMod,$this->action),$params);
        }elseif (isset( $this->add_cart)){
            $this->add_item_cart($_POST,$session_key);
        }elseif(isset($this->delete_item)){
            $this->delete_item_cart($this->item_to_delete,$create);
        }elseif(isset($this->json_cart)){
            $this->template->assign('getDataConfig',$this->getConfigData());
            $header->head_expires("Mon, 26 Jul 1997 05:00:00 GMT");
            $header->head_last_modified(gmdate( "D, d M Y H:i:s" ) . "GMT");
            $header->pragma();
            $header->cache_control("nocache");
            $header->getStatus('200');
            $header->html_header("UTF-8");
            //$this->load_cart_ajax($this->json_cart);
			$cartData = $this->getItemCartData($this->json_cart);
			$this->template->assign('getItemCartData',$cartData);

			if(!empty($cartData)) {
				$this->template->assign('getItemPriceData',$this->getItemPriceData($this->json_cart));
				$this->template->assign('setParamsData',array('remove'=>'true','editQuantity'=>'true'));
				$this->template->display('loop/cart.tpl');
			} else {
				return false;
			}
        }elseif(isset($this->get_nbr_items)){
            $this->load_cart_nbr_item($session_key);
        }elseif(isset($this->get_price_items)){
            $this->load_cart_price_item($session_key);
        }elseif(isset($this->get_amount_to_pay)){
            $prices = $this->load_cart_amount($this->get_amount_to_pay);
            $header->head_expires("Mon, 26 Jul 1997 05:00:00 GMT");
            $header->head_last_modified(gmdate( "D, d M Y H:i:s" ) . "GMT");
            $header->pragma();
            $header->cache_control("nocache");
            $header->getStatus('200');
            $header->html_header("UTF-8");
            print($prices['amount_to_pay']);
        }elseif(isset($this->pstring1)){
            $this->template->assign('getDataConfig',$this->getConfigData());
            if($this->pstring2 == 'process'){
                $this->getProcessOrder($create);
            }elseif(isset($this->pstring2)){
                if($this->pstring2 == 'success'){
                    unset($_SESSION['key_cart']);
                    $this->getNotify('success',false);
                }elseif($this->pstring2 == 'refused'){
                    unset($_SESSION['key_cart']);
                    $this->getNotify('refused',false);
                }elseif($this->pstring2 == 'cancel'){
                    unset($_SESSION['key_cart']);
                    $this->getNotify('cancel',false);
                }elseif($this->pstring2 == 'exception'){
                    unset($_SESSION['key_cart']);
                    $this->getNotify('exception',false);
                }
                $create->display('payment_statut.tpl');
            }else{
                if(isset($this->id_cart_to_send)){
                    $this->validate_cart($this->id_cart_to_send,$create);
                    $this->load_cart_data($session_key,$create);
                    $create->assign('getItemCartData',$this->getItemCartData($this->id_cart_to_send));
                    $create->assign('getItemPriceData',$this->getItemPriceData($this->id_cart_to_send));
                    $create->assign('setParamsData',array('remove'=>'false','editQuantity'=>'false'));
                    $create->display('payment_resume.tpl');
                }else {
                    $this->load_cart_data($session_key, $create);
                    $create->assign('getItemCartData', $this->getItemCartData($this->id_cart_to_send));
                    $create->assign('getItemPriceData', $this->getItemPriceData($this->id_cart_to_send));
                    $create->assign('setParamsData', array('remove' => 'false', 'editQuantity' => 'false'));
                    $create->display('payment_resume.tpl');
                }
            }
        }/*elseif(isset($this->booking)) {
            if(isset($_GET['testbooking'])){
                $this->getBookingData(true);
            }else{
                $this->getBookingData(false);
            }
        }*/else{
            if (magixcjquery_filter_request::isSession('key_cart')){
                if(isset($this->devis_to_send)){
                    //$this->validate_cart($this->id_cart_to_send,$create);
                    $dataCart = $this->getItemPriceData($this->id_cart_to_send);
                    parent::i_cart_order(
                        $this->id_cart_to_send,
                        magixglobal_model_cryptrsa::uuid_generator(),
                        $dataCart['amount_products'],
                        $dataCart['shipping_ttc'],
                        'EUR',
                        'bank_wire'
                    );
                    $this->sendOrder($this->id_cart_to_send,$create,false);
                    parent::u_transmission_cart($this->id_cart_to_send,1);
                    $this->getNotify('success',true);
                    //Supprime la session du panier après envoi du mail si le système de devis est activé
                    unset($_SESSION['key_cart']);
                    //return;
                }elseif(isset($this->quantity_qty)){
                    $this->update_quantity_item();
                }elseif(isset($this->attr)){
                    $this->update_attr_item();
                }elseif(isset($_GET['testmail'])){
                    $cart = 1;
                    if(!empty($_GET['testmail'])) {
                        $testmail = intval($_GET['testmail']);
                        $cart = is_int($testmail) ? $testmail : 1;
                    }
                    $this->sendOrder($cart,$create,true);
                }else{
                    $this->modelSystem = new magixglobal_model_system();
                    frontend_model_template::addConfigFile(
                        array(
                            $this->modelSystem->base_path().'plugins/cartpay/i18n/tools'
                        ),
                        array(
                            'country_',
                        )
                        ,false
                    );
                    $data_cart = parent::s_cart_session($session_key);
                    $id_cart = $data_cart['id_cart'];
                    $this->load_cart_data($session_key,$create);
                    $create->assign('getItemCartData',$this->getItemCartData($id_cart));
                    $create->assign('getItemPriceData',$this->getItemPriceData($id_cart));
                    $create->assign('setParamsData',array('remove'=>'true','editQuantity'=>'true'));
                    if(class_exists('plugins_ogone_public')
                        OR class_exists('plugins_hipay_public')){
                        $create->assign('setPaymentType','secure');
                    }else{
                        $create->assign('setPaymentType','devis');
                    }

                    $getDataConfig = $this->getConfigData();
					$this->template->assign('getDataConfig',$getDataConfig);
					$this->template->assign('getItemsCountryData',$this->getItemsTvaData(
						array(
							'fetch'=>'all',
							'context'=>'country'
						)
					));

					$moduleJS = array();
					$dynamicForm = false;

					if(!empty($this->activeMods)) {
						foreach ($this->activeMods as $name => $mod) {
							if(property_exists($mod,'js_impact')) {
								if($mod->js_impact) $moduleJS[] = $name;
							}
							if(($name == 'profil' && $getDataConfig['profil']) || $name != 'profil') {
								if(property_exists($mod,'dynamicForm')) {
									if($mod->dynamicForm) {
										$confdir = magixglobal_model_system::base_path().'plugins/'.$name.'/i18n/';
										$lang = frontend_model_template::getLanguage();
										if(file_exists($confdir)) {
											$translate = !empty($lang) ? $lang : 'fr';
											frontend_model_smarty::getInstance()->configLoad($confdir . 'public_local_' . $translate . '.conf', null);
											$dynamicForm = $this->template->fetch('forms/order.tpl', $name);
										}
									}
								}
							}
						}
					}

					$this->template->assign('moduleJS',$moduleJS);
					$this->template->assign('dynamicForm',$dynamicForm);
                    $create->display('index.tpl');
                }
            }
        }
    }
}
?>