<?php error_reporting(E_ALL ^ E_NOTICE); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
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
<?php 
 $session =& JFactory::getSession();
 $uinfo_update = $session->get('uinfo_update_msg');
 $post = JRequest::get('post');


?>
<?php if(!empty($uinfo_update)) echo "<span>".$uinfo_update."</span>"; //if($_SESSION['uinfo_update_msg']!="") echo "<span>".$_SESSION['uinfo_update_msg']."</span>"; ?>

<?php $userID = JRequest::getVar('user_id');
if(isset($userID) && $userID !="" && $userID !="0" )
$user_type1 = JRequest::getVar('user_type');
if($user_type1 == 'BT')
echo "<h3>Edit Customer Info</h3>"; 
else if($user_type1 == 'ST')
echo "<h3>Add/Edit Shipment Address</h3>"; 
else echo "<h3>Register</h3>"; 


$virtuemart_userinfo_id = JRequest::getVar('virtuemart_userinfo_id');
if($virtuemart_userinfo_id != '')
$virtuemart_userinfo = JRequest::getVar('virtuemart_userinfo_id'); //$_GET['virtuemart_userinfo_id'];
if($user_type1 == 'ST')
{
  $session->set('edit_select_shipping_id',$virtuemart_userinfo);
} if($user_type1 == 'BT')
{
  $radio = JRequest::getVar('radio_val');
  $session->set('edit_select_shipping_id',$radio);
} 

//$_SESSION['edit_select_shipping_id']= $_GET['virtuemart_userinfo_id'];


if(isset($userID) && $userID !="" && $userID !="0" ) $user_id = $userID; $session->set('uinfo_update_msg',$user_id); $uid = JRequest::getVar('user_id');
$session->set('pre_user_id_sess', $uid);
//$_SESSION['pre_user_id_sess'] = $_GET['user_id'] ;


 ?>

<?php echo $lsStyleSuffix;

$usertype = JRequest::getVar('user_type');
$userID1 = JRequest::getVar('user_id');
$virtemart_userinfo_id = JRequest::getVar('virtuemart_userinfo_id');

$user_detail="select * from #__virtuemart_userinfos as vu ,#__users as u  where vu.virtuemart_user_id = u.id AND  vu.address_type = '".trim($usertype)."' AND  vu.virtuemart_user_id = '".trim($userID1)."' AND vu.virtuemart_userinfo_id = '".trim($virtemart_userinfo_id)."'";
$db->setQuery($user_detail);
$db->query();
$user_info=$db->loadAssoc();



?>
<script>	
<?php 

