<?php
/**
 * Export gift certificates
 *
 * @package 	CSVI
 * @subpackage 	Export
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2012 RolandD Cyber Produksi
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: default_coupon.php 1925 2012-03-02 11:51:51Z RolandD $
 */

defined( '_JEXEC' ) or die;
?>
<fieldset>
	<legend><?php echo JText::_('COM_CSVI_OPTIONS'); ?></legend>
	<ul>
		<li><div class="option_label"><?php echo $this->form->getLabel('product_sku', 'giftcertificate'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('product_sku', 'giftcertificate'); ?></div></li>
		<li><div class="option_label"><?php echo $this->form->getLabel('template', 'giftcertificate'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('template', 'giftcertificate'); ?></div></li>
		<li><div class="option_label"><?php echo $this->form->getLabel('profile', 'giftcertificate'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('profile', 'giftcertificate'); ?></div></li>
	</ul>
</fieldset>
<div class="clr"></div>