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

	/**
	 * This allows us to print the user fields on
	 * the various sections of the shop
	 *
	 * @param array $rowFields An array returned from ps_database::loadObjectlist
	 * @param array $skipFields A one-dimensional array holding the names of fields that should NOT be displayed
	 * @param ps_DB $db A ps_DB object holding ovalues for the fields
	 * @param boolean $startform If true, print the starting <form...> tag
	 * 
	 * content of this file is a modification of function listUserFields( $rowFields, $skipFields=array(), $db = null, $startForm = true ) 
	 * of Virtuemart 1.1.7
	 */
	 
?>	 
<script src='<?php echo JURI::root();?>modules/mod_userregistration/jquery.maskedinput-1.3.js' ></script>
<script>

jQuery(function($){   
$("#phone_1_field").mask("999-999-9999");
$("#phone_2_field").mask("999-999-9999");
$("#fax_field").mask("999-999-9999");
});

</script>
<?php
	{
		
		
		global $VM_LANG;
		$missing = '';

		// collect all required fields
		$required_fields = Array(); 
		echo '<div style="width:100%;">';
				
		
		$delimiter = 0;
	   	foreach( $rowFields['fields'] as $field) {
			if (empty($field['type'])) continue;
			if (empty($fied['readonly'])) $field['readonly'] = false;
		    $readonly = $field['readonly'] ? ' readonly="readonly"' : '';
	   		// Title handling.
	   		$key = $field['title'];
	   		if( $field['name'] == 'agreed') {
				continue;
	   			
	   		}
	   		if (( $field['name'] == 'username' && VM_REGISTRATION_TYPE == 'OPTIONAL_REGISTRATION' ) || (( $field['name'] == 'opc_password' && VM_REGISTRATION_TYPE == 'OPTIONAL_REGISTRATION' && (!empty($op_usernameisemail)) ))) {
				echo '<div class="formLabel">
						<input type="checkbox" autocomplete="off" id="register_account" name="register_account" value="1" class="inputbox" onchange="showFields( this.checked, new Array('; 
						if (empty($op_usernameisemail))
						echo '\'username\', \'password\', \'password2\''; 
						else echo '\'password\', \'password2\''; 
					echo ') );" '; 
					if (empty($op_create_account_unchecked)) echo ' checked="checked" '; 
					echo '/>
					</div>
					<div class="formField">
						<label for="register_account">'.JText::_('COM_VIRTUEMART_ORDER_REGISTER').'</label>
					</div>
					';
			} elseif( $field['name'] == 'username' ) {
			    
				echo '<input type="hidden" id="register_account" name="register_account" value="1" />';
			}
	   		// a delimiter marks the beginning of a new fieldset and
	   		// the end of a previous fieldset
			
	   		if( $field['type'] == 'delimiter') {
	   			if( $delimiter > 0) {
	   				echo "</fieldset>\n";
	   			}
	   			if( VM_REGISTRATION_TYPE == 'SILENT_REGISTRATION' && $field['title'] == $VM_LANG->_('PHPSHOP_ORDER_PRINT_CUST_INFO_LBL') && $page == 'checkout.index' ) {
	   				continue;
	   			}
	   			echo '<fieldset>
				     <legend class="sectiontableheader">'.$field['title'].'</legend>
';
	   			$delimiter++;
	   			continue;
	   		}
	   		
	   		echo '<div id="'.$field['name'].'_div" class="formLabel ';
	   		if (stristr($missing,$field['name'])) {
	   			echo 'missing';
	   		}
	   		echo '">';
	        echo '<label for="'.$field['name'].'_field">'.$field['title'].'</label>';
	        if( !empty( $field['required'] )) {
	        	echo '<strong>* </strong>';
	        }
	      	echo ' </div>
	      <div class="formField" id="'.$field['name'].'_input">'."\n";
	      	
	      	/**
	      	 * This is the most important part of this file
	      	 * Here we print the field & its contents!
	      	 */
			 
	   		switch( $field['name'] ) {
	   			case 'title':
	   				//$ps_html->list_user_title($db->sf('title', true, false), "id=\"title_field\"");
					echo $field['formcode'];
	   				break;
	   			
	   			case 'virtuemart_country_id':
	   				/*
					if( in_array('state', $allfields ) ) {
	   					$onchange = "onchange=\"changeStateList();\"";
	   				}
	   				else {
	   					$onchange = "";
	   				}
					*/
	   				//$ps_html->list_country("country", $db->sf('country', true), "id=\"country_field\" $onchange style=\"width: 215px;\"");
					echo $field['formcode'];
	   				break;
	   			
	   			case 'virtuemart_state_id':
	   				//echo $ps_html->dynamic_state_lists( "country", "state", $db->sf('country', true), $db->sf('state', true, false) );
					echo $bodytag = str_replace('<select ', '<select aria-required="true" required="required" class="virtuemart_state_id required" ', $field['formcode']);
					//echo $field['formcode'];
					
					//echo "<select ".$pieces[0]; 
				    //echo "<noscript>\n";
				    //$ps_html->list_states("state", $db->sf('state', true, false), "", "id=\"state_field\"");
				    //echo "</noscript>\n";
	   				break;
				case 'agreed':
					//echo '<input type="checkbox" id="agreed_field" name="agreed" value="1" class="inputbox" />';
					echo $field['formcode'];
					break;
				case 'password':
				case 'password2':
					
					echo $field['formcode']; 
		   			break;
					
	   			default:
					echo $field['formcode']; 
					break;
	   				
	   		}
	   		echo '</div>';
	   }
		if( $delimiter > 0) {
		
		if( !empty( $required_fields ))  {
			echo '<div style="padding:5px;text-align:center;"><strong>(* = '.$VM_LANG->_('CMN_REQUIRED').')</strong></div>';
		  	 
		}
			echo "</fieldset>\n";
		}
	   
	   if( VM_REGISTRATION_TYPE == 'OPTIONAL_REGISTRATION') {
		   	echo '<script type="text/javascript">
		   	//<![CDATA[
		   function showFields( show, fields ) {
		   	if( fields ) {
		   		for (i=0; i<fields.length;i++) {
		   			if( show ) {
		   				document.getElementById( fields[i] + \'_div\' ).style.display = \'\';
		   				document.getElementById( fields[i] + \'_input\' ).style.display = \'\';
		   			} else {
		   				document.getElementById( fields[i] + \'_div\' ).style.display = \'none\';
		   				document.getElementById( fields[i] + \'_input\' ).style.display = \'none\';
		   			}
		   		}
		   	}
		   }
		   try {
		   	showFields( document.getElementById( \'register_account\').checked, new Array(\'username\', \'password\', \'password2\') );
		   } catch(e){}
		   //]]>
		   </script>';
	   }
	   	   echo '</div>';

	}


?>