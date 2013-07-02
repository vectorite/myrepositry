<?php
/**
 * @package		com_contactenhanced
* @copyright	Copyright (C) 2006 - 2010 Ideal Custom Software Development
 * @author     Douglas Machado {@link http://ideal.fok.com.br}
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT.DS.'helpers'.DS.'html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
$document = &JFactory::getDocument();


?>
<script type="text/javascript">
<!--
	function submitbutton(task)
	{
		if (task == 'customfield.cancel' || document.formvalidator.isValid(document.id('contact-form'))) {
		}
		// @todo Deal with the editor methods
		submitform(task);
	}
// -->
</script>

<form action="<?php JRoute::_('index.php?option=com_contactenhanced'); ?>" method="post" name="adminForm" id="contact-form" class="form-validate">
	<div class="width-60 fltlft">

		<fieldset class="adminform">
			<ul class="adminformlist">
				<li><?php echo $this->form->getLabel('name'); ?>
				<?php echo $this->form->getInput('name'); ?></li>
	
				<li><?php echo $this->form->getLabel('type'); ?>
				<?php echo $this->form->getInput('type'); ?></li>
			</ul>
			<div class="clr"></div>
			<?php echo $this->form->getLabel('html'); ?>
			<div class="clr"></div>
			<?php echo $this->form->getInput('html'); ?>
		</fieldset>
		
	</div>

	<div class="width-40 fltrt">
		<?php echo  JHtml::_('sliders.start', 'ce-cf-more-slider'); ?>
			<?php echo JHtml::_('sliders.panel',JText::_('CE_CF_PUBLISHING_INFO'), 'publishing-options'); ?>
			<fieldset class="panelform">
				<ul class="adminformlist">
					<li><?php echo $this->form->getLabel('published'); ?>
					<?php echo $this->form->getInput('published'); ?></li>
		
					<li><?php echo $this->form->getLabel('language'); ?>
					<?php echo $this->form->getInput('language'); ?></li>
					
					<li><?php echo $this->form->getLabel('access'); ?>
					<?php echo $this->form->getInput('access'); ?></li>
					
					<li><?php echo $this->form->getLabel('id'); ?>
					<?php echo $this->form->getInput('id'); ?></li>
				</ul>
			</fieldset>

			<?php echo $this->loadTemplate('params'); ?>

		<?php echo JHtml::_('sliders.end'); ?>
	</div>

	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
</form>