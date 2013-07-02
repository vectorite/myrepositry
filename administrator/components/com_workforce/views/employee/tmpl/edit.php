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
		if (task == 'employee.cancel' || document.formvalidator.isValid(document.id('workforce-form'))) {
			<?php echo $this->form->getField('bio')->save(); ?>
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
            <div class="width-50 fltlft">                
                <ul class="adminformlist">
                    <li><?php echo $this->form->getLabel('fname'); ?>
                    <?php echo $this->form->getInput('fname'); ?></li>
                    <li><?php echo $this->form->getLabel('lname'); ?>
                    <?php echo $this->form->getInput('lname'); ?></li>
                    <li><?php echo $this->form->getLabel('department'); ?>
                    <?php echo $this->form->getInput('department'); ?></li>
                    <li><?php echo $this->form->getLabel('position'); ?>
                    <?php echo $this->form->getInput('position'); ?></li>
                    <li><?php echo $this->form->getLabel('email'); ?>
                    <?php echo $this->form->getInput('email'); ?></li>
                    <li><?php echo $this->form->getLabel('phone1'); ?>
                    <?php echo $this->form->getInput('phone1'); ?></li>
                    <li><?php echo $this->form->getLabel('ext1'); ?>
                    <?php echo $this->form->getInput('ext1'); ?></li>
                    <li><?php echo $this->form->getLabel('phone2'); ?>
                    <?php echo $this->form->getInput('phone2'); ?></li>
                    <li><?php echo $this->form->getLabel('ext2'); ?>
                    <?php echo $this->form->getInput('ext2'); ?></li>
                    <li><?php echo $this->form->getLabel('fax'); ?>
                    <?php echo $this->form->getInput('fax'); ?></li>
                </ul>
            </div>
            <div class="width-50 fltrt">
                <ul class="adminformlist">
                    <li><?php echo $this->form->getLabel('street'); ?>
                    <?php echo $this->form->getInput('street'); ?></li>
                    <li><?php echo $this->form->getLabel('street2'); ?>
                    <?php echo $this->form->getInput('street2'); ?></li>
                    <li><?php echo $this->form->getLabel('city'); ?>
                    <?php echo $this->form->getInput('city'); ?></li>
                    <li><?php echo $this->form->getLabel('locstate'); ?>
                    <?php echo $this->form->getInput('locstate'); ?></li>
                    <li><?php echo $this->form->getLabel('or'); ?></li>
                    <li><?php echo $this->form->getLabel('province'); ?>
                    <?php echo $this->form->getInput('province'); ?></li>
                    <li><?php echo $this->form->getLabel('postcode'); ?>
                    <?php echo $this->form->getInput('postcode'); ?></li>
                    <li><?php echo $this->form->getLabel('country'); ?>
                    <?php echo $this->form->getInput('country'); ?></li>
                    <li><?php echo $this->form->getLabel('website'); ?>
                    <?php echo $this->form->getInput('website'); ?></li>
                </ul>
            </div>
            <div class="clr"></div>
            <?php echo $this->form->getLabel('wfhead1'); ?>
            <div class="clr"></div>
            <?php echo $this->form->getInput('bio'); ?>
        </fieldset>
    </div>
    <div class="width-40 fltrt">
        <?php echo JHtml::_('sliders.start','banner-sliders-'.$this->item->id, array('useCookie'=>1)); ?>
        <?php echo JHtml::_('sliders.panel',JText::_('COM_WORKFORCE_PUBLISHING'), 'publishing-details'); ?>
            <fieldset class="panelform">
                <ul class="adminformlist">
                    <li><?php echo $this->form->getLabel('state'); ?>
                    <?php echo $this->form->getInput('state'); ?></li>
                    <li><?php echo $this->form->getLabel('featured'); ?>
                    <?php echo $this->form->getInput('featured'); ?></li>
                    <li><?php echo $this->form->getLabel('user_id'); ?>
                    <?php echo $this->form->getInput('user_id'); ?></li>
                </ul>
            </fieldset>
        <?php echo JHtml::_('sliders.panel',JText::_('COM_WORKFORCE_SOCIAL'), 'social-details'); ?>
            <fieldset class="panelform">
                <ul class="adminformlist">
                    <li><?php echo $this->form->getLabel('twitter'); ?>
                    <?php echo $this->form->getInput('twitter'); ?></li>
                    <li><?php echo $this->form->getLabel('facebook'); ?>
                    <?php echo $this->form->getInput('facebook'); ?></li>
                    <li><?php echo $this->form->getLabel('youtube'); ?>
                    <?php echo $this->form->getInput('youtube'); ?></li>
                    <li><?php echo $this->form->getLabel('linkedin'); ?>
                    <?php echo $this->form->getInput('linkedin'); ?></li>
                </ul>
            </fieldset>
        <?php echo JHtml::_('sliders.panel',JText::_('COM_WORKFORCE_IMAGE'), 'image-details'); ?>
            <fieldset class="panelform">
                <ul class="adminformlist">
                    <li><?php echo $this->form->getInput('icon'); ?></li>
                </ul>
            </fieldset>
        <?php echo JHtml::_('sliders.panel',JText::_('COM_WORKFORCE_AVAILABILITY'), 'availability-details'); ?>
            <fieldset class="panelform">
                <ul class="adminformlist">
                    <li><?php echo $this->form->getLabel('wfhead2'); ?></li>
                    <li><?php echo $this->form->getInput('availability'); ?></li>
                </ul>
            </fieldset>
        <?php echo JHtml::_('sliders.end'); ?>
    </div>
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
    <div class="clr"></div>
</form>
<p class="copyright"><?php echo workforceAdmin::footer( ); ?></p>