<?php
 /**
 *
 @package Module Free Order for Joomla! 1.6
 * @link       http://www.test.com/
* @copyright (C) 2011- test rca
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
// no direct access
defined('_JEXEC') or die('Restricted access');
class modsalesrepOrderHelper
{
		
	function sendEmail($params)
	{
		global $mainframe;
		
		$post = JRequest::get('post');
		$config =& JFactory::getConfig();
		$jAp =& JFactory::getApplication();
		$user = JFactory::getUser();
    	$member_id = $user->id;
		$jApp = JFactory::getApplication();
		$session =& JFactory::getSession();
		$db = JFactory::getDBO();		
		$customer_id = $post['customer_name'];
		if($customer_id == "" && $post['select_user_id1'] !="")
		$customer_id = $post['select_user_id1'];
		
		$sales_reps_id = $post['sales_reps_id'];		
		
		/*if($post['user_classref'] != "")
		$user_classref = $post['user_classref'];
		else
		$user_classref = "inbound";*/
		
		$add_details = $post['user_address_detail'];
		$item_tax    = $post['item_total_tax'];
		
		$session->set('customer_id_session',$customer_id);
		$session->set('customer_addr_session',$add_details);
		$session->set('item_discout_total',$item_tax);

		//$chkbox = $post['chk'];
		$item_ids = $post['item_name'];
		$item_qty = $post['item_qty'];
		$item_discout = $post['item_discout'];
		
		$virtuemart_product_ids = "";
		
		$qty_array = array();
		$item_ids = array_diff($item_ids, array('0'));
		
		foreach($item_ids as $a => $b)
		{
		$virtuemart_product_ids .= $item_ids[$a].","; 
		$qty_array[] = array("item_id"=>$item_ids[$a],"item_qty"=>$item_qty[$a]);
		$discout_array[$item_ids[$a]]= $item_discout[$a]; 
		$discout_qty_array[$item_ids[$a]]= $item_discout[$a] / $item_qty[$a];
		}
		
		//$session->set('item_qty_relation',$qty_array);
		$_SESSION['item_qty_relation'] = $qty_array;
		
		$virtuemart_product_ids = substr($virtuemart_product_ids, 0, strlen($virtuemart_product_ids)-1); 
		
		$virtuemart_product_ids = explode(",", $virtuemart_product_ids);
		
		require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart'.DS.'helpers'.DS.'config.php');		
		require(JPATH_SITE . DS . 'components' . DS . 'com_virtuemart' . DS . 'helpers' . DS . 'cart.php');
		
			
		$cart123 = VirtueMartCart::getCart();
		$cart123->emptyCart();
		
		
		
		if($post['ClearOrderButton'] == "clearorder")
		{
			 $session =& JFactory::getSession();
			 $item_qty_relation       = '';
			 $customer_addr_session   = '';
			 $customer_id_session     = '';
			 $customer_id_session     = '';
			 $pre_user_id_sess        = '';
			 $orderID                 = '';
			 $pending_orders_products = '';
			 $edit_select_shipping_id = '';
			 $cc_card_number          = '';
			 $cc_expire_month_2       = '';
			 $cc_expire_year_2        = '';
			 $cc_cvv                  = '';
			 
			 $session->clear('item_qty_relation');
			 $session->set('item_qty_relation',$item_qty_relation);
			 $session->clear('customer_addr_session');
			 $session->set('customer_addr_session',$customer_addr_session);
			 $session->clear('customer_id_session');
			 $session->set('customer_id_session',$customer_id_session);			
			 $session->clear('pre_user_id_sess');
			 $session->set('pre_user_id_sess',$pre_user_id_sess);
			 $session->clear('orderID');
			 $session->set('orderID',$orderID);
			 $session->clear('pending_orders_products');
			 $session->set('pending_orders_products',$pending_orders_products);
			 $session->clear('edit_select_shipping_id');
			 $session->set('edit_select_shipping_id',$edit_select_shipping_id);
			 $session->set('cc_card_number',$cc_card_number);
			 $session->set('cc_expire_month_2',$cc_expire_month_2);
			 $session->set('cc_expire_year_2',$cc_expire_year_2);
			 $session->set('cc_cvv',$cc_cvv);
			 
			/*unset($_SESSION['item_qty_relation']);
			$_SESSION['item_qty_relation'] = "";
			unset($_SESSION['customer_addr_session']); 
			$_SESSION['customer_addr_session']='';
			unset($_SESSION['customer_id_session']);
			$_SESSION['customer_id_session']='';
			unset($_SESSION['pre_user_id_sess']);
			$_SESSION['pre_user_id_sess'] = '';
			unset($_SESSION['orderID']);
			$_SESSION['orderID']='';
			unset($_SESSION['pending_orders_products']);
			$_SESSION['pending_orders_products']='';
			unset($_SESSION['edit_select_shipping_id']);
			$_SESSION['edit_select_shipping_id']='';
			$_SESSION["cc_card_number"]		= '';
			$_SESSION["cc_expire_month_2"]	= '';
			$_SESSION["cc_expire_year_2"]	= '';
			$_SESSION["cc_cvv"]  			='';*/
			$jAp->redirect(JURI::root().'sales-orders/', '');
			return true;		
		}
		
		///set BillTo array in cart object.
		$user_detail="select * from #__virtuemart_userinfos as vu ,#__users as u  where vu.virtuemart_user_id = u.id AND  vu.virtuemart_user_id = '".trim($customer_id)."' AND vu.address_type = 'BT'";
		$db->setQuery($user_detail);
		$db->query();
		$user_info=$db->loadAssoc();
		$cart_BT = array();
		$cart_BT['company'] =$user_info['company'];
		$cart_BT['email'] =$user_info['email'];
		$cart_BT['title'] =$user_info['title'];
		$cart_BT['first_name'] =$user_info['first_name'];
		$cart_BT['middle_name'] =$user_info['middle_name'];
		$cart_BT['last_name'] =$user_info['last_name'];
		$cart_BT['address_1'] =$user_info['address_1'];
		$cart_BT['address_2'] =$user_info['address_2'];
		$cart_BT['zip'] = $user_info['zip'];
		$cart_BT['city'] =$user_info['city'];
		$cart_BT['virtuemart_country_id'] =$user_info['virtuemart_country_id'];
		$cart_BT['virtuemart_state_id'] =$user_info['virtuemart_state_id'];
		$cart_BT['phone_1'] =$user_info['phone_1'];
		$cart_BT['phone_2'] =$user_info['phone_2'];
		$cart_BT['fax'] =$user_info['fax'];
		
		$cart_BT_select_state="select `state_2_code` from #__virtuemart_states  where virtuemart_state_id='".$user_info['virtuemart_state_id']."'";
		$db->setQuery($cart_BT_select_state);
		$db->query();
		$cart_BT_state=$db->loadResult();
		
		$cart_BT_select_country="select country_3_code from #__virtuemart_countries  where virtuemart_country_id ='".$user_info['virtuemart_country_id']."'";
		$db->setQuery($cart_BT_select_country);
		$db->query();
		$cart_BT_country=$db->loadResult();	
		
		
		$cart123->BT = $cart_BT;
		///End code set BillTo array in cart object.
		
		///set ShipTo array in cart object.
		$st_user_detail="select * from #__virtuemart_userinfos as vu ,#__users as u  where vu.virtuemart_user_id = u.id AND  vu.virtuemart_user_id = '".trim($customer_id)."' AND vu.virtuemart_userinfo_id = '".trim($post['ship_to_info_id'])."'";
		$db->setQuery($st_user_detail);
		$db->query();
		$st_user_info=$db->loadAssoc();
		$cart_ST = array();
		$cart_ST['address_type_name'] = $st_user_info['address_type_name'];
		$cart_ST['company'] =$st_user_info['company'];
		$cart_ST['email'] =$st_user_info['email'];
		$cart_ST['title'] =$st_user_info['title'];
		$cart_ST['first_name'] =$st_user_info['first_name'];
		$cart_ST['middle_name'] =$st_user_info['middle_name'];
		$cart_ST['last_name'] =$st_user_info['last_name'];
		$cart_ST['address_1'] =$st_user_info['address_1'];
		$cart_ST['address_2'] =$st_user_info['address_2'];
		$cart_ST['zip'] = $st_user_info['zip'];
		$cart_ST['city'] =$st_user_info['city'];
		$current_user_country_id = $cart_ST['virtuemart_country_id'] =$st_user_info['virtuemart_country_id'];
		$cart_ST['virtuemart_state_id'] =$st_user_info['virtuemart_state_id'];
		$cart_ST['phone_1'] =$st_user_info['phone_1'];
		$cart_ST['phone_2'] =$st_user_info['phone_2'];
		$cart_ST['fax'] =$st_user_info['fax'];
		
		$cart_BT_select_state="select `state_2_code` from #__virtuemart_states  where virtuemart_state_id='".$st_user_info['virtuemart_state_id']."'";
		$db->setQuery($cart_BT_select_state);
		$db->query();
		$cart_ST_state=$db->loadResult();
		
		$cart_BT_select_country="select country_3_code from #__virtuemart_countries  where virtuemart_country_id ='".$st_user_info['virtuemart_country_id']."'";
		$db->setQuery($cart_BT_select_country);
		$db->query();
		$cart_ST_country=$db->loadResult();	
		
		if($post['ship_to_info_id'] != "")
		$cart123->ST = $cart_ST;
		else
		$cart123->ST = $cart_BT;
		
		//$cart123->pricesUnformatted['discountAmount'] = $cart123->pricesUnformatted['discountAmount'] - 30.00;
		///End code set ShipTo array in cart object.
		//print_r($cart123);die;
		if ($cart123)
		 {
			//$virtuemart_product_ids = array("1","2","3");
			$success = true;
			if ($cart123->add($virtuemart_product_ids,$success)) {
				$msg = JText::_('COM_VIRTUEMART_PRODUCT_ADDED_SUCCESSFULLY');
				$type = '';
			} else {
				$msg = JText::_('COM_VIRTUEMART_PRODUCT_NOT_ADDED_SUCCESSFULLY');
				$type = 'error';
			}
		}
		
		$usr = JFactory::getUser($customer_id);
		//$msg = $cart123->setCouponCode("test123");
		$prices = $cart123->getCartPrices();
		
				
		$cart123->setPaymentMethod("2");
		//$cart123->confirmedOrder2($cart123, $usr, $prices);		

		$discout_sub_total = (float)number_format($post['item_sub_discount'], 2, '.', '');
		
		$prices['salesPrice'] = $post['item_sub_total'];
		$prices['withTax'] = $post['item_total_amount'];
		$prices['billTotal'] = $post['item_total_amount'];
		
		$cart123->pricesUnformatted['billTotal'] = $post['item_total_amount'] + $discout_sub_total;
		$cart123->pricesUnformatted['withTax'] = $post['item_total_amount'];
		$cart123->pricesUnformatted['discountAfterTax'] = $post['item_total_amount'];
	
		
		///add shipping price in total prices
		if($post['item_total_shipping'] != "0.00")
		{
		$prices['shipmentValue'] = $post['item_total_shipping'];
		$prices['shipmentTotal'] = $post['item_total_shipping'];		
		}
		
		
