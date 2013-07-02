<?php
/**
 * Overrided Controller class for the OPC ajax and checkout
 *
 * @package One Page Checkout for VirtueMart 2
 * @subpackage opc
 * @author stAn
 * @author RuposTel s.r.o.
 * @copyright Copyright (C) 2007 - 2012 RuposTel - All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * One Page checkout is free software released under GNU/GPL and uses some code from VirtueMart
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * 
 *
 */

 // Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');



if (!class_exists('VirtueMartControllerCart'))
	  require_once(JPATH_VM_SITE.DS.'controllers'.DS.'cart.php'); 
	  
class VirtueMartControllerCartOpc extends VirtueMartControllerCart {

    /**
     * To set a payment method
     *
     * @author Max Milbers
     * @author Oscar van Eijk
     * @author Valerie Isaksen
     */
    function setpayment(&$cart) {

	/* Get the payment id of the cart */
	//Now set the payment rate into the cart
	
	if ($cart) {
		if(!class_exists('vmPSPlugin')) require(JPATH_VM_PLUGINS.DS.'vmpsplugin.php');
	    JPluginHelper::importPlugin('vmpayment');
	    //Some Paymentmethods needs extra Information like
	    $virtuemart_paymentmethod_id = JRequest::getInt('virtuemart_paymentmethod_id', '0');
	    $cart->setPaymentMethod($virtuemart_paymentmethod_id);

	    //Add a hook here for other payment methods, checking the data of the choosed plugin
	    $_dispatcher = JDispatcher::getInstance();
		$msg = ''; 
	    $_retValues = $_dispatcher->trigger('plgVmOnSelectCheckPayment', array( $cart, &$msg));
	    $dataValid = true;
	    foreach ($_retValues as $_retVal) {
		if ($_retVal === true ) {// Plugin completed succesfull; nothing else to do
		
		    $cart->setCartIntoSession();
			// opc mod:
			return true; 
		    break;
		} else if ($_retVal === false ) {

		   $mainframe = JFactory::getApplication();
		   if (empty($msg))
		   $msg = JFactory::getSession()->get('application.queue');; 
				if (!empty($msg) && (is_array($msg)))
				$redirectMsg = implode('<br />', $msg);
		   $mainframe->redirect(JRoute::_('index.php?option=com_virtuemart&view=cart',$this->useXHTML,$this->useSSL), $redirectMsg);
		    break;
		}
	    }
//			$cart->setDataValidation();	//Not needed already done in the getCart function

	    if ($cart->getInCheckOut()) {
		return true; 
		$mainframe = JFactory::getApplication();
		$mainframe->redirect(JRoute::_('index.php?option=com_virtuemart&view=cart'), $msg);
	    }
	}
	

	return true; 
	parent::display();
    }
	
	    /**
     * Sets a selected shipment to the cart
     *
     * @author Max Milbers
     */
    public function setshipment(&$cart, $virtuemart_shipmentmethod_id_here=null, $redirect=true, $incheckout=true) {
	include(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php'); 
	if (!empty($op_disable_shipping)) return true; 
	/* Get the shipment ID from the cart */
	if (empty($virtuemart_shipmentmethod_id_here))
	$virtuemart_shipmentmethod_id = JRequest::getInt('virtuemart_shipmentmethod_id', '0');
	else $virtuemart_shipmentmethod_id = $virtuemart_shipmentmethod_id_here; 
	
	if ($virtuemart_shipmentmethod_id) {
	    //Now set the shipment ID into the cart
	    
	    if ($cart) {
		if (!class_exists('vmPSPlugin')) require(JPATH_VM_PLUGINS . DS . 'vmpsplugin.php');
		JPluginHelper::importPlugin('vmshipment');
		$cart->setShipment($virtuemart_shipmentmethod_id);
		//Add a hook here for other payment methods, checking the data of the choosed plugin
		$_dispatcher = JDispatcher::getInstance();
		$_retValues = $_dispatcher->trigger('plgVmOnSelectCheckShipment', array( &$cart));
		$dataValid = true;
		foreach ($_retValues as $_retVal) {
		    if ($_retVal === true ) {// Plugin completed succesfull; nothing else to do
			$cart->setCartIntoSession();
			// opc mod
			return true; 
			break;
		    } else if ($_retVal === false ) {
		       $mainframe = JFactory::getApplication();
			   $msg = JFactory::getSession()->get('application.queue');; 
				if (!empty($msg) && (is_array($msg)))
				$redirectMsg = implode('<br />', $msg); 
			   if ($redirect)
		       $mainframe->redirect(JRoute::_('index.php?option=com_virtuemart&view=cart',$this->useXHTML,$this->useSSL), $redirectMsg);
			   else return;
			break;
		    }
		}
		if ($incheckout)
		if ($cart->getInCheckOut()) {
			//opc mod
			return true; 
		}
	    }
	}
// 	self::Cart();
	return true; 
	
    }



}