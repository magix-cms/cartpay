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
class plugins_cartpay_admin extends plugins_cartpay_db {
    /**
     * @var backend_model_template$template
     * @var backend_model_data $data
     * @var component_core_message $message
     * @var backend_controller_plugins $plugins
     * @var backend_model_language $modelLanguage
     * @var component_collections_language $collectionLanguage
     * @var backend_model_setting $settings
     * @var backend_controller_tableform $tableform
     * @var backend_controller_module $module
     */
    protected backend_model_template $template;
    protected backend_model_data $data;
    protected component_core_message $message;
    protected backend_model_plugins $plugins;
    protected backend_model_language $modelLanguage;
    protected component_collections_language $collectionLanguage;
    protected backend_model_setting $settings;
    protected backend_controller_tableform $tableform;
    protected backend_controller_module $module;

    /**
     * @var array $setting
     */
    protected array $setting;

    /**
     * @var string $controller
     * @var string $action
     * @var string $tableaction
     * @var string $tabs
     */
    public string
        $controller,
        $action,
        $tableaction,
        $tabs,
        $status_order;

    /**
     * @var int $edit
     * @var int $offset
     * @var int $page
     * @var int $id
     */
    public int
        $edit,
        $offset,
        $page,
        $id;

    /**
     * @var array $mods
     * @var array $cartpay
     * @var array $address
     * @var array $config
     * @var array $assign
     * @var array $tables
     * @var array $columns
     * @var array $search
     * @var array $extendedTables
     * @var array $extendedAssign
     * @var array $extendedColumns
     */
    public array
        $mods,
        $cartpay,
        $config,
        $address,
        $search,
        $columns,
        $assign,
        $tables,
        $extendedTables,
        $extendedAssign,
        $extendedColumns;

    /*public $tableconfig = array(
        'all' => array(
            'id_cart',
            'email' => ['title' => 'name'],
            'firstname' => ['title' => 'name'],
            'lastname' => ['title' => 'name'],
            'type_cart' => ['type' => 'enum', 'enum' => 'type_', 'input' => null, 'class' => ''],
            'nbr_product' => ['title' => 'name', 'input' => null],
            'nbr_quantity' => ['title' => 'name', 'input' => null],
            'status_order' => ['title' => 'name','type' => 'enum', 'enum' => '', 'input' => null, 'class' => ''],
            'date_register'
        )
    );*/

    /**
     * plugins_account_admin constructor.
     */
    public function __construct()
    {
        $this->template = new backend_model_template();
        $this->data = new backend_model_data($this);
        $this->plugins = new backend_model_plugins();
        $this->message = new component_core_message($this->template);
        $this->modelLanguage = new backend_model_language($this->template);
        $this->collectionLanguage = new component_collections_language();
        $this->settings = new backend_model_setting();
        $this->setting = $this->template->settings;

        // --- GET
        if (http_request::isGet('controller')) $this->controller = form_inputEscape::simpleClean($_GET['controller']);
        if (http_request::isGet('edit')) $this->edit = form_inputEscape::numeric($_GET['edit']);
        if (http_request::isGet('tabs')) $this->tabs = form_inputEscape::simpleClean($_GET['tabs']);
        if (http_request::isGet('page')) $this->page = intval(form_inputEscape::simpleClean($_GET['page']));
        $this->offset = (http_request::isGet('offset')) ? intval(form_inputEscape::simpleClean($_GET['offset'])) : 25;
        if (http_request::isRequest('action')) $this->action = form_inputEscape::simpleClean($_REQUEST['action']);

        if (http_request::isGet('tableaction')) {
            $this->tableaction = form_inputEscape::simpleClean($_GET['tableaction']);
            $this->tableform = new backend_controller_tableform($this, $this->template);
        }

        // --- Search
        if (http_request::isGet('search')) {
            $this->search = form_inputEscape::arrayClean($_GET['search']);
            $this->search = array_filter($this->search, function ($value) {
                return $value !== '';
            });
        }

        // --- ADD or EDIT
        if (http_request::isPost('cartpay')) $this->cartpay = form_inputEscape::arrayClean($_POST['cartpay']);
        elseif (http_request::isGet('cartpay')) $this->cartpay = form_inputEscape::arrayClean($_GET['cartpay']);
        if (http_request::isPost('address')) $this->address = form_inputEscape::arrayClean($_POST['address']);
        if (http_request::isPost('acConfig')) $this->config = (array)form_inputEscape::arrayClean($_POST['acConfig']);
        if (http_request::isPost('id')) $this->id = (int)form_inputEscape::simpleClean($_POST['id']);
        if (http_request::isPost('status_order')) $this->status_order = form_inputEscape::simpleClean($_POST['status_order']);
        //
    }

