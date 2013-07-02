<?php
/**
* product builder component
* @package productbuilder
* @version $Id:1 config/tmpl/edit.php  2012-2-20 sakisTerz $
* @author Sakis Terz(sakis@breakDesigns.net)
* @copyright	Copyright (C) 2010-2012 breakDesigns.net. All rights reserved
* @license	GNU/GPL v2
*/

defined('_JEXEC') or die('Restricted access');
// Load the tooltip behavior.
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
JHTML::script ('jscolor.js','administrator/components/com_productbuilder/assets/js/');
JHTML::script ('config.js','administrator/components/com_productbuilder/assets/js/');
?>
<script type="text/javascript">
Joomla.submitbutton = function(task){

	var form=document.adminForm;
	var colorsArray=new Array(
			$('jform_groups_area_bckgr').value,
			$('jform_group_bckgr').value,
			$('jform_group_border_color').value,
			$('jform_group_border_radius').value,
			$('jform_gr_header_bckgr').value,
			$('jform_gr_header_font_color').value,
			$('jform_gr_header_font_size').value,
			$('jform_gr_header_text_shadow').value,
			$('jform_attr_font_color').value);


	
    if (task == 'group.cancel') {
		Joomla.submitform(task, document.getElementById('item-form'));
	}  

	for(var i=0; i<colorsArray.length; i++)

    if (colorsArray[i].match('/[0-9a-f]/gi')){
		alert('<?php echo JText::_("COM_PRODUCTBUILDER_INVALID_COLOR_FOUND");?>:colorsArray[i]');
	}    
	else {
		Joomla.submitform(task, document.getElementById('item-form'));
	}
}

</script>
<form action="<?php echo JRoute::_('index.php?option=com_productbuilder&view=config&layout=edit&id=1');?>" method="post" name="adminForm" id="item-form" class="form-validate">
	<div class="width-100 fltlft">
		<fieldset class="adminform">
				<legend><?php echo JText::_('COM_PRODUCTBUILDER_FIELDSET_BASICSETTINGS');?></legend>
				<ul class="adminformlist">
								
				
				<li><?php echo $this->form->getLabel('compatibility'); ?>
				<?php echo $this->form->getInput('compatibility'); ?></li>

				<li><?php echo $this->form->getLabel('disp_price'); ?>
				<?php echo $this->form->getInput('disp_price'); ?></li>

				<li><?php echo $this->form->getLabel('name_price_sep'); ?>
				<?php echo $this->form->getInput('name_price_sep'); ?></li>				
			</ul>
		</fieldset>

		<fieldset class="adminform">
			<legend><?php echo JText::_('COM_PRODUCTBUILDER_FIELDSET_STYLING');?></legend>
			
			<ul class="adminformlist">				
				<li><?php echo $this->form->getLabel('groups_area_bckgr'); ?>
				<?php echo $this->form->getInput('groups_area_bckgr'); ?></li>

				<li><?php echo $this->form->getLabel('group_bckgr'); ?>
				<?php echo $this->form->getInput('group_bckgr'); ?></li>

				<li><?php echo $this->form->getLabel('group_border_color'); ?>
				<?php echo $this->form->getInput('group_border_color'); ?></li>	
				
				<li>
				<?php echo $this->form->getLabel('group_border_radius'); ?>
				<div id="slider" class="slider">
					<div class="knob_border_radius"></div>
				</div>
				
				<?php echo $this->form->getInput('group_border_radius'); ?>
				
				
				</li>	
				
				<li><?php echo $this->form->getLabel('gr_header_bckgr'); ?>
				<?php echo $this->form->getInput('gr_header_bckgr'); ?></li>	
				
				<li><?php echo $this->form->getLabel('gr_header_font_color'); ?>
				<?php echo $this->form->getInput('gr_header_font_color'); ?></li>	
				
				<li><?php echo $this->form->getLabel('gr_header_font_size'); ?>
				<?php echo $this->form->getInput('gr_header_font_size'); ?></li>	
				
				<li><?php echo $this->form->getLabel('gr_header_text_shadow'); ?>
				<?php echo $this->form->getInput('gr_header_text_shadow'); ?></li>	
				
				<li><?php echo $this->form->getLabel('attr_font_color'); ?>
				<?php echo $this->form->getInput('attr_font_color'); ?></li>
				
				<li><?php echo $this->form->getLabel('img_border_color'); ?>
				<?php echo $this->form->getInput('img_border_color'); ?></li>				
			</ul>
		</fieldset>
	</div>		
<div class="clr"></div> 
	<?php echo $this->form->getInput('id'); ?>
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
</form>
