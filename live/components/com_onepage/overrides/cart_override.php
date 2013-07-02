<?php
/**
 * Overrided portion of cart class for OPC2 on Virtuemart 2
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

class OPCcheckout extends VirtueMartCart
{
     public static $_triesValidateCoupon;
    
	
	 var $prices = null;
	 var $pricesUnformatted = null;
	 var $pricesCurrency = null;
	 public static $_cart = null; 
     function __construct(&$cart) {
	    $this->useSSL = VmConfig::get('useSSL',0);
		$this->useXHTML = true;
		self::$_triesValidateCoupon=0;
		
		$this->useSSL = VmConfig::get('useSSL',0);
		$this->useXHTML = true;
		//	var $productIds = array();
		$this->products = $cart->products; 
		$this->vendorId = $cart->vendorId; 
		$this->virtuemart_shipmentmethod_id = JRequest::getInt('virtuemart_shipmentmethod_id', 0); 
		$this->virtuemart_paymentmethod_id = JRequest::getInt('virtuemart_paymentmethod_id', 0); 
	    $this->BT = $cart->BT; 
		$this->ST = $cart->ST; 
		$this->tosAccepted = $cart->tosAccepted; 
		$this->customer_comment = $cart->customer_comment; 
		$this->couponCode = $cart->couponCode; 
		$this->cartData = $cart->cartData; 
		$this->lists = $cart->lists; 
		if (isset($cart->prices))
		$this->prices = $cart->prices; 
		if (isset($cart->pricesUnformatted))
		$this->pricesUnformatted = $cart->pricesUnformatted; 
		
		if (isset($cart->pricesCurrency))
		$this->pricesCurrency = $cart->pricesCurrency; 
		
		$this->paymentCurrency = $cart->paymentCurrency; 
		$this->STsameAsBT = $cart->STsameAsBT;
		
		self::$_cart = $cart;
		
		
	}
	function checkoutData(&$cart, $cartClass) {
		include(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php'); 
		$cart->_inCheckOut = true;

		$cart->tosAccepted = JRequest::getInt('tosAccepted', $cart->tosAccepted);
		
		if (!isset($cart->customer_comment)) $cart->customer_comment = ''; 
		
		$cart->customer_comment = JRequest::getVar('customer_comment', $cart->customer_comment);
		if (empty($cart->customer_comment))
		{
		  $cart->customer_comment = JRequest::getVar('customer_note', $cart->customer_comment);
		}
		
		
		if (empty($op_disable_shipto))
		{
		if (($cart->selected_shipto = JRequest::getVar('shipto', null)) !== null) {
			JModel::addIncludePath(JPATH_VM_ADMINISTRATOR . DS . 'models');
			$userModel = JModel::getInstance('user', 'VirtueMartModel');
			$stData = $userModel->getUserAddressList(0, 'ST', $cart->selected_shipto);
			$this->validateUserData('ST', $stData[0], $cart);
		}
		}
		else
		{
		    $cart->STsameAsBT = 1;
			$cart->ST = $cart->BT;
		}
		
		$cart->setCartIntoSession();

		$mainframe = JFactory::getApplication();
		if (count($cart->products) == 0) {
			$mainframe->redirect(JRoute::_('index.php?option=com_virtuemart'), JText::_('COM_VIRTUEMART_CART_NO_PRODUCT'));
		} else {
			foreach ($cart->products as $product) {
				$redirectMsg = $this->checkForQuantities($product, $product->quantity);
				if (!$redirectMsg) {
					//					$this->setCartIntoSession();
					$this->redirect(JRoute::_('index.php?option=com_virtuemart&view=cart'), $redirectMsg);
				}
			}
		}
		
		include(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php'); 
		// Check if a minimun purchase value is set
		if (($msg = $this->checkPurchaseValue()) != null) {
			$this->redirect(JRoute::_('index.php?option=com_virtuemart&view=cart'), $msg);
		}
		
		//But we check the data again to be sure
		if (empty($cart->BT)) {
		
			$this->redirect(JRoute::_('index.php?option=com_virtuemart&view=cart') );
		} else {
			$redirectMsg = $this->validateUserData('BT', null, $cart);
			if ($redirectMsg) {
		
				$this->redirect(JRoute::_('index.php?option=com_virtuemart&view=cart'), $redirectMsg);
			}
		}

		if($cart->STsameAsBT!==0){
			$cart->ST = $cart->BT;
		} else {
			//Only when there is an ST data, test if all necessary fields are filled
			if (!empty($cart->ST)) {
				$redirectMsg = $this->validateUserData('ST', null, $cart);
				if ($redirectMsg) {
					//				$cart->setCartIntoSession();
					$this->redirect(JRoute::_('index.php?option=com_virtuemart&view=cart'), $redirectMsg);
				}
			}
		}


		// Test Coupon
		if (!empty($cart->couponCode)) {
			$prices = $cartClass->getCartPrices();
			if (!class_exists('CouponHelper')) {
				require(JPATH_VM_SITE . DS . 'helpers' . DS . 'coupon.php');
			}
			$redirectMsg = CouponHelper::ValidateCouponCode($cart->couponCode, $prices['salesPrice']);
			if (!empty($redirectMsg)) {
				$cart->couponCode = '';
				//				$this->setCartIntoSession();
				$this->redirect(JRoute::_('index.php?option=com_virtuemart&view=cart',$cart->useXHTML,$cart->useSSL), $redirectMsg);
			}
		}

		//Test Shipment and show shipment plugin
			
		if (empty($op_disable_shipping))
		{
		if (empty($cart->virtuemart_shipmentmethod_id)) {
			$this->redirect(JRoute::_('index.php?option=com_virtuemart&view=cart',$cart->useXHTML,$cart->useSSL), $redirectMsg);
		} else {
			if (!class_exists('vmPSPlugin')) require(JPATH_VM_PLUGINS . DS . 'vmpsplugin.php');
			JPluginHelper::importPlugin('vmshipment');
			//Add a hook here for other shipment methods, checking the data of the choosed plugin
			$dispatcher = JDispatcher::getInstance();
			$retValues = $dispatcher->trigger('plgVmOnCheckoutCheckDataShipment', array(  $cart));

			foreach ($retValues as $retVal) {
				if ($retVal === true) {
					break; // Plugin completed succesful; nothing else to do
				} elseif ($retVal === false) {
					// Missing data, ask for it (again)
					$this->redirect(JRoute::_('index.php?option=com_virtuemart&view=cart',$cart->useXHTML,$cart->useSSL), $redirectMsg);
					// 	NOTE: inactive plugins will always return null, so that value cannot be used for anything else!
				}
			}
		}
		}
		 
		//echo 'hier ';
		//Test Payment and show payment plugin
		if (empty($cart->virtuemart_paymentmethod_id)) {
			$this->redirect(JRoute::_('index.php?option=com_virtuemart&view=cart',$cart->useXHTML,$cart->useSSL), $redirectMsg);
		} else {
			if(!class_exists('vmPSPlugin')) require(JPATH_VM_PLUGINS.DS.'vmpsplugin.php');
			JPluginHelper::importPlugin('vmpayment');
			//Add a hook here for other payment methods, checking the data of the choosed plugin
			$dispatcher = JDispatcher::getInstance();
			$retValues = $dispatcher->trigger('plgVmOnCheckoutCheckDataPayment', array( $cart));

			foreach ($retValues as $retVal) {
				if ($retVal === true) {
					break; // Plugin completed succesful; nothing else to do
				} elseif ($retVal === false) {
				
				$msg = JFactory::getSession()->get('application.queue');; 
				if (!empty($msg) && (is_array($msg)))
				$redirectMsg = implode('<br />', $msg); 
				
					// Missing data, ask for it (again)
					$this->redirect(JRoute::_('index.php?option=com_virtuemart&view=cart',$cart->useXHTML,$cart->useSSL), $redirectMsg);
					// 	NOTE: inactive plugins will always return null, so that value cannot be used for anything else!
				}
			}
		}
		

		if (VmConfig::get('agree_to_tos_onorder', 1))
		{
		if (empty($cart->tosAccepted)) {
			vmdebug('checkoutData');
			if (!class_exists('VirtueMartModelUserfields')){
				require(JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'userfields.php');
			}
			$userFieldsModel = new VirtueMartModelUserfields();
			$required = $userFieldsModel->getIfRequired('agreed');
			if(!empty($required)){
				$this->redirect(JRoute::_('index.php?option=com_virtuemart&view=cart'), JText::_('COM_VIRTUEMART_CART_PLEASE_ACCEPT_TOS'));
			}
		}
		}
		
		if (empty($GLOBALS['is_dup']))
		if(VmConfig::get('oncheckout_only_registered',0)) {
			$currentUser = JFactory::getUser();
			if(empty($currentUser->id)){
				$this->redirect(JRoute::_('index.php?option=com_virtuemart&view=cart'), JText::_('COM_VIRTUEMART_CART_ONLY_REGISTERED') );
			}
		 }


		//Show cart and checkout data overview
		
		$cart->_inCheckOut = false;
		$cart->_dataValidated = true;

		$cart->setCartIntoSession();

		return true;
	}
	
	
	function doCurl($order)
	{
	 if (!function_exists('curl_multi_exec')) return; 
	 $ch = array(); 
	  include(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php'); 
	  if (!empty($curl_url) && (is_array($curl_url)))
	   {
	     $i = 0; 
	     foreach ($curl_url as $blink)
		  {
		   $i++; 
		   
		    $link = @base64_decode($blink); 
			if (strpos($link, 'http')===0)
			 {
			   	if (!function_exists('curl_init'))
				 return; 
		
			if (isset($order->email))
			$link = str_replace('{email}', $order->email, $link); 
			
			if (isset($order->first_name))
			$link = str_replace('{first_name}', $order->first_name, $link); 
			
			if (isset($order->last_name))
			$link = str_replace('{last_name}', $order->last_name, $link); 
			
			if (isset($order->virtuemart_order_id))
			$link = str_replace('{order_id}', $order->virtuemart_order_id, $link); 
			
			
			
			//$link = str_replace('{amount}', $order['details']->
			
		
			// http://arguments.callee.info/2010/02/21/multiple-curl-requests-with-php/
		
		    $ch[$i] = null; 
			$ch[$i] = curl_init($link); 
			$url = $link;
			curl_setopt ($ch[$i], CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch[$i], CURLOPT_SSL_VERIFYPEER, 0); 
			curl_setopt($ch[$i], CURLOPT_URL,$url); // set url to post to
			curl_setopt($ch[$i], CURLOPT_RETURNTRANSFER,1); // return into a variable
			curl_setopt($ch[$i], CURLOPT_TIMEOUT, 4000); // times out after 4s
			curl_setopt($ch[$i], CURLOPT_POST, 0); 
			curl_setopt($ch[$i], CURLOPT_ENCODING , "gzip");
			curl_setopt($ch[$i], CURLOPT_CUSTOMREQUEST, 'GET');
		  }
		  }
		
		  $mh = curl_multi_init();
		  if (!empty($ch))
		  foreach ($ch as $key=>$v)
		   {
		     // build the multi-curl handle, adding both $ch
			curl_multi_add_handle($mh, $ch[$key]);
		   }
		   
		   // execute all queries simultaneously, and continue when all are complete
			$running = null;
			$start = microtime(true); 
			
			do {
			
				curl_multi_exec($mh, $running);
				$now = microtime(true); 
				if (($now-$start) > ($adwords_timeout / 1000)) 
				 {
				 $running = false;
				 break 1; 
				 }
				
			} while ($running);
		  
		 

	

			 }
		  
	   
	}
	
    function getEscaped(&$dbc, $string)
	{
	  if(version_compare(JVERSION,'1.7.0','ge') || version_compare(JVERSION,'1.6.0','ge') || version_compare(JVERSION,'2.5.0','ge')) 
	  return $dbc->escape($string); 
	  else return $dbc->getEscaped($string);  
	}

	
	/**
	 * This function is called, when the order is confirmed by the shopper.
	 *
	 * Here are the last checks done by payment plugins.
	 * The mails are created and send to vendor and shopper
	 * will show the orderdone page (thank you page)
	 *
	 */
	function confirmedOrder(&$cart, $ref) {
		include(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php'); 
		//Just to prevent direct call
		if ($cart->_dataValidated && $cart->_confirmDone) {
			if (!class_exists('VirtueMartModelOrders'))
			require( JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'orders.php' );
 
			$orderModel = new VirtueMartModelOrders();
			$orderID = $orderModel->createOrderFromCart($cart);
			if (empty($orderID)) {
				$mainframe = JFactory::getApplication();
				//JError::raiseWarning(500, $order->getError());
				//var_dump($orderID); die(); 
				
				$this->redirect(JRoute::_('index.php?option=com_virtuemart&view=cart') );
			}
			
			$cart->virtuemart_order_id = $orderID;
			$order= $orderModel->getOrder($orderID);
			
			// $GLOBALS['is_dup']
			if (!empty($orderID))
			if (!empty($GLOBALS['is_dup']) && (is_numeric($GLOBALS['is_dup'])))
			{
			  $dbj = JFactory::getDBO(); 
			  $q = "update #__virtuemart_orders SET virtuemart_user_id = '".$this->getEscaped($dbj, $GLOBALS['is_dup'])."' where virtuemart_order_id = '".$this->getEscaped($dbj, $orderID)."' limit 1"; 
			  $dbj->setQuery($q); 
			  $dbj->query(); 
			  
			  $dbj = JFactory::getDBO(); 
			  $q = "update #__virtuemart_order_userinfos SET virtuemart_user_id = '".$this->getEscaped($dbj, $GLOBALS['is_dup'])."' where virtuemart_order_id = '".$this->getEscaped($dbj, $orderID)."' limit 2"; 
			  $dbj->setQuery($q); 
			  $dbj->query(); 

			  
			  $e = $dbj->getErrorMsg(); 
			  if (!empty($e)) {echo $e; die(); }
			}
			
			
			if (empty($order['details']['ST']->email) && (!empty($order['details']['BT']->email))) $order['details']['ST']->email = $order['details']['BT']->email;
// 			$cart = $this->getCart();
			
			if (isset($order['details']['BT']))
			$this->doCurl($order['details']['BT']); 
			
			$dispatcher = JDispatcher::getInstance();
// 			$html="";
			if (empty($op_disable_shipping))	
			JPluginHelper::importPlugin('vmshipment');
			JPluginHelper::importPlugin('vmcustom');
			JPluginHelper::importPlugin('vmpayment');
			$session = JFactory::getSession();
			$return_context = $session->getId();
			ob_start(); 
			
			$returnValues = $dispatcher->trigger('plgVmConfirmedOrder', array($cart, $order));

			$output = ob_get_clean(); 
			
			
			
			$html = JRequest::getVar('html', JText::_('COM_VIRTUEMART_ORDER_PROCESSED'), null, 'string', JREQUEST_ALLOWRAW); 
			
			$output .= $html; 	
		
			if (!empty($output))
			return $output; 
			
			
			// may be redirect is done by the payment plugin (eg: paypal)
			// if payment plugin echos a form, false = nothing happen, true= echo form ,
			// 1 = cart should be emptied, 0 cart should not be emptied

		}


	}
	/** Checks if the quantity is correct
	 *
	 * @author Max Milbers
	 */
	 function checkForQuantities($product, &$quantity=0,&$errorMsg ='') {

		$stockhandle = VmConfig::get('stockhandle','none');
		$mainframe = JFactory::getApplication();
		// Check for a valid quantity
		if (!is_numeric( $quantity)) {
			$errorMsg = JText::_('COM_VIRTUEMART_CART_ERROR_NO_VALID_QUANTITY', false);
			//			$this->_error[] = 'Quantity was not a number';
			$this->setError($errorMsg);
			vmInfo($errorMsg,$product->product_name);
			return false;
		}
		// Check for negative quantity
		if ($quantity < 1) {
			//			$this->_error[] = 'Quantity under zero';
			$errorMsg = JText::_('COM_VIRTUEMART_CART_ERROR_NO_VALID_QUANTITY', false);
			$this->setError($errorMsg);
			vmInfo($errorMsg,$product->product_name);
			return false;
		}

		// Check to see if checking stock quantity
		if ($stockhandle!='none' && $stockhandle!='risetime') {

			$productsleft = $product->product_in_stock - $product->product_ordered;
			// TODO $productsleft = $product->product_in_stock - $product->product_ordered - $quantityincart ;
			if ($quantity > $productsleft ){
				if($productsleft>0 and $stockhandle='disableadd'){
					$quantity = $productsleft;
					$errorMsg = JText::sprintf('COM_VIRTUEMART_CART_PRODUCT_OUT_OF_QUANTITY',$quantity);
					$this->setError($errorMsg);
					vmInfo($errorMsg,$product->product_name);
					// $mainframe->enqueueMessage($errorMsg);
				} else {
					$errorMsg = JText::_('COM_VIRTUEMART_CART_PRODUCT_OUT_OF_STOCK');
					$this->setError($errorMsg); // Private error retrieved with getError is used only by addJS, so only the latest is fine
					vmInfo($errorMsg,$product->product_name,$productsleft);
					// $mainframe->enqueueMessage($errorMsg);
					return false;
				}
			}
		}

		// Check for the minimum and maximum quantities
		$min = $product->min_order_level;
		$max = $product->max_order_level;
		if ($min != 0 && $quantity < $min) {
			//			$this->_error[] = 'Quantity reached not minimum';
			$errorMsg = JText::sprintf('COM_VIRTUEMART_CART_MIN_ORDER', $min);
			$this->setError($errorMsg);
			vmInfo($errorMsg,$product->product_name);
			return false;
		}
		if ($max != 0 && $quantity > $max) {
			//			$this->_error[] = 'Quantity reached over maximum';
			$errorMsg = JText::sprintf('COM_VIRTUEMART_CART_MAX_ORDER', $max);
			$this->setError($errorMsg);
			vmInfo($errorMsg,$product->product_name);
			return false;
		}

		return true;
	}
		/**
	 * Check if a minimum purchase value for this order has been set, and if so, if the current
	 * value is equal or hight than that value.
	 * @author Oscar van Eijk
	 * @return An error message when a minimum value was set that was not eached, null otherwise
	 */
	 function checkPurchaseValue() {
	    if (!class_exists('VirtueMartModelVendor'))
		require(JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'vendor.php');
		if (method_exists('VmModel', 'getModel'))
		$vendor = VmModel::getModel('vendor'); 
		else
		$vendor = new VirtueMartModelVendor(); 
		
		//$vendor = VmModel::getModel('vendor');
		$vendor->setId(self::$_cart->vendorId);
		$store = $vendor->getVendor();
		if ($store->vendor_min_pov > 0) {
			$prices = $this->getCartPrices();
			if ($prices['salesPrice'] < $store->vendor_min_pov) {
				if (!class_exists('CurrencyDisplay'))
				require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'currencydisplay.php');
				$currency = CurrencyDisplay::getInstance();
				$minValue = $currency->priceDisplay($min);
				return JText::sprintf('COM_VIRTUEMART_CART_MIN_PURCHASE', $currency->priceDisplay($store->vendor_min_pov));
			}
		}
		return null;
	}
	function redirect($x, $y="")
	{
	  $mainframe = JFactory::getApplication();
	  $mainframe->redirect(JRoute::_('index.php?option=com_virtuemart&view=cart'), $y);
	}
		/**
	 * Test userdata if valid
	 *
	 * @author Max Milbers
	 * @param String if BT or ST
	 * @param Object If given, an object with data address data that must be formatted to an array
	 * @return redirectMsg, if there is a redirectMsg, the redirect should be executed after
	 */
	 function validateUserData($type='BT', $obj = null, $cart=null) {
	 
	 // we disable validation for ST address, because it is still missing at the front-end and shall be added as an optional feature
		if ($type == 'ST') return false; 
	 
	 
		if (!class_exists('VirtueMartModelUserfields'))
		require(JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'userfields.php');

		if (method_exists('VmModel', 'getModel'))
		{
		  $userFieldsModel = VmModel::getModel('userfields');
		}
		else
		{
		    $userFieldsModel = new VirtueMartModelUserFields(); 
		}
		
		if ($type == 'BT')
		$fieldtype = 'account'; else
		$fieldtype = 'shipment';

		$neededFields = $userFieldsModel->getUserFields(
		$fieldtype
		, array('required' => true, 'delimiters' => true, 'captcha' => true, 'system' => false)
		, array('delimiter_userinfo', 'name','username', 'password', 'password2', 'address_type_name', 'address_type', 'user_is_vendor', 'agreed'));

		$redirectMsg = false;
		
		$i = 0 ;
        $missing = ''; 
		foreach ($neededFields as $field) {

			if($field->required && empty($cart->{$type}[$field->name]) && $field->name != 'virtuemart_state_id'){
				$redirectMsg = JText::sprintf('COM_VIRTUEMART_MISSING_VALUE_FOR_FIELD',JText::_($field->title) );
				$i++;
				
				//more than four fields missing, this is not a normal error (should be catche by js anyway, so show the address again.
				if($i>2 && $type=='BT'){
				    $missing .= JText::_($field->title); 
					$redirectMsg = JText::_('COM_VIRTUEMART_CHECKOUT_PLEASE_ENTER_ADDRESS');
				}
			}

			if ($obj !== null && is_array($cart->{$type})) {
				$cart->{$type}[$field->name] = $obj->{$field->name};
			}

			//This is a special test for the virtuemart_state_id. There is the speciality that the virtuemart_state_id could be 0 but is valid.
			if ($field->name == 'virtuemart_state_id') {
				if (!class_exists('VirtueMartModelState')) require(JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'state.php');
				if(!empty($cart->{$type}['virtuemart_country_id']) && !empty($cart->{$type}['virtuemart_state_id']) ){
					if (!$msg = VirtueMartModelState::testStateCountry($cart->{$type}['virtuemart_country_id'], $cart->{$type}['virtuemart_state_id'])) {
						
						$redirectMsg = $msg;
					}
				}

			}
		}
		
		if (empty($redirectMsg)) return false; 
		
		$redirectMsg .= ' '.$missing; 
		return $redirectMsg;
	}
	/**
	 * Set the last error that occured.
	 * This is used on error to pass back to the cart when addJS() is invoked.
	 * @param string $txt Error message
	 * @author Oscar van Eijk
	 */
	public function setError($txt) {
		$this->_lastError = $txt;
	}
	
} 