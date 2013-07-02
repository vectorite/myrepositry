<?php
/**
* product builder component
* @package productbuilder
* @version $Id:2 product/tmpl/edit.php  2012-2-3 sakisTerz $
* @author Sakis Terzis(sakis@breakDesigns.net)
* @copyright	Copyright (C) 2010-2012 breakDesigns.net. All rights reserved
* @license	GNU/GPL v3
*/


defined('_JEXEC') or die('Restricted access');
// Load the tooltip behavior.
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task) {
		if (task == 'product.cancel' || document.formvalidator.isValid(document.id('item-form'))) {
			<?php echo $this->form->getField('description')->save(); ?>
			Joomla.submitform(task, document.getElementById('item-form'));
		} else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}
</script>


<form action="<?php echo JRoute::_('index.php?option=com_productbuilder&view=products&layout=edit&id='.(int) $this->item->id);?>" method="post" name="adminForm" id="item-form" class="form-validate" enctype="multipart/form-data">
	<div class="width-60 fltlft">
		<fieldset class="adminform">
				<legend><?php echo JText::_('COM_PRODUCTBUILDER_FIELDSET_BASICDETAILS');?></legend>
				<ul class="adminformlist">
								
				<li><?php echo $this->form->getLabel('name'); ?>
				<?php echo $this->form->getInput('name'); ?></li>
				
				<li><?php echo $this->form->getLabel('alias'); ?>
				<?php echo $this->form->getInput('alias'); ?></li>
				
				<li><?php echo $this->form->getLabel('sku'); ?>
				<?php echo $this->form->getInput('sku'); ?></li>				

				<li><?php echo $this->form->getLabel('compatibility'); ?>
				<?php echo $this->form->getInput('compatibility'); ?></li>

				<li><?php echo $this->form->getLabel('published'); ?>
				<?php echo $this->form->getInput('published'); ?></li>
				
				<li><?php echo $this->form->getLabel('ordering'); ?>
				<?php echo $this->form->getInput('ordering'); ?></li>
				
				
				
				<li><?php echo $this->form->getLabel('image_path'); ?>
				<?php echo $this->form->getInput('image_path'); ?></li>
		
				<?php if(isset($this->item->image_path) && JFile::exists(JPATH_ROOT.DIRECTORY_SEPARATOR.$this->item->image_path)){?>
				<li>
				<div class="clr"></div> 
				<img class="bund_img" src="<?php echo JURI::root().$this->item->image_path?>" />
				</li>				
				<?php } ?>	
				
				<li><?php echo $this->form->getLabel('language'); ?>
				<?php echo $this->form->getInput('language'); ?></li>
				
				<?php if($this->item->id){?>
				<li><?php echo $this->form->getLabel('id'); ?>
				<?php echo $this->form->getInput('id'); ?></li>
				<?php } ?>
			
			<li><div class="clr"></div> 
			<?php echo $this->form->getLabel('description'); ?></li>
			<li><div class="clr"></div> <?php echo $this->form->getInput('description'); ?></li>

			</ul>  
		</fieldset>
	</div>
	
		<div class="width-40 fltrt">
			<?php echo JHtml::_('sliders.start','products-sliders-'.$this->item->id, array('useCookie'=>1)); ?>
			<?php echo JHtml::_('sliders.panel',JText::_('COM_PRODUCTBUILDER_BUNDLE_METADATA'), 'image-options'); ?>
			<fieldset class="panelform">
			<?php echo $this->form->getLabel('metaKeywords'); ?><br/>
			<?php echo $this->form->getInput('metaKeywords'); ?><br/>
			<div class="clr"></div> 
			<?php echo $this->form->getLabel('metaDecr'); ?><br/>
			<?php echo $this->form->getInput('metaDecr'); ?><br/>
			</fieldset>   
			<?php echo JHtml::_('sliders.end'); ?>        
		</div>
<div class="clr"></div> 
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
</form>