    /**
     * Method to override the name of the plugin in the admin menu
     * @return string
     */
    public function getExtensionName(): string {
        return $this->template->getConfigVars('cartpay_plugin');
    }

    /**
     * Assign data to the defined variable or return the data
     * @param string $type
     * @param array|int|null $id
     * @param string|null $context
     * @param boolean|string $assign
     * @param boolean $pagination
     * @return mixed
     */
    private function getItems(string $type, $id = null, string $context = null, $assign = true, bool $pagination = false) {
        return $this->data->getItems($type, $id, $context, $assign, $pagination);
    }

    /**
     *
     */
    private function loadModules() {
        $this->module = $this->module ?? new backend_controller_module();
        if(empty($this->mods)) $this->mods = $this->module->load_module('cartpay');
    }

    // -------- Listing root ----------
    /**
     * @return void
     */
    public function setTablesArray() {
        if(!isset($this->tables)) {
            $this->tables = ['mc_cartpay','mc_cartpay_buyer','mc_cartpay_order','mc_cartpay_quotation'];
            $this->loadModules();
            if(!empty($this->mods)) {
                foreach ($this->mods as $mod){
                    if(method_exists($mod,'extendTablesArray')) {
                        $this->tables = array_merge($this->tables,$mod->extendTablesArray());
                    }
                }
            }
        }
    }

    /**
     * @return void
     */
    public function setColumnsArray() {
        if(!isset($this->columns)) {
            //$this->columns = ['id_cart', 'email_buyer', 'firstname_buyer', 'lastname_buyer', 'type_cart', 'nbr_product', 'nbr_quantity', 'status_order', 'date_register'];
            $this->columns = ['id_cart', 'email_buyer', 'firstname_buyer', 'lastname_buyer', 'status_order', 'date_register'];
            $this->loadModules();
            if(!empty($this->mods)) {
                foreach ($this->mods as $mod){
                    if(method_exists($mod,'extendColumnsArray')) {
                        $this->columns = array_merge($this->columns,$mod->extendColumnsArray());
                    }
                }
            }
        }
    }

    /**
     * @return void
     */
    public function setAssignArray() {
        if(!isset($this->assign)) {
            $this->assign = [
                'id_cart',
                'email' => ['title' => 'name', 'col' => 'email_buyer'],
                'firstname' => ['title' => 'name', 'col' => 'firstname_buyer'],
                'lastname' => ['title' => 'name', 'col' => 'lastname_buyer'],
                'type_cart' => ['title' => 'name', 'type' => 'enum', 'col' => 'type_cart', 'enum' => 'type_', 'input' => null, 'class' => ''],
                'nbr_product' => ['title' => 'name', 'col' => 'nbr_product', 'input' => null],
                'nbr_quantity' => ['title' => 'name', 'col' => 'nbr_quantity', 'input' => null],
                'status_order' => ['title' => 'name','type' => 'enum', 'enum' => '', 'input' => null, 'class' => ''],
                'date_register'
            ];
            $this->loadModules();
            if(!empty($this->mods)) {
                $extendArray = [];
                foreach ($this->mods as $name => $mod){
                    if(method_exists($mod,'extendAssignArray')) {
                        $extendArray[] = $mod->extendAssignArray();
                    }
                }
                $newAssignArray = [];
                foreach ($extendArray as $cols) {
                    foreach ($cols as $pos => $item) {
                        $i = 1;
                        foreach ($this->assign as $key => $col) {
                            if($i === $pos) {
                                if(is_array($item)) $newAssignArray = array_merge($newAssignArray,$item);
                                else $newAssignArray[] = $item;
                            }
                            if(is_string($key)) $newAssignArray[$key] = $col;
                            else $newAssignArray[] = $col;
                            $i++;
                        }
                        $this->assign = $newAssignArray;
                    }
                }
            }
        }
    }

