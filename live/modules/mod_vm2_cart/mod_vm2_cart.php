<?php
/*
 * Created on Feb 23, 2012
 *
 * Author: Linelab.org
 * Project: mod_vm2cart_j25
 */

defined('_JEXEC') or die('Restricted access');

if (!class_exists( 'VmConfig' )) require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart'.DS.'helpers'.DS.'config.php');
if(!class_exists('VirtueMartCart')) require(JPATH_VM_SITE.DS.'helpers'.DS.'cart.php');
//$cart = VirtueMartCart::getCart(false);
//$data = $cart->prepareAjaxData();
require_once JPATH_SITE.DS.'plugins'.DS.'system'.DS.'vm2_cart'.DS.'vm2_cart.php';
$plg=new plgSystemVM2_Cart(JDispatcher::getInstance(),array());
$data=$plg->prepareAjaxData();

$lang = JFactory::getLanguage();
$extension = 'com_virtuemart';
$lang->load($extension);

require_once JModuleHelper::getLayoutPath('mod_vm2_cart');
?>