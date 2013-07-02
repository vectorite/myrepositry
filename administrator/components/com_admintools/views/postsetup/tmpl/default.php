<?php
/**
 * @package AkeebaBackup
 * @copyright Copyright (c)2006-2013 Nicholas K. Dionysopoulos
 * @license GNU General Public License version 3, or later
 * @version $Id$
 * @since 1.3
 */

defined('_JEXEC') or die();

$disabled = ADMINTOOLS_PRO ? '' : 'disabled = "disabled"';

$confirmText = JText::_('COM_ADMINTOOLS_POSTSETUP_MSG_MINSTABILITY');
$script = <<<ENDSCRIPT
window.addEvent('domready', function(){
	(function($) {
		$('#akeeba-postsetup-apply').click(function(e){
			var minstability = $('#minstability').val();
			if(minstability != 'stable') {
				var reply=confirm("$confirmText");
				if(!reply) return false;
			}
			$('#adminForm').submit();
		});
	})(akeeba.jQuery);
});

ENDSCRIPT;
JFactory::getDocument()->addScriptDeclaration($script);

if(version_compare(JVERSION, '3.0', 'ge')) {
	JHTML::_('behavior.framework');
} else {
	JHTML::_('behavior.mootools');
}
?>
<form action="index.php" method="post" name="adminForm" id="adminForm" class="form">
	<input type="hidden" name="option" value="com_admintools" />
	<input type="hidden" name="view" value="postsetup" />
	<input type="hidden" name="task" id="task" value="save" />
	<input type="hidden" name="<?php echo JFactory::getSession()->getFormToken();?>" value="1" />
	
	<p class="alert alert-info"><?php echo JText::_('COM_ADMINTOOLS_POSTSETUP_LBL_WHATTHIS'); ?></p>

	<label for="autoupdate" class="postsetup-main">
		<input type="checkbox" id="autoupdate" name="autoupdate" <?php if($this->enableautoupdate): ?>checked="checked"<?php endif; ?> <?php echo $disabled?> />
		<?php echo JText::_('COM_ADMINTOOLS_POSTSETUP_LBL_AUTOUPDATE')?>
	</label>
	
	<?php if(ADMINTOOLS_PRO): ?>
	<div class="help-block"><?php echo JText::_('COM_ADMINTOOLS_POSTSETUP_DESC_autoupdate');?></div>
	<?php else: ?>
	<div class="help-block"><?php echo JText::_('COM_ADMINTOOLS_POSTSETUP_NOTAVAILABLEINCORE');?></div>
	<?php endif; ?>
	<br/>
	
	<label for="autojupdate" class="postsetup-main">
		<input type="checkbox" id="autojupdate" name="autojupdate" <?php if($this->enableautojupdate): ?>checked="checked"<?php endif; ?> <?php echo $disabled?> />
		<?php echo JText::_('COM_ADMINTOOLS_POSTSETUP_LBL_AUTOJUPDATE')?>
	</label>
	
	<?php if(ADMINTOOLS_PRO): ?>
	<div class="help-block"><?php echo JText::_('COM_ADMINTOOLS_POSTSETUP_DESC_autojupdate');?></div>
	<?php else: ?>
	<div class="help-block"><?php echo JText::_('COM_ADMINTOOLS_POSTSETUP_NOTAVAILABLEINCORE');?></div>
	<?php endif; ?>
	<br/>
	
	<?php if(ADMINTOOLS_PRO): ?>
	<label for="minstability" class="postsetup-main"><?php echo JText::_('COM_ADMINTOOLS_POSTSETUP_LBL_MINSTABILITY')?></label>
	<select id="minstability" name="minstability">
		<option value="alpha" <?php if($this->minstability=='alpha'): ?>selected="selected"<?php endif; ?>><?php echo JText::_('ATOOLS_STABILITY_ALPHA') ?></option>
		<option value="beta" <?php if($this->minstability=='beta'): ?>selected="selected"<?php endif; ?>><?php echo JText::_('ATOOLS_STABILITY_BETA') ?></option>
		<option value="rc" <?php if($this->minstability=='rc'): ?>selected="selected"<?php endif; ?>><?php echo JText::_('ATOOLS_STABILITY_RC') ?></option>
		<option value="stable" <?php if($this->minstability=='stable'): ?>selected="selected"<?php endif; ?>><?php echo JText::_('ATOOLS_STABILITY_STABLE') ?></option>
	</select>
	</br>
	<div class="help-block"><?php echo JText::_('COM_ADMINTOOLS_POSTSETUP_DESC_MINSTABILITY');?></div>
	<br/>
	<?php else: ?>
	<input type="hidden" id="minstability" name="minstability" value="stable" />
	<?php endif; ?>
	<br/>
	
	<label for="acceptlicense" class="postsetup-main">
		<input type="checkbox" id="acceptlicense" name="acceptlicense" <?php if($this->acceptlicense): ?>checked="checked"<?php endif; ?> />
		<?php echo JText::_('COM_ADMINTOOLS_POSTSETUP_LBL_ACCEPTLICENSE')?>
	</label>
	<div class="help-block"><?php echo JText::_('COM_ADMINTOOLS_POSTSETUP_DESC_ACCEPTLICENSE');?></div>
	<br/>
	
	<label for="acceptsupport" class="postsetup-main">
		<input type="checkbox" id="acceptsupport" name="acceptsupport" <?php if($this->acceptsupport): ?>checked="checked"<?php endif; ?> />
		<?php echo JText::_('COM_ADMINTOOLS_POSTSETUP_LBL_ACCEPTSUPPORT')?>
	</label>
	</br>
	<div class="help-block"><?php echo JText::_('COM_ADMINTOOLS_POSTSETUP_DESC_ACCEPTSUPPORT');?></div>
	<br/>

	<div class="form-actions">
		<button class="btn btn-large btn-primary" id="akeeba-postsetup-apply" onclick="return false;"><?php echo JText::_('COM_ADMINTOOLS_POSTSETUP_LBL_APPLY');?></button>
	</div>
</form>