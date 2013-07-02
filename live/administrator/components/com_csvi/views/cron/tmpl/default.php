<?php
/**
 * Shows the cronline to use for the chosen export
 *
 * @package 	CSVI
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2012 RolandD Cyber Produksi
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: default.php 1924 2012-03-02 11:32:38Z RolandD $
 */

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );
?>
<div class="crontitle"><?php echo JText::_('COM_CSVI_CRONTITLE_STRING'); ?></div>
<div class="cronline"><?php echo $this->cronline; ?></div>
<div id="cronnote"><?php echo JText::_('COM_CSVI_CRONNOTE'); ?></div>
<form method="post" action="<?php echo JRoute::_('index.php?option=com_csvi'); ?>" name="adminForm" id="adminForm">
	<input type="hidden" name="view" id="view" value="" />
	<input type="hidden" name="task" id="task" value="" />
</form>
<script type="text/javascript">
Joomla.submitbutton = function(task) {
	document.adminForm.view.value = task;
	task = '';
	Joomla.submitform(task);	
}
</script>