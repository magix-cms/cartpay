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
class plugins_cartpay_admin extends db_cartpay{
    /**
     * @var
     */
    protected $header, $template, $message;
    public static $notify = array('plugin' => 'true');
    // getpage
    public $getpage;
    public $tab,$edit,$action,$plugin;
    private static $default_country = array(
        "AF"=>"Afghanistan",
        "AL"=>"Albania",
        "DZ"=>"Algeria",
        "AD"=>"Andorra",
        "AO"=>"Angola",
        "AG"=>"Antigua and Barbuda",
        "AR"=>"Argentina",
        "AM"=>"Armenia",
        "AW"=>"Aruba",
        "AU"=>"Australia",
        "AT"=>"Austria",
        "AZ"=>"Azerbaijan",
        "BS"=>"Bahamas",
        "BH"=>"Bahrain",
        "BD"=>"Bangladesh",
        "BB"=>"Barbados",
        "BY"=>"Belarus",
        "BE"=>"Belgium",
        "BZ"=>"Belize",
        "BJ"=>"Benin",
        "BM"=>"Bermuda",
        "BT"=>"Bhutan",
        "BO"=>"Bolivia",
        "BA"=>"Bosnia-Herzegovina",
        "BW"=>"Botswana",
        "BR"=>"Brazil",
        "VG"=>"British Virgin Islands",
        "BN"=>"Brunei",
        "BG"=>"Bulgaria",
        "BF"=>"Burkina Faso",
        "BI"=>"Burundi",
        "KH"=>"Cambodia",
        "CM"=>"Cameroon",
        "CA"=>"Canada",
        "CV"=>"Cape Verde",
        "KY"=>"Cayman Islands",
        "CF"=>"Central African Republic",
        "TD"=>"Chad",
        "CL"=>"Chile",
        "CN"=>"China",
        "CO"=>"Colombia",
        "KM"=>"Comoros",
        "CG"=>"Congo (Brazzaville)",
        "CD"=>"Congo (Democratic Rep.)",
        "CR"=>"Costa Rica",
        "CI"=>"Cote d'Ivoire",
        "HR"=>"Croatia",
        "CU"=>"Cuba",
        "CY"=>"Cyprus",
        "CZ"=>"Czech Republic",
        "DK"=>"Denmark",
        "DJ"=>"Djibouti",
        "DM"=>"Dominica",
        "DO"=>"Dominican Republic",
        "EC"=>"Ecuador",
        "EG"=>"Egypt",
        "SV"=>"El Salvador",
        "GQ"=>"Equatorial Guinea",
        "ER"=>"Eritrea",
        "EE"=>"Estonia",
        "ET"=>"Ethiopia",
        "FK"=>"Falkland Islands",
        "FO"=>"Faroe Islands",
        "FJ"=>"Fiji",
        "FI"=>"Finland",
        "FR"=>"France",
        "GF"=>"French Guiana",
        "PF"=>"French Polynesia",
        "GA"=>"Gabon",
        "GM"=>"Gambia",
        "GE"=>"Georgia",
        "DE"=>"Germany",
        "GH"=>"Ghana",
        "GI"=>"Gibraltar",
        "GR"=>"Greece",
        "GL"=>"Greenland",
        "GD"=>"Grenada",
        "GP"=>"Guadeloupe",
        "GT"=>"Guatemala",
        "GG"=>"Guernsey",
        "GN"=>"Guinea",
        "GW"=>"Guinea-Bissau",
        "GY"=>"Guyana",
        "HT"=>"Haiti",
        "HN"=>"Honduras",
        "HK"=>"Hong Kong",
        "HU"=>"Hungary",
        "IS"=>"Iceland",
        "IN"=>"India",
        "ID"=>"Indonesia",
        "IR"=>"Iran",
        "IQ"=>"Iraq",
        "IE"=>"Ireland",
        "IM"=>"Isle of Man",
        "IL"=>"Israel",
        "IT"=>"Italy",
        "JM"=>"Jamaica",
        "JP"=>"Japan",
        "JE"=>"Jersey",
        "JO"=>"Jordan",
        "KZ"=>"Kazakhstan",
        "KE"=>"Kenya",
        "KI"=>"Kiribati",
        "KV"=>"Kosovo",
        "KW"=>"Kuwait",
        "KG"=>"Kyrgyzstan",
        "LA"=>"Laos",
        "LV"=>"Latvia",
        "LB"=>"Lebanon",
        "LS"=>"Lesotho",
        "LR"=>"Liberia",
        "LY"=>"Libya",
        "LI"=>"Liechtenstein",
        "LT"=>"Lithuania",
        "LU"=>"Luxembourg",
        "MO"=>"Macau",
        "MK"=>"Macedonia",
        "MG"=>"Madagascar",
        "MW"=>"Malawi",
        "MY"=>"Malaysia",
        "MV"=>"Maldives",
        "ML"=>"Mali",
        "MT"=>"Malta",
        "MH"=>"Marshall Islands",
        "MQ"=>"Martinique",
        "MR"=>"Mauritania",
        "MU"=>"Mauritius",
        "YT"=>"Mayotte",
        "MX"=>"Mexico",
        "FM"=>"Micronesia",
        "MD"=>"Moldova",
        "MC"=>"Monaco",
        "MN"=>"Mongolia",
        "ME"=>"Montenegro",
        "MA"=>"Morocco",
        "MZ"=>"Mozambique",
        "MM"=>"Myanmar",
        "NA"=>"Namibia",
        "NR"=>"Nauru",
        "NP"=>"Nepal",
        "NL"=>"Netherlands",
        "NC"=>"New Caledonia",
        "NZ"=>"New Zealand",
        "NI"=>"Nicaragua",
        "NE"=>"Niger",
        "NG"=>"Nigeria",
        "KP"=>"North Korea",
        "NO"=>"Norway",
        "OM"=>"Oman",
        "PK"=>"Pakistan",
        "PW"=>"Palau",
        "PA"=>"Panama",
        "PG"=>"Papua New Guinea",
        "PY"=>"Paraguay",
        "PE"=>"Peru",
        "PH"=>"Philippines",
        "PL"=>"Poland",
        "PT"=>"Portugal",
        "PR"=>"Puerto Rico",
        "QA"=>"Qatar",
        "RE"=>"Reunion",
        "RO"=>"Romania",
        "RU"=>"Russia",
        "RW"=>"Rwanda",
        "BL"=>"Saint Barthelemy",
        "KN"=>"Saint Kitts and Nevis",
        "LC"=>"Saint Lucia",
        "MF"=>"Saint Martin",
        "PM"=>"Saint Pierre and Miquelon",
        "VC"=>"Saint Vincent and the Grenadines",
        "WS"=>"Samoa",
        "SM"=>"San Marino",
        "ST"=>"Sao Tome and Principe",
        "SA"=>"Saudi Arabia",
        "SN"=>"Senegal",
        "RS"=>"Serbia",
        "SC"=>"Seychelles",
        "SL"=>"Sierra Leone",
        "SG"=>"Singapore",
        "SK"=>"Slovakia",
        "SI"=>"Slovenia",
        "SB"=>"Solomon Islands",
        "SO"=>"Somalia",
        "ZA"=>"South Africa",
        "KR"=>"South Korea",
        "SS"=>"South Sudan",
        "ES"=>"Spain",
        "LK"=>"Sri Lanka",
        "SD"=>"Sudan",
        "SR"=>"Suriname",
        "SJ"=>"Svalbard",
        "SZ"=>"Swaziland",
        "SE"=>"Sweden",
        "CH"=>"Switzerland",
        "SY"=>"Syria",
        "TW"=>"Taiwan",
        "TJ"=>"Tajikistan",
        "TZ"=>"Tanzania",
        "TH"=>"Thailand",
        "TL"=>"Timor-Leste",
        "TG"=>"Togo",
        "TO"=>"Tonga",
        "TT"=>"Trinidad and Tobago",
        "TN"=>"Tunisia",
        "TR"=>"Turkey",
        "TM"=>"Turkmenistan",
        "TC"=>"Turks and Caicos",
        "TV"=>"Tuvalu",
        "UG"=>"Uganda",
        "UA"=>"Ukraine",
        "AE"=>"United Arab Emirates",
        "GB"=>"United Kingdom",
        "US"=>"United States",
        "UY"=>"Uruguay",
        "UZ"=>"Uzbekistan",
        "VU"=>"Vanuatu",
        "VA"=>"Vatican City",
        "VE"=>"Venezuela",
        "VN"=>"Vietnam",
        "WF"=>"Wallis et Futuna",
        "EH"=>"Western Sahara",
        "YE"=>"Yemen",
        "ZM"=>"Zambia",
        "ZW"=>"Zimbabwe"
    );
    public $mail_order,$mail_order_from,$profil,$online_payment,$bank_wire,$hipay,$ogone,$shipping,$account_owner,$contact_details,$bank_address;
    public $zone_tva_1,$zone_tva_2,$amount_tva_1,$amount_tva_2,$remove_tva;
    public $iso,$country,$idtvac;
    /**
     * construct
     */
    public function __construct(){
        if (class_exists('backend_model_message')) {
            $this->message = new backend_model_message();
        }
        if (magixcjquery_filter_request::isGet('tab')) {
            $this->tab = magixcjquery_form_helpersforms::inputClean($_GET['tab']);
        }
        if (magixcjquery_filter_request::isGet('edit')) {
            $this->edit = magixcjquery_filter_isVar::isPostNumeric($_GET['edit']);
        }
        if (magixcjquery_filter_request::isGet('action')) {
            $this->action = magixcjquery_form_helpersforms::inputClean($_GET['action']);
        }
        if (magixcjquery_filter_request::isGet('plugin')) {
            $this->plugin = magixcjquery_form_helpersforms::inputClean($_GET['plugin']);
        }
        //GET
        if(magixcjquery_filter_request::isGet('page')) {
            // si numéric
            if(is_numeric($_GET['page'])){
                $this->getpage = intval($_GET['page']);
            }else{
                // Sinon retourne la première page
                $this->getpage = 1;
            }
        }else {
            $this->getpage = 1;
        }
        // POST
        /* ################# CONFIG ###################*/
        if (magixcjquery_filter_request::isPost('mail_order')) {
            $this->mail_order = magixcjquery_form_helpersforms::inputClean($_POST['mail_order']);
        }
        if (magixcjquery_filter_request::isPost('mail_order_from')) {
            $this->mail_order_from = magixcjquery_form_helpersforms::inputClean($_POST['mail_order_from']);
        }
        if (magixcjquery_filter_request::isPost('online_payment')) {
            $this->online_payment = 1;
        }
        if (magixcjquery_filter_request::isPost('profil')) {
            $this->profil = 1;
        }
        if (magixcjquery_filter_request::isPost('bank_wire')) {
            $this->bank_wire = 1;
        }
        if (magixcjquery_filter_request::isPost('hipay')) {
            $this->hipay = 1;
        }
        if (magixcjquery_filter_request::isPost('ogone')) {
            $this->ogone = 1;
        }
        if (magixcjquery_filter_request::isPost('shipping')) {
            $this->shipping = 1;
        }
        if (magixcjquery_filter_request::isPost('account_owner')) {
            $this->account_owner = magixcjquery_form_helpersforms::inputClean($_POST['account_owner']);
        }
        if (magixcjquery_filter_request::isPost('contact_details')) {
            $this->contact_details = magixcjquery_form_helpersforms::inputClean($_POST['contact_details']);
        }
        if (magixcjquery_filter_request::isPost('bank_address')) {
            $this->bank_address = magixcjquery_form_helpersforms::inputClean($_POST['bank_address']);
        }
        /* ################### TVA #####################*/
        // Config
        if (magixcjquery_filter_request::isPost('amount_tva_1')) {
            $this->amount_tva_1 = magixcjquery_form_helpersforms::inputClean($_POST['amount_tva_1']);
        }
        if (magixcjquery_filter_request::isPost('amount_tva_2')) {
            $this->amount_tva_2 = magixcjquery_form_helpersforms::inputClean($_POST['amount_tva_2']);
        }
        if (magixcjquery_filter_request::isPost('zone_tva_1')) {
            $this->zone_tva_1 = magixcjquery_form_helpersforms::inputClean($_POST['zone_tva_1']);
        }
        if (magixcjquery_filter_request::isPost('zone_tva_2')) {
            $this->zone_tva_2 = magixcjquery_form_helpersforms::inputClean($_POST['zone_tva_2']);
        }
        // TVA
        if (magixcjquery_filter_request::isPost('remove_tva')) {
            $this->remove_tva = magixcjquery_form_helpersforms::inputClean($_POST['remove_tva']);
        }
        if (magixcjquery_filter_request::isPost('iso')) {
            $this->iso = magixcjquery_form_helpersforms::inputClean($_POST['iso']);
        }
        if (magixcjquery_filter_request::isPost('country')) {
            $this->country = magixcjquery_form_helpersforms::inputClean($_POST['country']);
        }
        if (magixcjquery_filter_request::isPost('idtvac')) {
            $this->idtvac = magixcjquery_form_helpersforms::inputClean($_POST['idtvac']);
        }
        $this->header = new magixglobal_model_header();
        $this->template = new backend_controller_plugins();
    }
    /* ################# ORDER ###################*/
    /**
     * offset for pager in pagination
     * @param $max
     * @return int
     */
    private function offsetPager($max){
        $pagination = new magixcjquery_pager_pagination();
        return $pagination->pageOffset($max,$this->getpage);
    }

