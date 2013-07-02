<?php
/**
 * @package AkeebaReleaseSystem
 * @copyright Copyright (c)2010-2013 Nicholas K. Dionysopoulos
 * @license GNU General Public License version 3, or later
 * @version $Id$
 */

defined('_JEXEC') or die();

$editor = JFactory::getEditor();

if(version_compare(JVERSION, '3.0', 'ge')) {
	JHTML::_('behavior.framework');
} else {
	JHTML::_('behavior.mootools');
}

$this->loadHelper('select');
?>
<form name="adminForm" id="adminForm" action="index.php" method="post" class="form form-horizontal">
	<input type="hidden" name="option" value="com_admintools" />
	<input type="hidden" name="view" value="redirs" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="id" value="<?php echo $this->item->id ?>" />
	<input type="hidden" name="<?php echo JFactory::getSession()->getFormToken();?>" value="1" />

	<div class="control-group">
		<label for="title" class="control-label"><?php echo JText::_('ATOOLS_LBL_REDIRS_SOURCE'); ?></label>
		<div class="controls">
			<input type="text" name="source" id="source" value="<?php echo $this->item->source ?>">
		</div>
	</div>
	<div class="control-group">
		<label for="alias" class="control-label"><?php echo JText::_('ATOOLS_LBL_REDIRS_DEST'); ?></label>
		<div class="controls">
			<input type="text" name="dest" id="dest" value="<?php echo $this->item->dest ?>">
		</div>
	</div>
	<div class="control-group">
		<label for="published" class="control-label">
			<?php echo JText::_('JPUBLISHED'); ?>
		</label>
		<div class="controls">
			<?php echo JHTML::_('select.booleanlist', 'published', null, $this->item->published); ?>
		</div>
	</div>
	<div style="clear:left"></div>
</form>