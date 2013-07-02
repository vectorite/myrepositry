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

	defined( '_JEXEC' ) or die( 'Restricted access' );
?>

    Configuration for language: 
		<select name="language" id="language_selector" onchange="javascript:sp_toggleD('lang_'+this.value+'_table')">
		<?php
		foreach($this->langs as $p)
		{
		 $str = $p;
		 ?>
		 <option value=<?php echo '"'.$str.'" '; 
		 if (is_object($p))
		 if ($p->published)  
		 { 
		  echo ' selected="selected" ';
		  $def_lang = "lang_".$str;
		 }
		 ?>><?php echo $str; ?></option>
		 <?php
		}
		
		?>

	
		
	</select>
		<?php
		foreach($this->langs as $p)
		{
		 $str = $p;	
		 $str = "lang_".$str;	
		?>

        <table class="admintable" id="<?php echo $str ?>_table" <?php if (isset($firstover)) { echo ' style="display: none; width: 100%;"'; } else { echo ' style="width: 100%;"'; $firstover = true; } ?> >
        <tr class="adminform" style="width: 100%;">
        <td><h3>Payment messages settings for <?php $t = explode('_', $str, 2); echo $t[1]; ?></h3>
                <input type="hidden" id="<?php echo 'op_'.$str.'_changed'; ?>" name="<?php echo 'op_'.$str.'_changed'; ?>" value="no" />
        </td>
        </tr>
            	<tr>
	    	<td class="key">
	     		<label for="<?php echo $str.'_' ?>ONEPAGE_PAYMENT_EXTRA_DEFAULT_INFO" >Default message for any payment method: </label>
	    	</td>
	    	<td  >
	     		<input onchange="javascript:op_langedit('<?php echo $str; ?>');" class="text_area" type="text" name="<?php echo $str.'_' ?>ONEPAGE_PAYMENT_EXTRA_DEFAULT_INFO" id="<?php echo $str.'_' ?>ONEPAGE_PAYMENT_EXTRA_DEFAULT_INFO" size="50" value="<?php 
	     		if (isset($this->lang_vars[$str]['ONEPAGE_PAYMENT_EXTRA_DEFAULT_INFO']))  
	     		echo $this->lang_vars[$str]['ONEPAGE_PAYMENT_EXTRA_DEFAULT_INFO'];
	     		?>"/>
	    	</td> 
	     	<td colspan="3">
	     	  Example: Click the button below to confirm order.
	    	</td>
			</tr>
            	<tr>
	    	<td class="key">
	     		<label for="<?php echo $str.'_' ?>ONEPAGE_PAYMENT_BUTTON_DEFAULT" >Default text on submit button: </label>
	    	</td>
	    	<td  >
	     		<input class="text_area" type="text" onchange="javascript:op_langedit('<?php echo $str; ?>');"  name="<?php echo $str.'_' ?>ONEPAGE_PAYMENT_BUTTON_DEFAULT" id="<?php echo $str.'_' ?>ONEPAGE_PAYMENT_BUTTON_DEFAULT" size="50" value="<?php
			     		if (isset($this->lang_vars[$str]['ONEPAGE_PAYMENT_BUTTON_DEFAULT']))  
	     		echo $this->lang_vars[$str]['ONEPAGE_PAYMENT_BUTTON_DEFAULT'];
	     		else
	     		echo $this->lang_vars[$str]['PHPSHOP_ORDER_CONFIRM_MNU'];

	     		?>"/>
	    	</td> 
	     	<td colspan="3">
	     	  Example: Confirm Order
	    	</td>
			</tr>

        <?php
	foreach($this->pms as $p)
	{
	    $name = $p['payment_method_name'];
	    $id = $p['payment_method_id'];
	?>
	<tr class="row1">
	    <td class="key">
	     <label for="<?php echo $str.'_' ?>ONEPAGE_PAYMENT_EXTRA_INFO_<?php echo $id; ?>" >Payment Info text for <?php echo $name  ?> </label>
	    </td>
	    <td  >
	     <input class="text_area" type="text" onchange="javascript:op_langedit('<?php echo $str; ?>');"  name="<?php echo $str.'_' ?>ONEPAGE_PAYMENT_EXTRA_INFO_<?php echo $id; ?>" id="<?php echo $str.'_' ?>ONEPAGE_PAYMENT_EXTRA_INFO_<?php echo $id; ?>" size="50" value="<?php 
     		if (isset($this->lang_vars[$str]['ONEPAGE_PAYMENT_EXTRA_INFO_'.$id]))  
	     		echo $this->lang_vars[$str]['ONEPAGE_PAYMENT_EXTRA_INFO_'.$id];
	     
	     ?>"/> 
	 
	    </td>
	    <td colspan="3">
	    This will be shown above submit button. Please put any information for customer, what will happen or what should he do after clicking the button. Example: After clicking the submit button you will be redirected to PayPal.com
	    </td>
	</tr>
	<tr class="row0">
	    <td class="key">
	     <label for="<?php echo $str.'_' ?>ONEPAGE_PAYMENT_EXTRA_INFO_BUTTON_<?php echo $id; ?>" >Payment submit button text for <?php echo $name;  ?> </label>
	     
	    </td>
	    <td  >
	     <input class="text_area" type="text" onchange="javascript:op_langedit('<?php echo $str; ?>');"  name="<?php echo $str.'_' ?>ONEPAGE_PAYMENT_EXTRA_INFO_BUTTON_<?php echo $id; ?>" id="<?php echo $str.'_' ?>ONEPAGE_PAYMENT_EXTRA_INFO_BUTTON_<?php echo $id; ?>" size="50" value="<?php 
     		if (isset($this->lang_vars[$str]['ONEPAGE_PAYMENT_EXTRA_INFO_BUTTON_'.$id]))  
	     		echo $this->lang_vars[$str]['ONEPAGE_PAYMENT_EXTRA_INFO_BUTTON_'.$id];
	     
	     ?>" />
	  
	    </td>
	    <td colspan="3">
	    This will be shown on the submit button. If left empty a default message will be shown. Example: Confirm order, Proceed to PayPal.com to pay
	    </td>
	</tr>
	

<?php } ?>
        <tr class="adminform">
        <td><h3>Shipping related messages for <?php $t = explode('_', $str, 2); echo $t[1]; ?></h3></td>
        </tr>
        	<tr>
	    	<td class="key">
	     		<label for="<?php echo $str.'_' ?>ONEPAGE_SHIP_COUNTRY_INVALID" >Non existent standard shipping for selected country message</label>
	    	</td>
	    	<td  >
	     		<input class="text_area" onchange="javascript:op_langedit('<?php echo $str; ?>');"  type="text" name="<?php echo $str.'_' ?>ONEPAGE_SHIP_COUNTRY_INVALID" id="<?php echo $str.'_' ?>ONEPAGE_SHIP_COUNTRY_INVALID" size="50" value="<?php
     			if (isset($this->lang_vars[$str]['ONEPAGE_SHIP_COUNTRY_INVALID']))  
	     			echo $this->lang_vars[$str]['ONEPAGE_SHIP_COUNTRY_INVALID'];
	     		else 
	     	echo "We are sorry, but we don't ship to chosen country. Please select a different country or contact us by phone.";
  		
	     		?>"/>
	    	</td>
	     	<td colspan="3">
	     		This message will be shown instead of shipping methods when there is none available for a selected country or on checkout validation. If you use other modules than standard shipping, leave this empty. Javascript will not check for validity of other shipping modules than standard shipping. Example: We are sorry, but we don't ship to chosen country. Please select a different country or contact us by phone.
	    	</td>
			</tr>
			        	<tr>
	    	<td class="key">
	     		<label for="<?php echo $str.'_' ?>ONEPAGE_SHIP_COUNTRY_CHANGED" >Invalid or no shipping is selected</label>
	    	</td>
	    	<td  >
	     		<input class="text_area" onchange="javascript:op_langedit('<?php echo $str; ?>');"  type="text" name="<?php echo $str.'_' ?>ONEPAGE_SHIP_COUNTRY_CHANGED" id="<?php echo $str.'_' ?>ONEPAGE_SHIP_COUNTRY_CHANGED" size="50" value="<?php
     			if (isset($this->lang_vars[$str]['ONEPAGE_SHIP_COUNTRY_CHANGED']))  
	     			echo $this->lang_vars[$str]['ONEPAGE_SHIP_COUNTRY_CHANGED'];
	     		else
	     		 	echo $this->lang_vars[$str]['PHPSHOP_CHECKOUT_ERR_OTHER_SHIP'];
  		
	     		?>"/>
	    	</td>
	     	<td colspan="3">
	     		This message will be shown when invalid or no shipping is selected on submit order in alert box. 
	    	</td>
			</tr>

			<tr>
	    	<td class="key">
	     		<label for="<?php echo $str.'_' ?>ONEPAGE_SHIP_DELAYED" >Delayed shipping</label>
	    	</td>
	    	<td  >
	     		<input class="text_area" onchange="javascript:op_langedit('<?php echo $str; ?>');"  type="text" name="<?php echo $str.'_' ?>ONEPAGE_SHIP_DELAYED" id="<?php echo $str.'_' ?>ONEPAGE_SHIP_DELAYED" size="50" value="<?php
     			if (isset($this->lang_vars[$str]['ONEPAGE_SHIP_DELAYED']))  
	     			echo $this->lang_vars[$str]['ONEPAGE_SHIP_DELAYED'];
	     		//else
	     		// 	echo 'Shipping rates will be shown after you fill the address.';
  		
	     		?>"/>
	    	</td>
	     	<td colspan="3">
	     		This message will be shown if you are using delayed shipping before customer leaves last shipping field. Enter something like this: Shipping rates will be shown after filling address fields.
	    	</td>
			</tr>
			<tr>
	    	<td class="key">
	     		<label for="<?php echo $str.'_' ?>ONEPAGE_CLICK_HERE_TO_SHOW_SHIPPING" >Click here to update shipping</label>
	    	</td>
	    	<td  >
	     		<input class="text_area" onchange="javascript:op_langedit('<?php echo $str; ?>');"  type="text" name="<?php echo $str.'_' ?>ONEPAGE_CLICK_HERE_TO_SHOW_SHIPPING" id="<?php echo $str.'_' ?>ONEPAGE_CLICK_HERE_TO_SHOW_SHIPPING" size="50" value="<?php
     			if (isset($this->lang_vars[$str]['ONEPAGE_CLICK_HERE_TO_SHOW_SHIPPING']))  
	     			echo $this->lang_vars[$str]['ONEPAGE_CLICK_HERE_TO_SHOW_SHIPPING'];
  		
	     		?>"/>
	    	</td>
	     	<td colspan="3">
	     		This message is shown when using delayed shipping. It should say: Click here to update shipping. 
	    	</td>
			</tr>



        	<tr>
	    	<td class="key">
	     		<label for="<?php echo $str.'_' ?>ONEPAGE_ORDER_TOTAL_INCL_SHIPPING" >Order total message </label>
	    	</td>
	    	<td  >
	     		<input class="text_area" onchange="javascript:op_langedit('<?php echo $str; ?>');"  type="text" name="<?php echo $str.'_' ?>ONEPAGE_ORDER_TOTAL_INCL_SHIPPING" id="<?php echo $str.'_' ?>ONEPAGE_ORDER_TOTAL_INCL_SHIPPING" size="50" value="<?php 
	     		     			if (isset($this->lang_vars[$str]['ONEPAGE_ORDER_TOTAL_INCL_SHIPPING']))  
	     			echo $this->lang_vars[$str]['ONEPAGE_ORDER_TOTAL_INCL_SHIPPING'];
			?>"/>
	    	</td>
	     	<td colspan="3">
	     		This is shown above submit button. Example: Order total incl. shipping and payment fees:

	    	</td>
			</tr>
        	<tr>
	    	<td class="key">
	     		<label for="<?php echo $str.'_' ?>ONEPAGE_SHIPPING_ADDRESS" >Ship to section name</label>
	    	</td>
	    	<td  >
	     		<input class="text_area" onchange="javascript:op_langedit('<?php echo $str; ?>');"  type="text" name="<?php echo $str.'_' ?>ONEPAGE_SHIPPING_ADDRESS" id="<?php echo $str.'_' ?>ONEPAGE_SHIPPING_ADDRESS" size="50" value="<?php 
	     		     			if (isset($this->lang_vars[$str]['ONEPAGE_SHIPPING_ADDRESS']))  
	     			echo $this->lang_vars[$str]['ONEPAGE_SHIPPING_ADDRESS'];
	     			else 
	     			echo $this->lang_vars[$str]['PHPSHOP_ADD_SHIPTO_2'];
			?>"/>
	    	</td>
	     	<td colspan="3">
	     	  Name of shipping section in checkout page. Example: Shipping address
	    	</td>
			</tr>
        	<tr>
	    	<td class="key">
	     		<label for="<?php echo $str.'_' ?>ONEPAGE_SHIPPING_ADDRESS_IS_DIFFERENT" >Checkbox text to show shipping address</label>
	    	</td>
	    	<td  >
	     		<input class="text_area" onchange="javascript:op_langedit('<?php echo $str; ?>');"  type="text" name="<?php echo $str.'_' ?>ONEPAGE_SHIPPING_ADDRESS_IS_DIFFERENT" id="<?php echo $str.'_' ?>ONEPAGE_SHIPPING_ADDRESS_IS_DIFFERENT" size="50" value="<?php 
	     		     			if (isset($this->lang_vars[$str]['ONEPAGE_SHIPPING_ADDRESS_IS_DIFFERENT']))  
	     			echo $this->lang_vars[$str]['ONEPAGE_SHIPPING_ADDRESS_IS_DIFFERENT'];
			?>"/>
	    	</td>
	     	<td colspan="3">
	     		This is text on the checkbox for shipping address. If it is not checked, the shipping address fields are not shown. Example: Shipping address is different from bill to address.
	    	</td>
			</tr>
        <tr class="adminform">
        <td><h3>Error related checkout messages for <?php $t = explode('_', $str, 2); echo $t[1]; ?></h3></td>
        </tr>
		
        	<tr>
	    	<td class="key">
	     		<label for="<?php echo $str.'_' ?>ONEPAGE_ADD_USER_ERROR" >Add User problem</label>
	    	</td>
	    	<td  >
	     		<input class="text_area" onchange="javascript:op_langedit('<?php echo $str; ?>');"  type="text" name="<?php echo $str.'_' ?>ONEPAGE_ADD_USER_ERROR" id="<?php echo $str.'_' ?>ONEPAGE_ADD_USER_ERROR" size="50" value="<?php 
	     		     			if (isset($this->lang_vars[$str]['ONEPAGE_ADD_USER_ERROR']))  
	     			echo $this->lang_vars[$str]['ONEPAGE_ADD_USER_ERROR'];
	     			else 
	     			echo "Your user data were not saved. Most common reason is that you are probably already registered.  If so, please login via form on left. ";
			?>"/>
	    	</td>
	     	<td colspan="3">
	     	 This text is shown on checkout page after user data could not be saved and order could not be processed. This occurs when user tries to register with already registered email address if you have any other option selected then 'NO_REGISTRATION' in Virtuemart global settings.
	    	</td>
		</tr>
        	<tr>
	    	<td class="key">
	     		<label for="<?php echo $str.'_' ?>ONEPAGE_SAVE_ORDER_ERROR" >General error message</label>
	    	</td>
	    	<td  >
	     		<input class="text_area" onchange="javascript:op_langedit('<?php echo $str; ?>');"  type="text" name="<?php echo $str.'_' ?>ONEPAGE_SAVE_ORDER_ERROR" id="<?php echo $str.'_' ?>ONEPAGE_SAVE_ORDER_ERROR" size="50" value="<?php 
	     		     			if (isset($this->lang_vars[$str]['ONEPAGE_SAVE_ORDER_ERROR']))  
	     			echo $this->lang_vars[$str]['ONEPAGE_SAVE_ORDER_ERROR'];
	     			else echo "Your order WAS NOT saved. Please fill all the required fields. If problem persists, please let us know by email. ";
			?>"/>
	    	</td>
	     	<td colspan="3">
	     		If you get this message please let us know for debugging. Chek your ps_checkout and ps_shopper. Example: Your order WAS NOT saved. Please fill all the required fields. If problem persists, please let us know by email. 
	    	</td>
		</tr>

        	<tr>
	    	<td class="key">
	     		<label for="<?php echo $str.'_' ?>ONEPAGE_THANKYOU_ORDER_OK" >Order saved message</label>
	    	</td>
	    	<td  >
	     		<input class="text_area" onchange="javascript:op_langedit('<?php echo $str; ?>');"  type="text" name="<?php echo $str.'_' ?>ONEPAGE_THANKYOU_ORDER_OK" id="<?php echo $str.'_' ?>ONEPAGE_THANKYOU_ORDER_OK" size="50" value="<?php 
	     		     			if (isset($this->lang_vars[$str]['ONEPAGE_SHIPPING_ADDRESS']))  
	     			echo $this->lang_vars[$str]['ONEPAGE_THANKYOU_ORDER_OK'];
			?>"/>
	    	</td>
	     	<td colspan="3">
	     		This text is shown on thank you page in system info box when order is successfully saved. You don't have to translate this if you disabled positive messages.  Example: Your order was successfully saved. Thank you for your purchase.
	    	</td>
		</tr>
        	<tr>
	    	<td class="key">
	     		<label for="<?php echo $str.'_' ?>ONEPAGE_THANKYOU_ORDER_ERROR" >Order not saved.</label>
	    	</td>
	    	<td  >
	     		<input class="text_area" onchange="javascript:op_langedit('<?php echo $str; ?>');"  type="text" name="<?php echo $str.'_' ?>ONEPAGE_THANKYOU_ORDER_ERROR" id="<?php echo $str.'_' ?>ONEPAGE_THANKYOU_ORDER_ERROR" size="50" value="<?php 
	     		     			if (isset($this->lang_vars[$str]['ONEPAGE_THANKYOU_ORDER_ERROR']))  
	     			echo $this->lang_vars[$str]['ONEPAGE_THANKYOU_ORDER_ERROR'];
	     			else echo "Your order WAS NOT saved. Please contact us by email. ";
			?>"/>
	    	</td>
	     	<td colspan="3">
	     		User data were saved, but there was a problem with saving the order data. The reason might be: wrong shipping method, country, state, incompatible payment. If you get this message you should contact RuposTel team for support. Example: Your order WAS NOT saved. Please contact us by email.
	    	</td>
			</tr>
        	<tr>
	    	<td class="key">
	     		<label for="<?php echo $str.'_' ?>ONEPAGE_THANKYOU_ORDER_OK_BUT_PAYMENT_BAD" >Payment error message</label>
	    	</td>
	    	<td>
	     		<input class="text_area" onchange="javascript:op_langedit('<?php echo $str; ?>');"  type="text" name="<?php echo $str.'_' ?>ONEPAGE_THANKYOU_ORDER_OK_BUT_PAYMENT_BAD" id="<?php echo $str.'_' ?>ONEPAGE_THANKYOU_ORDER_OK_BUT_PAYMENT_BAD" size="50" value="<?php 
	     		     			if (isset($this->lang_vars[$str]['ONEPAGE_THANKYOU_ORDER_OK_BUT_PAYMENT_BAD']))  
	     			echo $this->lang_vars[$str]['ONEPAGE_THANKYOU_ORDER_OK_BUT_PAYMENT_BAD'];
	     			else echo "Your order was saved, but there was a problem with payment. We will contact you soon.";
			?>"/>
	    	</td>
	     	<td>
	     		If there is a payment processor between checkout page and thank you page and payment was not validated this message will be shown on thank you page in system info box. Example: Your order was saved, but there was a problem with payment. We will contact you soon.
	    	</td>
		</tr>
		<tr>
	    	<td class="key">
	     		<label for="<?php echo $str.'_' ?>ONEPAGE_THANKYOU_ORDER_OK_PAYMENT_OK" >Payment and order saved message</label>
	    	</td>
	    	<td>
	     		<input class="text_area" onchange="javascript:op_langedit('<?php echo $str; ?>');"  type="text" name="<?php echo $str.'_' ?>ONEPAGE_THANKYOU_ORDER_OK_PAYMENT_OK" id="<?php echo $str.'_' ?>ONEPAGE_THANKYOU_ORDER_OK_PAYMENT_OK" size="50" value="<?php 
	     		     			if (isset($this->lang_vars[$str]['ONEPAGE_THANKYOU_ORDER_OK_PAYMENT_OK']))  
	     			echo $this->lang_vars[$str]['ONEPAGE_THANKYOU_ORDER_OK_PAYMENT_OK'];
			?>"/>
	    	</td>
	     	<td>
	     	 Shown after successfull payment validation. You don't have to translate this if you disabled positive messages. Example: Your order and payment were saved. Thank you for your purchase.

	    	</td>
		</tr>



	
	</table>
<?php } 