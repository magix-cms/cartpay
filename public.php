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
class plugins_cartpay_public extends database_plugins_cartpay{
    /**
     * @var frontend_controller_plugins
     */
    protected $template,$modelSystem;
    public $pstring1,$pstring2;
    /**
     * @var string
     */
    public $add_cart,$delete_item,$get_nbr_items,$get_price_items,$get_amount_to_pay,$idprofil_session,
        $shipping_price,$json_cart,$item_to_delete,$id_cart_to_send,$devis_to_send,$booking,$payment,$tva_amount,$idcatalog,$booking_quantity,$tva_country;
    public $item_qty,$quantity_qty;
    public $logo_perso,$logo;
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
            $this->cart_to_send = magixcjquery_form_helpersforms::inputClean($_POST['id_cart_to_send']);
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
        if(magixcjquery_filter_request::isPost('item_qty')){
            $this->item_qty = magixcjquery_form_helpersforms::inputNumeric($_POST['item_qty']);
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
            $dataTva
        );
        return $this->setItemsTvaData($data);
    }

    private function getItemTvaData($dataTva){
        $data = parent::fetchTva(
            $dataTva
        );
        return $data;
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
            if($v_item['idcatalog']!= null){
                //Si l'item est déjà dans le panier, on modifie la quantité
                parent::u_cart_item_qty($v_item['id_item'],$v_item['quantity_items']+$quantity);
            }else{
                //insertion du nouvel item
                parent::i_cart_items($id_cart,$idcatalog,$quantity,$current_price);
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

            $shipping =  $cart_amount['shipping'];
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
            $tax_amount = $cart_amount['ammount_products'] - ($cart_amount['ammount_products']/ $tva_amount);


            //$amount_pay_with_tax = ($amount_to_pay-$shipping);//$tax_amount+$cart_amount['ammount_products'];
            $amount_pay_with_tax = $cart_amount['ammount_products']+$shipping;
            //Assignation des coordonnée
            if($this->pstring1 === 'payment'){

                //$this->hipayProcess($create->getLanguage(),$session_key,$id_cart,$shipping,$amount_pay_with_tax);
                if(class_exists('plugins_ogone_public')) {
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
                            'order'     =>  $data_cart['id_cart'],
                            'amount'    =>  $amount_pay_with_tax
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
     * Retourne le prix total à payer (prix des produits + taxes 21% + shipping)
     * Sous forme de table ('ammount_products', 'amount_tax', 'shipping', 'amount_to_pay')
     * Avec vérification dans le cas ou le prix encodé serait différent du prix du catalog
     * Update le prix si nécessaire
     * @access private
     * @param int id_cart
     * @return array
     */
    public function load_cart_amount($id_cart){
        $amount_to_pay = null;
        $amount_products = '0.00';
        $quantity_total = '0';
        $data_cart = parent::s_cart_items($id_cart);
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
        }
        /*$collectionShipping =  new plugins_shipping_public();
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
        $shipping = 0;
        $shipping_ttc = $shipping;
        if(class_exists('plugins_cashback_public')){
            $collectionCashback = new plugins_cashback_public();
            if(isset($_POST['v_code'])){
                $promo_amount =$collectionCashback->selectPromo($_POST['v_code']);
            }else{
                $promo_amount = 0;
            }
            /*if(isset($_POST['profil_cashback'])){
                $setTotalProfil = $collectionCashback->setTotalProfil();
                $profil_amount = $setTotalProfil['sumount'];
            }else{
                $profil_amount = 0;
            }*/
        }else{
            $promo_amount = 0;
            $profil_amount = 0;
        }
        $comp_data = parent::s_customer_info($id_cart);
        //print_r($comp_data);
        if(isset($this->tva_country)){
            $tva = $this->getItemTvaData(
                array(
                    'fetch'=>'one',
                    'context'=>'config',
                    'country'=>$this->tva_country
                )
            );
            $calculate_tva = $tva['amount_tva'];
        }elseif($comp_data['country_cart'] != null){
            $tva = $this->getItemTvaData(
                array(
                    'fetch'=>'one',
                    'context'=>'config',
                    'country'=>$comp_data['country_cart']
                )
            );
            $calculate_tva = $tva['amount_tva'];
        }else{
            $calculate_tva = $this->tva_amount ;
        }


        // formate la TVA avant le calcule
        $tva_amount = floatval('1.'.sprintf("%.02d", $calculate_tva));
        //$shipping = ($amount_products < $this->free_shipping_amount) ? $this->shipping_price : '0.00';
        $tax_amount = ($amount_products - ($amount_products / $tva_amount));
        $tva = number_format($tax_amount, 2, '.', '');
        $total = ($amount_products) + $shipping_ttc;
        if($total <= $promo_amount || $total <=$profil_amount){
            $amount_to_pay = $total;
        }elseif($total <= ($promo_amount+$profil_amount)){
            $amount_to_pay = $total;
        }else{
            //$amount_to_pay =  $total - $promo_amount - $profil_amount;
            $amount_to_pay =  $total - $promo_amount;
        }
        $amount_hvat = ($total-$tva);
        /**
         * retourne un tableau de données formatée
         */
        $prices = array (
            'amount_products'  => number_format($amount_products, 2, '.', ''),
            'shipping'          => $shipping_ttc,
            'amount_vat'        => $calculate_tva,
            'amount_tax'        => $tva,
            'amount_promo'      => number_format($promo_amount, 2, '.', ''),
            //'amount_profil'     => $profil_amount,
            'amount_hvat'       => number_format($amount_hvat, 2, '.', ''),
            'amount_to_pay'     => number_format($amount_to_pay, 2, '.', ''),
            'quantity_total'    => $quantity_total
        );
        return $prices;
    }

    /**
     * @param $row
     * @return array
     */
    private function setItemCartData($row){
        $data = null;
        $newData = array();
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
            $newData[$key]['urlproduct']      = $urlProduct;
            $newData[$key]['quantity']        = $value['quantity_items'];
            $newData[$key]['price']           = $value['price_items'];
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
        $dataItem = parent::s_cart_items($dataCart['id_cart']);

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
            parent::u_cart_customer_infos($id_cart,$idprofil,$firstname,$lastname,$email,$phone,$street,$city,$tva,$postal,$country,$message,$street_liv,$city_liv,$postal_liv,$country_liv);
        }

    }

    /**
     * Titre ou sujet du mail de commande
     * @param $create
     * @return string
     */
    private function setTitleMail($create){
        $subject = $create->getConfigVars('subject_mail');
        $website = $create->getConfigVars('website');
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
        $tva_amount = floatval('1.'.sprintf("%.02d", $row['amount_tva']));
        $tax_amount = $row['amount_order'] - ($row['amount_order']/ $tva_amount);

        $newData['id_cart'] = $row['id_cart'];
        $newData['id_order'] = $row['id_order'];
        $newData['shipping_price_order'] = $row['shipping_price_order'];
        $newData['amount_order'] = $row['amount_order'];
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
        $newData['street_liv_cart'] = $row['street_liv_cart'];
        $newData['city_liv_cart'] = $row['city_liv_cart'];
        $newData['postal_liv_cart'] = $row['postal_liv_cart'];
        $newData['country_liv_cart'] = $row['country_liv_cart'];
        $newData['amount_tva'] = $row['amount_tva'];
        $newData['amount_tax'] = number_format($tax_amount, 2, '.', '');
        $catalog = array();
        $catalog = array_map(
            null,
            explode('|', $row['CATALOG_LIST_ID']),
            explode('|', $row['CATALOG_LIST_NAME']),
            explode('|', $row['CATALOG_LIST_QUANTITY']),
            explode('|', $row['CATALOG_LIST_PRICE'])
        );
        foreach($catalog as $key => $value){
            $newData['catalog'][$key]['CATALOG_LIST_ID'] = $value[0];
            $newData['catalog'][$key]['CATALOG_LIST_NAME'] = $value[1];
            $newData['catalog'][$key]['CATALOG_LIST_QUANTITY'] = $value[2];
            $newData['catalog'][$key]['CATALOG_LIST_PRICE'] = $value[3];
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
            if($getConfigData['hipay'] === '1'){
                if(class_exists('plugins_ogone_public')) {
                    if (isset($_POST['COMPLUS'])) {
                        parse_str($_POST['COMPLUS']);
                        $currency_order = 'EUR';
                        $transid = magixglobal_model_cryptrsa::uuid_generator();
                        $id_cart = $_POST['orderID'];
                        $shipping_amount = $shipping;
                        $amount = $_POST['amount'];
                        parent::i_cart_order($id_cart,$transid,$amount,$shipping_amount,$currency_order);
                        parent::u_transmission_cart($id_cart,1);
                        $this->sendOrder($id_cart, $create);
                    }
                }else{
                    parent::u_transmission_cart($this->cart_to_send,1);
                }
            }
            if($getConfigData['ogone'] === '1'){
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
                                parent::i_cart_order($id_cart,$transid,$amount,0,$currency_order);
                                parent::u_transmission_cart($id_cart,1);
                            }elseif($operation == 'capture'){
                                $this->sendOrder($id_cart, $create);
                            }elseif($operation == 'rejet'){
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
                    parent::u_transmission_cart($this->cart_to_send,1);
                }
            }

        } else{
            parent::u_transmission_cart($this->cart_to_send,1);
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
                $lotsOfRecipients = array('contact@web-solution-way.be');
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
                    array('contact@web-solution-way.be'),
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
            $tva_amount = floatval('1.'.sprintf("%.02d", $value['amount_tva']));
            $tax_amount = $value['amount_order'] - ($value['amount_order']/ $tva_amount);

            $newData[$key]['id_cart'] = $value['id_cart'];
            $newData[$key]['id_order'] = $value['id_order'];
            $newData[$key]['shipping_price_order'] = $value['shipping_price_order'];
            $newData[$key]['amount_order'] = $value['amount_order'];
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
            $newData[$key]['street_liv_cart'] = $value['street_liv_cart'];
            $newData[$key]['city_liv_cart'] = $value['city_liv_cart'];
            $newData[$key]['postal_liv_cart'] = $value['postal_liv_cart'];
            $newData[$key]['amount_tva'] = $value['amount_tva'];
            $newData[$key]['amount_tax'] = number_format($tax_amount, 2, '.', '');
            $catalog[$key]['catalog'] = array_map(
                null,
                explode('|', $value['CATALOG_LIST_ID']),
                explode('|', $value['CATALOG_LIST_NAME']),
                explode('|', $value['CATALOG_LIST_QUANTITY']),
                explode('|', $value['CATALOG_LIST_PRICE'])
            );
            foreach($catalog[$key]['catalog'] as $key1 => $value1){
                $newData[$key]['catalog'][$key]['CATALOG_LIST_ID'] = $value1[0];
                $newData[$key]['catalog'][$key1]['CATALOG_LIST_NAME'] = $value1[1];
                $newData[$key]['catalog'][$key1]['CATALOG_LIST_QUANTITY'] = $value1[2];
                $newData[$key]['catalog'][$key1]['CATALOG_LIST_PRICE'] = $value1[3];
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
        //Chargement des données de traduction
        $this->_loadConfigVars();
        $create = frontend_controller_plugins::create();
        $header= new magixglobal_model_header();
        if (isset( $this->add_cart)){
            $this->add_item_cart($_POST,$session_key);
        }elseif(isset($this->delete_item)){
            $this->delete_item_cart($this->item_to_delete,$create);
        }elseif(isset($this->json_cart)){
            $header->head_expires("Mon, 26 Jul 1997 05:00:00 GMT");
            $header->head_last_modified(gmdate( "D, d M Y H:i:s" ) . "GMT");
            $header->pragma();
            $header->cache_control("nocache");
            $header->getStatus('200');
            $header->html_header("UTF-8");
            //$this->load_cart_ajax($this->json_cart);
            $create->assign('getItemCartData',$this->getItemCartData($this->json_cart));
            $create->assign('getItemPriceData',$this->getItemPriceData($this->json_cart));
            $create->assign('setParamsData',array('remove'=>'true','editQuantity'=>'true'));
            $create->display('loop/cart.tpl');

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
                if(isset($this->cart_to_send)){
                    $this->validate_cart($this->cart_to_send,$create);
                    $this->load_cart_data($session_key,$create);
                    $create->assign('getItemCartData',$this->getItemCartData($this->cart_to_send));
                    $create->assign('getItemPriceData',$this->getItemPriceData($this->cart_to_send));
                    $create->assign('setParamsData',array('remove'=>'false','editQuantity'=>'false'));
                    $create->display('payment_resume.tpl');
                }else{
                    $this->load_cart_data($session_key,$create);
                    $create->assign('getItemCartData',$this->getItemCartData($this->cart_to_send));
                    $create->assign('getItemPriceData',$this->getItemPriceData($this->cart_to_send));
                    $create->assign('setParamsData',array('remove'=>'false','editQuantity'=>'false'));
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
                    $this->validate_cart($this->cart_to_send,$create);
                    /*$this->sendOrder($this->cart_to_send,$create);*/
                    //Supprime la session du panier après envoi du mail si le système de devis est activé
                    unset($_SESSION['key_cart']);
                    //return;
                }elseif(isset($this->quantity_qty)){
                    $this->update_quantity_item();
                }elseif(isset($_GET['testmail'])){
                    $this->sendOrder(9,$create,true);
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
                    if(class_exists("plugins_profil_public")){
                        if(frontend_model_smarty::getInstance()->templateExists('profil/forms/profil-forms.tpl')){
                            $member = new plugins_profil_public();
                            $getConfigData = $member->getConfigData();
                            $this->template->assign('getConfigData', $getConfigData, true);
                            $profilExist = $this->template->fetch('forms/profil-forms.tpl','profil');
                            $this->template->assign('profilExist',$profilExist);
                        }else{
                            trigger_error("Missing 'profil files'");
                            return;
                        }
                    }

                    $this->template->assign('getDataConfig',$this->getConfigData());
                    $this->template->assign('getItemsCountryData',$this->getItemsTvaData(
                        array(
                            'fetch'=>'all',
                            'context'=>'country'
                        )
                    ));
                    $create->display('index.tpl');
                }
            }
        }
    }
}
class database_plugins_cartpay{
    /**
     * Vérifie si les tables du plugin sont installé
     * @access protected
     * return integer
     */
    protected function c_show_table(){
        $table = 'mc_plugins_cartpay';
        return magixglobal_model_db::layerDB()->showTable($table);
    }
    /**
     * @access protected
     * Selectionne les contacts pour le formulaire
     */
    protected function s_contact($iso){
        $sql = 'SELECT c.*
        FROM mc_plugins_contact AS c
        JOIN mc_lang AS lang ON(c.idlang = lang.idlang)
		WHERE lang.iso = :iso';
        return magixglobal_model_db::layerDB()->select($sql,array(':iso'=>$iso));
    }

