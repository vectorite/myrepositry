<?php
/**
 * @package SmartIcons Component for Joomla! 2.5
 * @version $Id$
 * @author SUTA Bogdan-Ioan
 * @copyright (C) 2011 SUTA Bogdan-Ioan
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

// No direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');

?>
<form enctype="multipart/form-data"  action="<?php echo JRoute::_('index.php?option=com_smarticons&idIcon='.(int) $this->item->idIcon); ?>" method="post" name="adminForm" id="smarticon-form" class="form-validate">
	<div class="width-100 fltlft">
		<fieldset>
			<div class="fltrt">
				<button type="button" onclick="Joomla.submitform('import.save', this.form);">
					<?php echo JText::_('JSAVE');?></button>
				<button type="button" onclick="window.parent.location.href=window.parent.location.href; window.parent.SqueezeBox.close();">
					<?php echo JText::_('JCANCEL');?></button>
			</div>
			<div class="configuration" >
				<?php echo JText::_( 'COM_SMARTICONS_IMPORT_DETAILS' ) ?>
			</div>
		</fieldset>

		<fieldset class="adminform" style="margin:0px">
			<legend><?php echo JText::_( 'COM_SMARTICONS_IMPORT_DETAILS' ); ?></legend>
			<label id="xmlFile-lbl" for="xmlFile" class="hasTip required" title="<?php echo JText::_('COM_SMARTICONS_IMPORT_FIELD_FILEUPLOAD_LABEL') . '::' . JText::_('COM_SMARTICONS_IMPORT_FIELD_FILEUPLOAD_DESC'); ?>"><?php echo JText::_('COM_SMARTICONS_IMPORT_FIELD_FILEUPLOAD_LABEL'); ?></label>
			<input class="input_box" id="xmlFile" name="xmlFile" type="file" size="57" />
		</fieldset>
		<input type="hidden" name="task" value="smartimport.save" />
		<input type="hidden" name="view" value="close" />
		<input type="hidden" name="tmpl" value="component" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>