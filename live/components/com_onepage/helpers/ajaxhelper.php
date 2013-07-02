<?php
/* 
*
* @copyright Copyright (C) 2007 - 2012 RuposTel - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* One Page checkout is free software released under GNU/GPL and uses code from VirtueMart
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* 
*/

// load OPC loader
//require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'loader.php'); 

if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' ); 
class basketHelper
{

function getPaymentArray()
{
  		if(!class_exists('vmPSPlugin')) require(JPATH_VM_PLUGINS.DS.'vmpsplugin.php');
		JPluginHelper::importPlugin('vmpayment');
		if (!class_exists('VirtueMartModelPaymentmethod'))
		require(JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'paymentmethod.php');
		$paymentmodel = new VirtueMartModelPaymentmethod(); 
		$payments = $paymentmodel->getPayments(true, true); 
		
		return $payments; 
}

function getHtmlArray($payment, $shipping)
{

}

// converts a UTF8-string into HTML entities
//  - $utf8:        the UTF8-string to convert
//  - $encodeTags:  booloean. TRUE will convert "<" to "&lt;"
//  - return:       returns the converted HTML-string
function utf8tohtml($utf8, $encodeTags = true) {
    $result = '';
    for ($i = 0; $i < strlen($utf8); $i++) {
        $char = $utf8[$i];
        $ascii = ord($char);
        if ($ascii < 128) {
            // one-byte character
            $result .= ($encodeTags) ? htmlentities($char) : $char;
        } else if ($ascii < 192) {
            // non-utf8 character or not a start byte
        } else if ($ascii < 224) {
            // two-byte character
            $result .= htmlentities(substr($utf8, $i, 2), ENT_QUOTES, 'UTF-8');
            $i++;
        } else if ($ascii < 240) {
            // three-byte character
            $ascii1 = ord($utf8[$i+1]);
            $ascii2 = ord($utf8[$i+2]);
            $unicode = (15 & $ascii) * 4096 +
                       (63 & $ascii1) * 64 +
                       (63 & $ascii2);
            $result .= "&#$unicode;";
            $i += 2;
        } else if ($ascii < 248) {
            // four-byte character
            $ascii1 = ord($utf8[$i+1]);
            $ascii2 = ord($utf8[$i+2]);
            $ascii3 = ord($utf8[$i+3]);
            $unicode = (15 & $ascii) * 262144 +
                       (63 & $ascii1) * 4096 +
                       (63 & $ascii2) * 64 +
                       (63 & $ascii3);
            $result .= "&#$unicode;";
            $i += 3;
        }
    }
    return $result;
}

  /**
     * Sets a selected shipment to the cart
     *
     * @author Max Milbers
     */
    public function setshipment($virtuemart_shipmentmethod_id) {

	/* Get the shipment ID from the cart */
	//$virtuemart_shipmentmethod_id = JRequest::getInt('virtuemart_shipmentmethod_id', '0');
	if ($virtuemart_shipmentmethod_id) {
	    //Now set the shipment ID into the cart
	    $cart = VirtueMartCart::getCart();
	    if ($cart) {
		if (!class_exists('vmPSPlugin')) require(JPATH_VM_PLUGINS . DS . 'vmpsplugin.php');
		JPluginHelper::importPlugin('vmshipment');
		$cart->setShipment($virtuemart_shipmentmethod_id);
		$cart->setCartIntoSession();
		//Add a hook here for other payment methods, checking the data of the choosed plugin
		
		// ok, what if we have USPS which shows more then one option here?
		// 
		$_dispatcher = JDispatcher::getInstance();
		$_retValues = $_dispatcher->trigger('plgVmOnSelectCheck', array( $cart, $cart ));
		
		$dataValid = true;
		foreach ($_retValues as $_retVal) {
		    if ($_retVal === true ) {// Plugin completed succesfull; nothing else to do
			$cart->setCartIntoSession();
			return true; 
		    } else if ($_retVal === false ) {
			   return false;
		       //$mainframe = JFactory::getApplication();
		       //$mainframe->redirect(JRoute::_('index.php?option=com_virtuemart&view=cart&task=editshipment',$this->useXHTML,$this->useSSL), $_retVal);
			break;
		    }
		}

	    }
		
		
		
		return false;
	}
	//self::Cart();
    }
	function checkShipmentMethodsConfigured() {
		if (!class_exists('VirtueMartModelShipmentMethod'))
		require(JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'shipmentmethod.php');
		//For the selection of the shipment method we need the total amount to pay.
		$shipmentModel = new VirtueMartModelShipmentmethod();
		$shipments = $shipmentModel->getShipments();
		if (empty($shipments)) {

			$text = '';
			if (!class_exists('Permissions'))
			require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'permissions.php');
			if (Permissions::getInstance()->check("admin,storeadmin")) {
				$uri = JFactory::getURI();
				$link = $uri->root() . 'administrator/index.php?option=com_virtuemart&view=shipmentmethod';
				$text = JText::sprintf('COM_VIRTUEMART_NO_SHIPPING_METHODS_CONFIGURED_LINK', '<a href="' . $link . '">' . $link . '</a>');
			}
				
			if (!defined('COM_VIRTUEMART_NO_SHIPPING_METHODS_CONFIGURED'))
			{
			 vmInfo('COM_VIRTUEMART_NO_SHIPPING_METHODS_CONFIGURED', $text);
			 define('COM_VIRTUEMART_NO_SHIPPING_METHODS_CONFIGURED', $text); 
			}

			$tmp = 0;
			//$this->assignRef('found_shipment_method', $tmp);

			return false;
		}
		return true;
	}