    /**
     * Récupére un panier en fonction de l'id
     * @access protected
     * @param integer id_cart
     * @return array
     */
    protected function s_cart($id_cart){
        $sql=' SELECT cart.*
        FROM mc_plugins_cartpay AS cart
        WHERE cart.id_cart = :id_cart AND cart.transmission_cart = 0';
        return magixglobal_model_db::layerDB()->selectOne($sql,array(
            ':id_cart' => $id_cart
        ));
    }

    /**
     * Récupére un panier déja transmis en fonction de l'id
     * @access protected
     * @param integer id_cart
     * @return array
     */
    protected function s_cart_transmitted($id_cart){
        $sql=' SELECT cart.*
        FROM mc_plugins_cartpay AS cart
        WHERE cart.id_cart = :id_cart AND cart.transmission_cart = 1';
        return magixglobal_model_db::layerDB()->selectOne($sql,array(
            ':id_cart' => $id_cart
        ));
    }

    /**
     * Récupére un panier en fonction de la session
     * @access protected
     * @param string session_key
     * @return array
     */
    protected function s_cart_session($session_key){
        $sql=' SELECT cart.*
        FROM mc_plugins_cartpay AS cart
        WHERE cart.session_key_cart = :session_key AND cart.transmission_cart = 0';
        return magixglobal_model_db::layerDB()->selectOne($sql,array(
            ':session_key' => $session_key
        ));
    }
    /**
     * Récupére l'id_cart
     * @access protected
     * @param integer id_cart
     * return array
     * */
    protected function s_idcart_session($session_key){
        $sql=' SELECT cart.id_cart
        FROM mc_plugins_cartpay AS cart
        WHERE cart.session_key_cart = :session_key AND cart.transmission_cart = 0';
        return magixglobal_model_db::layerDB()->selectOne($sql,array(
            ':session_key' => $session_key
        ));
    }
    /**
     * Récupére les données du client dans le panier (nom, prénom, téléphone,...)
     * @access protected
     * @param int id_cart
     * return array
     * */
    protected function s_customer_info($id_cart){
        $sql=' SELECT cart.id_cart, cart.firstname_cart, cart.lastname_cart, cart.email_cart, cart.phone_cart,
        cart.street_cart, cart.city_cart, cart.tva_cart, cart.postal_cart, cart.country_cart,cart.message_cart
        FROM mc_plugins_cartpay AS cart
        WHERE cart.id_cart = :id_cart';
        return magixglobal_model_db::layerDB()->selectOne($sql,array(
            ':id_cart' => $id_cart
        ));
    }

