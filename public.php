<?php
require_once('db.php');
/*
 # -- BEGIN LICENSE BLOCK ----------------------------------
 #
 # This file is part of MAGIX CMS.
 # MAGIX CMS, The content management system optimized for users
 # Copyright (C) 2008 - 2013 magix-cms.com <support@magix-cms.com>
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
/**
 * MAGIX CMS
 * @category   advantage
 * @package    plugins
 * @copyright  MAGIX CMS Copyright (c) 2008 - 2018 Gerits Aurelien,
 * http://www.magix-cms.com,  http://www.magix-cjquery.com
 * @license    Dual licensed under the MIT or GPL Version 3 licenses.
 * @version    3.0
 * Author: Gerits Aurelien
 * Date: 24-08-2018
 * Time: 10:38
 */
class plugins_cartpay_public extends plugins_cartpay_db
{
    /**
     * @var object
     */
    protected $template,
        $data,
        $header,
        $message,
        $lang,
        $session,
        $settings,
        $module,
        $activeMods,
        $about,
        $sanitize,
        $modelDomain,
        $mail,
		$routingUrl,
		$imagesComponent;

    /**
     * @var array $config
     */
    private $config;

    /**
     * Session var
     * @var string $email_session
     * @var string $keyuniqid_session
     * @var integer $id_account_session
     */
    private $email_session,
        $session_key_cart,
        $id_account_session;

    /**
     * Les variables globales
     * @var integer $edit
     * @var string $action
     * @var string $tabs
     */
    public $edit = 0,
        $action = '',
        $hash = '',
        $key = '',
        $tab = '';

    /**
     * Les variables plugin
     * @var array $v_email
     * @var array $account
     * @var array $address
     * @var string $gRecaptchaResponse
     */
    public $v_email,
        $cart;

    public function __construct()
    {
        $this->template = new frontend_model_template();
        $this->data = new frontend_model_data($this);
        $this->header = new http_header();
        $this->settings = new frontend_model_setting();
        $set = $this->settings->getSetting('ssl');
        $this->session = new http_session($set['ssl']);
        $this->message = new component_core_message($this->template);
        $this->lang = $this->template->currentLanguage();
        $this->sanitize = new filter_sanitize();
        $this->mail = new mail_swift('mail');
        $this->modelDomain = new frontend_model_domain($this->template);
        $this->routingUrl = new component_routing_url();
		$this->imagesComponent = new component_files_images($this->template);

        $this->session->start('mc_cartpay');
        //$this->session->token('token_ac');

        /*if(class_exists('plugins_profil_module')) {
            $this->module = new plugins_profil_module();
        }*/

        $formClean = new form_inputEscape();

        // --- Session
        /*if (http_request::isSession('email_ac')) {
            $this->email_session = $formClean->simpleClean($_SESSION['email_ac']);
        }*/
        if (http_request::isSession('session_key_cart')) {
            $this->session_key_cart = $formClean->simpleClean($_SESSION['session_key_cart']);
        }
        if (http_request::isSession('id_account')) {
            $this->id_account_session = (int)$formClean->simpleClean($_SESSION['id_account']);
        }

        // --- Get
        if (http_request::isGet('action')) {
            $this->action = $formClean->simpleClean($_GET['action']);
        }

        if (http_request::isGet('v_email')) {
            $this->v_email = $formClean->simpleClean($_GET['v_email']);
        }

        // --- Post
        if (http_request::isPost('cart')) {
            $this->cart = (array)$formClean->arrayClean($_POST['cart']);
        }
    }

    /**
     * Assign data to the defined variable or return the data
     * @param string $type
     * @param string|int|null $id
     * @param string $context
     * @param boolean $assign
     * @return mixed
     */
    private function getItems($type, $id = null, $context = null, $assign = true) {
        return $this->data->getItems($type, $id, $context, $assign);
    }

	// --- Session
    /**
     * Open Session
     * @param $session_key_cart
     * @param $id_account
     * @return bool
     */
    private function openSession($session_key_cart, $id_account = null) {

        $this->add(array(
            'type' => 'session',
            'data' => array(
                'session_key_cart' => $session_key_cart,
                'id_account' => $id_account
            )
        ));

        return true;
    }

    /**
     * @return mixed
     */
    private function compareSessionId() {
        return $this->getItems('session',array('id_session' => session_id()),'one',false);
    }
    // --------------------

    // --- Database actions
    /**
     * Insert data
     * @param array $config
     */
    private function add($config)
    {
        switch ($config['type']) {
            case 'product':
            case 'quotation':
            case 'session':
                parent::insert(
                    array('type' => $config['type']),
                    $config['data']
                );
                break;
        }
    }

