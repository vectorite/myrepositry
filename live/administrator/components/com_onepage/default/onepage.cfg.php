<?php
if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );
/*
*      One Page Checkout configuration file
*      Copyright RuposTel s.r.o. under GPL license
*      Version 2 of date April 2012
*      Feel free to modify this file according to your needs
*
*
*     @copyright Copyright (C) 2007 - 2010 RuposTel - All rights reserved.
*     @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*     One Page checkout is free software released under GNU/GPL and uses code from VirtueMart
*     VirtueMart is free software. This version may have been modified pursuant
*     to the GNU General Public License, and as distributed it includes or
*     is derivative of works licensed under the GNU General Public License or
*     other free or open source software licenses.
* 
*/




$disable_onepage = false; 
    
/* If user in Optional, normal, silent registration sets email which already exists and is registered 
* and you set this to true
* his order details will be saved but he will not be added to joomla registration and checkout can continue
* if registration type allows username and password which is already registered but his new password is not the same as in DB then checkout will return error
*/
$email_after = false;
      $newitemid = "";
      $agreed_notchecked = true;
      $op_no_basket = false;
      $shipping_template = true;
      $op_articleid = "";
	  $op_sum_tax = false;
      $op_last_field = false;
      $op_default_zip = "11111"; 
	  $op_numrelated = "5"; 
      $cut_login = true;
      $op_delay_ship = false;
      $op_loader = true;
      $op_usernameisemail = false;
      $shipping_inside_choose = false;
      $no_continue_link_bottom = true;
      $op_default_state = false;
      $list_userfields_override = true;
      $no_jscheck = true;
      $op_dontloadajax = false;
      $shipping_error_override = "ERROR";
      $op_zero_weight_override = false;
      $email_after = false;
      $override_basket = true;
      $selected_template = "icetheme_thestore"; 
      $dont_show_inclship = false;
      $no_continue_link = true;
      
 	$adwords_name = array(); $adwords_code = array(); $adwords_amount = array();
 	$adwords_code[0] = "";
 	$adwords_name[0] = "";
 	$adwords_amount[0] = "";
 	$no_login_in_template = false;
      $shipping_inside = false;
      $payment_inside = false;
      $payment_advanced = true;
      $fix_encoding = false;
      $fix_encoding_utf8 = true;
$fix_encoding = false;
      $shipping_inside_basket = false;
      $payment_inside_basket = false;
      $email_only_pok = false;
      $no_taxes_show = false;
      $use_order_tax = true;
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
      $custom_tax_rate = "0.15"; 
      $no_decimals = false;$curr_after = false;
/*
Set this to true to unlog (from Joomla) all shoppers after purchase
*/
$unlog_all_shoppers = false;
 
/* set this to true if you don't accept other than valid EU VAT id */
$must_have_valid_vat = false;
      $vat_input_id = "";
         
/*
* Set this to true to unlog (from Joomla) all shoppers after purchase
*/
 $unlog_all_shoppers = false;
     
/* This will disable positive messages on Thank You page in system info box */

 $disable_positive_msgs = false;

/* please check your source code of your country list in your checkout and get exact virtuemart code for your country
* all incompatible shipping methods will be hiddin until customer choses other country
* this will also be preselected in registration and shipping forms
* Your shipping method cannot have 0 index ! Otherwise it will not be set as default
*/     
 $default_shipping_country = "USA";
      
/* since VM 1.1.5 there is paypal new api which can be clicked on image instead of using checkout process
* therefore we can hide it from payments
* These payments will be hidden all the time
* example:  $payments_to_hide = "4,3,5,2";
*/

/* default payment option id
* leave commented or 0 to let VM decide
*/
$payment_default = 7;
	
/* turns on google analytics tracking, set to false if you don't use it */
 $g_analytics = false;

/* set this to false if you don't want to show full TOS
* if you set show_full_tos, set this variable to one of theses:
* use one of these values:
* 'shop.tos' to read tos from your VirtueMart configuration
* '25' if set to number it will search for article with this ID, extra lines will be removed automatically
* both will be shown without any formatting
*/
 $show_full_tos = false; 
 $tos_config = ""; 
 $use_ssl = false; 
 $op_show_others = false; 
 $op_fix_payment_vat = true; 
 $op_free_shipping = false; 

/* change this variable to your real css path of '>> Proceed to Checkout'
* let's hide 'Proceed to checkout' by CSS
* if it doesn't work, change css path accordingly, i recommend Firefox Firebug to get the path
* but this works for most templates, but if you see 'Proceed to checkout' link, contact me at stan@rupostel.sk
* for rt_mynxx_j15 template use '.cart-checkout-bar {display: none; }'
*/
$style_class = "checkout_link";
$style = ".".$style_class." { display: none; } ";

