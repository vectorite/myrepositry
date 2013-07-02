<?php if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );

/*
*
* @copyright Copyright (C) 2007 - 2010 RuposTel - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* One Page checkout is free software released under GNU/GPL and uses code from VirtueMart
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* 
*/
global $Itemid; 

global $VM_LANG;
 
//joomore hacked;
global $jmshortcode;
if (!empty($jmshortcode) && (is_object($jmshortcode)))
$lang = $jmshortcode->shortcode;

if(!class_exists('shopFunctionsF')) require(JPATH_VM_SITE.DS.'helpers'.DS.'shopfunctionsf.php');
$comUserOption=shopfunctionsF::getComUserOption();

if (!empty($newitemid))
$Itemid = $newitemid;

if (empty($registration_html)) $no_login_in_template = true; 

echo $intro_article; 

?>
<div id="top_basket_wrapper">
<?php
echo $op_basket; // will show either basket/basket_b2c.html.php or basket/basket_b2b.html 
echo $op_coupon; // will show coupon if enabled from common/couponField.tpl.php with corrected width to size
echo $html_in_between; // from configuration file.. if you don't want it, just comment it or put any html here to explain how should a customer use your cart, update quantity and so on
echo $google_checkout_button; // will load google checkout button if you have powersellersunite.com/googlecheckout installed

if (!empty($paypal_express_button)) { ?>
<div id="op_paypal_express" style="float: right; clear: both; width: 100%; padding-top: 10px;">
 <?php echo $paypal_express_button; ?>
</div>
<?php } 


?>
</div>
<?php

?>
<!-- main onepage div, set to hidden and will reveal after javascript test -->
<div <?php if (empty($no_jscheck) || (!defined("_MIN_POV_REACHED"))) echo 'style="display: none;"'; ?> id="onepage_main_div">
<?php if (empty($no_continue_link) && (!empty($continue_link)) && ($continue_link != '//')) {  ?>
<div class="continue_link_under_basket"><a href="<?php echo $continue_link ?>" class="continue_link"><?php echo $VM_LANG->_('PHPSHOP_CONTINUE_SHOPPING') ?></a></div>
<?php 
} 

?>
<!-- start of checkout form -->
<form action="<?php echo $action_url; ?>" method="post" name="adminForm" class="form-validate">

<!-- login box -->
<?php 
if (!empty($no_login_in_template))  {
 echo '<div style="display: none;">';
}
?>

<div class="op_inside loginsection">
<div class="op_rounded">
	<div><div><div><div>
	   <div class="op_rounded_fix">  
<div id="tab_selector">
 <button  id="op_login_btn" onclick="javascript: return op_unhide('logintab', 'registertab');"  type="button" style="border: none;"><span class="op_round" id="op_round_and_separator" style="width: 150px;"><span style="width: 146px;"><?php echo JText::_('COM_VIRTUEMART_LOGIN'); ?></span></span></button>
 <button  id="op_register_btn" onclick="javascript: return op_unhide('registertab', 'logintab');" style="border: none;" type="button"><span class="op_round" style="width: 150px;"><span id="span2" style="width: 150px;"><?php echo JText::_('COM_VIRTUEMART_REGISTER') ?></span></span></button>
</div>

                        	<div class="op_rounded_content">
								<div style="padding-left: 10px;">
								<div>
								<div>
								    <div id="registertab">
									  <?php	echo $registration_html; ?>
									</div>
									<div id="logintab" style="display: none;">
									    			<div class="formLabel">
			   <label for="username_login"><?php echo JText::_('COM_VIRTUEMART_REGISTER_UNAME'); ?>:</label>
			</div>
			<div class="formField">
			  <input type="text" id="username_login" name="username_login" class="inputbox" size="20" autocomplete="off" />
			</div>
			<div class="formLabel">
			 <label for="passwd_login"><?php echo JText::_('COM_VIRTUEMART_SHOPPER_FORM_PASSWORD_1') ?>:</label> 
			</div>
			<div class="formField">
				<input type="password" id="passwd_login" name="<?php 
				if ((version_compare(JVERSION,'1.7.0','ge')) || (version_compare(JVERSION,'2.5.0','ge'))) echo 'password';
				else echo 'passwd'; 
				?>" class="inputbox" size="20" onkeypress="return submitenter(this,event)"  autocomplete="off" />
			</div>
	<br />
	<br />
	<?php if (JPluginHelper::isEnabled('system', 'remember')) : ?>
	<br />
	<div class="formLabel">	<label for="remember_login"><?php echo $remember_me = VmConfig::isJ15()? JText::_('Remember me') : JText::_('JGLOBAL_REMEMBER_ME'); ?></label></div>
	<div class="formField">
	<input type="checkbox" name="remember" id="remember_login" value="yes" checked="checked" />
	</div>
	<?php else : ?>
	<input type="hidden" name="remember" value="yes" />
	<?php endif; ?>
	<div class="formField" style="width: 60%; padding-left: 20%;">
	<span style="float: left; padding-right: 5%; padding-top: 5px;">
	(<a title="<?php echo JText::_('COM_VIRTUEMART_ORDER_FORGOT_YOUR_PASSWORD'); ?>" href="<?php echo $lostPwUrl =  JRoute::_( 'index.php?option='.$comUserOption.'&view=reset' ); ?>"><?php echo JText::_('COM_VIRTUEMART_ORDER_FORGOT_YOUR_PASSWORD');  ?></a>)
	</span>
	<input type="button" name="LoginSubmit" class="button" value="<?php echo JText::_('COM_VIRTUEMART_LOGIN'); ?>" onclick="javascript: return op_login();"/>

	<input type="hidden" name="return" value="<?php echo $return_url; ?>" />
	<input type="hidden" name="<?php echo JUtility::getToken(); ?>" value="1" />
	</div>

									 
									</div>
								</div>
								</div>
								</div>
								<br style="clear: both;"/>

							</div>
							
	  </div>
	 </div></div></div></div>
