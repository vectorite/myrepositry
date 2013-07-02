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
	ob_start();
	JToolBarHelper::Title('Configuration of One Page Checkout' , 'generic.png');
//	JToolBarHelper::install();
	JToolBarHelper::save();
/*	JToolBarHelper::apply(); */
	JToolBarHelper::cancel();

	$docj =& JFactory::getDocument();
	$url = JURI::base(true); 
	if (substr($url, strlen($url))!= '/') $url .= '/'; 
	$javascript =  "\n".' var op_ajaxurl = "'.$url.'"; '."\n";
    $javascript .= 'if(window.addEventListener){ // Mozilla, Netscape, Firefox' . "\n";
    $javascript .= '    window.addEventListener("load", function(){ op_runAjax(); }, false);' . "\n";
    $javascript .= '} else { // IE' . "\n";
    $javascript .= '    window.attachEvent("onload", function(){ op_runAjax(); });' . "\n";
    $javascript .= '}';
 
	$docj =& JFactory::getDocument();
	$docj->addScriptDeclaration( $javascript );	
	
	
      $session =& JFactory::getSession();
      
        jimport('joomla.html.pane');
        jimport('joomla.utilities.utility');
	JHTML::script('toggle_langs.js', 'administrator/components/com_onepage/views/config/tmpl/js/', false);
    

		  require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'config.php'); 
		  VmConfig::loadConfig(true); 

	



$document =& JFactory::getDocument();
$document->addScript('includes/js/joomla.javascript.js');

    include(JPATH_SITE.DS."components".DS."com_onepage".DS."config".DS."onepage.cfg.php");
   	$document =& JFactory::getDocument();
	$style = '
	
	div.current {
	 float: left;
	 padding: 5 !important;
	 width: 98%;
	}
	div {
	 text-indent: 0;
	}
	dl {
	 margin-left: 0 !important;
	 padding: 0 !important;
	}
	dd {
	 margin-left: 0 !important;
	 padding: 0 !important;
	 width: 100%;
	 
	}
	dd div {
	 margin-left: 0 !important;
	 padding-left: 0 !important;
	 text-indent: 0 !important;
	 
	 
	}
	div.current dd {
	 display: block;
	 padding-left:1px;
     padding-right:1px;
     margin-left:1px;
     margin-right:1px;
     text-indent:1px;
     float: left;
	}';
   $document->addStyleDeclaration($style);

//include_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_onepage'.DS.'assets'.DS.'export_helper.php');

// set default variables:
if (!isset($disable_onepage)) $disable_onepage = false;
if (!isset($must_have_valid_vat)) $must_have_valid_vat = true;
if (!isset($unlog_all_shoppers)) $unlog_all_shoppers = false;
if (!isset($allow_duplicit)) $allow_duplicit = true;
if (!isset($tpl_logged)) $tpl_logged = '';
if (!isset($tpl_unlogged)) $tpl_unlogged = '';
if (!isset($css_logged)) $css_logged = '';
if (!isset($css_unlogged)) $css_unlogged = '';
if (!isset($show_full_tos)) $show_full_tos = false;
if (!isset($payment_default)) $payment_default = 'default';
if (!empty($this->default_country))
if (!isset($default_shipping_country)) $default_shipping_country = $this->default_country;

$userConfig =& JComponentHelper::getParams('com_users');
$regA = $userConfig->get('allowUserRegistration');
$regB = $userConfig->get('useractivation');

if (!empty($_SESSION['onepage_err'])) $msg = unserialize($_SESSION['onepage_err']).'<br />';
else $msg = ''; 

if (isset($_SESSION['onepage_err']))
{
    $msg = @unserialize($_SESSION['onepage_err']);
	if (!empty($msg))
	{
	    echo '<div style="width = 100%; border: 2px solid red;">';
	    echo $msg;
	    unset($_SESSION['onepage_err']);
	    echo '</div>';
	}
}	
	if (isset($payments_to_hide))
	{
	 $payments_to_hide = str_replace(' ', '',  $payments_to_hide);
	 $pth = explode(',', $payments_to_hide);
	}
	if (!isset($pth)) $pth = array();

?>
	
	<form action="<?php echo JURI::base(); ?>index.php?option=com_onepage&amp;controller=config&amp;<?php echo $session->getName().'='.$session->getId(); ?>&amp;<?php JUtility::getToken();?>=1" method="post" name="adminForm" id="adminForm">

	<?php
        $pane =& JPane::getInstance('tabs', array('startOffset'=>0));
        echo $pane->startPane('pane');
        
        echo $pane->startPanel('General', 'panel1');
?>
<fieldset class="adminform">
        <legend>General configuration of One Page Checkout</legend>
        <table class="admintable" style="width: 100%;">
	<tr>
	    <td class="key">
	     <label for="disable_op">Disable One Page checkout </label> 
	    </td>
	    <td  >
	    <input id="disable_op" type="checkbox" name="disable_op" value="disable" <?php if ($disable_onepage === true) echo 'checked="checked"'; ?>/> 

		<input type="hidden" name="option" value="com_onepage" />
		<input type="hidden" name="view" value="config" />
		<input type="hidden" name="task" id="task" value="save" />
		<input type="hidden" name="backview" id="backview" value="panel1" />


	    </td><td>This will enable standard VirtueMart checkout process and disable all onepage plugins from which it is loaded. </td>
	</tr>

	
	
	<tr>
	    <td class="key">
	     <label for="agreed_notchecked" >Do NOT check agreement checkbox by default</label>
	    </td>
	    <td  >
	     <input type="checkbox" name="agreed_notchecked" id="agreed_notchecked" value="agreed_notchecked" <?php if (isset($agreed_notchecked)) if ($agreed_notchecked==true) echo 'checked="checked"';?> />
	    </td>
	    <td>When checked, agreement checkbox will not be checked by default at the checkout page. GERMAN users should enable this setting.  
	    </td>
	</tr>

	<tr>
	    <td class="key">
	     <label for="opc_link_type" >Add-to-cart as link configuration</label>
	    </td>
	    <td>
	     <select  name="opc_link_type" id="opc_link_type">
		   <option <?php if (empty($opc_link_type)) echo ' selected="selected" '; ?> value="0">Not enabled</option>
		   <option <?php if (!empty($opc_link_type) && ($opc_link_type == '1')) echo ' selected="selected" '; ?> value="1">Delete cart content and set link quantity</option>
		   <option <?php if (!empty($opc_link_type) && ($opc_link_type == '2')) echo ' selected="selected" '; ?> value="2">Do not increment quantity and do not delete cart</option>
		   <option <?php if (!empty($opc_link_type) && ($opc_link_type == '3')) echo ' selected="selected" '; ?> value="3">Increment quantity and do not delete cart</option>
		   
		 </select>
	    </td>
	    <td>Add-to-cart as link in One page checkout can be used as a direct link to your checkout page from external sites or from your customized themes. For further documentation of this feature please visit our <a href="https://www.rupostel.com/one-page-checkout-component/features/add-to-cart-as-a-link">documentation page</a>. 
	    </td>
	</tr>
	
	
	<tr>
	    <td class="key">
	     <label for="use_ssl" >Use SSL everywhere possible on checkout </label>
	    </td>
	    <td  >
	     <input type="checkbox" name="use_ssl" id="use_ssl" value="use_ssl" <?php if (!empty($use_ssl))  echo 'checked="checked"'; ?> />
	    </td>
	    <td> 
	    </td>
	</tr>
	

		

	<tr>
	    <td class="key">
	     <label for="g_analytics" >Use google analytics ecommerce tracking: </label>
	    </td>
	    <td>
		<select name="g_analytics" id="g_analytics">
		<option <?php if (($g_analytics==true) || (!isset($g_analytics))) echo 'selected="selected"'; ?> value="1">Yes</option>
		<option <?php if ($g_analytics===false) echo 'selected="selected"'; ?>value="0">No</option>
		</select> 
		
	    </td>
		<td>
		<?php if (false) { ?>
		<label for="google_id">Your Google analytics ID (optional)</label>
		<input type="text" id="google_id" name="google_id" value="<?php if (!empty($google_id)) echo $google_id; ?>" />
		<br style="clear: both;"/>
		<?php 
		}
		?>
		
		When you select 'Yes' pageTracker object of Google Analytics (javascript) will be used to track all transactions after clicking submit button. Item name, quantity, approximate next order ID with session hash, customer's country, state and city will be stored. You have to have analytics installed before using this feature. 
	    </td>
	</tr>


	

	
	


        </table>
    </fieldset>    
	
	<script language="javascript" type="text/javascript">
	//<![CDATA[
	var op_next = 0;
	<?php 
	if (false) 
	{
	?>
	var html1 = '<tr><td class="key"><label for="hidep_';
	var html2 = '" >Payment configuration: </label></td><td colspan="3" > For this shipping method <select style="max-width: 100px;"  id="hidepsid_';
	var html21 = '" name="hidepsid_';
	var html3 = '"><option value="del" selected="selected">NOT CONFIGURED/DELETE</option><?php
		  if (!empty($this->sids))
		  foreach ($this->sids as $k => &$sid)
		  {
		  ?><option value="<?php echo addslashes($k); ?>"><?php echo $sid ?></option><?php
		  }
		  ?></select> 	disable these payment payments methods (use CTRL)		<select style="max-width: 100px;" multiple="multiple" size="5" id="hidep_';
	var html31 = '" name="hidep_';	
	var html4 = '[]">	<?php
		if (!empty($this->pms))
		foreach($this->pms as $p)
		{
		 ?> <option value=<?php echo '"'.addslashes($p['payment_method_id']).'" '; ?>><?php echo addslashes($p['payment_method_name']);?></option><?php
		}
		?></select>and make default this one	<select style="max-width: 100px;" id="hidepdef_';
	var html41 = '"  name="hidepdef_';	
	var html5 = '">	<?php
	    if (!empty($this->pms))
		foreach($this->pms as $p)
		{
		 ?> <option value=<?php echo '"'.$p['payment_method_id'].'" ';  ?>><?php echo addslashes($p['payment_method_name']);?></option><?php
		}
		?></select><a href="#" onclick="javascript: return(addnew());"> Click here to ADD MORE ... </a>	    </td>	</tr>';
    <?php } ?>
	function submitbutton(task)
	{
	 if (task == 'template_upload') 
	  { 
	   document.adminForm.enctype = 'multipart/form-data';
	   document.adminForm.backview = 'exportpane';
	  }
	 if (task == 'template_update_upload')
	 {
	   document.adminForm.enctype = 'multipart/form-data';
	   document.adminForm.backview = 'exportpane';
	 }
	 
	 var d = document.getElementById('task');
	 d.value = task;
	 document.adminForm.submit();
	 return true;
	}
	//]]>
	</script>

	
<?php    

        echo $pane->endPanel();
    echo $pane->startPanel('Shipping', 'panel77');
