<?php
/**
 * @package SmartIcons Component for Joomla! 2.5
 * @version $Id: default.php 9 2012-03-28 20:07:32Z Bobo $
 * @author SUTA Bogdan-Ioan
 * @copyright (C) 2011 SUTA Bogdan-Ioan
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

// No direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');

$params = $this->form->getFieldsets('params');
?>
<form action="<?php echo JRoute::_('index.php?option=com_smarticons&idIcon='.(int) $this->item->idIcon); ?>" method="post" name="adminForm" id="smarticon-form" class="form-validate">
	<div class="width-60 fltlft">
		<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_SMARTICONS_ICON_DETAILS' ); ?></legend>
			<ul class="adminformlist">
<?php foreach($this->form->getFieldset('details') as $field): ?>
                                <li><?php echo $field->label;echo $field->input;?></li>
<?php endforeach; ?>
			</ul>
		</fieldset>
	</div>
	<div class="width-40 fltrt">
<?php echo JHtml::_('sliders.start', 'smarticons-slider'); ?>
<?php foreach ($params as $name => $fieldset): ?>
                <?php echo JHtml::_('sliders.panel', JText::_($fieldset->label), $name.'-params');?>
        <?php if (isset($fieldset->description) && trim($fieldset->description)): ?>
                <p class="tip"><?php echo $this->escape(JText::_($fieldset->description));?></p>
        <?php endif;?>
                <fieldset class="panelform" >
                        <ul class="adminformlist">
        <?php foreach ($this->form->getFieldset($name) as $field) : ?>
                                <li><?php echo $field->label; ?><?php echo $field->input; ?></li>
        <?php endforeach; ?>
                        </ul>
                </fieldset>
<?php endforeach; ?>
 
<?php echo JHtml::_('sliders.end'); ?>
        </div>
	<div id="cpanel" class="width-20 fltrt">
		<?php echo $this->loadTemplate('button'); ?>
	</div>
	<div class="clr"></div>
	<?php if ($this->canDo->get('core.admin')): ?>
		<div  class="width-100 fltlft">

			<?php echo JHtml::_('sliders.start','permissions-sliders-'.$this->item->idIcon, array('useCookie'=>1)); ?>
	
			<?php echo JHtml::_('sliders.panel',JText::_('COM_SMARTICONS_ACCESS_FIEDLSET_RULES'), 'access-rules'); ?>	
				<fieldset class="panelform">
					<?php echo $this->form->getLabel('rules'); ?>
					<?php echo $this->form->getInput('rules'); ?>
				</fieldset>
			
			<?php echo JHtml::_('sliders.end'); ?>
		</div>
	<?php endif; ?>
	<div>
		<input type="hidden" name="task" value="smarticon.edit" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>