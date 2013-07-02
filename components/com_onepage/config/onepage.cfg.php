<?php
if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );
/*
*      One Page Checkout configuration file
*      Copyright RuposTel s.r.o. under GPL license
*      Version 2 of date 31.March 2012
*      Feel free to modify this file according to your needs
*
*
*     @copyright Copyright (C) 2007 - 2012 RuposTel - All rights reserved.
*     @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*     One Page checkout is free software released under GNU/GPL and uses code from VirtueMart
*     VirtueMart is free software. This version may have been modified pursuant
*     to the GNU General Public License, and as distributed it includes or
*     is derivative of works licensed under the GNU General Public License or
*     other free or open source software licenses.
* 
*/





		  require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'config.php'); 
		  VmConfig::loadConfig(true); 

$disable_onepage = false; 
    $hide_payment_if_one = true;
    
/* If user in Optional, normal, silent registration sets email which already exists and is registered 
* and you set this to true
* his order details will be saved but he will not be added to joomla registration and checkout can continue
* if registration type allows username and password which is already registered but his new password is not the same as in DB then checkout will return error
*/
$email_after = false;
      $opc_link_type = 0;
      $newitemid = "";
       $business_fields = array();  $custom_rendering_fields = array();  $shipping_obligatory_fields = array('first_name','last_name','address_1','zip','city','virtuemart_country_id','virtuemart_state_id','phone_1'); $op_disable_shipping = false;
      $op_disable_shipto = false;
      $op_no_display_name = true;
      $op_create_account_unchecked = false;
       $product_price_display = "salesPrice";
 $subtotal_price_display = "salesPrice";
 $opc_usmode = false;  $full_tos_logged = false;  $tos_scrollable = false;  $full_tos_unlogged = false;  $tos_logged = true;  $tos_unlogged = true;  $opc_email_in_bt = false;  $double_email = false;  $coupon_price_display = "salesPriceCoupon";
$agreed_notchecked = false;
      $op_default_shipping_zero = true;
      $never_count_tax_on_shipping = false;
      $save_shipping_with_tax = false;
      $op_no_basket = false;
      $shipping_template = false;
      $op_articleid = "";
	  $op_sum_tax = false;
      $op_last_field = false;
      $op_default_zip = "11111"; 
	$op_numrelated = "5"; 
      
// auto config by template
$cut_login = false;
      $op_delay_ship = false;
      $op_loader = true;
      $op_usernameisemail = true;
      $shipping_inside_choose = true;
      $no_continue_link_bottom = false;
      $op_default_state = false;
      $list_userfields_override = false;
      $no_jscheck = true;
      $op_dontloadajax = false;
      $shipping_error_override = "ERROR";
      $op_zero_weight_override = false;
      $email_after = false;
      $override_basket = false;
      $selected_template = "pbv_multi_tr";
	    
		$selected_template_override = JRequest::getVar('opc_theme', ''); 
		if (!empty($selected_template_override))
		{
		$test = str_replace('_', '', $selected_template_override); 
		if (ctype_alnum($test))
		 {
		   $selected_template = $selected_template_override; 
		 }
		}
		
       $adwords_timeout = 4000; $dont_show_inclship = false;
      $no_continue_link = true;
      
 	$adwords_name = array(); $adwords_code = array(); $adwords_amount = array();
 	$adwords_name[0] = "body.html";
 	$adwords_amount[0] = "";
 	$no_login_in_template = false;
      $shipping_inside = true;
      $payment_inside = false;
      $payment_saveccv = false;
      $payment_advanced = false;
      $fix_encoding = false;
      $fix_encoding_utf8 = false;
      $shipping_inside_basket = false;
      $payment_inside_basket = false;
      $email_only_pok = false;
      $no_taxes_show = false;
      $use_order_tax = false;
      $no_taxes = false;
      $never_show_total = false;
      $email_dontoverride = false;
      $allow_duplicit = true;
      $show_only_total = false;
      $show_andrea_view = false;
      $always_show_tax = false;