?>
		<fieldset class="adminform">
        <legend>Shipping configuration for One Page Checkout</legend>
        <table class="admintable" id="comeshere" style="width: 100%;">
	<tr>
	    <td class="key">
	     <label for="op_disable_shipping" >Disable shipipng</label>
	    </td>
	    <td  >
		 <?php $sa = VmConfig::get('automatic_shipment', 0); 
		 
		 ?>
	     <input type="checkbox" name="op_disable_shipping" id="op_disable_shipping" <?php if (VmConfig::get('automatic_shipment', 0)==1) echo ' disabled="disabled" '; else if (!empty($op_disable_shipping))echo 'checked="checked"'; ?> value="op_disable_shipping"  />
	    </td>
	    <td>
	     <?php if (VmConfig::get('automatic_shipment', 1)) echo '<span style="color: red;">Automatic shipping selection cannot be enabled for this feature. </span>Please disable it at Virtuemart configuration page: <a href="index.php?option=com_virtuemart&view=config">Automatic Selected Shipment</a> at the tab checkout.'; ?>  You can disable shipping at all here. No plugins will be triggered and the shipping section will not show. 
	    </td>
	</tr>
		<tr>
	    <td class="key">
	     <label for="op_disable_shipto" >Disable Ship To address</label>
	    </td>
	    <td  >
	     <input type="checkbox" name="op_disable_shipto" id="op_disable_shipto" value="op_disable_shipto" <?php if (!empty($op_disable_shipto))echo 'checked="checked"';?> />
	    </td>
	    <td>
	     You can disable ship to section if you want your customers to enter ONLY bill to address to which you will ship to. This can also be used together with no shipping. 
	    </td>
	</tr>

		<tr>
	    <td class="key">
	     <label for="op_dontloadajax" >Do not use AJAX</label>
	    </td>
	    <td  >
	     <input type="checkbox" name="op_dontloadajax" id="op_dontloadajax" value="op_dontloadajax" <?php if (isset($op_dontloadajax)) if ($op_dontloadajax==true) echo 'checked="checked"';?> />
	    </td>
	    <td>
			Do not load AJAX at all. YOUR SHIPPING WILL NOT GET UPDATED BY CHANGE OF ADDRESS! This is usefull only for vendors who use only one destination for shipping and the shipping does not change when address, zip, state, country changes. On SSL, slow connections and mobile devices AJAX loads slower and therefore can be disabled here.
	    </td>
	</tr>
		<tr>
	    <td class="key">
	     <label for="op_loader" >Use loader image</label>
	    </td>
	    <td>
	     <input type="checkbox" name="op_loader" id="op_loader" value="op_loader" <?php if (!empty($op_loader)) echo 'checked="checked"';?> />
	    </td>
	    <td>
	       Use loader image in between AJAX request, so customer knows when shipping is being updated (this also works for EU VAT id when shipping is disabled)
	    </td>
	</tr>
   <?php 
   
   if (!empty($this->countries))
   {
   ?>
	<tr>
	    <td class="key">
	     <label for="default_country" >Default shipping country: </label>
	    </td>
	    <td>
		<select name="default_country" id="default_country">
		<option value="default">--VM Default--</option>
		<?php
		$sel = false;
		foreach($this->countries as $p)
		{
		 
		 ?>
		 <option value=<?php echo '"'.$p['virtuemart_country_id'].'"';
		 if ($p['virtuemart_country_id']==$default_shipping_country) { echo ' selected="selected" '; $sel = true;}
		 if (empty($default_shipping_country) || ($default_shipping_country == 'default'))
		 if ($p['virtuemart_country_id']==$this->default_country) echo ' selected="selected" ';
		 ?>><?php echo $p['country_name']; ?></option>
		 <?php
		}
		
		?>
		</select></td><td> The default country decides which shipping and taxes will be shown at first load of checkout. Default country is decided according to these rules: <br />1st: If <a href="http://www.rupostel.com/free-virtuemart-extensions/extensions/geolocator-for-joomla">com_geolocator</a> is available, the default country is selected according to the IP address of the customer<br />2nd: If geolocator is not available, then the default country is selected according to this configuration<br />3rd: If this configuration is left empty (vm default), the default country is set to vendor's country
	    </td>
	    
	</tr>
	<?php 
	}
	?>
<tr>
	    <td class="key">
	     <label for="op_default_zip"><br />Default ZIP code</label>
	    </td>
	    <td>
	     <input type="text" name="op_default_zip" id="op_default_zip" value="<?php if (!empty($op_default_zip)) echo urldecode($op_default_zip); else 
		 if ($op_default_zip === 0) echo '0';
		 else
		 echo '99999'; ?>"/>
	    </td>
	    <td>
	    <b>Please configure this for UPS,USPS,Fedex shipping methods.</b> If your shipping method does not accept empty ZIP code, this zip code will be used when no other found.<br />
		</td>
	</tr>
	<tr>
	    <td class="key">
	     <label for="op_default_shipping_zero">Enable zero price default shipping</label>
	    </td>
	    <td>
	     <input type="checkbox" name="op_default_shipping_zero" id="op_default_shipping_zero" <?php if (!empty($op_default_shipping_zero)) echo ' checked="checked" '; ?>/>
	    </td>
	    <td>
	    Enable 0 price default shipping. By default OPC chooses the lowest rate not equal zero. When this option is selected a zero priced shipping option is selected. This is usefull if your free shipping is handled by UPS or AWO coupons. 
		</td>
	</tr>

		<tr style="display: none;">
	    <td class="key">
	     <label for="shipping_error_override">[SOON AVAILABLE]<br />Shipping Errors Override</label>
	    </td>
	    <td colspan="2">
	    If HTML of your shipping methods includes this text, customer will see message "We are sorry, but we don't ship to chosen country. Please select a different country or contact us by phone." configurable from Labels tab instead of default shipping method's error output. Set the appropriate text in "Non existent standard shipping for selected country message" label. It is usefull for UPS and similar shipipng methods, where there is no ZIP, the shiping method gives an error. Set this to a text such as ERROR. Search is case sensitive.<br />
		Search for: <input type="text" name="shipping_error_override" id="shipping_error_override" value="<?php if (!empty($shipping_error_override)) echo urldecode($shipping_error_override); else echo 'ERROR'; ?>"/>
		</td><td>
	    </td>
	</tr>
	<tr>
	    <td class="key">
	     <label for="op_zero_weight_override" >[SOON AVAILABLE]<br />Zero Weight Free Shipping</label>
	    </td>
	    <td>
	     <input type="checkbox" name="op_zero_weight_override" id="op_zero_weight_override" value="op_free_shipping" <?php if (isset($op_zero_weight_override)) if ($op_zero_weight_override==true) echo 'checked="checked"';?> />
	    </td>
	    <td>
	     [APLPHA FEATURE FOR OPC2] If you use API based shipping method such as UPS and you also sell digital products with no weight, checking this checkbox the customer will get free shipping for 0 weight sum of all products in the cart.<br />You don't need to turn on this feature for Downloadable products or Gift Certificates by AWO.
	    </td>
	</tr>
	
	<tr>
	    <td class="key">
	     <label for="op_delay_ship" >Delayed Shipping</label>
	    </td>
	    <td>
	     <input type="checkbox" name="op_delay_ship" id="op_delay_ship" value="op_delay_ship" <?php if (!empty($op_delay_ship)) echo 'checked="checked"';?> />
	    </td>
	    <td>
	      Turn on delayed shipping. Shipping will be loaded ONLY by ajax thread which saves the time to load the checkout page. 
	    </td>
	</tr>

	<tr>
	    <td class="key">
	     <label for="op_last_field" >[SOON AVAILABLE]<br />Show shipping automatically on last field</label>
	    </td>
	    <td>
	     <input type="checkbox" name="op_last_field" id="op_last_field" value="op_last_field" <?php if (!empty($op_last_field)) echo 'checked="checked"';?> />
	    </td>
	    <td>
	      If you enable this option shipping will be automatically loaded after leaving the last address field. It is not recommend to use this option if your last address field is state or country. If you don't enable this option a customer must click on 'Click here to show shipping options.' Please configure the texts at the labels tab.
	    </td>
	</tr>

	
	
	<tr>
	    <td class="key">
	     <label for="shipping_inside_basket" >[SOON AVAILABLE]<br />Shipping inside basket</label>
	    </td>
	    <td>
	     <input disabled="disabled" class="shipping_inside_basket" type="checkbox" name="shipping_inside_basket" id="shipping_inside_basket" <?php if (!empty($shipping_inside_basket)) echo 'checked="checked"'; ?>/>
	    </td>
	    <td>This will put shipping inside your basket on the checkout page. It only works for shipping to one country! This will use custom basket_b2c.html (or b2b) in onepage template folder. 
	    </td>
	</tr>
		<tr>
	    <td class="key">
	     <label for="shipping_inside" >[SOON AVAILABLE]<br />Shipping as select box</label>
	    </td>
	    <td>
	     <input class="shipping_inside" type="checkbox" name="shipping_inside" id="shipping_inside" <?php if (!empty($shipping_inside)) echo 'checked="checked"'; ?>/>
	    </td>
	    <td colspan="2">It is recommended to delete all countries in VM config to which you don't ship! <br /><input class="shipping_inside_choose" type="checkbox" name="shipping_inside_choose" id="shipping_inside_choose" <?php if (!empty($shipping_inside_choose)) echo 'checked="checked"'; ?>/> Add "-- Select --" as default shipping option at first loading of checkout.
	    </td>
	</tr>

		<tr>
	 <td class="key">[SOON AVAILABLE]<br />Advanced default ship to country
	 </td>
	 <td colspan="2">
	   For current user lang choose a bill to and ship to country as default (This might be usefull if you are using JoomFish):<br />
	   <?php
	   	 $larr = array();
	     $num = 0;
	   
	   if (!empty($this->codes))
	   {
	   foreach ($this->codes as $uu)
	   {
	   ?>
	   <select name="op_lang_code_<?php echo $num; ?>">
	    <option <?php if (empty($default_country_array[$uu['code']])) echo ' selected="selected" '; ?> value="">NOT CONFIGURED</option>
	    <option  <?php if (!empty($default_country_array[$uu['code']])) echo ' selected="selected" '; ?> value="<?php echo $uu['code']; ?>"><?php echo $uu['code'] ?></option>
	   </select>
	   <select name="op_selc_<?php echo $num; ?>">
	    <?php 
		
		foreach ($this->countries as $p)  { 
		$ua = explode('-', $uu['code']); 
		$uc = $ua[1]; 
		$uc = strtoupper($uc); 
		
		?>
		 <option value=<?php echo '"'.$p['virtuemart_country_id'].'"';
		  if ((!empty($default_country_array[$uu['code']])) &&
		   ($default_country_array[$uu['code']]==$p['virtuemart_country_id'])) echo ' selected="selected" '; 
		  else
		  if ((empty($default_country_array[$uu['code']])))
		   {
		     if ($uc == $p['country_2_code']) echo ' selected="selected" '; 
		   }
		   ?>><?php echo $p['country_name']; ?></option>
	    <?php } ?>
	   </select><br />
	   <?php 
	   $num++;
	   $larr[] = $uu;
	   }
	   }
	   else
	   {
	    echo 'jos_languages not found. JoomFISH not installed.';
	   } ?>
	 </td>
	</tr>
	<tr>
	    <td class="key">
	     <label for="shipping_template" >[SOON AVAILABLE]<br />Use inbuilt shipping template <br /> Recommended for UPS-like </label>
	    </td>
	    <td>
	     <input class="shipping_template" type="checkbox" name="shipping_template" id="shipping_template" <?php if (!empty($shipping_template)) echo 'checked="checked"'; ?>/>
	    </td>
	    <td colspan="2">For all the next shipping features you will need to use custom shipping templates. By default they are styled in standard shipping style. This feature is not compatible with "shipping as select drop down feature" - you have to choose either of them.  
	    </td>
	</tr>

	
        </table>
        </fieldset>