/////////============= payment by authorize.net ================/////////
		$check_payment= false;
		if($post['SaveOrderButton'] != "saveorder")
		{
		if($prices['billTotal'] > 0)
		{
			if($current_user_country_id == "38")
			{
			// $x_login = "8Vu75v8U6"; // Live
			// $x_tran_key = "8Uq22Ue3zvtK9P6a"; // Live
			$x_login = "6CQ58N4kyq"; // Sandbox
			$x_tran_key = "826hFG5mg5JgT73t"; // Sandbox
			}
			else
			{
			// $x_login = "6am2JHY95Dv"; // Live
			// $x_tran_key = "4B85434pY5u6Vrxg"; // Live
			$x_login = "7jg7QD4d"; // Sandbox
			$x_tran_key = "9nnF897564eP5aMx"; // Sandbox
			}
				
		//$post_url = "https://secure.authorize.net/gateway/transact.dll";
		$post_url = "https://test.authorize.net/gateway/transact.dll";
		$post_values = array(	
			"x_login"			=> $x_login,
			"x_tran_key"		=> $x_tran_key,
					
			"x_version"			=> "3.1",
			"x_delim_data"		=> "TRUE",
			"x_delim_char"		=> "|",
			"x_relay_response"	=> "FALSE",
		
			"x_type"			=> "AUTH_CAPTURE",
			"x_method"			=> "CC",
			"x_card_num"		=> $post['cc_card_number'],
			"x_exp_date"		=> $post['cc_expire_month'].$post['cc_expire_year'],
			'x_card_code'   	=> $post['cc_cvv'],
			
			// Email Settings
			'x_email' => $user_info['email'],
			'x_email_customer' => "TRUE",
			'x_merchant_email' => $config->getValue('config.email'),	
				
			"x_amount"			=> $prices["billTotal"],
			"x_description"		=> "Tile Redi Order Payment",
		
			"x_first_name"		=> $user_info['first_name'],
			"x_last_name"		=> $user_info['last_name'],
			"x_address"			=> $user_info['address_1'],
			'x_city' 			=> $user_info['city'],
			"x_state"			=> $cart_BT_state,
			"x_zip"				=> $user_info['zip'],
			'x_country' 		=> $cart_BT_country,
			'x_phone' 			=> $user_info['phone_1'],
			'x_fax' 			=> $user_info['fax'],
			
			// Customer Shipping Address
			'x_ship_to_first_name' => substr($cart_ST["first_name"], 0, 50),
			'x_ship_to_last_name' => substr($cart_ST["last_name"], 0, 50),
			'x_ship_to_company' => substr($cart_ST["company"], 0, 50),
			'x_ship_to_address' => substr($cart_ST["address_1"], 0, 60),
			'x_ship_to_city' => substr($cart_ST["city"], 0, 40),
			'x_ship_to_state' => substr($cart_ST_state, 0, 40),
			'x_ship_to_zip' => substr($cart_ST["zip"], 0, 20),
			'x_ship_to_country' => substr($cart_ST_country, 0, 60)
		);
		
		$post_string = "";
		foreach( $post_values as $key => $value )
		{ $post_string .= "$key=" . urlencode( $value ) . "&"; }
		$post_string = rtrim( $post_string, "& " );
		
		$request = curl_init($post_url); // initiate curl object
		curl_setopt($request, CURLOPT_HEADER, 0); // set to 0 to eliminate header info from response
		curl_setopt($request, CURLOPT_RETURNTRANSFER, 1); // Returns response data instead of TRUE(1)
		curl_setopt($request, CURLOPT_POSTFIELDS, $post_string); // use HTTP POST to send form data
		curl_setopt($request, CURLOPT_SSL_VERIFYPEER, FALSE); // uncomment this line if you get no gateway response.
		$post_response = curl_exec($request); // execute curl post and store results in $post_response
		curl_close ($request); // close curl object
		
		// This line takes the response and breaks it into an array using the specified delimiting character
		$response_array = explode($post_values["x_delim_char"],$post_response);
		
		
		$payment_msg = $response_array[3];
		// The results are output to the screen in the form of an html numbered list.
		foreach ($response_array as $value)
		{
			if($value == "This transaction has been approved.")
			$check_payment= true;
		}
		
		$authorizeNetResponse['response_code'] = $response_array[0];	
	    $authorizeNetResponse['response_subcode'] = $response_array[1];
	    $authorizeNetResponse['response_reason_code'] = $response_array[2];
	    $authorizeNetResponse['response_reason_text'] = $response_array[3];
	    $authorizeNetResponse['authorization_code'] = $response_array[4];
	    $authorizeNetResponse['avs_response'] = $response_array[5]; //Address Verification Service
	    $authorizeNetResponse['transaction_id'] = $response_array[6];
	    $authorizeNetResponse['invoice_number'] = $response_array[7];
	    $authorizeNetResponse['description'] = $response_array[8];
	    if ($check_payment) {
		$authorizeNetResponse['amount'] = $response_array[9];
		$authorizeNetResponse['method'] = $response_array[10];
		$authorizeNetResponse['transaction_type'] = $response_array[11];
		$authorizeNetResponse['customer_id'] = $response_array[12];
		$authorizeNetResponse['first_name'] = $response_array[13];
		$authorizeNetResponse['last_name'] = $response_array[14];
		$authorizeNetResponse['company'] = $response_array[15];
		$authorizeNetResponse['address'] = $response_array[16];
		$authorizeNetResponse['city'] = $response_array[17];
		$authorizeNetResponse['state'] = $response_array[18];
		$authorizeNetResponse['zip_code'] = $response_array[19];
		$authorizeNetResponse['country'] = $response_array[20];
		$authorizeNetResponse['phone'] = $response_array[21];
		$authorizeNetResponse['fax'] = $response_array[22];
		$authorizeNetResponse['email_address'] = $response_array[23];
		$authorizeNetResponse['ship_to_first_name'] = $response_array[24];
		$authorizeNetResponse['ship_to_last_name'] = $response_array[25];
		$authorizeNetResponse['ship_to_company'] = $response_array[26];
		$authorizeNetResponse['ship_to_address'] = $response_array[27];
		$authorizeNetResponse['ship_to_city'] = $response_array[28];
		$authorizeNetResponse['ship_to_state'] = $response_array[29];
		$authorizeNetResponse['ship_to_zip_code'] = $response_array[30];
		$authorizeNetResponse['ship_to_country'] = $response_array[31];
		$authorizeNetResponse['tax'] = $response_array[32];
		$authorizeNetResponse['duty'] = $response_array[33];
		$authorizeNetResponse['freight'] = $response_array[34];
		$authorizeNetResponse['tax_exempt'] = $response_array[35];
		$authorizeNetResponse['purchase_order_number'] = $response_array[36];
		$authorizeNetResponse['md5_hash'] = $response_array[37];
		$authorizeNetResponse['card_code_response'] = $response_array[38];
		$authorizeNetResponse['cavv_response'] = $response_array[39];  
		$authorizeNetResponse['account_number'] = $response_array[50];
		$authorizeNetResponse['card_type'] = $response_array[51];
		$authorizeNetResponse['split_tender_id'] = $response_array[52];
		$authorizeNetResponse['requested_amount'] = $response_array[53];
		$authorizeNetResponse['balance_on_card'] = $response_array[54];
	    }
		
		}
		
		
		}
		
		
		$key_val = array_keys($cart123->products);
		if ($check_payment)
		{
		for($p=0;$p<count($key_val);$p++)
			{		
			$cart123->pricesUnformatted[$key_val[$p]]['discountAmount'] = $cart123->pricesUnformatted[$key_val[$p]]['discountAmount']+$discout_qty_array[$key_val[$p]];
			$cart123->pricesUnformatted[$key_val[$p]]['salesPrice'] 	= $cart123->pricesUnformatted[$key_val[$p]]['salesPrice']-$discout_qty_array[$key_val[$p]];
			$cart123->pricesUnformatted[$key_val[$p]]['subtotal_discount'] =$cart123->pricesUnformatted[$key_val[$p]]['subtotal_discount']-$discout_array[$key_val[$p]];
			$cart123->pricesUnformatted[$key_val[$p]]['subtotal_with_tax'] =$cart123->pricesUnformatted[$key_val[$p]]['subtotal_with_tax']-$discout_array[$key_val[$p]];	
			}
		}
	
		$orderModel = VmModel::getModel('orders');	
			
		if (($orderID = $orderModel->_createOrder($cart123, $usr, $prices)) == 0) {
			echo 'Couldn\'t create order','Couldn\'t create order';			
		}
		if (!$orderModel->_createOrderLines($orderID, $cart123)) {
			echo 'Couldn\'t create order items','Couldn\'t create order items';			
		}
				
		$orderModel->_updateOrderHist($orderID);
		
		if (!$orderModel->_writeUserInfo($orderID, $usr, $cart123)) {
			echo 'Couldn\'t create order history','Couldn\'t create order history';
		}
		if (!$orderModel-> _createOrderCalcRules($orderID, $cart123) ) {
			echo 'Couldn\'t create order items','Couldn\'t create order items';			
		}
		
		
		if($orderID != '')
		{
		$db->setQuery("select order_number from #__virtuemart_orders where virtuemart_order_id = '".$orderID."'");
		$db->query();
		$current_order_number = $db->loadResult();
		
			for($q=0;$q<count($key_val);$q++)
			{
			$order_product_sku_id ='';
			$query = 'SELECT `product_sku` FROM `#__virtuemart_products` WHERE virtuemart_product_id= "'.$key_val[$q].'"';
			$db->setQuery($query);
			$order_product_sku_id = $db->loadResult();
				if($order_product_sku_id != "")
				{
				
				$sql='UPDATE `#__virtuemart_order_items` SET `qb_discount` = "'.$discout_array[$key_val[$q]].'"  where  virtuemart_order_id= "'.$orderID.'"  AND  order_item_sku = "'.$order_product_sku_id.'"';
				$db->setQuery($sql);
				$db->query();		
				}
				
			}		
		}
		
		$setSession = $session->get('orderID');
		
		//if($_SESSION['orderID'] == '')
		if(empty($setSession)) 
		{	
			$session->set('orderID',$orderID);
			//$_SESSION['orderID'] = $orderID;         
		}
		else 		
		{
				
		/*$query = 'SELECT `virtuemart_user_id` FROM `#__virtuemart_order_salerep` WHERE virtuemart_order_id= "'.$_SESSION['orderID'].'"';
		$db->setQuery($query);
		$order_commission_user_id = $db->loadResult();*/
		//$db->setQuery('DELETE FROM `#__virtuemart_order_salerep` where virtuemart_order_id= "'.$_SESSION['orderID'].'"');
		//$db->query();
		
		
		$db->setQuery('DELETE FROM `#__virtuemart_orders` where virtuemart_order_id= "'.$session->get('orderID').'"');
		$db->query();
		
		$db->setQuery('DELETE FROM `#__virtuemart_order_userinfos` where virtuemart_order_id= "'.$session->get('orderID').'"');
		$db->query();
		
		$db->setQuery('DELETE FROM `#__virtuemart_order_items` where virtuemart_order_id= "'.$session->get('orderID').'"');
		$db->query();		
		
		//$db->setQuery('DELETE FROM `#__virtuemart_order_salerep` where virtuemart_order_id= "'.$_SESSION['orderID'].'"');
		//$db->query();
		//$db->setQuery('UPDATE `#__virtuemart_order_salerep` SET `virtuemart_order_id` = "'.$_SESSION['orderID'].'" where virtuemart_order_id= "'.$orderID.'"');
		//$db->query();		
		
		
		$db->setQuery('UPDATE `#__virtuemart_orders` SET `virtuemart_order_id` = "'.$session->get('orderID').'" where virtuemart_order_id= "'.$orderID.'"');
		$db->query();
		
		$db->setQuery('UPDATE `#__virtuemart_order_userinfos` SET `virtuemart_order_id` = "'.$session->get('orderID').'" where virtuemart_order_id= "'.$orderID.'"');
		$db->query();
		
		$db->setQuery('UPDATE `#__virtuemart_order_items` SET `virtuemart_order_id` = "'.$session->get('orderID').'" where virtuemart_order_id= "'.$orderID.'"');
		$db->query();
		
		$db->setQuery('UPDATE `#__virtuemart_order_histories` SET `virtuemart_order_id` = "'.$session->get('orderID').'" where virtuemart_order_id= "'.$orderID.'"');
		$db->query();			
		
		$orderID = $session->get('orderID'); //$_SESSION['orderID'];
		//$_SESSION['orderID']="";	
		}
		
		
		$query = 'SELECT `virtuemart_user_id` FROM `#__virtuemart_order_salerep` WHERE virtuemart_order_id= "'.$orderID.'"';
		$db->setQuery($query);
		$order_commission_user_id = $db->loadResult();
		if($order_commission_user_id == '')
		{ //'.$user_classref.' save inbound values
			if($sales_reps_id == '')$sales_reps_id = $member_id;
			$db->setQuery('INSERT INTO #__virtuemart_order_salerep (id, virtuemart_order_id, virtuemart_user_id, user_classref,virtuemart_calc_id ) VALUES ( "", '.(int)$orderID.', '.(int)$sales_reps_id.',"inbound","'.$session->get('virtuemart_calc_id').'")');
			if (!$db->query()) {
				   echo $db->getErrorMsg();                       
			}		
			
		}
		/*else
		{
		$db->setQuery('UPDATE `#__virtuemart_order_salerep` SET `user_classref` = "'.$user_classref.'" where virtuemart_order_id= "'.$orderID.'"');
		$db->query();
				
		}*/
		
		$db->setQuery('UPDATE `#__virtuemart_orders` SET `customer_note` = "'.$post['sales_note'].'" where virtuemart_order_id= "'.$orderID.'"');
		$db->query();
		
		
