<?php
/**
 * @version 2.0.1 2012-08-17
 * @package Joomla
 * @subpackage Work Force
 * @copyright (C) 2012 the Thinkery
 * @license GNU/GPL see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');

// Create shortcut to parameters.
$params = $this->state->get('params');
$this->form->setFieldAttribute('wfhead1', 'color', $params->get('accent', '#777'));
$this->form->setFieldAttribute('wfhead1', 'tcolor', $params->get('secondary_accent', '#f7f7f7'));
$this->form->setFieldAttribute('wfhead2', 'color', $params->get('accent', '#777'));
$this->form->setFieldAttribute('wfhead2', 'tcolor', $params->get('secondary_accent', '#f7f7f7'));
?>

<script type="text/javascript">
	Joomla.submitbutton = function(task) {
		if (task == 'employeeform.cancel'){
            <?php echo $this->form->getField('bio')->save(); ?>
			Joomla.submitform(task);
        }else if(document.formvalidator.isValid(document.id('adminForm'))) {
			<?php echo $this->form->getField('bio')->save(); ?>
			Joomla.submitform(task);
		} else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}
</script>

<div class="edit item-page<?php echo $this->pageclass_sfx; ?>">
    <?php if ($this->params->get('show_page_heading', 1)) : ?>
        <h1>
            <?php echo $this->escape($this->params->get('page_heading')); ?>
        </h1>
    <?php endif; ?>
    <div class="wf_mainheader">
        <h2><?php echo $this->wftitle; ?></h2>
    </div>
    
    <div id="system-message-container"></div>

    <form action="<?php echo JRoute::_('index.php?option=com_workforce&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate wfform">
        <div class="formelm-buttons">
            <button type="button" onclick="Joomla.submitbutton('employeeform.apply')">
                <?php echo JText::_('COM_WORKFORCE_APPLY') ?>
            </button>
            <button type="button" onclick="Joomla.submitbutton('employeeform.save')">
                <?php echo JText::_('JSAVE') ?>
            </button>
            <button type="button" onclick="Joomla.submitbutton('employeeform.cancel')">
                <?php echo JText::_('JCANCEL') ?>
            </button>
        </div>
        <?php
        echo JHtml::_('tabs.start', 'employee_tabs', array('useCookie' => false));
        echo JHtml::_('tabs.panel', JText::_( 'COM_WORKFORCE_DETAILS' ), 'details_panel');
        ?>
        <div class="ip_spacer"></div>
        <div style="width: 100%;">
            <fieldset>
                <legend><?php echo JText::_('COM_WORKFORCE_DETAILS'); ?></legend>
                <div class="formelm">
                    <?php echo $this->form->getLabel('fname'); ?>
                    <?php echo $this->form->getInput('fname'); ?>
                </div>
                <div class="formelm">
                    <?php echo $this->form->getLabel('lname'); ?>
                    <?php echo $this->form->getInput('lname'); ?>
                </div>
                <?php if($this->user->authorise('core.admin', 'com_workforce')): //only show department if admin user ?>
                    <div class="formelm"><?php echo $this->form->getLabel('department'); ?>
                    <?php echo $this->form->getInput('department'); ?></div>
                <?php elseif($this->form->getValue('department')): //if not admin and department already set, leave it as a hidden field ?>
                    <div class="formelm"><?php echo $this->form->getLabel('department'); ?>
                    <?php echo workforceHTML::getDepartmentName($this->form->getValue('department')); ?></div>
                    <input type="hidden" name="jform[department]" value="<?php echo $this->form->getValue('department'); ?>" />
                <?php else: ?>
                    <input type="hidden" name="jform[department]" value="<?php echo $this->settings->get('default_department', 1); ?>" />
                <?php endif; ?>
                <div class="formelm">
                    <?php echo $this->form->getLabel('position'); ?>
                    <?php echo $this->form->getInput('position'); ?>
                </div>
                <div class="formelm">
                    <?php echo $this->form->getLabel('email'); ?>
                    <?php echo $this->form->getInput('email'); ?>
                </div>
                <div class="formelm">
                    <?php echo $this->form->getLabel('phone1'); ?>
                    <?php echo $this->form->getInput('phone1'); ?>
                </div>
                <div class="formelm">
                    <?php echo $this->form->getLabel('ext1'); ?>
                    <?php echo $this->form->getInput('ext1'); ?>
                </div>
                <div class="formelm">
                    <?php echo $this->form->getLabel('phone2'); ?>
                    <?php echo $this->form->getInput('phone2'); ?>
                </div>
                <div class="formelm">
                    <?php echo $this->form->getLabel('ext1'); ?>
                    <?php echo $this->form->getInput('ext2'); ?>
                </div>
                <div class="formelm">
                    <?php echo $this->form->getLabel('fax'); ?>
                    <?php echo $this->form->getInput('fax'); ?>
                </div>
                <div class="formelm">
                    <?php echo $this->form->getLabel('website'); ?>
                    <?php echo $this->form->getInput('website'); ?>
                </div>                
            </fieldset>
            <fieldset>
                <legend><?php echo JText::_('COM_WORKFORCE_ADDRESS'); ?></legend>
                <div class="formelm">
                    <?php echo $this->form->getLabel('street'); ?>
                    <?php echo $this->form->getInput('street'); ?>
                </div>
                <div class="formelm">
                    <?php echo $this->form->getLabel('street2'); ?>
                    <?php echo $this->form->getInput('street2'); ?>
                </div>
                <div class="formelm">
                    <?php echo $this->form->getLabel('city'); ?>
                    <?php echo $this->form->getInput('city'); ?>
                </div>
                <div class="formelm">
                    <?php echo $this->form->getLabel('locstate'); ?>
                    <?php echo $this->form->getInput('locstate'); ?>
                </div>
                <div class="formelm">
                    <?php echo $this->form->getLabel('province'); ?>
                    <?php echo $this->form->getInput('province'); ?>
                </div>
                <div class="formelm">
                    <?php echo $this->form->getLabel('postcode'); ?>
                    <?php echo $this->form->getInput('postcode'); ?>
                </div>
                <div class="formelm">
                    <?php echo $this->form->getLabel('country'); ?>
                    <?php echo $this->form->getInput('country'); ?>
                </div>
            </fieldset>
        </div>
        <?php echo JHtml::_('tabs.panel', JText::_( 'COM_WORKFORCE_IMAGE' ).'/'.JText::_('COM_WORKFORCE_BIO'), 'img_panel');?> 
        <div class="ip_spacer"></div>
        <div style="width: 100%;">
            <fieldset>
                <legend><?php echo JText::_('COM_WORKFORCE_IMAGE'); ?></legend>
                <div class="formelm">
                    <?php echo $this->form->getInput('icon'); ?>
                </div>
            </fieldset>
            <fieldset>
                <legend><?php echo JText::_('COM_WORKFORCE_BIO'); ?></legend>
                <div class="formelm">
                    <?php echo $this->form->getLabel('wfhead1'); ?>
                    <div class="clr"></div>
                    <?php echo $this->form->getInput('bio'); ?>
                </div>            
            </fieldset>
        </div>  
        <?php echo JHtml::_('tabs.panel', JText::_( 'COM_WORKFORCE_AVAILABILITY' ).'/'.JText::_('COM_WORKFORCE_SOCIAL'), 'social_panel');?> 
        <div class="ip_spacer"></div>
        <div style="width: 100%;"> 
            <fieldset>
                <legend><?php echo JText::_('COM_WORKFORCE_AVAILABILITY'); ?></legend>
                <div class="formelm">
                    <?php echo $this->form->getLabel('wfhead2'); ?>
                    <?php echo $this->form->getInput('availability'); ?>
                </div>
            </fieldset>
            <fieldset>
                <legend><?php echo JText::_('COM_WORKFORCE_SOCIAL'); ?></legend>                
                <div class="formelm">
                    <?php echo $this->form->getLabel('twitter'); ?>
                    <?php echo $this->form->getInput('twitter'); ?>
                </div>
                <div class="formelm">
                    <?php echo $this->form->getLabel('facebook'); ?>
                    <?php echo $this->form->getInput('facebook'); ?>
                </div>
                <div class="formelm">
                    <?php echo $this->form->getLabel('youtube'); ?>
                    <?php echo $this->form->getInput('youtube'); ?>
                </div>
                <div class="formelm">
                    <?php echo $this->form->getLabel('linkedin'); ?>
                    <?php echo $this->form->getInput('linkedin'); ?>
                </div>
            </fieldset>
        </div>
        <?php echo JHtml::_('tabs.end'); ?>
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="return" value="<?php echo $this->return_page;?>" />
        <?php echo JHtml::_( 'form.token' ); ?>
    </form>
</div>