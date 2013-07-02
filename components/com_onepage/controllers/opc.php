<?php
/**
 * Controller for the OPC ajax and checkout
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
 */
if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' ); 
jimport('joomla.application.component.controller');
class VirtueMartControllerOpc extends JController {
     /**
     * Construct the cart
     *
     * @access public
     * @author Max Milbers
     */
    public function __construct() {
	parent::__construct();
	{
	    if (!defined('JPATH_OPC')) define('JPATH_OPC', JPATH_SITE.DS.'components'.DS.'com_onepage'); 
	    if (!class_exists('VirtueMartCart'))
		require(JPATH_VM_SITE . DS . 'helpers' . DS . 'cart.php');
	    if (!class_exists('calculationHelper'))
		require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'calculationh.php');
		
		
		
	}
	}
	
	function cart()
	{
	  $view = new VirtueMartViewCartopc(); 
	  $view->display(); 
	  
	}
	
	function tracker()
	{
	  
	  include(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'tracker'.DS.'tracker.php'); 
	  $mainframe = JFactory::getApplication();
	  $mainframe->close(); 
	}
	
	function getEscaped(&$dbc, $string)
	{
	  if(version_compare(JVERSION,'1.7.0','ge') || version_compare(JVERSION,'1.6.0','ge') || version_compare(JVERSION,'2.5.0','ge')) 
	  return $dbc->escape($string); 
	  else return $dbc->getEscaped($string);  
	}	
	function getEmail($id)
	{
	    $user =& JFactory::getUser();
		return $user->email; 
	    if(!class_exists('VirtuemartModelUser')) require(JPATH_VM_ADMINISTRATOR.DS.'models'.DS.'user.php');
	    $user = new VirtueMartModelUser;
		//$user->setCurrent();
		$d = $user->getUser();
		return $d->JUser->get('email');
	}
	function setAddress(&$cart, $ajax=false)
	{
	   $post = JRequest::get('post'); 
	   // add condtion for address selected after a/c creation..
		$user =& JFactory::getUser();
	   	if ($user->get('id') == NULL) {   
		if($_REQUEST['shiptoopen'] == true)	 
		$_SESSION['ship_select_on_reg']="ship_select";
	   }//end code address select
	   
	   $cart->prepareAddressDataInCart('BT', 1);
	 
	     if(!class_exists('VirtuemartModelUserfields')) require(JPATH_VM_ADMINISTRATOR.DS.'models'.DS.'userfields.php');
		 $corefields = VirtueMartModelUserfields::getCoreFields();
		 $fieldtype = 'BTaddress';
		 $userFields = $cart->$fieldtype;
		
		 $cart->prepareAddressDataInCart('ST', 1);
		 $fieldtype = 'STaddress';
		 $userFieldsst = $cart->$fieldtype;

		
		$db=&JFactory::getDBO();
		
		// we will populate the data for logged in users
		if (!empty($post['ship_to_info_id']))
		{
		 
		  // this part for registered users, let's retrieve the selected address
		  
		  $q = "select * from #__virtuemart_userinfos where virtuemart_userinfo_id = '".$this->getEscaped($db, $post['ship_to_info_id'])."' limit 0,1"; 

		  $db->setQuery($q); 
		  $res = $db->loadAssoc(); 
		  $err = $db->getErrorMsg(); 
		 
		  $user_id = $res['virtuemart_user_id']; 
		  JRequest::setVar('shipto', $_POST['ship_to_info_id']);
		  $cart->selected_shipto = $this->getEscaped($db, $post['ship_to_info_id']); 
		  
		  $email = $this->getEmail($user_id); 
		  if (!empty($email)) $post['email'] = $email; 
		  
		  if (false)
		  {
		  // updated on 4th may 2012
		  if (!empty($res))
		  foreach ($res as $k=>$line)
		   {
		     // rewrite POST only when empty
		     if (empty($post[$k]))
		     $post[$k] = $line; 
			 // we will also set this for rest of ajax
		   }
		   // the selected address is BT
		   // we need to set STsameAsBT
		   // delete any ST address if found
		   }
			 
			  foreach ($userFields['fields'] as $key=>$uf22)   
				{
				 // don't save passowrds
				 if (stripos($uf22['name'], 'password')) $post[$uf22['name']] = ''; 
				 
				 // POST['variable'] and POST['shipto_variable'] are prefered from database information
				 if (!empty($post[$uf22['name']]) || ((($res['address_type'] == 'ST') && (!empty($post['shipto_'.$uf22['name']])))))
					{
					    // if the selected address is ST, let's first checkout POST shipto_variable
						// then POST['variable']
						// and then let's insert it from the DB
					    if (($res['address_type'] == 'ST') && (!empty($post['shipto_'.$uf22['name']])))
						$address[$uf22['name']] = $post['shipto_'.$uf22['name']]; 
						else
						$address[$uf22['name']] = $post[$uf22['name']]; 
					}
					else
					{
					   if (!empty($res[$uf22['name']]))
					   $address[$uf22['name']] = $res[$uf22['name']]; 
					   else $address[$uf22['name']] = ''; 
					}
				}
				
		 // the selected is BT
		 if ($res['address_type'] == 'BT') 
		    {
			    $cart->STsameAsBT = 1; 
				$cart->BT = $address; 
				$cart->ST = 0; 
				/*
				 foreach ($res as $keybt2=>$val2)
				 {
				   $cart->BT = array(); 
				   $cart->BT[$keybt2] = $val2; 
				 }
				 */
				 return;
			}
			else 
			{
			 $cart->ST = $address; 
			 $cart->STsameAsBT = 0; 
			}
			
			// the selected address is not BT
			// we need to get a proper BT
			// and set up found address as ST
			if ((!$cart->STsameAsBT))
			{
				$q = "select * from #__virtuemart_userinfos where virtuemart_user_id = '".$this->getEscaped($db, $res['virtuemart_user_id'])."' and address_type = 'BT' limit 0,1"; 
				$db->setQuery($q); 
				$btres = $db->loadAssoc();

				 $cart->prepareAddressDataInCart('BT', 1);
				 $fieldtype = 'BTaddress';
				 $userFieldsbt = $cart->$fieldtype;
				foreach ($userFieldsbt['fields'] as $key=>$uf)   
				{
				 // POST['variable'] is prefered form userinfos.variable in db
				 $index = str_replace('shipto_', '', $uf['name']); 
				 if (!empty($post[$index]))
					{
						$address[$index] = $post[$index]; 
					}
					else
					{
					   $address[$index] = $btres[$index]; 
					}
				}
				$cart->BT = $address; 
				
				//var_dump($cart->BT); 
				//var_dump($cart->ST); 
				//die();
				 return;
			}
			
		}
		if (!empty($res)) return; 
		
		
		// unlogged users get data from the form BT address
		$stopen = JRequest::getVar('shiptoopen', 0); 
		if ($stopen == 'false') $stopen = 0; 
		
		if (empty($stopen)) 
		{
		$sa = JRequest::getVar('sa', ''); 
		if ($sa == 'adresaina') $stopen = 1; 
		}
		foreach ($userFields['fields'] as $key=>$uf33)   
		 {
		   if (!empty($post[$uf33['name']]))
		    {
			  $address[$uf33['name']] = $post[$uf33['name']]; 
			}
		 }
		 if (!empty($address))
		 foreach ($address as $ka => $kv)
		  {
		    if ($kv === 'false') $address[$ka] = false;
		  }
		  
		if ((empty($stopen) && $ajax))
		 {
		  if (!empty($address))
		  $cart->BT = $address; 
		 }
		 else
		 {
		  if (!empty($address) && ($ajax))
		  {
		  $cart->ST = $address; 
		  $cart->STsameAsBT = 0; 
		  }
		 }
		 
 // ST address for unlogged

		 $address = array(); 
		 foreach ($userFieldsst['fields'] as $key=>$uf44)   
		 {
		   if (!empty($post['shipto_'.$uf44['name']]) || (!empty($post[$uf44['name']])))
		    {
			  if (strpos($uf44['name'], 'shipto_')!==0)
			  $address[$uf44['name']] = $post['shipto_'.$uf44['name']]; 
			  else
			  $address[$uf44['name']] = $post[$uf44['name']]; 

			  if ($key != $uf44['name'])
			   {
			     // supports address['company_name'] = post['shipto_company_name']; 
			     $address[$key] = $post[$uf44['name']]; 
			   }
			  
			}
		 }
		 
		  foreach ($address as $ka => $kv)
		  {
		    if ($kv === 'false') $address[$ka] = false;
		  }
		 //var_dump($userFieldsst['fields']); 
		 //var_dump($post);
		 //var_dump($address); die(); 
		  if (!empty($address))
		  if (!empty($stopen))
		  {
		  $cart->ST = $address; 
		  $cart->STsameAsBT = 0; 
		  }
		  
		  if ((!$ajax) && (!empty($address)))
		  {
		  $cart->ST = $address; 
		  $cart->STsameAsBT = 0; 
		  }
		  if ((!$ajax) && (empty($address)))
		  {
		   // there is no ST info: 
		   $cart->ST = 0; 
		   $cart->STsameAsBT = 1; 
		  }
		 
		 
		  
		 // if we have the user unlogged, and he is using BT, but ST is not deleted from the cart:
		 if ($ajax)
		 if (((!$ajax) && (empty($address))) || (empty($stopen)))
		 {
		   $cart->ST = 0; 
		   $cart->STsameAsBT = 1; 
		 }
		 
		 

	}
	
	function setAddress2(&$cart)
	{
	  $address = array(); 
	  $address['virtuemart_country_id'] = JRequest::getInt('virtuemart_country_id', 0); 
	  $address['zip'] = JRequest::getVar('zip', ''); 
	  $address['virtuemart_state_id'] = JRequest::getInt('virtuemart_state_id', ''); 
	  $address['address_1'] = JRequest::getVar('address_1', ''); 
	  $address['address_2'] = JRequest::getVar('address_2', ''); 
	  $cart->ST = $address; 
	  // not used $ship_to_info_id = JRequest::getVar('ship_to_info_id'); 
	}	
	
	
	
	function checkout()
	{ 
		
	  include(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php'); 
	  // password modification in OPC
	  $pwd = JRequest::getVar('opc_password', '', 'post', 'string', JREQUEST_ALLOWRAW); 
	  if (!empty($pwd)) 
	   {
	     JRequest::setVar('password', $pwd); 
		 // raw
		 $_POST['password'] = $pwd; 
	   }
	   

	   /* ======== add by RCA chetnath pandey ========== */

			$sa = JRequest::getVar('sa'); 
			
			if ($sa == 'adresaina')
				$_SESSION['checkvalue'] = 1;
			else
			 {
				unset($_SESSION['myuserfield']['shipto_company']);
				unset($_SESSION['myuserfield']['shipto_first_name']);
				unset($_SESSION['myuserfield']['shipto_last_name']);
				unset($_SESSION['myuserfield']['shipto_address_1']);
				unset($_SESSION['myuserfield']['shipto_address_2']);
				unset($_SESSION['myuserfield']['shipto_city']);
				unset($_SESSION['myuserfield']['shipto_virtuemart_country_id']);
				unset($_SESSION['myuserfield']['shipto_virtuemart_state_id']);
				unset($_SESSION['myuserfield']['shipto_zip']);
				unset($_SESSION['myuserfield']['shipto_phone_1']);
			 }
		
	   /* ======== end by RCA ========== */

	  // register user first: 
	  $reg = JRequest::getVar('register_account'); 
	  if (empty($reg)) $reg = false; 
	  else $reg = true; 
	  
	  
	  // ENABLE ONLY BUSINESS REGISTRATION WHEN REGISTER_ACCOUNT IS SET AS BUSINESS FIELD
	  if ($reg)
	  if (in_array('register_account', $business_fields))
	   {
	     $is_business = JRequest::getVar('opc_is_business', 0); 
		 if (empty($is_business))
		  {
		    $reg = false;
		  }
	   }
  $is_business = JRequest::getVar('opc_is_business', 0); 
		 // we need to alter shopper group for business when set to: 
	     $is_business = JRequest::getVar('opc_is_business', 0); 
		 if (false)
		 {
		 if (!empty($is_business))
		  {
		    // we will differenciate between default and anonymous shopper group
			// default is used for non-logged users
			// anononymous is used for logged in users as guests

	  // let try to alter shopper group
	 // will be implemented later
	 
	 
	 if (!class_exists('VirtueMartModelShopperGroup'))
			    require( JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'shoppergroup.php' );
	 
	 
	 
	 $shoppergroupmodel = new VirtueMartModelShopperGroup(); 
	 // function appendShopperGroups(&$shopperGroups,$user,$onlyPublished = FALSE,$vendorId=1){
	 // remove previous: 
	 
	 $session = JFactory::getSession();
	 $shoppergroup_ids = $session->get('vm_shoppergroups_add',array(),'vm');
	 $shoppergroupmodel->removeSessionSgrps($shoppergroup_ids); 
	 $new_shoppergroups = array(); 
	 $new_shoppergroups[] = 6; 
	 $shoppergroupmodel->appendShopperGroups($new_shoppergroups, null); 
//appendShopperGroups
	    }
		else
		{
	 if (!class_exists('VirtueMartModelShopperGroup'))
			    require( JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'shoppergroup.php' );
	 
	 $shoppergroupmodel = new VirtueMartModelShopperGroup(); 
	 // function appendShopperGroups(&$shopperGroups,$user,$onlyPublished = FALSE,$vendorId=1){
	 // remove previous: 
	 $session = JFactory::getSession();
	 $shoppergroup_ids = $session->get('vm_shoppergroups_add',array(),'vm');
	 $shoppergroupmodel->removeSessionSgrps($shoppergroup_ids); 
	 $new_shoppergroups = array(); 
	 $new_shoppergroups[] = 5; 
	 $shoppergroupmodel->appendShopperGroups($new_shoppergroups, null); 
		  }
	  }
	 
	  // if we used just first name then create a full name by string separation: 
	  $fname = JRequest::getVar('first_name', ''); 
	  $lname = JRequest::getVar('last_name', ''); 
	  if (!empty($fname) && (empty($lname)))
	   {
	      $a = explode(' ', $fname); 
		  if (count($a)>1)
		   {
		     JRequest::setVar('first_name', $a[0]);
			  unset($a[0]); 
			  $lname = implode(' ', $a); 
			 JRequest::setVar('last_name', $lname); 
		   }
		  else 
		   {
		     // no last name
			 JRequest::setVar('last_name', '   '); 
		   }
	   }
	   $fname = JRequest::getVar('shipto_first_name', ''); 
	  $lname = JRequest::getVar('shipto_last_name', ''); 
	  if (!empty($fname) && (empty($lname)))
	   {
	      $a = explode(' ', $fname); 
		  if (count($a)>1)
		   {
		     JRequest::setVar('shipto_first_name', $a[0]);
			  unset($a[0]); 
			  $lname = implode(' ', $a); 
			 JRequest::setVar('shipto_last_name', $lname); 
		   }
		  else 
		   {
		     // no last name
			 JRequest::setVar('shipto_last_name', '   '); 
		   }
	   }
	  
	  $this->runExt(); 
	  
	  
	  
	  //if (!class_exists('VirtueMartControllerUser'))
	  //require_once(JPATH_SITE.DS.'components'.DS.'com_virtuemart'.DS.'controllers'.DS.'user.php'); 
	  //$userC = new VirtueMartControllerUser(); 
	  $cart =& VirtueMartCart::getCart(false);
	  $this->saveData($cart,$reg); 
	  
	  $cart->virtuemart_paymentmethod_id = JRequest::getInt('virtuemart_paymentmethod_id', '');
	  
	  if (!class_exists('VirtueMartControllerCartOpc'))
	  require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'overrides'.DS.'cartcontroller.php'); 
	  $cartcontroller = new VirtueMartControllerCartOpc(); 
	  
	  $cart->virtuemart_shipmentmethod_id = JRequest::getInt('virtuemart_shipmentmethod_id', ''); 
	  
	  $cartcontroller->setshipment($cart); 
	  
	  $cartcontroller->setpayment($cart); 
	 
	  // security: 
	  JRequest::setVar('html', ''); 
	 
 $this->setAddress($cart, false); 
	  $post = JRequest::get('post'); 
	  
	  require_once(JPATH_OPC.DS.'overrides'.DS.'cart_override.php'); 
	  require_once(JPATH_OPC.DS.'helpers'.DS.'loader.php'); 
	  
	  $OPCcheckout = new OPCcheckout($cart); 
	  $loader = new OPCloader(); 
	  
	  $obj = new stdClass; 
	  $obj->cart = $cart; 
	  
	  $tos_required = $loader->getTosRequired($obj); 
	  
	  
	
	  if ($tos_required)
	  {
	    if (!empty($post['tosAccepted']))
		{
	     $cart->tosAccepted = 1; 
		 $cart->BT['agreed'] = 1; 
		 if (!empty($cart->ST)) $cart->ST['agreed'] = 1; 
		 JRequest::setVar('agreed', 1); 
		 JRequest::setVar('shipto_agreed', 1); 
		}
		else
		{
		 
		}
	  }
	  else
	  {
	     JRequest::setVar('agreed', 1); 
		 JRequest::setVar('shipto_agreed', 1); 
		 JRequest::setVar('tosAccepted', 1); 
	  }
	  if (empty($cart->BT)) 
	   {
	   
	   }

	 if (!empty($op_no_display_name))
	 {
	   JRequest::setVar('name', JText::_('COM_VIRTUEMART_SHOPPER_FORM_ADDRESS_1'));
	   JRequest::setVar('shipto_name', JText::_('COM_VIRTUEMART_SHOPPER_FORM_ADDRESS_1'));
	 }
	   
	 // fix the customer comment
	 // $cart->customer_comment = JRequest::getVar('customer_comment', $cart->customer_comment);
	 $cc = JRequest::getVar('customer_comment', ''); 
	 $cc2 = JRequest::getVar('customer_note', '');
     
	 if (empty($cart->customer_comment)) $cart->customer_comment = $cc2.$cc;
	 else $cart->customer_comment = $cart->customer_comment.$cc2.$cc;
	 
	  JRequest::setVar('customer_comment', $cart->customer_comment); 	 
	  
	  
	  
	  $OPCcheckout->checkoutData($cart, $OPCcheckout); 
	  
	  
	  
	  
	  if ($cart->_dataValidated)
		{
	  $cart->_confirmDone = true;
	 // echo 'OK';
	   $output =  $OPCcheckout->confirmedOrder($cart, $this);
	  
		}
		else 
		{
			$mainframe = JFactory::getApplication();
			$mainframe->redirect(JRoute::_('index.php?option=com_virtuemart&view=cart'), JText::_('COM_VIRTUEMART_CART_CHECKOUT_DATA_NOT_VALID'));

		}
	
	  //$post = JRequest::get('post');
		$mainframe =& JFactory::getApplication();		  
	  	$pathway =& $mainframe->getPathway();
		$document = JFactory::getDocument();
	  
	//  $html = JRequest::getVar('html', JText::_('COM_VIRTUEMART_ORDER_PROCESSED'), 'post', 'STRING', JREQUEST_ALLOWRAW);
	  $pathway->addItem(JText::_('COM_VIRTUEMART_CART_THANKYOU'));
	  $document->setTitle(JText::_('COM_VIRTUEMART_CART_THANKYOU'));
	  $cart->setCartIntoSession(); 
	  // now the plugins should have already loaded the redirect html
	  // we can safely 
	  $virtuemart_order_id = $cart->virtuemart_order_id; 
	   
	      if ($virtuemart_order_id) {
			if (!class_exists('VirtueMartCart'))
			    require(JPATH_VM_SITE . DS . 'helpers' . DS . 'cart.php');
			// get the correct cart / session
			//$cart = VirtueMartCart::getCart();
			
			// send the email ONLY if payment has been accepted
			if (!class_exists('VirtueMartModelOrders'))
			    require( JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'orders.php' );
			$order = new VirtueMartModelOrders();
			$orderitems = $order->getOrder($virtuemart_order_id);
			if (method_exists($cart, 'sentOrderConfirmedEmail'))
			{
			  //$cart->sentOrderConfirmedEmail($orderitems);

			}
			//We delete the old stuff

			//$cart->emptyCart();
		    }

	   JRequest::setVar('view', 'cart'); 
	  $_REQUEST['view'] = 'cart'; 
	  $_POST['view'] = 'cart'; 
	  $_GET['view'] = 'cart'; 


	 
	  $view = $this->getView('cart', 'html');
	  $view->setLayout('order_done');
	  $view->assignRef('html', $output); 
	  JRequest::setVar('html', $output);  
	  //$view->html = $output; 
	  
	    // Display it all
	   $view->display();
	  
	}
	
	// support for non-standard extensions
	// will be changed in the future over OPC extension tab and API
	function runExt()
	{
	  
	// support for USPS: 
	$shipping_method = JRequest::getVar('saved_shipping_id', ''); 
	if (stripos($shipping_method, 'usps_')!==false)
	 {
	   $data = JRequest::getVar($shipping_method.'_extrainfo', ''); 
	   
	  
	  
	   if (!empty($data))
	    {
		  $data = @base64_decode($data);  
		
	   
		  // example data-usps='{"service":"Parcel Post","rate":15.09}'
		  $data = @json_decode($data, true); 
		   //var_dump($data); var_dump($shipmentid); //usps_id_0
		  if (!empty($data))
		   {
		     JRequest::setVar('usps_name', (string)$data['service']); 
			 JRequest::setVar('usps_rate', (float)$data['rate']);
			
			 
			 
			 
		   }
		}
	 }
	 // end support USPS
	 	// support for UPS: 
	$shipping_method = JRequest::getVar('saved_shipping_id', ''); 
	if (stripos($shipping_method, 'ups_')!==false)
	 {
	   $data = JRequest::getVar($shipping_method.'_extrainfo', ''); 
	   
	  
	  
	   if (!empty($data))
	    {
		  $data = @base64_decode($data);  
		
	   
		  // example data-usps='{"service":"Parcel Post","rate":15.09}'
		  $data = @json_decode($data, true); 
		  //{"id":"03","code3":"USD","rate":8.58,"GuaranteedDaysToDelivery":[]}
		   //var_dump($data); var_dump($shipmentid); //usps_id_0
		  if (!empty($data))
		   {
		     //JRequest::setVar('ups_name', (string)$data['service']); 
			 JRequest::setVar('ups_rate', $data['id']);
			
			 
			 
			 
		   }
		}
	 }
	 // end support UPS
	 // skipcart is not compatible, therefore don't use it: 
	 // $plugin =& JPluginHelper::getPlugin( 'system', 'vmskipcart' );
	 $session = JFactory::getSession();
	 $session->set('vmcart_redirect', true,'vmcart_redirect');
	 
	 
	}
	function opc()
	{
       /// load shipping here
	   $vars = JRequest::get('post'); 
	   
	   // custom tag test
	
	
	
	
	$doc =& JFactory::getDocument();
	$type = get_class($doc); 
	if ($type == 'JDocumentRAW')
	 {
	    //C:\Documents and Settings\Rupos\Local Settings\Temp\scp02371\srv\www\clients\client1\web90\web\vm2\components\com_onepage\overrides\
	    //require_once(JPATH_SITE.DS.'components'.DS.'com_onepage'.DS.'overrides'.DS.'opchtml.php'); 
		//JFactory::$instance = new JDocumentOpchtml(); 
		//JFactory::$document = new JDocumentOpchtml(); 
	    
	 }
	 /*
	$doc->addCustomTag = create_function('$string', 'return;');  
	$doc->addCustomTag( '<!-- This is a comment. -->' );
     */

   JRequest::setVar('virtuemart_currency_id', (int)JRequest::getVar('virtuemart_currency_id'));
   
   /* to test the currency: */
   $mainframe = Jfactory::getApplication();
   $virtuemart_currency_id = $mainframe->getUserStateFromRequest( "virtuemart_currency_id", 'virtuemart_currency_id',JRequest::getInt('virtuemart_currency_id') );
   
   //var_dump($virtuemart_currency_id); 
 
   
	// end custom tag test
	   $view = $this->getView('cart', 'html');
	    
	   if (!defined('JPATH_OPC')) define('JPATH_OPC', JPATH_SITE.DS.'components'.DS.'com_onepage'); 
	   require_once(JPATH_OPC.DS.'helpers'.DS.'loader.php'); 
	   require_once(JPATH_OPC.DS.'helpers'.DS.'ajaxhelper.php'); 
	   include(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php'); 
	   //die('ok');
	   $OPCloader = new OPCloader; 
	   $cart = VirtueMartCart::getCart(false);
	   $cart->paymentCurrency = $virtuemart_currency_id; 
	   $this->setAddress($cart, true); 
	 
	 
	   // US and Canada fix, show no tax for no state selected
	   if (!isset($cart->BT['virtuemart_state_id'])) $cart->BT['virtuemart_state_id'] = '00'; 
	   if (!empty($cart->ST))
	    {
		$cart->BT = $cart->ST; 
		if (!isset($cart->ST['virtuemart_state_id'])) $cart->ST['virtuemart_state_id'] = '00'; 
		}
	
	//var_dump($cart->BT); echo '<br /><br />'; var_dump($cart->ST); die(); 
	   $cart->prepareCartViewData();
	   $view->cart = $cart; 
	   $view->assignRef('cart', $cart); 
	   @header('Content-Type: text/html; charset=utf-8');
	   @header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
	   @header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
		
	   $shipping = $OPCloader->getShipping($view, $cart, true); 
	  
	   $return = array(); 
	   $return['shipping'] = $shipping; 
	   // get payment html
	   
	   $num = 0; 
	   $ph2 = $OPCloader->getPayment($view, $num); 
	   if (!empty($hide_payment_if_one) && ($num === 1))
	    {
		  $ph = '<div class="payment_inner_html" rel="force_hide_payments">'.$ph2;
		}
		else $ph = '<div class="payment_inner_html" rel="force_show_payments">'.$ph2;
	   $ph .= '</div>'; 
	   $return['payment'] = $ph;
	   
  $t = $return['shipping'].' '.$return['payment']; 
	   $t = str_replace('//<![CDATA[', '', $t); 
	   $t = str_replace('//]]> ', '', $t); 
	   $t = str_replace('<![CDATA[', '', $t); 
	   $t = str_replace(']]> ', '', $t); 
	   
	   $js = array(); 
	   if (strpos('<script', $t)!==false)
	    {
		   $xa = basketHelper::strposall($t, '<script'); 
		   foreach ($xa as $st)
		    {
			  // end of <script tag
			  $x1 = strpos($t, '>', $st+1); 
			  // end of </scrip tag
			  $x2 = strpos($t, '</scrip', $st+1); 
			  $js1 = substr($t, $x1+1, $x2-$x1-1); 
			  $js[] = $js1; 
		      	  
			}
		}
	   $return['javascript'] = $js; 
	   echo json_encode($return); 
	   //echo $shipping;
	   $cart->virtuemart_shipmentmethod_id = 0; 
	   $cart->virtuemart_paymentmethod_id = 0; 
	   $cart->setCartIntoSession();
	   
	  
	    
	  
	  
	   $mainframe = JFactory::getApplication();
	   // do not allow further processing
	   $mainframe->close(); 
	}
	/**
	 * Save the user info. The saveData function dont use the userModel store function for anonymous shoppers, because it would register them.
	 * We make this function private, so we can do the tests in the tasks.
	 *
	 * @author Max Milbers
	 * @author Valérie Isaksen
	 *
	 * @param boolean Defaults to false, the param is for the userModel->store function, which needs it to determin how to handle the data.
	 * @return String it gives back the messages.
	 */
	private function saveData($cart=false,$register=false, $disable_duplicit=false) {

	include(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'config'.DS.'onepage.cfg.php'); 
	
	$mainframe = JFactory::getApplication();
		$currentUser = JFactory::getUser();
		$msg = '';
		
		$data = JRequest::get('post');
		

		if (empty($data['address_type'])) $data['address_type'] = 'BT'; 
		$at = JRequest::getWord('addrtype');
		if (!empty($at))
		$data['address_type'] = $at; 

		
		$r = JRequest::getVar('register_account', ''); 
		if (!empty($r) || (VmConfig::get('oncheckout_only_registered', 0)))
		$register = true; 
		
// 		vmdebug('$currentUser',$currentUser);
		$this->addModelPath( JPATH_VM_ADMINISTRATOR.DS.'models' );
			
			//require_once(JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'overrides'.DS.'usermodel.php'); 
			
			$userModel = $this->getModel('user');


		if($currentUser->id!=0 || $register){
	
			//$userModel = new OPCUsermodel(); 
			
			if(!$cart){
				// Store multiple selectlist entries as a ; separated string
				if (key_exists('vendor_accepted_currencies', $data) && is_array($data['vendor_accepted_currencies'])) {
					$data['vendor_accepted_currencies'] = implode(',', $data['vendor_accepted_currencies']);
				}

				$data['vendor_store_name'] = JRequest::getVar('vendor_store_name','','post','STRING',JREQUEST_ALLOWHTML);
				$data['vendor_store_desc'] = JRequest::getVar('vendor_store_desc','','post','STRING',JREQUEST_ALLOWHTML);
				$data['vendor_terms_of_service'] = JRequest::getVar('vendor_terms_of_service','','post','STRING',JREQUEST_ALLOWHTML);
			}
			$data['user_is_vendor'] = 0; 

			
			
			
			//It should always be stored, stAn: it will, but not here
			if($currentUser->id==0 || (empty($data['ship_to_info_id']))){
			if (!empty($data['email']))
		if (empty($data['shipto_email'])) $data['shipto_email'] = $data['email']; 
		
		// check for duplicit registration feature
		if (($allow_duplicit) && (empty($disable_duplicit)))
		{
		  // set the username if appropriate
		  if (empty($data['username']))
			{
			  $username = $data['email']; 
			  $email = $data['email']; 
			}
			else 
			{
			$username = $data['username'];
			if (!empty($data['email'])) $email = $data['email']; 
			else 
			 {
			   // support for 3rd party exts
			   if (strpos($username, '@')!==false)
			    $email = $username; 
			 }
			}
			$db =& JFactory::getDBO(); 
			
			$q = "select * from #__users where email LIKE '".$this->getEscaped($db, $email)."' limit 0,1"; //or username = '".$db->escape($username)."' ";

			$db->setQuery($q); 
			$res = $db->loadAssoc(); 
			$is_dup = false; 
			if (!empty($res))
			 {
			 
			   //ok, the customer already used the same email address
			   $is_dup = true; 
			   $duid = $res['id']; 
			   $GLOBALS['is_dup'] = $duid; 
			   
			   
			   $data['address_type'] = 'BT';
			   $data['virtuemart_user_id'] = $duid; 
			   $data['shipto_virtuemart_user_id'] = $duid; 
			   $this->saveToCart($data);
			   // we will not save the user into the jos_virtuermart_userinfos
			   return true; 
			   
			   // ok, we've got a duplict registration here
			   
			   if (!empty($data['password']) && (!empty($data['username'])))
			    {
				 // if we showed the password fields, let try to log him in 
				 
				  // we can try to log him in if he entered password
				  $credentials = array('username'  => $username,
							'password' => $data['password']);
								
				// added by stAn, so we don't ge an error
				$options = array('silent' => true );
				$mainframe =& JFactory::getApplication(); 
				ob_start();

				$ret = $mainframe->login( $credentials, $options );
					ob_start(); 
					$mainframe->logout($user->id, $options); 
					ob_get_clean(); 
					$return = $mainframe->login($credentials, $options);

				
				$xxy = ob_get_clean();
				unset($xxy); 
				if ($ret === false)
				 {
				  // the login was not sucessfull
				 }
				 else
				 {
				  // login was sucessfull
				  $dontproceed = true; 
				 }

				}
				// did he check: shipping address is different?
			   $cart->prepareAddressDataInCart('BT', 1);
	 
				if(!class_exists('VirtuemartModelUserfields')) require(JPATH_VM_ADMINISTRATOR.DS.'models'.DS.'userfields.php');
				$corefields = VirtueMartModelUserfields::getCoreFields();
				$fieldtype = 'BTaddress';
				$userFields = $cart->$fieldtype;
				$cart->prepareAddressDataInCart('ST', 1);
				$fieldtype = 'STaddress';
				$userFieldsst = $cart->$fieldtype;
				
				if ((!empty($data['sa'])) && ($data['sa'] == 'adresaina'))
				{
				 // yes, his data are in the shipto_ fields
				 $address = array(); 
				 foreach ($data as $ksa=>$vsa)
				  {
				    if (strpos($ksa, 'shipto_')===0)
					$address[$ksa] = $vsa; 
				  }
				}
				else
				{
				 // load the proper BT address
				 $q = "select * from #__virtuemart_userinfos where virtuemart_user_id = '".$duid."' and address_type = 'BT' limit 0,1"; 
				 $db->setQuery($q); 
				 $bta = $db->loadAssoc(); 
				 if (!empty($bta))
				 {
				 $address = array(); 
				 // no, his data are in the BT address and therefore we need to copy them and set a proper BT address
				 foreach ($userFieldsst['fields'] as $key=>$uf)   
				  {
				   $uf['name'] = str_replace('shipto_', '', $uf['name']); 
				   // POST['variable'] is prefered form userinfos.variable in db
				   if (empty($bta[$uf['name']])) $bta[$uf['name']] = ''; 
					{
					  if (!isset($data[$uf['name']])) $data[$uf['name']] = ''; 
					  if (empty($data['address_type_name'])) $data['address_type_name'] = JText::_('COM_VIRTUEMART_ORDER_PRINT_SHIPPING_LBL');
					  if (empty($data['name'])) $data['name'] = $bta[$uf['name']];
					  JRequest::setVar('shipto_'.$uf['name'], $data[$uf['name']], 'post'); 
					  // this will set the new BT address in the cart later on and in the order details as well
					  if (!empty($bta[$uf['name']]))
					  JRequest::setVar($uf['name'], $bta[$uf['name']], 'post'); 
					  $address['shipto_'.$uf['name']] = $data[$uf['name']]; 
					}
					
				  }
				  }
				  }
				  // ok, we've got the ST addres here, let's check if there is anything similar
				  $q = "select * from #__virtuemart_userinfos where virtuemart_user_id = '".$duid."'"; 
				  $db->setQuery($q); 
				  $res = $db->loadAssocList(); 
				  $ign = array('virtuemart_userinfo_id', 'virtuemart_user_id', 'address_type', 'address_type_name', 'name', 'agreed', '', 'created_on', 'created_by', 'modified_on', 'modified_by', 'locked_on', 'locked_by');  
				  if (function_exists('mb_strtolower'))
				  $cf = 'mb_strtolower'; 
				  else $cf = 'strtolower'; 
				  $e = $db->getErrorMsg(); 
				  
				  if (!empty($res))
				  {
				  // user is already registered, but we need to fill some of the system fields
				  foreach ($res as $k=>$ad)
				   {
				     $match = false; 
				     foreach ($ad as $nn=>$val)
					  {
					    if (!in_array($nn, $ign))
						{
						  
						  
						  if (!isset($address['shipto_'.$nn])) $address['shipto_'.$nn] = ''; 
						  if ($cf($val) != $cf($address['shipto_'.$nn])) { $match = false; break; }
						  else { $match = true; 
						    $lastuid = $ad['virtuemart_userinfo_id']; 
							$lasttype = $ad['address_type']; 
						  }
						}
					  }
					  if (!empty($match))
					   {
					    // we've got a ST address already registered
						if ($lasttype == 'BT')
						 {
						   // let's set STsameAsBT
						   JRequest::setVar('sa', null); 
						   	
						   // we don't have to do anything as the same data will be saved
							
						   
						 }
						 else
						 {
						   
						   JRequest::setVar('shipto_virtuemart_userinfo_id', $lastuid);
						   $new_shipto_virtuemart_userinfo_id = $lastuid;
						   
						 }
						 break; 
					   }
					  
					  
				   }
				   
				   // the user is registered and logged in, but he wants to checkout with a new address. he might still be in the guest mode
				   
				   	if (empty($match) || (!empty($new_shipto_virtuemart_userinfo_id)))
					   {
					   
					     // we need to store it as a new ST address
						 $address['address_type'] = 'ST'; 
						 $address['virtuemart_user_id'] = $duid; 
						 $address['shipto_virtuemart_user_id'] = $duid; 
						 if (empty($new_shipto_virtuemart_userinfo_id))
						 {
						 $address['shipto_virtuemart_userinfo_id'] = 0; 
						 $address['shipto_virtuemart_userinfo_id'] = $this->OPCstoreAddress($address, $duid); 
						 // let's set ST address here
						 }
						 else 
						 $address['shipto_virtuemart_userinfo_id'] = $new_shipto_virtuemart_userinfo_id;


						 
						 if (!isset($address['agreed']))
						  {
						    $address['agreed'] = JRequest::getBool('agreed', 1); 
						  }
						// empty radios fix start
						//Notice: Undefined index:  name in /srv/www/clients/client1/web90/web/svn/2072/virtuemart/components/com_virtuemart/helpers/cart.php on line 1030
						//Notice: Undefined index:  agreed in /srv/www/clients/client1/web90/web/svn/2072/virtuemart/components/com_virtuemart/helpers/cart.php on line 1030
						//Notice: Undefined index:  myradio in /srv/www/clients/client1/web90/web/svn/2072/virtuemart/components/com_virtuemart/helpers/cart.php on line 1030
						//Notice: Undefined index:  testcheckbox in /srv/www/clients/client1/web90/web/svn/2072/virtuemart/components/com_virtuemart/helpers/cart.php on line 1030
						
						if(!class_exists('VirtueMartModelUserfields')) require(JPATH_VM_ADMINISTRATOR.DS.'models'.DS.'userfields.php' );
						$userFieldsModel = $this->getModel('userfields');
						$prefix = '';

						$prepareUserFieldsBT = $userFieldsModel->getUserFieldsFor('cart','BT');
						$prepareUserFieldsBT = $userFieldsModel->getUserFieldsFor('cart','ST');
						
						if (!empty($prepareUserFieldsBT))
						 foreach ($prepareUserFieldsBT as $fldb) {
						    $name = $fldb->name;
							
							
							if (!isset($btdata[$name]))
							{
							 $btdata[$name] = '';
							}

						  }
						  if (!empty($prepareUserFieldsST))
						  foreach ($prepareUserFieldsST as $flda)
						   {
						     $name = $flda->name;
						     // we need to add empty values for checkboxes and radios
							if (!isset($address['shipto_'.$name]))
							{
							 $address['shipto_'.$name] = '';
							}
						   }
						// empty radios fix end
						 $cart->saveAddressInCart($address, 'ST');
						 $btdata = JRequest::get('post'); 
						 $btdata['virtuemart_user_id'] = $duid;
						 $btdata['address_type'] = 'BT'; 
						 
						 if (!isset($btdata['agreed']))
						  {
						    $btdata['agreed'] = JRequest::getBool('agreed', 1); 
						  }
						 
						 
						 $cart->saveAddressInCart($btdata, 'BT');
						 
						 return;
					   }

				  
				 }
				 
				

				
				
			 }
			
			
		}
		if (empty($dontproceed))
		{
			if (empty($data['username']))
			{
			  $data['username'] = $data['email']; 
			}
			if (empty($data['password']) && (!VmConfig::get('oncheckout_show_register', 0)))
			{
			
			$data['password'] = $data['password2'] = uniqid(); 			
			}
			
			if (!empty($data['first_name']))
			$data['name'] = $data['first_name'].' '.$data['last_name']; 
			else
			if (!empty($data['last_name']))
			$data['name'] = $data['last_name']; 
			else $data['name'] = '   '; 
			
			if (empty($_POST['name']))
			 {
			   $_POST['name'] = $data['name']; 
			 }
			 // Bind the post data to the JUser object and the VM tables, then saves it, also sends the registration email
			if (empty($unlog_all_shoppers))
            $data['guest'] = 0; 
			
			$usersConfig = JComponentHelper::getParams( 'com_users' );
			if ($usersConfig->get('allowUserRegistration') != '0')
			{
			 $ret = $userModel->store($data);
			}
			else
			{
			  $ret['success'] = true; 
			  $user = JFactory::getUser();
			  $unlog_all_shoppers = true; 
			}
			$data['address_type'] = 'ST'; 
			
			// this gives error on shipping address save
			if ((!empty($data['sa'])) && ($data['sa'] == 'adresaina'))
			$userModel->storeAddress($data);
		
			$user = $ret['user']; 
			$ok = $ret['success']; 
			
			// we will not send this again
			if (empty($unlog_all_shoppers))
			if($currentUser->id==0){
				$msg = (is_array($ret)) ? $ret['message'] : $ret;
				$usersConfig = &JComponentHelper::getParams( 'com_users' );
				$useractivation = $usersConfig->get( 'useractivation' );
				
				
				
				
				if (is_array($ret) && $ret['success'] && !$useractivation) {
					// Username and password must be passed in an array
					$credentials = array('username' => $ret['user']->username,
			  					'password' => $ret['user']->password_clear
					);
					$options = array('silent' => true );
					
					$return = $mainframe->login($credentials, $options);
					// this part of code fixes the _levels caching issue on joomla 1.7 to 2.5
					ob_start(); 
					$mainframe->logout($user->id, $options); 
					ob_get_clean(); 
					$return = $mainframe->login($credentials, $options);
				}
			}
			}
		  }

		}
		
		$data['address_type'] = 'BT'; 
		$this->saveToCart($data);
		
		return $msg;
	}
	// this is an overrided function to support duplict emails
	// the orginal function was in: user.php storeAddress($data)
	function OPCstoreAddress($data, $user_id=0)
	{
		  //$user =JFactory::getUser();
		  $this->addModelPath( JPATH_VM_ADMINISTRATOR.DS.'models' );
		  $userModel = $this->getModel('user');

	      $userinfo   = $userModel->getTable('userinfos');
		  if($data['address_type'] == 'BT'){
			$userfielddata = VirtueMartModelUser::_prepareUserFields($data, 'BT');

			if (!$userinfo->bindChecknStore($userfielddata)) {
				vmError('storeAddress '.$userinfo->getError());
			}
		}
		// Check for fields with the the 'shipto_' prefix; that means a (new) shipto address.
		if(isset($data['shipto_virtuemart_userinfo_id'])){
			$dataST = array();
			$_pattern = '/^shipto_/';

			foreach ($data as $_k => $_v) {
				if (preg_match($_pattern, $_k)) {
					$_new = preg_replace($_pattern, '', $_k);
					$dataST[$_new] = $_v;
				}
			}

			$userinfo   = $userModel->getTable('userinfos');
			if(isset($dataST['virtuemart_userinfo_id']) and $dataST['virtuemart_userinfo_id']!=0){
				if(!class_exists('Permissions')) require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'permissions.php');
				if(!Permissions::getInstance()->check('admin')){

					$userinfo->load($dataST['virtuemart_userinfo_id']);
					/*
					$user = JFactory::getUser();
					if($userinfo->virtuemart_user_id!=$user->id){
						vmError('Hacking attempt as admin?','Hacking attempt');
						return false;
					}
					*/
				}
			}

			if(empty($userinfo->virtuemart_user_id)){
				if(isset($data['virtuemart_user_id'])){
					$dataST['virtuemart_user_id'] = $data['virtuemart_user_id'];
				} else {
					//Disadvantage is that admins should not change the ST address in the FE (what should never happen anyway.)
					$dataST['virtuemart_user_id'] = $user_id;
				}
			}

			$dataST['address_type'] = 'ST';
			$userfielddata = VirtueMartModelUser::_prepareUserFields($dataST, 'ST');

			if (!$userinfo->bindChecknStore($userfielddata)) {
				vmError($userinfo->getError());
			}
		}


		return $userinfo->virtuemart_userinfo_id;
		
	}
	function sendRegistrationMail($user)
	{
	
	  // Compile the notification mail values.
		$data = $user->getProperties();
		$config	= JFactory::getConfig();
		$data['fromname']	= $config->get('fromname');
		$data['mailfrom']	= $config->get('mailfrom');
		$data['sitename']	= $config->get('sitename');
		$data['siteurl']	= JUri::base();
		$usersConfig = &JComponentHelper::getParams( 'com_users' );
		$useractivation = $usersConfig->get( 'useractivation' );
		// Handle account activation/confirmation emails.
		if ($useractivation == 2)
		{
			// Set the link to confirm the user email.
			$uri = JURI::getInstance();
			$base = $uri->toString(array('scheme', 'user', 'pass', 'host', 'port'));
			$data['activate'] = $base.JRoute::_('index.php?option=com_users&task=registration.activate&token='.$data['activation'], false);

			$emailSubject	= JText::sprintf(
				'COM_USERS_EMAIL_ACCOUNT_DETAILS',
				$data['name'],
				$data['sitename']
			);

			$emailBody = JText::sprintf(
				'COM_USERS_EMAIL_REGISTERED_WITH_ADMIN_ACTIVATION_BODY',
				$data['name'],
				$data['sitename'],
				$data['siteurl'].'index.php?option=com_users&task=registration.activate&token='.$data['activation'],
				$data['siteurl'],
				$data['username'],
				$data['password_clear']
			);
		}
		elseif ($useractivation == 1)
		{
			// Set the link to activate the user account.
			$uri = JURI::getInstance();
			$base = $uri->toString(array('scheme', 'user', 'pass', 'host', 'port'));
			$data['activate'] = $base.JRoute::_('index.php?option=com_users&task=registration.activate&token='.$data['activation'], false);

			$emailSubject	= JText::sprintf(
				'COM_USERS_EMAIL_ACCOUNT_DETAILS',
				$data['name'],
				$data['sitename']
			);

			$emailBody = JText::sprintf(
				'COM_USERS_EMAIL_REGISTERED_WITH_ACTIVATION_BODY',
				$data['name'],
				$data['sitename'],
				$data['siteurl'].'index.php?option=com_users&task=registration.activate&token='.$data['activation'],
				$data['siteurl'],
				$data['username'],
				$data['password_clear']
			);
		} else {

			$emailSubject	= JText::sprintf(
				'COM_USERS_EMAIL_ACCOUNT_DETAILS',
				$data['name'],
				$data['sitename']
			);

			$emailBody = JText::sprintf(
				'COM_USERS_EMAIL_REGISTERED_BODY',
				$data['name'],
				$data['sitename'],
				$data['siteurl']
			);
		}

		// Send the registration email.
		$return = JUtility::sendMail($data['mailfrom'], $data['fromname'], $data['email'], $emailSubject, $emailBody);

	}
	
		/**
	 * This function just gets the post data and put the data if there is any to the cart
	 *
	 * @author Max Milbers
	 *
	 * this is from user model 
	 */
	function saveToCart($data){
		if(!class_exists('VirtueMartModelUserfields')) require(JPATH_VM_ADMINISTRATOR.DS.'models'.DS.'userfields.php' );
		$userFieldsModel = $this->getModel('userfields');
		
		$prepareUserFields = $userFieldsModel->getUserFieldsFor('cart',$data['address_type']);
						if (!empty($prepareUserFields))
						 foreach ($prepareUserFields as $fld) {
						    $name = $fld->name;
							// we need to add empty values for checkboxes and radios
							if ($data['address_type'] == 'ST')
							if (!isset($data['shipto_'.$name]))
							{
							 $data['shipto_'.$name] = '';
							}
							if ($data['address_type'] == 'BT')
							if (!isset($data[$name]))
							{
							 $data[$name] = '';
							}

						  }
 
		if(!class_exists('VirtueMartCart')) require(JPATH_VM_SITE.DS.'helpers'.DS.'cart.php');
		$cart = VirtueMartCart::getCart();
		$cart->saveAddressInCart($data, $data['address_type']);
		
		$sa = JRequest::getVar('sa', ''); 

		if ($sa == 'adresaina')
			$cart->saveAddressInCart($data, 'ST');
		else $cart->STsameAsBT = 1; 
			$cart->setCartIntoSession();
	}
	
	

    
}