function restoreDefaultAddress(&$ref, &$cart)
{
  if (!empty($GLOBALS['opc_cart_empty']))
   {
     $cart->BT = array(); 
	 return;
   }
  if (!empty($GLOBALS['opc_zip_empty'])) 
   {
     $cart->BT['zip'] = ''; 
   }
  if (!empty($GLOBALS['opc_country_empty']))
   {
     $cart->BT['virtuemart_country_id'] = ''; 
   }
  if (!empty($GLOBALS['opc_state_empty'])) 
  {
    $cart->BT['virtuemart_state_id'] = ''; 
  }
  
   if (!empty($GLOBALS['st_opc_cart_empty']))
   {
     $cart->ST = 0;
	 return;
   }
  if (!empty($GLOBALS['st_opc_zip_empty'])) 
   {
     if (is_array($cart->ST))
     $cart->ST['zip'] = ''; 
   }
  if (!empty($GLOBALS['st_opc_country_empty']))
   {
     if (is_array($cart->ST))
     $cart->ST['virtuemart_country_id'] = ''; 
   }
  if (!empty($GLOBALS['st_opc_state_empty'])) 
  {
    if (is_array($cart->ST))
    $cart->ST['virtuemart_state_id'] = ''; 
  }
  
}	



function createDefaultAddress(&$ref, &$cart)
{
  include(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php'); 
  require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'loader.php'); 
  $vendor = OPCloader::getVendorInfo($cart); 
  
  if (empty($cart->BT))
   {
     $cart->BT = array(); 
	 $GLOBALS['opc_cart_empty'] = true; 
   }
   if (empty($cart->BT['zip']))
    {
	  $GLOBALS['opc_zip_empty'] = true; 
	  if (!empty($op_default_zip))
	  $cart->BT['zip'] = $op_default_zip; 
	  else
	   {
	      if ($op_default_zip === 0)
		 $cart->BT['zip'] = ''; 
		 else
	     $cart->BT['zip'] = $vendor['zip']; 
	   }
	}
   if (empty($cart->BT['virtuemart_country_id']))
    {
	  $GLOBALS['opc_country_empty'] = true; 
	  // ok, here we decide on default country: 
	  if (!empty($default_shipping_country))
	  $cart->BT['virtuemart_country_id'] = $default_shipping_country; 
	  else
	  $cart->BT['virtuemart_country_id'] = $vendor['virtuemart_country_id']; 
	}
	
	
   
   
   if (empty($cart->BT['virtuemart_state_id']))
    {
	  $GLOBALS['opc_state_empty'] = true;
	  // this will set taxes to zero:
	  if (!empty($opc_usmode)) $cart->BT['virtuemart_state_id'] = ' '; 
	}
	
	// we need to check the ST address as well
	if (!empty($cart->ST))
	{
	   if (empty($cart->ST['zip']))
    {
	  $GLOBALS['st_opc_zip_empty'] = true; 
	  if (!empty($op_default_zip))
	  $cart->ST['zip'] = $op_default_zip; 
	  else
	   {
	      if ($op_default_zip === 0)
		 $cart->ST['zip'] = '';
		 else
	     $cart->ST['zip'] = $vendor['zip']; 
	   }
	}
   if (empty($cart->ST['virtuemart_country_id']))
    {
	  $GLOBALS['st_opc_country_empty'] = true; 
	  // ok, here we decide on default country: 
	  if (!empty($default_shipping_country))
	  $cart->ST['virtuemart_country_id'] = $default_shipping_country; 
	  else
	  $cart->ST['virtuemart_country_id'] = $vendor['virtuemart_country_id']; 
	}
	
	
   
   // we will not do the state for now
   if (empty($cart->ST['virtuemart_state_id']))
    {
	  $GLOBALS['st_opc_state_empty'] = true; 
	  if (!empty($opc_usmode)) $cart->ST['virtuemart_state_id'] = ' '; 
	}
	
	}

	
}


