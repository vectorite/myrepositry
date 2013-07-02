<?php
/**
 * @version 2.0.1 2012-08-17
 * @package Joomla
 * @subpackage Work Force
 * @copyright (C) 2012 the Thinkery
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.modal');
JHtml::_('behavior.formvalidation');
?>

<script language="javascript" type="text/javascript">
    Joomla.submitbutton = function(task)
	{
		if (task == 'department.cancel' || document.formvalidator.isValid(document.id('workforce-form'))) {
			<?php echo $this->form->getField('desc')->save(); ?>
            Joomla.submitform(task, document.getElementById('workforce-form'));
		} else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_workforce&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="workforce-form" class="form-validate">
    <div class="width-60 fltlft">
		<fieldset class="adminform">
			<legend><?php echo JText::_('COM_WORKFORCE_DETAILS'); ?></legend>
			<ul class="adminformlist">
                <li><?php echo $this->form->getLabel('name'); ?>
				<?php echo $this->form->getInput('name'); ?></li>
                <li><?php echo $this->form->getLabel('ordering'); ?>
				<?php echo $this->form->getInput('ordering'); ?></li>
            </ul>
            <div class="clr"></div>
            <?php echo $this->form->getLabel('wfhead1'); ?>
            <div class="clr"></div>
            <?php echo $this->form->getInput('desc'); ?>
        </fieldset>
    </div>
    <div class="width-40 fltrt">
        <?php echo JHtml::_('sliders.start','banner-sliders-'.$this->item->id, array('useCookie'=>1)); ?>
        <?php echo JHtml::_('sliders.panel',JText::_('COM_WORKFORCE_PUBLISHING'), 'publishing-details'); ?>
            <fieldset class="panelform">
                <ul class="adminformlist">
                    <li><?php echo $this->form->getLabel('state'); ?>
                    <?php echo $this->form->getInput('state'); ?></li>
                </ul>
            </fieldset>
        <?php echo JHtml::_('sliders.panel',JText::_('COM_WORKFORCE_IMAGE'), 'image-details'); ?>
            <fieldset class="panelform">
                <ul class="adminformlist">
                    <li><?php echo $this->form->getInput('icon'); ?></li>
                </ul>
            </fieldset>
        <?php echo JHtml::_('sliders.end'); ?>
    </div>
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
    <div class="clr"></div>
</form>
<p class="copyright"><?php echo workforceAdmin::footer( ); ?></p>