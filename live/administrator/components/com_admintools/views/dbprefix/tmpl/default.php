<?php
/**
 *  @package AdminTools
 *  @copyright Copyright (c)2010-2011 Nicholas K. Dionysopoulos
 *  @license GNU General Public License version 3, or later
 *  @version $Id$
 */

// Protect from unauthorized access
defined('_JEXEC') or die('Restricted Access');

FOFTemplateUtils::addCSS('media://com_admintools/css/backend.css?'.ADMINTOOLS_VERSION);
?>
<div class="disclaimer">
	<p><?php echo JText::_('ATOOLS_LBL_DBREFIX_INTRO'); ?></p>
</div>

<?php if($this->isDefaultPrefix): ?>
<p class="admintools-warning">
	<?php echo JText::_('ATOOLS_LBL_DBREFIX_DEFAULTFOUND'); ?>
</p>
<?php endif; ?>

<form name="adminForm" action="index.php" action="post">
	<input type="hidden" name="option" value="com_admintools" />
	<input type="hidden" name="view" value="dbprefix" />
	<input type="hidden" name="task" value="change" />
	
	<div class="editform-row">
		<label for="oldprefix"><?php echo JText::_('ATOOLS_LBL_DBREFIX_OLDPREFIX') ?></label>
		<input type="text" name="oldprefix" disabled="disabled" value="<?php echo $this->currentPrefix ?>" size="7" />
	</div>
	
	<div class="editform-row">
		<label for="prefix"><?php echo JText::_('ATOOLS_LBL_DBREFIX_NEWPREFIX') ?></label>
		<input type="text" name="prefix" value="<?php echo $this->newPrefix ?>" size="7" /><br/>
	</div>
	
	<br/>
	<input type="submit" value="<?php echo JText::_('ATOOLS_LBL_DBREFIX_CHANGE') ?>" />
</form>