function getShippingArrayHtml(&$ref, &$cart, $ajax=false)
{
  include(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php'); 
      if ((!$ajax) && (!empty($op_delay_ship)))
	   {
	     return array('<input type="hidden" name="invalid_country" id="invalid_country" value="invalid_country" /><input type="hidden" name="virtuemart_shipmentmethod_id" checked="checked" id="shipment_id_0" value="choose_shipping" />');
	   }
	  $this->createDefaultAddress($ref, $cart); 
	  
	  $preselected = JRequest::getVar('virtuemart_shipmentmethod_id', ''); 
	  
      $found_shipment_method=false;
   
	   $shipment_not_found_text = JText::_('COM_VIRTUEMART_CART_NO_SHIPPING_METHOD_PUBLIC');
		

		$shipments_shipment_rates=array();
		
		include(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php'); 
		if (!$this->checkShipmentMethodsConfigured() || (!empty($op_disable_shipping))) {
			//define('NO_SHIPPING', '1'); 
			$this->restoreDefaultAddress($ref, $cart); 
			return array();
		}
		//
		
		
		$selectedShipment = (empty($cart->virtuemart_shipmentmethod_id) ? 0 : $cart->virtuemart_shipmentmethod_id);
		
		if (empty($selectedShipment) && (!empty($preselected))) $selectedShipment = $preselected; 
		
		$shipments_shipment_rates = array();
		if (!class_exists('vmPSPlugin')) require(JPATH_VM_PLUGINS . DS . 'vmpsplugin.php');
		JPluginHelper::importPlugin('vmshipment');
		$dispatcher = JDispatcher::getInstance();
		$returnValues = $dispatcher->trigger('plgVmDisplayListFEShipment', array( $cart, $selectedShipment, &$shipments_shipment_rates));
		// if no shipment rate defined
		$extraHtml = array(); 
		$found_shipment_method = false;
		foreach ($returnValues as $returnValue) {
			if($returnValue){
				$found_shipment_method = true;
				//$extraHtml[] = $returnValue;
				break;
			}
		}
	
	
		$shipment_not_found_text = JText::_('COM_VIRTUEMART_CART_NO_SHIPPING_METHOD_PUBLIC');
	
	
    $ret = '';
    if ($found_shipment_method) {

	
	    
	// if only one Shipment , should be checked by default
	$arr = array(); 
	    foreach ($shipments_shipment_rates as $shipment_shipment_rate) {
		 
		if (is_array($shipment_shipment_rate)) {
		    foreach ($shipment_shipment_rate as $shipment_shipment_rat) {
			$arr[] = $shipment_shipment_rat; 
			$ret .= $shipment_shipment_rat.'<br />';
		    }
		}
			

	    }
		
	 
    } else {
	 $shipment_not_found_text = $shipment_not_found_text.'<input type="hidden" name="invalid_country" id="invalid_country" value="invalid_country" /><input type="hidden" name="virtuemart_shipmentmethod_id" checked="checked" id="shipment_id_0" value="choose_shipping" />';
	 
    }
   
   $this->restoreDefaultAddress($ref, $cart); 
   if (!empty($arr)) return $arr; 
   else return array($shipment_not_found_text); 
}

// search value of a prop in temp
function getValFT($temp, $mustProp)
{
     // example data-usps='{"service":"Parcel Post","rate":15.09}'
	 // or id="xyz"
	  
	    $x5 = stripos($temp, $mustProp.'=');
		
	    if ($x5===false) return false; 
		
		$single = false;
		
		 
		   $x4 = stripos($temp, '"', $x5);
		   $x42 = stripos($temp, "'", $x5);
		   
		   if (($x42 !== false) && ($x4 !== false))
		   if ($x42 < $x4)
		   {
		    // we will start with ' instead of "
			$x4 = $x42; 
			$single = true; 
		   }
		   
		   // search for start and end by '
		   if ($single) 
		    {
			//$x4 = stripos($temp, "'", $x5);
			if ($x4 !== false)
			{
			  //$single = true; 
			  if ($single)
			  $x6e = basketHelper::strposall($temp, "'", $x4+1);
			  else $x6e = basketHelper::strposall($temp, '"', $x4+1);
			  
			  if (!empty($x6e))
			  foreach ($x6e as $x6test)
			   {
			     if (substr($temp, $x6test-1, 1)!=urldecode('%5C'))
				 {
				 $x6 = $x6test; 
				 break; 
				 }
			   }
			  //$x6 = stripos($temp, "'", $x4+1);
			}
			}
		   
		   if ($x4 === false) return ""; 
		   
		   // search for end by " 
		   if (!$single)
		   if (!isset($x6))
		   {
		     $x6e = basketHelper::strposall($temp, '"', $x4+1);
			  foreach ($x6e as $x6test)
			   {
			     if (substr($temp, $x6test-1, 1)!=urldecode('%5C'))
				 {
				 $x6 = $x6test; 
				 break; 
				 }
			   }
		     //$x6 = stripos($temp, '"', $x4+1);
		   }
		   if (!isset($x6)) 
		   {
		     return "";
		     echo $mustProp.' in: '.$temp.' '.$x4; 
		   }
		   if ($x6 === false) return ""; 
		   
		   $val = substr($temp, $x4+1, $x6-$x4-1); 
		   
		   return $val; 
		   
		 
	  
	  return false; 
}
// html = <input type="radio" value="123" name="myname" id="myid" />
// tagname = input
// mustIncl = myname
// mystProp = type
// mustVal = 123
// getProp = id
function getFT($html, $tagname, $mustIncl='', $mustProp='', $mustVal='', $ending='>', $getProp)
{
  $posa = basketHelper::strposall($html, $mustIncl); 
  $rev = strrev($html); 
  $len = strlen($html); 
  $ret = array(); 

//if ($mustIncl == 'usps_id_1')
{
   // $x = htmlentities($html); 
  
  
}
  
  if (!empty($posa))
  foreach ($posa as $x1)
  {
   $x2 = stripos($rev, strrev('<'.$tagname), $len-$x1); 
   $x2 = $len - $x2 - strlen('<'.$tagname) + 1; 
   
   if ($x2 < $x1)
   {
     
     
	 
	 // here we can search for /> or just > depending on what we need... 
	 $x3 = stripos($html, $ending, $x2); 
	 if ($x3 === false) continue; 
	 
	 // our search tag starts at $x2 and ends at $x3
	 $temp = substr($html, $x2, $x3-$x2); 
	
	

	 if (!empty($mustProp))
	  {
	     
	  	 $val = basketHelper::getValFT($temp, $mustProp); 
		 if ($val === false) continue; 
		 if (!empty($mustVal))
		 if ($val != $mustVal) continue; 

	  }
	  
	  $val = basketHelper::getValFT($temp, $getProp); 
	  if ($val !== false) 
	  {
	
	  $ret[] = $val; 
	  continue;
	  }
	  
	  
   }
   else
   continue;
  }
  if (empty($ret)) return false; 
  return $ret; 
  
}
function getFTArray($html, $tagname, $mustProp, $mustVal)
{
}
function getPaymentArrayHtml($cart2, $payment_array, $shipping_array)
{
		include(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php'); 
		  
		$preselected = JRequest::getVar('virtuemart_shipmentmethod_id', ''); 
		$default = array(); 
   		// if ($op_show_others) $vendor_freeshipping = 0;
   		
   		// $extHelper = new opExtension();
   		// $extHelper->runExt('setFreeShipping', '', '', $vars['country'], $vendor_freeshipping); 

		// coupon will get counted again
		
/*
		echo '-- Checkout Debug--<br />
		
	Subtotal: '.$order_subtotal.'<br />
	Taxable: '.$order_taxable.'<br />
	Payment Discount: '.$payment_discount.'<br />
	Coupon Discount: '.$coupon_discount.'<br />
	Shipping: '.$order_shipping.'<br />
	Shipping Tax : '.$order_shipping_tax.'<br />
	Tax : '.$order_tax.'<br />
	Order tax details: '.var_export($order_tax_details, true).'
	------------------------<br />
	Order Total: '.$order_total.'<br />
	----------------------------<br />' 
		;
*/



	///if(!class_exists('vmPSPlugin')) require(JPATH_VM_PLUGINS.DS.'vmpsplugin.php');
	
	
	
 
	//$this->setShipment(0); //$shipping_method->virtuemart_shipmentmethod_id); 
	$cart =& VirtueMartCart::getCart();
	
	// again and again we have to do overrides because VM team decides about private functions and properties
	
	//$cart->virtuemart_payment_method_id = $payment_id; 
	///if (!class_exists('vmPSPlugin')) require(JPATH_VM_PLUGINS . DS . 'vmpsplugin.php');
		
	$dispatcher = JDispatcher::getInstance();
	$prices = array(); 
	
	// renew parameters
	//For the selection of the shipment method we need the total amount to pay.
	$shipmentModel = new VirtueMartModelShipmentmethod();
	
	// the configuration is reloaded only when this function is called interanally
	// getPluginMethods which is called by FEdisplay method
	$html = ''; 
	

	
	 $calc = calculationHelper::getInstance();
	 $mainframe = Jfactory::getApplication();
	 $virtuemart_currency_id = $mainframe->getUserStateFromRequest( "virtuemart_currency_id", 'virtuemart_currency_id',JRequest::getInt('virtuemart_currency_id') );	 
	 $calc->setVendorCurrency($virtuemart_currency_id); 
	 
	 
    if (empty($shipping_array))
	{
	  $shipping_array = array(); 
	  $shipping_array[] = '<input type="hidden" name="virtuemart_shipmentmethod_id" checked="checked" id="shipment_id_0" value="0" />'; 
	  $cart->virtuemart_shipmentmethod_id = 0; 
	  
	}
	
	if (empty($payment_array)) 
	 {
	 $payment_a = new stdClass(); 
	 $payment_a->virtuemart_paymentmethod_id = 0; 
	 $payment_array[] = $payment_a; 
	 
	 }
	
	if (!class_exists('CurrencyDisplay')
		)require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'currencydisplay.php');
		$currencyDisplay = CurrencyDisplay::getInstance();
	 
	 
	foreach ($shipping_array as $shipping_method)
	foreach ($payment_array as $payment)
	{
	if (strpos($shipping_method, 'invalid_country')!==false) 
	{
	$cart->virtuemart_shipmentmethod_id = 0; 
	
	}
	if (strpos($shipping_method, 'virtuemart_shipmentmethod_id')===false) 
	{
	 $cart->virtuemart_shipmentmethod_id = 0; 
	}
	
	/*
	$x1 = strpos($shipping_method, 'shipment_id_'); 
	if ($x1 === false)
	 {
	  // ok, we have special shipping here such as USPS
	  $x1 = strpos($shipping_method, 'value="'); 
	 }
	$x2 = strpos($shipping_method, '"', $x1); 
	
	$shipmentid = substr($shipping_method, $x1+12, $x2-$x1-12); 
	*/
	
	
	//echo $id; die(); 
	$multishipmentid = array();
	
	if (!empty($shipping_method))
	{
	 $multishipmentid = $this->getFT($shipping_method, 'input', 'virtuemart_shipmentmethod_id', 'type', 'radio', '>', 'id');
	 //$idth = 'shipment_id_'.$shipmentid;   
	// $idth = $shipmentid;
	}
	else $idth = 'shipment_id_0';

	if (empty($multishipmentid))
	 {
	   $idth = 'shipment_id_0';
	   $multishipmentid[0] = $idth;
	 }
	 
	foreach ($multishipmentid as $shipmentid)
	{
	
	$idth = $shipmentid;
	
	$ida = $this->getFT($shipping_method, 'input', $shipmentid, 'type', 'radio', '>', 'value');
    $id = $ida[0];
	
	/*  support of multi shipping methods:
	$this->_usps_id = JRequest::getInt('usps');
	$this->_usps_name = JRequest::getVar('usps_name', '');
	$this->_usps_rate = JRequest::getVar('usps_rate', '');
	*/

	
	$payment_id = (int)$payment->virtuemart_paymentmethod_id; 
	
	
	$_REQUEST['virtuemart_shipmentmethod_id'] = $id; 
	
	$cart->automaticSelectedShipment = true; 
	$cart->automaticSelectedPayment = true; 
	
	$cart->automaticSelectedShipment = true; 
	$cart->automaticSelectedPayment = true; 
	$cart->setPaymentMethod($payment_id); 
	$cart->setShipment($id);
	$cart->virtuemart_shipmentmethod_id = $id; 
	$cart->virtuemart_paymentmethod_id = $payment_id; 
	
	
	
	//$cart = VirtueMartCart::getCart();
	// support for USPS: 
	if (stripos($shipping_method, 'usps_')!==false)
	 {
	   $dataa = $this->getFT($shipping_method, 'input', $shipmentid, 'type', 'radio', '>', 'data-usps');
	  
	   if (!empty($dataa))
	    {
		 
		   // example data-usps='{"service":"Parcel Post","rate":15.09}'
		  $data = @json_decode($dataa[0], true); 
		   
		  if (!empty($data))
		   {
		     JRequest::setVar('usps_name', (string)$data['service']); 
			 JRequest::setVar('usps_rate', (float)$data['rate']);
			
			 $html .= '<input type="hidden" name="'.$idth.'_extrainfo" value="'.base64_encode($dataa[0]).'"/>';
			 
			 
		   }
		}
	 }
	 // end support USPS
$session =& JFactory::getSession();
	 // support for UPS: 
	if (stripos($shipping_method, 'ups_')!==false)
	 {
	    if (empty($ups_saved_semafor))
	 {
	  $ups_saved = $session->get('ups_rates', null, 'vm');
	  $ups_saved_semafor = true; 
	 }
	 else
	 {
	   $session->set('ups_rates', $ups_saved, 'vm'); 
	 }
	 
	   $dataa = $this->getFT($shipping_method, 'input', $shipmentid, 'type', 'radio', '>', 'data-ups');
	  
	   if (!empty($dataa))
	    {
		 
		   // example data-usps='{"service":"Parcel Post","rate":15.09}'
		  $data = @json_decode($dataa[0], true); 
		   
		  if (!empty($data))
		   {
		     //JRequest::setVar('usps_name', (string)$data['service']); 
			 JRequest::setVar('ups_rate', (string)$data['id']);
			
			 $html .= '<input type="hidden" name="'.$idth.'_extrainfo" value="'.base64_encode($dataa[0]).'"/>';
			 
			 
		   }
		}
	 }
	 // end support UPS
	
	
	
	//$returnValues = $dispatcher->trigger('calculateSalesPrice',array(  &$cart, &$id, &$cart->priceUnformatted  ));
	
	$cart->setCartIntoSession();
	if (!class_exists('VirtueMartControllerCartOpc'))
	require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'overrides'.DS.'cartcontroller.php'); 
	$cartcontroller = new VirtueMartControllerCartOpc(); 
	// will trigger plugin code for the selected shipment
	$cartcontroller->setshipment($cart, $id, false, false); 
	
	if (empty($op_disable_shipping))
	{
	
	 $prices = $calc->calculateShipmentPrice($cart, $id, true);
	 
	}
	else
	 {
	   $cart->virtuemart_shipmentmethod_id = 0; 
	   //$calc->calculateShipmentPrice($cart, $id, false); 
	   //$calc->_cartPrices
	 }
	 
	$calc->calculatePaymentPrice($cart, $payment_id, true); 
	
	$prices = $calc->getCheckoutPrices(  $cart, false);
	
	if ($shipmentid == 'usps_id_1')
	 {
	 }
	
	
	if (empty($subtotal_price_display)) $subtotal_price_display = 'salesPrice'; 
	$order_subtotal = $prices[$subtotal_price_display]; 
	
	$order_subtotal = $currencyDisplay->convertCurrencyTo( $cart->paymentCurrency, $order_subtotal,false);
	
	$order_total = $prices['billTotal']; 
	$order_total = $currencyDisplay->convertCurrencyTo( $cart->paymentCurrency, $order_total,false);
	$order_tax = 0;
	
	if (empty($coupon_price_display)) $coupon_price_display = 'discountAmount'; 
	if (!empty($prices[$coupon_price_display]))
	$coupon_discount = $prices[$coupon_price_display]; 
	else $coupon_discount = 0; 
	
	$coupon_discount = $currencyDisplay->convertCurrencyTo( $cart->paymentCurrency, $coupon_discount,false);
	
	//var_dump($coupon_price_display); 
	//var_dump($coupon_discount); die();
	//if (!empty($prices['salesPriceCoupon'])) $coupon_discount += $prices['salesPriceCoupon']; 
	
	$order_shipping = $prices['salesPriceShipment']; 
	$order_shipping = $currencyDisplay->convertCurrencyTo( $cart->paymentCurrency, $order_shipping,false);
	
	
	if (empty($order_shipping)) 
	{
	 
	
	}
	
	// lets select a default shipping method: 
	if (!empty($id))
    if (empty($default))
    {
	  $default['id'] = $id; 
	  $default['shipmentid'] = $shipmentid; 
	  $default['price'] = $order_shipping; 
	}	
	else
	{
	  if ($preselected == $id)
	   {
	     // if we found the preselected, let's leave it there
	     $default['p'] = true; 
		 $default['id'] = $id; 
		 $default['shipmentid'] = $shipmentid; 
		 $default['price'] = $order_shipping; 
	   }
	  // if we haven't found the preselected, lets make the  cheapest not 0 to be selected
	  if (empty($default['p']))
	  {
	    if (($default['price'] > $order_shipping) && ((!empty($order_shipping)) || (empty($op_default_shipping_zero))))
		{
	     $default['id'] = $id;
		  $default['shipmentid'] = $shipmentid; 
		 $default['price'] = $order_shipping; 
		}
		
		
	  }
	}
	
	$payment_discount = (-1)*$prices['salesPricePayment']; 
	$payment_discount = $currencyDisplay->convertCurrencyTo( $cart->paymentCurrency, $payment_discount,false);
	$order_tax = $prices['billTaxAmount']; 
	$order_tax = $currencyDisplay->convertCurrencyTo( $cart->paymentCurrency, $order_tax,false);
	// ok, here we should reprocess the coupon
	
	$html .= '<input type="hidden" id="'.$idth.'_'.$payment_id.'_subtotal" value="'.$order_subtotal.'"/>';
	/*
	if (!empty($order_tax_details) && (empty($use_order_tax)))
	{
	foreach ($order_tax_details as $rate=>$tax)
	{
	 $html .= '<input type="hidden" name="'.$idth.'_'.$payment_id.'_tax" value="'.$rate.'|'.$tax.'"/>';
	 //else
	 //echo '<input type="hidden" name="'.$idth.'_'.$payment_id.'_tax" value="'.$rate.'|'.$order_tax.'"/>';
	}

	}
	else 
	*/
	{
//	echo 'tom ';die();
	$html .= '<input type="hidden" name="'.$idth.'_'.$payment_id.'_tax" value="|'.$order_tax.'"/>';
	}
	
	if (!empty($payment_discount))
	$html .= '<input type="hidden" id="'.$idth.'_'.$payment_id.'_payment_discount" value="'.$payment_discount.'"/>';
	else $html .= '<input type="hidden" id="'.$idth.'_'.$payment_id.'_payment_discount" value="0.00"/>';
	
	if (!empty($coupon_discount))
	$html .= '<input type="hidden" id="'.$idth.'_'.$payment_id.'_coupon_discount" value="'.$coupon_discount.'"/>';
	else $html .= '<input type="hidden" id="'.$idth.'_'.$payment_id.'_coupon_discount" value="0.00"/>';
	
	if (!empty($order_shipping))
	$html .= '<input type="hidden" id="'.$idth.'_'.$payment_id.'_order_shipping" value="'.$order_shipping.'"/>';
	else $html .= '<input type="hidden" id="'.$idth.'_'.$payment_id.'_order_shipping" value="0.00"/>';
	
	if (!empty($order_shipping_tax))
	$html .= '<input type="hidden" id="'.$idth.'_'.$payment_id.'_order_shipping_tax" value="'.$order_shipping_tax.'"/>';
	else $html .= '<input type="hidden" id="'.$idth.'_'.$payment_id.'_order_shipping_tax" value="0.00"/>';

	if (!empty($order_total))
	$html .= '<input type="hidden" id="'.$idth.'_'.$payment_id.'_order_total" value="'.$order_total.'"/>';
	else $html .= '<input type="hidden" id="'.$idth.'_'.$payment_id.'_order_total" value="0.00"/>';
	
   // input parameters $d, $_SESSION['coupon_discount'], global $discount_factor, $mosConfig_offset
   // returns array ['order_subtotal'] (calc_order_subtotal), ['order_taxable'] calc_order_taxable, 
   // ['payment_discount'] = get_payment_discount(...
   // $totals['coupon_discount'] = 0.00
   // $d['order_tax'] (rounded)
   // $d['order_shipping'] calc_order_shipping
   // $d['order_shipping_tax'] calc_order_shipping_tax
   // $d['order_total'] = $totals['order_total']
   // $d['order_tax'] *= $discount_factor (discount_factor is created in calc_order_tax)
   
   //$taxable = $ps_checkout2->calc_order_taxable($vars);
   
   //global $attach_javascript;
   //$extHelper = new opExtension();
   //$extHelper->runExt('run_per_payment', '', '', $attach_javascript, $vars, $payment_id, $shipping_method_id, $shipping_method); 
   
      

   
   //global $order_tax_details;
   //$order_tax_details = array();
   //$GLOBALS['order_tax_details']  = $order_tax_details;
   
   //$subtotal = $ps_checkout2->calc_order_subtotal($vars);
   // calc_order_subtotal($d) will create 
   // $d['order_subtotal_withtax'], $d['payment_discount'], 
   // $order_tax_details[$my_taxrate] if multiple taxes
   // return order_subtotal (without tax)
   
   // this is product tax without tax
   //$order_tax_details = array();
   
   
   //unset($order_tax_details);
   // input parameters for calc_order_tax are:
   // global $order_tax_details, $discount_factor
   // $d / $vars as direct parameter
   // $_SESSION['ps_vendor_id']
   // $_REQUEST['ship_to_info_id'] select state, country for tax 
   // $_SESSION['cart']
   // $_SESSION['coupon_discount']
   // $d['payment_discount']
   // $order_tax_details[$tax_rate] += ...
   // $auth ...
   // $d['order_subtotal_withtax']
   // $this->SHIPPING->get_tax_rate()
   // $this->SHIPPING->get_rate()
   // returns tax rounded to 2 decimals
   // discount_factor = discounted_total / $d['order_subtotal_withtax']
   // discounted_total = $d['order_subtotal_withtax'] - $_SESSION['coupon_discount'] - $d['payment_discount']
   
   //$tax = $ps_checkout2 -> calc_order_tax($taxable, $vars);
   //global $order_tax_details;
   
   //$_SESSION['coupon_discount'] = '-0';
   }
   
   // once more: 
   }
   unset($ke); unset($html2); 
   if (!empty($shipping_array))
   foreach ($shipping_array as $ke=>$html2)
    {
	if (strpos($html2, 'virtuemart_shipmentmethod_id')!==false)
	{
	     //$x = $this->getFT($html2, 'input', 'virtuemart_shipmentmethod_id', 'type', 'radio', '>', 'id');
	    
	     //$x1 = strpos($shipping_array[$ke], '<input'); 
		 //if ($x1 !== false) 
		 {
		 //$x2 = strpos($shipping_array[$ke], '>', $x1+1); 
		 //if ($x2 !== false) 
		 {
		 $tmp = $tmp2 = $shipping_array[$ke]; //substr($shipping_array[$ke], $x1, $x2); 
		 if (!empty($default))
		 $shipmentid = (string)$default['shipmentid']; 
		 else $shipmentid = ''; 
		 
		 if (strpos($tmp, '"'.$shipmentid.'"')!==false)
		 {
	     $tmp = str_replace('checked="checked"', '', $tmp); 
		 $tmp = str_replace('checked', '', $tmp); 
		 //virtuemart_shipmentmethod_id
	     $tmp = str_replace('name="virtuemart_shipmentmethod_id"', ' autocomplete="off" name="virtuemart_shipmentmethod_id"', $tmp); 
		 if (!empty($default))
		 {
		  
		  
	      $tmp = $this->str_replace_once('"'.$shipmentid.'"', '"'.$shipmentid.'" checked="checked" ', $tmp);
         }
		 }
		 $tmp = str_replace('name="virtuemart_shipmentmethod_id"', 'name="virtuemart_shipmentmethod_id" onclick="javascript:changeTextOnePage3(op_textinclship, op_currency, op_ordertotal);" ', $tmp);  
		 //if (strpos($tmp, 'shipment_id_'.$id.'"')!== false) $tmp.' ok sel ';
		 $shipping_array[$ke] = $tmp; //str_replace($shipping_array[$ke], $tmp, $shipping_array[$ke]);
		 
		  $x1 = strpos($shipping_array[$ke], '<input'); 
		  $x1a = $this->strposall($shipping_array[$ke], '<input'); 
		  if (!empty($x1a))
		  foreach ($x1a as $x1)
		  {
		   $x2 = strpos($shipping_array[$ke], '>', $x1+1); 
		   if ($x2 !== false)
		   {
		     if (substr($shipping_array[$ke], $x2-1, 1)!='/')
			  {
			    // fixed a bug in 2.0.87 !! otherwise the shipping method might be rendered incorrectly
			    $a1 = substr($shipping_array[$ke], 0, $x2); 
				$a2 = substr($shipping_array[$ke], $x2); 
			    $shipping_array[$ke] = $a1.'/'.$a2; 
				//var_dump($a1); die(); $a1.'/'.$a2; die('ok'); 
				
			  }
		   }
		   }
		  }
		  }
		 
	  }
		  $html .= $shipping_array[$ke].'<br />';
		 //echo 'sa:'.$shipping_array[$ke].'endsa';
		 
	   
	}
	
	
	if (strpos($html, 'checked')===false)
	 {
	   $html = $this->str_replace_once('"virtuemart_shipmentmethod_id"', '"virtuemart_shipmentmethod_id" checked="checked"', $html); 
	 }
	  
	  // ups end mod: 
	 if (!empty($ups_saved_semafor))
	   $session->set('ups_rates', $ups_saved, 'vm'); 
	 
   return $html;

}
// http://tycoontalk.freelancer.com/php-forum/21334-str_replace-only-once-occurence-only.html
function str_replace_once($needle , $replace , $haystack){ 
    // Looks for the first occurence of $needle in $haystack 
    // and replaces it with $replace. 
    $pos = strpos($haystack, $needle); 
    if ($pos === false) { 
        // Nothing found 
    return $haystack; 
    } 
    return substr_replace($haystack, $replace, $pos, strlen($needle)); 
}
  
function setCartAddress()
 {
   // lets create a new instance of the cart
   if (!class_exists('VirtueMartCart'))
	require(JPATH_VM_SITE . DS . 'helpers' . DS . 'cart.php');
	
   $cart =& VirtueMartCart::getCart(false, false);
   /*
   		if (!class_exists('VirtueMartModelUser'))
		require(JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'user.php');
		$usermodel = new VirtueMartModelUser();
		$usermodel->setCurrent();
		$user = $usermodel->getUser();
   */   
	//	$cart->BT['virtuemart_country_id'] = '100'; 
   
 }
 function getNextOrderId()
 {
         // get list of avaiable ship to countries from currier configuration
       $db =& JFactory::getDBO();
       $prefix = $db->getPrefix();
       $table = $prefix.'virtuemart_orders';
	
	$db->setQuery("show table status where name='".$table."'");
	$a = $db->loadObjectList();
	if (empty($a)) $next_order_id = rand(990000, 999999);
	else
	foreach ($a as $r)
	{
	if (isset($r) && ($r !== false))
	{
	$next_order_id = $r->Auto_increment;
//	echo $next_order_id; die();
	}
	else 
	$next_order_id = rand(90000, 100000);
	}
	return $next_order_id; 
	
	
 }

 function calculateShipping()
 {
   $cartData['shipmentName'] = JText::_('COM_VIRTUEMART_CART_NO_SHIPMENT_SELECTED');
	$cartPrices['shipmentValue'] = 0; //could be automatically set to a default set in the globalconfig
	$cartPrices['shipmentTax'] = 0;
	$cartPrices['shipmentTotal'] = 0;
	$cartPrices['salesPriceShipment'] = 0;
 }
 
function getShippingArray()
  {
   
    if (!class_exists('vmPSPlugin')) require(JPATH_VM_PLUGINS . DS . 'vmpsplugin.php');
	JPluginHelper::importPlugin('vmshipment');
	if (!class_exists('VirtueMartModelShipmentMethod'))
		require(JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'shipmentmethod.php');

	// lets create a new instance of the cart
	if (!class_exists('VirtueMartCart'))
		require(JPATH_VM_SITE . DS . 'helpers' . DS . 'cart.php');

	$cart =& VirtueMartCart::getCart(false, false);		
	if (!class_exists('VirtueMartModelShipmentMethod'))
		require(JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'shipmentmethod.php');
		//For the selection of the shipment method we need the total amount to pay.
		$shipmentModel = new VirtueMartModelShipmentmethod();
		$shipments = $shipmentModel->getShipments();
/*
		$q = "select * from #__virtuemart_userinfos as u, #__virtuemart_vmusers as v where v.virtuemart_vendor_id = '".$cart->vendorId."' and v.user_is_vendor = 1 and v.perms = 'admin' ";  
	$db =& JFactory::getDBO(); 
	$db->setQuery($q); 
	$vendorinfo = $db->loadAssoc(); 

	if (!class_exists('vmPSPlugin')) require(JPATH_VM_PLUGINS . DS . 'vmpsplugin.php');
			JPluginHelper::importPlugin('vmshipment');
			$dispatcher = JDispatcher::getInstance();
*/
	 return $shipments;	
	 foreach ($shipments as &$s)
	 {
	 
	
    
	
	$cartData['shipmentName'] = JText::_('COM_VIRTUEMART_CART_NO_SHIPMENT_SELECTED');
	$cartPrices['shipmentValue'] = 0; //could be automatically set to a default set in the globalconfig
	$cartPrices['shipmentTax'] = 0;
	$cartPrices['shipmentTotal'] = 0;
	$cartPrices['salesPriceShipment'] = 0;

	$savedc = $cart;
	
	$cart->virtuemart_shipmentmethod_id = $s->virtuemart_shipmentmethod_id;
	$cart->automaticSelectedShipment=true;
	$cart->setCartIntoSession();
	$returnValues = $dispatcher->trigger('calculateSalesPrice',array(  &$cart, &$s, &$cart->priceUnformatted  ));
    $found_shipment_method=false;
    $shipment_not_found_text = JText::_('COM_VIRTUEMART_CART_NO_SHIPPING_METHOD_PUBLIC');
	$shipments_shipment_rates=array();

	$s->op_prices = $cartPrices;
	$s->op_name = $cartData; 
	
	$cart = $savedc;
	   
	 
	 }
	 
	return $shipments; 
	 
	if (empty($shipments))
	{
	  // we have no shipping method avaiable
	  define('NO_SHIPPING', '0');    
	}
	
	
		$selectedShipment = (empty($cart->virtuemart_shipmentmethod_id) ? 0 : $cart->virtuemart_shipmentmethod_id);

		$shipments_shipment_rates = array();

		//JPluginHelper::importPlugin('vmshipment');
		$dispatcher =& JDispatcher::getInstance();
		
		$returnValues = $dispatcher->trigger('plgVmDisplayListFEShipment', array( $cart, $selectedShipment, &$shipments_shipment_rates));
		// if no shipment rate defined
		$found_shipment_method = false;
		foreach ($returnValues as $returnValue) {
			if($returnValue){
				$found_shipment_method = true;
				break;
			}
		}
		$shipment_not_found_text = JText::_('COM_VIRTUEMART_CART_NO_SHIPPING_METHOD_PUBLIC');
		return;
	
	
    $ret = '';
   


	    
	// if only one Shipment , should be checked by default
	    foreach ($shipments_shipment_rates as $shipment_shipment_rates) {
		if (is_array($shipment_shipment_rates)) {
		    foreach ($shipment_shipment_rates as $shipment_shipment_rate) {
			$ret .= $shipment_shipment_rate.'<br />';
		    }
		}
	    }
	 
   
	return $ret; 
  }
  /**
 * strposall
 *
 * Find all occurrences of a needle in a haystack
 *
 * @param string $haystack
 * @param string $needle
 * @return array or false
 */

function strposall($haystack,$needle, $offset = 0){
   
    $s=$offset;
    $i=0;
   
    while (is_integer($i)){
       
        $i = stripos($haystack,$needle,$s);
       
        if (is_integer($i)) {
            $aStrPos[] = $i;
            $s = $i+strlen($needle);
			
        }
    }
    if (isset($aStrPos)) {
        return $aStrPos;
    }
    else {
        return false;
    }
}
  
}