    /**
     * Récupére tous les élémetns du panier
     * @access protected
     * @param integer id_cart
     * return array
     * */
    protected function s_cart_items($id_cart){
        $sql='SELECT items.*, p.idproduct, catalog.urlcatalog, catalog.titlecatalog, catalog.idlang, p.idclc, p.idcls,
        catalog.price,c.pathclibelle, s.pathslibelle, lang.iso
		FROM mc_plugins_cartpay_items AS items
		LEFT JOIN mc_catalog_product AS p ON(p.idcatalog = items.idcatalog)
		LEFT JOIN mc_catalog AS catalog ON ( catalog.idcatalog = p.idcatalog )
		LEFT JOIN mc_catalog_c AS c ON ( c.idclc = p.idclc )
		LEFT JOIN mc_catalog_s AS s ON ( s.idcls = p.idcls )
		LEFT JOIN mc_lang AS lang ON ( catalog.idlang = lang.idlang )
        WHERE items.id_cart = :id_cart
        GROUP BY items.idcatalog';
        return magixglobal_model_db::layerDB()->select($sql,array(
            ':id_cart' => $id_cart
        ));
    }
    /**
     * Récupére tous les élémetns du panier
     * @access protected
     * @param integer id_cart
     * return array
     * */
    protected function s_cart_item_one($id_item){
        $sql='SELECT items.*
        FROM mc_plugins_cartpay_items AS items
        WHERE id_item = :id_item';
        return magixglobal_model_db::layerDB()->selectOne($sql,array(
            ':id_item' => $id_item
        ));
    }

