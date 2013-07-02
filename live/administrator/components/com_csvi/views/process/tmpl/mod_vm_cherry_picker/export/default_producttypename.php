<?php
/**
 * General export options
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2012 RolandD Cyber Produksi
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: default_file.php 1924 2012-03-02 11:32:38Z RolandD $
 */

defined( '_JEXEC' ) or die;
?>
<fieldset>
	<legend><?php echo JText::_('COM_CSVI_IMPORT_FILE_OPTIONS'); ?></legend>
	<ul>
		<li><div class="option_label"><?php echo $this->form->getLabel('producttypenames', 'producttypename'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('producttypenames', 'producttypename'); ?></div></li>
	</ul>
</fieldset>
<div class="clr"></div>