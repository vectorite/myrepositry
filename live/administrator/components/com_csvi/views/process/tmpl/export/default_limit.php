<?php
/**
 * Export system limit options
 *
 * @package 	CSVI
 * @subpackage 	Export
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2012 RolandD Cyber Produksi
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: default_limit.php 1924 2012-03-02 11:32:38Z RolandD $
 */

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );
?>
<fieldset>
	<legend><?php echo JText::_('COM_CSVI_EXPORT_LIMIT_OPTIONS'); ?></legend>
	<ul>
		<li><div class="option_label"><?php echo $this->form->getLabel('use_system_limits', 'limit'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('use_system_limits', 'limit'); ?></div></li>
		<li><div class="option_label"><?php echo $this->form->getLabel('max_execution_time', 'limit'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('max_execution_time', 'limit'); ?></div><div>
			<?php echo JText::_('COM_CSVI_DEFAULT'); ?>: <?php echo intval(ini_get('max_execution_time')); ?></div></li>
		<li><div class="option_label"><?php echo $this->form->getLabel('memory_limit', 'limit'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('memory_limit', 'limit'); ?></div><div>
			<?php echo JText::_('COM_CSVI_DEFAULT'); ?>: <?php echo intval(ini_get('memory_limit')); ?></div></li>
	</ul>
</fieldset>
<div class="clr"></div>