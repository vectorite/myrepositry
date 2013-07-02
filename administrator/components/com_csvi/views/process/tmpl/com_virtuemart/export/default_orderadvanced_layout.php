<?php
/**
 * Export file layout options
 *
 * @package 	CSVI
 * @subpackage 	Export
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2012 RolandD Cyber Produksi
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: default_layout.php 1924 2012-03-02 11:32:38Z RolandD $
 */

defined('_JEXEC') or die;
?>
<fieldset>
	<legend><?php echo JText::_('COM_CSVI_EXPORT_LAYOUT_OPTIONS'); ?></legend>
	<ul>
		<li><div class="option_label"><?php echo $this->form->getLabel('header', 'layout'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('header', 'layout'); ?></div></li>
		<li><div class="option_label"><?php echo $this->form->getLabel('order', 'layout'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('order', 'layout'); ?></div></li>
		<li><div class="option_label"><?php echo $this->form->getLabel('orderline', 'layout'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('orderline', 'layout'); ?></div></li>
		<li><div class="option_label"><?php echo $this->form->getLabel('footer', 'layout'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('footer', 'layout'); ?></div></li>
	</ul>
</fieldset>
<div class="clr"></div>