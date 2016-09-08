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
class plugins_cartpay_module extends database_plugins_cartpay {
	public $plugins, $pRoot;

	/**
	 * construct
	 */
	public function __construct(){
		if (class_exists('backend_controller_plugins')) {
			$this->plugins = new backend_controller_plugins();
			$this->pRoot = $this->plugins->directory_plugins();
		}
	}

	/**
	 * Register module and set it as active
	 * @param $module
	 */
	public function register($module){
		if(parent::c_show_table('mc_plugins_cartpay_module')) {
			if(parent::g_mod($module) == null) {
				$module_class = 'plugins_'.$module.'_cartpay';
				if(class_exists($module_class)) {
					$active = $this->call_method($this->get_call_class($module_class),'c_active',array()) ? 1 : 0;
					parent::register_module($module,$active);
				}
			} else {
				$this->u_register($module);
			}
		}
	}

	/**
	 * Update register module
	 * @param $module
	 */
	public function u_register($module){
		if(parent::c_show_table('mc_plugins_cartpay_module')) {
			$module_class = 'plugins_'.$module.'_cartpay';
			$active = $this->call_method($this->get_call_class($module_class),'c_active',array()) ? 1 : 0;
			parent::u_register_module($module,$active);
		}
	}

	/**
	 * Unregister module and set it as active
	 * @param $module
	 */
	public function unregister($module){
		if(parent::c_show_table('mc_plugins_cartpay_module')) {
			if(parent::g_mod($module) != null) {
				parent::unregister_module($module);
			}
		}
	}

	/**
	 * Return a list of all plugin
	 * @param $root
	 * @return array
	 */
	private function get_plugins($root){
		$result = array();

		$ldir = scandir($root);
		foreach ($ldir as $key => $value)
		{
			if (!in_array($value,array(".","..","cartpay")))
			{
				if (is_dir($root . DIRECTORY_SEPARATOR . $value))
				{
					$result[] = $value;
				}
			}
		}

		return $result;
	}

	/**
	 * Search and register all non loaded compatible modules
	 */
	private function extend_module($plugins = null){
		if(!$plugins) $plugins = $this->get_plugins($this->pRoot);

		foreach ($plugins as $plugin) {
			if(!parent::g_mod($plugin)) {
				if(file_exists($this->pRoot.$plugin.DIRECTORY_SEPARATOR.'cartpay.php')){
					if(class_exists('plugins_'.$plugin.'_cartpay')){
						$this->register($plugin);
					}
				}
			} else {
				if(!file_exists($this->pRoot.$plugin.DIRECTORY_SEPARATOR.'cartpay.php') || !class_exists('plugins_'.$plugin.'_cartpay')){
					$this->unregister($plugin);
				} else {
					$this->u_register($plugin);
				}
			}
		}
	}

	/**
	 * Get active modules and return a array of all active module instance
	 *
	 * the `extend_module` method should be executed before to ensure that
	 * all compatible modules will be loaded
	 *
	 * @return array
	 */
	public function load_module($parse_module){
		if($parse_module) $this->extend_module();
		$modules = parent::g_module();
		if($parse_module) $this->extend_module(array_map(function($a){ return $a['module_name']; },$modules));
		$active_mods = array();

		foreach ( $modules as $mod ) {
			$modClass = 'plugins_'.$mod['module_name'].'_cartpay';
			$active_mods[$mod['module_name']] = $this->get_call_class($modClass);
		}

		return $active_mods;
	}

	/**
	 * Instantiate module class
	 * @param $module
	 * @return mixed
	 */
	private function get_call_class($module){
		try{
			$class =  new $module;
			if($class instanceof $module){
				return $class;
			}else{
				throw new Exception('not instantiate the class: '.$module);
			}
		}catch(Exception $e) {
			magixglobal_model_system::magixlog("Error plugins execute", $e);
		}
	}

	/**
	 * Call module method and return result
	 * @param $mod
	 * @param $methodName
	 * @param $param_arr
	 * @return mixed
	 */
	public function call_method($mod,$methodName,$param_arr){
		if(method_exists($mod,$methodName)){
			return call_user_func_array(
				array(
					$mod,
					$methodName
				),
				$param_arr
			);
		}
	}
}
?>