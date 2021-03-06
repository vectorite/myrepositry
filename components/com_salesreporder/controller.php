<?php error_reporting(E_ALL ^ E_NOTICE); ?>

<?php
 
// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.controller');

/**
 * Hello World Component Controller
 *
 * @package    Joomla.Tutorials
 * @subpackage Components
 */

class SalesreporderController extends JController
{
	/**
	 * Method to display the view
	 * 
	 * @access    public
	 */	 
	 
	function display()
	{
		parent::display();
	}
	 
	function get_address()
	{
		$db = JFactory::getDBO();
		$session =& JFactory::getSession();
		$post = JRequest::get('post');
		$jApp = JFactory::getApplication();
 
		$query ='SELECT * FROM #__virtuemart_userinfos WHERE  virtuemart_user_id = '.$post['id'].' ';
		$db->setQuery($query);
		$userdetail = $db->loadObject();	 
		
		//get order previous shipping address
		$st_user_id = '';
		$order_ID = $session->get('orderID');
		 
		 	 
		if( isset($order_ID) && !empty($order_ID) )
		{
		$sqlQuery = 'SELECT * FROM #__virtuemart_order_userinfos WHERE address_type = "ST" AND  virtuemart_order_id = "'.$session->get('orderID').'" LIMIT 1';
		 $db->setQuery($sqlQuery);
		 $st_userdetail = $db->loadObject();		
		
	$sqlQuery1 = 'SELECT virtuemart_userinfo_id FROM #__virtuemart_userinfos WHERE  virtuemart_user_id = "'.$post['id'].'" AND address_type = "ST" AND  first_name = "'.$st_userdetail->first_name.'" AND last_name="'.$st_userdetail->last_name.'" AND address_1 = "'.$st_userdetail->address_1.'" AND virtuemart_state_id = "'.$st_userdetail->virtuemart_state_id.'" AND zip = "'.$st_userdetail->zip.'" LIMIT 1';
		$db->setQuery($sqlQuery1);
		$st_user_id = $db->loadResult();
		
				
		}	
		//End code for get order previous shipping address 
		
		$state = 'SELECT state_2_code FROM #__virtuemart_states WHERE  virtuemart_state_id = '.$userdetail->virtuemart_state_id;
		$db->setQuery($state);
		$statename = $db->loadResult();
		
		$country = 'SELECT country_name FROM #__virtuemart_countries WHERE  virtuemart_country_id = '.$userdetail->virtuemart_country_id;
		$db->setQuery($country);
		$countryname = $db->loadResult();
			
		 $user_company = '';	
		 if($userdetail->company != '')
		 $user_company = $userdetail->company."\n";
		
		 $bt_address2 = '';
		 if($userdetail->address_2 != '')
		 $bt_address2 = "\n".$userdetail->address_2."\n";
		
		 // ======== Add by RCA chetnath ========

		 if(!empty($userdetail->CustomerType))
			$data[4] = $userdetail->CustomerType;			
		 else
			$data[4] = "N/A";	

		 // ======== End by RCA ========

		$data[0]= $userdetail->title." ".$userdetail->first_name." ".$userdetail->last_name."\n".$userdetail->address_1.", ".$bt_address2.$userdetail->city.", ".$statename."  ".$userdetail->zip."\n".$countryname."\n".$userdetail->phone_1;
		
		 $textarea_shipping_address =$userdetail->title." ".$userdetail->first_name." ".$userdetail->last_name."\n". $userdetail->address_1.", ".$bt_address2.$userdetail->city.", ".$statename."  ".$userdetail->zip."\n".$countryname;
		
		$sql123 = 'SELECT * FROM #__virtuemart_userinfos WHERE address_type="ST" AND virtuemart_user_id = '.$post['id'];			
		$db->setQuery($sql123);
		$sp_details = $db->loadObjectList();
					
		$html .='<table>
     <tr><td><input onclick="cal_total(\'dataTable\')" type="radio" name="ship_to_info_id" value="'.$userdetail->virtuemart_userinfo_id.'" class="stradio" id="stradio"></td><td colspan="2">Ship to same as bill to address.<input type="hidden" id="shipping_address_'.$userdetail->virtuemart_userinfo_id.'" value="'.$textarea_shipping_address.'"/>';
		
		foreach($sp_details as $sp_detail)
		{
		$session =& JFactory::getSession();
		$sp_address2 = '';
		if($sp_detail->address_2 != '')
		$sp_address2 = "\n".$sp_detail->address_2."\n";
		
		$vsate = 'SELECT state_2_code FROM #__virtuemart_states WHERE  virtuemart_state_id = '.$sp_detail->virtuemart_state_id;
		$db->setQuery($vsate);
		$sp_statename = $db->loadResult();
		
		$ccount = 'SELECT country_name FROM #__virtuemart_countries WHERE  virtuemart_country_id = '.$sp_detail->virtuemart_country_id;
		$db->setQuery($ccount);
		$sp_countryname = $db->loadResult();
		$chk = '';		

		//echo $session->get('edit_select_shipping_id'); echo "-----";
		//echo $session->get('edit_select_shipping_id'); echo "---" ; echo $sp_detail->virtuemart_userinfo_id; echo "----"; echo $st_user_id; echo $sp_detail->virtuemart_userinfo_id;
		if($session->get('edit_select_shipping_id')	== $sp_detail->virtuemart_userinfo_id )
		{
		$chk = 'checked="checked"';
		$textarea_shipping_address = $sp_detail->title." ".$sp_detail->first_name." ".$sp_detail->last_name."\n".$sp_detail->address_1.', '.$sp_address2.$sp_detail->city.', '.$sp_statename.', '.$sp_detail->zip."\n".$sp_countryname;
		}
		
		if($sp_detail->address_type_name != "") 
		$html .= '<tr><td valign="top"><input onclick="cal_total(\'dataTable\'); " type="radio" id="ship_to_info_id" name="ship_to_info_id" '.$chk.' value="'.$sp_detail->virtuemart_userinfo_id.'" /></td><td>'.$sp_detail->address_type_name.', '.$sp_detail->address_1.', '.$sp_detail->city.', '.$sp_statename.', '.$sp_detail->zip.', '.$sp_countryname.'<input type="hidden" id="shipping_address_'.$sp_detail->virtuemart_userinfo_id.'" value="'.$sp_detail->title." ".$sp_detail->first_name." ".$sp_detail->last_name.' '."\n".''.$sp_detail->address_1.', '.$sp_address2.$sp_detail->city.', '.$sp_statename.' '." ".' '.$sp_detail->zip.' '."\n".''.$sp_countryname.''."\n".''.$sp_detail->phone_1.''."\n".''.$sp_detail->phone_2.' '."\n".''.$sp_detail->fax.'"/></td><td valign="top"><a class="popupwindow" rel="windowCallUnload" href="'.JURI::root().'index.php/register-user?user_type=ST&user_id='.$sp_detail->virtuemart_user_id.'&virtuemart_userinfo_id='.$sp_detail->virtuemart_userinfo_id.'">Edit</a></td></tr>' ;
		else		
		$html .= '<tr><td><input onclick="cal_total(\'dataTable\')" type="radio" name="ship_to_info_id" '.$chk.' value="'.$sp_detail->virtuemart_userinfo_id.'" /></td><td>'.$sp_detail->first_name." ".$sp_detail->last_name.', '.$sp_detail->address_1.', '.$sp_detail->city.', '.$sp_statename.', '.$sp_detail->zip.', '.$sp_countryname.'<input type="hidden" id="shipping_address_'.$sp_detail->virtuemart_userinfo_id.'" value="'.$sp_detail->address_1.', '.$sp_address2.$sp_detail->city.', '.$sp_statename.', '.$sp_detail->zip.', '.$sp_countryname.'"/></td><td valign="top"><a class="popupwindow" rel="windowCallUnload" href="'.JURI::root().'index.php/register-user?user_type=ST&user_id='.$sp_detail->virtuemart_user_id.'&virtuemart_userinfo_id='.$sp_detail->virtuemart_userinfo_id.'">Edit</a></td></tr>' ;
		
		}
		
		
		$html .='<tr><td></td><td colspan="2"><a class="popupwindow" rel="windowCallUnload" href="'.JURI::root().'index.php/register-user?user_type=ST&user_id='.$post['id'].'&new=1">Add New Address.</a></td></tr></table>';
		$data[1] = html_entity_decode($html, ENT_COMPAT, "UTF-8");
		$radio = JRequest::getVar('radio.btn');
		$data[2] = '<a class="popupwindow" rel="windowCallUnload" href="'.JURI::root()."index.php/register-user?user_type=BT&user_id=".$userdetail->virtuemart_user_id."&virtuemart_userinfo_id=".$userdetail->virtuemart_userinfo_id.'"  >Edit Customer Info.</a>&nbsp;&nbsp;&nbsp;<a class="popupwindow" rel="windowCallPendingOrder" href="'.JURI::root()."index.php?option=com_virtuemart&view=orders&layout=pending_list&user_id=".$userdetail->virtuemart_user_id.'">Pending order history.</a>';
		
		$query1 = 'SELECT id,email FROM ekxob_users as e join `ekxob_virtuemart_userinfos` as v WHERE e.id='.$post['id'].' ';
		$db->setQuery($query1);
		$virtuemart = $db->loadObject();
		$data[3] = $virtuemart->email;
		
		
		//print_r($data); //die;
		echo "{";
		echo "item1: ", json_encode($data[0]).",";
		echo "item2: ", json_encode($data[1]).",";                                
		echo "item3: ", json_encode($data[2]).",";			
		echo "item4: ", json_encode($textarea_shipping_address).",";		
		echo "item5: ", json_encode($data[3]).",";
		echo "item6: ", json_encode($data[4]);
		echo "}";
		die;
	}
	
	
	
	
	function cus_message()
	{
		echo '<div style="background-color: #c3d2e5; border-top: 3px solid #84a7db; border-bottom: 3px solid #84a7db; padding-left: 1em; font-weight: bold; 		color:green; padding-left:40px; padding-top:10px; padding-bottom:10px; margin-top:10px;">Customer Information Update Successfully...</div>';
 die;
	}
	