    /**
     * @return void
     */
    private function setTableformData() {
        //$this->modelLanguage->getLanguage();
        $this->setTablesArray();
        $this->setColumnsArray();
        $this->setAssignArray();
        $params = [];
        $this->loadModules();
        if(!empty($this->mods)) {
            $extendQueryParams = [];
            foreach ($this->mods as $mod){
                if(method_exists($mod,'extendListingQuery')) {
                    $extendQueryParams[] = $mod->extendListingQuery();
                }
            }
            if(!empty($extendQueryParams)) {
                foreach ($extendQueryParams as $extendParams) {
                    if(isset($extendParams['select']) && !empty($extendParams['select'])) $params['select'][] = $extendParams['select'];
                    if(isset($extendParams['join']) && !empty($extendParams['join'])) $params['join'][] = $extendParams['join'];
                    if(isset($extendParams['where']) && !empty($extendParams['where'])) $params['where'][] = $extendParams['where'];
                }
            }
        }
        $carts = $this->getItems('carts',$params,'all',true,true);
        if(!empty($carts)) $this->data->getScheme($this->tables,$this->columns,$this->assign);
    }

    /**
     * @param bool $ajax
     * @return array
     */
    public function tableSearch(bool $ajax = false): array {
        $params = [];
        $this->setTablesArray();
        $this->setColumnsArray();
        $this->setAssignArray();
        $this->loadModules();
        if(!empty($this->mods)) {
            $extendQueryParams = [];
            foreach ($this->mods as $mod){
                if(method_exists($mod,'extendListingQuery')) {
                    $extendQueryParams[] = $mod->extendListingQuery();
                }
            }
            if(!empty($extendQueryParams)) {
                foreach ($extendQueryParams as $extendParams) {
                    if(isset($extendParams['select']) && !empty($extendParams['select'])) $params['select'][] = $extendParams['select'];
                    if(isset($extendParams['join']) && !empty($extendParams['join'])) $params['join'][] = $extendParams['join'];
                    if(isset($extendParams['where']) && !empty($extendParams['where'])) $params['where'][] = $extendParams['where'];
                    if(isset($extendParams['search']) && !empty($extendParams['search'])) $params['search'][] = $extendParams['search'];
                }
            }
        }

        $results = $this->getItems('carts',$params,'all',true,true);
        if($ajax) {
            $params['section'] = 'carts';
            $params['idcolumn'] = 'id_cart';
            $params['activation'] = false;
            $params['sortable'] = false;
            $params['checkbox'] = true;
            $params['edit'] = true;
            $params['dlt'] = true;
            $params['readonly'] = [];
            $params['cClass'] = 'plugins_cartpay_admin';
        }
        //$this->data->getScheme(['mc_cartpay','mc_cartpay_buyer'],['id_cart', 'email', 'firstname', 'lastname', 'type_cart', 'nbr_product', 'nbr_quantity', 'status_order', 'date_register'],$this->assign);
        $this->data->getScheme($this->tables,$this->columns,$this->assign);

        return [
            'data' => empty($results) ? [] : $results,
            'var' => 'carts',
            'tpl' => 'index.tpl',
            'params' => $params
        ];

    }
    // -------- End Listing root ----------

    // -------- Start Listing edit --------
    /**
     * @return void
     */
    public function setTablesEditArray() {
        if(!isset($this->extendedTables)) {
            $this->extendedTables = ['mc_cartpay','mc_cartpay_buyer', 'mc_catalog_product', 'mc_catalog_product_content'];
            $this->loadModules();
            if(!empty($this->mods)) {
                foreach ($this->mods as $mod){
                    if(method_exists($mod,'extendTablesEditArray')) {
                        $this->extendedTables = array_merge($this->extendedTables,$mod->extendTablesEditArray());
                    }
                }
            }
        }
    }