if($post['virtuemart_state_id'] != ''){ ?>
 jQuery( function($) {
			$("select.virtuemart_country_id").vm2front("list",{dest : "#virtuemart_state_id",ids : "<?php echo $post['virtuemart_state_id']; ?>"});
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
   var zipcode = jQuery('#zip_field').val();
   var state_id = jQuery('#virtuemart_state_id').val();
   //var email = $('#email_field').val();
if ( jQuery.browser.msie )
 {  
   var flg = 0;
   
   var email = jQuery('#email_field').val();
   var first_name = jQuery('#first_name_field').val();
   var last_name = jQuery('#last_name_field').val();
   var address_1 = jQuery('#address_1_field').val();
   var city = jQuery('#city_field').val();
   var zip = jQuery('#zip_field').val();
   var vm_country_id = jQuery('#virtuemart_country_id').val();
   var vm_state_id = jQuery('#virtuemart_state_id').val();
   var phone_1 = jQuery('#phone_1_field').val();
   var phone_edit = jQuery('#phone_1').val();
   var address_type = jQuery('#address_type_name_field').val();

//======== Add by RCA =========
   var customer_type = jQuery('#customer_type_field').val();

   if(customer_type == '')
	   {
	   	jQuery("#customer_type_field").css('border','1px solid red');		
		flg = 1;		  
	   }
	  else
	   {
	   	jQuery("#customer_type_field").css('border','1px solid #e3e9ef');				
	   }
//======== End by RCA =========

   	  if(email == '')
	   {
	   	jQuery("#email_field").css('border','1px solid red');		
		flg = 1;		  
	   }
	  else
	   {
	   	jQuery("#email_field").css('border','1px solid #e3e9ef');				
	   }
	   
	   if(address_type == '')
	   {
	   	jQuery("#address_type_name_field").css('border','1px solid red');		
		flg = 1;		  
	   }
	   else
	   {
	   	jQuery("#address_type_name_field").css('border','1px solid #e3e9ef');				
	   }
	   
	   if(first_name=='')
	   {
	   	jQuery("#first_name_field").css('border','1px solid red');		
		flg = 1;
	   }
	   else
	   {
	   	jQuery("#first_name_field").css('border','1px solid #e3e9ef');		   
	   }
	 
	   if(last_name == '')
	   {
	   	jQuery("#last_name_field").css('border','1px solid red');		
		flg = 1;
	   }
	   else
	   {
	   jQuery("#last_name_field").css('border','1px solid #e3e9ef');		
	   }
	  
	   if(address_1 == '')
	   {
	   	jQuery("#address_1_field").css('border','1px solid red');		
		flg = 1;
	   }
	   else
	   {
	   	jQuery("#address_1_field").css('border','1px solid #e3e9ef');		   
	   }
	   
	   if(city == '')
	   {
	   	jQuery("#city_field").css('border','1px solid red');		
		flg = 1;
	   }
	   else
	   {
	   	jQuery("#city_field").css('border','1px solid #e3e9ef');		   
	   }
	  
	   if(zip == '')
	   {
	   	jQuery("#zip_field").css('border','1px solid red');		
		flg = 1;
	   }
	   else
	   {
	   	jQuery("#zip_field").css('border','1px solid #e3e9ef');		   
	   }
	   
	   if(vm_country_id == '')
	   {
	   	jQuery("#virtuemart_country_id").css('border','1px solid red');		
		flg = 1;
	   }
	   else
	   {
	   	jQuery("#virtuemart_country_id").css('border','1px solid #e3e9ef');		   
	   }
	  
	   if(vm_state_id == '')
	   {
	   	jQuery("#virtuemart_state_id").css('border','1px solid red');		
		flg = 1;
	   }
	   else
	   {
	   	jQuery("#virtuemart_state_id").css('border','1px solid #e3e9ef');		   
	   }
	  
	   if(phone_1 == '')
	   {
	   	jQuery("#phone_1_field").css('border','1px solid red');		
		flg = 1;
	   }
	   else
	   {
	   	jQuery("#phone_1_field").css('border','1px solid #e3e9ef');		   
	   }
	   
	   if(phone_edit == '')
	   {
	   	jQuery("#phone_1").css('border','1px solid red');		
		flg = 1;
	   }
	   else
	   {
	   	jQuery("#phone_1").css('border','1px solid #e3e9ef');		   
	   }

	   if(flg == 1)return false;
 }	
	
	jQuery.ajax({  
		type: "POST",  
		url: "index.php?option=com_salesreporder&task=check_zipcode",  
		data: { 'zipcode': zipcode,'state_id':state_id },   
		success: function(responce){
		responce = jQuery.trim(responce);		
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
		jQuery("#zip_field").css('border','1px solid red');		
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

<script>
					   
function sendData()
{
 
		$.ajax(
					{
						// The link we are accessing.   [Note:- Replace the url with the one you want to access]
						url: "index.php?option=com_salesreporder&task=cus_message",
						
						// The type of request.
						type: "post",
						
						// The type of data that is getting returned.
						dataType: "text",
						
						// The form data is sent using the id of the form.  [Note:- Change the id as per each form]
					   //data: $('#writeMessageForm').serialize(),
						
						error: function(){
							alert("Error in ajax call for insert");
						},		
						success: function(strData){
							
                           //alert("Email address in use")
						   $("#cus_message").html(strData);
						   return false;
						 
						}
					}							
		 );
 }					
</script>

<script>
 function Chk()
 {
   document.getElementById('chkemail').value="chkmail";
   
 }
</script>

<div id="part_second">

<form id="userregistrationForm" name="frm1" method="post" onsubmit="return myuserregistrationValidate1();"  action="<?php echo $_SERVER['REQUEST_URI']; ?>" >
	<table width="540" border="0" cellspacing="10px">        
       <?php
           
	       $userID2 = JRequest::getVar('user_id');		  
		   if(!isset($userID2) || $_SERVER['REQUEST_URI'] == "/register-user.html" ){
	       //$post = JRequest::get('post');
	   ?>
        <tr>
			<td class="key">
				<label for="company_field" class="company">
					Company (if applicable)
				</label>
			</td>
			<td>
				<input type="text" maxlength="40" value="<?php echo $post['company'] ?>" size="30" name="company" id="company_field"  />
			</td>
		</tr>
        <tr>
			<td class="key">
				<label for="email_field" class="email">
					E-Mail *
				</label>
			</td>
			<td>
				<input type="text" maxlength="100" class="required" value="<?php echo $post['email'] ?>" size="30" name="email" id="email_field" aria-required="true" required="required" />
               
			</td>
		</tr>
		
<!--=======  add by RCA ==========-->

	<?php
			
			$db = JFactory::getDBO();
			$mysql = "select uv.fieldvalue from #__virtuemart_userfields as uf right join #__virtuemart_userfield_values as uv on uv.virtuemart_userfield_id = uf.virtuemart_userfield_id where uf.name='CustomerType'";
			$db->setQuery($mysql);
			$myresults = $db->loadObjectList();
	?>


		 <tr>
			<td class="key">
				<label for="customer_type" class="customertype">
					Customer Type *
				</label>
			</td>
			<td>
				
				<select name="customer_type" id="customer_type_field" aria-required="true" required="required" class="required">
					<option value="">---Select---</option>
					<?php foreach($myresults as $myvalue) : ?>
						<option value="<?php echo $myvalue->fieldvalue?>"<?php if($myvalue->fieldvalue==$post['customer_type']) echo "selected='selected'" ?>><?php echo $myvalue->fieldvalue; ?></option>
					<?php endforeach; ?>
				</select>
				               
			</td>
		</tr>	

<!--=======  end by RCA ==========-->

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
				<input type="text" maxlength="15" class="required" value="<?php echo $post['first_name'] ?>" size="30" name="first_name" id="first_name_field" aria-required="true" required="required" /> 
			</td>
		</tr>
		<!--<tr>
			<td class="key">
				<label for="middle_name_field" class="middle_name">
					Middle Name
				</label>
			</td>
			<td>
				<input type="text" maxlength="32" value="<?php echo $post['middle_name'] ?>" size="30" name="middle_name" id="middle_name_field"> 
			</td>
		</tr>-->
		<tr>
			<td class="key">
				<label for="last_name_field" class="last_name">
					Last Name *
				</label>
			</td>
			<td>
				<input type="text" maxlength="15" class="required" value="<?php echo $post['last_name'] ?>" size="30" name="last_name" id="last_name_field" aria-required="true" required="required" /> 
			</td>
		</tr>
		<tr>
			<td class="key">
				<label for="address_1_field" class="address_1">
					Address 1 *
				</label>
			</td>
			<td>
				<input type="text" maxlength="41" class="required" value="<?php echo htmlspecialchars($post['address_1']); ?>" size="30" name="address_1" id="address_1_field" aria-required="true" required="required" /> 
			</td>
		</tr>
		<tr>
			<td class="key">
				<label for="address_2_field" class="address_2">
					Address 2
				</label>
			</td>
			<td>
			<input type="text" maxlength="41" value="<?php echo htmlspecialchars($post['address_2']); ?>" size="30" name="address_2" id="address_2_field" />
			</td>
		</tr>		
		<tr>
			<td class="key">
				<label for="city_field" class="city">
					City *
				</label>
			</td>
			<td>
				<input type="text" maxlength="31" class="required" value="<?php echo htmlspecialchars($post['city']); ?>" size="30" name="city" id="city_field" aria-required="true" required="required" /> 
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
    <option value="223" <?php if($post['virtuemart_country_id'] == "223")echo 'selected="selected"'; ?>>United States</option>
	<option value="38" <?php if($post['virtuemart_country_id'] == "38")echo 'selected="selected"'; ?> >Canada</option>
	
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
				<input type="text" maxlength="32" class="required" value="<?php echo $post['zip'] ?>" size="30" name="zip" id="zip_field" aria-required="true" required="required" /> 
			</td>
		</tr>
		<tr>
			<td class="key">
				<label for="phone_1_field" class="phone_1">
					Phone *
				</label>
			</td>
			<td>
				<input type="text" maxlength="32" class="required" value="<?php echo $post['phone_1'] ?>" size="30" name="phone_1" id="phone_1_field" aria-required="true" required="required" /> 
			</td>
		</tr>
		<tr>
			<td class="key">
		  <label for="phone_2_field" class="phone_2">Alternate  Phone </label></td>
	  <td>
				<input type="text" maxlength="32" value="<?php echo $post['phone_2'] ?>" size="30" name="phone_2" id="phone_2_field" /> 
			</td>
		</tr>
		<tr>
			<td class="key">
				<label for="fax_field" class="fax">
					Fax
				</label>
			</td>
			<td>
				<input type="text" maxlength="32" value="<?php echo $post['fax'] ?>" size="30" name="fax" id="fax_field" /> 
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
				<input type="text" maxlength="41" value="<?php echo $user_info['company'] ?>"  size="30" name="company" id="company_field" /> 
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
				<input type="text" maxlength="100" class="required" value="<?php echo $user_info['email'] ?>" size="30" name="email" id="email_field" aria-required="true" required="required" /> 
                
                 <input type="hidden" value="<?php echo $user_info['email'] ?>" name="chkemail" id="chkemail" />
                
			</td>
		</tr>        
        <?php } else { 
		
		?>	
        <tr>
			<td class="key">
				<label for="shipto_address_type_name_field" class="shipto_address_type_name" aria-invalid="false">
					Address Nickname *
				</label>
			</td>
			<td>
				<input type="text" maxlength="32" class="required" value="<?php echo $user_info['address_type_name'] ?>" size="30" name="address_type_name" id="address_type_name_field" aria-required="true" required="required" /> 
			</td>
		</tr>
        <?php  } ?>	

<!--=======  add by RCA ==========-->

		<tr>
			<td class="key">
				<label for="customer_type" class="customertype">
					Customer Type *
				</label>
			</td>
			<td>
				
				<select name="customer_type" id="customer_type_field" aria-required="true" required="required" class="required">
					<option value="">---Select---</option>
					<?php foreach($myresults as $myvalue) : ?>
						<option value="<?php echo $myvalue->fieldvalue?>"<?php if($myvalue->fieldvalue==$user_info['CustomerType']) echo "selected='selected'" ?>><?php echo $myvalue->fieldvalue; ?></option>
					<?php endforeach; ?>
				</select>
				               
			</td>
		</tr>

<!--=======  end by RCA ==========-->

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
				<input type="text" maxlength="15" class="required" value="<?php echo $user_info['first_name'] ?>" size="30" name="first_name" id="first_name_field" aria-required="true" required="required" /> 
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
				<input type="text" maxlength="15" class="required" value="<?php echo $user_info['last_name'] ?>" size="30" name="last_name" id="last_name_field" aria-required="true" required="required" /> 
			</td>
		</tr>
		<tr>
			<td class="key">
				<label for="address_1_field" class="address_1">
					Address 1 *
				</label>
			</td>
			<td>
				<input type="text" maxlength="41" class="required" value="<?php echo htmlspecialchars($user_info['address_1']); ?>" size="30" name="address_1" id="address_1_field" aria-required="true" required="required" /> 
			</td>
		</tr>
		<tr>
			<td class="key">
				<label for="address_2_field" class="address_2">
					Address 2
				</label>
			</td>
			<td>
				<input type="text" maxlength="41" value="<?php echo htmlspecialchars($user_info['address_2']); ?>" size="30" name="address_2" id="address_2_field" /> 
			</td>
		</tr>		
		<tr>
			<td class="key">
				<label for="city_field" class="city">
					City *
				</label>
			</td>
			<td>
				<input type="text" maxlength="31" class="required" value="<?php echo htmlspecialchars($user_info['city']); ?>" size="30" name="city" id="city_field" aria-required="true" required="required" /> 
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
    <option <?php if($user_info['virtuemart_country_id'] == "223" || $post['virtuemart_country_id'] == "223")echo 'selected="selected"'; ?> value="223">United States</option>
	<option <?php if($user_info['virtuemart_country_id'] == "38" || $post['virtuemart_country_id'] == "38")echo 'selected="selected"'; ?> value="38">Canada</option>	
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
				<input type="text" maxlength="32" class="required" value="<?php echo $user_info['zip'] ?>" size="30" name="zip" id="zip_field" aria-required="true" required="required" /> 
			</td>
		</tr>
		<tr>
			<td class="key">
				<label for="phone_1_field" class="phone_1">
					Phone *
				</label>
			</td>
			<td>
				<input type="text" maxlength="32" class="required" value="<?php echo $user_info['phone_1'] ?>" size="30" name="phone_1" id="phone_1" aria-required="true"  required="required" />
			</td>
		</tr>
		<tr>
			<td class="key">
		  <label for="phone_2_field" class="phone_2">Alternate Phone</label></td>
	  <td>
				<input type="text" maxlength="32" value="<?php echo $user_info['phone_2'] ?>" size="30" name="phone_2" id="phone_2_field" /> 
			</td>
		</tr>
		<tr>
			<td class="key">
				<label for="fax_field" class="fax">
					Fax
				</label>
			</td>
			<td>
				<input type="text" maxlength="32" value="<?php echo $user_info['fax'] ?>" size="30" name="fax" id="fax_field" /> 
			</td>
		</tr>        	
       <?php } ?> 
          
      <tr>
        <td></td>
        <td valign="top"><input type="submit" value="<?php echo $lsSubmitText; ?>" id="userregistrationButton" class="userregistrationButton" name="submit" />
      
            <input type="hidden" name="userregistrationAction" value="send"/>
            <input type="hidden" name="form_type" value="second_form"/>
            <input type="hidden" name="user_id" value="<?php $user_ID = JRequest::getVar('user_id'); echo trim($user_ID); ?>"/>
            <input type="hidden" name="virtuemart_userinfo_id" value="<?php echo trim($user_info['virtuemart_userinfo_id']); ?>"/>
            
            <input type="hidden" name="user_type" value="<?php $user_type = JRequest::getVar('user_type'); if($user_type =="")echo "BT"; else echo $user_type; ?>"/>  
            <input type="hidden" name="new" value="<?php $new1 = JRequest::getVar('new'); if($new1 == "")echo "0"; else echo $new1; ?>"/>          
            <input type="hidden" name="check" value="post"/>
            </td>
      </tr>
      </table>		
  </form>
</div>

</div>
<script>

/*function test()
{
	setTimeout(function(){window.close()}, 500);	
}*/
/*$(function() {
 if($('form').submit(function() {
     $('.userregistrationButton').live('click',function(){ 
		window.close();												setTimeout(function() { window.close(); }, 500);
	window.opener.document.getElementById('customer_name').value;
	window.opener.document.getElementById('customer_name1').value;
	window.close();						
      setTimeout(function(){window.close()}, 500);*/


 //setTimeout("test()", 8000);
       //window.setTimeout(test(), 500);
       //setTimeout(test(),2000);
      //setTimeout(function(){window.close();},500)
/*function test()
		{
			window.close();
		}
		
});
  }));
});*/
</script>

<?php

$uinfo_update_msg = "";
$session->set('uinfo_update_msg',$uinfo_update_msg);	
//$session->set('uinfo_update_msg')
//$_SESSION['uinfo_update_msg']="";

$uid1 = JRequest::getVar('user_id');
if($uid1!='')
die; ?>