<?php
    echo $pane->endPanel(); 
    echo $pane->startPanel('Payment', 'panel799');
    ?>
    <fieldset class="adminform">
    <legend>Payment configuration of One Page Checkout</legend>
     <table class="admintable" style="width: 100%;">
   <?php 
   
   if (!empty($this->pms))
   {
   ?>
	<tr>
	    <td class="key">
	     <label for="default_payment" >Default payment option: </label>
	    </td>
	    <td colspan="3" >
		<select id="default_payment" name="default_payment">
		<option value="default">--VM Default--</option>
		<?php
		
		foreach($this->pms as $p)
		{
		 ?>
		 <option value=<?php echo '"'.$p['payment_method_id'].'" '; if ($p['payment_method_id']==$payment_default) echo 'selected="selected" '; ?>><?php echo $p['payment_method_name'];?></option>
		 <?php
		}
		
		?>
		</select>
	    </td>
	</tr>

   <?php 
   }
   ?>

	<tr>
	    <td class="key">
	     <label for="hide_payment_if_one" >Hide payment</label>
	    </td>
	    <td>
	     <input name="hide_payment_if_one" type="checkbox"  id="hide_payment_if_one" <?php if (!empty($hide_payment_if_one)) echo 'checked="checked"'; ?>/>
	    </td>
	    <td>Hide payment if only one payment plugin is found. Please don't use this feature when are you processing credit cards. 
	    </td>
	</tr>
	<tr>
	    <td class="key">
	     <label for="payment_inside_basket" >[SOON AVAILABLE]<br />Payment inside basket</label>
	    </td>
	    <td>
	     <input disabled="disabled" class="payment_inside_basket" type="checkbox" name="payment_inside_basket" id="payment_inside_basket" <?php if (!empty($payment_inside_basket)) echo 'checked="checked"'; ?>/>
	    </td>
	    <td>This will put payment inside your basket on the checkout page. It does not work if you have Credit Card payment forms on your checkout! This will use custom basket_b2c.html (or b2b) in onepage template folder. 
	    </td>
	</tr>

	

	<tr>
	    <td class="key">
	     <label for="payment_inside" >[SOON AVAILABLE]<br />Payment as select drop-down</label>
	    </td>
	    <td>
	     <input disabled="disabled" class="payment_inside" type="checkbox" name="payment_inside" id="payment_inside" <?php if (!empty($payment_inside)) echo 'checked="checked"'; ?>/>
	    </td>
	    <td>This will put payment options inside your basket on the checkout page. Your Credit Card payments will not be shown! 
	    </td>
	</tr>
     </table>
    </fieldset>
    <?php 
    if (false) {
    ?>
    <fieldset class="adminform">
    <legend>Payment advanced configuration</legend>
        <?php
        jimport( 'joomla.filesystem.folder' );
        $editor =& JFactory::getEditor();
        $mce = true; 
         $ofolder = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'classes'.DS.'payment'.DS.'onepage';
         if (!file_exists($ofolder))
         {
          if (!JFolder::create($ofolder))
           echo '<span style="color: red;">Cannot create directory for this feature: '.$ofolder.'</span>';
         }
		?>
        <table class="admintable" style="width: 100%;">
        <tr>
	    <td class="key">
	     <label for="payment_advanced">Enabled advanced payment configuration </label>
	    </td>
	    <td >
	     <input type="checkbox" name="payment_advanced" id="payment_advanced" value="payment_advanced" <?php if (!empty($payment_advanced)) echo 'checked="checked"' ?> />
	    </td>
	    <td>
	     [ADVANCED_PAYMENT] Enable <b>overriding of ps_checkout::list_payment_methods, ps_payment::list_payment_radio</b> and appropriate payment template files. All the features below need this feature to be enabled. If your payment method modifies VirtueMart's list_payment_method function, these features might not work correctly for you. Please let us know and we might create a custom OnePage extension to support your payment module.  
	    </td>
		</tr>
		<tr>
		<td colspan="3">
		<a href="index.php?option=com_onepage&view=payment">Edit HTML per payment with content editor (SAVE CONFIGURATION FIRST!)</a>
		</td>
		</tr>
		<tr>
		<td colspan="3">
		
		and if joomfish available configure per language settings:
		<?php
		 if (!empty($this->codes))
	   {
	   ?>
	   <select name="payment_per_lang">
	   <?php
	   	   foreach ($this->codes as $uu)
	    {
		?>
	     <option value="<?php echo $uu['code']; ?>"><?php echo $uu['code']; ?></option>
		<?php 
		}
		?>
	   </select><input type="button" value="Edit..." onclick="javascript: submitbutton('perlangedit');" /><br />
	   If content editor cannot save your payment information please edit the files directly in /administrator/components/com_virtuemart/classes/payment/onepage/{lang2code}/{payment_id}.part.html On some systems there are problems with relative image paths when using SEO and JCE.
	   <?php 
	   $num++;
	   
	   
	   }
	   else
	   {
	    echo 'jos_languages not found. JoomFISH not installed.';
	   }
	   ?>
		</td>

		</tr>
        <?php
		if (!empty($this->pms))
        foreach($this->pms as $p)
        {
        ?>
        <tr>
        <td class="key">
        Set text for<br />
         <?php echo $p['payment_method_name'];
         ?>
        </td>
        <td colspan="2">
        <?php
         $id = $p['payment_method_id'];
         if (file_exists($ofolder.DS.$id.'.part.html')) 
         $html = file_get_contents($ofolder.DS.$id.'.part.html');
         else $html = ''; 
         
         $id = $p['payment_method_id']; 
         echo 'You can use {payment_discount} to insert payment fee or discount at a specific location. If not used, it will be automatically appended at the end.<br />';
		 if (!$mce)
		 echo $editor->display('payment_content_'.$id, $html, '550', '400', '60', '20', true);
		 else echo '<textarea id="payment_content_'.$id.'" style="width: 550px; height: 400px;" cols="60" rows="20">'.$html.'</textarea>';
		 echo '<input type="hidden" name="payment_contentid_'.$id.'"/>';
        ?>
        </td>
        </tr>
        <?php
        }
        ?>
        </table>
        
    </fieldset>
    
    <?php
    }
    echo $pane->endPanel(); 
	if (false)
	{
	echo $pane->startPanel('Coupons', 'panel7');
			?>
			 <fieldset class="adminform">
        <legend>Coupon Products configuration</legend>
        <table class="admintable" style="width: 100%;">
		<tr>
		 <h2>Experimental !</h2>This feature is built for K2 + Virtuemart coupon selling features. <br />
		 You need to set up available date for coupon products and optionally end date in attribute of the product. 
		</tr>
        <tr>
	    <td class="key">
	     <label for="fix_encoding">Coupon Products </label>
	    </td>
	    <td >
	     <input type="text" name="coupon_products" style="width: 200px;" id="coupon_products" value="<?php if (!empty($coupon_products)) echo $coupon_products; ?>" />
	    </td>
		<td>
		 Please enter product IDs separated by comma for which coupon code should be automatically generated on purchase and activated on order status change to confirmed. 
		</td>
		</tr>
        <tr>
		<tr>
	    <td class="key">
	     <label for="all_products">All products</label>
	    </td>
	    <td>
	     <input type="checkbox" name="all_products" style="width: 200px;" id="all_products" value="<?php if (!empty($coupon_products)) echo $coupon_products; ?>" />
	    </td>
		<td>
		 Please enter product IDs separated by comma for which coupon code should be automatically generated on purchase and activated on order status change to confirmed. 
		</td>
		</tr>
        <tr>
		</tr>
		</table>
		</fieldset>
		<?php
			echo $pane->endPanel(); 
			}
            echo $pane->startPanel('Display', 'panel7');
