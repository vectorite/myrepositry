<?php
/**
 * @package		com_contactenhanced
 * @copyright	Copyright (C) 2006 - 2010 Ideal Custom Software Development
 * @author     Douglas Machado {@link http://ideal.fok.com.br}
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

$script = "window.addEvent('domready', function(){
		document.ceImportSliderBox = new Fx.Slide($('topbox')); 
		document.ceImportSliderBox.toggle();
	});";
		$doc =& JFactory::getDocument();
		$doc->addScriptDeclaration($script);
?>
<div id="topbox">
	<div>
		
		<table>
		<tr>
			<td align="left" width="100%">
				<?php echo JText::_('CE_CF_SQL_IMPORT_WARNING'); ?>
			</td>
		</tr>
		<tr>
			<td align="left" width="100%">
				<label for="sql_file"> <?php echo JText::_( 'CE_CF_SQL_FILE' ); ?>:</label>
				<input type="file" size="57" name="sql_file" id="sql_file" class="input_box"/>
				<input type=submit value="<?php echo JText::_('CE_UPLOAD_AND_INSTALL');?>" 
					onclick="document.adminForm.task.value='customfields.import';" class="button" />
			</td>
		</tr>
		</table>	
	</div>
</div>