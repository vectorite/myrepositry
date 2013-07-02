<?php
 /**
 *User Register after card purchase
 @package Module Free Contact for Joomla! 1.6
 * @link       http://www.greek8.com/
* @copyright (C) 2011- test test
 * @license GNU/GPL http://www.gnu.org 
 */

defined('_JEXEC') or die('Restricted access');


//JHTMLBehavior::formvalidation();


require_once (dirname(__FILE__).DS.'helper.php');

$db = JFactory::getDBO();
$loDoc =& JFactory::getDocument();
$session =& JFactory::getSession();
//$loDoc->addScript("http://code.jquery.com/jquery-latest.js");
//$loDoc->addScript(JURI::root().'modules/mod_userregistration/mod_userregistration.js');
//$loDoc->addStyleSheet(JURI::root().'modules/mod_userregistration/mod_userregistration.css');
//$loDoc->addScript(JURI::root().'components/com_virtuemart/assets/js/vmsite.js');

$lsSubmitText = $params->get('submit_button', 'Continue');
$lsStyleSuffix = $params->get('moduleclass_sfx', null);

$lsAction = JRequest::getVar('userregistrationAction', null, 'POST');
if ($lsAction == 'send') {
    $lsMessage = moduserregistrationHelper::sendDataReg($params);
}
if (!isset($lsMessage)){ $lsMessage = $params->get('introtext');}

$credit = @$params->get('credit');

?>
<link href="<?php echo JURI::root();?>modules/mod_userregistration/mod_userregistration.css" type="text/css" />
<script src="<?php echo JURI::root();?>modules/mod_userregistration/jquery-latest.js"></script>
<script src="<?php echo JURI::root();?>components/com_virtuemart/assets/js/vmsite.js"></script>
<script src='<?php echo JURI::root();?>modules/mod_userregistration/mod_userregistration.js' ></script>
<script src='<?php echo JURI::root();?>modules/mod_userregistration/jquery.maskedinput-1.3.js' ></script>

<style>
#WWMainPage{display:none;}
#userregistration h3 {margin:0 auto;}
</style>
<div id="userregistration">
<?php if($_SESSION['uinfo_update_msg']!="") echo "<span>".$_SESSION['uinfo_update_msg']."</span>"; ?>

<?php if(isset($_GET['user_id']) && $_GET['user_id'] !="" && $_GET['user_id'] !="0" )echo "<h3>Add/Edit shipment address</h3>"; else echo "<h3>Register</h3>"; 

if(isset($_GET['user_id']) && $_GET['user_id'] !="" && $_GET['user_id'] !="0" )$_SESSION['pre_user_id_sess'] = $_GET['user_id'] ;

if($_GET['virtuemart_userinfo_id'] != '')
$_SESSION['edit_select_shipping_id']= $_GET['virtuemart_userinfo_id'];
 ?>

<?php echo $lsStyleSuffix;
$user_detail="select * from #__virtuemart_userinfos as vu ,#__users as u  where vu.virtuemart_user_id = u.id AND  vu.address_type = '".trim($_GET['user_type'])."' AND  vu.virtuemart_user_id = '".trim($_GET['user_id'])."' AND vu.virtuemart_userinfo_id = '".trim($_GET['virtuemart_userinfo_id'])."'";
$db->setQuery($user_detail);
$db->query();
$user_info=$db->loadAssoc();
?>
<script>
<?php if($_POST['virtuemart_state_id'] != ''){ ?>
 jQuery( function($) {
			$("select.virtuemart_country_id").vm2front("list",{dest : "#virtuemart_state_id",ids : "<?php echo $_POST['virtuemart_state_id']; ?>"});
		});
<?php } else {?>
jQuery( function($) {
			$("select.virtuemart_country_id").vm2front("list",{dest : "#virtuemart_state_id",ids : "<?php echo $user_info['virtuemart_state_id']; ?>"});
		});
<?php }?>				
var flag_submit = 1;		

jQuery(function($){   
$("#phone_1_field").mask("999-999-9999");
$("#phone_2_field").mask("999-999-9999");
$("#fax_field").mask("999-999-9999");
$("#phone_1").mask("999-999-9999");
$("#phone_2_field").mask("999-999-9999");
});

