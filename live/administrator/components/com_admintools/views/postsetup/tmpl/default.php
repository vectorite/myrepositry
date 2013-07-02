<?php
/**
 * @package AkeebaBackup
 * @copyright Copyright (c)2006-2011 Nicholas K. Dionysopoulos
 * @license GNU General Public License version 3, or later
 * @version $Id$
 * @since 1.3
 */

defined('_JEXEC') or die('Restricted access');

$disabled = ADMINTOOLS_PRO ? '' : 'disabled = "disabled"';

FOFTemplateUtils::addCSS('media://com_admintools/css/backend.css?'.ADMINTOOLS_VERSION);
JHTML::_('behavior.mootools');
?>
<div id="atools-container" class="admintools-postsetup" style="width:100%">
<form action="index.php" method="post" name="adminForm" id="adminForm">
	<input type="hidden" name="option" value="com_admintools" />
	<input type="hidden" name="view" value="postsetup" />
	<input type="hidden" name="task" id="task" value="save" />
	<?php echo JHTML::_( 'form.token' ); ?>
	
	<p><?php echo JText::_('COM_ADMINTOOLS_POSTSETUP_LBL_WHATTHIS'); ?></p>
	
	<input type="checkbox" id="autoupdate" name="autoupdate" <?php if($this->enableautoupdate): ?>checked="checked"<?php endif; ?> <?php echo $disabled?> />
	<label for="autoupdate" class="postsetup-main"><?php echo JText::_('COM_ADMINTOOLS_POSTSETUP_LBL_AUTOUPDATE')?></label>
	</br>
	<?php if(ADMINTOOLS_PRO): ?>
	<div class="postsetup-desc"><?php echo JText::_('COM_ADMINTOOLS_POSTSETUP_DESC_autoupdate');?></div>
	<?php else: ?>
	<div class="postsetup-desc"><?php echo JText::_('COM_ADMINTOOLS_POSTSETUP_NOTAVAILABLEINCORE');?></div>
	<?php endif; ?>
	<br/>
	
	<input type="checkbox" id="autojupdate" name="autojupdate" <?php if($this->enableautojupdate): ?>checked="checked"<?php endif; ?> <?php echo $disabled?> />
	<label for="autojupdate" class="postsetup-main"><?php echo JText::_('COM_ADMINTOOLS_POSTSETUP_LBL_AUTOJUPDATE')?></label>
	</br>
	<?php if(ADMINTOOLS_PRO): ?>
	<div class="postsetup-desc"><?php echo JText::_('COM_ADMINTOOLS_POSTSETUP_DESC_autojupdate');?></div>
	<?php else: ?>
	<div class="postsetup-desc"><?php echo JText::_('COM_ADMINTOOLS_POSTSETUP_NOTAVAILABLEINCORE');?></div>
	<?php endif; ?>
	<br/>
	
	<br/>
	<button onclick="this.form.submit(); return false;"><?php echo JText::_('COM_ADMINTOOLS_POSTSETUP_LBL_APPLY');?></button>
</form>
</div>