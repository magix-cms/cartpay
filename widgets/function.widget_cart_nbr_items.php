<?php
# -- BEGIN LICENSE BLOCK ----------------------------------
#
# This file is part of Magix CMS.
# Magix CMS, a CMS optimized for SEO
# Copyright (C) 2010 - 2011  Gerits Aurelien <aurelien@magix-cms.com>
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
/**
 * MAGIX CMS
 * @category   extends 
 * @package    Smarty
 * @subpackage function
 * @copyright  MAGIX CMS Copyright (c) 2008 - 2016 Gerits Aurelien,
 * http://www.magix-cms.com,  http://www.magix-cjquery.com
 * @license    Dual licensed under the MIT or GPL Version 3 licenses.
 * @version    plugin version
 * @author Gérits Aurélien <aurelien@magix-cms.com> <aurelien@magix-dev.be>
 *
 */
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */
/**
 * Smarty {widget_cart_nbr_items} function plugin
 *
 * Type:     function
 * Name:     widget_cart_nbr_items
 * Date:     21 january 2016
 * Update:   28 january 2016
 * Purpose:
 * Examples: 
 * 			### BASIC ###
        {widget_cart_nbr_items}
 * Output:   
 * @link 	http://www.magix-cms.com
 * @author   Gerits Aurelien
 * @param array
 * @param Smarty
 * @return string
 * RETOURNE LE NOMBRE DE PRODUITS DANS LE PANIER
 */
function smarty_function_widget_cart_nbr_items($params, $template){
    plugins_Autoloader::register(); //chargement des function plugins
    $default_value = '0';
    $default_price = '0.00';
    //if session key_cart
    if(isset($_SESSION['key_cart'])){
        $session_key = $_SESSION['key_cart'];
    }else {
        $session_key = null;
    }
    if ($session_key != null){
        $nbr_items = plugins_cartpay_public::load_cart_nbr_items($session_key);
        $nbr_items = ($nbr_items != 0) ? $nbr_items : $default_value;
        $price_items = plugins_cartpay_public::load_cart_price_items($session_key);
        $price_items = ($price_items != 0) ? $price_items : $default_price;
    }else{
        $nbr_items = $default_value;
        $price_items = $default_price;
    }
    //collection items cart
    $cartItems = array(
        'nbr_items'     =>  $nbr_items,
        'price_items'   =>  $price_items
    );
    $template->assign('collectionCart',$cartItems,true);
}