?>
		<fieldset class="adminform">
        <legend>General configuration of One Page Checkout</legend>
        <table class="admintable" style="width: 100%;">
	
		

        	
	   <tr> 
	    <td class="key">
	     <label for="selected_template">Choose an inbuilt template for your checkout</label>
	    </td>
	    <td colspan="2" >
	     <select name="selected_template" id="selected_template">
	     <?php
		 
	     if (!empty($this->templates)) 
	     foreach($this->templates as $t)
	     {
	      ?>
	      <option value="<?php echo $t; ?>" <?php if ((empty($selected_template) && ($t=='default')) || ($selected_template == $t)) echo ' selected="selected" '; ?>><?php echo $t; ?></option>
	      <?php
	     }
	     ?>
	     </select>
	     <input class="text_area" type="hidden" name="override_css_by_class" id="override_css_by_class" size="60" value=""/>
	     <input class="text_area" type="hidden" name="override_css_by_id" id="override_css_by_id" size="60" value="<?php if (!empty($op_ids)) echo $op_ids ?>"/>
		 <input type="hidden" name="php_logged" value="onepage.logged.tpl.php" />
		 <input type="hidden" name="css_logged" value="onepage.css" />
 		 <input type="hidden" name="php_unlogged" value="onepage.unlogged.tpl.php" />
		 <input type="hidden" name="css_unlogged" value="onepage.css" />

	    </td>
		</tr>
        <tr style="display: none;">
	    <td class="key">
	     <label for="op_numrelated">[SOON AVAILABLE]<br />Number of related products</label>
	    </td>
	    <td>
	     <input type="text" name="op_numrelated" id="op_numrelated" value="<?php if (empty($op_numrelated) || (!is_numeric($op_numrelated))) echo '0'; else echo $op_numrelated; ?>" />
	    </td>
	    <td>
	     Made for two-step templates. For every product in the cart, there is a search in order history and products which were bought together with those in the cart are shown. If not enough, their related products are shown and if still not enough products of price up to 30 percent of order subtotal are shown. (query is in basket.php) You might need to customize your relatedProducts and productSnapShot template. If you don't use related products on your checkout, leave this 0. 
	    </td>
		</tr>
		
        <tr>
	    <td class="key">
	     <label for="op_customitemid">Custom ItemId for checkout page </label>
	    </td>
	    <td>
	     <input type="text" id="op_customitemid" value="<?php if (!empty($newitemid)) echo $newitemid; ?>" name="newitemid" />
	     
	    </td>
	    <td>
	     Here you can set a custom Itemid for which you configured visibility of your modules. If you leave empty, your default virtuemart Itemid will be used. If you change this to a non-existant Itemid, you might see full-width checkout page. You might need to edit your SEF links if they override the Itemid as well. 
	    </td>
		</tr>

        <tr>
	    <td class="key">
	     <label for="op_articleid">Joomla article above basket</label>
	    </td>
	    <td>
		 <?php if (false) { ?>
	     <input type="text" id="op_articleid" value="<?php if (!empty($op_articleid)) echo $op_articleid; ?>" name="op_articleid" /> <?php } ?>
	     <?php echo $this->articleselector2; ?>
		 <input type="button" onclick="return clearArticle('op_articleid');" value="Set to none" />
	    </td>
	    <td>
	     Here you can set an article id which you would like to show above basket. Leave empty or 0 to disable this functionality. You can find the article ID at the right column of your article manager in Content - Article Manager. The article can be unpublished as well. 
	    </td>
		</tr>
		
	<tr>
	
	    <td class="key">
	     <label for="show_full_tos" >Terms of service configuration</label>
	    </td>
	    <td  colspan="2">
		 <input type="checkbox" value="1" name="full_tos_logged"  <?php if (!empty($full_tos_logged)) echo ' checked="checked" '; ?> /> Show full TOS for logged users<br style="clear: both;"/>
		 <input type="checkbox" value="1" name="full_tos_unlogged" <?php if (VmConfig::get('oncheckout_show_legal_info', '1')) echo ' checked="checked" readonly="readonly" '; else if (!empty($full_tos_unlogged)) echo ' checked="checked" '; ?> /> Show full TOS for unlogged users<br style="clear: both;" />
		 <input type="checkbox" value="1" name="tos_logged" <?php if (VmConfig::get('agree_to_tos_onorder', '1')) echo ' checked="checked" readonly="readonly" '; else if (!empty($tos_logged)) echo ' checked="checked" '; if (!isset($tos_unlogged)) echo ' checked="checked" '; ?>/> Show TOS checkbox for logged users<br style="clear: both;" />
		 <input type="checkbox" value="1" name="tos_unlogged" <?php if (VmConfig::get('agree_to_tos_onorder', '1')) echo ' checked="checked" readonly="readonly" '; else if (!empty($tos_unlogged)) echo ' checked="checked" '; if (!isset($tos_unlogged)) echo ' checked="checked" '; ?> /> Show TOS checkbox for unlogged users<br style="clear: both;" />
	     To be able to configure obligatory TOS per login type, please unconfigure [Must agree to Terms of Service on EVERY ORDER?] at your Virtuemart's <a href="index.php?option=com_virtuemart&view=config">configuration</a> at the tab "Checkout".<br style="clear: both;" />
		 To be able to change "show TOS for unlogged users", please unconfigure [Show Terms of Service on the cart/checkout?] at your Virtuemart's <a href="index.php?option=com_virtuemart&view=config">configuration</a> at the tab "Checkout".<br style="clear: both;" />
		 
		 <input type="checkbox" value="1" name="tos_scrollable" <?php if (!empty($tos_scrollable)) echo ' checked="checked" '; ?> /> TOS in a scrollable area. <br style="clear: both;" />
		 
	    </td>
	</tr>
	<tr>
	    <td class="key">
	     <label for="tos_config" >Terms of Service Article</label>
	    </td>
	    <td>
		<?php echo $this->articleselector; ?>
	     <?php
		 if (false) { ?><input class="text_area" type="text" name="tos_config" id="tos_config" size="10" value="<?php if (!empty($tos_config)) echo $tos_config; ?>"/>
		 <?php } ?>
		 <input type="button" onclick="return clearArticle('tos_config');" value="Set to shop's standard TOS" />
	    </td>
	    <td>Set this to default Virtuemart's TOS (when empty) or an article
	    </td>
	</tr>

	<tr>
	    <td class="key">
	     <label for="op_no_basket" >Don't show top basket</label>
	    </td>
	    <td>
	     <input class="op_no_basket" type="checkbox" name="op_no_basket" id="op_no_basket" <?php if (!empty($op_no_basket)) echo 'checked="checked"'; ?>/>
	    </td>
	    <td>Don't show basket at all. IMPORTANT: these must be <b>unchecked</b> for javascript to work properly: Shipping inside basket, Payment inside basket
	    </td>
	</tr>
	<tr>
	    <td class="key">
	     <label for="no_login_in_template" >Don't show login in the template</label>
	    </td>
	    <td>
	     <input class="no_login_in_template" type="checkbox" name="no_login_in_template" id="no_login_in_template" <?php if (!empty($no_login_in_template)) echo 'checked="checked"'; ?>/>
	    </td>
	    <td>Will hide login part from the template. In 'NO REGISTRATION' type it is hidden automatically.
	    </td>
	</tr>
	<tr>
	    <td class="key">
	     <label for="no_continue_link" >Don't show 'Continue shopping' in the template</label>
	    </td>
	    <td>
	     <input class="no_continue_link" type="checkbox" name="no_continue_link" id="no_continue_link" <?php if (!empty($no_continue_link)) echo 'checked="checked"'; ?>/>
	    </td>
	    <td>Will hide continue shopping from the template. If it is still there, please have a look into your template: shop.cart.tpl.php
	    </td>
	</tr>

        
        </table>
        </fieldset>
        <?php 
         echo $pane->endPanel();
		 




                    echo $pane->startPanel('Registration', 'panel8');
					
					?>
					<fieldset class="adminform">
		 <legend>Email Related Configuration</legend>
		 <table class="admintable" id="comeshere" style="width: 100%;">
		 	<tr>
	    <td class="key">
	     <label for="op_usernameisemail" >E-mail As Username</label>
	    </td>
	    <td  >
	     <input type="checkbox" name="op_usernameisemail" id="op_usernameisemail" value="op_usernameisemail" <?php if (isset($op_usernameisemail)) if ($op_usernameisemail==true) echo 'checked="checked"';?> />
	    </td>
	    <td>
	     Will hide the username fields and save the email as username. This setting only concerns the cart page where onepage is loaded. It will not alter other parts of Virtuemart or Joomla system. Your previous customers can still use their usernames created prior this feature was enabled. 
	    </td>
		</tr>

	    <tr>
		 <td class="key">
		  Email in Bill To
		</td>
		<td>
		  <input type="checkbox" name="opc_email_in_bt" value="1" <?php if (!empty($opc_email_in_bt)) echo ' checked="checked" '; ?> />
		</td>
		<td>
		 Move email from registration to Bill To address section. 
		</td>
		</tr>
	    <tr>
		 <td class="key">
		  Double Email
		</td>
		<td>
		  <input type="checkbox" name="double_email" value="1" <?php if (!empty($double_email)) echo ' checked="checked" '; ?>/> 
		</td>
		<td>
		Generate another email field for extra validation. (usually used when selling digital products)
		</td>
		</tr>
			<tr>
	    <td class="key">
	     <label for="unlog_all_shoppers">Log out shopper before and after purchase (from Joomla)</label>
	    </td>
	    <td>
	    <input type="checkbox" id="unlog_all_shoppers" name="unlog_all_shoppers" value="unlog_all_shoppers" <?php if ($unlog_all_shoppers==true) echo 'checked="checked"'; ?> /> 
	    </td>
		<td>
		In Joomla 2.5 there is a new type of registration (guest) where the user with an unactivated email address is logged in the system with lowered privileges. When this state is detected, OPC will make the email address read only and show the unlogged template. You can enable this option if you don't need your users to be logged in and you want them to fill the address all the time. 
		</td>
	</tr>
	<tr>
	    <td class="key">
	     <label for="op_no_display_name" >Hide "Display name" field</label>
	    </td>
	    <td>
	     <input class="op_no_display_name" type="checkbox" name="op_no_display_name" id="op_no_display_name" <?php if (!empty($op_no_display_name)) echo 'checked="checked"'; ?>/>
	    </td>
	    <td>Will hide Display name field for the address
	    </td>
	</tr>
	<tr>
	    <td class="key">
	     <label for="op_create_account_unchecked" >Create account status</label>
	    </td>
	    <td>
	     <input class="op_create_account_unchecked" type="checkbox" name="op_create_account_unchecked" id="op_create_account_unchecked" <?php if (!empty($op_create_account_unchecked)) echo 'checked="checked"'; ?>/>
	    </td>
	    <td>When this is checked, the checkbox for create account will not be checked by default and username, password1, password2 will be hidden. This only applies to OPTIONAL REGISTRATION
	    </td>
	</tr>        
	<tr>
	    <td class="key" >
	     <label for="allow_duplicit" >Enable duplicit emails in any type of registration</label>
	    </td>
	    <td>
	    <input type="checkbox" id="allow_duplicit" name="allow_duplicit" value="allow_duplicit" <?php if ($allow_duplicit==true) echo 'checked="checked"'; ?> /> 
	    </td>
	    <td>
	    If you enable this, users can make purchases with the same email address more then once as unlogged. His order details will be associated with previous purchases (if virtuemart enable registration is enabled) and but he will not be registered in joomla again. When an already existing email is dected the user is not logged in unless he enters the same password into the registration as he did before. If the passwords don't match he will never gets logged and must ask for a new password if he wants to see his order history or invoices. 
	    </td>
	</tr>
		</table>
		</fieldset>
					<fieldset class="adminform">
					<legend>VirtueMart Configuration</legend>
		Tax configuration in VM2 is now fully configured with your calculation plugins <a href="index.php?option=com_virtuemart&view=calc">here</a><br />
					<?php
jimport( 'joomla.html.html.behavior' );
JHtml::_('behavior.modal', 'a.modal'); 
					
		 if (!defined('VM_REGISTRATION_TYPE'))
   {
    if (VmConfig::get('oncheckout_only_registered', 0))
	{
	  if (VmConfig::get('oncheckout_show_register', 0))
	  define('VM_REGISTRATION_TYPE', 'NORMAL_REGISTRATION'); 
	  else 
	  define('VM_REGISTRATION_TYPE', 'SILENT_REGISTRATION'); 
	}
	else
	{
	if (VmConfig::get('oncheckout_show_register', 0))
    define('VM_REGISTRATION_TYPE', 'OPTIONAL_REGISTRATION'); 
	else 
	define('VM_REGISTRATION_TYPE', 'NO_REGISTRATION'); 
	}
   }
					?>
