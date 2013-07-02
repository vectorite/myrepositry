<?php
/**
 * Import product options
 *
 * @package 	CSVI
 * @subpackage 	Import
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2012 RolandD Cyber Produksi
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: default_product.php 2059 2012-08-04 13:36:46Z RolandD $
 */

defined( '_JEXEC' ) or die;
?>
<fieldset>
	<legend><?php echo JText::_('COM_CSVI_OPTIONS'); ?></legend>
	<ul>
		<li><div class="option_label"><?php echo $this->form->getLabel('language', 'general'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('language', 'general'); ?></div></li>
		<li><div class="option_label"><?php echo $this->form->getLabel('category_separator', 'general'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('category_separator', 'general'); ?></div></li>
		<li><div class="option_label"><?php echo $this->form->getLabel('append_categories', 'product'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('append_categories', 'product'); ?></div></li>
		<li><div class="option_label"><?php echo $this->form->getLabel('update_based_on', 'product'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('update_based_on', 'product'); ?></div></li>
		<li><div class="option_label"><?php echo $this->form->getLabel('mpn_column_name', 'product'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('mpn_column_name', 'product'); ?></div></li>
		<li><div class="option_label"><?php echo $this->form->getLabel('unpublish_before_import', 'product'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('unpublish_before_import', 'product'); ?></div></li>
		<li><div class="option_label"><?php echo $this->form->getLabel('update_stockable_parent', 'product'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('update_stockable_parent', 'product'); ?></div></li>
	</ul>
</fieldset>
<fieldset>
	<legend><?php echo JText::_('COM_CSVI_SETTINGS_ICECAT_SETTINGS'); ?></legend>
	<ul>
		<li><div class="option_label"><?php echo $this->form->getLabel('use_icecat', 'product'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('use_icecat', 'product'); ?></div></li>
		<li><div class="option_label"><?php echo $this->form->getLabel('similar_sku', 'product'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('similar_sku', 'product'); ?></div></li>
		<li><div class="option_label"><?php echo $this->form->getLabel('icecat_update_fields', 'product'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('icecat_update_fields', 'product'); ?></div></li>
	</ul>
</fieldset>
<div class="clr"></div>
<script type="text/javascript">
jQuery(document).ready(function() {
	if ('<?php echo $this->template->get('update_based_on', 'product', 'product_sku'); ?>' != 'product_mpn') {
		jQuery('#jform_product_mpn_column_name').parent().parent().hide();
	}
});
jQuery('#jform_product_update_based_on').live('change', function() {
	if (jQuery(this).val() == 'product_mpn') {
		jQuery('#jform_product_mpn_column_name').parent().parent().show();
	}
	else jQuery('#jform_product_mpn_column_name').parent().parent().hide();
});
</script>