</div>
</div>
<?php
if (!empty($no_login_in_template))  {
 echo '</div>';
}
?>
<!-- user registration and fields -->
<div class="op_inside">
<div class="op_rounded">
	<div><div><div><div>
	   <div class="op_rounded_fix" style="width: 100%;">  
                             <h3>
                                <span class="col-module_header_r">
                                <span class="col-module_header_l">
                                <span class="col-module_header_arrow">
                                    <span class="col-module_header_color"><?php echo JText::_('COM_VIRTUEMART_USER_FORM_BILLTO_LBL'); ?></span>                           		
                                </span>
                                </span>
                             	</span>  
                        	</h3>
                        	<div class="op_rounded_content">
								<div>
								<div>
								<?php echo $op_userfields; // they are fetched from ps_userfield::listUserFields ?>
								</div>
								</div>
								<br style="clear: both;"/>

							</div>
	  </div>
	 </div></div></div></div>
</div>
</div>



<!-- end user registration and fields -->
<!-- shipping address info -->
<?php 
// stAn we disable ship to section only to unlogged users

if (NO_SHIPTO != '1') { ?>
<div class="op_inside">
<div class="op_rounded">
	<div><div><div><div>
	   <div class="op_rounded_fix" style="width: 100%;">  
                             <h3>
                                <span class="col-module_header_r">
                                <span class="col-module_header_l">
                                <span class="col-module_header_arrow">
                                    <span class="col-module_header_color"><?php echo JText::_('COM_VIRTUEMART_USER_FORM_SHIPTO_LBL'); ?></span>                           		
                                </span>
                                </span>
                             	</span>  
                        	</h3>
                        	<div class="op_rounded_content">
								<div>
								<div>
								<input type="checkbox" id="sachone" name="sa" value="adresaina" onkeypress="showSA(sa, 'idsa');" onclick="javascript: showSA(sa, 'idsa');" autocomplete="off" />
								<?php echo JText::_('COM_VIRTUEMART_USER_FORM_ADD_SHIPTO_LBL');  ?>
								<div id="idsa" style="display: none;">
								  <?php echo $op_shipto; // will list shipping user fields from ps_userfield::listUserFields with modification of ids and javascripts ?>
								</div>
								</div>
								</div>
								<br style="clear: both;"/>

							</div>
	  </div>
	 </div></div></div></div>
</div>
</div>

<?php }

 ?>
<!-- end shipping address info -->

<div class="op_inside" <?php if ($no_shipping || ($shipping_inside_basket)) echo 'style="display: none;"'; ?>>
<div class="op_rounded">
	<div><div><div><div>
	   <div class="op_rounded_fix" style="width: 100%;">  
                             <h3>
                                <span class="col-module_header_r">
                                <span class="col-module_header_l">
                                <span class="col-module_header_arrow">
                                    <span class="col-module_header_color"><?php echo $VM_LANG->_('PHPSHOP_ORDER_PRINT_SHIPPING_LBL'); ?></span>                           		
                                </span>
                                </span>
                             	</span>  
                        	</h3>
                        	<div class="op_rounded_content">
								<!-- shipping methodd -->
								<div id="ajaxshipping" style="width: 90%;">
								<?php echo $shipping_method_html; // this prints all your shipping methods from checkout/list_shipping_methods.tpl.php ?>
								</div>
								<br style="clear: both;"/>
								<!-- end shipping methodd -->

							</div>
	  </div>
	 </div></div></div></div>
