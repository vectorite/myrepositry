<?php
/**
 *  @package AdminTools
 *  @copyright Copyright (c)2010-2011 Nicholas K. Dionysopoulos
 *  @license GNU General Public License version 3, or later
 *  @version $Id$
 */

// Protect from unauthorized access
defined('_JEXEC') or die('Restricted Access');
?>

<form name="adminForm" action="index.php" method="post">
	<input type="hidden" name="option" value="com_admintools" />
	<input type="hidden" name="view" value="jupdate" />
	<input type="hidden" name="task" value="install" />
	<input type="hidden" name="act" id="act" value="nobackup" />
	<input type="hidden" name="file" value="<?php echo $this->file ?>" />

	<table class="adminTable">
	<tr>
		<td><?php echo JText::_('ATOOLS_LBL_EXTRACTIONMETHOD'); ?></td>
		<td><?php echo JHTML::_('select.genericlist', $this->extractionmodes, 'procengine', '', 'value', 'text', $this->ftpparams['procengine']);?></td>
	</tr>
	<tr>
		<td colspan="2">
			<input type="submit" value="<?php echo JText::_('ATOOLS_LBL_START') ?>" />
			<?php if($this->hasakeeba): ?>
				<input type="submit" value="<?php echo JText::_('ATOOLS_LBL_START_BACKUP') ?>" onclick="backupFirst();" />
			<?php endif; ?>
		</td>
	</tr>

	<tr>
		<td colspan="2"><hr /></td>
	</tr>

	<tr>
		<td colspan="2"><strong><?php echo JText::_('ATOOLS_LBL_FTPOPTIONS'); ?></strong></td>
	</tr>
	<tr>
		<td><?php echo JText::_('ATOOLS_LBL_HOST_TITLE') ?></td>
		<td><input id="ftp_host" name="ftp_host" value="<?php echo $this->ftpparams['ftp_host']; ?>" type="text" /></td>
	</tr>
	<tr>
		<td><?php echo JText::_('ATOOLS_LBL_PORT_TITLE') ?></td>
		<td><input id="ftp_port" name="ftp_port" value="<?php echo $this->ftpparams['ftp_port']; ?>" type="text" /></td>
	</tr>
	<tr>
		<td><?php echo JText::_('ATOOLS_LBL_USER_TITLE') ?></td>
		<td><input id="ftp_user" name="ftp_user" value="<?php echo $this->ftpparams['ftp_user']; ?>" type="text" /></td>
	</tr>
	<tr>
		<td><?php echo JText::_('ATOOLS_LBL_PASSWORD_TITLE') ?></td>
		<td><input id="ftp_pass" name="ftp_pass" value="<?php echo $this->ftpparams['ftp_pass']; ?>" type="password" /></td>
	</tr>
	<tr>
		<td><?php echo JText::_('ATOOLS_LBL_INITDIR_TITLE') ?></td>
		<td><input id="ftp_root" name="ftp_root" value="<?php echo $this->ftpparams['ftp_root']; ?>" type="text" /></td>
	</tr>
	</table>
</form>

<script type="text/javascript">
function backupFirst()
{
	document.getElementById('act').setAttribute('value', 'backup');
}
</script>