    /**
     * Insert data
     * @param array $config
     */
    private function upd($config)
    {
        switch ($config['type']) {
            case 'session':
            case 'quantity':
            case 'status':
                parent::update(
                    array('type' => $config['type']),
                    $config['data']
                );
                break;
        }
    }
	// --------------------

	// --- Cartpay actions
    /**
     * @param $data
     */
    private function addToCart($data){
        $product = $this->getItems('product',array('id' => $data['id_cart'],'id_product' => $data['id_product']),'one',false);
        if($product != null){
            $this->upd(array(
                'type' => 'quantity',
                'data' => array(
                    'id_cart' => $data['id_cart'],
                    'id_product' => $data['id_product'],
                    'quantity' => $product['quantity']+$data['quantity']
                )
            ));
        }else{
            $this->add(array(
                'type' => 'product',
                'data' => array(
                    'id_cart' => $data['id_cart'],
                    'id_product' => $data['id_product'],
                    'quantity' => $data['quantity']
                )
            ));
        }
        $jsonData = $this->getItems('countProduct',array('id' => $data['id_cart']),'one',false);
        $this->message->json_post_response(true,null,$jsonData);
    }

    /**
     *
     */
    public function cartData(){
        if(isset($this->session_key_cart)){
            //print $this->session_key_cart;
            $key_cart = $this->getItems('session',array('session_key_cart' => $this->session_key_cart),'one',false);
            if($key_cart != null){
                //print_r($key_cart);
                if($key_cart['id_account'] == NULL || $key_cart['id_account'] == ''){
                    $this->session->start('mc_account');
                    $account = $_SESSION;
                    $this->session->start('mc_cartpay');
                    if(isset($account['id_account']) && $account['id_account'] != NULL){
                        $this->upd(array(
                            'type' => 'session',
                            'data' => array(
                                'id' => $key_cart['id_cart'],
                                'id_account' => $account['id_account']
                            )
                        ));
                    }
                }
                $cart = array();
                $product = $this->getItems('countProduct',array('id' => $key_cart['id_cart']),'one',false);
                //print_r($product);
                if($product != null){
                    $cart['nbr_cart'] = $product['nbr'];
                }else{
                    $cart['nbr_cart'] = 0;
                }

                return $cart;


            }else{
                $this->openSession($this->session_key_cart,$this->id_account_session);
                $key_cart = $this->getItems('session',array('session_key_cart' => $this->session_key_cart),'one',false);
                $product = $this->getItems('countProduct',array('id' => $key_cart['id_cart']),'one',false);
                //print_r($product);
                if($product != null){
                    $cart['nbr_cart'] = $product['nbr'];
                }else{
                    $cart['nbr_cart'] = 0;
                }

                return $cart;
            }
        }
    }

	/**
	 * Return cartpay config
	 * @return mixed
	 */
	public function getConfig()
	{
		return $this->getItems('config',NULL,'one',false);
    }

    // --- Mail
    /**
     * @param $type
     * @return string
     */
    private function setTitleMail($type){
        $about = new frontend_model_about($this->template);
        $collection = $about->getCompanyData();

        switch ($type) {
            default: $title = $this->template->getConfigVars($type.'_title');
        }

        return sprintf($title, $collection['name']);
    }

    /**
     * @param string $tpl
     * @param bool $debug
     * @return string
     */
    private function getBodyMail($tpl, $debug = false){
        $cssInliner = $this->settings->getSetting('css_inliner');
        $this->template->assign('getDataCSSIColor',$this->settings->fetchCSSIColor());

        $data = array();

        switch ($tpl) {
            default: $data = $this->cart;
        }

        $key_cart = $this->getItems('session',array('session_key_cart' => $this->session_key_cart),'one',false);
        $langData = $this->getItems('idFromIso',array('iso' => $this->lang),'one',false);

        $product = $this->getItems('catalog',array('id' => $key_cart['id_cart'],':default_lang'=>$langData['id_lang']),'all',false);

        foreach ($product as $item => $val) {
            $product[$item]['url'] = $this->routingUrl->getBuildUrl(array(
                    'type' => 'product',
                    'iso' => $val['iso_lang'],
                    'id' => $val['id_product'],
                    'url' => $val['url_p'],
                    'id_parent'         =>  $val['id_cat'],
                    'url_parent'        =>  $val['url_cat']
                )
            );
        }

        $account = $this->getItems('account',$key_cart['id_account'],'one',false);

        if(!empty($account)) $this->template->assign('account',$account);
        if(!empty($product)) $this->template->assign('product',$product);

        //if(!empty($data)) $this->template->assign('data',$data);

        $bodyMail = $this->template->fetch('cartpay/mail/'.$tpl.'.tpl');
        if ($cssInliner['value']) {
            $bodyMail = $this->mail->plugin_css_inliner($bodyMail,array(component_core_system::basePath().'skin/'.$this->template->themeSelected().'/mail/css' => 'mail.min.css'));
        }

        if($debug) {
            print $bodyMail;
        }
        else {
            return $bodyMail;
        }
    }