<a class="modal" href="index.php?option=com_config&amp;view=component&amp;component=com_users&amp;path=&amp;tmpl=component" rel="{handler: 'iframe', size: {x: 875, y: 550}, onClose: function() {}}">
You can enable or disable activation here.  
</a>
<br />
<p>The guest checkout and other registration options can be set up by altering <a href="index.php?option=com_virtuemart&view=config" taget="_blank">virtuemart's checkout configuration at tab Checkout</a> as following: <br />
- (A) On checkout, ask for registration<br />
- (B) Only registered users can checkout<br />
</p>
<?php
 $usersConfig = JComponentHelper::getParams( 'com_users' );
 $reg = $usersConfig->get('allowUserRegistration'); 
 if (empty($reg) && (VM_REGISTRATION_TYPE != 'NO_REGISTRATION'))
 {
   echo '<p style="color: red;">POSSIBLE MISCONFIGURATION: Your Joomla\'s registration is disabled, but you require a logged in users for your checkout. Solution: Enable Joomla Registration or Configure Virtuemart not to require registration. If you handle user registration for the backend manually or with a 3rd party plugin and you don\'t need checkout to work for unlogged, this might be ignored.</p>'; 
 }
?>
<?php if (VM_REGISTRATION_TYPE == 'OPTIONAL_REGISTRATION') echo '<span style="color: green; font-weight: bold; font-size: 14px;">'; ?>
1. When A is checked and B is not checked this is equal to Optional registration (guest or create an account) EMAIL, USERNAME, REGISTRATION CHECKBOX, PWD1, PWD2<br />
<?php if (VM_REGISTRATION_TYPE == 'OPTIONAL_REGISTRATION') echo '</span>'; ?>
<?php if (VM_REGISTRATION_TYPE == 'SILENT_REGISTRATION') echo '<span style="color: green; font-weight: bold; font-size: 14px;">'; ?>
2. When A is not checked and B is checked this is equal to Silent registration (account is created automatically) EMAIL, USERNAME<br />
<?php if (VM_REGISTRATION_TYPE == 'SILENT_REGISTRATION') echo '</span>'; ?>
<?php if (VM_REGISTRATION_TYPE == 'NO_REGISTRATION') echo '<span style="color: green; font-weight: bold;font-size: 14px;">'; ?>
3. When A is not checked and B is not checked this is equal to No registration (no account in joomla or vm is created) EMAIL<br />
<?php if (VM_REGISTRATION_TYPE == 'NO_REGISTRATION') echo '</span>'; ?>
<?php if (VM_REGISTRATION_TYPE == 'NORMAL_REGISTRATION') echo '<span style="color: green; font-weight: bold;font-size: 14px;">'; ?>
4. When A is checked and B is checked this is equal to Normal registration EMAIL, USERNAME, PWD1, PWD2<br />
<?php if (VM_REGISTRATION_TYPE == 'NORMAL_REGISTRATION') echo '</span>'; ?>
<br />
<br />
Note: if duplicite email is detected then the user is not logged in, in all cases. If the feature is disabled the customer gets an error and must log in before confirming an order. WHEN EMAIL AS USERNAME IS USED, THEN USERNAME CAN GET HIDDEN AND FOR THE CHECKOUT PURPOSE EMAIL WILL BE USED AS USERNAME IN ALL LOGIN FORMS.YOU MIGHT NEED TO CHANGE YOUR LANGUAGE STRINGS FOR YOUR DEFAULT LOGIN FORMS. IT DOES NOT INFLUENCE STANDARD VM REGISTRATION OR JOOMLA REGISTRATION, IT ONLY SAVES THE EMAIL AS USERNAME.
<br />
These options are ignored or overrided: <br />
Enable Automatic Selected Shipment? (overriden in almost all processes, We recommend to disable this option if your shipping method does an API call)<br />
Enable Automatic Selected Payment? (overriden)<br />
Show product images (ignored - depends on selected template)<br />
<br />
Must agree to Terms of Service on EVERY ORDER? (this option will make the TOS agree checkbox obligatory)<br />
Show Terms of Service on the cart/checkout? (will show the full terms of service at the checkout for both logged and unlogged templates)<br />

<br />
User can be logged in Joomla when entering checkout but not logged in Virtuemart which will lead to email field to be read-only and registration will look like silent (no password and username will be shown)<br />

</fieldset>
<?php
					echo $pane->endPanel();
					?>
   
		<?php
		echo $pane->startPanel('Shopper Fields', 'panel8'); ?>
		<fieldset class="adminform">
		 <?php
		   if (empty($this->ulist)) echo 'This feature is available only for Virtuemart 2.0.8 and larger'; 
		   else
		   {
		   ?>
		  <p><h3>Business View Configuration</h3>In this section you can configure your shopper fields to be shown within a Vistor or Business section if your OPC theme supports it. If you'd like to show <strong>"Company Name"</strong> only for business view, please check the checkbox below under Business field only column. This feature is usefull also for shop owners selling digital products which don't require all invoicing data from non business customers.</p><h3>Custom rendering</h3><p>If your theme renders some of the input parameters on it's own, but you still want it to be saved to the database on a sucessfull order, you can check OPC rendering. Please don't use it unless you've added the custom rendering code (html) into OPC theme. This feature can be used for checkboxes outside userfields or any other extra information which you'd like to render outside user fields area.</p>
		  <table class="admintable" style="width: 50%;">
		  <tr>
		   <th>Field Name
		   </th>
		   <th>Business field only</th>
		   <th>Custom OPC rendering</th>
		   <th>Shipping field obligatory</th>
		  </tr>
		  

		   <?php
		   //var_dump($this->ulist); die(); 
		   foreach ($this->ulist as $key=>$row)
		    {
			 if (!$row->published) continue; 
			 
			 // the next line will filter core fields
			 //if (in_array($row->name, $this->clist)) continue;
			  //if ($row->published)

			   ?>
			   		<tr>
	    <td class="key">
	     <label><?php 
		 $title = $row->title; 
		 $title2 = JText::_($row->title); 
		 
		 if ($title2 != $title) echo $title2.' ('.JText::_($row->name).')'; 
		 else echo $row->name; 
		 
		 ?></label> 
	    </td>
	    <td><input type="checkbox" name="business_fields[]" value="<?php echo $row->name;
		if (!empty($business_fields))
		if (in_array($row->name, $business_fields)) echo '" checked="checked'; 
		
		?>" />
		</td>

	    <td><input type="checkbox" name="custom_rendering_fields[]" value="<?php echo $row->name; 
		switch ($row->name)
		{
		  case 'email':
		  case 'email2':
		  case 'username':
		  case 'agreed':
		  case 'name': 
		  case 'password':
		  case 'password2':
		  case 'register_account':
		    echo '" disabled="disabled" checked="checked" readyonly="readonly'; 
		
		
		}
		
		if (!empty($custom_rendering_fields))
		if (in_array($row->name, $custom_rendering_fields)) echo '" checked="checked'; 
		
		?>" />
		</td>
		 <td><input type="checkbox" name="shipping_obligatory_fields[]" value="<?php echo $row->name; 
		switch ($row->name)
		{
		  case 'email':
		  case 'email2':
		  case 'username':
		  case 'agreed':
		  case 'name': 
		  case 'password':
		  case 'password2':
		  case 'register_account':
		    echo '" disabled="disabled" checked="checked" readyonly="readonly'; 
			
		
		}
		if ($row->shipment != '1')
		    echo '" disabled="disabled" readyonly="readonly'; 
			
		if (!empty($shipping_obligatory_fields))
		if (in_array($row->name, $shipping_obligatory_fields)) echo '" checked="checked'; 
		
		?>" />
		</td>

		
		</tr>
		<?php
			}
			echo '</table>'; 
		   }
		 ?>
		</fieldset>
		<?php echo $pane->endPanel();
		
                    echo $pane->startPanel('Taxes and Prices', 'panel8');
