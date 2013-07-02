<?php 
if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );
/**
*
* @version $Id:couponField.tpl.php 431 2006-10-17 21:55:46 +0200 (Di, 17 Okt 2006) soeren_nb $
* @package VirtueMart
* @subpackage themes
* @copyright Copyright (C) 2008 soeren - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See /administrator/components/com_virtuemart/COPYRIGHT.php for copyright notices and details.
* @author Erich Vinson
* http://virtuemart.net
*/



?>

<div class="coupon_section">
<?php

// If you have a coupon code, please enter it here:
echo $this->coupon_text . '<br />';
?>  
	    <form action="<?php echo JRoute::_('index.php'); ?>" method="post" onsubmit="return checkCouponField(this);">
			<div class="coupon_input_section">
			 <div class="before_input"></div><div class="middle_input">
			 <input type="text" name="coupon_code" autocomplete="off" id="coupon_code" maxlength="30" class="inputbox" />
			 <div class="after_input">&nbsp;</div></div>
			</div>
			<input type="hidden" name="Itemid" value="<?php echo @intval($_REQUEST['Itemid'])?>" />
			 <input type="hidden" name="option" value="com_virtuemart" />
    <input type="hidden" name="view" value="cart" />
    <input type="hidden" name="task" value="setcoupon" />
    <input type="hidden" name="controller" value="cart" />
			<input type="submit" value="<?php echo JText::_('COM_VIRTUEMART_SAVE'); ?>" class="coupon_button" />
		</form>		
<script type="text/javascript">
function checkCouponField(form) {
	if(form.coupon_code.value == '') {
		new Effect.Highlight('coupon_code');
		return false;
	}
	return true;
}
</script>
</div>
<br style="clear: both;" />