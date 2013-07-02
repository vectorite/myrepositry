<?php 
if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );
/**
 * This is the page that is shown when the order has been placed.
 * It is used to thank the customer for her/his order and show a link 
 * to the order details.
*
* @version $Id: checkout.thankyou.tpl.php 1364 2008-04-09 16:44:28Z soeren_nb $
* @package VirtueMart
* @subpackage themes
* @copyright Copyright (C) 2004-2008 soeren - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See /administrator/components/com_virtuemart/COPYRIGHT.php for copyright notices and details.

* http://virtuemart.net
*/

mm_showMyFileName( __FILE__ );

global $VM_LANG;
?>

<h3><?php 
if ($db->f("payment_class")!="ps_google")
echo $VM_LANG->_('PHPSHOP_THANKYOU'); 
else
echo $VM_LANG->_('PHPSHOP_THANKYOU_GC_SUCCESS');
?></h3>
<p>
 	<?php 
 	if ($db->f("payment_class")!="ps_google")
 	{echo vmCommonHTML::imageTag( VM_THEMEURL .'images/button_ok.png', 'Success', 'center', '48', '48' );} ?>
   	<?php 
	   if ($db->f("payment_class")!="ps_google")
	   {echo $VM_LANG->_('PHPSHOP_THANKYOU_SUCCESS');
	   ?>
  	<br /><br />
	<?php echo $VM_LANG->_('PHPSHOP_EMAIL_SENDTO') .": <strong>". $user->user_email . '</strong>'; }?><br />
</p>
  