?>
		<fieldset class="adminform">
		Tax configuration in VM2 is now fully configured with your calculation plugins <a href="index.php?option=com_virtuemart&view=calc">here</a><br />
		We've created a small guide to help you set up your site with your required tax settings which is available at <a href="http://www.rupostel.com/one-page-checkout-component/features/ready-for-global-use-recommended-settings">rupostel.com</a>.
		 <table class="admintable" id="comeshere" style="width: 100%;">
	    <tr>
	    <td class="key">
	     <label for="american">American settings</label> 
	    </td>
	    <td>
			<input type="checkbox" value="1" name="opc_usmode" <?php if (!empty($opc_usmode)) echo ' checked="checked" '; ?> /> Never count taxes for vendor's state IN WHOLE ESHOP untill the same state is selected by a customer. You may want to use the base price below without tax for the checkout view. 
	    </td>
		</tr>

		 <tr>
	    <td class="key">
	     <label for="adwords_enabled_0">Product unit price at checkout</label> 
	    </td>
	    <td>
	    <select name="product_price_display">
		 <option <?php if (!empty($product_price_display) && ($product_price_display == 'basePriceWithTax')) echo ' selected="selected" '; ?>value="basePriceWithTax">basePriceWithTax</option>
		 <option <?php if (!empty($product_price_display) && ($product_price_display == 'basePrice')) echo ' selected="selected" '; ?>value="basePrice">basePrice</option>
		 <option <?php if (empty($product_price_display) || (!empty($product_price_display) && ($product_price_display == 'salesPrice'))) echo ' selected="selected" '; ?>value="salesPrice">salesPrice (default)</option>
		</select>
	    </td>
		</tr>
	    <tr>
	    <td class="key">
	     <label for="id_subtotal_display">Subtotal at the checkout</label> 
	    </td>
	    <td>
	    <select name="subtotal_price_display" id="id_subtotal_display">
		 <option <?php if (!empty($subtotal_price_display) && ($subtotal_price_display == 'basePriceWithTax')) echo ' selected="selected" '; ?>value="basePriceWithTax">basePriceWithTax</option>
		 <option <?php if (!empty($subtotal_price_display) && ($subtotal_price_display == 'basePrice')) echo ' selected="selected" '; ?>value="basePrice">basePrice</option>
		 <option <?php if (!empty($subtotal_price_display) && ($subtotal_price_display == 'billSub')) echo ' selected="selected" '; ?>value="billSub">billSub</option>
		 <option <?php if (empty($subtotal_price_display) || (!empty($subtotal_price_display) && ($subtotal_price_display == 'salesPrice'))) echo ' selected="selected" '; ?>value="salesPrice">salesPrice (default)</option>
		</select>
	    </td>
		</tr>
	    <tr>
	    <td class="key">
	     <label for="id_coupon_display">Coupon price at the checkout</label> 
	    </td>
	    <td>
	    <select name="coupon_price_display" id="id_coupon_display">
		 <option <?php if ((!empty($coupon_price_display) && ($coupon_price_display == 'billDiscountAmount'))) echo ' selected="selected" '; ?>value="billDiscountAmount">billDiscountAmount</option>
		 <option <?php if (!empty($coupon_price_display) && ($coupon_price_display == 'discountAmount')) echo ' selected="selected" '; ?>value="discountAmount">discountAmount</option>
		 <option <?php if  (empty($coupon_price_display) || (!empty($coupon_price_display) && ($coupon_price_display == 'salesPriceCoupon'))) echo ' selected="selected" '; ?>value="salesPriceCoupon">salesPriceCoupon (default for EU)</option>
		</select>
	    </td>
		</tr>

	  </table>
		</fieldset> 
		
		<?php
         echo $pane->endPanel();
        echo $pane->startPanel('Emails', 'panel1');
		?>
		<fieldset class="adminform">
        <legend>Email configuration for Order Confirm Email</legend>
         Order confirmation emails are now sent by payment plugins in VirtueMart 2. We would like to add more flexibility to these settings and we will welcome any idea from you. 
        </fieldset>
        <?php 
         echo $pane->endPanel();
		//if (false)
		{
        echo $pane->startPanel('Language Editor', 'panel4'); ?>
        
        <fieldset class="adminform" style="width: 100%;">
        <legend>You can use our language editor for translation of any of your components or modules.</legend>
		<div id="opc_language_editor">
		  <table class="admintable" id="comeshere" style="width: 100%;">
	    <tr>
	    <td class="key">
	     <label for="tr_ext_id">Translate this extension</label> 
	    </td>
		<td>
		  <select name="tr_ext" id="tr_ext_id">
		  <?php
		    foreach($this->exts as $key=>$xt)
			 {
			   echo '<option value="'.$key.'"';
			   if (strpos($key, 'com_onepage.ini')!==false) echo ' selected="selected" '; 
			   echo '>'.$key.'</option>'; 
			 }
		  ?>
		  </select>
		 </td>
		 </tr>
		 <tr>
		 <td class="key">
	     <label for="tr_fromlang_id">Translate from this language (if available)</label> 
	     </td>
		 <td>
		  <select name="tr_fromlang" id="tr_fromlang_id">
		  <?php
		    foreach($this->extlangs as $key=>$xt)
			 {
			   echo '<option value="'.$key.'"';
			   if (strpos($key, 'en-GB')!==false) echo ' selected="selected" '; 
			   echo '>'.$key.'</option>'; 
			 }
		  ?>
		  </select>
		 </td>
		 </tr>
		 
		 <tr>
		 <td class="key">
	     <label for="tr_tolang_id">Translate to this language (will create ini file and directory structure if needed)</label> 
	     </td>
		 <td>
		  <input type="hidden" name="tr_type" value="site" />
		  <select name="tr_tolang" id="tr_tolang_id">
		  <?php
		  
			$config =& JFactory::getConfig();
			$flang = $config->getValue('config.language');
			
			
		    foreach($this->extlangs as $key=>$xt)
			 {
			   echo '<option value="'.$key.'"';
			   if (stripos($key, $flang )!==false) echo ' selected="selected" '; 
			   echo '>'.$key.'</option>'; 
			 }
		  ?>
		  </select>
		</td>
		</tr>
		<tr>
		 <td>
		   <input type="button" value="Proceed to Language Editor" onclick="javascript: submitbutton('langedit');" />
		 </td>
		</tr>
		<?php if (!empty($this->langerr))
		{
		 ?>
		  <tr>
		  <td>
		  Do you want OPC to create copy of these tables?<br />
		  <?php
		  foreach ($this->langerr as $mi)
		   {
		     $orig = str_replace(VMLANG, 'en_gb', $mi); 
		     echo 'Copy table <b>'.$orig.'</b> to <b>'.$mi.'</b><br />'; 
		   }
		  ?>
		  </td>
		  </tr>
		  <tr>
		 <td>
		   <input type="button" value="YES, make the copy of the tables above" onclick="javascript: submitbutton('langcopy');" />
		 </td>
		</tr>
		 <?php
		}
		?>
		</table>
		  
		  
		  
		</div>
    
	
	</fieldset>
        <?php 
        echo $pane->endPanel();
		}
        echo $pane->startPanel('Tracking', 'paneladw');
?>
		<fieldset class="adminform">
        <legend>Your Google adwords (or other comapaigns) tracking code</legend>
        <p>When a customer clicks on 'Submit Order' button this script will be loaded in an iframe on the checkout page before redirection.</p>
        <table class="admintable" id="comeshere" style="width: 100%;">
	    <tr>
	    <td class="key">
	     <label for="adwords_enabled_0">Enable this feature</label> 
	    </td>
	    <td>
	    <input id="adwords_enabled_0" type="checkbox" name="adwords_enabled_0" <?php if (!empty($adwords_enabled[0])) echo 'checked="checked" '; ?>/>
	    </td>
		</tr>
	    <tr>
	    <td class="key">
	     <label for="adwords_name_0">IFrame Time-Out</label> 
	    </td>
	    <td>
	    <input id="adwords_name_0" type="text" name="adwords_timeout" value="<?php if (!empty($adwords_timeout)) echo (int)$adwords_timeout; else echo "4000"; ?>"/>
	    </td><td>Please enter time-out in ms for which the script can wait untill the tracking code is sent to the provider. Make sure the provider does not use asynchronious javascript as it might not work as expected. It is recommended to use an image tag for conversion tracking as it is much more reliable.</td>
		</tr>

	    <tr>
	    <td class="key">
	     <label for="adwords_amount_0">Pre-selected order total</label> 
	    </td>
	    <td>
	    <input id="adwords_amount_0" type="text" name="adwords_amount_0" value="<?php if (!empty($adwords_amount[0])) echo $adwords_amount[0]; ?>"/>
	    </td><td>Please insert the amount which you inserted at google adwords page or other conversion measuring site. This amout will be searched and replaced with current order total. 
	    </td>
		</tr>
		
	    <tr>
	    <td class="key">
	     <label for="adwords_code_0">Code</label> 
	    </td>
	    <td  >
	    <textarea id="adwords_code_0" name="adwords_code_0" cols="60" rows="20"><?php 
		
		if (file_exists(JPATH_ROOT.DS."components".DS."com_onepage".DS."trackers".DS."body.html"))
        $adwords_code[0] = @file_get_contents(JPATH_ROOT.DS."components".DS."com_onepage".DS."trackers".DS."body.html");
		else $adwords_code[0] = ''; 
		if (!empty($adwords_code[0])) echo $adwords_code[0];?></textarea> 
	    </td><td>Please insert code which will be loaded on Order Submit button click. The code should be XHTML valid, otherwise your checkout can get broken.<br />
	    <?php
	    	if (!empty($use_ssl))
				$op_securl = JURI::base(true).basename($_SERVER['PHP_SELF']);
			else
				$op_securl = JURI::base(true).basename($_SERVER['PHP_SELF']);
			
			$op_securl = str_replace('/administrator', '/', $op_securl); 
		?>
	        The iframe in which it will be loaded can be checked here: <a href="<?php echo $op_securl; ?>?option=com_virtuemart&nosef=1&task=tracker&view=opc&format=opchtml&tmpl=component&controller=opc&amount=99.99"><?php echo $op_securl; ?>?option=com_virtuemart&nosef=1&task=tracker&view=opc&format=opchtml&tmpl=component&controller=opc&amount=99.99</a> 
            Amount will be changed according to the real order total. If your tracking code requires a keyword in the URL you may use 'conversion' there. <br />
            Try to disable SEF redirects for all "view=opc&amp;option=com_virtuemart" calls to speed up ajax calls ang js generation. <br />
            You can insert as many external codes here as you want as far as they load in selected timeout after clicking submit order.</td>
		</tr>
		<tr>
		<td colspan="3">If you require 100% accurancy on your tracking, you can use non-javascript calls to your affiliates or campaign providers. The URL code below is loaded after the order is saved and before the payment is processed. <?php
		if (!function_exists('curl_multi_exec'))
		 {
		   echo '<span style="color: red;">curl_multi_exec is not found, contact your web hosting provider. The below feature will not work for you.</span>'; 
		 }
		?>
		<br />
		Example of Curl URL: http://www.heureka.sk/direct/dotaznik/objednavka.php?id=1234&email={email}<br />
		You can use {email} for customer's email, {first_name} for his first name, {last_name} for his last name, {order_id} for your order
		
		</td>
		</tr>
		
		<?php 
		if (empty($curl_url)) { $curl_url = array(); $curl_url[0] = ''; }
		foreach ($curl_url as $i=>$val)
		{
		?>
		<tr>
		  <td class="key">CURL this URL
		  </td>
		  <td colspan="2">
		   <input style="width: 80%;" type="text" value="<?php 
		   if (!empty($curl_url[$i]))
		   echo base64_decode($curl_url[$i]);  ?>" name="curl_url_<?php echo $i; ?>" />
		   <?php if ($i == (count($curl_url)-1)) { ?>
		   <a href="#" onclick="return add_curl();" >Add more...</a><br />
		   <div id="add_here">&nbsp;</div>
		   
		   <script type="text/javascript">
		    var next = <?php echo count($curl_url); ?>;
		    var toAdd0 = '<input style="width: 80%;" type="text" value="" name="curl_url_';
			var toAdd1 = '" /><br />'; 
			function add_curl()
			 {
			   ah = document.getElementById('add_here'); 
			   if (ah != null)
			    {
				  ah.innerHTML += toAdd0+next+toAdd1;
				  next++; 
				}
				return false; 
			 }
		   </script>
		   
		   <?php } ?>
		  </td>
		</tr>
		<?php
		}
		?>
		<tr>
		</tr>
		</table>
		</fieldset>