    /**
     * @param $id_cart
     * @param $idcatalog
     * @return array
     */
    protected function s_cart_item_catalog($id_cart,$idcatalog){
        $sql='SELECT items.*
        FROM mc_plugins_cartpay_items AS items
        WHERE idcatalog = :idcatalog AND id_cart = :id_cart';
        return magixglobal_model_db::layerDB()->selectOne($sql,array(
            ':id_cart' => $id_cart,
            ':idcatalog' => $idcatalog
        ));
    }

    /**
     * Récupére les informations nécessaire pour affichage titre + liens produit
     * @access protected
     * @param integer idcatalog
     * return array
     * */
    protected function s_catalog_product($idcatalog){
        $sql = 'SELECT p.idproduct, catalog.urlcatalog, catalog.titlecatalog, catalog.idlang, p.idclc, p.idcls, catalog.price,c.pathclibelle, s.pathslibelle, lang.iso
		FROM mc_catalog_product AS p
		LEFT JOIN mc_catalog AS catalog ON ( catalog.idcatalog = p.idcatalog )
		LEFT JOIN mc_catalog_c AS c ON ( c.idclc = p.idclc )
		LEFT JOIN mc_catalog_s AS s ON ( s.idcls = p.idcls )
		LEFT JOIN mc_lang AS lang ON ( catalog.idlang = lang.idlang )
		WHERE catalog.idcatalog = :idcatalog';
        return magixglobal_model_db::layerDB()->selectOne($sql,array(
            ':idcatalog'=>$idcatalog
        ));
    }
    /**
     * Récupére les informations nécessaire pour affichage titre + liens produit
     * @access protected
     * @param integer idcatalog
     * return array
     * */
    protected function s_catalog_price($idcatalog){
        $sql = 'SELECT catalog.price
		FROM mc_catalog AS catalog
		WHERE catalog.idcatalog = :idcatalog';
        return magixglobal_model_db::layerDB()->selectOne($sql,array(
            ':idcatalog'=>$idcatalog
        ));
    }

