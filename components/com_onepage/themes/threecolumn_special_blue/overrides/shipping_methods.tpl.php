<?php if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );
/*
*
* @copyright Copyright (C) 2007 - 2011 RuposTel - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* One Page checkout is free software released under GNU/GPL and uses code from VirtueMart
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* 
* THIS FILE WILL LIST FOUND SHIPPING METHODS WITH RADIO BOXES IN STYLE OF STANDARD SHIPPING WITH A FEW ADDITIONS
* 
* INPUT PARAMETER: $shipping_methods_array, $selected_idth
*
*     $new['value'] = 'choose_shipping| -- '.$selectl.' -- | -- '.$selectl.' --|0|';
*    $new['shipping_method_id'] = 'choose_shipping_99999';
*    $new['id']='99999';
*    $new['name']= ' -- '.$selectl.' -- ';
*    $new['price'] = '';
*    $new['tax_rate'] = "0";
*    $new['rate'] = '0';
* 	 // for javascript compatibility we did:
*	 $new['idth'] = 'hash'.$new['id'];
*/

mm_showMyFileName(__FILE__); 
global $VM_LANG;

// header
$html = '';

foreach ($shipping_methods_array as $ship)
{

          				$html .= "<input type=\"radio\" id=\"".$ship['idth']."\"  name=\"shipping_rate_id\" value=\"" . urlencode($ship['value']) . "\" onclick=\"javascript:changeTextOnePage3(op_textinclship, op_currency, op_ordertotal);\"" ;
				
				if ($ship['idth'] == $selected_idth)
					$html .= "checked=\"checked\"" ;
				$html .= " />";
				
				$html .= "<label for=\"".$ship['idth']. "\">" . $ship['name'] .'&nbsp   '. $ship['price']. "</label><br style=\"clear: both;\" />" ;
				//$html .= "<td>" . $ship['price'] . "</td></tr>\n" ;
			
		
}
// end of table

echo $html;
?>