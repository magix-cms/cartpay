<?php
require_once('db.php');
require_once('Cart.php');
/*
 # -- BEGIN LICENSE BLOCK ----------------------------------
 #
 # This file is part of MAGIX CMS.
 # MAGIX CMS, The content management system optimized for users
 # Copyright (C) 2008 - 2020 magix-cms.com <support@magix-cms.com>
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
 * @category cartpay
 * @package plugins
 * @copyright MAGIX CMS Copyright (c) 2008 - 2020 Gerits Aurelien,
 * http://www.magix-cms.com,  http://www.magix-cjquery.com
 * @license Dual licensed under the MIT or GPL Version 3 licenses.
 * @version 3.0
 * Author: Gerits Aurelien
 * Contributor Salvatore Di Salvo
 */
class plugins_cartpay_public extends plugins_cartpay_db {
	/**
	 * @var object
	 */
	protected $template,
		$data,
		$header,
		$message,
		$lang,
		$settings,
		$settingComp,
		$module,
		$mods,
		$sanitize,
		$modelDomain,
		$mail,
		$routingUrl,
		$clean,
		$cart;

	/**
     * @var array $config
     */
    private $config;

	/**
     * Session var
     * @var string $session_key_cart
     */
    private
		$id_account = null,
		$session_key_cart,
		$current_cart,
		$steps = [
			'quotation' => [
				1 => ['step' => 'info_step'],
				2 => ['step' => 'done_step']
			],
			'order' => [
				1 => ['step' => 'info_step'],
				2 => ['step' => 'billing_step'],
				3 => ['step' => 'payment_method_step'],
				4 => ['step' => 'confirmation_step'],
				5 => ['step' => 'done_step']
			]
		];

    /**
     * Les variables globales
     * @var string $action
     */
    public $action = '';

    /**
     * Les variables plugin
     * @var int $id_product
     * @var int $quantity
     * @var array $coor
     * @var array $billing
     * @var string $payment_method
     * @var array $purchase
     * @var array $custom
     * @var array $done
     */
    public
		$id_product,
		$quantity,
		$coor,
		$billing,
		$payment_method,
		$step,
		$purchase,
		$custom,
		$status,
		$done;