<!-- Begin Payment Information -->
<?php
if( empty($auth['user_id'])) {
	return;
}
if ($db->f("order_status") == "P" ) {
	// Copy the db object to prevent it gets altered
	$db_temp = ps_DB::_clone( $db );
 /** Start printing out HTML Form code (Payment Extra Info) **/ ?>
 <br />
<table width="100%">
  <tr>
    <td width="100%" align="center">
    	<?php 
	    /**
	     * PLEASE DON'T CHANGE THIS SECTION UNLESS YOU KNOW WHAT YOU'RE DOING
	     */
	    // Try to get PayPal/PayMate/Worldpay/whatever Configuration File
	    @include( CLASSPATH."payment/".$db->f("payment_class").".cfg.php" );
	    /* OPC: we will not parse extra info, it will be much better if you have it directly in this file
	    
		$vmLogger->debug('Beginning to parse the payment extra info code...' );
		
	    // Here's the place where the Payment Extra Form Code is included
	    // Thanks to Steve for this solution (why make it complicated...?)
	    if( eval('?>' . $db->f("payment_extrainfo") . '<?php ') === false ) {
	    	$vmLogger->debug( "Error: The code of the payment method ".$db->f( 'payment_method_name').' ('.$db->f('payment_method_code').') '
	    	.'contains a Parse Error!<br />Please correct that first' );
	    }
	    else {
	    	$vmLogger->debug('Successfully parsed the payment extra info code.' );
	    }
	    // END printing out HTML Form code (Payment Extra Info)
		*/
		
/* This extra information will redirect your users to this link with the information :
index.php?page=account.order_details&order_id=".$db->f("order_id")."&option=com_virtuemart&Itemid=".$Itemid
with the messsage of:
Please review the order and click the google checkout button to finish the payments.
*/
$dbst = new ps_DB;
$q2= "SELECT * FROM #__vm_order_user_info WHERE order_id='".(int)$db->f("order_id")."' and address_type = 'BT'";

$dbst->setQuery($q2);
$dbst->query();
$dbst->next_record();
if (GOOGLE_SANDBOX==false)
{
$url= "https://checkout.google.com/api/checkout/v2/checkoutForm/Merchant/".GOOGLE_MERCHANT_ID;
}
else
{
$url="https://sandbox.google.com/checkout/api/checkout/v2/checkoutForm/Merchant/".GOOGLE_MERCHANT_ID;
}
$tax_total= $db->f("order_tax") + $db->f("order_shipping_tax");
$discount_total= $db->f("coupon_discount") + $db->f("order_discount");
$order_total= $db->f("order_subtotal") + $tax_total - $discount_total;
$q3= "SELECT country_2_code FROM #__vm_country WHERE country_3_code='".$dbst->getEscaped(trim($dbst->f("country")))."'";
$dbst2= new ps_DB;
$dbst2->setQuery($q3);
$dbst2->query();
$dbst2->next_record();
if($dbst2->f("country_2_code") == "GB")
	$country_code= "UK";
else
	$country_code= $dbst2->f("country_2_code");
$post_variables= array();
$i= 1;
$jdb= new ps_DB;
$query= "SELECT * FROM #__vm_order_item WHERE order_id=".(int)$db->f("order_id");
$jdb->query($query);
while($jdb->next_record()) {
	$post_variables['item_name_'.$i]= $jdb->f("order_item_name");
	$post_variables['item_description_'.$i]= $jdb->f("order_item_name")."<br />".$jdb->f("product_attribute");
	$post_variables['item_merchant_id_'.$i]= $db->f("order_number");
	$post_variables['item_quantity_'.$i]= $jdb->f("product_quantity");
	$post_variables['item_price_'.$i]= $jdb->f("product_item_price");
	$post_variables['item_currency_'.$i]= $_SESSION['vendor_currency'];
	$jdb2= & JFactory :: getDBO();
	$query= "SELECT `product_parent_id` FROM `#__vm_product` WHERE product_id='".$jdb->f("product_id")."'";
	$jdb2->setQuery($query);
	$parent_id= $jdb2->loadResult();
	if($parent_id > 0) {
		$product_id= $parent_id;
	} else {
		$product_id= $jdb->f("product_id");
	}
	$query= "SELECT `tax_rate` FROM `#__vm_tax_rate` as a LEFT JOIN `#__vm_product` as b ON a.tax_rate_id= b.product_tax_id WHERE b.product_id={$product_id}";
	$jdb2->setQuery($query);
	$my_taxrate= $jdb2->loadResult();
	if(empty($my_taxrate)) {
		$query= "SELECT `tax_rate` FROM `#__vm_tax_rate` WHERE tax_country='{$_SESSION['auth']['country']}'";
		$jdb2->setQuery($query);
		$my_taxrate= $jdb2->loadResult();
	}
	if(empty($my_taxrate)) {
		$my_taxrate= 0;
	}
	$post_variables['shopping-cart.items.item-'.$i.'.tax-table-selector']= "item-".$i;
	$post_variables['checkout-flow-support.merchant-checkout-flow-support.tax-tables.alternate-tax-tables.alternate-tax-table-'.$i.'.name']= "item-".$i;
	$post_variables['checkout-flow-support.merchant-checkout-flow-support.tax-tables.alternate-tax-tables.alternate-tax-table-'.$i.'.standalone']= "true";
	$post_variables['checkout-flow-support.merchant-checkout-flow-support.tax-tables.alternate-tax-tables.alternate-tax-table-'.$i.'.alternate-tax-rules.alternate-tax-rule-1.rate']= $my_taxrate;
	$post_variables['checkout-flow-support.merchant-checkout-flow-support.tax-tables.alternate-tax-tables.alternate-tax-table-'.$i.'.alternate-tax-rules.alternate-tax-rule-1.tax-areas.world-area-1']= "";
	$i++;
}
//Shipping
$shippingcosts=$db->f("order_shipping");

if (!empty($shippingcosts) && $shippingcosts!='0.00')
{
	$shipping_tax_rate = $db->f("order_shipping_tax")/ $db->f("order_shipping");
	$post_variables['item_name_'.$i]= "Shipping and handling";
	$post_variables['item_description_'.$i]= "Shipping and handling costs: ".$_SESSION['vendor_currency']." ".$db->f("order_shipping");
	$post_variables['item_merchant_id_'.$i]= $db->f("order_id");
	$post_variables['item_quantity_'.$i]= 1;

	$post_variables['item_price_'.$i]= sprintf("%.2f", $db->f("order_shipping"));
	$post_variables['item_currency_'.$i]= $_SESSION['vendor_currency'];
	$post_variables['shopping-cart.items.item-'.$i.'.tax-table-selector']= "item-".$i;

	$post_variables['checkout-flow-support.merchant-checkout-flow-support.tax-tables.alternate-tax-tables.alternate-tax-table-'.$i.'.name']= "item-".$i;
	$post_variables['checkout-flow-support.merchant-checkout-flow-support.tax-tables.alternate-tax-tables.alternate-tax-table-'.$i.'.standalone']= "true";
	$post_variables['checkout-flow-support.merchant-checkout-flow-support.tax-tables.alternate-tax-tables.alternate-tax-table-'.$i.'.alternate-tax-rules.alternate-tax-rule-1.rate']= $shipping_tax_rate;
	$post_variables['checkout-flow-support.merchant-checkout-flow-support.tax-tables.alternate-tax-tables.alternate-tax-table-'.$i.'.alternate-tax-rules.alternate-tax-rule-1.tax-areas.world-area-1']= "";
	$i++;
}	
if(!empty($discount_total)) {
	$discount_total=(-1) * $discount_total;
	$post_variables['item_name_'.$i]= "Coupon Amount";
	$post_variables['item_description_'.$i]= "Coupon Discount: ".$_SESSION['vendor_currency']." ".$discount_total;
	$post_variables['item_merchant_id_'.$i]= $db->f("order_id");
	$post_variables['item_quantity_'.$i]= 1;
	$post_variables['item_price_'.$i]= $discount_total;
	$post_variables['item_currency_'.$i]= $_SESSION['vendor_currency'];
	$i++;
}
	


$post_variables['continue_url']= SECUREURL."index.php?option=com_virtuemart&page=checkout.googleresult&order_id=".$db->f("order_id");
if(false) {
	$Itemid= JRequest :: getVar("Itemid");
	vmRedirect("index.php?page=account.order_details&order_id=".$db->f("order_id")."&option=com_virtuemart&Itemid=".$Itemid);
	echo '<form action="'.$url.'" method="post" target="_blank" id="google" name="google"><input type="hidden" name="googleCheckout" value="octl53wDFSC-rSEy-S6gRa-jWtb" />';
	echo '<input type="image" name="Google Checkout" alt="Fast checkout through Google"
	src="images/checkout.gif?merchant_id='.GOOGLE_MERCHANT_ID.'&w=180&h=46&style=white&variant=text&loc=en_US"
	height="46" width="180"/>';
	foreach($post_variables as $name => $value) {
		echo '<input type="hidden" name="'.$name.'" value="'.htmlspecialchars($value).'" />';
	}
	echo '</form>';
} else {
	echo '
	<font color="red">Please review the order and click the google checkout button to finish the payments.</font><br/>
	<form action="'.$url.'" id="gForm" name="gForm" method="post" target="_blank"><input type="hidden" name="googleCheckout" value="octl53wDFSC-rSEy-S6gRa-jWtb" />';
	echo '<input type="image" name="Google Checkout" alt="Fast checkout through Google"
	src="http://checkout.google.com/buttons/checkout.gif?merchant_id='.GOOGLE_MERCHANT_ID.'&w=180&h=46&style=white&variant=text&loc=en_US"
	height="46" width="180"/>';
	foreach($post_variables as $name => $value) {
		echo '<input type="hidden" name="'.$name.'" value="'.htmlspecialchars($value).'" />';
	}
	echo '</form>';
}

      	?>
    </td>
  </tr>
</table>
<script type="text/javascript" language="JavaScript">
//<![CDATA[ 
gForm = document.getElementById('gForm'); 
if (typeof(gForm) != 'undefined' && (gForm!=null))
{
gForm.target = '';
gForm.submit();
}
//]]>
</script>
<br />
<?php
$db = $db_temp;
}
?>
<p>
	<a href="<?php $sess->purl(SECUREURL.basename($_SERVER['PHP_SELF'])."?page=account.order_details&order_id=". $order_id) ?>" onclick="if( parent.parent.location ) { parent.parent.location = this.href.replace(/index2.php/, 'index.php' ); };">
 		<?php echo $VM_LANG->_('PHPSHOP_ORDER_LINK') ?>
 	</a>
</p>
<?php