	function checkEmail()
	{
		$db = JFactory::getDBO();
		$post = JRequest::get('post');
		$email = JRequest::getVar('email');
		$sql = 'select email from #__users where email = "'.$email.'" ';
		$db->setQuery($sql);
		//$db->query();
		$num_rows = $db->getNumRows();
		//print_r($num_rows); 
		$result = $db->loadRowList();
		//echo "-----"; print_r($result); die;
		if($num_rows == 0)
    {
        echo('USER_AVAILABLE');
		
    }
    else
    {
        echo('USER_EXISTS');
		
    }
	die;
}
	
	
	function get_itemname_list()
	{
			$db = JFactory::getDBO(); 
			/*$sql = "SELECT p_detail.virtuemart_product_id, p_detail.product_name, p_detail.product_s_desc
			FROM '#__virtuemart_products_en_gb' AS p_detail  
			INNER JOIN #__virtuemart_products AS p
			ON p_detail.virtuemart_product_id = p.virtuemart_product_id 
			ORDER BY p_detail.product_name
			";*/
			
		/*$sql = 'SELECT *
				FROM ekxob_virtuemart_products_en_gb
				LEFT JOIN ekxob_virtuemart_products 
				ON ekxob_virtuemart_products_en_gb.virtuemart_product_id = ekxob_virtuemart_products.virtuemart_product_id 
				where published =1';*/
		
		/*require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart'.DS.'helpers'.DS.'config.php');		
		require(JPATH_SITE . DS . 'components' . DS . 'com_virtuemart' . DS . 'helpers' . DS . 'cart.php');
		$cart = VirtueMartCart::getCart();
		$cart->emptyCart();
		if ($cart)
		 {
			$virtuemart_product_ids = array("1","2","3");
			$success = true;
			if ($cart->add($virtuemart_product_ids,$success)) {
				$msg = JText::_('COM_VIRTUEMART_PRODUCT_ADDED_SUCCESSFULLY');
				$type = '';
			} else {
				$msg = JText::_('COM_VIRTUEMART_PRODUCT_NOT_ADDED_SUCCESSFULLY');
				$type = 'error';
			}
		}
		
		$usr = JFactory::getUser("47");
		$prices = $cart->getCartPrices();
		
		//$cart->confirmedOrder2($cart, $usr, $prices);
		$orderModel = VmModel::getModel('orders');
		
		if (($orderID = $orderModel->_createOrder($cart, $usr, $prices)) == 0) {
			echo 'Couldn\'t create order','Couldn\'t create order';			
		}
		if (!$orderModel->_createOrderLines($orderID, $cart)) {
			echo 'Couldn\'t create order items','Couldn\'t create order items';			
		}
				
		$orderModel->_updateOrderHist($orderID);
		if (!$orderModel->_writeUserInfo($orderID, $usr, $cart)) {
			echo 'Couldn\'t create order history','Couldn\'t create order history';
		}
		if (!$orderModel-> _createOrderCalcRules($orderID, $cart) ) {
			echo 'Couldn\'t create order items','Couldn\'t create order items';			
		}
		$this->virtuemart_order_id = $orderID;
		$order= $orderModel->getOrder($orderID);

		$dispatcher = JDispatcher::getInstance();

		JPluginHelper::importPlugin('vmshipment');
		JPluginHelper::importPlugin('vmcustom');
		JPluginHelper::importPlugin('vmpayment');
		$returnValues = $dispatcher->trigger('plgVmConfirmedOrder', array($cart, $order));
			
		echo $orderID;
		
		echo "---------->";die;*/
		$sql = 'SELECT *
				FROM #__virtuemart_products_en_gb AS pd
				, #__virtuemart_products  AS p , #__virtuemart_product_prices AS pp
				Where p.virtuemart_product_id = pd.virtuemart_product_id
				AND pp.virtuemart_product_id = pd.virtuemart_product_id
				AND published =1';			
		$db->setQuery($sql);
		$productdetails = $db->loadObjectList();
		foreach($productdetails as $productdetail)
		{
		$html="";
		$html = $productdetail->product_name."|".$productdetail->virtuemart_product_id."|".$productdetail->product_s_desc."|";
		if($productdetail->override == "1")
		$html .= $productdetail->product_override_price ."\n";
		else
		$html .= $productdetail->product_price ."\n";	
		
		echo html_entity_decode($html, ENT_COMPAT, "UTF-8");
		} 
	die;
	
	}
	
