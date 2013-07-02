<?php
/**
 * @package AkeebaReleaseSystem
 * @copyright Copyright (c)2010-2011 Nicholas K. Dionysopoulos
 * @license GNU General Public License version 3, or later
 * @version $Id$
 */

defined('_JEXEC') or die('Restricted Access');

$editor = JFactory::getEditor();

FOFTemplateUtils::addCSS('media://com_admintools/css/backend.css?'.ADMINTOOLS_VERSION);
JHTML::_('behavior.mootools');

$this->loadHelper('select');
?>
<div id="atools-container">
<form name="adminForm" id="adminForm" action="index.php" method="post">
	<input type="hidden" name="option" value="<?php echo JRequest::getCmd('option') ?>" />
	<input type="hidden" name="view" value="<?php echo JRequest::getCmd('view') ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="id" value="<?php echo $this->item->id ?>" />
	<input type="hidden" name="<?php echo JUtility::getToken();?>" value="1" />

	<fieldset>
		<div class="editform-row">
			<label for="title"><?php echo JText::_('ATOOLS_LBL_REDIRS_SOURCE'); ?></label>
			<input type="text" name="source" id="source" value="<?php echo $this->item->source ?>">
		</div>
		<div class="editform-row">
			<label for="alias"><?php echo JText::_('ATOOLS_LBL_REDIRS_DEST'); ?></label>
			<input type="text" name="dest" id="dest" value="<?php echo $this->item->dest ?>">
		</div>
		<div class="editform-row">
			<label for="published">
				<?php if(version_compare(JVERSION, '1.6.0', 'ge')): ?>
				<?php echo JText::_('JPUBLISHED'); ?>
				<?php else: ?>
				<?php echo JText::_('PUBLISHED'); ?>
				<?php endif ?>
			</label>
			<span class="radio">
				<?php echo JHTML::_('select.booleanlist', 'published', null, $this->item->published); ?>
			</span>
		</div>
		<div style="clear:left"></div>
	</fieldset>
</form>
</div>