/* list of styles which will be overwritten in onepage.css in your template path
* how it works: all the listed styles will be given prefix of onepage_ i.e. for sectiontableheader it will be given onepage_sectiontableheader
* style classes are searched this way
* 1. search for class='yourstyle' case insensitively
* 2. change class=' to class='onepage_
*  
* list classes separated by comma in $op_classes
* list of ids to be given prefix onepage_  ANY IDs WHICH END WITH _field WILL BE IGNORED AS THEY WOULD BRAKE YOUR CHECKOUT PROCESS
* all spaces will be ignored
*  
* change of classes and ids will be done AFTER temlate processing
* 
* only generated code will be influenced. You can't change classes and ids outside scope of this component, such as menus and other modules
*/

$op_classes = "";
$op_ids = ""; 

/*
*   filename of css file to use for unlogged users if you want to override default styles
*   if not set, for basic functionality default.css in /components/com_virtuemart/themes/default/templates/onepage/onepage.css will be used
*/
$css_logged = "onepage.css";
$css_unlogged = "onepage.css";
$tpl_logged = "onepage.logged.tpl.php"; 
$tpl_unlogged = "onepage.unlogged.tpl.php"; 

/*
*  New feature from 18.aug 2010
*  Hide these payment options for these shipping methods
*  
*  example:
*  $hidep[ship_id]='2,3,4';
*  notes: 2,4,7,11,18 (payments)
*  notes: 23, 24,25,26
*  $hidep[23] = '2/4,11/4';
*  $hidep[26] = '4/7';
*
*/

$hidep = array();

$payment_info = array();
$payment_button = array();
$default_country_array = array();
$payment_info["2"] = JText::_("ONEPAGE_PAYMENT_EXTRA_INFO_2"); 
$payment_button["2"] = JText::_("ONEPAGE_PAYMENT_EXTRA_INFO_BUTTON_2"); 
$payment_info["4"] = JText::_("ONEPAGE_PAYMENT_EXTRA_INFO_4"); 
$payment_button["4"] = JText::_("ONEPAGE_PAYMENT_EXTRA_INFO_BUTTON_4"); 
$payment_info["7"] = JText::_("ONEPAGE_PAYMENT_EXTRA_INFO_7"); 
$payment_button["7"] = JText::_("ONEPAGE_PAYMENT_EXTRA_INFO_BUTTON_7"); 
$payment_info["27"] = JText::_("ONEPAGE_PAYMENT_EXTRA_INFO_27"); 
$payment_button["27"] = JText::_("ONEPAGE_PAYMENT_EXTRA_INFO_BUTTON_27"); 

/* all the rest are language vars */

$ship_country_change_msg = JText::_("ONEPAGE_SHIP_COUNTRY_CHANGED");
 if (empty($ship_country_change_msg)) $ship_country_change_msg = "Please choose the right carrier for selected country";

$ship_country_is_invalid_msg = JText::_("ONEPAGE_SHIP_COUNTRY_INVALID");
 if (empty($ship_country_is_invalid_msg)) $ship_country_is_invalid_msg = "We are sorry, but we don't ship to chosen country. Please select a different country or contact us by phone.";

$default_info_message = JText::_(	"ONEPAGE_PAYMENT_EXTRA_DEFAULT_INFO");
$addusererr = JText::_("ONEPAGE_ADD_USER_ERROR");
$saveordererr = JText::_("ONEPAGE_SAVE_ORDER_ERROR" ); 

$incship = JText::_("ONEPAGE_ORDER_TOTAL_INCL_SHIPPING"); 
  if ((empty($incship)) || (!isset($incship)))
   	$incship = JText::_("PHPSHOP_ORDER_LIST_TOTAL").": ";

$shippingtxt = JText::_("ONEPAGE_SHIPPING_ADDRESS");

if ((empty($shippingtxt)) || (!isset($shippingtxt)))
 $shippingtxt =  JText::_("ONEPAGE_SHIPPING_ADDRESS_IS_DIFFERENT"); 

$chkship =  JText::_("ONEPAGE_SHIPPING_ADDRESS_IS_DIFFERENT"); 

if (empty($chkship) || (!isset($chkship)))
	$chkship = JText::_("PHPSHOP_ADD_SHIPTO_2");

$msg1 =  JText::_("ONEPAGE_THANKYOU_ORDER_OK");

$msg2 =   JText::_("ONEPAGE_THANKYOU_ORDER_ERROR");
if (empty($msg2)) $msg2 = "Your order was NOT SAVED, please contact us by phone";

$msg3 = JText::_("ONEPAGE_THANKYOU_ORDER_OK_BUT_PAYMENT_BAD");
if (empty($msg3)) $msg3 = "Your order was saved, but there was a problem with payment. We will contact you soon for more specific information.";

$msg4 = JText::_("ONEPAGE_THANKYOU_ORDER_OK_PAYMENT_OK");

if (!empty($selected_template) && (file_exists(JPATH_ROOT.DS."components".DS."com_onepage".DS."themes".DS.$selected_template.DS."overrides".DS."onepage.cfg.php")))
{
  include(JPATH_ROOT.DS."components".DS."com_onepage".DS."themes".DS.$selected_template.DS."overrides".DS."onepage.cfg.php");
}
