<?php
/**
 * Export property
 *
 * @package 	CSVI
 * @subpackage 	Export
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2012 RolandD Cyber Produksi
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: default_coupon.php 1925 2012-03-02 11:51:51Z RolandD $
 */

defined('_JEXEC') or die;
?>
<fieldset>
	<legend><?php echo JText::_('COM_CSVI_OPTIONS'); ?></legend>
	<ul>
		<li><div class="option_label"><?php echo $this->form->getLabel('transaction_type', 'property'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('transaction_type', 'property'); ?></div></li>
		<li><div class="option_label"><?php echo $this->form->getLabel('property_type', 'property'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('property_type', 'property'); ?></div></li>
		<li><div class="option_label"><?php echo $this->form->getLabel('street', 'property'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('street', 'property'); ?></div></li>
		<li><div class="option_label"><?php echo $this->form->getLabel('locality', 'property'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('locality', 'property'); ?></div></li>
		<li><div class="option_label"><?php echo $this->form->getLabel('state', 'property'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('state', 'property'); ?></div></li>
		<li><div class="option_label"><?php echo $this->form->getLabel('country', 'property'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('country', 'property'); ?></div></li>
		<li><div class="option_label"><?php echo $this->form->getLabel('owner', 'property'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('owner', 'property'); ?></div></li>
	</ul>
</fieldset>
<div class="clr"></div>