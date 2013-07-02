<?php
/**
 * Overrided Cart View class for the One Page Checkout and Virtuemart 2
 * This is the main loader of the checkout view itself independent on user selected template in virtuemart
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
 * ORIGINAL LICENSE AND COPYRIGHT NOTICE
 *
 * View for the shopping cart, modified for One Page Checkout by RuposTel
 *
 * @package	VirtueMart
 * @subpackage
 * @author Max Milbers
 * @author Oscar van Eijk
 * @author RolandD
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: view.html.php 4999 2011-12-09 21:31:02Z Milbo $
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// Load the view framework
jimport('joomla.application.component.view');

require_once(JPATH_VM_ADMINISTRATOR.DS.'version.php'); 
require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'ajaxhelper.php'); 

if(!class_exists('VmView'))
{
if (file_exists(JPATH_VM_SITE.DS.'helpers'.DS.'vmview.php'))
require(JPATH_VM_SITE.DS.'helpers'.DS.'vmview.php');
else
require(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'overrides'.DS.'vmview.php');
}

//require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'helpers'.DS.'loader.php'); 
/**
 * View for the shopping cart
 * @package VirtueMart
 * @author Max Milbers
 * @author Patrick Kohl
 */
class VirtueMartViewCart extends VmView {
	
	public function display($tpl = null) {

			

	
		$mainframe = JFactory::getApplication();
		$pathway = $mainframe->getPathway();
		$document = JFactory::getDocument();

		$layoutName = $this->getLayout();
		if (!$layoutName)
		$layoutName = JRequest::getWord('layout', 'default');
		
		
		// extra layouts here
		if ($layoutName == 'blog') 
		{
			$layoutName = 'default'; 
			JRequest::setVar('layout', 'default'); 
		}
		
		
		
		$this->assignRef('layoutName', $layoutName);
		$format = JRequest::getWord('format');
		// if(!class_exists('virtueMartModelCart')) require(JPATH_VM_SITE.DS.'models'.DS.'cart.php');
		// $model = new VirtueMartModelCart;

		if (!class_exists('VirtueMartCart'))
		require(JPATH_VM_SITE . DS . 'helpers' . DS . 'cart.php');
		$cart = VirtueMartCart::getCart(false, true);
		$this->assignRef('cart', $cart);

		
		
		require_once(JPATH_OPC.DS.'helpers'.DS.'loader.php');  
			
			$VM_LANG = new op_languageHelper(); 
			$GLOBALS['VM_LANG'] = $VM_LANG;
			$OPCloader = new OPCloader; 
			$OPCloader->addtocartaslink($this); 
		
		$this->user = $OPCloader->getUser($cart); 
		
		//Why is this here, when we have view.raw.php
		if ($format == 'raw') {
			$cart->prepareCartViewData();
			JRequest::setVar('layout', 'mini_cart');
			$this->setLayout('mini_cart');
			$this->prepareContinueLink();
		}
		
		/*
	  if($layoutName=='edit_coupon'){

		$cart->prepareCartViewData();
		$this->lSelectCoupon();
		$pathway->addItem(JText::_('COM_VIRTUEMART_CART_OVERVIEW'),JRoute::_('index.php?option=com_virtuemart&view=cart'));
		$pathway->addItem(JText::_('COM_VIRTUEMART_CART_SELECTCOUPON'));
		$document->setTitle(JText::_('COM_VIRTUEMART_CART_SELECTCOUPON'));

		} else */
		if ($layoutName == 'select_shipment') {
			if (!class_exists('vmPSPlugin')) require(JPATH_VM_PLUGINS . DS . 'vmpsplugin.php');
			JPluginHelper::importPlugin('vmshipment');
			

			//$pathway->addItem(JText::_('COM_VIRTUEMART_CART_OVERVIEW'), JRoute::_('index.php?option=com_virtuemart&view=cart'));
			//$pathway->addItem(JText::_('COM_VIRTUEMART_CART_SELECTSHIPMENT'));
			//$document->setTitle(JText::_('COM_VIRTUEMART_CART_SELECTSHIPMENT'));
		} else if ($layoutName == 'select_payment') {

			/* Load the cart helper */
			//			$cartModel = $this->getModel('cart');

			//$pathway->addItem(JText::_('COM_VIRTUEMART_CART_OVERVIEW'), JRoute::_('index.php?option=com_virtuemart&view=cart'));
			//$pathway->addItem(JText::_('COM_VIRTUEMART_CART_SELECTPAYMENT'));
			//$document->setTitle(JText::_('COM_VIRTUEMART_CART_SELECTPAYMENT'));
		} else if ($layoutName == 'order_done') {

			$language = JFactory::getLanguage();
			$language->load('com_virtuemart', JPATH_SITE);

			$this->lOrderDone();

			$pathway->addItem(JText::_('COM_VIRTUEMART_CART_THANKYOU'));
			$document->setTitle(JText::_('COM_VIRTUEMART_CART_THANKYOU'));
		} else if ($layoutName == 'default') {
			$cart->virtuemart_shipmentmethod_id = 0; 
			$cart->virtuemart_paymentmethod_id = 0; 
			$cart->setCartIntoSession();
			$cart->prepareCartViewData();
			
			$cart->prepareAddressRadioSelection();

			$this->prepareContinueLink();
			$this->lSelectCoupon();
			$totalInPaymentCurrency =$this->getTotalInPaymentCurrency();
			if ($cart->getDataValidated()) {
				$pathway->addItem(JText::_('COM_VIRTUEMART_ORDER_CONFIRM_MNU'));
				$document->setTitle(JText::_('COM_VIRTUEMART_ORDER_CONFIRM_MNU'));
				$text = JText::_('COM_VIRTUEMART_ORDER_CONFIRM_MNU');
				$checkout_task = 'confirm';
			} else {
				$pathway->addItem(JText::_('COM_VIRTUEMART_CART_OVERVIEW'));
				$document->setTitle(JText::_('COM_VIRTUEMART_CART_OVERVIEW'));
				$text = JText::_('COM_VIRTUEMART_CHECKOUT_TITLE');
				$checkout_task = 'checkout';
			}
			$this->assignRef('checkout_task', $checkout_task);
			$this->checkPaymentMethodsConfigured();
			$this->checkShipmentMethodsConfigured();
			if ($cart->virtuemart_shipmentmethod_id) {
				$this->assignRef('select_shipment_text', JText::_('COM_VIRTUEMART_CART_CHANGE_SHIPPING'));
			} else {
				$this->assignRef('select_shipment_text', JText::_('COM_VIRTUEMART_CART_EDIT_SHIPPING'));
			}
			if ($cart->virtuemart_paymentmethod_id) {
				$this->assignRef('select_payment_text', JText::_('COM_VIRTUEMART_CART_CHANGE_PAYMENT'));
			} else {
				$this->assignRef('select_payment_text', JText::_('COM_VIRTUEMART_CART_EDIT_PAYMENT'));
			}
			
			if (!empty($cart) && (!empty($cart->pricesCurrency)))
			{
			$currencyDisplay = CurrencyDisplay::getInstance($cart->pricesCurrency);
			$this->assignRef('currencyDisplay', $currencyDisplay); 
			}
			
			if (!VmConfig::get('use_as_catalog')) {
				$checkout_link_html = '<a class="vm-button-correct" href="javascript:document.checkoutForm.submit();" ><span>' . $text . '</span></a>';
			} else {
				$checkout_link_html = '';
			}
			$this->assignRef('checkout_link_html', $checkout_link_html);
		}
		//dump ($cart,'cart');
		$useSSL = VmConfig::get('useSSL', 0);
		$useXHTML = true;
		$this->assignRef('useSSL', $useSSL);
		$this->assignRef('useXHTML', $useXHTML);
		$this->assignRef('totalInPaymentCurrency', $totalInPaymentCurrency);

		// @max: quicknirty
		$cart->setCartIntoSession();
		shopFunctionsF::setVmTemplate($this, 0, 0, $layoutName);
		
		// 		vmdebug('my cart',$cart);
		if (($layoutName == 'default') || ($layoutName == 'select_shipment') || ($layoutName=='select_payment')) 
		{
		 
		 $this->lSelectShipment();
		 $this->lSelectPayment();
		 

		 
		 $cart->prepareVendor();
		 $only_page = JRequest::getCmd('only_page', ''); 
		 $inside = JRequest::getCmd('insideiframe', ''); 
		 
		$url = JURI::base(true); 
		if (empty($url)) $url = '/'; 
		if (substr($url, strlen($url)-1)!=='/') $url .= '/'; 

		 
		 if (!empty($only_page) && (empty($inside))) 
		 {
		   echo '<iframe id="opciframe" src="'.JRoute::_($url.'index.php?option=com_virtuemart&view=cart&insideiframe=1&template=system').'" style="width: 100%; height: 2000px; margin:0; padding:0; border: 0 none;" ></iframe>'; 
		 }
		 if ($inside)
		 {
			$document->addStyleDeclaration('
			  body { width:95% !important; }
			  div#dockcart { display: none !important; }
			  '); 
		 }
		 $document->addStyleDeclaration('
			  
			  div#dockcart { display: none !important; }
			  '); 
		 @header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
		 @header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

		 
		 //$mainframe->redirect(JRoute::_('index.php?option=com_virtuemart&view=cart')); 
		 if (empty($only_page) || (!empty($inside)))
		 $this->renderOnepage($cart);
		 
		 $cart->virtuemart_shipmentmethod_id = 0; 
		 $cart->virtuemart_paymentmethod_id = 0; 
		 $cart->setCartIntoSession();
		}
		else
		{
		
		parent::display($tpl);
		}
	}
	
	public function renderOnepage(&$cart)
	{
	$mainframe =& JFactory::getApplication(); 
	$useSSL = VmConfig::get('useSSL', 0);
	$mainframe =& JFactory::getApplication(); 
	if ((!empty($useSSL)) && (!($_SERVER['HTTPS']=="on") || ($_SERVER['HTTPS']=="1")) && (empty($_SESSION['op_redirected'])))
	{
 
		$_SESSION['op_redirected'] = true;
		$mainframe->redirect(JRoute::_('index.php?option=com_virtuemart&view=cart', true, true));
		
	}
	unset($_SESSION['op_redirected']); 
	
	    $language = JFactory::getLanguage();
		$language->load('com_onepage', JPATH_SITE, 'en-GB', true);
		$language->load('com_onepage', JPATH_SITE, null, true);
		
	    //require_once(JPATH_VM_ADMINISTRATOR.DS.'version.php'); 
	    
		//$x = version_compare(vmVersion::$RELEASE, '2.0.0', '>'); 
	    //var_dump($x); die();
		// in j1.7+ we have a special case of a guest login where user is logged in (and does not know it) and the registration fields in VM don't show up
			
		   //here we decide if to unlog user before purchase if he is somehow logged
		   include(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php'); 
		   if ($unlog_all_shoppers)
		   {
		     	$currentUser =& JFactory::getUser();
				$uid = $currentUser->get('id');
				if (!empty($uid))
				 {
				   
				  
				   $mainframe->logout(); 
				 }

		   }
	       if (!empty($newitemid))
		   {
		     if (is_numeric($newitemid))
			  {
			   JRequest::setVar('Itemid', $newitemid); 
			   $GLOBALS['Itemid'] = $newitemid; 
			  }
		   }
       if (!defined('NO_SHIPTO'))
		define('NO_SHIPTO', $op_disable_shipto);
   
		   
			$this->cart = $cart;
			$OPCloader = new OPCloader; 
			$OPCloader->addtocartaslink($this); 
			$continue_link = $OPCloader->getContinueLink($this); 
		    if (empty($cart) || (empty($cart->products)))
			{
			  $tpla = array('continue_link' => $continue_link); 
			}
			else
			{
			
			require_once(JPATH_OPC.DS.'helpers'.DS.'loader.php');  
			
			$VM_LANG = new op_languageHelper(); 
			$GLOBALS['VM_LANG'] = $VM_LANG;
			
			
			
			
			if ($OPCloader->logged($cart))
			 {
			   	require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'controllers'.DS.'opc.php'); 
				$c = new VirtueMartControllerOpc(); 
				$c->setAddress($cart, true); 
			 }
			 
			
			
			$op_basket = $OPCloader->getBasket($this); 
			
			$op_coupon = $OPCloader->getCoupon($this);
			
			$op_userfields = $OPCloader->getBTfields($this); 
			if (empty($op_disable_shipping))
			{
			if (!$this->checkShipmentMethodsConfigured()) 
			$no_shipping = $op_disable_shipping; 
			else
			$no_shipping = true; 
			}
			else
			$no_shipping = 1; 
			
			$shipping_method_html = $OPCloader->getShipping($this, $cart); 
			
			if (empty($no_login_in_template))
			$registration_html = $OPCloader->getRegistrationHhtml($this);
			else $registration_html = ''; 
			
			$jsvalidator = $OPCloader->getJSValidatorScript($this); 
			$return_url = $OPCloader->getReturnLink($this); 
			$num = 0; 
			$op_payment = $OPCloader->getPayment($this, $num); 
			
			if (!empty($hide_payment_if_one) && ($num === 1))
			$force_hide_payment = true; 
			else $force_hide_payment = false; 
			
			
			
			$op_shipto = $OPCloader->getSTfields($this); 
			$op_formvars = $OPCloader->getFormVars($this).$jsvalidator;
			$op_userfields .= $op_formvars; 
			
			$js = $OPCloader->getJavascript($this); 

			$action_url = $OPCloader->getActionUrl($this); 
			

			$inside = JRequest::getCmd('insideiframe', ''); 
			
			$js .= "\n".' 
			if (typeof jQuery != \'undefined\' && (jQuery != null))
			{
			 jQuery(document).ready(function() { '; 
			 
			 if (!empty($inside)) $js .= "\n".' op_resizeIframe(); '."\n"; 
			 $js .= ' op_runSS(\'init\'); '."\n".' });
			}
			else
			 {
			   if ((typeof windows != \'undefined\') && (typeof window.addEvent != \'undefined\'))
			   {
			   window.addEvent(\'domready\', function() {
			   ';
			   if (!empty($inside)) $js .= ' op_resizeIframe(); '; 
			$js .= '
			      op_runSS(\'init\'); 
			    });
			   }
			   else
			   {
			     if(window.addEventListener){ // Mozilla, Netscape, Firefox
			window.addEventListener("load", function(){ op_runSS(\'init\', false, true, null ); }, false);
			 } else { // IE
			window.attachEvent("onload", function(){ op_runSS(\'init\', false, true, null); });
			 }
			   }
			 }
			 
			 '; 
			
			
			
			
			$document  =& JFactory::getDocument();
			$document->addCustomTag('<script type="text/javascript">'."\n".'//<![CDATA[  '."\n".$js."\n".'//]]> '."\n".'</script>');

			$op_login_t = ''; 
			$html_in_between = ''; 
			
			//$op_userfields = ''; 
			
			$shippingtxt = ''; 
			$chkship = ''; 
			
			
			
			$tos_required = $OPCloader->getTosRequired($this); 
			
			$op_tos = ''; 
			$extras = $OPCloader->getExtras($this); 
			$tos_con = $OPCloader->getTos($this); 
			$agreement_txt = ''; 
			$show_full_tos = $OPCloader->getShowFullTos($this); //VmConfig::get('oncheckout_show_legal_info', 0); 
			
			$agree_checked = intval(!$agreed_notchecked); 
			$intro_article = $OPCloader->getIntroArticle($this); 
			
			$html_in_between = ''; 
			$google_checkout_button = ''; 
			$paypal_express_button = ''; 
			$related_products = ''; 
			$onsubmit = $OPCloader->getJSValidator($this);
			$op_onclick = $onsubmit; 
			$ref = $this;
			$tos_link = $OPCloader->getTosLink($this); 
			$tpla = Array(
			"force_hide_payment" => $force_hide_payment, 
			"intro_article" => $intro_article, 
			"return_url" => $return_url, 
			"no_shipping" => $no_shipping,
			"op_onclick" => ' onclick="'.$onsubmit.'" ', 
			"no_shipto" => NO_SHIPTO, 
			"tos_required" => $tos_required,
			"op_userinfo_st" => "",
            "op_basket" => $op_basket,
            "op_coupon" => $op_coupon, 
            "html_in_between" => $html_in_between, 
            "continue_link" => $continue_link, 
            "op_login_t" => $op_login_t,
            "shipping_method_html" => $shipping_method_html,
            "op_userfields" => $op_userfields,
            "shippingtxt" => $shippingtxt,
            "chkship" => $chkship,
            "op_shipto" => $op_shipto,
            "op_tos" => $op_tos,
             "op_payment" => $op_payment,
             "tos_con" => $tos_con, 
             "agreement_txt" => $agreement_txt,
             "show_full_tos" => $show_full_tos,
             "google_checkout_button" => $google_checkout_button,
             "paypal_express_button" => $paypal_express_button,
             "related_products" => $related_products, 
             "registration_html" => $registration_html,
			 "onsubmit" => $onsubmit,
			 "tos_link" => $tos_link,

          
           ) ;
			}
			
			include_once(JPATH_OPC.DS.'helpers'.DS.'legacy_templates.php');  
			$this->script('onepage.js', 'components/com_onepage/assets/js/', false);
			
			// will not be included inside form
			if (!empty($extras)) echo $extras; 
			// newScript.onload=scriptLoaded;
			
			
	}
	public function logged($cart)
	{
	  //$OPCloader = new OPCloader; 
	  return OPCloader::logged($cart); 
	}
	public function renderMailLayout($doVendor=false) {
		if (!class_exists('VirtueMartCart'))
		require(JPATH_VM_SITE . DS . 'helpers' . DS . 'cart.php');

		$cart = VirtueMartCart::getCart(false);
		$this->assignRef('cart', $cart);
		$cart->prepareCartViewData();
		$cart->prepareMailData();

		if ($doVendor) {
			$this->subject = JText::sprintf('COM_VIRTUEMART_VENDOR_NEW_ORDER_CONFIRMED', $this->shopperName, $this->cart->prices['billTotal'], $this->order['details']['BT']->order_number);
			$recipient = 'vendor';
		} else {
			$this->subject = JText::sprintf('COM_VIRTUEMART_ACC_ORDER_INFO', $this->cart->vendor->vendor_store_name, $this->cart->prices['billTotal'], $this->order['details']['BT']->order_number, $this->order['details']['BT']->order_pass);
			$recipient = 'shopper';
		}
		$this->doVendor = true;
		if (VmConfig::get('order_mail_html'))
		$tpl = 'mail_html';
		else
		$tpl = 'mail_raw';
		$this->assignRef('recipient', $recipient);

		$vendorModel = $this->getModel('vendor');
		$this->vendorEmail = $vendorModel->getVendorEmail($cart->vendor->virtuemart_vendor_id);
		
		$this->layoutName = $tpl;
		$this->setLayout($tpl);
		parent::display();
	}

	private function prepareContinueLink() {
		// Get a continue link */
		$virtuemart_category_id = shopFunctionsF::getLastVisitedCategoryId();
		$categoryLink = '';
		if ($virtuemart_category_id) {
			$categoryLink = '&virtuemart_category_id=' . $virtuemart_category_id;
		}
		$continue_link = JRoute::_('index.php?option=com_virtuemart&view=category' . $categoryLink);

		$continue_link_html = '<a class="continue_link" href="' . $continue_link . '" >' . JText::_('COM_VIRTUEMART_CONTINUE_SHOPPING') . '</a>';
		$this->assignRef('continue_link_html', $continue_link_html);
		$this->assignRef('continue_link', $continue_link);
	}

	private function lSelectCoupon() {

		$this->couponCode = (isset($this->cart->couponCode) ? $this->cart->couponCode : '');
		$coupon_text = $this->cart->couponCode ? JText::_('COM_VIRTUEMART_COUPON_CODE_CHANGE') : JText::_('COM_VIRTUEMART_COUPON_CODE_ENTER');
		$this->assignRef('coupon_text', $coupon_text);
	}

	/*
	 * lSelectShipment
	* find al shipment rates available for this cart
	*
	* @author Valerie Isaksen
	*/

	function lSelectShipment() {
	  
	  if (!empty($op_delay_ship)) return;
	  if (!empty($op_disable_shipto)) return;
	  
	  $x = null; 
	  basketHelper::createDefaultAddress($x, $this->cart); 
	  // USPS returns redirect when no BT address is set here
	
		$found_shipment_method=false;
		$shipment_not_found_text = JText::_('COM_VIRTUEMART_CART_NO_SHIPPING_METHOD_PUBLIC');
		$this->assignRef('shipment_not_found_text', $shipment_not_found_text);

		$shipments_shipment_rates=array();
		if (!$this->checkShipmentMethodsConfigured()) {
			$this->assignRef('shipments_shipment_rates',$shipments_shipment_rates);
			$this->assignRef('found_shipment_method', $found_shipment_method);
			return;
		}
		$selectedShipment = (empty($this->cart->virtuemart_shipmentmethod_id) ? 0 : $this->cart->virtuemart_shipmentmethod_id);

		$shipments_shipment_rates = array();
		if (!class_exists('vmPSPlugin')) require(JPATH_VM_PLUGINS . DS . 'vmpsplugin.php');
		JPluginHelper::importPlugin('vmshipment');
		$dispatcher = JDispatcher::getInstance();
		$returnValues = $dispatcher->trigger('plgVmDisplayListFEShipment', array( $this->cart, $selectedShipment, &$shipments_shipment_rates));
		// if no shipment rate defined
		$found_shipment_method = false;
		foreach ($returnValues as $returnValue) {
			if($returnValue){
				$found_shipment_method = true;
				break;
			}
		}
		$shipment_not_found_text = JText::_('COM_VIRTUEMART_CART_NO_SHIPPING_METHOD_PUBLIC');
		$this->assignRef('shipment_not_found_text', $shipment_not_found_text);
		$this->assignRef('shipments_shipment_rates', $shipments_shipment_rates);
		$this->assignRef('found_shipment_method', $found_shipment_method);
		$x = null; 
		basketHelper::restoreDefaultAddress($x, $this->cart); 
		return;
	}

	/*
	 * lSelectPayment
	* find al payment available for this cart
	*
	* @author Valerie Isaksen
	*/

	private function lSelectPayment() {

		$payment_not_found_text='';
		$payments_payment_rates=array();
		if (!$this->checkPaymentMethodsConfigured()) {
			$this->assignRef('paymentplugins_payments', $payments_payment_rates);
			$this->assignRef('found_payment_method', $found_payment_method);
		}

		$selectedPayment = empty($this->cart->virtuemart_paymentmethod_id) ? 0 : $this->cart->virtuemart_paymentmethod_id;

		$paymentplugins_payments = array();
		if(!class_exists('vmPSPlugin')) require(JPATH_VM_PLUGINS.DS.'vmpsplugin.php');
		JPluginHelper::importPlugin('vmpayment');
		$dispatcher = JDispatcher::getInstance();
		$returnValues = $dispatcher->trigger('plgVmDisplayListFEPayment', array($this->cart, $selectedPayment, &$paymentplugins_payments));
		// if no payment defined
		$found_payment_method = false;
		foreach ($returnValues as $returnValue) {
			if($returnValue){
				$found_payment_method = true;
				break;
			}
		}

		if (!$found_payment_method) {
			$link=''; // todo
			$payment_not_found_text = JText::sprintf('COM_VIRTUEMART_CART_NO_PAYMENT_METHOD_PUBLIC', '<a href="'.$link.'">'.$link.'</a>');
		}

		$this->assignRef('payment_not_found_text', $payment_not_found_text);
		$this->assignRef('paymentplugins_payments', $paymentplugins_payments);
		$this->assignRef('found_payment_method', $found_payment_method);
	}

	private function getTotalInPaymentCurrency() {

		if (empty($this->cart->virtuemart_paymentmethod_id)) {
			return null;
		}

		if (!$this->cart->paymentCurrency or ($this->cart->paymentCurrency==$this->cart->pricesCurrency)) {
			return null;
		}

		$paymentCurrency = CurrencyDisplay::getInstance($this->cart->paymentCurrency);

		$totalInPaymentCurrency = $paymentCurrency->priceDisplay( $this->cart->pricesUnformatted['billTotal'],$this->cart->paymentCurrency) ;

		$cd = CurrencyDisplay::getInstance($this->cart->pricesCurrency);


		return $totalInPaymentCurrency;
	}

	private function lOrderDone() {
		$html = JRequest::getVar('html', JText::_('COM_VIRTUEMART_ORDER_PROCESSED'), 'post', 'STRING', JREQUEST_ALLOWRAW);
		$this->assignRef('html', $html);

		//Show Thank you page or error due payment plugins like paypal express
	}

	function checkPaymentMethodsConfigured() {
		if (!class_exists('VirtueMartModelPaymentmethod'))
		require(JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'paymentmethod.php');
		//For the selection of the payment method we need the total amount to pay.
		$paymentModel = new VirtueMartModelPaymentmethod();
		$payments = $paymentModel->getPayments(true, false);
		if (empty($payments)) {

			$text = '';
			if (!class_exists('Permissions'))
			require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'permissions.php');
			if (Permissions::getInstance()->check("admin,storeadmin")) {
				$uri = JFactory::getURI();
				$link = $uri->root() . 'administrator/index.php?option=com_virtuemart&view=paymentmethod';
				$text = JText::sprintf('COM_VIRTUEMART_NO_PAYMENT_METHODS_CONFIGURED_LINK', '<a href="' . $link . '">' . $link . '</a>');
			}
			
			
			if (!defined('COM_VIRTUEMART_NO_PAYMENT_METHODS_CONFIGURED'))
			{
			 
			 vmInfo('COM_VIRTUEMART_NO_PAYMENT_METHODS_CONFIGURED', $text);
			 define('COM_VIRTUEMART_NO_PAYMENT_METHODS_CONFIGURED', $text); 
			}
			

			$tmp = 0;
			$this->assignRef('found_payment_method', $tmp);

			return false;
		}
		return true;
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
			$this->assignRef('found_shipment_method', $tmp);

			return false;
		}
		return true;
	}

	function stylesheet($file, $path, $arg=array())
	{
	  $onlypage = JRequest::getCmd('only_page', ''); 
	  if (false)
	  if (!empty($onlypage))
	  {
	    echo '
		<script type="text/javascript">
		/* <![CDATA[ */
		 // content of your Javascript goes here
		 var headID = document.getElementsByTagName("head")[0];    
		 var cssNode = document.createElement(\'link\'); 
		 cssNode.type = \'text/css\';
		 cssNode.rel = \'stylesheet\';
		 cssNode.href = \''.$path.$file.'\';
		 cssNode.media = \'screen\';
		 headID.appendChild(cssNode);
		
		/* ]]> */
		</script>';

	  }
	  //else
	  JHTML::stylesheet($file, $path, $arg);
	}
	function script($file, $path, $arg, $onload="")
	{
	  $onlypage = JRequest::getCmd('only_page', ''); 
	  //if (false)
	  if (false)
	  if (!empty($onlypage))
	  {
	    echo '
		<script type="text/javascript">
		
		/* <![CDATA[ */
		// content of your Javascript goes here

		var headID = document.getElementsByTagName("head")[0];         
		var newScript = document.createElement(\'script\');
		newScript.type = \'text/javascript\';
		newScript.src = \''.$path.$file.'\'; ';
		if (!empty($onload))
		 {
		   echo ' newScript.onload = '.$onload.';'; 
		 }
		echo '
		headID.appendChild(newScript);
		
		/* ]]> */
		</script>';
		echo 'okhere';

	  }
	  //else
	  {
	  
	  JHTML::script($file, $path, $arg);
	  }
	}
}

class op_languageHelper 
{
 function _($val)
 {
   $v2 = str_replace('PHPSHOP_', 'COM_VIRTUEMART_', $val); 
   return JText::_($v2);  
 }
 function load($str='')
 {
 }
}
//no closing tag
if (!function_exists('mm_showMyFileName'))
{
function mm_showMyFileName()
{
}
}
if (!function_exists('vmIsJoomla'))
{
 function vmIsJoomla()
 {
   return false;
 }
}

