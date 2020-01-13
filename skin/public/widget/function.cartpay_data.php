<?php
/*
 # -- BEGIN LICENSE BLOCK ----------------------------------
 #
 # This file is part of MAGIX CMS.
 # MAGIX CMS, The content management system optimized for users
 # Copyright (C) 2008 - 2018 magix-cms.com <support@magix-cms.com>
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
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */
/**
 * Smarty {cartpay_data} function plugin
 *
 * Type: function
 * Name: cartpay_data
 * Purpose:
 * USAGE: {cartpay_data}
 * Output:   
 * @link http://www.magix-dev.be
 * @author Gerits Aurelien
 * @version 1.5
 * @param array
 * @param Smarty
 * @return string
 */
function smarty_function_cartpay_data($params, $template){
    $settings = new frontend_model_setting();
    $set = $settings->getSetting('ssl');
    $session = new http_session($set['ssl']);
	$cart = new plugins_cartpay_public();

	$modelTemplate = new frontend_model_template();
	$modelTemplate->addConfigFile(
		array(component_core_system::basePath().'/plugins/cartpay/i18n/'),
		array('public_local_'),
		false
	);
	$modelTemplate->configLoad();

    $session->start('mc_cartpay');
    $array_sess = array(
        'session_key_cart'     => session_id()
    );
    $session->run($array_sess);
    //$session->debug();

    $template->assign('cart',$cart->cartData());
    $template->assign('config_cart',$cart->getConfig());

	/*$template->assign('hashurl',$member->hashUrl());
	$template->assign('account',$member->accountData());*/
}