	function get_itemdetail_by_id()
	{
		$db = JFactory::getDBO();		
		$post = JRequest::get('post');
		$sql = 'SELECT *
				FROM #__virtuemart_products_en_gb AS pd
				, #__virtuemart_products  AS p , #__virtuemart_product_prices AS pp
				Where p.virtuemart_product_id = pd.virtuemart_product_id
				AND pp.virtuemart_product_id = pd.virtuemart_product_id
				AND published =1 AND p.virtuemart_product_id ='.$post['id'];			
		$db->setQuery($sql);
		$productdetails = $db->loadObjectList();
		foreach($productdetails as $productdetail)
		{
		$html="";
		$html = $productdetail->product_name."|".$productdetail->virtuemart_product_id."|".$productdetail->product_s_desc."|";
		if($productdetail->override == "1")
		$html .= $productdetail->product_override_price ."\n";
		else
		$html .= $productdetail->product_price ."\n";		
		echo html_entity_decode($html, ENT_COMPAT, "UTF-8");
		} 
	die;
	
	}
	
	function reset_order()
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
			unset($_SESSION['customer_addr_session']);
			$_SESSION['customer_addr_session'] = '';
			unset($_SESSION['edit_select_shipping_id']);
			$_SESSION['edit_select_shipping_id']='';			
			$_SESSION["cc_card_number"]		= '';
			$_SESSION["cc_expire_month_2"]	= '';
			$_SESSION["cc_expire_year_2"]	= '';
			$_SESSION["cc_cvv"]  			='';*/
			echo "clearorder";
			die;
	}
	
	
	function check_zipcode()
	{
		$db = JFactory::getDBO();
		//$session =& JFactory::getSession();
		$post = JRequest::get('post');
		if($post['state_id'] != '10')
		{	
		$sql = 'SELECT COUNT(*) FROM #__virtuemart_zipcode WHERE  zipcode = "'.$post['zipcode'].'"';				
		$db->setQuery($sql);
		$zipcode_id_count = $db->loadResult();
		
		if($zipcode_id_count > 0)
		{
		echo "notcorrect"; die;
		}
		else
		{
		echo "correct"; die;
		}
		}
		else	
		{	
		$sql = 'SELECT id FROM #__virtuemart_zipcode WHERE virtuemart_state_id="'.$post['state_id'].'" AND zipcode = "'.$post['zipcode'].'"';				
		$db->setQuery($sql);
		$zipcode_id = $db->loadObject();
		if($zipcode_id > 0)
		{
		echo "correct";	die;
		}
		else
		{
		echo "notcorrect"; die;
		}
		}
	
	}
	
	function calculate_tax()
	{
		$db = JFactory::getDBO();
		$post = JRequest::get('post');
		
		if($post['vm_user_id'] != "")
		$sql = 'SELECT * FROM #__virtuemart_userinfos WHERE virtuemart_userinfo_id ='.$post['vm_user_id'].' AND virtuemart_user_id = '.$post['user_id'];
		else
		$sql = 'SELECT * FROM #__virtuemart_userinfos WHERE  virtuemart_user_id = '.$post['user_id'];
		
		$db->setQuery($sql);
		$userdetail = $db->loadObject();
		$calc_id_countries  = array();
		$calc_id_states  = array();
		$calc_id_zipcodes  = array();
		$calculated = 0.00;
		$price = $post['total_amt'];
		$discount_amt = $post['discount_amt'];
		if($post['shipping_amt'] != '' && $post['shipping_amt'] != '0.00' && $post['shipping_amt'] != '0' )
		$shipping_amt = $post['shipping_amt'];
		else
		$shipping_amt = 0;
		$price_without_discount_amt = $price + $discount_amt;
		if($userdetail->virtuemart_country_id != "")
		{
		$q = 'SELECT `virtuemart_calc_id` FROM #__virtuemart_calc_countries WHERE `virtuemart_country_id`="' . $userdetail->virtuemart_country_id. '"';
		$db->setQuery($q);
		$calc_id_countries = $db->loadResultArray();
		}
		
		if($userdetail->virtuemart_state_id != "")
		{
		$q = 'SELECT `virtuemart_calc_id` FROM #__virtuemart_calc_states WHERE `virtuemart_state_id`="' . $userdetail->virtuemart_state_id. '"';
		$db->setQuery($q);
		$calc_id_states = $db->loadResultArray();
		}
		
		if($userdetail->zip != "")
		{
		$q = 'SELECT `id` FROM #__virtuemart_zipcode WHERE `zipcode`="'.$userdetail->zip.'"';
		$db->setQuery($q);
		$zipcode_id = $db->loadResult();	
			if($zipcode_id != '')
			{
				$q = 'SELECT `virtuemart_calc_id` FROM #__virtuemart_calc_zipcode WHERE `virtuemart_zipcode_id`="'.$zipcode_id.'"';	
				$db->setQuery($q);
				$calc_id_zipcodes = $db->loadResultArray();
			}
		}
		
		if (!empty($calc_id_countries) && empty($calc_id_states)) {
				$calc_ids = $calc_id_countries;
			} else if (!empty($calc_id_states)) {
				$calc_ids = $calc_id_states;				
			}
			if (!empty($calc_id_zipcodes)) {								
				$calc_ids = $calc_id_zipcodes;											
			}
			else
			 {
					$remove_ids = "";
					for($i=0; $i < count($calc_id_states); $i++)
					{
					  $q = 'SELECT `virtuemart_calc_id` FROM #__virtuemart_calc_zipcode WHERE `virtuemart_calc_id`="'.$calc_id_states[$i].'" LIMIT 1';
  					  $db->setQuery($q);
					  $remove_id = $db->loadResult();
					  if($remove_id > 0)
					  $remove_ids .= $remove_id.",";						
					}
					$remove_ids = substr($remove_ids, 0, -1);
					$remove_ids = explode(",", $remove_ids);
					
					$calc_ids = array_diff($calc_id_states, $remove_ids);
				}
			
				
		
		for($i=0;$i<count($calc_ids);$i++)
		{
		$session =& JFactory::getSession();
		//$calc = $calc_ids[$i];
		$session->set('virtuemart_calc_id', $calc_ids[$i]);
		//$_SESSION['virtuemart_calc_id'] = $calc_ids[$i];
		$q = 'SELECT * FROM #__virtuemart_calcs WHERE
                `calc_kind`="TaxBill"
                AND `published`="1"              
				AND `virtuemart_calc_id`= "'.$calc_ids[$i].'" ';
		//			$shoppergrps .  $countries . $states ;
		$db->setQuery($q);
		$rules = $db->loadAssocList();
		
		foreach ($rules as $rule) {
		//function interpreteMathOp($mathop, $value, $price, $currency='')
		$mathop = $rule["calc_value_mathop"];
		$value = $rule["calc_value"];		
		$currency=$rule["calc_currency"];		
		}
				
			$coreMathOp = array('+','-','+%','-%');
			if(!$this->_revert){
				$plus = '+';
				$minus = '-';
			} else {
				$plus = '-';
				$minus = '+';
			}
						if(in_array($mathop,$coreMathOp)){
				$sign = substr($mathop, 0, 1);
				$calculated = false;
				if (strlen($mathop) == 2) {
					$cmd = substr($mathop, 1, 2);
					if ($cmd == '%') {
						if(!$this->_revert){
							$calculated = $price * $value / 100.0;
						} else {

							if($sign == $plus){
								$calculated =  abs($price /(1 -  (100.0 / $value)));
							} else {
								$calculated = abs($price /(1 +  (100.0 / $value)));
							} 							
						}
					}
				} else if (strlen($mathop) == 1){
					$calculated =  abs($value);
				} 
				
			}
					
		}
		
		if($sign == $plus){
			$total_amount_with_tax =  $price_without_discount_amt + (float)$calculated;
		} else if($sign == $minus){
			$total_amount_with_tax =  $price_without_discount_amt - (float)$calculated;
		} else {					
			$total_amount_with_tax =  $price_without_discount_amt;
		}
		//echo ($total_amount_with_tax - $shipping_amt)."###".$calculated;
		echo $total_amount_with_tax."###".$calculated;		
		die;	
	}

}