<?php
/**
 * Property image options
 *
 * @package 	CSVI
 * @subpackage 	Import
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2012 RolandD Cyber Produksi
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: default_media_image.php 1924 2012-03-02 11:32:38Z RolandD $
 */

defined('_JEXEC') or die;
?>
<fieldset class="float31">
	<legend><?php echo JText::_('COM_CSVI_IMPORT_GENERAL_IMAGES'); ?></legend>
	<ul>
		<li><div class="option_label_image"><?php echo $this->form->getLabel('process_image', 'image'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('process_image', 'image'); ?></div></li>
		<li><div class="option_label_image"><?php echo $this->form->getLabel('change_case', 'image'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('change_case', 'image'); ?></div></li>
	</ul>
</fieldset>
<fieldset class="float30">
	<legend><?php echo JText::_('COM_CSVI_IMPORT_FULL_IMAGES'); ?></legend>
	<ul>
		<li><div class="option_label_image"><?php echo $this->form->getLabel('keep_original', 'image'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('keep_original', 'image'); ?></div></li>
		<li><div class="option_label_image"><?php echo $this->form->getLabel('convert_type', 'image'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('convert_type', 'image'); ?></div></li>
		<li><div class="option_label_image"><?php echo $this->form->getLabel('save_images_on_server', 'image'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('save_images_on_server', 'image'); ?></div></li>
		<li><div class="option_label_image"><?php echo $this->form->getLabel('redownload_external_image', 'image'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('redownload_external_image', 'image'); ?></div></li>
		<li><div class="option_label_image"><?php echo $this->form->getLabel('full_resize', 'image'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('full_resize', 'image'); ?></div></li>
		<li><div class="option_label_image"><?php echo $this->form->getLabel('full_width', 'image'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('full_width', 'image'); ?></div></li>
		<li><div class="option_label_image"><?php echo $this->form->getLabel('full_height', 'image'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('full_height', 'image'); ?></div></li>
		<li><div class="option_label_image"><?php echo $this->form->getLabel('full_watermark', 'image'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('full_watermark', 'image'); ?></div></li>
		<li><div class="option_label_image"><?php echo $this->form->getLabel('full_watermark_right', 'image'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('full_watermark_right', 'image'); ?></div></li>
		<li><div class="option_label_image"><?php echo $this->form->getLabel('full_watermark_bottom', 'image'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('full_watermark_bottom', 'image'); ?></div></li>
		<li><div class="option_label_image"><?php echo $this->form->getLabel('full_watermark_image', 'image'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('full_watermark_image', 'image'); ?></div></li>
	</ul>
</fieldset>
<fieldset class="float30">
	<legend><?php echo JText::_('COM_CSVI_IMPORT_THUMB_IMAGES'); ?></legend>
	<ul>
		<li><div class="option_label_image"><?php echo $this->form->getLabel('thumb_check_filetype', 'image'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('thumb_check_filetype', 'image'); ?></div></li>
		<li><div class="option_label_image"><?php echo $this->form->getLabel('thumb_create', 'image'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('thumb_create', 'image'); ?></div></li>
		<li><div class="option_label_image"><?php echo $this->form->getLabel('thumb_extension', 'image'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('thumb_extension', 'image'); ?></div></li>
		<li><div class="option_label_image"><?php echo $this->form->getLabel('thumb_width', 'image'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('thumb_width', 'image'); ?></div></li>
		<li><div class="option_label_image"><?php echo $this->form->getLabel('thumb_height', 'image'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('thumb_height', 'image'); ?></div></li>
		<li><div class="option_label_image"><?php echo $this->form->getLabel('resize_max_width', 'image'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('resize_max_width', 'image'); ?></div></li>
		<li><div class="option_label_image"><?php echo $this->form->getLabel('resize_max_height', 'image'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('resize_max_height', 'image'); ?></div></li>
	</ul>
</fieldset>
<div class="clr"/></div>
<script type="text/javascript">
jQuery(document).ready(function() {
	if (<?php echo $this->template->get('full_resize', 'image', '0'); ?> == '0') {
		jQuery('#jform_image_full_width, #jform_image_full_height').parent().parent().hide();
	}
	if (<?php echo $this->template->get('full_watermark', 'image', '0'); ?> == '0') {
		jQuery('#jform_image_full_watermark_right, #jform_image_full_watermark_bottom').parent().parent().hide();
		jQuery('#jform_image_full_watermark_image').parent().parent().parent().hide();
	}
	if (<?php echo $this->template->get('thumb_create', 'image', '0'); ?> == '0') {
		jQuery('#jform_image_thumb_extension, #jform_image_thumb_width, #jform_image_thumb_height').parent().parent().hide();
	}
});

// Add some behaviors
jQuery("#jform_image_full_resize").live('change', function() {
	jQuery('#jform_image_full_width, #jform_image_full_height').parent().parent().toggle();
});

jQuery("#jform_image_full_watermark").live('change', function() {
	jQuery('#jform_image_full_watermark_right, #jform_image_full_watermark_bottom').parent().parent().toggle();
	jQuery('#jform_image_full_watermark_image').parent().parent().parent().toggle();
})

jQuery("#jform_image_thumb_create").live('change', function() {
	jQuery('#jform_image_thumb_extension, #jform_image_thumb_width, #jform_image_thumb_height').parent().parent().toggle();
});
</script>
