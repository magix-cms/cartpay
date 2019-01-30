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
 * @category   cartpay
 * @package    plugins
 * @copyright  MAGIX CMS Copyright (c) 2008 - 2018 Gerits Aurelien,
 * http://www.magix-cms.com
 * @license    Dual licensed under the MIT or GPL Version 3 licenses.
 * @version    2.0
 * Author: Gerits Aurelien
 * Date: 16-08-18
 * Time: 10:00
 * @name plugins_cartpay_admin
 * Le plugin cartpay
 */
class plugins_cartpay_admin extends plugins_cartpay_db
{
    /**
     * @var object
     */
    protected $controller,
        $data,
        $template,
        $message,
        $plugins,
        $modelLanguage,
        $collectionLanguage,
        $header,
        $settings,
        $setting;

    /**
     * Les variables globales
     * @var integer $edit
     * @var string $action
     * @var string $tabs
     */
    public $edit = 0,
        $action = '',
        $tabs = '';

    /**
     * Les variables plugin
     * @var array $account
     * @var array $address
     * @var array $config
     * @var integer $id
     */
    public
        $cartpay = array(),
        $address = array(),
        $config = array(),
        $id = 0;


    /**
     * Modules
     * @var $module
     * @var $activeMods
     * @var $cartpayModule
     * @var $country
     */
    //protected $module, $activeMods, $cartpayModule;

    /**
     * plugins_account_admin constructor.
     */
    public function __construct()
    {
        $this->template = new backend_model_template();
        $this->plugins = new backend_controller_plugins();
        $this->message = new component_core_message($this->template);
        $this->modelLanguage = new backend_model_language($this->template);
        $this->collectionLanguage = new component_collections_language();
        $this->data = new backend_model_data($this);
        $this->settings = new backend_model_setting();
        $this->setting = $this->settings->getSetting();
        $this->header = new http_header();

        $formClean = new form_inputEscape();

        // --- GET
        if (http_request::isGet('controller')) {
            $this->controller = $formClean->simpleClean($_GET['controller']);
        }
        if (http_request::isGet('edit')) {
            $this->edit = (int)$formClean->numeric($_GET['edit']);
        }
        if (http_request::isGet('action')) {
            $this->action = $formClean->simpleClean($_GET['action']);
        } elseif (http_request::isPost('action')) {
            $this->action = $formClean->simpleClean($_POST['action']);
        }
        if (http_request::isGet('tabs')) {
            $this->tabs = $formClean->simpleClean($_GET['tabs']);
        }

        /*if (class_exists('plugins_profil_cartpay')) {
            $this->cartpayModule = new plugins_profil_cartpay();
        }
        if(class_exists('plugins_profil_module')) {
            $this->module = new plugins_profil_module();
        }*/

        // --- ADD or EDIT
        if (http_request::isPost('cartpay')) {
            $this->cartpay = (array)$formClean->arrayClean($_POST['cartpay']);
        } elseif (http_request::isGet('cartpay')) {
            $this->cartpay = (array)$formClean->arrayClean($_GET['cartpay']);
        }
        if (http_request::isPost('address')) {
            $this->address = (array)$formClean->arrayClean($_POST['address']);
        }
        if (http_request::isPost('acConfig')) {
            $this->config = (array)$formClean->arrayClean($_POST['acConfig']);
        }
        if (http_request::isPost('id')) {
            $this->id = (int)$formClean->simpleClean($_POST['id']);
        }
    }

    /**
     * Method to override the name of the plugin in the admin menu
     * @return string
     */
    /*public function getExtensionName()
    {
        return $this->template->getConfigVars('cartpay_plugin');
    }*/

    /**
     * Assign data to the defined variable or return the data
     * @param string $type
     * @param string|int|null $id
     * @param string $context
     * @param boolean $assign
     * @return mixed
     */
    private function getItems($type, $id = null, $context = null, $assign = true)
    {
        return $this->data->getItems($type, $id, $context, $assign);
    }