    /**
     * @param $max
     * @return string
     */
    private function paginationList($limit){
        $data = parent::fetchOrder(
            array(
                'fetch'=>'count'
            )
        );
        $total = $data['total'];
        // *** Set pagination
        $dataPager = null;
        if (isset($total) AND isset($limit)) {
            $lib_rewrite = new magixglobal_model_rewrite();
            $basePath = $this->template->pluginUrl().'&amp;';
            $dataPager = magixglobal_model_pager::setPaginationData(
                $total,
                $limit,
                $basePath,
                $this->getpage,
                '='
            );
            $pagination = null;
            if ($dataPager != null) {
                $pagination = '<ul class="pagination">';
                foreach ($dataPager as $row) {
                    switch ($row['name']){
                        case 'first':
                            $name = '<<';
                            break;
                        case 'previous':
                            $name = '<';
                            break;
                        case 'next':
                            $name = '>';
                            break;
                        case 'last':
                            $name = '>>';
                            break;
                        default:
                            $name = $row['name'];
                    }
                    $classItem = ($name == $this->getpage) ? ' class="active"' : null;
                    $pagination .= '<li'.$classItem.'>';
                    $pagination .= '<a href="'.$row['url'].'" title="'.$name.'" >';
                    $pagination .= $name;
                    $pagination .= '</a>';
                    $pagination .= '</li>';
                }
                $pagination .= '</ul>';
            }
            unset($total);
            unset($limit);
        }
        return $pagination;
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
            $tax_amount = number_format(($value['amount_order'] - ($value['amount_order'] / $tva_amount)), 2, '.', '');

            $newData[$key]['id_cart'] = $value['id_cart'];
            $newData[$key]['id_order'] = $value['id_order'];
            $newData[$key]['idlang'] = $value['idlang'];
            $newData[$key]['iso'] = $value['iso'];
            $newData[$key]['shipping_price_order'] = $value['shipping_price_order'];
            $newData[$key]['amount_order'] = $value['amount_order'];
            $newData[$key]['amount_tax'] = $tax_amount;
            $newData[$key]['currency_order'] = $value['currency_order'];
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
            $newData[$key]['street_liv_cart'] = $value['street_liv_cart'];
            $newData[$key]['city_liv_cart'] = $value['city_liv_cart'];
            $newData[$key]['postal_liv_cart'] = $value['postal_liv_cart'];
            $catalog[$key]['catalog'] = array_map(
                null,
                explode('|', $value['CATALOG_LIST_NAME']),
                explode('|', $value['CATALOG_LIST_QUANTITY']),
                explode('|', $value['CATALOG_LIST_PRICE']),
                explode('|', ($value['CATALOG_LIST_PRICE']*$value['CATALOG_LIST_QUANTITY']))
            );
            foreach($catalog[$key]['catalog'] as $key1 => $value1){
                $newData[$key]['catalog'][$key1]['CATALOG_LIST_NAME'] = $value1[0];
                $newData[$key]['catalog'][$key1]['CATALOG_LIST_QUANTITY'] = $value1[1];
                $newData[$key]['catalog'][$key1]['CATALOG_LIST_PRICE'] = $value1[2];
                $newData[$key]['catalog'][$key1]['CATALOG_LIST_SUBTOTAL_PRICE'] = number_format($value1[3], 2, '.', '');
            }
        }
        //$catalog = array_map(null, $listtabs, $idtabs, $pricetabs);

