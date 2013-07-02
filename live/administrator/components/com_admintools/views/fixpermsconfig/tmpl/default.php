<?php
/**
 * @package AkeebaReleaseSystem
 * @copyright Copyright (c)2010-2011 Nicholas K. Dionysopoulos
 * @license GNU General Public License version 3, or later
 * @version $Id$
 */

defined('_JEXEC') or die('Restricted Access');

FOFTemplateUtils::addCSS('media://com_admintools/css/backend.css?'.ADMINTOOLS_VERSION);
$this->loadHelper('select');

$path = $this->path;
if(!empty($path)) $path .= '/';

function renderPermissions($perms)
{
	if($perms === false) return '&mdash;';
	return decoct($perms & 0777);
}

function renderUGID($uid, $gid)
{
	static $users = array();
	static $groups = array();

	if($uid === false) {
		$user = '&mdash;';
	} else {
		if(!array_key_exists($uid, $users)) {
			if(function_exists('posix_getpwuid')) {
				$uArray = posix_getpwuid($uid);
				$users[$uid] = $uArray['name']; //." ($uid)";
			} else {
				$users[$uid] = $uid;
			}
		}
		$user = $users[$uid];
	}

	if($gid === false) {
		$group = '&mdash;';
	} else {
		if(!array_key_exists($gid, $groups)) {
			if(function_exists('posix_getgrgid')) {
				$gArray = posix_getgrgid($gid);
				$groups[$gid] = $gArray['name']; //." ($gid)";
			} else {
				$groups[$gid] = $gid;
			}
		}
		$group = $groups[$gid];
	}

	return "$user:$group";
}
?>
<form name="defaultsForm" id="defaultsForm" action="index.php" method="post">
	<input type="hidden" name="option" value="com_admintools" />
	<input type="hidden" name="view" value="fixpermsconfig" />
	<input type="hidden" name="task" value="savedefaults" />
	<input type="hidden" name="<?php echo JUtility::getToken();?>" value="1" />
	<fieldset>
		<legend><?php echo JText::_('ATOOLS_LBL_FIXPERMSCONFIG_DEFAULTS') ?></legend>
		<div class="editform-row">
			<label for="dirperms"><?php echo JText::_('ATOOLS_LBL_FIXPERMSCONFIG_DEFDIRPERM'); ?></label>
			<?php echo AdmintoolsHelperSelect::perms('dirperms', array(), $this->dirperms) ?>
		</div>
		<div class="editform-row">
			<label for="fileperms"><?php echo JText::_('ATOOLS_LBL_FIXPERMSCONFIG_DEFFILEPERMS'); ?></label>
			<?php echo AdmintoolsHelperSelect::perms('fileperms', array(), $this->fileperms) ?>
		</div>
	</fieldset>
	<input type="submit" value="<?php echo JText::_('ATOOLS_LBL_FIXPERMSCONFIG_SAVEDEFAULTS') ?>" />
</form>
<hr/>
<?php if(!empty($this->listing['crumbs'])): ?>
<?php echo JText::_('ATOOLS_LBL_FIXPERMSCONFIG_PATH'); ?>:
<a href="index.php?option=com_admintools&view=fixpermsconfig&path=/">
	<?php echo JText::_('ATOOLS_LBL_FIXPERMSCONFIG_ROOT'); ?>
</a>
<?php $relpath = ''; ?>
<?php foreach($this->listing['crumbs'] as $crumb): ?>
<?php if(empty($crumb)) continue; ?>
<?php $relpath = ltrim($relpath.'/'.$crumb,'/'); ?>
 &bull;
<a href="index.php?option=com_admintools&view=fixpermsconfig&path=<?php echo urlencode($relpath) ?>">
	<?php echo $this->escape($crumb); ?>
</a>
<?php endforeach; ?>
<hr/>
<?php endif ?>
<form name="adminForm" id="adminForm" action="index.php" method="post">
	<input type="hidden" name="option" value="com_admintools" />
	<input type="hidden" name="view" value="fixpermsconfig" />
	<input type="hidden" name="task" value="saveperms" />
	<input type="hidden" name="path" value="<?php echo $this->path ?>" />
	<input type="hidden" name="<?php echo JUtility::getToken();?>" value="1" />

	<input type="submit" value="<?php echo JText::_('ATOOLS_LBL_FIXPERMSCONFIG_SAVEPERMS') ?>" />
	<input type="submit" value="<?php echo JText::_('ATOOLS_LBL_FIXPERMSCONFIG_SAVEAPPLYPERMS') ?>" onclick="document.forms.adminForm.task.value='saveapplyperms';" />

	<div id="splitlist">
		<fieldset>
			<legend><?php echo JText::_('ATOOLS_LBL_FIXPERMSCONFIG_FOLDERS') ?></legend>
			<table class="adminlist">
			<thead>
				<tr>
					<th><?php echo JText::_('ATOOLS_LBL_FIXPERMSCONFIG_FOLDER'); ?></th>
					<th><?php echo JText::_('ATOOLS_LBL_FIXPERMSCONFIG_OWNER'); ?></th>
					<th colspan="2"><?php echo JText::_('ATOOLS_LBL_FIXPERMSCONFIG_PERMS'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php $i = 1; ?>
				<?php if(!empty($this->listing['folders'])) foreach($this->listing['folders'] as $folder): ?>
				<?php $i = 1 - $i; ?>
				<tr class="row<?php echo $i ?>">
					<td>
						<a href="index.php?option=com_admintools&view=fixpermsconfig&path=<?php echo urlencode($folder['path']) ?>">
							<?php echo $this->escape($folder['item']) ?>
						</a>
					</td>
					<td>
						<?php echo renderUGID( $folder['uid'], $folder['gid'] ); ?>
					</td>
					<td>
						<?php echo renderPermissions($folder['realperms']) ?>
					</td>
					<td align="right">
						<?php echo AdmintoolsHelperSelect::perms('folders['.$folder['path'].']', array(), $folder['perms']) ?>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
			</table>
		</fieldset>

		<fieldset>
			<legend><?php echo JText::_('ATOOLS_LBL_FIXPERMSCONFIG_FILES') ?></legend>

			<table class="adminlist">
			<thead>
				<tr>
					<th><?php echo JText::_('ATOOLS_LBL_FIXPERMSCONFIG_FILE'); ?></th>
					<th><?php echo JText::_('ATOOLS_LBL_FIXPERMSCONFIG_OWNER'); ?></th>
					<th colspan="2"><?php echo JText::_('ATOOLS_LBL_FIXPERMSCONFIG_PERMS'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php $i = 1; ?>
				<?php if(!empty($this->listing['files'])) foreach($this->listing['files'] as $file): ?>
				<?php $i = 1 - $i; ?>
				<tr class="row<?php echo $i ?>">
					<td>
						<?php echo $this->escape($file['item']) ?>
					</td>
					<td>
						<?php echo renderUGID( $file['uid'], $file['gid'] ); ?>
					</td>
					<td>
						<?php echo renderPermissions($file['realperms']) ?>
					</td>
					<td align="right">
						<?php echo AdmintoolsHelperSelect::perms('files['.$file['path'].']', array(), $file['perms']) ?>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
			</table>
		</fieldset>
	</div>
	<div style="clear: left;"></div>

	<input type="submit" value="<?php echo JText::_('ATOOLS_LBL_FIXPERMSCONFIG_SAVEPERMS') ?>" />
	<input type="submit" value="<?php echo JText::_('ATOOLS_LBL_FIXPERMSCONFIG_SAVEAPPLYPERMS') ?>" onclick="document.forms.adminForm.task.value='saveapplyperms';" />
</form>