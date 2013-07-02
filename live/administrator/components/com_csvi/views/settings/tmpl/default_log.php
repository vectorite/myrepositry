<?php
/**
 * Log settings page
 *
 * @package 	CSVI
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2012 RolandD Cyber Produksi
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: default_log.php 1924 2012-03-02 11:32:38Z RolandD $
 */

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );
?>
<div class="width-60 fltlft">
	<fieldset class="adminform">
		<legend><?php echo JText::_('COM_CSVI_LOG_SETTINGS'); ?></legend>
			<ul class="adminformlist">
				<?php foreach ($this->form->getGroup('log') as $field) : ?>
				<li>
					<?php echo $field->label; ?>
					<?php echo $field->input; ?>
				</li>
				<?php endforeach; ?>
			</ul>
	</fieldset>
	<fieldset class="adminform">
		<legend><?php echo JText::_('COM_CSVI_DEBUG_LOG_SETTINGS'); ?></legend>
		<ul class="adminformlist">
			<?php foreach ($this->form->getGroup('debuglog') as $field) : ?>
			<li>
				<?php echo $field->label; ?>
				<?php echo $field->input; ?>
			</li>
			<?php endforeach; ?>
		</ul>
	</fieldset>
</div>
<div class="clr"></div>