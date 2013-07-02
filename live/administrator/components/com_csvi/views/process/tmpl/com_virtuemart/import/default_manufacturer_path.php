<?php
/**
 * Import path options
 *
 * @package 	CSVI
 * @subpackage 	Export
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2012 RolandD Cyber Produksi
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: default_category_path.php 1924 2012-03-02 11:32:38Z RolandD $
 */

defined('_JEXEC') or die;

?>
<fieldset>
	<legend><?php echo JText::_('COM_CSVI_IMPORT_PATH_OPTIONS'); ?></legend>
	<ul>
		<li><div class="option_label"><?php echo $this->form->getLabel('file_location_manufacturer_images', 'path'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('file_location_manufacturer_images', 'path'); ?></div>
			<div><?php echo sprintf(JText::_('COM_CSVI_SUGGESTED_PATH'), '<span id="pathsuggest_manufacturer_images">'.$this->config->get('media_manufacturer_path').'</span>');?> |
			<a href="#" onclick="document.getElementById('jform_path_file_location_manufacturer_images').value=document.getElementById('pathsuggest_manufacturer_images').innerHTML; return false;"><?php echo JText::_('COM_CSVI_PASTE');?></a> |
			<a href="#" onclick="document.getElementById('jform_path_file_location_manufacturer_images').value=''; return false;"><?php echo JText::_('COM_CSVI_CLEAR');?></a>
			</div></li>
	</ul>
</fieldset>