<?php
		echo $pane->endPanel();     
		if (false)
		{
        echo $pane->startPanel('Export', 'exportpane'); 
         $ehelper = new OnepageTemplateHelper;
		 $files = $ehelper->getExportTemplates('ALL');
		 if (!empty($files))
		 {
		  $pane2 =& JPane::getInstance('sliders', array('allowAllClose'=>true));
		  echo $pane2->startPane('tabse333');
		 }
       
        ?>
        
		<fieldset class="adminform">
		<legend>EXPERIMENTAL! These templates can be used for order export capabilities</legend>
		<p>You can set up templates for shipping export, order data export, postal cheques and much more. This service might become unavailable or paid later on. The conversion from Office files to pdf is done on RuposTel's virtual servers. Your data and data of your customers are not being stored anyhow and all communication toward our server is done on secured lines over HTTPS.</p>
		 <?php
		 $q = "show columns from #__onepage_exported where field = 'status'";
		 $db =& JFactory::getDBO();
		 $db->setQuery($q); 
		 $x = $db->loadAssocList(); 
		 if (!empty($x))
		 {
		   if (stripos($x[0]['Type'], 'enum') !== false)
		   {
		     $db = JFactory::getDBO();
			 $db->setQuery("ALTER TABLE  `#__onepage_exported` CHANGE  `status`  `status` VARCHAR( 20 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  'NONE'");
			 $db->query(); 
		   }
		 }
		 //var_dump($x); die();
		
		 foreach($files as $f)
		 {
		 $name = '';
		 if (!empty($f['tid_name'])) $name = $f['tid_name'].' - '.$f['file'];  
		 else $name = $f['file'];
		 
		 echo $pane2->startPanel($name, 'p2anelexp'.$f['tid']);
		 
		 ?>
		  <fieldset><legend><?php if (!empty($f['tid_name'])) echo $f['tid_name'].' - ';  echo $f['file'] ?></legend>
		  <?php if (!isset($f['tid_name'])) $f['tid_name'] = $f['file']; ?>
		  Template name:<input type="text" size="20" <?php if (isset($f['tid_name'])) 
		  { echo ' value="'.$f['tid_name'].'"'; } else echo ' value=""'; ?> name="tid_name_<?php echo $f['tid'] ?>" id="tid_name_<?php echo $f['tid'] ?>" />&nbsp;<a href="<?php echo $ehelper->getTemplateLink($f['tid']); ?>">Download</a>
   		  Update template: <input name="uploadedupdatefile_<?php echo $f['tid']; ?>" type="file" />&nbsp;&nbsp;&nbsp;
		  <input type="button" value="Upload File" onclick="javascript: submitbutton('template_update_upload');" />
		  <br />
		  <input type="checkbox" <?php if (isset($f['tid_enabled']) && $f['tid_enabled']=='1') echo 'checked="checked" '; ?> name="tid_enabled_<?php echo $f['tid'] ?>" id="tid_enabled_<?php echo $f['tid'] ?>"  />Enabled <br />
		  <input type="checkbox" <?php if (isset($f['tid_special']) && $f['tid_special']=='1') echo 'checked="checked" '; ?> name="tid_special_<?php echo $f['tid'] ?>" id="tid_name_<?php echo $f['tid'] ?>" />Has manual entry&nbsp;&nbsp;&nbsp;&nbsp;How many: <input type="text" name="tid_specials_<?php echo $f['tid'] ?>" <?php if (isset($f['tid_specials'])) 
		  { echo ' value="'.$f['tid_specials'].'"'; } else echo ' value="1"'; ?> /><br />
		  <input type="checkbox" <?php if (isset($f['tid_ai']) && $f['tid_ai']=='1') echo 'checked="checked" '; ?> name="tid_ai_<?php echo $f['tid'] ?>" id="tid_ai_<?php echo $f['tid'] ?>"  />Autoincrement the first manual entry? (manual entries must be set to at least 1)<br />
		  Has shared entry: 
		  <?php echo '<select name="tid_shared_'.$f['tid'].'">';
		   echo '<option value="" ';
		   if (empty($f['tid_shared'])) echo ' selected="selected" ';
		   echo '>NOT CONFIGURED</option>';
		   foreach ($files as $ff)
		   {
		    if ($ff['tid']!=$f['tid'])
		    echo '<option value="'.$ff['tid'].'" ';
		    if ($f['tid_shared'] == $ff['tid']) echo ' selected="selected" ';
		    echo '>'.$ff['tid_name'].'</option>';
		   }
		   echo '</select>';
		   ?> (manual entries must be set to at least 1, AI checked) If this is a variation of other template, it can share the same AI variable. <br />
		  <input type="checkbox" <?php if (count($files)==1) echo ' disabled="disabled" '; if (isset($f['tid_foreign']) && $f['tid_foreign']=='1') echo 'checked="checked" '; ?> name="tid_foreign_<?php echo $f['tid'] ?>" id="tid_foreign_<?php echo $f['tid'] ?>"  />Has foreign entry? Will create dependance on the other template and will use its manual entry. 
		  <?php if (count($files)>1) 
		  { 
		  ?>Choose foreign template: <?php
		   
		   echo '<select name="tid_foreigntemplate_'.$f['tid'].'">';
		   foreach ($files as $ff)
		   {
		    if ($ff['tid']!=$f['tid'])
		    echo '<option value="'.$ff['tid'].'" ';
		    if ($f['tid_foreigntemplate'] == $ff['tid']) echo ' selected="selected" ';
		    echo '>'.$ff['tid_name'].'</option>';
		   }
		   echo '</select>';
		   } 
		   ?><br />
		  <input type="checkbox" <?php if (isset($f['tid_email']) && $f['tid_email']=='1') echo 'checked="checked" '; ?> name="tid_email_<?php echo $f['tid'] ?>" id="tid_email_<?php echo $f['tid'] ?>"  />Can be sent to customers by email<br />
		  <input type="checkbox" <?php if (isset($f['tid_autocreate']) && $f['tid_autocreate']=='1') echo 'checked="checked" '; ?> name="tid_autocreate_<?php echo $f['tid'] ?>" id="tid_autocreate_<?php echo $f['tid'] ?>"  />Autocreate when order changes status to this status. (does not work with manual entries except auto increment entry)
		  <select name="tid_autocreatestatus_<?php echo $f['tid'] ?>">
		<?php
		  if (!empty($this->statuses))
		  foreach ($this->statuses as $s)
		  {
		    if ($s['order_status_code'] == $f['tid_autocreatestatus']) $ch = ' selected="selected" ';
		    else $ch = '';
		    echo '<option value="'.$s['order_status_code'].'" '.$ch.'>'.$s['order_status_name'].'</option>';
		  }
		  ?>
		  </select> (HELP: 'Pending' will create it for all new orders and 'Confirmed' only for paid orders.)
		  <br />
		  <input type="checkbox" <?php if (isset($f['tid_num']) && $f['tid_num']=='1') echo 'checked="checked" '; ?> name="tid_num_<?php echo $f['tid'] ?>" id="tid_num_<?php echo $f['tid'] ?>"  />Add zeroes to numbers and and use them with {account_muber_back_0} where 0 is the last digit   <br />
		  <input type="text" value="<?php if (!empty($f['tid_nummax'])) echo $f['tid_nummax']; ?>" size="10" name="tid_nummax_<?php echo $f['tid'] ?>" id="tid_nummax_<?php echo $f['tid'] ?>"  />Maximum total digits for adding zeroes <br />
		  <input type="text" value="<?php if (!empty($f['tid_itemmax'])) echo $f['tid_itemmax']; ?>" size="10" name="tid_itemmax_<?php echo $f['tid'] ?>" id="tid_itemmax_<?php echo $f['tid'] ?>" />Maximum number of items (order items or orders). <br />
		  <input type="checkbox" <?php if (isset($f['tid_back']) && $f['tid_back']=='1') echo 'checked="checked" '; ?> name="tid_back_<?php echo $f['tid'] ?>" id="tid_back_<?php echo $f['tid'] ?>"  />Create character arrays for strings accessible from 0 index of last character in field_name_back_0 <br />
		  <input type="checkbox" <?php if (isset($f['tid_forward']) && $f['tid_forward']=='1') echo 'checked="checked" '; ?> name="tid_forward_<?php echo $f['tid'] ?>" id="tid_forward_<?php echo $f['tid'] ?>"  />Create character arrays for strings accessible from 0 index of the first character in field_name_forward_0 <br />
		  
		  Type: <select name="tid_type_<?php echo $f['tid']; ?>">
		  <option <?php if (isset($f['tid_type']) && ($f['tid_type']=='ORDER_DATA')) echo ' selected="selected" '; ?> value="ORDER_DATA">Single Order - Office Conversion</option>
		  <option <?php if (isset($f['tid_type']) && ($f['tid_type']=='ORDER_DATA_TXT')) echo ' selected="selected" '; ?>value="ORDER_DATA_TXT">Single Order - Local Text Conversion [NOT IMPLEMENTED]</option>
		  <option <?php if (isset($f['tid_type']) && ($f['tid_type']=='ORDERS')) echo ' selected="selected" '; ?>value="ORDERS">Multiple Orders - Office Conversion</option>
		  <option <?php if (isset($f['tid_type']) && ($f['tid_type']=='ORDERS_TXT')) echo ' selected="selected" '; ?>value="ORDERS_TXT">Multiple Orders - Local Text Conversion [NOT IMPLEMENTED]</option>
		  </select><br />
		  <b>E-Mail Configuration</b><br />
		  E-Mail Subject: <input type="text" value="<?php if (!empty($f['tid_emailsubject'])) echo $f['tid_emailsubject']; ?>" size="100" name="tid_emailsubject_<?php echo $f['tid'] ?>" id="tid_emailsubject_<?php echo $f['tid'] ?>" /><br />
		  E-Mail Body: <textarea cols="100" rows="7" name="tid_emailbody_<?php echo $f['tid'] ?>" id="tid_emailbody_<?php echo $f['tid'] ?>"><?php
	 	   if (!empty($f['tid_emailbody'])) echo $f['tid_emailbody']; ?></textarea>
		  </fieldset>
		  <?php
		  
		  echo $pane2->endPanel();
		 }
		 
		 if (!empty($files))
		 {
		  echo $pane2->startPanel('General', 'generale');
		 }
		echo 'Choose a file to upload: <input name="uploadedfile" type="file" /><br />';
		echo '<input type="button" value="Upload File" onclick="javascript: submitbutton('."'template_upload'".');" />';
		if (!empty($files)) {
		echo '<br />
		 <a href="?option=com_onepage&view=config&showvars=1" target="_blank" title="Show template variables">Show template variables (SAVE CONFIG FIRST!)</a>';
		 $showvars = JRequest::getVar('showvars', '');
		 if (!empty($showvars))
		 {
		  echo 'Available template variables: <br /><textarea cols="40" rows="5">';
		 
 		 $x = @ob_get_clean(); $x = @ob_get_clean(); $x = @ob_get_clean(); $x = @ob_get_clean(); $x = @ob_get_clean(); $x = @ob_get_clean(); 
		 echo '
		 <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-gb" lang="en-gb" dir="ltr" id="minwidth" >
		<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		</head>
		<body>';
		
		 
		 $x = end($f);
		    $data = $ehelper->getOrderDataEx($x['tid'], ''); 
		    foreach ($data as $k=>$v)
		    {
		     if (!empty($v) || ($v === '0'))
		     echo '{'.$k."}".$v."<br />\n";
		    }
		   echo '</body></html>';
		    die();
		   
		   echo '</textarea>';
		 }
		 } 
		if (!empty($files))
		 {
		  echo $pane2->endPanel();
		 }
		 echo '</fieldset>';
		 if (!empty($files)) {
		  echo $pane2->endPane();
		 }
		echo $pane->endPanel();
        
        echo $pane->startPanel('Installation &amp; Upgrade', 'panel5');
        
	?>


        <fieldset class="adminform">
        <legend>This part will check your Installation status.</legend>
	<p>
        YOUR TEMPLATE FILES WILL BE OVERWRITTEN ! PLEASE INSTALL THEM TO YOUR OWN TEMPLATE DIRECTORY IF YOU DON'T WANT TO OVERWRITE THEM AUTOMATICALLY ! <br />
	If you installed a new version with Joomla Installer, please use this step to modify your VirtueMart files of OnePage Checkout component. You can choose to overwrite OnePage Checkout template files by checking the box below. Shop.cart method is recommended (will overwrite your original basket.php). If you want to manually copy component files to your VirtueMart installation, you can find them in /administrator/components/com_onepage/vm_files and basket.php which will be used is located in /components/com_onepage/templates/basket.php You may move templates files from default directory to your real VM template and your template files will never get overwritten.
	</p>
        File installation check: <br />
        <table>
        <?php 
        $dir = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart';
        $dir2 = JPATH_SITE.DS.'components'.DS.'com_virtuemart';
	?><tr><td><?php
        echo $dir.DS.'classes'.DS.'ps_onepage.php'.'<br />';
        ?></td><td><?php echo checkFile($dir.DS.'classes'.DS.'ps_onepage.php'); ?></td></tr><tr><td><?php
        echo $dir.DS.'classes'.DS.'onepage'.DS.'index.php'.'<br />';
        ?></td><td><?php echo checkFile($dir.DS.'classes'.DS.'onepage'.DS.'index.php'); ?></td></tr><tr><td><?php
        echo $dir.DS.'classes'.DS.'onepage'.DS.'onepage.cfg.php'.'<br />';
        ?></td><td><?php echo checkFile($dir.DS.'classes'.DS.'onepage'.DS.'onepage.cfg.php'); echo ' When you click save this file will be newly generated.'; ?></td></tr><tr><td><?php
        echo $dir.DS.'html'.DS.'checkout.onepage.php'.'<br />';
        ?></td><td><?php echo checkFile($dir.DS.'html'.DS.'checkout.onepage.php'); ?></td></tr><tr><td><?php
        echo $dir2.DS.'themes'.DS.'default'.DS.'templates'.DS.'onepage'.DS.'onepage.css'.'<br />';
        ?></td><td><?php echo checkFile($dir2.DS.'themes'.DS.'default'.DS.'templates'.DS.'onepage'.DS.'onepage.css'); ?></td></tr><tr><td><?php
        echo $dir2.DS.'themes'.DS.'default'.DS.'templates'.DS.'onepage'.DS.'onepage.logged.tpl.php'.'<br />';
        ?></td><td><?php echo checkFile($dir2.DS.'themes'.DS.'default'.DS.'templates'.DS.'onepage'.DS.'onepage.logged.tpl.php'); ?></td></tr><tr><td><?php
        echo $dir2.DS.'themes'.DS.'default'.DS.'templates'.DS.'onepage'.DS.'onepage.unlogged.tpl.php'.'<br />';
        ?></td><td><?php echo checkFile($dir2.DS.'themes'.DS.'default'.DS.'templates'.DS.'onepage'.DS.'onepage.unlogged.tpl.php'); ?></td></tr>
		<tr><td><?php echo JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'html'.DS.'basket.php<br />';
        ?></td><td><?php echo checkFile(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_virtuemart'.DS.'html'.DS.'basket.php', JPATH_ROOT.DS.'components'.DS.'com_onepage'.DS.'templates'.DS.'basket.php' ); ?></td></tr>
        
        
        <tr><td><?php
	?>
	Installation method: <select name="imethod"><option value="inline">Use shop.cart</option><option value="redirect">Redirect to com_onepage</option></select>  Overwrite template files <input type="checkbox" checked="checked" disabled="disabled" name="otpl" value="otpl" />
	<br />
	</td><td>
	<input type="submit" value="Install/Upgrade" onclick="javascript: submitbutton('install')"  />
	</td>
	</tr><tr><td>
	Choose whether you want to use shop.cart page under VirtueMart component or you want your visitors to redirect to custom checkout of this component. If you use redirect you may set up joomla to show or hide other modules on an ItemId page.
	</td></tr>
	<tr><td colspan="3">
	This component uses parts of GPL software and therefore is licenced under GNU/GPL licence. For more specific copyright notices please review each php file of this component. This component uses parts of VirtueMart code which was modified according to GNU/GPL license. 
	</td></tr>
<tr><td colspan="3">
	Since version 1.2.104 (jan-2011) template files are due to many changes and compatibility with included javascript functions installed automatically. If you changed your template, please use comparing software to modify them again, or try to install them to your real template directory outside "default" path. 
	</td></tr>
        </table>
        </fieldset>
        <fieldset>
        <legend>Simple uninstall</legend>
        If the component does not work as you expect and you wait for modifications from RuposTel company, you may restore your basket.php without uninstalling onepage component. This will disable onepage and none of its features will be used. If you enabled extended classes and you installed ps_checkout and/or ps_order as extended class, please uninstall them in the appropriate section of this configurator.<br />
        <input type="button" value="Restore original basket.php" onclick="javascript: submitbutton('restorebasket');" />
        </fieldset>
        <fieldset>
        <legend>Built In Extensions</legend>
        Here you can install built in Extensions of One Page Checkout.<br />
        <table class="adminlist">
         <tr class="row0">
         <td style="width: 40%;">
         <img src="/administrator/components/com_onepage/vm_files/google_checkout/checkout.gif" alt="Google Checkout"/> 
         <br />
         Google Checkout provided by: <a href="http://www.opensource-excellence.com/">Open Source Excellence</a> 
         <br />For support of this extension, please buy support license at Open Source Excellence
         </td>
         <td>
         <input type="button" value="Install Google Checkout" onclick="javascript: submitbutton('googleinstall');" /><br />
         This will install ps_google.php and ps_google.cfg.php into your payment modules. It will insert any necessary data into database and use a custom thank you page found in OPC templates. <br />
         <b>Please edit your language files in /administrator/components/com_virtuemart/languages/checkout/__yourlang__.php</b> and change and add this variables: <br />
		 Add PHPSHOP_THANKYOU_GC_SUCCESS variable and find PHPSHOP_GOOGLE_THANKYOU and set it up as following:<br />
		 <textarea cols="70" rows="5">
'PHPSHOP_GOOGLE_THANKYOU' => 'Thanks for your payment. The transaction was successful. You will get a confirmation e-mail for the transaction by Google Checkout. You can now continue or log in at <a href="http://checkout.google.com">checkout.google.com</a> to see the transaction details.',
'PHPSHOP_THANKYOU_GC_SUCCESS' => 'Your order has been successfully placed! Please click on the Google Checkout button to finish the payments in Google Checkout.',
		 </textarea><br />
PHPSHOP_GOOGLE_THANKYOU is shown at the result page after redirection from paypal or google checkout. <br />
Use the following configuration in your Google Checkout Account:<br />
    * Go to Google Checkout (checkout.google.com) --> Settings --> Integration --> API callback URL <br />
    * Enter the url: https://www.yoursite.com/google_notify.php <br />
       google_notify.php is uploaded to your root dir. If you want to change it, please move it to some other directory. <br />
    * Make sure that you UNTICK -- > "My company will only post digitally signed carts" <br />
    * Callback contents: CHOOSE "Notification as HTML (name/value pairs)" <br />
    * API Version: please choose version 2.0 <br />
Also, please make sure that you turn on the following option in Google Checkout: <br />
    * Login Google Checkout (checkout.google.com) <br />
    * My Sales --> Setting<br />
    * Preferrences<br />
    * Check the option "Automatically authorise and charge the buyer's card. " <br />
         </td>
        </tr>
		         <tr class="row1">
         <td style="width: 40%;">
          <img src="/administrator/components/com_onepage/vm_files/fortus_payment/MOTFAKTURA_NY_2.JPG" width="100px" alt="Fortus Finance"/> 
         <br />
         Fortus Finance Extension is a modification of: <a href="http://www.certitrade.net/opensource.php">CERTITRADE OPEN SOURCE</a> which can be downloaded in original code from <a href="http://www.1solution.se/index.php?option=com_content&view=article&id=187&Itemid=165&lang=se">1Solution.se</a>
         This extension was modified by RuposTel to support no core modifications of Virtuemart and to support One Page Checkout. You also need to enable it at OPC extensions and ADVANCED PAYMENT at the Payment tab. 
         </td>
         <td>
         <input type="button" value="Install Fortus Finance Payment Module" onclick="javascript: submitbutton('fortusinstall');" /><br />
  
         </td>
        </tr>

		
		
        </table>
        </fieldset>

<?php
	echo $pane->endPanel();
	}
	echo $pane->startPanel('OPC Extensions', 'panel6');
?>
<fieldset><legend>OnePage Extensions</legend>
	<?php 
	if (empty($this->exthtml)) echo 'No OPC extensions installed'; 
	else
	echo $this->exthtml; 
	?>
</fieldset>

<?php
	echo $pane->endPanel();
	echo $pane->endPane();
		?>
  </form>

<?php
echo ob_get_clean();
function checkFile($file, $file2=null)
{
 $pi = pathinfo($file);
 if (!empty($pi['extension']))
  $name = str_replace('.'.$pi['extension'], '', $pi['basename']);
 else $name = $pi['basename']; 

 $orig = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_onepage'.DS.'vm_files'.DS.$pi['basename'];
 if (!empty($file2)) $orig = $file2;
 
 if (!file_exists($orig)) return 'Cannot Check';
 if (!file_exists($orig) && (file_exists($file))) return 'OK';
 if (file_exists($file))
 {
  
  $d1 = filemtime($file);
  $d2 = filemtime($orig);
  if ($d2>$d1)
  { 
  $d1 = hash_file('md5',$file);
  $d2 = hash_file('md5',$orig);
  if ($d1 != $d2 )
  {
   if (strpos($file, 'templates')!==false)
   return 'Template will not be overwritten'.retI($name, 'template');
   else
   return 'Upgrade'.retI($name, 'install');
  }
  else return 'OK'.retI($name, 'ok');
  
  }
  
  else
  return 'OK'.retI($name, 'ok');; 
 }
 else return 'File not found'.retI($name, 'install');;
}

function retI($name, $task)
{
 return '<input type="hidden" name="'.$name.'" value="'.$task.'" />';
}

// functions to parse variables
function parseP($hidep)
{
 $hidep = str_replace(' ', '', $hidep);
 $arr = explode (',', $hidep);
 return $arr;
}
// returns true if an payment id is there
function isThere($id, $hidep)
{
 //var_dump($id); 
 //var_dump($hidep);
 
 $hidep = ','.$hidep.',';
 if (strpos($hidep, ','.$id.',') !== false) return true;
 if (strpos($hidep, ','.$id.'/') !== false) return true;
 return false;
}
// for an payment id get a default payment id 
function getDefP($id, $hidep)
{
 $hidep = ','.$hidep.',';
 if (strpos($hidep, '/'.$id.',') !== false) return true;
 return false;
 
}
$_SESSION['endmem'] = memory_get_usage(true); 
$mem =  $_SESSION['endmem'] - $_SESSION['startmem'];
//echo 'Cm: '.$mem.' All:'.$_SESSION['endmem'];
?>