function myuserregistrationValidate1()
{
var zipcode = $('#zip_field').val();
var state_id = $('#virtuemart_state_id').val();
if ( $.browser.msie )
 {
   var flg = 0;
   var email = $('#email_field').val();
   var first_name = $('#first_name_field').val();
   var last_name = $('#last_name_field').val();
   var address_1 = $('#address_1_field').val();
   var city = $('#city_field').val();
   var zip = $('#zip_field').val();
   var vm_country_id = $('#virtuemart_country_id').val();
   var vm_state_id = $('#virtuemart_state_id').val();
   var phone_1 = $('#phone_1_field').val();
   	  if(email == '')
	   {
	   	jQuery("#email_field").css('outline','1px solid red');		
		flg = 1;		  
	   }
	   else
	   {
	   	jQuery("#email_field").css('outline','none');				
	   }
	   
	   if(first_name=='')
	   {
	   	jQuery("#first_name_field").css('outline','1px solid red');		
		flg = 1;
	   }
	   else
	   {
	   	jQuery("#first_name_field").css('outline','none');		   
	   }
	 
	   if(last_name == '')
	   {
	   	jQuery("#last_name_field").css('outline','1px solid red');		
		flg = 1;
	   }
	   else
	   {
	   jQuery("#last_name_field").css('outline','none');		
	   }
	  
	   if(address_1 == '')
	   {
	   	jQuery("#address_1_field").css('outline','1px solid red');		
		flg = 1;
	   }
	   else
	   {
	   	jQuery("#address_1_field").css('outline','none');		   
	   }
	   
	   if(city == '')
	   {
	   	jQuery("#city_field").css('outline','1px solid red');		
		flg = 1;
	   }
	   else
	   {
	   	jQuery("#city_field").css('outline','none');		   
	   }
	  
	   if(zip == '')
	   {
	   	jQuery("#zip_field").css('outline','1px solid red');		
		flg = 1;
	   }
	   else
	   {
	   	jQuery("#zip_field").css('outline','none');		   
	   }
	   
	   if(vm_country_id == '')
	   {
	   	jQuery("#virtuemart_country_id").css('outline','1px solid red');		
		flg = 1;
	   }
	   else
	   {
	   	jQuery("#virtuemart_country_id").css('outline','none');		   
	   }
	  
	   if(vm_state_id == '')
	   {
	   	jQuery("#virtuemart_state_id").css('outline','1px solid red');		
		flg = 1;
	   }
	   else
	   {
	   	jQuery("#virtuemart_state_id").css('outline','none');		   
	   }
	  
	   if(phone_1 == '')
	   {
	   	jQuery("#phone_1_field").css('outline','1px solid red');		
		flg = 1;
	   }
	   else
	   {
	   	jQuery("#phone_1_field").css('outline','none');		   
	   }
	   if(flg == 1)return false;
 }	
	
	$.ajax({  
		type: "POST",  
		url: "index.php?option=com_salesreporder&task=check_zipcode",  
		data: { 'zipcode': zipcode,'state_id':state_id },   
		success: function(responce){
		responce = $.trim(responce);
		if(responce == "correct")
		{
		if(flag_submit){
						flag_submit=0;						
						document.getElementById("userregistrationButton").click();
						}else{
		//document.getElementById("userregistrationButton").click();
		document.getElementById("userregistrationForm").submit();
		return true;
		}
		//document.getElementById("userregistrationForm").submit();
		//document.frm1.submit();	
		
		}
		else
		{
		jQuery("#zip_field").css('outline','1px solid red');		
		alert('zip code is not correct.');
		return false;
		}			
		} 
	});
		
if(flag_submit)	
return false;
else
return true;
}		
</script>