    /**
     * Send a mail
     * @param $email
     * @param $tpl
     * @return bool
     */
    protected function send_email($email, $tpl) {
        if($email) {
            $this->template->configLoad();
            if(!$this->sanitize->mail($email)) {
                $this->message->json_post_response(false,'error_mail');
            }
            else {
                if($this->lang) {
                    $noreply = '';

                    $allowed_hosts = array_map(function($dom) { return $dom['url_domain']; },$this->modelDomain->getValidDomains());

                    if (!isset($_SERVER['HTTP_HOST']) || !in_array($_SERVER['HTTP_HOST'], $allowed_hosts)) {
                        header($_SERVER['SERVER_PROTOCOL'].' 400 Bad Request');
                        exit;
                    }
                    else {
                        $noreply = 'noreply@'.str_replace('www.','',$_SERVER['HTTP_HOST']);
                    }

                    if(!empty($noreply)) {

                        $message = $this->mail->body_mail(
                            self::setTitleMail($tpl),
                            array($noreply),
                            array($email),
                            self::getBodyMail($tpl),
                            false
                        );

                        if($this->mail->batch_send_mail($message)) {
                            return true;
                        }
                        else {
                            $this->message->json_post_response(false,'error');
                            return false;
                        }
                    }
                    else {
                        $this->message->json_post_response(false,'error_config');
                        return false;
                    }
                }
            }
        }
    }
    // --------------------


    public function run(){
        if(!$this->action) {
        	$conf = $this->getItems('config',NULL,'one',false);
			$breadplugin = array();
			$breadplugin[] = array('name' => $this->template->getConfigVars('cart_'.$conf['type_order']));
			$this->template->assign('breadplugin', $breadplugin);

            //$this->getItems('config',NULL,'one','config_cart');
            $key_cart = $this->getItems('session',array('session_key_cart' => $this->session_key_cart),'one','session_cart');
            $langData = $this->getItems('idFromIso',array('iso' => $this->lang),'one',false);
            //$this->account['id_lang'] = $langData['id_lang'];
            $product = $this->getItems('catalog',array('id' => $key_cart['id_cart'],':default_lang'=>$langData['id_lang']),'all',false);
            /*print '<pre>';
            print_r($product);
            print '</pre>';*/
            foreach ($product as $item => $val) {
				if(isset($val['name_img'])){
					$imgPrefix = $this->imagesComponent->prefix();
					$fetchConfig = $this->imagesComponent->getConfigItems(array(
						'module_img'=>'catalog',
						'attribute_img'=>'category'
					));
					foreach ($fetchConfig as $key => $value) {
						$product[$item]['imgSrc'][$value['type_img']] = '/upload/catalog/p/'.$val['id_product'].'/'.$imgPrefix[$value['type_img']] . $val['name_img'];
					}
				}else{
					$product[$item]['imgSrc']['default'] = '/skin/'.$this->template->themeSelected().'/img/catalog/p/default.png';
				}

                $product[$item]['url'] = $this->routingUrl->getBuildUrl(array(
                        'type' => 'product',
                        'iso' => $val['iso_lang'],
                        'id' => $val['id_product'],
                        'url' => $val['url_p'],
                        'id_parent'         =>  $val['id_cat'],
                        'url_parent'        =>  $val['url_cat']
                    )
                );
            }
            $this->template->assign('product_cart',$product);
            $this->template->display('cartpay/index.tpl');
        }
        else {
            $key_cart = $this->getItems('session',array('session_key_cart' => $this->session_key_cart),'one',false);
            switch ($this->action) {
                case 'add':
                    if(isset($this->cart)) {
                        if ($key_cart != null) {
                            $this->addToCart(
                                array(
                                    'id_cart' => $key_cart['id_cart'],
                                    'id_product' => $this->cart['id_product'],
                                    'quantity' => $this->cart['quantity']
                                )
                            );
                        }
                    }
                    break;
                case 'edit':

                    break;
                case 'send':
                    $account = $this->getItems('account',$key_cart['id_account'],'one',false);

                    if($account != null){
                        if($this->send_email($account['email_ac'],'quotation')){
                            $this->add(array(
                                'type' => 'quotation',
                                'data' => array(
                                    'id_cart' => $key_cart['id_cart']
                                )
                            ));
                            $this->upd(array(
                                'type' => 'status',
                                'data' => array(
                                    'id' => $key_cart['id_cart'],
                                    'tc' => 1
                                )
                            ));
                            $this->session->close('mc_cartpay');
                            $this->session->start('mc_cartpay');
                            $this->message->json_post_response(true,'update',null,array('template' => 'cartpay/message.tpl'));
                        }
                    }

                    break;
            }
        }
    }
}
?>