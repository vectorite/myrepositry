<?php
/**
 * General export options
 *
 * @package 	CSVI
 * @subpackage 	Import
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2012 RolandD Cyber Produksi
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: default_file.php 1924 2012-03-02 11:32:38Z RolandD $
 */

defined('_JEXEC') or die;
?>
<!-- Some default values -->
<input type="hidden" name="jform[general][export_file]" value="xml" />
<input type="hidden" name="jform[general][export_site]" value="orderadvanced" />
<fieldset class="float30">
	<legend><?php echo JText::_('COM_CSVI_IMPORT_FILE_OPTIONS'); ?></legend>
	<ul>
		<li><div class="option_label"><?php echo $this->form->getLabel('export_filename', 'general'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('export_filename', 'general'); ?></div></li>
		<li><div class="option_label"><?php echo $this->form->getLabel('combine_char', 'general'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('combine_char', 'general'); ?></div></li>
		<li><div class="option_label"><?php echo $this->form->getLabel('export_frontend', 'general'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('export_frontend', 'general'); ?></div></li>
		<li><div class="option_label"><?php echo $this->form->getLabel('collect_debug_info', 'general'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('collect_debug_info', 'general'); ?></div></li>
	</ul>
</fieldset>
<fieldset class="float30">
	<legend><?php echo JText::_('COM_CSVI_EXPORT_FILTER_OPTIONS'); ?></legend>
	<ul>
		<li><div class="option_label"><?php echo $this->form->getLabel('publish_state', 'general'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('publish_state', 'general'); ?></div></li>
		<li><div class="option_label"><?php echo $this->form->getLabel('recordstart', 'general'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('recordstart', 'general'); ?> <?php echo $this->form->getInput('recordend', 'general'); ?></div></li>
		<li><div class="option_label"><?php echo $this->form->getLabel('groupby', 'general'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('groupby', 'general'); ?></div></li>
	</ul>
</fieldset>
<fieldset class="float30">
	<legend><?php echo JText::_('COM_CSVI_EXPORT_FORMAT_OPTIONS'); ?></legend>
	<ul>
		<li><div class="option_label"><?php echo $this->form->getLabel('export_date_format', 'general'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('export_date_format', 'general'); ?></div></li>
		<li><label class="hasTip" id="subtitle" title="<?php echo JText::_('COM_CSVI_EXPORT_PRICE_FORMAT_LABEL'); ?> :: <?php echo JText::_('COM_CSVI_EXPORT_PRICE_FORMAT_DESC'); ?>"><?php echo JText::_('COM_CSVI_EXPORT_PRICE_FORMAT_LABEL'); ?></label><br /></li>
		<li><div class="option_label"><?php echo $this->form->getLabel('export_price_format_decimal', 'general'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('export_price_format_decimal', 'general'); ?></div></li>
		<li><div class="option_label"><?php echo $this->form->getLabel('export_price_format_decsep', 'general'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('export_price_format_decsep', 'general'); ?></div></li>
		<li><div class="option_label"><?php echo $this->form->getLabel('export_price_format_thousep', 'general'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('export_price_format_thousep', 'general'); ?></div></li>
		<li><div class="option_label"><?php echo $this->form->getLabel('add_currency_to_price', 'general'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('add_currency_to_price', 'general'); ?></div></li>
	</ul>
</fieldset>
<div class="clr"></div>
