<?php
/**
 * product builder component
 * @package productbuilder
 * @version $Id: product/tmpl/form.php  2012-2-16 sakisTerz $
 * @author Sakis Terz(sakis@breakDesigns.net)
 * @copyright	Copyright (C) 2010-2012 breakDesigns.net. All rights reserved
 * @license	GNU/GPL v2
 */

defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task) {
		if (task == 'tag.cancel' || document.formvalidator.isValid(document.id('item-form'))) {			
			Joomla.submitform(task, document.getElementById('item-form'));
		} else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}
</script>

<form
	action="<?php echo JRoute::_('index.php?option=com_productbuilder&view=tagss&layout=edit&id='.(int) $this->item->id);?>"
	method="post" name="adminForm" id="item-form" class="form-validate">
	<fieldset class="adminform">
		<legend>
		<?php echo JText::_('COM_PRODUCTBUILDER_FIELDSET_BASICDETAILS');?>
		</legend>
		<ul class="adminformlist">

			<?php if($this->item->id){?>
			<li>
			<?php echo $this->form->getInput('id'); ?>
			</li>
			<?php } ?>

			<li><?php echo $this->form->getLabel('name'); ?> <?php echo $this->form->getInput('name'); ?></li>
			<li><?php echo $this->form->getLabel('published'); ?> <?php echo $this->form->getInput('published'); ?></li>
			
			<li><?php echo $this->form->getLabel('note'); ?> <?php echo $this->form->getInput('note'); ?></li>

		</ul>
	</fieldset>
	<div style="clear: both"></div>

	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
</form>