    /**
     * @return void
     */
    public function setColumnsEditArray() {
        if(!isset($this->extendedColumns)) {
            $this->extendedColumns = ['id_items','name_img', 'name_p','quantity', 'price_p'];
            $this->loadModules();
            if(!empty($this->mods)) {
                foreach ($this->mods as $mod){
                    if(method_exists($mod,'extendColumnsEditArray')) {
                        $this->extendedColumns = array_merge($this->extendedColumns,$mod->extendColumnsEditArray());
                    }
                }
            }
        }
    }

    /**
     * @return void
     */
    public function setAssignEditArray() {
        if(!isset($this->extendedAssign)) {
            $this->extendedAssign = [
                'id_items',
                'name_p' => ['title' => 'name'],
                'quantity' => ['title' => 'name'],
                'price_p' => ['title' => 'name','type' => 'price','input' => null],
                'name_img' => ['title' => 'img','type'=>'img','input' => null]
            ];
            $this->loadModules();
            if(!empty($this->mods)) {
                $extendArray = [];
                $unsetArray = [];
                foreach ($this->mods as $name => $mod){
                    if(method_exists($mod,'unsetAssignEditArray')) {
                        $unsetArray[] = $mod->unsetAssignEditArray();
                    }
                    if(method_exists($mod,'setAssignEditArray')) {
                        $extendArray[] = $mod->setAssignEditArray();
                    }
                }
                $newAssignArray = [];
                $newUnsetArray = [];
                if(is_array($unsetArray)) {

                    foreach ($unsetArray as $key => $value) {
                        foreach ($value as $item){
                            $newUnsetArray[] = $item;
                        }

                    }
                    foreach ($newUnsetArray as $cols) {
                        unset($this->extendedAssign[$cols]);
                    }
                }
                foreach ($extendArray as $cols) {
                    foreach ($cols as $pos => $item) {
                        $i = 1;
                        foreach ($this->extendedAssign as $key => $col) {
                            if($i === $pos) {
                                if(is_array($item)) $newAssignArray = array_merge($newAssignArray,$item);
                                else $newAssignArray[] = $item;
                            }
                            if(is_string($key)) $newAssignArray[$key] = $col;
                            else $newAssignArray[] = $col;
                            $i++;
                        }
                        $this->extendedAssign = $newAssignArray;
                        $newAssignArray = [];
                    }
                }
            }
        }
    }

    /**
     * @return void
     */
    private function setTableformEditData() {
        $this->modelLanguage->getLanguage();
        $this->setTablesEditArray();
        $this->setColumnsEditArray();
        $this->setAssignEditArray();
        $params = [];
        $this->loadModules();
        /*if(!empty($this->mods)) {
            $extendQueryParams = [];
            foreach ($this->mods as $mod){
                if(method_exists($mod,'extendEditListingQuery')) {
                    $extendQueryParams[] = $mod->extendEditListingQuery();
                }
            }
            if(!empty($extendQueryParams)) {
                foreach ($extendQueryParams as $extendParams) {
                    if(isset($extendParams['select']) && !empty($extendParams['select'])) $params['select'][] = $extendParams['select'];
                    if(isset($extendParams['join']) && !empty($extendParams['join'])) $params['join'][] = $extendParams['join'];
                    if(isset($extendParams['where']) && !empty($extendParams['where'])) $params['where'][] = $extendParams['where'];
                }
            }
        }*/

        $defaultLanguage = $this->collectionLanguage->fetchData(['context' => 'one', 'type' => 'default']);
        $products = $this->getItems('product',['id' => $this->edit,'default_lang' => $defaultLanguage['id_lang']],'all',false,false);
        $newProduct = [];
        foreach($products as $key =>$item){
            $newProduct[] = $item;
            if(!empty($this->mods)) {
                foreach ($this->mods as $mod) {
                    if (method_exists($mod, 'extendEditListingQuery')) {
                        $newProduct[$key]['params'] = $mod->extendEditListingQuery($item['id_items'],$item['id_product'],$item['price_p']);
                    }
                }
            }
        }
        $extendProduct = [];
        foreach($newProduct as $key => $item){
            $extendProduct[$key]['id_items'] = $item['id_items'];
            $extendProduct[$key]['name_p'] = $item['name_p'];
            $extendProduct[$key]['quantity'] = $item['quantity'];
            $extendProduct[$key]['name_img'] = $item['name_img'];
            $extendProduct[$key]['price_p'] = $item['price_p'];
            if(isset($item['params'])) {
                foreach ($item['params'] as $val => $val2) {
                    $extendProduct[$key][$val] = $val2;
                    //$test[$key]['price_p'] = !is_null($val2['price_p']) ? $val2['price_p'] : $item['price_p'];
                }
            }
        }
        /*print '<pre>';
        print_r($extendProduct);
        print '</pre>';*/
        $this->template->assign('product',$extendProduct);
        //$this->getItems('carts', array(':default_lang' => $defaultLanguage['id_lang']), 'all');
        /*print '<pre>';
        print_r(array_merge(['id' => $this->edit],$params));
        print '</pre>';*/
        //$this->getItems('product',array_merge(['id' => $this->edit,'default_lang' => $defaultLanguage['id_lang']],$params),'all',true,false);
        $this->data->getScheme($this->extendedTables,$this->extendedColumns,$this->extendedAssign);
    }
    // -------- End Listing edit ----------