<div id="part_second">
<form id="userregistrationForm" name="frm1" method="post" onsubmit="return myuserregistrationValidate1()"  action="<?php echo $_SERVER['REQUEST_URI']; ?>" >
		<table width="540" border="0" cellspacing="10px">        
       <?php if(!isset($_GET['user_id'])){ ?>
        <tr>
			<td class="key">
				<label for="company_field" class="company">
					Company (if applicable)
				</label>
			</td>
			<td>
				<input type="text" maxlength="64" value="<?php echo $_POST['company'] ?>" size="30" name="company" id="company_field"> 
			</td>
		</tr>
        <tr>
			<td class="key">
				<label for="email_field" class="email">
					E-Mail *
				</label>
			</td>
			<td>
				<input type="text" maxlength="100" class="required" value="<?php echo $_POST['email'] ?>" size="30" name="email" id="email_field" aria-required="true" required="required"> 
			</td>
		</tr>		
		<tr>
			<td class="key">
				<label for="title_field" class="title">
					Title
				</label>
			</td>
			<td>
				<select size="0" name="title" id="title">
	<option value="Mr">Mr</option>
	<option value="Mrs">Mrs</option>
	<option value="Ms">Ms</option>
	<option value="Dr">Dr</option>
</select>

			</td>
		</tr>
		<tr>
			<td class="key">
				<label for="first_name_field" class="first_name">
					First Name *
				</label>
			</td>
			<td>
				<input type="text" maxlength="32" class="required" value="<?php echo $_POST['first_name'] ?>" size="30" name="first_name" id="first_name_field" aria-required="true" required="required"> 
			</td>
		</tr>
		<!--<tr>
			<td class="key">
				<label for="middle_name_field" class="middle_name">
					Middle Name
				</label>
			</td>
			<td>
				<input type="text" maxlength="32" value="<?php echo $_POST['middle_name'] ?>" size="30" name="middle_name" id="middle_name_field"> 
			</td>
		</tr>-->
		<tr>
			<td class="key">
				<label for="last_name_field" class="last_name">
					Last Name *
				</label>
			</td>
			<td>
				<input type="text" maxlength="32" class="required" value="<?php echo $_POST['last_name'] ?>" size="30" name="last_name" id="last_name_field" aria-required="true" required="required"> 
			</td>
		</tr>
		<tr>
			<td class="key">
				<label for="address_1_field" class="address_1">
					Address 1 *
				</label>
			</td>
			<td>
				<input type="text" maxlength="64" class="required" value="<?php echo htmlspecialchars($_POST['address_1']); ?>" size="30" name="address_1" id="address_1_field" aria-required="true" required="required"> 
			</td>
		</tr>
		<tr>
			<td class="key">
				<label for="address_2_field" class="address_2">
					Address 2
				</label>
			</td>
			<td>
				<input type="text" maxlength="64" value="<?php echo htmlspecialchars($_POST['address_2']); ?>" size="30" name="address_2" id="address_2_field"> 
			</td>
		</tr>		
		<tr>
			<td class="key">
				<label for="city_field" class="city">
					City *
				</label>
			</td>
			<td>
				<input type="text" maxlength="32" class="required" value="<?php echo htmlspecialchars($_POST['city']); ?>" size="30" name="city" id="city_field" aria-required="true" required="required"> 
			</td>
		</tr>       
        
		<tr>
			<td class="key">
				<label for="virtuemart_country_id_field" class="virtuemart_country_id">
					Country *
				</label>
			</td>
			<td>
				<select class="virtuemart_country_id" name="virtuemart_country_id" id="virtuemart_country_id" aria-required="true" required="required" aria-invalid="true" onchange="jQuery( function($) {$('select.virtuemart_country_id').vm2front('list',{dest : '#virtuemart_state_id',ids : this.id});});">
	<!--<option selected="selected" value="">-- Select --</option>-->
    <option value="223" <?php if($_POST['virtuemart_country_id'] == "223")echo 'selected="selected"'; ?>>United States</option>
	<option value="38" <?php if($_POST['virtuemart_country_id'] == "38")echo 'selected="selected"'; ?> >Canada</option>
	
</select>

			</td>
		</tr>
		 <tr>
			<td class="key">
				<label for="virtuemart_state_id_field" class="virtuemart_state_id">
					State / Province / Region *
				</label>
			</td>
			<td>
				<select required="" name="virtuemart_state_id" size="1" id="virtuemart_state_id" class="inputbox multiple" aria-invalid="false">
						<option value="">-- Select --</option>
						</select>
			</td>
		</tr>
        <tr>
			<td class="key">
				<label for="zip_field" class="zip">
					Zip / Postal Code *
				</label>
			</td>
			<td>
				<input type="text" maxlength="32" class="required" value="<?php echo $_POST['zip'] ?>" size="30" name="zip" id="zip_field" aria-required="true" required="required"> 
			</td>
		</tr>
		<tr>
			<td class="key">
				<label for="phone_1_field" class="phone_1">
					Phone *
				</label>
			</td>
			<td>
				<input type="text" maxlength="32" class="required" value="<?php echo $_POST['phone_1'] ?>" size="30" name="phone_1" id="phone_1_field" aria-required="true" required="required"> 
			</td>
		</tr>
		<tr>
			<td class="key">
		  <label for="phone_2_field" class="phone_2">Alternate  Phone </label></td>
	  <td>
				<input type="text" maxlength="32" value="<?php echo $_POST['phone_2'] ?>" size="30" name="phone_2" id="phone_2_field"> 
			</td>
		</tr>
		<tr>
			<td class="key">
				<label for="fax_field" class="fax">
					Fax
				</label>
			</td>
			<td>
				<input type="text" maxlength="32" value="<?php echo $_POST['fax'] ?>" size="30" name="fax" id="fax_field"> 
			</td>
		</tr>		
       <?php }else{ ?>
               <tr>
			<td class="key">
				<label for="company_field" class="company">
					Company (if applicable)
				</label>
			</td>
			<td>
				<input type="text" maxlength="64" value="<?php echo $user_info['company'] ?>"  size="30" name="company" id="company_field"> 
			</td>
		</tr>
        <?php if($user_info['address_type']=="BT"){  ?>
        <tr>
			<td class="key">
				<label for="email_field" class="email">
					E-Mail *
				</label>
			</td>
			<td>
				<input type="text" maxlength="100" readonly="readonly" class="required" value="<?php echo $user_info['email'] ?>" size="30" name="email" id="email_field" aria-required="true" required="required"> 
			</td>
		</tr>        
        <?php } else { ?>	
        <tr>
			<td class="key">
				<label for="shipto_address_type_name_field" class="shipto_address_type_name" aria-invalid="false">
					Address Nickname *
				</label>
			</td>
			<td>
				<input type="text" maxlength="32" class="required" value="<?php echo $user_info['address_type_name'] ?>" size="30" name="address_type_name" id="address_type_name_field" aria-required="true" required="required"> 
			</td>
		</tr>
        <?php } ?>	
		<tr>
			<td class="key">
				<label for="title_field" class="title">
					Title
				</label>
			</td>
			<td>
				<select size="0" name="title" id="title">
                    <option <?php if($user_info['title'] == "Mr")echo 'selected="selected"'; ?> value="Mr">Mr</option>
                    <option <?php if($user_info['title'] == "Mrs")echo 'selected="selected"'; ?> value="Mrs">Mrs</option>
                    <option <?php if($user_info['title'] == "Ms")echo 'selected="selected"'; ?> value="Ms">Ms</option>
                    <option <?php if($user_info['title'] == "Dr")echo 'selected="selected"'; ?> value="Dr">Dr</option>
                    Ms
                </select>

			</td>
		</tr>
		<tr>
			<td class="key">
				<label for="first_name_field" class="first_name">
					First Name *
				</label>
			</td>
			<td>
				<input type="text" maxlength="32" class="required" value="<?php echo $user_info['first_name'] ?>" size="30" name="first_name" id="first_name_field" aria-required="true" required="required"> 
			</td>
		</tr>
		<!--<tr>
			<td class="key">
				<label for="middle_name_field" class="middle_name">
					Middle Name
				</label>
			</td>
			<td>
				<input type="text" maxlength="32" value="<?php echo $user_info['middle_name'] ?>" size="30" name="middle_name" id="middle_name_field"> 
			</td>
		</tr>-->
		<tr>
			<td class="key">
				<label for="last_name_field" class="last_name">
					Last Name *
				</label>
			</td>
			<td>
				<input type="text" maxlength="32" class="required" value="<?php echo $user_info['last_name'] ?>" size="30" name="last_name" id="last_name_field" aria-required="true" required="required"> 
			</td>
		</tr>
		<tr>
			<td class="key">
				<label for="address_1_field" class="address_1">
					Address 1 *
				</label>
			</td>
			<td>
				<input type="text" maxlength="64" class="required" value="<?php echo htmlspecialchars($user_info['address_1']); ?>" size="30" name="address_1" id="address_1_field" aria-required="true" required="required"> 
			</td>
		</tr>
		<tr>
			<td class="key">
				<label for="address_2_field" class="address_2">
					Address 2
				</label>
			</td>
			<td>
				<input type="text" maxlength="64" value="<?php echo htmlspecialchars($user_info['address_2']); ?>" size="30" name="address_2" id="address_2_field"> 
			</td>
		</tr>		
		<tr>
			<td class="key">
				<label for="city_field" class="city">
					City *
				</label>
			</td>
			<td>
				<input type="text" maxlength="32" class="required" value="<?php echo htmlspecialchars($user_info['city']); ?>" size="30" name="city" id="city_field" aria-required="true" required="required"> 
			</td>
		</tr>
        
       
		<tr>
			<td class="key">
				<label for="virtuemart_country_id_field" class="virtuemart_country_id">
					Country *
				</label>
			</td>
			<td>
				<select class="virtuemart_country_id" name="virtuemart_country_id" id="virtuemart_country_id" aria-required="true" required="required" aria-invalid="true" onchange="jQuery( function($) {$('select.virtuemart_country_id').vm2front('list',{dest : '#virtuemart_state_id',ids : this.id});});">
	<!--<option selected="selected" value="">-- Select --</option>-->
    <option <?php if($user_info['virtuemart_country_id'] == "223" || $_POST['virtuemart_country_id'] == "223")echo 'selected="selected"'; ?> value="223">United States</option>
	<option <?php if($user_info['virtuemart_country_id'] == "38" || $_POST['virtuemart_country_id'] == "38")echo 'selected="selected"'; ?> value="38">Canada</option>	
</select>

			</td>
		</tr>
		<tr>
			<td class="key">
				<label for="virtuemart_state_id_field" class="virtuemart_state_id">
					State / Province / Region *
				</label>
			</td>
			<td>
				<select required="" name="virtuemart_state_id" size="1" id="virtuemart_state_id" class="inputbox multiple" aria-invalid="false">
						<option value="">-- Select --</option>
						</select>
			</td>
		</tr>
         <tr>
			<td class="key">
				<label for="zip_field" class="zip">
					Zip / Postal Code *
				</label>
			</td>
			<td>
				<input type="text" maxlength="32" class="required" value="<?php echo $user_info['zip'] ?>" size="30" name="zip" id="zip_field" aria-required="true" required="required"> 
			</td>
		</tr>
		<tr>
			<td class="key">
				<label for="phone_1_field" class="phone_1">
					Phone *
				</label>
			</td>
			<td>
				<input type="text" maxlength="32" class="required" value="<?php echo $user_info['phone_1'] ?>" size="30" name="phone_1" id="phone_1" required="required" > 
			</td>
		</tr>
		<tr>
			<td class="key">
		  <label for="phone_2_field" class="phone_2">Alternate Phone</label></td>
	  <td>
				<input type="text" maxlength="32" value="<?php echo $user_info['phone_2'] ?>" size="30" name="phone_2" id="phone_2_field"> 
			</td>
		</tr>
		<tr>
			<td class="key">
				<label for="fax_field" class="fax">
					Fax
				</label>
			</td>
			<td>
				<input type="text" maxlength="32" value="<?php echo $user_info['fax'] ?>" size="30" name="fax" id="fax_field"> 
			</td>
		</tr>        	
       <?php } ?>      
      <tr>
        <td></td>
        <td valign="top"><input type="submit" value="<?php echo $lsSubmitText; ?>" id="userregistrationButton" class="userregistrationButton" name="submit" />
            <input type="hidden" name="userregistrationAction" value="send"/>
            <input type="hidden" name="form_type" value="second_form"/>
            <input type="hidden" name="user_id" value="<?php echo trim($_GET['user_id']); ?>"/>
            <input type="hidden" name="virtuemart_userinfo_id" value="<?php echo trim($user_info['virtuemart_userinfo_id']); ?>"/>
            
            <input type="hidden" name="user_type" value="<?php if($_GET['user_type'] =="")echo "BT"; else echo $_GET['user_type']; ?>"/>  
            <input type="hidden" name="new" value="<?php if($_GET['new'] == "")echo "0"; else echo $_GET['new']; ?>"/>          
            <input type="hidden" name="check" value="post"/></td>
      </tr>
      </table>		
  </form>
</div>

</div><?php $_SESSION['uinfo_update_msg']="";
if($_REQUEST['user_id']!='')
die; ?>