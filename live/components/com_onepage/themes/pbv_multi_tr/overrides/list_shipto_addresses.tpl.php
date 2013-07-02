<?php
if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' ); 
/**
*
* @version $Id: list_shipto_addresses.tpl.php 1725 2009-04-21 09:10:34Z soeren_nb $
* @package VirtueMart
* @subpackage templates
* @copyright Copyright (C) 2007-2009 Soeren Eberhardt. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See /administrator/components/com_virtuemart/COPYRIGHT.php for copyright notices and details.
*
* http://virtuemart.net
*/

?>

<div class="BTaddress" id="staddresses">
	<div class="sectiontableentry1">
		<div class="op_radiowrapper">
		<?php
		$checked = '';
		//if( empty($STaddress) || (!empty($cart->STsameAsBT))) 
		{
			$checked = 'checked="checked" ';
		}
		 
		//echo '<input type="radio" name="'.$name.'" id="'.$bt_user_info_id.'" value="'.$bt_user_info_id.'" '.$checked.'/>'."\n";
		//echo JText::_('COM_VIRTUEMART_USER_FORM_ST_SAME_AS_BT'); 
		echo '<input type="radio" name="ship_to_info_id" id="'.$bt_user_info_id.'" value="'.$bt_user_info_id.'" '.$checked.' class="stradio"/>'."\n";
		
		?></div>
		<div class="op_labelwrapper">
		<label for="<?php echo $bt_user_info_id ?>"><?php echo JText::_('COM_VIRTUEMART_USER_FORM_ST_SAME_AS_BT') ?></label>
		</div>
	</div>
<div>
<?php
$i = 2;
foreach ($STaddressList as $key=>$ST)
{

	echo '<div class="sectiontableentry'.$i.'">';
	echo '<div class="op_radiowrapper">';
	
	$checked = '';
	//if (false)
	{
	// for now there will be no shipping address selected according to userinfo id
	//var_dump($cart); die(); 
	if(empty($cart->ST)){$cart->ST = $_SESSION['mera_cart']->ST;}
	if($cart->ST['virtuemart_userinfo_id'] ==''){
	$cart->ST['virtuemart_userinfo_id'] = $_SESSION['mera_cart_shipto_id'];	
	}
	$select_staddr = true;
	//if (empty($cart->STsameAsBT) && (!empty($cart->ST) && (!empty($cart->ST['virtuemart_userinfo_id']))))
	if ((!empty($cart->ST) && (!empty($cart->ST['virtuemart_userinfo_id']))))
	{
	
	$value = $cart->ST['virtuemart_userinfo_id']; 
	if ( $value == $ST->virtuemart_userinfo_id){
				$checked = 'checked="checked" ';
				$select_staddr = false;
				}
	}
	if($select_staddr && ($_SESSION['ship_select_on_reg']=="ship_select"))
		{
		$checked = 'checked="checked" ';
		$_SESSION['ship_select_on_reg'] = '';
		}
	
	
	}
	echo '<input type="radio" name="ship_to_info_id" id="' . $ST->virtuemart_userinfo_id . '" value="' . $ST->virtuemart_userinfo_id. '" '.$checked.' class="stradio"/>'."\n";
	
	echo '</div>'."\n";
	echo '<div class="op_labelwrapper">'."\n";
	echo '<label for="'.$ST->virtuemart_userinfo_id.'">';
	//obsolete: $edit_link = 'index.php?option=com_virtuemart&view=user&task=editAddressSt&addrtype=ST&cid[]='.$uid.'&virtuemart_userinfo_id='.$ST->virtuemart_userinfo_id;
	
	echo '<strong>' . $ST->address_type_name . "</strong> ";
	
	$edit_label = JText::_('JACTION_EDIT'); 
	if ($edit_label == 'JACTION_EDIT') $edit_label = JText::_('EDIT'); 
	
	echo '(<a href="'.$ST->edit_link.'">'.$edit_label.'</a>)'."\n";
	echo '<div>';
	foreach ($BTaddress as $item)
	{
	
	if (!empty($ST->$item['name']))
	{
	?>
         <div style="width: 100%; clear: both;">
           <div class="op_field_name" ><?php echo $item['title'] ?> </div>
           <div class="op_field_value">
           <?php
		        //var_dump($item['value']); 
				echo $this->escape($ST->$item['name'])
           ?>
           </div>
        </div>
	<?php
	}
	}
	echo '</div>';
	echo '</label>
	</div>
	</div>'."\n";
	if($i == 1) $i++;
	elseif($i == 2) $i--;
}
?>
<div><div style="width: 100%; clear: both; text-align: center;">
<a href="<?php echo $new_address_link; ?>">
<?php  echo JText::_('COM_VIRTUEMART_USER_FORM_ADD_SHIPTO_LBL'); ?></a>
</div></div>

</div>
</div>