    /**
     * @return void
     */
    public function setTableFormArray(){
        $params = [];
        $this->loadModules();
        if(!empty($this->mods)) {
            $extendQueryParams = [];
            foreach ($this->mods as $mod){
                if(method_exists($mod,'extendCartpayQuery')) {
                    $extendQueryParams[] = $mod->extendCartpayQuery();
                }
            }
            if(!empty($extendQueryParams)) {
                foreach ($extendQueryParams as $extendParams) {
                    if(isset($extendParams['select']) && !empty($extendParams['select'])) $params['select'][] = $extendParams['select'];
                    if(isset($extendParams['join']) && !empty($extendParams['join'])) $params['join'][] = $extendParams['join'];
                    if(isset($extendParams['where']) && !empty($extendParams['where'])) $params['where'][] = $extendParams['where'];
                }
            }
        }

        $cart = $this->getItems('cart', array_merge(['id'=>$this->edit],$params), 'one', false);
        $this->template->assign('cart', $cart);
    }
    /**
     * @return void
     */
    public function extendFormEdit(){
        $formFiles = [];
        $this->loadModules();
        if(!empty($this->mods)) {
            $extendQueryParams = [];
            foreach ($this->mods as $mod) {
                if(method_exists($mod,'extendCartpayForm')) {
                    $formFiles[] = $mod->extendCartpayForm();
                }
            }
        }
        $this->template->assign('cartpay_params',$formFiles);
        //print_r($formFiles);
        //if(is_string($temp)) $this->template->display($dir.$temp.'.tpl');
    }

    /// -------- Cart data --------
    /**
     * Update data
     * @param array $config
     */
    private function upd(array $config) {
        switch ($config['type']) {
            case 'cart':
            case 'address':
            case 'cartConfig':
            case 'pwd':
            case 'config':
            case 'status_order':
                parent::update(
                    ['type' => $config['type']],
                    $config['data']
                );
                break;
        }
    }