/////////============= Code END payment by authorize.net ================/////////				
		if($check_payment)
		{
		
		//$order123 = $orderModel->getOrder($orderID);
		/*$cart123->virtuemart_order_id = $orderID;		
		$dispatcher = JDispatcher::getInstance();
		JPluginHelper::importPlugin('vmshipment');
		JPluginHelper::importPlugin('vmcustom');
		JPluginHelper::importPlugin('vmpayment');
		$returnValues = $dispatcher->trigger('plgVmConfirmedOrder', array($cart123, $order123));*/
			
		// set order status of orders
		$orderModel->_updateOrderHist($orderID,'C',0,'');
		$db->setQuery('UPDATE `#__virtuemart_orders` SET `order_status` = "C" where virtuemart_order_id= "'.$orderID.'"');
		$db->query();
		$db->setQuery('UPDATE `#__virtuemart_order_items` SET `order_status` = "C" where virtuemart_order_id = "'.$orderID.'"');
		$db->query();
		
		
		// send order notification to user
		$orderModel->notifyCustomer($orderID,0);
		
		///update  ekxob_virtuemart_order_userinfos table detail b'coz it's contain login user detail. 
		$user_info = array();
		$user_detail="select * from #__virtuemart_userinfos as vu ,#__users as u  where vu.virtuemart_user_id = u.id AND  vu.virtuemart_user_id = '".trim($customer_id)."' AND vu.address_type = 'BT'";
		$db->setQuery($user_detail);
		$db->query();
		$user_info=$db->loadAssoc();		
		if(count($user_info) > 0)
		{
		/*$db->setQuery('UPDATE `#__virtuemart_order_userinfos` SET `company` = "'.$user_info['company'].'", `title` = "'.$user_info['title'].'", `last_name` = "'.$user_info['last_name'].'",`first_name` = "'.$user_info['first_name'].'", `middle_name` = "'.$user_info['middle_name'].'", `phone_1` = "'.$user_info['phone_1'].'",`phone_2` = "'.$user_info['phone_2'].'", `fax` = "'.$user_info['fax'].'", `address_1` = "'.$user_info['address_1'].'", `address_2` = "'.$user_info['address_2'].'",`city` = "'.$user_info['city'].'", `virtuemart_state_id` = "'.$user_info['virtuemart_state_id'].'", `virtuemart_country_id` = "'.$user_info['virtuemart_country_id'].'",`zip`="'.$user_info['zip'].'",`email`="'.$user_info['email'].'",`created_by`="'.trim($customer_id).'",`modified_by`="'.trim($customer_id).'" WHERE `virtuemart_order_id` ="'.$orderID.'" AND `address_type`="BT" AND `virtuemart_user_id` ="'.trim($customer_id).'"');		
		$db->query();*/
		}
		
		$s_user_info = array();
		
		$s_user_detail="select * from #__virtuemart_userinfos as vu ,#__users as u  where vu.virtuemart_user_id = u.id AND  vu.virtuemart_user_id = '".trim($customer_id)."' AND vu.address_type = 'ST'";
		$db->setQuery($s_user_detail);
		$db->query();
		$s_user_info=$db->loadAssoc();		
		if(count($s_user_info) > 0)
		{
		/*$db->setQuery('UPDATE `#__virtuemart_order_userinfos` SET `company` = "'.$s_user_info['company'].'", `title` = "'.$s_user_info['title'].'", `last_name` = "'.$s_user_info['last_name'].'",`first_name` = "'.$s_user_info['first_name'].'", `middle_name` = "'.$s_user_info['middle_name'].'", `phone_1` = "'.$s_user_info['phone_1'].'",`phone_2` = "'.$user_info['phone_2'].'", `fax` = "'.$user_info['fax'].'", `address_1` = "'.$s_user_info['address_1'].'", `address_2` = "'.$s_user_info['address_2'].'",`city` = "'.$s_user_info['city'].'", `virtuemart_state_id` = "'.$s_user_info['virtuemart_state_id'].'", `virtuemart_country_id` = "'.$s_user_info['virtuemart_country_id'].'",`zip`="'.$s_user_info['zip'].'",`email`="'.$s_user_info['email'].'",`created_by`="'.trim($customer_id).'",`modified_by`="'.trim($customer_id).'" WHERE `virtuemart_order_id` ="'.$orderID.'" AND `address_type`="ST" AND `virtuemart_user_id` ="'.trim($customer_id).'"');		
		$db->query();*/
		}
		else
		{
		if(count($user_info) > 0){
		/*$db->setQuery('UPDATE `#__virtuemart_order_userinfos` SET `company` = "'.$user_info['company'].'", `title` = "'.$user_info['title'].'", `last_name` = "'.$user_info['last_name'].'",`first_name` = "'.$user_info['first_name'].'", `middle_name` = "'.$user_info['middle_name'].'", `phone_1` = "'.$user_info['phone_1'].'",`phone_2` = "'.$user_info['phone_2'].'", `fax` = "'.$user_info['fax'].'", `address_1` = "'.$user_info['address_1'].'", `address_2` = "'.$user_info['address_2'].'",`city` = "'.$user_info['city'].'", `virtuemart_state_id` = "'.$user_info['virtuemart_state_id'].'", `virtuemart_country_id` = "'.$user_info['virtuemart_country_id'].'",`zip`="'.$user_info['zip'].'",`email`="'.$user_info['email'].'",`created_by`="'.trim($customer_id).'",`modified_by`="'.trim($customer_id).'" WHERE `virtuemart_order_id` ="'.$orderID.'" AND `address_type`="ST" AND `virtuemart_user_id` ="'.trim($customer_id).'"');		
		$db->query();*/
		}
		}
		///end code
		
		$db->setQuery('INSERT INTO #__virtuemart_payment_plg_authorizenet( id, virtuemart_order_id, order_number, virtuemart_paymentmethod_id, payment_name,return_context, authorizenet_response_authorization_code,  	authorizenet_response_transaction_id, authorizenet_response_response_code, authorizenet_response_response_subcode, authorizenet_response_response_reason_code, authorizenet_response_response_reason_text, authorizenet_response_transaction_type, authorizenet_response_account_number, authorizenet_response_card_type, authorizenet_response_card_code_response, authorizenet_response_cavv_response, created_on, modified_on, created_by, modified_by  ) VALUES ( "", '.(int)$orderID.',"","2","Payment Name","4t79j7ijfh6f0kc4412r0kbm86", "'.$authorizeNetResponse['authorization_code'].'","'.$authorizeNetResponse['transaction_id'].'","'.$authorizeNetResponse['avs_response'].'","'.$authorizeNetResponse['response_subcode'].'","'.$authorizeNetResponse['response_reason_code'].'","'.$authorizeNetResponse['response_reason_text'].'","'.$authorizeNetResponse['transaction_type'].'","XXXX","'.$authorizeNetResponse['card_type'].'","'.$authorizeNetResponse['card_code_response'].'","'.$authorizeNetResponse['cavv_response'].'",now(),now(),"'.(int)$member_id.'","'.(int)$member_id.'")');
		if (!$db->query()) {
			   echo $db->getErrorMsg();
		}
		
		/*$query = 'SELECT `virtuemart_user_id` FROM `#__virtuemart_order_salerep` WHERE virtuemart_order_id= "'.$orderID.'"';
		$db->setQuery($query);
		$order_commission_user_id = $db->loadResult();
		if($order_commission_user_id == '')
		{
			if($sales_reps_id == '')$sales_reps_id = $member_id;
			$db->setQuery('INSERT INTO #__virtuemart_order_salerep (id, virtuemart_order_id, virtuemart_user_id, user_classref,virtuemart_calc_id ) VALUES ( "", '.(int)$orderID.', '.(int)$sales_reps_id.',"'.$user_classref.'","'.$_SESSION['virtuemart_calc_id'].'")');
			if (!$db->query()) {
				   echo $db->getErrorMsg();                       
			}
		}*/
		$cart123->emptyCart();	
		echo "<div style='color:#569E01;font-size:15px;'>Order inserted Successfully. Order Id is --> ".$orderID."</div>";
		if($orderID > 0){
           	 $session =& JFactory::getSession();
		     $item_qty_relation       = '';
			 $customer_addr_session   = '';
			 $customer_id_session     = '';
			 $customer_id_session     = '';
			 $pre_user_id_sess        = '';
			 $orderID                 = '';
			 $pending_orders_products = '';
			 $edit_select_shipping_id = '';
			 $cc_card_number          = '';
			 $cc_expire_month_2       = '';
			 $cc_expire_year_2        = '';
			 $cc_cvv                  = '';
			 
			 $session->clear('item_qty_relation');
			 $session->set('item_qty_relation',$item_qty_relation);
			 $session->clear('customer_addr_session');
			 $session->set('customer_addr_session',$customer_addr_session);
			 $session->clear('customer_id_session');
			 $session->set('customer_id_session',$customer_id_session);
			 $session->clear('pre_user_id_sess');
			 $session->set('pre_user_id_sess',$pre_user_id_sess);
			 $session->clear('orderID');
			 $session->set('orderID',$orderID);
			 $session->clear('pending_orders_products');
			 $session->set('pending_orders_products',$pending_orders_products);
			 $session->clear('edit_select_shipping_id');
			 $session->set('edit_select_shipping_id',$edit_select_shipping_id);
			 $session->set('cc_card_number',$cc_card_number);
			 $session->set('cc_expire_month_2',$cc_expire_month_2);
			 $session->set('cc_expire_year_2',$cc_expire_year_2);
			 $session->set('cc_cvv',$cc_cvv);
		
		/*unset($_SESSION['item_qty_relation']);
		$_SESSION['item_qty_relation'] = "";
		unset($_SESSION['customer_addr_session']); 
		$_SESSION['customer_addr_session']='';
		unset($_SESSION['customer_id_session']);
		$_SESSION['customer_id_session']='';
		unset($_SESSION['pre_user_id_sess']);
		$_SESSION['pre_user_id_sess'] = '';
		unset($_SESSION['orderID']);
		$_SESSION['orderID']='';
		unset($_SESSION['pending_orders_products']);
		$_SESSION['pending_orders_products']='';
		unset($_SESSION['edit_select_shipping_id']);
		$_SESSION['edit_select_shipping_id']='';
		$_SESSION["cc_card_number"]		= '';
			$_SESSION["cc_expire_month_2"]	= '';
			$_SESSION["cc_expire_year_2"]	= '';
			$_SESSION["cc_cvv"]  			='';*/
		}
	
	$jAp->redirect(JURI::root().'sales-orders/', "Order inserted Successfully. Order Number is --> ".$current_order_number);
	}
		else
		{   $session =& JFactory::getSession();
		    $cc_card          = $post['cc_card_number'];
			$cc_expire_month  = $post['cc_expire_month'];
			$cc_expire_year   = $post['cc_expire_year'];
			$cc_cvv           = $post['cc_cvv'];
		    $session->set('cc_card_number',$cc_card);
			$session->set('cc_expire_month_2',$cc_expire_month);
			$session->set('cc_expire_year_2',$cc_expire_year);
			$session->set('cc_cvv',$cc_cvv);
			/*$_SESSION["cc_card_number"]		= $post['cc_card_number'];
			$_SESSION["cc_expire_month_2"]	= $post['cc_expire_month'];
			$_SESSION["cc_expire_year_2"]		= $post['cc_expire_year'];
			$_SESSION["cc_cvv"]  			= $post['cc_cvv'];*/
			
		if($post['SaveOrderButton'] == "saveorder")
		{
		echo "<div style='font-size:12px;color:red;font-weight:bold;margin-left:10px;'>Order Save Successfully. Order Number is --> ".$current_order_number."</div>"; 
		 $session =& JFactory::getSession();		  
		 $session->set('orderID',$orderID);
		 
		//$_SESSION['orderID'] = $orderID;
		$cart123->emptyCart();
		$jAp->redirect(JURI::root().'sales-orders/', "Order Save Successfully. Order Number is --> ".$current_order_number);
		}
		else
		{
		echo '<div style="font-size:12px;color:red;font-weight:bold;margin-left:10px;">'.$payment_msg.'</div>';
		$jAp->redirect(JURI::root().'sales-orders/', $payment_msg);
		}
	}		

/////----end code----/////		
		
		return true;
	} 

} 
?>