    /**
     * Récupére un panier en fonction de la session
     * @access protected
     * @param string session_key
     * @return array
     */
    protected function count_cart_items($id_cart){
        $sql=' SELECT count(items.id_item) as total
        FROM mc_plugins_cartpay_items AS items
        WHERE id_cart = :id_cart';
        return magixglobal_model_db::layerDB()->selectOne($sql,array(
            ':id_cart' => $id_cart
        ));
    }

    /**
     * Ajoute un prix en db
     * @access protected
     * return array
     */
    protected function i_cart_items($id_cart,$idcatalog,$quantity,$price){
        $sql='INSERT INTO mc_plugins_cartpay_items (id_cart,idcatalog,quantity_items,price_items)
        VALUE (:id_cart,:idcatalog,:quantity_item,:price_item)';
        return magixglobal_model_db::layerDB()->insert($sql,array(
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
    protected function i_cart_session($idlang,$session_key){
        $sql='INSERT INTO mc_plugins_cartpay (idlang,nbr_items_cart,transmission_cart,session_key_cart)
        VALUE (:idlang,0,0,:session_key)';
        return magixglobal_model_db::layerDB()->insert($sql,array(
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
     */
    protected function i_cart_order($id_cart,$transaction_id_order,$amount_order,$shipping_price_order,$currency_order){
        $sql='INSERT INTO mc_plugins_cartpay_order (id_cart,transaction_id_order,amount_order,shipping_price_order,currency_order)
        VALUE (:id_cart,:transaction_id_order,:amount_order,:shipping_price_order,:currency_order)';
        return magixglobal_model_db::layerDB()->insert($sql,array(
            ':id_cart'              => $id_cart,
            ':transaction_id_order' => $transaction_id_order,
            ':amount_order'         => $amount_order,
            ':shipping_price_order' => $shipping_price_order,
            ':currency_order'       => $currency_order
        ));
    }

    /**
     * Ajoute une demande de réservation
     * @access protected
     * return array
     */
    protected function i_booking($idcatalog,$idprofil,$quantity){
        $sql='INSERT INTO mc_plugins_booking (idprofil,idcatalog,quantity_bk)
        VALUE (:idprofil,:idcatalog,:quantity_bk)';
        return magixglobal_model_db::layerDB()->insert($sql,array(
            ':idprofil'     => $idprofil,
            ':idcatalog'    => $idcatalog,
            ':quantity_bk'  => $quantity
        ));
    }
    /**
     * @access protected
     * Mise à jour du nombre d'éléments dans le panier
     * @param integer $id_cart
     * @param integer $nbr_items
     */
    protected function u_cart_items($id_cart,$nbr_items){
        $sql='UPDATE mc_plugins_cartpay SET
          nbr_items_cart=:nbr_items_cart
          WHERE id_cart=:id_cart';
        magixglobal_model_db::layerDB()->update($sql,
            array(
                ':id_cart' => $id_cart,
                ':nbr_items_cart'=> $nbr_items
            )
        );
    }
    /**
     * @access protected
     * Mise à jour du prix d'un item
     * @param integer $id_item
     * @param integer $price_item
     */
    protected function u_cart_item_price($id_item,$price_item){
        $sql='UPDATE mc_plugins_cartpay_items SET
          price_items=:price_item
          WHERE id_item=:id_item';
        magixglobal_model_db::layerDB()->update($sql,
            array(
                ':id_item' => $id_item,
                ':price_item'=> $price_item
            )
        );
    }
    /**
     * @access protected
     * Mise à jour de la quantité d'un item
     * @param integer $id_item
     * @param integer $quantity_items
     */
    protected function u_cart_item_qty($id_item,$quantity_items){
        $sql='UPDATE mc_plugins_cartpay_items SET
          quantity_items=:quantity_items
          WHERE id_item=:id_item';
        magixglobal_model_db::layerDB()->update($sql,
            array(
                ':id_item' => $id_item,
                ':quantity_items'=> $quantity_items
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
     * @param $postal
     * @param $country
     * @param $message
     * @param $street_liv
     * @param $city_liv
     * @param $postal_liv
     * @param $country_liv
     */
    protected function u_cart_customer_infos($id_cart,$idprofil = null,$firstname,$lastname,$email,$phone,$street,$city,$tva,$postal,$country,$message,$street_liv,$city_liv,$postal_liv,$country_liv){
        $sql='UPDATE mc_plugins_cartpay SET
          idprofil=:idprofil, firstname_cart=:firstname_cart, lastname_cart=:lastname_cart, email_cart=:email_cart, phone_cart=:phone_cart,
          street_cart=:street_cart, city_cart=:city_cart, tva_cart=:tva_cart, postal_cart=:postal_cart, country_cart=:country_cart, message_cart=:message_cart,
          street_liv_cart=:street_liv_cart, city_liv_cart=:city_liv_cart,
          postal_liv_cart=:postal_liv_cart, country_liv_cart=:country_liv_cart
          WHERE id_cart=:id_cart';
        magixglobal_model_db::layerDB()->update($sql,
            array(
                ':id_cart' => $id_cart,
                ':idprofil' => $idprofil,
                ':firstname_cart'=> $firstname,
                ':lastname_cart'=> $lastname,
                ':email_cart'=> $email,
                ':phone_cart'=> $phone,
                ':street_cart'=> $street,
                ':city_cart'=> $city,
                ':tva_cart'=> $tva,
                ':postal_cart'=> $postal,
                ':country_cart'=> $country,
                ':message_cart'=> $message,
                ':street_liv_cart'=> $street_liv,
                ':city_liv_cart'=> $city_liv,
                ':postal_liv_cart'=> $postal_liv,
                ':country_liv_cart'=> $country_liv
            )
        );
    }

//u_cart_infos($id_cart,$firstname,$lastname,$email,$phone,$street,$city,$postal,$country,$message)
    /**
     * @access protected
     * Mise à jour du statu de l'envois du panier (transmission_cart)
     * @param integer $id_cart
     * @param bool $val_transmission[0,1]
     */
    protected function u_transmission_cart($id_cart,$val_transmission){
        $sql='UPDATE mc_plugins_cartpay SET
          transmission_cart=:transmission_cart
          WHERE id_cart=:id_cart';
        magixglobal_model_db::layerDB()->update($sql,
            array(
                ':id_cart' => $id_cart,
                ':transmission_cart'=> $val_transmission
            )
        );
    }

    /**
     * @access protected
     * Supprime un élément du panier
     * @param integer $id_item
     * */
    protected function d_item_cart($id_item){
        $sql = array('DELETE FROM mc_plugins_cartpay_items
		WHERE id_item = '.$id_item);
        magixglobal_model_db::layerDB()->transaction($sql);
    }

    /**
     * Return complete data by id_cart
     * @param $id_cart
     * @return array
     */
    protected function s_complete_data($id_cart){
        $sql='SELECT ord.id_cart,ord.id_order,ord.transaction_id_order,ord.shipping_price_order,ord.amount_order,ord.date_order,
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
        return magixglobal_model_db::layerDB()->selectOne($sql,array(
            ':id_cart'=>$id_cart
        ));
    }

    /**
     * @param $idprofil
     * @return array
     */
    protected function s_profil_data($idprofil){
        $sql = 'SELECT ord.id_cart,ord.id_order,ord.transaction_id_order,ord.shipping_price_order,ord.amount_order,ord.date_order,
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
        return magixglobal_model_db::layerDB()->select($sql,array(
            ':idprofil' => $idprofil
        ));
    }

    /**
     * @param $idprofil
     * @param $idbooking
     * @return array
     */
    protected function s_booking_info($idprofil,$idbooking){
        $sql = 'SELECT DISTINCT bk.idbooking,bk.quantity_bk,bk.date_bk,pr.idprofil,pr.lastname_pr,pr.firstname_pr,pr.email_pr,catalog.titlecatalog
        FROM mc_plugins_booking AS bk
        JOIN mc_catalog_product AS p ON ( bk.idcatalog = p.idcatalog )
        JOIN mc_plugins_profil AS pr ON(bk.idprofil = pr.idprofil)
        JOIN mc_catalog AS catalog ON ( catalog.idcatalog = p.idcatalog )
        LEFT JOIN mc_catalog_c AS c ON ( c.idclc = p.idclc )
        LEFT JOIN mc_catalog_s AS s ON ( s.idcls = p.idcls )
        LEFT JOIN mc_lang AS lang ON ( catalog.idlang = lang.idlang )
        WHERE bk.idprofil = :idprofil AND bk.idbooking = :idbooking';
        return magixglobal_model_db::layerDB()->selectOne($sql,array(
            ':idprofil' => $idprofil,
            ':idbooking' => $idbooking
        ));
    }
    protected function fetchConfig(){
        $query = "SELECT *
                      FROM mc_plugins_cartpay_config";
        return magixglobal_model_db::layerDB()->selectOne($query);
    }
    /**
     * Retourne la configuration de la TVA de base
     * @param $data
     * @return array
     */
    protected function fetchTva($data){
        if(is_array($data)) {
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
            if($fetch == 'all'){

                if($context == 'config'){
                    // Configuration
                    $query = "SELECT *
                      FROM mc_plugins_cartpay_tva_conf";
                    return magixglobal_model_db::layerDB()->select($query);
                }elseif($context == 'country'){
                    // Liste des pays avec la zone, tva, etc
                    $query = "SELECT t.*,conf.zone_tva,conf.amount_tva
                      FROM mc_plugins_cartpay_tva AS t
                      JOIN mc_plugins_cartpay_tva_conf AS conf ON(t.idtvac=conf.idtvac)
                      ORDER BY t.country ASC";
                    return magixglobal_model_db::layerDB()->select($query);
                }
            }elseif($fetch == 'one'){
                if($context == 'config') {
                    $query = "SELECT t.*,conf.zone_tva,conf.amount_tva
                      FROM mc_plugins_cartpay_tva AS t
                      JOIN mc_plugins_cartpay_tva_conf AS conf ON(t.idtvac=conf.idtvac)
                      WHERE t.country = :country";
                    return magixglobal_model_db::layerDB()->selectOne($query,array(':country'=>$data['country']));
                }
            }
        }
    }
}
?>