$always_show_all = false;
$add_tax = false;
      $add_tax_to_shipping_problem = false;
      $add_tax_to_shipping = false;
      $custom_tax_rate = 0;
      $no_decimals = false;$curr_after = false;
/*
Set this to true to unlog (from Joomla) all shoppers after purchase
*/
$unlog_all_shoppers = false;
$vat_input_id = ""; $eu_vat_always_zero = ""; $vat_except = ""; $move_vat_shopper_group = "";  $zerotax_shopper_group = array();  
/* set this to true if you don't accept other than valid EU VAT id */
$must_have_valid_vat = false; 
/*
* Set this to true to unlog (from Joomla) all shoppers after purchase
*/
 $unlog_all_shoppers = false;
     
/* This will disable positive messages on Thank You page in system info box */


/* please check your source code of your country list in your checkout and get exact virtuemart code for your country
* all incompatible shipping methods will be hiddin until customer choses other country
* this will also be preselected in registration and shipping forms
* Your shipping method cannot have 0 index ! Otherwise it will not be set as default
*/     
 $default_shipping_country = "223";
      
	if (!defined("DEFAULT_COUNTRY"))
	{
	 if (file_exists(JPATH_SITE.DS."administrator".DS."components".DS."com_geolocator".DS."assets".DS."helper.php"))
	 {
	  require_once(JPATH_SITE.DS."administrator".DS."components".DS."com_geolocator".DS."assets".DS."helper.php"); 
	  if (class_exists("geoHelper"))
	   {
	     $country_2_code = geoHelper::getCountry2Code(""); 
		 if (!empty($country_2_code))
		 {
		 $db=&JFactory::getDBO(); 
		 $db->setQuery("select virtuemart_country_id from #__virtuemart_countries where country_2_code = '".$country_2_code."' "); 
		 $r = $db->loadResult(); 
		 if (!empty($r)) 
		 $default_shipping_country = $r; 
		 }
	     //$default_shipping_country = 
	   }
	 }
	  define("DEFAULT_COUNTRY", $default_shipping_country); 
	 }
	 else
	 {
	  $default_shipping_country = DEFAULT_COUNTRY; 
	 
	 }
	
/* since VM 1.1.5 there is paypal new api which can be clicked on image instead of using checkout process
* therefore we can hide it from payments
* These payments will be hidden all the time
* example:  $payments_to_hide = "4,3,5,2";
*/

/* default payment option id
* leave commented or 0 to let VM decide
*/
$payment_default = 2;
	
/* turns on google analytics tracking, set to false if you don't use it */
 $g_analytics = false;

/* set this to false if you don't want to show full TOS
* if you set show_full_tos, set this variable to one of theses:
* use one of these values:
* 'shop.tos' to read tos from your VirtueMart configuration
* '25' if set to number it will search for article with this ID, extra lines will be removed automatically
* both will be shown without any formatting
*/
 $tos_config = ""; 
 $use_ssl = false; 
 $op_show_others = false; 
 $op_fix_payment_vat = false; 
 $op_free_shipping = false; 

/* change this variable to your real css path of '>> Proceed to Checkout'
* let's hide 'Proceed to checkout' by CSS
* if it doesn't work, change css path accordingly, i recommend Firefox Firebug to get the path
* but this works for most templates, but if you see 'Proceed to checkout' link, contact me at stan@rupostel.sk
* for rt_mynxx_j15 template use '.cart-checkout-bar {display: none; }'
*/

$payment_info = array();
$payment_button = array();
$default_country_array = array();

 /* URLs fetched after checkout encoded by base64_encode */
 $curl_url = array();

$default_country_array["en-GB"] = "223"; 


if (!empty($selected_template) && (file_exists(JPATH_ROOT.DS."components".DS."com_onepage".DS."themes".DS.$selected_template.DS."overrides".DS."onepage.cfg.php")))
{
 
  include(JPATH_ROOT.DS."components".DS."com_onepage".DS."themes".DS.$selected_template.DS."overrides".DS."onepage.cfg.php");
 
}

