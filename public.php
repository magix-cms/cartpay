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
     * @var int|string|null $id_account
     */
    private
		$id_account = null;

	/**
     * Session var
     * @var string $session_key_cart
     */
    private $session_key_cart;

	/**
     * @var array $config
     * @var array $current_cart
     * @var array $steps
     */
    private
		$config,
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
     * @var string $action
     * @var string $step
     * @var string $status
	 * @var string $payment_method
     */
    public
		$payment_method,
		$step,
		$status,
		$action = '';

    /**
     * @var int $id_product
     * @var int $id_items
     * @var int $quantity
     */
    public
		$id_product,
		$id_items,
		$quantity;

    /**
     * @var array $param
     * @var array $coor
     * @var array $billing
     * @var array $purchase
     * @var array $custom
     * @var array $done
     */
    public
		$param,
		$coor,
		$billing,
		$purchase,
		$custom,
		$done;

    /**
     * plugins_cartpay_public constructor.
     * @param null|object|frontend_model_template $t
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
     * @param string|null $context
     * @param boolean $assign
     * @return mixed
     */
    private function getItems(string $type, $id = null, string $context = null, bool $assign = true) {
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
			$this->id_account = $account['id'];
            //print $this->id_account;
            //print_r($account);
            // If id account is set, look for a abandonned cart for this account
            if($this->id_account) $account_cart = $this->getItems('account_session',$this->id_account,'one',false);
		}

        // Start cart session
        //$this->cart = new Cart('mc_cart');
		$this->cart = Cart::getInstance();

        // Get the key of the cart
        $this->session_key_cart = $this->cart->getKey();

        // Check if cart with this key exists in db
        $session_cart = $this->getItems('session',['session_key_cart' => $this->session_key_cart],'one',false);
        //if($this->config['retreive_enabled']) {
        // Check if there is an account cart
        if(isset($this->config['retreive_enabled']) && isset($account_cart) && !empty($account_cart)) {
            
            // Check if the current cart is different from the account cart
            if($session_cart['session_key_cart'] !== $account_cart['session_key_cart']) {
                // Get the content of the current cart
                $cart = $this->cartData();

                // - The current cart si empty so we'll replace it with the old one
                if(!$cart['nb_items']) {
                    // - create new cart session with this key and retreive items of the cart
                    $session_cart = $account_cart;
					$this->cart->newCart($account_cart['session_key_cart'] ?? '');
                    $this->session_key_cart = $account_cart['session_key_cart'];
					$this->cart->openCart();
                    $this->retreiveAccountCart();
                }
                else {
                    // - There is at least one product in the current cart so we assign it to the account
                    $this->upd([
						'type' => 'session',
						'data' => [
							'id' => $session_cart['id_cart'],
							'id_account' => $this->id_account
						]
					]);
                    $session_cart['id_account'] = $this->id_account;
                }
            }
        }
        else {
            // - no account cart was found
            // Check if there is a current cart
            if(!$session_cart) {
                // - no cart was found so we create new cart session
                $this->openSession($this->session_key_cart, $this->id_account);
                $session_cart = $this->getItems('session',['session_key_cart' => $this->session_key_cart],'one',false);
            }
            elseif($session_cart['transmission_cart'] === 1){
				// - cart was found but is closed
				$this->cart->newCart(null);
				// Get the new key of the cart
				$this->session_key_cart = $this->cart->getKey();
				$this->openSession($this->session_key_cart, $this->id_account);
				// Get the new cart from the db
				$session_cart = $this->getItems('session',['session_key_cart' => $this->session_key_cart],'one',false);
			}
            elseif(empty($session_cart['id_account']) && isset($this->id_account) && !empty($this->id_account)){
                // - cart was found but not assign to the current account, update the session to save into account
                $this->upd([
					'type' => 'session',
					'data' => [
						'id' => $session_cart['id_cart'],
						'id_account' => $this->id_account
					]
				]);
                $session_cart['id_account'] = $this->id_account;
            }
            elseif($session_cart['id_account'] && !$this->id_account) {
                // - cart was found but assign to an account, empty the cart and start a new one
                $this->cart->newCart(null);
                // Get the new key of the cart
                $this->session_key_cart = $this->cart->getKey();
				$this->openSession($this->session_key_cart, $this->id_account);
                // Get the new cart from the db
                $session_cart = $this->getItems('session',['session_key_cart' => $this->session_key_cart],'one',false);
            }
        }

		$this->current_cart = $session_cart ?: [];
		$this->cart->openCart();
	}

    /**
     * Open Session
     * @param $session_key_cart
     * @param $id_account
     */
    private function openSession($session_key_cart, $id_account = null) {
        $this->add([
			'type' => 'session',
			'data' => [
				'session_key_cart' => $session_key_cart,
				'id_account' => $id_account
			]
		]);
    }
    // --------------------

    // --- Database actions
    /**
     * Insert data
     * @param array $config
     */
    private function add(array $config) {
        switch ($config['type']) {
            case 'order':
            case 'buyer':
            case 'billing':
            case 'product':
            case 'quotation':
            case 'session':
                parent::insert(
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
    private function del(array $config) {
        switch ($config['type']) {
            case 'product':
                parent::delete(
					['type' => $config['type']],
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
	public function getPaymentMethodAvailable(): array {
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
					$this->template->addConfigFile([component_core_system::basePath().'/plugins/'.$name.'/i18n/'], ['public_local_']);
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
	private function getProductPrice(array $params): float {
		$this->loadModules();

		$pPrice = $this->getItems('product_price',$params['id_product'],'one',false);
		// Promo ou pas sur le produit
        $unit_price = $pPrice['price_promo_p'] !== '0.00' ? $pPrice['price_promo_p'] : $pPrice['price_p'];

		if(!empty($this->mods)) {
			// Replace unit price
			foreach ($this->mods as $mod){
				if(method_exists($mod,'replace_unit_price')) $unit_price = $mod->replace_unit_price($params);
			}

			// Add price on price or impact unit price
			foreach ($this->mods as $mod){
                if(method_exists($mod,'impact_unit_price')) $unit_price = $unit_price + $mod->impact_unit_price($params);
			}
		}

		return $unit_price;
	}

	/**
	 * @param array $params
	 * @return float
	 */
	private function getProductVatRate(array $params): float {
		$this->loadModules();

		$vat_rate = $this->settings['vat_rate'];

		if(!empty($this->mods)) {
			foreach ($this->mods as $mod){
				if(method_exists($mod,'impact_product_vat_rate')) $vat_rate = $mod->impact_product_vat_rate($params);
			}
		}

		return $vat_rate;
	}

    /**
     * @param array $params
     */
    private function getProductParamPrice(array &$params) {
        $this->loadModules();

        if(!empty($this->mods)) {
            // Replace unit price
            foreach ($params as $mod => &$param) {
                if(isset($this->mods[$mod]) && method_exists($this->mods[$mod],'impact_price')) $param['price'] = $this->mods[$mod]->impact_price($param);
            }
        }
    }

    /**
     * @param int $id
     * @return array
     */
    private function getRetreiveParam(int $id): array {
        $this->loadModules();
        $arb = [];

        if(!empty($this->mods)) {
            foreach ($this->mods as $mod){
                if(method_exists($mod,'getRetreiveParam')) $arb = $mod->getRetreiveParam($id);
            }
        }

        return $arb;
    }

	/**
	 * @param string $type
	 * @return array|bool
	 */
	private function getSteps(string $type) {
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
					}
					catch(ReflectionException $e) {
						$logger = new debug_logger(MP_LOG_DIR);
						$logger->log('php','error','Reflection Exception : '.$e->getMessage(),debug_logger::LOG_MONTH);
						return false;
					}

					if(is_array($mod_step) && key_exists('step',$mod_step) && key_exists('pos',$mod_step) && key_exists('mod',$mod_step)) {
						$this->template->addConfigFile([component_core_system::basePath().'/plugins/'.$name.'/i18n/'], ['public_local_']);
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
	private function exeStepAction(object $mod, string $action) {
		if(method_exists($mod,$action)) {
			try {
				$reflectionMethod = new ReflectionMethod($mod, $action);
				$reflectionMethod->invoke($mod, $this->current_cart);
			}
			catch(ReflectionException $e) {
				$logger = new debug_logger(MP_LOG_DIR);
				$logger->log('php','error','Reflection Exception : '.$e->getMessage(),debug_logger::LOG_MONTH);
			}
		}
	}

    /**
     * @return array
     */
    private function getAdditionnalResume(): array {
        $this->loadModules();
        $arb = [];

        if(!empty($this->mods)) {
            foreach ($this->mods as $mod){
                if(method_exists($mod,'orderResumeInfos')) $arb[] = $mod->orderResumeInfos($this->current_cart);
            }
        }

        return $arb;
    }

    /**
     * @param array $params
     * @return mixed
     */
    private function getParamValue(array $params) {
        $this->loadModules();
		$value = null;

        if(!empty($this->mods)) {
            foreach ($this->mods as $name => $mod){
                if(method_exists($mod,'get_param_value') && $name === $params['module']) $value = $mod->get_param_value($params);
            }
        }

        return $value;
    }

    /**
     * @param array $params
     * @return mixed
     */
    private function getParamInfo(array $params) {
        $this->loadModules();
		$value = null;

        if(!empty($this->mods)) {
            foreach ($this->mods as $name => $mod){
                if(method_exists($mod,'get_param_info') && $name === $params['module']) $value = $mod->get_param_info($params);
            }
        }

        return $value;
    }

    /**
     * @param array $params
     * @return mixed
     */
    private function getParamPrice(array $params) {
        $this->loadModules();
		$value = null;

        if(!empty($this->mods)) {
            foreach ($this->mods as $name => $mod){
                if(method_exists($mod,'get_param_price') && $name === $params['module']) $value = $mod->get_param_price($params);
            }
        }

        return $value;
    }

    /**
     * @return array
     */
    public function getParams(): array {
        $this->loadModules();
		$params = [];

        if(!empty($this->mods)) {
            foreach ($this->mods as $mod){
                if(method_exists($mod,'add_to_cart_params')) $params[] = $mod->add_to_cart_params();
            }
        }

        return $params;
    }
	// --------------------

	// --- Cartpay actions
	/**
	 * Get account cart item and place them into cart
	 */
	private function retreiveAccountCart() {
        $this->current_cart = $this->getItems('session',['session_key_cart' => $this->session_key_cart],'one',false);

        if(is_array($this->current_cart) && isset($this->current_cart['id_cart'])) {
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
                //$item['id_items'];
                $param = $this->getRetreiveParam($item['id_items']);
                // Get the product retreive Param
                if(!empty($param)) {
                    // Insert into cart
                    $this->cart->addItem($product, $quantity, $unit_price ,$pVat, $param);
                }else{
                    // Insert into cart
                    $this->cart->addItem($product, $quantity, $unit_price ,$pVat);
                }
                //print_r($param);
			}
		}
	}

	/**
	 * @param int|string $product
	 * @param int $quantity
	 * @param array $param
	 */
    private function addToCart($product, int $quantity, array $param) {
    	// Get the product unit price
		$unit_price = $this->getProductPrice([
			'id_product' => $product,
			'quantity' => $quantity,
			'id_account' => $this->id_account,
			'param' => $param
		]);

		// Get the product vat rate
		$pVat = $this->getProductVatRate([
			'id_product' => $product,
			'quantity' => $quantity,
			'id_account' => $this->id_account,
			'param' => $param
		]);

		// Get the product vat rate
		if(!empty($param)) {
            $this->getProductParamPrice($param);
        }

		// Insert into cart
        $updated = $this->cart->addItem($product, $quantity, $unit_price ,$pVat, $param);

		$conf = [
			'type' => 'product',
			'data' => [
				'id_cart' => $this->current_cart['id_cart'],
				'id_product' => $product,
				'quantity' => $quantity
			]
		];
		// if the product is already in the cart : Yes - update quantity | No - insert in db
		$updated ? $this->upd($conf) : $this->add($conf);

        // Get full cart info
		$cart = $this->cartData();
        $item = end($cart['items']);

		$html = null;
		// If the item wasn't in the cart, prepare the line to be inserted in the float cart
		if(!$updated) {
			$this->template->assign('setting',$this->settings);
			$this->template->assign('data',[$item]);
			$html = $this->template->fetch('cartpay/loop/float-cart-item.tpl');
		}

		$price_display = $this->settings['price_display'];

		// Return a JSON object with the cart changes
        $this->message->json_post_response(true,'add_to_cart',[
            'result' => $html,
            'extend' => [
                'id' => $product,
                'id_item' => $item['id_items'],
                'nb' => $item['q'],
                'nb_items' => $cart['nb_items'],
                'total' => $price_display === 'tinc' ? $cart['total']['inc'] : $cart['total']['exc']
            ]
        ],
        ['template' => 'cartpay/message.tpl']);
    }

	/**
	 * @param int|string $product
	 * @param int|string $item
	 * @param int $quantity
	 * @param array $param
	 */
    private function updCart($product, $item, int $quantity, array $param) {
    	// Update in db
		$id = $item;

		// Get the product unit price
		$unit_price = $this->getProductPrice([
			'id_product' => $product,
			'quantity' => $quantity,
			'id_account' => $this->id_account,
			'param' => $this->param
		]);

		// Get the product vat rate
		$pVat = $this->getProductVatRate([
			'id_product' => $product,
			'quantity' => $quantity,
			'id_account' => $this->id_account,
			'param' => $this->param
		]);

        // Get the product vat rate
        if(!empty($param)) {
            $this->getProductParamPrice($param);
        }

		if($quantity > 0){
			$this->upd([
				'type' => 'product',
				'data' => [
					'id_cart' => $this->current_cart['id_cart'],
					'id_items' => $item,
					'quantity' => $quantity
				]
			]);
		}
		else {
			$this->del([
				'type' => 'product',
				'data' => [
					'id_cart' => $this->current_cart['id_cart'],
					'id_items' => $item
				]
			]);
		}

		// Update item in cart
		$item = $this->cart->updItem($product, $quantity, $unit_price, $pVat, $param);

		// Get full cart info
		$cart = $this->cartData();

		$product_tot = 0;
		$price_display = $this->settings['price_display'];
		if($quantity > 0) {
            $product_tot = $item['item']->unit_price * $item['q'] * ($price_display === 'tinc' ? 1 + ($item['item']->vat/100) : 1);

            if(!empty($item['item']->params)) {
                foreach ($item['item']->params as $param) {
                    if(isset($param['price']) && !empty($param['price'])) {
                        $product_tot = $product_tot + ( $param['price']['price'] * ($price_display === 'tinc' ? 1 + ($param['price']['vat']/100) : 1) );
                    }
                }
            }
        }

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
				'id_item' => $id,
				'nb' => $quantity,
				'product_tot' => $product_tot,
				'nb_items' => $cart['nb_items'],
				'total' => $total
			]
		]);
    }

    /**
     * @return array
     */
    public function cartData(): array {
		$cart = $this->cart->getCartData();

        if(isset($this->session_key_cart)) {
            $key_cart = $this->current_cart;
			$langData = $this->getItems('idFromIso',['iso' => $this->lang],'one',false);
            $cart_items = $this->getItems('catalog',['id' => $key_cart['id_cart'],'default_lang' => $langData['id_lang']],'all',false);

			if(!empty($cart_items)) {
                $products = [];
                foreach ($cart_items as $item) {
                    $products[$item['id_product']][] = $item;
                }

				$mc = new frontend_model_catalog($this->template);
				$ms = new frontend_model_core();
				$current = $ms->setCurrentId();

                $usedIndexes = [];
                foreach ($cart['items'] as &$item) {
                    $i = isset($usedIndexes[$item['item']->id]) ? $usedIndexes[$item['item']->id] + 1 : 0;
                    try {
                        $product = $mc->setItemData($products[$item['item']->id][$i],$current);
                    }
                    catch(Exception $e) {
                        $log = new debug_logger(MP_LOG_DIR);
                        $log->log('php','error',$e->getMessage());
                    }
                    $product['id_items'] = $products[$item['item']->id][$i]['id_items'];
                    $usedIndexes[$item['item']->id] = $i;
                    $rate = 1 + $item['item']->vat/100;
                    $product['vat'] = $item['item']->vat;
                    $product['unit_price'] = round($item['item']->unit_price, 2);
                    $product['unit_price_inc'] = round($item['item']->unit_price * $rate, 1);
                    $product['total'] = $item['item']->unit_price * $item['q'];
                    $product['total_inc'] = $item['item']->unit_price * $item['q'] * $rate;

                    // --- Additional parameters
                    if(!empty($item['item']->params)) {
                        $item['params'] = [];
                        foreach ($item['item']->params as $param => $value) {
                            if(!empty($value['price'])) {
                                $rate = 1 + ($value['price']['vat']/100);
                                $exc = $value['price']['price'];
                                $inc = $exc * $rate;
                                //$vat = $inc - $exc;
                                $product['total'] += round($exc,2);
                                $product['total_inc'] += round($inc,2);
                            }

                            //if(!empty($value['value'])) {
                                $item['params'][$param] = [
                                    'id' => $value,
                                    'value' => $this->getParamValue([
                                        'module' => $param,
                                        'value' => $value,
                                        'items' => $products[$item['item']->id][$i]['id_items']
                                    ]),
                                    'info' => $this->getParamInfo([
                                        'module' => $param,
                                        'value' => $value,
                                        'items' => $products[$item['item']->id][$i]['id_items']
                                    ]),
                                    'price' => !empty($value['price']) ? $value['price'] : null
                                ];
                            /*}
                            else {
                                $item['params'][$param] = $value;
                            }*/
                        }
                    }

                    $product['total'] = round($product['total'],2);
                    $product['total_inc'] = round($product['total_inc'],1);

                    $item = array_merge($item,$product);
                }

                foreach ($cart['fees'] as &$fee) {
                    $rate = 1 + $fee['vat']/100;
                    $fee['price'] = round($fee['price'], 2);
                    $fee['price_inc'] = round($fee['price'] * $rate, 2);
                }

                $cart['total']['exc'] = round($cart['total']['exc'], 2);
                $settings = new frontend_model_setting($this->template);
                $price_display = $settings->getSetting("price_display");
                /*if($price_display['value'] === 'tinc'){
                    $cart['total']['inc'] = round($cart['total']['inc'], 2);
                }else{
                    $cart['total']['inc'] = round($cart['total']['exc'], 2);
                }*/
                //print number_format(round($cart['total']['inc'], 3),2);
                $cart['total']['inc'] = number_format(round($cart['total']['inc'], 3),2);//round($cart['total']['inc'], 2);

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
	private function setStepUrl(string $type, string $step): string {
		$url = http_url::getUrl().'/'.$this->template->lang.'/cartpay/'.$type.'/?step='.$step;
		$this->template->assign('next_step_url',$url);
		return $url;
	}

	/**
	 * @param string $type
	 * @param array $data
	 * @return array
	 */
	private function setItemsAccount(string $type, array $data): array {
		$newArr = array();
		if(!empty($data)) {
			switch($type){
				case 'account':
					$newArr['id'] = $data['id_buyer'];
					$newArr['email'] = $data['email'];
					$newArr['lastname'] = $data['lastname'];
					$newArr['firstname'] = $data['firstname'];
					$newArr['phone'] = $data['phone'];
					$newArr['company'] = $data['company'];
					$newArr['vat'] = $data['vat'];
					$newArr['address'] = $data['address']['billing']['street'];
					$newArr['postcode'] = $data['address']['billing']['postcode'];
					$newArr['city'] = $data['address']['billing']['town'];
					$newArr['country'] = $data['address']['billing']['country'];
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
	 * @param array $cart
	 * @return array
	 */
	private function getbuyerInfo(array $cart): array {
		if(!empty($this->mods) && isset($this->mods['account']) && isset($this->id_account)) {
			$account = $this->mods['account'];
			$type = 'account';
			$buyerData = $account->accountData();
            $buyer = $this->getItems('buyer', $cart['id_cart'], 'one', false);
            $buyerData['id_buyer'] = $buyer['id_buyer'];
            //print_r($buyerData);
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
	private function saveRecord(string $action, array $data, string $step) {
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
	 * @param array $cart
	 * @param array $data
	 */
	private function saveBuyer(array $cart, array $data) {
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
	 * @param array $data
	 */
	private function saveBilling(array $data) {
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
        //print_r($conf);
		$this->upd($conf);
	}

	/**
	 * @param string $action
	 * @param string $type
	 * @return array
	 */
	private function getDoneStepStatus(string $action, string $type): array	{
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
	 * @param string $type
	 * @param null|string $subContent
	 * @return array
	 */
	private function setNotify(string $type, string $subContent = null): array {
		$this->template->configLoad();
		switch($type){
			case 'warning':
				$warning = [
					'empty' => $this->template->getConfigVars('fields_empty'),
					'mail' => $this->template->getConfigVars('mail_format')
				];
				$message = $warning[$subContent];
				break;
			case 'success':
				$message = $this->template->getConfigVars('message_send_success');
				break;
			case 'error':
				$error = [
					'installed' => $this->template->getConfigVars('installed'),
					'configured' => $this->template->getConfigVars('configured')
				];
				$error = $error[$subContent] ?: '';
				$message = sprintf($error,'plugin_error','contact');
				break;
			default:
				$message = '';
		}

		return [
			'type' => $type,
			'content' => $message
		];
	}

	/**
	 * getNotify
	 * @param string $type
	 * @param string|null $subContent
	 */
	private function getNotify(string $type, string $subContent = null) {
		$this->template->assign('message',$this->setNotify($type,$subContent));
		$this->template->display('contact/notify/message.tpl');
	}

	/**
	 * @return array
	 */
	private function getAdditionnalMailResume(): array {
		$this->loadModules();

		$arb = [];

		if(!empty($this->mods)) {
			foreach ($this->mods as $mod){
				if(method_exists($mod,'mailResumeInfos')) $arb[] = $mod->mailResumeInfos($this->current_cart);
			}
		}

		return $arb;
	}

    /**
     * Send a mail
     * @param string $email
     * @param string $tpl
     * @param array $buyer
     * @param array $record
     */
    protected function send_email(string $email, string $tpl, array $buyer, array $record) {
        if($email) {
            $this->template->configLoad();
			$this->sanitize = new filter_sanitize();

            if(!$this->sanitize->mail($email)) {
				$this->getNotify('warning','mail');
            }
            else {
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
					'config' => $this->getConfig(),
                    'additionnalResume' => $this->getAdditionnalMailResume()
				];

				if(!empty($data)) {
					$contacts = $contact->getContact();
					//$from = $contact->getSender();
                    $mail_settings = new frontend_model_setting($this->template);
                    $from = $mail_settings->getSetting('mail_sender');
                    $file = null;
                    $config = $this->getConfig();

					if($contacts) {
						//Initialisation du contenu du message
						//$send = false;
						foreach ($contacts as $recipient) {
							/*$isSend = $this->mail->send_email(
								$recipient['mail_contact'],
								$tpl,
								$data,
								'',
								$buyer['email'],
                                $from['value'],//$from['mail_sender'],
                                $file
							);*/
							$this->mail->send_email(
								$recipient['mail_contact'],
								$tpl,
								$data,
								'',
								$buyer['email'],
								$from['value'],//$from['mail_sender'],
								$file
							);
						}
					}
					//Initialisation du contenu du message
					//$send = false;
					/*$isSend = $this->mail->send_email(
						$buyer['email'],
						$tpl,
						$data,
						'',
						$noreply,
                        $from['value'],//$from['mail_sender'],
                        $file
					);*/

                    $this->mail->send_email(
                        $config['email_config'],
                        $tpl,
                        $data,
                        '',
                        $noreply,
                        $config['email_config_from'],//$from['mail_sender'],
                        $file
                    );
					$this->mail->send_email(
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
				}
            }
        }
    }
    // --------------------

	/**
	 *
	 */
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
						if (http_request::isPost('id_items')) $this->id_items = (int)$this->clean->numeric($_POST['id_items']);
						if (http_request::isPost('quantity')) $this->quantity = (int)$this->clean->numeric($_POST['quantity']);
						$this->param = http_request::isPost('param') ? $this->clean->arrayClean($_POST['param']) : [];

						if(isset($this->id_product) && isset($this->quantity)) {
							$this->action === 'add' ? $this->addToCart($this->id_product, $this->quantity, $this->param) : $this->updCart($this->id_product, $this->id_items, $this->quantity, $this->param);
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
                    $this->template->breadcrumb->addItem($this->template->getConfigVars('my_cart'));

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
												$country = new component_collections_country();
												$this->template->assign('countries',$country->getAllowedCountries());
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
														&& isset($this->custom['email'])
														&& $record['payment_order'] === 'bank_wire') {
														$this->status = 'pending';
                                                        /*$data = [
                                                            //'type' => $tpl,
                                                            'buyer' => $buyer,
                                                            'record' => $record,
                                                            'cart' => $this->cartData(),
                                                            'pma' => $this->getPaymentMethodAvailable(),
                                                            'config' => $this->getConfig(),
                                                            'additionnalResume' => $this->getAdditionnalResume()
                                                        ];
                                                        print '<pre>';
                                                        print_r($data);
                                                        print '</pre>';*/
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
                                                        /*$log = new debug_logger(MP_LOG_DIR);
                                                        $log->tracelog('start payment');
                                                        $log->tracelog(json_encode(array('id'=>$record['id_order'],'payment_status'=>$this->mods[$record['payment_order']]->getPaymentStatus())));
                                                        *///$log->tracelog('sleep');

                                                        if(method_exists($this->mods[$record['payment_order']],'getPaymentStatus')){
                                                            //$log->tracelog('start payment');
                                                            /*$log->tracelog(json_encode(array(
                                                                'id' => $record['id_order'],
                                                                'status_order' => $this->mods[$record['payment_order']]->getPaymentStatus(),
                                                                'tc' => 1
                                                            )));

                                                            $log->tracelog('sleep');*/

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
																$this->cart->newCart();
                                                            }
                                                        }
                                                    }
													elseif ($record['payment_order'] === 'bank_wire'){
                                                        /*$log = new debug_logger(MP_LOG_DIR);
                                                        $log->tracelog('start payment');
                                                        $log->tracelog(json_encode(
                                                            array(
                                                                'type' => 'status',
                                                                'data' => array(
                                                                    'id' => $this->current_cart['id_cart'],
                                                                    'tc' => 1
                                                                )
                                                            )
                                                        ));*/
                                                        $this->upd(array(
                                                            'type' => 'status',
                                                            'data' => array(
                                                                'id' => $this->current_cart['id_cart'],
                                                                'tc' => 1
                                                            )
                                                        ));
                                                        $this->cart->newCart();
                                                        //$this->session_key_cart = $this->cart->getKey();
                                                        //$this->openSession($this->session_key_cart, $this->id_account);
                                                    }
                                                }
                                                elseif($this->action === 'quotation'){
                                                    $this->upd(array(
                                                        'type' => 'status',
                                                        'data' => array(
                                                            'id' => $this->current_cart['id_cart'],
                                                            'tc' => 1
                                                        )
                                                    ));
                                                    //$this->cart->newCart();
                                                    //$this->session_key_cart = $this->cart->getKey();
                                                    //$this->openSession($this->session_key_cart, $this->id_account);
                                                }
                                            }
                                            //$log = new debug_logger(MP_LOG_DIR);
											// Set the done step data based on the action type and the status of the process
											$this->done = $this->getDoneStepStatus($this->action, $this->status);
											$this->template->assign('done',$this->done);

											// If payment method method is bank wire or the transaction has been succeed
											if(isset($this->done) && !$this->done['error']) {
                                                /*$log = new debug_logger(MP_LOG_DIR);
                                                $log->tracelog($buyer['email']);
                                                $log->tracelog(json_encode($this->status));
                                                $log->tracelog(json_encode($this->action));
                                                $log->tracelog(json_encode($buyer));
                                                $log->tracelog(json_encode($record));*/

                                                $this->send_email($buyer['email'],$this->action,$buyer,$record);
                                            }
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
                $this->template->breadcrumb->addItem($this->template->getConfigVars('my_cart'));
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