</div>
</div>


<!-- payment method -->
<?php if (!empty($op_payment))
{
?>
<div class="op_inside">
<div class="op_rounded">
	<div><div><div><div>
	   <div class="op_rounded_fix" style="width: 100%;">  
                             <h3>
                                <span class="col-module_header_r">
                                <span class="col-module_header_l">
                                <span class="col-module_header_arrow">
                                    <span class="col-module_header_color"><?php echo JText::_('COM_VIRTUEMART_CART_PAYMENT'); ?></span>                           		
                                </span>
                                </span>
                             	</span>  
                        	</h3>
                        	<div class="op_rounded_content">


<?php echo $op_payment; ?>
<br style="clear: both;"/>
								<!-- end shipping methodd -->

							</div>
	  </div>
	 </div></div></div></div>
</div>
</div>




<?php 
} 
?>
<!-- end payment method -->

<!-- customer note box -->

<?php
	
if($show_full_tos)
{
?>

				                                                   	
<!-- remove this section if you have 'must agree to tos' disabled' -->


<!-- show full TOS -->
	
<!-- end of full tos -->

<?php 

{
?>
<div class="op_inside">
<div class="op_rounded">
	<div><div><div><div>
	   <div class="op_rounded_fix" style="width: 100%;">  
                             <h3>
                                <span class="col-module_header_r">
                                <span class="col-module_header_l">
                                <span class="col-module_header_arrow">
                                    <span class="col-module_header_color"><?php echo JText::_('COM_VIRTUEMART_CART_TOS'); ?></span>                           		
                                </span>
                                </span>
                             	</span>  
                        	</h3>
                        	<div class="op_rounded_content">



<?php 

	echo $tos_con;
?>

<br style="clear: both;"/>
								<!-- end shipping methodd -->

							</div>
	  </div>
	 </div></div></div></div>
</div>
</div>
<?php 
}
}
?>
<!-- end of customer note -->
<!-- some little tricks for virtuemart classes -->
<input type="hidden" name="checkout_last_step" value="1" /><input type="hidden" name="page" value="checkout.onepage" /><input type="hidden" name="onepage" value="1" /><input type="hidden" name="checkout_this_step[]" value="CHECK_OUT_GET_SHIPPING_METHOD" /><input type="hidden" name="checkout_this_step[]" value="CHECK_OUT_GET_PAYMENT_METHOD" /><input type="hidden" name="checkout_this_step[]" value="CHECK_OUT_GET_FINAL_CONFIRMATION" />

<div class="op_inside">
<div class="op_rounded">
	<div><div><div><div>
	   <div class="op_rounded_fix" style="width: 100%;">  
                             <h3>
                                <span class="col-module_header_r">
                                <span class="col-module_header_l">
                                <span class="col-module_header_arrow">
                                    <span class="col-module_header_color"><?php 
									$comment = JText::_('COM_VIRTUEMART_COMMENT_CART'); 
								    if ($comment == 'COM_VIRTUEMART_COMMENT_CART')
									echo JText::_('COM_VIRTUEMART_COMMENT'); 
									else echo $comment;
									
									?></span>                           		
                                </span>
                                </span>
                             	</span>  
                        	</h3>
                        	<div class="op_rounded_content">
                        	 <div>
                        	 <div style="width: 300px; float: left;">
							 <span id="customer_note_input" class="formField">
								<label for="customer_note_field"><?php 	
									$comment = JText::_('COM_VIRTUEMART_COMMENT_CART'); 
								    if ($comment == 'COM_VIRTUEMART_COMMENT_CART')
									echo JText::_('COM_VIRTUEMART_COMMENT'); 
									else echo $comment; ?>:</label>
							   <textarea rows="3" cols="30" name="customer_comment" id="customer_note_field" ></textarea>
							
							 </span>
							 <br style="clear: both;" />
							 <div id="payment_info" style="padding-top; 20px;"></div>
                        	 </div>
                        	 <div id="rbsubmit" style="width: 330px; float: right;">
                        	   <!-- show total amount at the bottom of checkout and payment information, don't change ids as javascript will not find them and OPC will not function -->