    /**
     * @return void
     */
    public function run(){
        $this->loadModules();
        if (http_request::isGet('plugin')) $this->plugin = form_inputEscape::simpleClean($_GET['plugin']);

        if(isset($this->plugin)) {
            //$defaultLanguage = $this->collectionLanguage->fetchData(['context' => 'one', 'type' => 'default']);
            //$this->getItems('carts', array(':default_lang' => $defaultLanguage['id_lang']), 'all');
            $this->getItems('carts', NULL, 'all');
            $this->plugins->getModuleTabs('cartpay');
            // Initialise l'API menu des plugins core
            $this->modelLanguage->getLanguage();
            // Execute un plugin core
            $class = 'plugins_' . $this->plugin . '_core';
            if(file_exists(component_core_system::basePath().'plugins'.DIRECTORY_SEPARATOR.$this->plugin.DIRECTORY_SEPARATOR.'core.php') && class_exists($class) && method_exists($class, 'run')) {
                $executeClass =  new $class;
                if($executeClass instanceof $class){
                    $executeClass->run();
                }
            }
        }
        else {
            if (isset($this->tableaction)) {
                $this->tableform->run();
            }
			else {
                if (isset($this->action)) {
                    switch ($this->action) {
                        case 'edit':
                            $status = false;
                            $notify = 'error';
                            if (!empty($this->tabs)) {
                                switch ($this->tabs) {
                                    case 'config':
                                        $config = $this->getItems('config', null, 'one', false);
                                        $this->config['id'] = $config['id_config'];
                                        $this->config['bank_wire'] = isset($this->config['bank_wire']) ? 1 : 0;
                                        $this->config['quotation_enabled'] = isset($this->config['quotation_enabled']) ? 1 : 0;
                                        $this->config['order_enabled'] = isset($this->config['order_enabled']) ? 1 : 0;
                                        //$this->config['type_order'] = $this->config['type_order'];
                                        $this->config['account_owner'] = $this->config['account_owner'] === '' ? null : $this->config['account_owner'];
                                        $this->config['bank_account'] = $this->config['bank_account'] === '' ? null : $this->config['bank_account'];
                                        $this->config['bank_address'] = $this->config['bank_address'] === '' ? null : $this->config['bank_address'];
                                        $this->config['bank_link'] = $this->config['bank_link'] === '' ? null : $this->config['bank_link'];

                                        $this->config['email_config'] = $this->config['email_config'] === '' ? null : $this->config['email_config'];
                                        $this->config['email_config_from'] = $this->config['email_config_from'] === '' ? null : $this->config['email_config_from'];
                                        $this->config['billing_address'] = isset($this->config['billing_address']) ? 1 : 0;
                                        $this->config['show_price'] = isset($this->config['show_price']) ? 1 : 0;
                                        //print_r($this->config);
                                        $this->upd(array(
                                            'type' => 'config',
                                            'data' => $this->config
                                        ));

                                        $status = true;
                                        $notify = 'update';
                                        break;
                                }
                                $this->message->json_post_response($status, $notify);
                            } else {
                                if (isset($this->status_order)) {
                                    $this->upd(array(
                                        'type' => 'status_order',
                                        'data' => array(
                                            'status_order' => $this->status_order,
                                            'id' => $this->id
                                        )
                                    ));
                                    $status = true;
                                    $notify = 'update';
                                    $this->message->json_post_response($status, $notify);

                                } else {
                                    /*if (class_exists('plugins_account_admin')) {
                                        $cart = $this->getItems('cart_account', $this->edit, 'one', false);
                                    } else {
                                        $cart = $this->getItems('cart', $this->edit, 'one', false);
                                    }*/

                                    $this->setTableFormArray();
                                    $this->extendFormEdit();

                                    /*$country = new component_collections_country();
                                    $this->template->assign('countries',$country->getCountries());*/
                                    $this->setTableformEditData();
                                    $this->loadModules();
                                    //print_r($this->mods);
                                    if(!empty($this->mods)) {
                                        foreach ($this->mods as $name => $mod){
                                            if(method_exists($mod,'extendAssignEditArray')) {
                                                $this->template->addConfigFile([component_core_system::basePath().'plugins'.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.'i18n'.DIRECTORY_SEPARATOR], [$name.'_admin_']);
                                            }
                                        }
                                    }
                                    $this->template->display('edit.tpl');
                                }
                            }
                            break;
                        case 'delete':
                            if (isset($this->id) && !empty($this->id)) {
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
                    /*if (class_exists('plugins_account_admin')) {
                        $this->getItems('carts_account', null, 'all', true, true);
                    } else {
                        $this->getItems('carts', null, 'all', true, true);
                    }*/

                    //$this->template->assign('carts',$carts);
                    $this->getItems('config', null, 'one');
                    $this->setTableformData();
                    $this->loadModules();
                    if (!empty($this->mods)) {
                        foreach ($this->mods as $name => $mod) {
                            if (method_exists($mod, 'extendAssignArray')) {
                                $this->template->addConfigFile([component_core_system::basePath() . 'plugins' . DIRECTORY_SEPARATOR . $name . DIRECTORY_SEPARATOR . 'i18n' . DIRECTORY_SEPARATOR], [$name . '_admin_']);
                            }
                        }
                    }
                    $this->template->display('index.tpl');
                }
            }
        }
    }
}