        /*$newKey = array(
            'id_order'  => 'id_order',
            'shipping_price_order'    => 'shipping_price_order',
            'catalog'=> $catalog
        );*/
        //return $this->arrayChangeKeys($row, $newKey);
        return $newData;
    }

    /**
     * @param $max
     * @return array
     */
    private function getItemsOrderData($max){
        $limit = $max;
        $offset = $this->offsetPager($max);
        $data = parent::fetchOrder(
            array(
                'fetch'=>'all',
                'limit'=>$limit,
                'offset'=>$offset
            )
        );
        return $this->setItemsOrderData($data);
    }
    /**
     * @param $max
     */
    private function setOrderData($max){
        $row = $this->getItemsOrderData($max);
        $pagination = $this->paginationList($max);
        $this->template->assign('pagination',$pagination);
        $this->template->assign('getOrderData',$row);
        /**/
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

    /**
     * @return array
     */
    private function setPostConfig(){
        $data = $this->getConfigData();
        if($data['idconfig'] != null){
            $edit = $data['idconfig'];
        }else{
            $edit = false;
        }
        if(!isset($this->profil)){
            $profil = '0';
        }else{
            $profil = $this->profil;
        }
        if(!isset($this->online_payment)){
            $online_payment = '0';
        }else{
            $online_payment = $this->online_payment;
        }
        if(!isset($this->bank_wire)){
            $bank_wire = '0';
        }else{
            $bank_wire = $this->bank_wire;
        }
        if(!isset($this->hipay)){
            $hipay = '0';
        }else{
            $hipay = $this->hipay;
        }
        if(!isset($this->ogone)){
            $ogone = '0';
        }else{
            $ogone = $this->ogone;
        }
        if(!isset($this->shipping)){
            $shipping = '0';
        }else{
            $shipping = $this->shipping;
        }
        return array(
            'edit'           =>  $edit,
            'fetch'          =>  'config',
            'mail_order'     =>  $this->mail_order,
            'mail_order_from'=>  $this->mail_order_from,
            'profil'         =>  $profil,
            'online_payment' =>  $online_payment,
            'bank_wire'      =>  $bank_wire,
            'hipay'          =>  $hipay,
            'ogone'          =>  $ogone,
            'shipping'       =>  $shipping,
            'account_owner'  =>  (!empty($this->account_owner))? $this->account_owner: NULL,
            'contact_details'=>  (!empty($this->contact_details))? $this->contact_details: NULL,
            'bank_address'   =>  (!empty($this->bank_address))? $this->bank_address: NULL
        );
    }
    /* ########### TVA ############*/
    private function getTvaConfData(){
        return parent::fetchTva(
            array(
                'fetch'=>'all',
                'context'=>'config'
            )
        );
    }
    private function setPostTvac($amount_tva,$zone_tva){
        return array(
            'fetch'         =>  'tvac',
            'amount_tva'=>  $amount_tva,
            'zone_tva'     =>  $zone_tva
        );
    }
    /**
     * @param $row
     * @return array
     */
    public function setItemsTvaData ($row)
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
     * @param $iso
     * @param $country
     * @param $idtvac
     * @return array
     */
    private function setPostTva($iso,$country,$idtvac){
        return array(
            'fetch'         =>  'tva',
            'iso'=>             $iso,
            'country'       =>  $country,
            'idtvac'        =>  $idtvac
        );
    }
    /**
     * @param $max
     * @return array
     */
    private function getItemsTvaData(){
        $data = parent::fetchTva(
            array(
                'fetch'=>'all',
                'context'=>'country'
            )
        );
        return $this->setItemsTvaData($data);
    }
    /* ########### Global ##############*/
    /**
     * @param $data
     */
    private function add($data){
        parent::insert($data);
    }

    /**
     * @param $data
     */
    private function update($data){
        parent::uData($data);
    }

    /**
     * @param $data
     */
    private function save($data,$action){
        if($action == 'update'){
            $this->update($data);
            $this->message->getNotify('update',self::$notify);
        }else{
            $this->add($data);
            $this->message->getNotify('add',self::$notify);
        }
    }
    /**
     * Suppression d'un pays
     * @access private
     * @param integer $remove_tva
     */
    private function removeTva($remove_tva){
        if(isset($remove_tva)){
            $this->delete($remove_tva);
        }
    }
    /**
     * run
     */
    public function run(){
        if($this->tab == 'config'){
            if(isset($this->action)){
                if($this->action === 'update'){
                    $this->save(
                        $this->setPostConfig(),
                        'update'
                    );
                }
            }else{
                $this->template->assign('getDataConfig',$this->getConfigData());
                $this->template->display('config.tpl');
            }

        }elseif($this->tab == 'about'){
            $this->template->display('about.tpl');
        }elseif($this->tab == 'tva'){
            if(isset($this->zone_tva_1) OR isset($this->zone_tva_2)){
                $tvac1 = parent::fetchTva(
                    array(
                        'fetch'=>'one',
                        'context'=>'config',
                        'zone_tva'=> $this->zone_tva_1
                    )
                );
                $tvac2 = parent::fetchTva(
                    array(
                        'fetch'=>'one',
                        'context'=>'config',
                        'zone_tva'=> $this->zone_tva_2
                    )
                );
                if($tvac1['idtvac'] != null){
                    $this->save(
                        $this->setPostTvac($this->amount_tva_1,$this->zone_tva_1),
                        'update'
                    );
                }else{
                    $this->save(
                        $this->setPostTvac($this->amount_tva_1,$this->zone_tva_1),
                        'add'
                    );
                }
                if($tvac2['idtvac'] != null){
                    $this->update(
                        $this->setPostTvac($this->amount_tva_2,$this->zone_tva_2)
                    );
                }else{
                    $this->add(
                        $this->setPostTvac($this->amount_tva_2,$this->zone_tva_2)
                    );
                }
                /*$this->save(
                    $this->setPostConfig(),
                    'update'
                );*/
            }elseif($this->action){
                if($this->action === 'html'){
                    $this->header->head_expires("Mon, 26 Jul 1997 05:00:00 GMT");
                    $this->header->head_last_modified(gmdate( "D, d M Y H:i:s" ) . "GMT");
                    $this->header->pragma();
                    $this->header->cache_control("nocache");
                    $this->header->getStatus('200');
                    $this->header->html_header("UTF-8");
                    $this->template->assign('getItemsTvaData',$this->getItemsTvaData());
                    $this->template->display('loop/tva.tpl');
                }elseif($this->action === 'remove'){
                    $this->removeTva($this->remove_tva);
                }elseif($this->action === 'add'){
                    $this->save(
                        $this->setPostTva($this->iso,$this->country,$this->idtvac),
                        'add'
                    );
                }
            }else{
                $this->template->assign('countryTools',self::$default_country);
                $this->template->assign('getConfDataTVA',$this->getTvaConfData());
                $this->template->display('tva.tpl');
            }
        }else{
            $this->setOrderData(30);
            $this->template->display('list.tpl');
        }
    }

    /**
     * @return array
     */
    public function setConfig(){
        return array(
            'url'=> array(
                'lang'  => 'none',
                'action'=>'',
                'name'=>'Cartpay'
            ),
            'icon'=> array(
                'type'=>'font',
                'name'=>'fa fa-shopping-cart'
            )
        );


    }
}
class db_cartpay{
    /**
     * Vérifie si les tables du plugin sont installé
     * @access protected
     * return integer
     */
    protected function c_show_table(){
        $table = 'mc_plugins_cartpay';
        return magixglobal_model_db::layerDB()->showTable($table);
    }
    protected function s_count_cart_order(){
        $sql = 'SELECT count(ord.id_order) AS total
		FROM mc_plugins_cartpay_order AS ord';
        return magixglobal_model_db::layerDB()->selectOne($sql);
    }
    protected function s_count_cart_order_currentDate(){
        $sql = 'SELECT count(ord.id_order) AS total
		FROM mc_plugins_cartpay_order AS ord
		WHERE DATE_FORMAT(date_order, "%Y%m%d") = DATE_FORMAT(NOW(), "%Y%m%d")';
        return magixglobal_model_db::layerDB()->selectOne($sql);
    }
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
                      ORDER BY conf.zone_tva DESC,t.country ASC";
                    return magixglobal_model_db::layerDB()->select($query);
                }
            }elseif($fetch == 'one'){
                if($context == 'config') {
                    $query = "SELECT *
                      FROM mc_plugins_cartpay_tva_conf WHERE
                      zone_tva=:zone_tva";
                    return magixglobal_model_db::layerDB()->selectOne($query,array(':zone_tva'=>$data['zone_tva']));
                }
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
    protected function delete($remove_tva){
        $sql = 'DELETE FROM mc_plugins_cartpay_tva WHERE idtva = :remove';
        magixglobal_model_db::layerDB()->delete($sql,
            array(
                ':remove'   =>  $remove_tva
            )
        );
    }
}