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
JHtml::_('behavior.modal');
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
function updateInputs(ftype) {
	var f = document.adminForm;
	if (ftype=='selectlist' || ftype=='recipient'		|| ftype=='selectmultiple' || ftype=='checkbox' 
			|| ftype=='radiobutton' || ftype=='number'	|| ftype=='numberrange'		
			|| ftype=='freetext' 	|| ftype=='hidden'	|| ftype=='subject' || ftype=='sql' 
			|| ftype=='multitext'	|| ftype=='text'	|| ftype=='email'	|| ftype=='email_verify'
			|| ftype=='surname'		|| ftype=='password' || ftype=='username' 
			|| ftype=='css'			|| ftype=='php'		|| ftype=='js'		|| ftype=='autocomplete'			   	
		) {
		f.getElementById('jform_value').disabled=false;
		f.getElementById('jform_value').style.backgroundColor='#FFF'; 
	} else {
		f.getElementById('jform_value').style.backgroundColor='#F5F5F5'; 
		f.getElementById('jform_value').disabled=true;
	}
}
window.addEvent('domready', function(){
	updateInputs('<?php echo $this->item->type; ?>');
});
// -->
</script>

<form action="<?php JRoute::_('index.php?option=com_contactenhanced'); ?>" method="post" name="adminForm" id="contact-form" class="form-validate">
	<div class="width-60 fltlft">
	<?php echo  JHtml::_('sliders.start', 'contact-slider2'); ?>
			<?php echo JHtml::_('sliders.panel',JText::_('CE_CF_DETAILS'), 'ce-cf-info'); ?>
		
		<fieldset class="adminform">
			<ul class="adminformlist">
				<li><?php echo $this->form->getLabel('name'); ?>
				<?php echo $this->form->getInput('name'); ?></li>
	
				<li><?php echo $this->form->getLabel('type'); ?>
				<?php echo $this->form->getInput('type'); ?></li>
				
				<?php if($this->item->id): ?>
					<li><label  class="hasTip" for="jform_value" id="jform_value-lbl" 
							title="<?php echo JText::_('CE_CF_VALUE_DESC');?>">
					<?php echo JText::_('CE_CF_VALUE_LABEL'); ?></label>
					
					
					<?php
					
						if($this->item->type == 'freetext'){
							$editor = &JFactory::getEditor();
							echo '<div style="clear:both">'
								. $editor->display( 'jform[value]',  $this->item->value , '80%', '200', '75', '20' ).'</div>' ;
						}elseif($this->code_editor){
							$editor = &JFactory::getEditor($this->code_editor);
							echo '<div style="clear:both">'
								. $editor->display( 'jform[value]',  $this->item->value , '80%', '200', '75', '20',false ).'</div>' ;
						
						}else{
							echo '<textarea class="inputbox" name="jform[value]" id="jform_value" style="width:70%;height:150px">'.$this->item->value.'</textarea>';
						}
					?>
				
				</li>
				
				<div style="clear:both">
				<?php echo JText::_('COM_CONTACTENHANCED_CF_TIP_'.strtoupper($this->item->type)); ?>
				</div>
				<?php 
				else:
						echo '<br /><h2>'.JText::_('COM_CONTACTENHANCED_CF_PLEASE_SAVE_BEFORE_CONTINUING').'</h2>';
				endif;
				?>
			</ul>
		</fieldset>
			<?php echo JHtml::_('sliders.panel',JText::_('CE_CF_TOOLTIP'), 'ce-cf-tooltip'); ?>
		<fieldset class="adminform">
			<ul class="adminformlist">
				<li><?php echo $this->form->getLabel('tooltip'); ?>
				<?php echo $this->form->getInput('tooltip'); ?></li>
			</ul>
		</fieldset>
			<?php echo JHtml::_('sliders.panel',JText::_('CE_CF_ATTRIBUTES'), 'ce-cf-attributes'); ?>
		<fieldset class="adminform">
			<ul class="adminformlist">
				<li><?php echo $this->form->getLabel('attributes'); ?>
				<?php echo $this->form->getInput('attributes'); ?></li>
			</ul>
			<br style="clear: both" />
			<?php echo JHtml::_('link'
									, 'http://ideal.fok.com.br/joomla-extensions/component-contact-enhanced/documentation/article/20-customizing-your-forms/102-html-field-attributes.html?tmpl=component'
									, JText::_('COM_CONTACTENHANED_CF_HTML_ATTRIBUTES')
									, array(
											'class'	=> 'modal',
											'rel'	=> "{handler: 'iframe', size: {x:800, y:480}}"
										)
								); ?>
		</fieldset>
		<?php echo JHtml::_('sliders.end'); ?>
	</div>

	<div class="width-40 fltrt">
		<?php echo  JHtml::_('sliders.start', 'ce-cf-more-slider'); ?>
			<?php echo JHtml::_('sliders.panel',JText::_('CE_CF_PUBLISHING_INFO'), 'publishing-options'); ?>
			<fieldset class="panelform">
				<ul class="adminformlist">
					
					
					<li><?php echo $this->form->getLabel('required'); ?>
					<?php echo $this->form->getInput('required'); ?></li>
		
					<li><?php echo $this->form->getLabel('catid'); ?>
					<?php echo $this->form->getInput('catid'); ?></li>
					
					<li><?php echo $this->form->getLabel('ordering'); ?>
					<?php echo $this->form->getInput('ordering'); ?></li>
		
					<li><?php echo $this->form->getLabel('language'); ?>
					<?php echo $this->form->getInput('language'); ?></li>
					
					<li><?php echo $this->form->getLabel('access'); ?>
					<?php echo $this->form->getInput('access'); ?></li>
		
					<li><?php echo $this->form->getLabel('published'); ?>
					<?php echo $this->form->getInput('published'); ?></li>
		
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