<div id="onepage_info_above_button">
<div id="onepage_total_inc_sh">
<?php
/*
 content of next divs will be changed by javascript, please don't change it's id, you may freely format it and if you add any content of txt fields it will not be overwritten by javascript 
*/
?>
<div id="totalam">
<div id="tt_order_subtotal_div"><span id="tt_order_subtotal_txt" class="bottom_totals_txt"></span><span id="tt_order_subtotal" class="bottom_totals"></span><br class="op_clear"/></div>
<div id="tt_order_payment_discount_before_div"><span id="tt_order_payment_discount_before_txt" class="bottom_totals_txt"></span><span class="bottom_totals" id="tt_order_payment_discount_before"></span><br class="op_clear"/></div>
<div id="tt_order_discount_before_div"><span id="tt_order_discount_before_txt" class="bottom_totals_txt"></span><span id="tt_order_discount_before" class="bottom_totals"></span><br class="op_clear"/></div>
<div id="tt_shipping_rate_div"><span id="tt_shipping_rate_txt" class="bottom_totals_txt"></span><span id="tt_shipping_rate" class="bottom_totals"></span><br class="op_clear"/></div>
<div id="tt_shipping_tax_div"><span id="tt_shipping_tax_txt" class="bottom_totals_txt"></span><span id="tt_shipping_tax" class="bottom_totals"></span><br class="op_clear"/></div>
<div id="tt_tax_total_0_div"><span id="tt_tax_total_0_txt" class="bottom_totals_txt"></span><span id="tt_tax_total_0" class="bottom_totals"></span><br class="op_clear"/></div>
<div id="tt_tax_total_1_div"><span id="tt_tax_total_1_txt" class="bottom_totals_txt"></span><span id="tt_tax_total_1" class="bottom_totals"></span><br class="op_clear"/></div>
<div id="tt_tax_total_2_div"><span id="tt_tax_total_2_txt" class="bottom_totals_txt"></span><span id="tt_tax_total_2" class="bottom_totals"></span><br class="op_clear"/></div>
<div id="tt_tax_total_3_div"><span id="tt_tax_total_3_txt" class="bottom_totals_txt"></span><span id="tt_tax_total_3" class="bottom_totals"></span><br class="op_clear"/></div>
<div id="tt_tax_total_4_div"><span id="tt_tax_total_4_txt" class="bottom_totals_txt"></span><span id="tt_tax_total_4" class="bottom_totals"></span><br class="op_clear"/></div>
<div id="tt_order_payment_discount_after_div"><span id="tt_order_payment_discount_after_txt" class="bottom_totals_txt"></span><span id="tt_order_payment_discount_after" class="bottom_totals"></span><br class="op_clear"/></div>
<div id="tt_order_discount_after_div"><span id="tt_order_discount_after_txt" class="bottom_totals_txt"></span><span id="tt_order_discount_after" class="bottom_totals"></span><br class="op_clear"/></div>
<div id="tt_total_div"><span id="tt_total_txt" class="bottom_totals_txt"></span><span id="tt_total" class="bottom_totals"></span><br class="op_clear"/></div>
</div>
<?php 
/*
* END of order total at the bottom
*/
?>
</div>
 <div><br /></div>
<!-- content of next div will be changed by javascript, please don't change it's id -->
 
<!-- end of total amount and payment info -->
<!-- submit button -->
 <br />
 
 <!-- show TOS and checkbox before button -->
<?php

if ($tos_required)
{

?>
	<div id="agreed_div" class="formLabel " style="text-align: left; margin-left: -20px;">
	

<input value="1" type="checkbox" id="agreed_field" name="tosAccepted" <?php if (!empty($agree_checked)) echo ' checked="checked" '; ?> class="terms-of-service"  required="required" autocomplete="off" />
					<label for="agreed_field"><?php echo JText::_('COM_VIRTUEMART_I_AGREE_TO_TOS'); 
					if (!empty($tos_link))
					{
					?><a target="_blank" href="<?php echo $tos_link; ?>" onclick="javascript: return op_openlink(this); " >(<?php echo JText::_('COM_VIRTUEMART_CART_TOS'); ?>)</a><?php } ?></label>
				
		<strong>* </strong> 
	</div>
	<div class="formField" id="agreed_input">
</div>


<?php
}
?>
<!-- end show TOS and checkbox before button -->

 <div style="float: left; clear: both;">
	<button id="confirmbtn_button" type="submit" autocomplete="off" onclick="<?php echo $onsubmit; ?>" ><span class="op_round"><span id="confirmbtn" style="width: 250px;"><?php echo $VM_LANG->_('PHPSHOP_ORDER_CONFIRM_MNU') ?></span></span></span></button>
 </div>
<br style="clear: both;"/>
</div>
<!-- end of submit button -->




                        	 </div>
                        	
                        	</div> 
                        	</div>
                        	<div style="clear: both;"></div>
	   </div>
	</div></div></div></div>  
</div>
</div>

<!-- end of tricks -->


</form>
<!-- end of checkout form -->
<!-- end of main onepage div, set to hidden and will reveal after javascript test -->
</div>
<div id="tracking_div"></div>