    /**
     * plugins_cartpay_public constructor.
     * @param null|frontend_model_template $t
     */
    public function __construct($t = null) {
        $this->template = $t instanceof frontend_model_template ? $t : new frontend_model_template();
		$this->lang = $this->template->lang;
        $this->data = new frontend_model_data($this,$this->template);
        $this->settingComp = new component_collections_setting();
        $this->settings = $this->settingComp->getSetting();
        $this->message = new component_core_message($this->template);
        $this->clean = new form_inputEscape();
        $this->startSession();
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

	/**
	 * Load modules attached to cartpay
	 */
	private function loadModules() {
		if(!isset($this->module)) $this->module = new frontend_model_module();
		if(!isset($this->mods)) $this->mods = $this->module->load_module('cartpay');
    }

	// --- Session
	/**
	 * Start cart session
	 */
	private function startSession() {
		// Get id account if exists
		$this->loadModules();
		if(isset($this->mods['account']) && method_exists($this->mods['account'],'accountData')){
			$account = $this->mods['account']->accountData();
			$this->id_account = $account['id_account'];

            // If id account is set, look for a abandonned cart for this account
            if($this->id_account) $account_cart = $this->getItems('account_session',$this->id_account,'one',false);
		}

        // Start cart session
        $this->cart = new Cart('mc_cart');

        // Get the key of the cart
        $this->session_key_cart = $this->cart->getKey();

        // Check if cart with this key exists in db
        $this->current_cart = $this->getItems('session',['session_key_cart' => $this->session_key_cart],'one',false);

        // Check if there is a account cart
        if(isset($account_cart)) {
            // Check if the current cart is different from the current cart
            if($this->current_cart['session_key_cart'] !== $account_cart['session_key_cart']) {
                // Get the content of the current cart
                $cart = $this->cartData();

                // - The current cart si empty so we'll replace it with the old one
                if(!$cart['nb_items']) {
                    // - create new cart session with this key and retreive items of the cart
                    $this->current_cart = $account_cart;
                    $this->cart = new Cart('mc_cart', $account_cart['session_key_cart']);
                    $this->session_key_cart = $account_cart['session_key_cart'];
                    $this->retreiveAccountCart();
                }
                else {
                    // - There is at least one product in the current cart so we assign it to the account
                    $this->upd(array(
                        'type' => 'session',
                        'data' => array(
                            'id' => $this->current_cart['id_cart'],
                            'id_account' => $this->id_account
                        )
                    ));
                    $this->current_cart['id_account'] = $this->id_account;
                }
            }
        }
        else {
            // - no account cart was found
            // Check if there is a current cart
            if(!$this->current_cart) {
                // - no cart was found so we create new cart session
                $this->openSession($this->session_key_cart, $this->id_account);
                $this->current_cart = $this->getItems('session',['session_key_cart' => $this->session_key_cart],'one',false);
            }
            elseif(empty($this->current_cart['id_account']) && isset($this->id_account) && !empty($this->id_account)){
                // - cart was found but not assign to the current account, update the session to save into account
                $this->upd(array(
                    'type' => 'session',
                    'data' => array(
                        'id' => $this->current_cart['id_cart'],
                        'id_account' => $this->id_account
                    )
                ));
                $this->current_cart['id_account'] = $this->id_account;
            }
            elseif($this->current_cart['id_account'] && !$this->id_account) {
                // - cart was found but assign to an account, empty the cart and start a new one
                $this->cart->emptyCart();
                $this->cart = new Cart('mc_cart');
                // Get the new key of the cart
                $this->session_key_cart = $this->cart->getKey();
                // Get the new cart from the db
                $this->current_cart = $this->getItems('session',['session_key_cart' => $this->session_key_cart],'one',false);
            }
        }
	}

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
    // --------------------

    // --- Database actions
    /**
     * Insert data
     * @param array $config
     */
    private function add($config) {
        switch ($config['type']) {
            case 'order':
            case 'buyer':
            case 'billing':
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
    private function upd(array $config) {
        switch ($config['type']) {
            case 'order_step':
            case 'quotation_step':
            case 'order_info':
            case 'quotation_info':
            case 'order_payment':
            case 'buyer':
            case 'cart_account':
            case 'billing':
            case 'session':
            case 'product':
            case 'status_order':
            case 'status':
                parent::update(
                    ['type' => $config['type']],
                    $config['data']
                );
                break;
        }
    }

    /**
     * Insert data
     * @param array $config
     */
    private function del($config) {
        switch ($config['type']) {
            case 'product':
                parent::delete(
                    array('type' => $config['type']),
                    $config['data']
                );
                break;
        }
    }
	// --------------------

	// --- Cartpay extendable actions
	/**
	 * Get available payment method names
	 * @return array
	 */
	public function getPaymentMethodAvailable() {
		$this->loadModules();

		$pma = [];
		if($this->config['bank_wire']) {
			$pma['bank_wire'] = [
				'value' => 'bank_wire',
				'icon' => $this->template->getConfigVars('icon_bank_wire'),
				'name' => $this->template->getConfigVars('title_bank_wire'),
				'desc' => $this->template->getConfigVars('txt_bank_wire')
			];
		}

		if(!empty($this->mods)) {
			foreach ($this->mods as $name => $mod){
				if(property_exists($mod,'payment_plugin') && $mod->payment_plugin === true) {
					$this->template->addConfigFile(
						[component_core_system::basePath().'/plugins/'.$name.'/i18n/'],
						['public_local_'],
						false
					);
					$this->template->configLoad();
					$pma[$name] = [
						'value' => $name,
						'icon' => $this->template->getConfigVars('icon_'.$name),
						'img' => http_url::getUrl().'/plugins/'.$name.'/img/'.$this->template->getConfigVars('img_'.$name),
						'name' => $this->template->getConfigVars('title_'.$name),
						'desc' => $this->template->getConfigVars('txt_'.$name)
					];
				}
			}
		}

		return $pma;
	}

	/**
	 * @param array $params
	 * @return float
	 */
	private function getProductPrice($params) {
		$this->loadModules();

		$pPrice = $this->getItems('product_price',$params['id_product'],'one',false);
		$unit_price = $pPrice['price_p'];

		if(!empty($this->mods)) {
			foreach ($this->mods as $name => $mod){
				if(method_exists($mod,'impact_unit_price')) {
					$unit_price = $mod->impact_unit_price($params);
				}
			}
		}

		return $unit_price;
	}

	/**
	 * @param array $params
	 * @return float
	 */
	private function getProductVatRate($params) {
		$this->loadModules();

		$vat_rate = $this->settings['vat_rate']['value'];

		if(!empty($this->mods)) {
			foreach ($this->mods as $name => $mod){
				if(method_exists($mod,'impact_product_vat_rate')) {
					$vat_rate = $mod->impact_product_vat_rate($params);
				}
			}
		}

		return $vat_rate;
	}

	/**
	 * @param $type
	 * @return array|bool|mixed
	 */
	private function getSteps($type) {
		if(!in_array($type,['quotation','order'])) return false;

		$this->loadModules();
		$steps = $this->steps[$type];
		$method = null;

		switch ($type) {
			case 'quotation':
				$method = 'setQuotationStep';
				break;
			case 'order':
				$method = 'setOrderStep';
				break;
		}

		if(!empty($this->mods) && isset($method)) {
			foreach ($this->mods as $name => $mod){
				if(method_exists($mod,$method)) {
					try {
						$reflectionMethod = new ReflectionMethod($mod, $method);
					}
					catch(ReflectionException $e) {
						$logger = new debug_logger();
						$logger->log('php','error','Reflection Exception : '.$e->getMessage(),debug_logger::LOG_MONTH);
						return false;
					}

					/**
					 * This
					 * [
					 * 	'step' => 'My step',
					 * 	'pos' => 5,
					 * 	'mod' => plugin_name
					 * ]
					 * will insert the 'My step' step at the fifth position in the step order
					 */
					$mod_step = $reflectionMethod->invoke($mod);

					if(is_array($mod_step) && key_exists('step',$mod_step) && key_exists('pos',$mod_step) && key_exists('mod',$mod_step)) {
						$this->template->addConfigFile(
							array(component_core_system::basePath().'/plugins/'.$name.'/i18n/'),
							array('public_local_'),
							false
						);
						$this->template->configLoad();
						$newsteps = [];
						$i = 1;
						while(count($steps)) {
							$newsteps[$i] = ($i === $mod_step['pos']) ? $mod_step : array_shift($steps);
							$i++;
						}
						$steps = $newsteps;
					}
				}
			}
		}

		return $steps;
	}

	/**
	 * @param object $mod
	 * @param string $action
	 */
	private function exeStepAction($mod, $action) {
		if(method_exists($mod,$action)) {
			try {
				$reflectionMethod = new ReflectionMethod($mod, $action);
			}
			catch(ReflectionException $e) {
				$logger = new debug_logger();
				$logger->log('php','error','Reflection Exception : '.$e->getMessage(),debug_logger::LOG_MONTH);
				return false;
			}
			$reflectionMethod->invoke($mod, $this->current_cart);
		}
	}

    private function getAdditionnalResume(){
        $this->loadModules();

        $arb = [];

        if(!empty($this->mods)) {
            foreach ($this->mods as $name => $mod){
                if(method_exists($mod,'orderResumeInfos')) {
                    $arb[] = $mod->orderResumeInfos($this->current_cart);
                }
            }
        }

        return $arb;
    }
	// --------------------

	// --- Cartpay actions
	/**
	 * Get account cart item and place them into cart
	 */
	private function retreiveAccountCart() {
		if(isset($this->current_cart['id_cart'])) {
			$items = $this->getItems('account_cart_items',$this->current_cart['id_cart'],'all',false);
			foreach ($items as $item) {
			    $product = $item['id_product'];
			    $quantity = $item['quantity'];

                // Get the product unit price
                $unit_price = $this->getProductPrice([
                    'id_product' => $product,
                    'quantity' => $quantity,
                    'id_account' => $this->id_account
                ]);

                // Get the product vat rate
                $pVat = $this->getProductVatRate([
                    'id_product' => $product,
                    'quantity' => $quantity,
                    'id_account' => $this->id_account
                ]);

                // Insert into cart
                $this->cart->addItem($product, $quantity, $unit_price ,$pVat);
			}
		}
	}

	/**
	 * @param $cart
	 * @param $product
	 * @param $quantity
	 * @param $return
	 */
    private function addToCart($cart, $product, $quantity){
    	// Check if the product is already in the cart
    	$inCart = $this->cart->inCart($product);

		// Get the product unit price
		$unit_price = $this->getProductPrice([
			'id_product' => $product,
			'quantity' => $quantity,
			'id_account' => $this->id_account
		]);

		// Get the product vat rate
		$pVat = $this->getProductVatRate([
			'id_product' => $product,
			'quantity' => $quantity,
			'id_account' => $this->id_account
		]);

		// Insert into cart
        $item = $this->cart->addItem($product, $quantity, $unit_price ,$pVat);

		$conf = [
			'type' => 'product',
			'data' => [
				'id_cart' => $cart,
				'id_product' => $product,
				'quantity' => $item['q']
			]
		];
		// if the product is already in the cart : Yes - update quantity | No - insert in db
		$inCart ? $this->upd($conf) : $this->add($conf);

        // Get full cart info
		$cart = $this->cartData();

		$html = null;
		// If the item wasn't in the cart, prepare the line to be inserted in the float cart
		if(!$inCart){
			$this->template->assign('setting',$this->settings);
			$this->template->assign('data',[$cart['items'][$product]]);
			$html = $this->template->fetch('cartpay/loop/float-cart-item.tpl');
		}

		$price_display = $this->settings['price_display']['value'];

		// Return a JSON object with the cart changes
        $this->message->json_post_response(true,null,[
            'result' => $html,
            'extend' => [
                'id' => $product,
                'nb' => $conf['data']['quantity'],
                'nb_items' => $cart['nb_items'],
                'total' => $price_display === 'tinc' ? $cart['total']['inc'] : $cart['total']['exc']
            ]
        ]);
    }

	/**
	 * @param $cart
	 * @param $product
	 * @param $quantity
	 */
    private function updCart($cart, $product, $quantity){
    	// Update in db
		if($quantity > 0){
			$this->upd([
				'type' => 'product',
				'data' => [
					'id_cart' => $cart,
					'id_product' => $product,
					'quantity' => $quantity
				]
			]);

			// Get the product unit price
			$unit_price = $this->getProductPrice([
				'id_product' => $product,
				'quantity' => $quantity,
				'id_account' => $this->id_account
			]);

			// Get the product vat rate
			$pVat = $this->getProductVatRate([
				'id_product' => $product,
				'quantity' => $quantity,
				'id_account' => $this->id_account
			]);
		}
		else {
			$this->del([
				'type' => 'product',
				'data' => [
					'id_cart' => $cart,
					'id_product' => $product
				]
			]);
			$unit_price = null;
			$pVat = null;
		}

		// Update item in cart
		$item = $this->cart->updItem($product,$quantity,$unit_price,$pVat);

		// Get full cart info
		$cart = $this->cartData();

		$product_tot = 0;
		$price_display = $this->settings['price_display']['value'];
		if($quantity > 0) $product_tot = $item['unit_price'] * $item['q'] * ($price_display === 'tinc' ? 1 + ($item['vat']/100) : 1);

        $total = [
        	'tot' => $price_display === 'tinc' ? $cart['total']['inc'] : $cart['total']['exc'],
        	'exc' => $cart['total']['exc'],
        	'inc' => $cart['total']['inc'],
        	'vat' => $cart['total']['vat'],
		];

        $this->message->json_post_response(true,null,[
        	'result' => null,
			'extend' => [
				'id' => $product,
				'nb' => $quantity,
				'product_tot' => $product_tot,
				'nb_items' => $cart['nb_items'],
				'total' => $total
			]
		]);
    }

    /**
     *
     */
    public function cartData() {
		$cart = $this->cart->getCartData();

        if(isset($this->session_key_cart)) {
            $key_cart = $this->current_cart;
			$langData = $this->getItems('idFromIso',['iso' => $this->lang],'one',false);
            $cart_items = $this->getItems('catalog',['id' => $key_cart['id_cart'],'default_lang' => $langData['id_lang']],'all',false);

			if(!empty($cart_items)) {
				$mc = new frontend_model_catalog($this->template);
				$ms = new frontend_model_core();
				$current = $ms->setCurrentId();

				foreach ($cart_items as $item) {
					$product = $mc->setItemData($item,$current);
					$itemDetails = $cart['items'][$product['id']];
					$rate = 1 + $itemDetails['vat']/100;
					$product['unit_price'] = round($itemDetails['unit_price'], 2);
					$product['unit_price_inc'] = round($itemDetails['unit_price'] * $rate, 2);
					$product['total'] = round($itemDetails['unit_price'] * $itemDetails['q'], 2);
					$product['total_inc'] = round($itemDetails['unit_price'] * $itemDetails['q'] * $rate, 2);
					$cart['items'][$product['id']] = array_merge($itemDetails,$product);
				}

				$cart['total']['exc'] = round($cart['total']['exc'], 2);
				$cart['total']['inc'] = round($cart['total']['inc'], 2);
				foreach ($cart['total']['vat'] as $key => $val) {
					$cart['total']['vat'][$key] = round($val,2);
				}
			}
        }

		return $cart;
    }

	/**
	 * Return cartpay config
	 * @return mixed
	 */
	public function getConfig() {
		return $this->getItems('config',NULL,'one',false);
    }
	// --------------------

    // --- Payment plugin info
	/**
	 * @param string $type order|quotation
	 * @param string $step
     * @return string url
	 */
	private function setStepUrl($type, $step) {
		$url = http_url::getUrl().'/'.$this->template->lang.'/cartpay/'.$type.'/?step='.$step;
		$this->template->assign('next_step_url',$url);
		return $url;
	}

	/**
	 * @param $type
	 * @param $data
	 * @return array
	 */
	private function setItemsAccount($type,$data) {
		$newArr = array();
		if(!empty($data)) {
			switch($type){
				case 'account':
					$newArr['id'] = $data['id_account'];
					$newArr['email'] = $data['email_ac'];
					$newArr['lastname'] = $data['lastname_ac'];
					$newArr['firstname'] = $data['firstname_ac'];
					$newArr['phone'] = $data['phone_ac'];
					$newArr['company'] = $data['company_ac'];
					$newArr['vat'] = $data['vat_ac'];
					$newArr['address'] = $data['street_address'];
					$newArr['postcode'] = $data['postcode_address'];
					$newArr['city'] = $data['city_address'];
					$newArr['country'] = $data['country_address'];
					$newArr['type_buyer'] = $type;
					break;
				case 'buyer':
					$newArr['id'] = $data['id_buyer'];
					$newArr['email'] = $data['email_buyer'];
					$newArr['lastname'] = $data['lastname_buyer'];
					$newArr['firstname'] = $data['firstname_buyer'];
					$newArr['phone'] = $data['phone_buyer'];
					$newArr['company'] = $data['company_buyer'];
					$newArr['vat'] = $data['vat_buyer'];
					$newArr['address'] = $data['street_billing'];
					$newArr['postcode'] = $data['postcode_billing'];
					$newArr['city'] = $data['city_billing'];
					$newArr['country'] = $data['country_billing'];
					$newArr['type_buyer'] = $type;
					break;
			}
		}
		return $newArr;
	}

	/**
	 * @param $cart
	 * @return array
	 */
	private function getbuyerInfo($cart) {
		if(!empty($this->mods) && isset($this->mods['account']) && isset($this->id_account)) {
			$account = $this->mods['account'];
			$type = 'account';
			$buyerData = $account->accountData();
		}
		else {
			$type = 'buyer';
			$buyerData = $this->getItems('buyer', $cart['id_cart'], 'one', false);
		}
		return $this->setItemsAccount($type, $buyerData);
	}

	/**
	 * @param string $action
	 * @param array $data
	 * @param string $step
	 */
	private function saveRecord($action,$data,$step) {
		$cart = $this->cartData();
		$conf = ['type' => $action];
		if($action === 'order') {
			$conf['data'] = [
				'id_cart' => $data['id_cart'],
				'step_order' => $step,
				'amount_order' => $cart['total']['inc'],
				'currency_order' => 'EUR',
				'transaction_id' => 'pending',
				'payment_order' => 'pending',
				'status_order' => 'pending'
			];
		}
		elseif($action === 'quotation') {
			$conf['data'] = [
				'id_cart' => $data['id_cart'],
				'step_quotation' => $step,
				'amount_quotation' => $cart['total']['inc'],
				'currency_quotation' => 'EUR'
			];
		}
		$this->add($conf);
	}

	/**
	 * @param $cart
	 * @param $data
	 */
	private function saveBuyer($cart, $data) {
		$conf = [
			'type' => 'buyer',
			'data' => [
				'firstname_buyer' => $data['firstname'],
				'lastname_buyer' => $data['lastname'],
				'email_buyer' => $data['email'],
				'phone_buyer' => empty($data['phone']) ? null : $data['phone'],
				'company_buyer' => empty($data['company']) ? null : $data['company'],
				'vat_buyer' => empty($data['vat']) ? null : $data['vat']
			]
		];

		if(!empty($cart['id_buyer'])) {
			$conf['data']['id'] = $cart['id_buyer'];
			$this->upd($conf);
		}
		else {
			$conf['data']['id_cart'] = $cart['id_cart'];
			$this->add($conf);

			if(isset($data['type']) && $data['type'] === 'account') {
				$this->upd([
					'type' => 'cart_account',
					'data' => [
						'id_cart' => $cart['id_cart'],
						'id_account' => $data['id']
					]
				]);
			}
		}
	}

	/**
	 * @param $cart
	 * @param $data
	 */
	private function saveBilling($data) {
		$conf = [
			'type' => 'billing',
			'data' => [
				'id' => $data['id_buyer'],
				'street_billing' => $data['address'],
				'postcode_billing' => $data['postcode'],
				'city_billing' => $data['city'],
				'country_billing' => $data['country']
			]
		];
		$this->upd($conf);
	}

	/**
	 * @param string $action
	 * @param string $type
	 * @return array
	 */
	private function getDoneStepStatus($action, $type)
	{
		$status = [
			'success' => [
				'title' => $this->template->getConfigVars($action.'_success'),
				'msg' => $this->template->getConfigVars($action.'_success_msg'),
				'status' => 'success',
				'error' => false,
				'icon' => 'check'
			],
			'pending' => [
				'title' => $this->template->getConfigVars($action.'_pending'),
				'msg' => $this->template->getConfigVars($action.'_pending_msg'),
				'status' => 'warning',
				'error' => false,
				'icon' => 'warning'
			],
			'canceled' => [
				'title' => $this->template->getConfigVars($action.'_canceled'),
				'msg' => $this->template->getConfigVars($action.'_canceled_msg'),
				'status' => 'warning',
				'error' => true,
				'icon' => 'warning'
			],
			'error' => [
				'title' => $this->template->getConfigVars($action.'_error'),
				'msg' => $this->template->getConfigVars($action.'_error_msg'),
				'status' => 'error',
				'error' => true,
				'icon' => 'warning'
			]
		];
		return $status[$type];
	}
	// --------------------

	// --- Mail
	/**
	 * Retourne le message de notification
	 * @param $type
	 * @param null $subContent
	 * @return string
	 */
	private function setNotify($type,$subContent=null){
		$this->template->configLoad();
		switch($type){
			case 'warning':
				$warning = array(
					'empty' =>  $this->template->getConfigVars('fields_empty'),
					'mail'  =>  $this->template->getConfigVars('mail_format')
				);
				$message = $warning[$subContent];
				break;
			case 'success':
				$message = $this->template->getConfigVars('message_send_success');
				break;
			case 'error':
				$error = array(
					'installed'   =>  $this->template->getConfigVars('installed'),
					'configured'  =>  $this->template->getConfigVars('configured')
				);
				$message = sprintf('plugin_error','contact',$error[$subContent]);
				break;
		}

		return array(
			'type'      => $type,
			'content'   => $message
		);
	}

	/**
	 * getNotify
	 * @param $type
	 * @param null $subContent
	 */
	private function getNotify($type,$subContent=null) {
		$this->template->assign('message',$this->setNotify($type,$subContent));
		$this->template->display('contact/notify/message.tpl');
	}

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
     * @throws Exception
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
        if($key_cart['id_account'] != NULL) {

            $newData = $this->getItems('account', $key_cart['id_account'], 'one', false);
            $account = $this->setItemsAccount('account', $newData);

        }elseif($key_cart['id_buyer'] != NULL) {

            $newData = $this->getItems('buyer', $key_cart['id_buyer'], 'one', false);
            $account = $this->setItemsAccount('buyer', $newData);

        }
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
    protected function send_email($email, $tpl, $buyer, $record) {
        if($email) {
            $this->template->configLoad();
			$this->sanitize = new filter_sanitize();

            if(!$this->sanitize->mail($email)) {
				$this->getNotify('warning','mail');
            }
            else {
				/*$this->mail = new frontend_model_mail($this->template,'cartpay','smtp',[
					'setHost'		=> 'web-solution-way.com',
					'setPort'		=> 25,
					'setEncryption'	=> '',
					'setUsername'	=> 'server@web-solution-way.com',
					'setPassword'	=> 'Wsw123/*'
				]);*/
                $this->mail = new frontend_model_mail($this->template,'cartpay');
				$this->modelDomain = new frontend_model_domain($this->template);
				$this->routingUrl = new component_routing_url();
				$contact = new plugins_contact_public();
				$allowed_hosts = array_map(function($dom) { return $dom['url_domain']; },$this->modelDomain->getValidDomains());
				if (!isset($_SERVER['HTTP_HOST']) || !in_array($_SERVER['HTTP_HOST'], $allowed_hosts)) {
					header($_SERVER['SERVER_PROTOCOL'].' 400 Bad Request');
					exit;
				}

				$noreply = 'noreply@'.str_replace('www.','',$_SERVER['HTTP_HOST']);

				$data = [
					'type' => $tpl,
					'buyer' => $buyer,
					'record' => $record,
					'cart' => $this->cartData(),
					'pma' => $this->getPaymentMethodAvailable(),
					'config' => $this->getConfig()
				];

				if(!empty($data)) {
					$contacts = $contact->getContact();
					//$from = $contact->getSender();
                    $mail_settings = new frontend_model_setting($this->template);
                    $from = $mail_settings->getSetting('mail_sender');
                    $file = null;
					if($contacts) {
						//Initialisation du contenu du message
						$send = false;
						foreach ($contacts as $recipient) {
							$isSend = $this->mail->send_email(
								$recipient['mail_contact'],
								$tpl,
								$data,
								'',
								$buyer['email'],
                                $from['value'],//$from['mail_sender'],
                                $file
							);
						}
						//$this->getNotify($send ? 'success' : 'error');
					}
					else {
						//$this->getNotify('error','configured');
					}

					//Initialisation du contenu du message
					//$send = false;
					$isSend = $this->mail->send_email(
						$buyer['email'],
						$tpl,
						$data,
						'',
						$noreply,
                        $from['value'],//$from['mail_sender'],
                        $file
					);
					//if(!$send) $send = $isSend;
					//$this->getNotify($send ? 'success' : 'error');

				}
				else {
					$this->message->json_post_response(false,'error_config');
					return false;
				}
            }
        }
    }
    // --------------------

    public function run() {
		$key_cart = $this->current_cart;

		if(isset($key_cart) && is_array($key_cart) && !empty($key_cart)) {
			if (http_request::isGet('action')) $this->action = $this->clean->simpleClean($_GET['action']);

			$this->config = $this->getConfig();
			$pma = $this->getPaymentMethodAvailable();
			$this->template->assign('available_payment_methods', $pma);
			$breadplugin = [];
			$breadplugin[] = ['name' => $this->template->getConfigVars('my_cart')];

			if($this->action) {
				switch ($this->action) {
					case 'add':
					case 'edit':
						if (http_request::isPost('id_product')) $this->id_product = (int)$this->clean->numeric($_POST['id_product']);
						if (http_request::isPost('quantity')) $this->quantity = (int)$this->clean->numeric($_POST['quantity']);

						if(isset($this->id_product) && isset($this->quantity)) {
							$this->action === 'add' ? $this->addToCart($key_cart['id_cart'], $this->id_product, $this->quantity) : $this->updCart($key_cart['id_cart'], $this->id_product, $this->quantity);
						}
						break;
					case 'quotation':
					case 'order':
						if(!function_exists("array_key_last")) {
							function array_key_last($array) {
								if (!is_array($array) || empty($array)) return NULL;
								return array_keys($array)[count($array)-1];
							}
						}
						if(!function_exists('getStepIndex')) {
							function getStepIndex($steps, $name) {
								$index = 0;
								foreach ($steps as $key => $step) {
									if($step['step'] === $name) {
										$index = $key;
										break;
									}
								}
								return $index;
							}
						}

						// Get the step the user is trying to access
						if(http_request::isGet('step')) $this->step = $this->clean->simpleClean($_GET['step']);

						// Get the total number of product in the cart
						$cartProducts = $this->getItems('countProduct',['id' => $key_cart['id_cart']],'one',false);

						// Set breadcrumb information
						$breadplugin[0]['url'] = http_url::getUrl().'/'.$this->template->lang.'/cartpay/';
						$breadplugin[0]['title'] = $this->template->getConfigVars('my_cart');
						$breadplugin[] = ['name' => $this->template->getConfigVars($this->action)];
						$this->template->assign('breadplugin', $breadplugin);

						// Set action type directory
						$dir = 'cartpay/'.$this->action.'/';
						// Set default template to display
						$temp = 'access-disallow';
						// if the action type is enabled and there is at least one product in the cart
						if($this->config[$this->action.'_enabled'] && $cartProducts['nbr'] > 0) {
							// Get valid steps for the type action
							$steps = $this->getSteps($this->action);
							$this->template->assign('steps',$steps);

							// If there is at least one step defined
							if(is_array($steps) && !empty($steps)) {
								// Set action titles
								$this->template->assign('type_title',$this->template->getConfigVars($this->action));
								$this->template->assign('type',$this->action);

								// Set available payment methods
								if($this->action === 'order') $this->template->assign('pma', $pma);

								// Get the record associated for the type action
								$record = $this->getItems($this->action,['id_cart' => $key_cart['id_cart']],'one',false);

								// If no record is found, create one
								if(!$record) {
									$this->saveRecord($this->action,$key_cart,$this->step);
									$record = $this->getItems($this->action,['id_cart' => $key_cart['id_cart']],'one',false);
								}

								// Get the index of the step the user try to access
								$current_step = getStepIndex($steps,$this->step);

								// If there is a record, a step and the step is valid
								if(is_array($record) && !empty($record) && isset($this->step) && $current_step) {
									// Get the index of the last step accessed
									$record_step = getStepIndex($steps,$record['step_'.$this->action]);

									// If the current step is the logical next step, update the record step
									if($current_step === $record_step + 1) {
										$this->upd([
											'type' => $this->action.'_step',
											'data' => [
												'step' => $steps[$current_step]['step'],
												'id' => $record['id_'.$this->action]
											]
										]);
										$record_step++;
									}

									// If the current step is a previous step compare to the recorded step or there is no recorded step
									if(($current_step <= $record_step || $record['step_'.$this->action] === NULL) && isset($steps[$current_step])) {
										// Set current step infos
										$this->template->assign('current_step',[
											'step' => $steps[$current_step]['step'],
											'pos' => $current_step
										]);
										// Set next step url
										if($current_step !== array_key_last($steps)) $this->setStepUrl($this->action,$steps[$current_step+1]['step']);

										// --- Process post data
										// If the previous step is not a plugin or the action type is order and the current step is the one before last or the last
										if(!isset($steps[$current_step - 1]['mod'])
											|| ($this->action === 'order' && $current_step >= array_key_last($steps) - 1)) {

											// Get the contact information given by the user
											if (http_request::isPost('coor')) $this->coor = (array)$this->clean->arrayClean($_POST['coor']);
											// Get the billing information given by the user
											if (http_request::isPost('billing')) $this->billing = (array)$this->clean->arrayClean($_POST['billing']);
											// Get the payment method chosen by the user
											if (http_request::isPost('payment_method')) $this->payment_method = $this->clean->simpleClean($_POST['payment_method']);
											// Get the purchased information
											if (http_request::isPost('purchase')) $this->purchase = (array)$this->clean->arrayClean($_POST['purchase']);
											if (http_request::isPost('custom')) $this->custom = (array)$this->clean->arrayClean($_POST['custom']);

											// If the contact information had been given
											if(isset($this->coor)) {
												// Step 1
												$this->saveBuyer($key_cart,$this->coor);
												$this->template->assign('buyer', $this->getbuyerInfo($key_cart));
												if(isset($this->coor['info']) && !empty(isset($this->coor['info']))) {
                                                    $this->upd([
                                                        'type' => $this->action.'_info',
                                                        'data' => [
                                                            'info' => $this->coor['info'],
                                                            'id' => $record['id_'.$this->action]
                                                        ]
                                                    ]);
                                                }
											}
											// If the billing information had been given
											if(isset($this->billing)) {
												// step 2
												$this->saveBilling($this->billing);
											}
											// If the payment method had been chosen
											if(isset($this->payment_method)) {
												// step 3
												$this->upd([
													'type' => 'order_payment',
													'data' => [
														'id' => $record['id_order'],
														'payment_order' => $this->payment_method
													]
												]);
												$record['payment_order'] = $this->payment_method;
												$this->template->assign('order',$record);
											}
										}
										else {
											$temp = null;
											$this->exeStepAction($steps[$current_step-1]['mod'],$steps[$current_step-1]['process']);
										}

										// Get the buyer information
										$buyer = $this->getbuyerInfo($key_cart);
										if(!empty($buyer)) {
										    $buyer['info'] = $record['info_'.$this->action];
                                            $this->template->assign('buyer', $buyer);
                                        }

										// --- Order confirmation page specific process
										if($this->action === 'order' && $current_step === array_key_last($steps) - 1) {
											$temp = $steps[$current_step]['step'];

                                            $this->template->assign('additionnalResume', $this->getAdditionnalResume());

											// If the payment method chosen is a payment plugin we change the next step url
											if($record['payment_order'] !== 'bank_wire') {
												$url = http_url::getUrl().'/'.$this->template->lang.'/'.$record['payment_order'].'/';
												$this->template->assign('next_step_url',$url);
											}
										}
										// --- Last step specific process
										elseif($current_step === array_key_last($steps)) {
											$temp = $steps[$current_step]['step'];
											// If passed, get the status of the process
											if(http_request::isGet('status')) $this->status = $this->clean->simpleClean($_GET['status']);

											// If no status has been passed
											if(!isset($this->status)) {
												if($this->action === 'order') {
													// If the action type is order and the purchased information had been given and the method payment is bank wire
													if(isset($this->purchase['amount'])
														&& isset($this->purchase['email'])
														&& $record['payment_order'] === 'bank_wire') {
														$this->status = 'pending';
													}
												}
												elseif($this->action === 'quotation') {
													$this->status = 'success';
												}
											}


                                            if(isset($this->status)) {
                                                if($this->action === 'order') {
                                                    $this->loadModules();
                                                    if($record['payment_order'] !== 'bank_wire' && isset($this->mods[$record['payment_order']])){
                                                        $log = new debug_logger(MP_LOG_DIR);
                                                        $log->tracelog('start payment');
                                                        $log->tracelog(json_encode(array('id'=>$record['id_order'],'payment_status'=>$this->mods[$record['payment_order']]->getPaymentStatus())));
                                                        //$log->tracelog('sleep');

                                                        if(method_exists($this->mods[$record['payment_order']],'getPaymentStatus')){
                                                            //$log->tracelog('start payment');
                                                            $log->tracelog(json_encode(array(
                                                                'id' => $record['id_order'],
                                                                'status_order' => $this->mods[$record['payment_order']]->getPaymentStatus(),
                                                                'tc' => 1
                                                            )));

                                                            $log->tracelog('sleep');

                                                            $this->upd([
                                                                'type' => 'status_order',
                                                                'data' => [
                                                                    'id' => $this->current_cart['id_cart'],
                                                                    'status_order' => $this->mods[$record['payment_order']]->getPaymentStatus()
                                                                ]
                                                            ]);
                                                            if($this->mods[$record['payment_order']]->getPaymentStatus() === "paid") {
                                                                $this->upd([
                                                                    'type' => 'status',
                                                                    'data' => [
                                                                        'id' => $this->current_cart['id_cart'],
                                                                        'tc' => 1
                                                                    ]
                                                                ]);
                                                                $this->cart->emptyCart();
                                                            }
                                                        }
                                                    }
                                                }
                                            }
											// Set the done step data based on the action type and the status of the process
											$this->done = $this->getDoneStepStatus($this->action, $this->status);
											$this->template->assign('done',$this->done);

											// If payment method method is bank wire or the transaction has been succeed
											if(isset($this->done) && !$this->done['error']) $this->send_email($buyer['email'],$this->action,$buyer,$record);
										}
										// --- Step before the one before the last
										elseif($current_step < array_key_last($steps)) {
											// If the step is not a mod
											if(!isset($steps[$current_step]['mod'])) {
												$temp = $steps[$current_step]['step'];
											}
											else {
												$temp = null;
												$this->exeStepAction($steps[$current_step]['mod'],$steps[$current_step]['display']);
											}
										}
									}
								}
							}
						}
						if(is_string($temp)) $this->template->display($dir.$temp.'.tpl');
					break;
				}
			}
			else {
				$this->template->assign('breadplugin', $breadplugin);
				$quotationStep = $this->getSteps('quotation');
                $this->template->assign('quotationFirstStep', $this->setStepUrl('quotation',$quotationStep[1]['step']));
				$orderStep = $this->getSteps('order');
                $this->template->assign('orderFirstStep', $this->setStepUrl('order',$orderStep[1]['step']));
				$this->template->display('cartpay/index.tpl');
			}
		}
        else {
			$this->header = new component_httpUtils_header($this->template);
			$this->template->assign('getTitleHeader', $this->header->getTitleHeader(404), true);
			$this->template->assign('getTxtHeader', $this->header->getTxtHeader(404), true);
			$this->template->assign('error_code', 404, true);
			$this->template->display('error/index.tpl');
		}
    }
}