    /**
     * Update data
     * @param array $config
     */
    private function upd($config)
    {
        switch ($config['type']) {
            //case 'accountActive':
            case 'cart':
            case 'address':
            case 'cartConfig':
            case 'pwd':
            case 'config':
                parent::update(
                    array('type' => $config['type']),
                    $config['data']
                );
                break;
        }
    }
    public function run(){
        if($this->action) {
            switch ($this->action) {
                /*case 'add':
                    if(is_array($this->account) && !empty($this->account)) {
                        if($this->account['passwd'] === $this->account['repeat_passwd']) {
                            $this->account['passcrypt_ac'] = password_hash($this->account['passwd'], PASSWORD_DEFAULT);
                            $this->account['keyuniqid_ac'] = filter_rsa::uniqID();
                            $this->account['active_ac'] = isset($this->account['active_ac']) ? 1 : 0;
                            unset($this->account['passwd']);
                            unset($this->account['repeat_passwd']);

                            $this->add(array(
                                    'type' => 'account',
                                    'data' => $this->account
                                )
                            );
                            $this->message->json_post_response(true,'add_redirect');
                        }
                    }
                    else {
                        $this->modelLanguage->getLanguage();

                        $this->template->display('add.tpl');
                    }
                    break;*/
                case 'edit':
                    $status = false;
                    $notify = 'error';
                    if(!empty($this->tabs)) {
                        switch ($this->tabs) {
                            /*case 'account':
                                $this->account['id'] = $this->id;
                                $this->address['id'] = $this->id;

                                $this->upd(array(
                                    'type' => 'account',
                                    'data' => array_map(function($v) { return $v === '' ? null : $v; }, $this->account)
                                ));
                                $this->upd(array(
                                    'type' => 'address',
                                    'data' => array_map(function($v) { return $v === '' ? null : $v; }, $this->address)
                                ));
                                $status = true;
                                $notify = 'update';
                                break;*/
                            case 'config':
                                $config = $this->getItems('config',$this->edit,'one',false);
                                $this->config['id'] = $config['id_config'];
                                $this->config['bank_wire'] = isset($this->config['bank_wire']) ? 1 : 0;
                                $this->config['type_order'] = $this->config['type_order'];
                                $this->config['account_owner'] = $this->config['account_owner'] === '' ? null : $this->config['account_owner'];
                                $this->config['bank_account'] = $this->config['bank_account'] === '' ? null : $this->config['bank_account'];
                                $this->config['bank_address'] = $this->config['bank_address'] === '' ? null : $this->config['bank_address'];

                                $this->config['email_config'] = $this->config['email_config'] === '' ? null : $this->config['email_config'];
                                $this->config['email_config_from'] = $this->config['email_config_from'] === '' ? null : $this->config['email_config_from'];

                                $this->upd(array(
                                    'type' => 'config',
                                    'data' => $this->config
                                ));

                                $status = true;
                                $notify = 'update';
                                break;
                        }
                        $this->message->json_post_response($status,$notify);
                    }
                    else {
                        //$this->modelLanguage->getLanguage();
                        $defaultLanguage = $this->collectionLanguage->fetchData(array('context'=>'one','type'=>'default'));

                        $cart = $this->getItems('cart',$this->edit,'one',false);
                        $this->template->assign('cart',$cart);

                        $this->getItems('product',array(':id'=>$this->edit,':default_lang'=>$defaultLanguage['id_lang']),'all');
                        if($cart['type_cart'] == 'sale') {
                            $assign = array(
                                'id_items',
                                'name_p' => ['title' => 'name'],
                                'quantity' => ['title' => 'name', 'input' => null],
                                'price_p' => ['type' => 'price', 'input' => null]
                            );
                            $this->data->getScheme(array('mc_cartpay_items', 'mc_catalog_product', 'mc_catalog_product_content'), array('id_items', 'name_p', 'price_p'), $assign);
                        }else{
                            $assign = array(
                                'id_items',
                                'name_p' => ['title' => 'name'],
                                'quantity' => ['title' => 'name', 'input' => null]
                            );
                            $this->data->getScheme(array('mc_cartpay_items', 'mc_catalog_product', 'mc_catalog_product_content'), array('id_items', 'name_p'), $assign);
                        }
                        /*$country = new component_collections_country();
                        $this->template->assign('countries',$country->getCountries());*/

                        $this->template->display('edit.tpl');
                    }
                    break;
                case 'delete':
                    if(isset($this->id) && !empty($this->id)) {
                        $this->del(
                            array(
                                'type' => 'account',
                                'data' => array(
                                    'id' => $this->id
                                )
                            )
                        );
                    }
                    break;
            }
        }
        else {
            /*$this->modelLanguage->getLanguage();
            $langs = $this->modelLanguage->setLanguage();
            $opts = array();
            foreach ($langs as $id => $iso) {
                $opts[] = array(
                    'v' => $id,
                    'name' => $iso
                );
            }*/
            $this->getItems('carts');
            $this->getItems('config', $this->edit, 'one');
            $assign = array(
                'id_cart',
                /*'iso_lang' => array(
                    'title' => 'lang',
                    'class' => 'fixed-td-md',
                    'input' => array(
                        'type' => 'select',
                        'var' => false,
                        'values' => $opts
                    )
                ),*/
                'email_ac',
                'firstname_ac',
                'lastname_ac',
                'type_cart' => ['type' => 'enum', 'enum' => 'type_', 'input' => null, 'class' => ''],
                'nbr_product' => ['title' => 'name', 'input' => null],
                'nbr_quantity' => ['title' => 'name', 'input' => null],
                'date_register'
            );
            $this->data->getScheme(array('mc_cartpay', 'mc_profil'), array('id_cart', 'email_ac', 'firstname_ac', 'lastname_ac', 'type_cart', 'nbr_product', 'nbr_quantity', 'date_register'), $assign);
            $this->template->display('index.tpl');
        }
    }
}