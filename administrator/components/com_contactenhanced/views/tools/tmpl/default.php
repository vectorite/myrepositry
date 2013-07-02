<?php
/**
 * @package		com_contactenhanced
* @copyright	Copyright (C) 2006 - 2012 Ideal Custom Software Development
 * @author     Douglas Machado {@link http://ideal.fok.com.br}
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT.DS.'helpers'.DS.'html');
JHtml::_('behavior.tooltip');
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		Joomla.submitform(task, document.getElementById('adminForm'));
	}
</script>
<form 
	enctype="multipart/form-data" 
	action="<?php echo JRoute::_('index.php?option=com_contactenhanced');?>" id="adminForm" method="post" 
	name="adminForm"  class="form-validate">
<?php
	echo JHtml::_('tabs.start','ce-tools-tabs', array('useCookie'=>1));
			echo JHtml::_('tabs.panel',JText::_('COM_CONTACTENHANCED_TOOLS_IMPORT_LABEL'), 'publishing-details');
				//echo '<p class="tab-description">'.JText::_('COM_CONTACTENHANCED_INSTRUCTIONS_IMPORT_CONTACTS_AND_CATEGORIES').'</p>';
				
	?>
	<div class="width-100">
			<fieldset class="uploadform">
				<legend><?php echo JText::_('COM_CONTACTENHANCED_TOOLS_IMPORT_UPLOAD_CSV_FILE_LABEL'); ?></legend>
				<div><?php echo JText::_('COM_CONTACTENHANCED_TOOLS_IMPORT_UPLOAD_CSV_FILE_DESC'); ?></div>
				<label for="catid"><?php echo JText::_('COM_CONTACTENHANCED_TOOLS_DEFAULT_CATEGORY_LABEL'); ?></label>
				<select name="catid" id="catid" class="inputbox" >
					<?php echo JHtml::_('select.options', JHtml::_('category.options', 'com_contactenhanced'), 'value', 'text', JRequest::getVar('catid'));?>
				</select>
				<label for="csv_file"><?php echo JText::_('COM_CONTACTENHANCED_TOOLS_IMPORT_CSV_FILE_LABEL'); ?></label>
				<input type="file" size="57" name="csv_file" id="csv_file" class="input_box">
				<input type="button" onclick="Joomla.submitbutton('tools.importcsv')" 
						value="<?php echo JText::_('JTOOLBAR_UPLOAD'); ?>" 
						class="button" />
			</fieldset>
			<fieldset class="uploadform">
				<legend><?php echo JText::_('COM_CONTACTENHANCED_INSTRUCTIONS_IMPORT_CONTACTS_AND_CATEGORIES'); ?></legend>
			
			<?php echo '<p>'.JText::_('COM_CONTACTENHANCED_TOOLS_IMPORT_WARNING').'</p>'; ?>
				<!-- label for=""><?php echo JText::_('COM_CONTACTENHANCED_TOOLS_IMPORT_LABEL'); ?></label  -->
					<button class="button" 
						onclick="Joomla.submitbutton('import')">
						<?php echo JText::_('COM_CONTACTENHANCED_INSTRUCTIONS_IMPORT_CONTACTS_AND_CATEGORIES');?>
					</button>
			</fieldset>
	</div>
	<div class="clr"></div>
	<?php
	echo JHtml::_('tabs.end');
	?>
	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="view" value="tools" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>