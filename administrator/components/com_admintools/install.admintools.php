<?php
/**
 *  @package AdminTools
 *  @copyright Copyright (c)2010-2011 Nicholas K. Dionysopoulos
 *  @license GNU General Public License version 3, or later
 *  @version $Id$
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

// no direct access
defined('_JEXEC') or die('');

// =============================================================================
// Akeeba Component Installation Configuration
// =============================================================================
$installation_queue = array(
	// modules => { (folder) => { (module) => { (position), (published) } }* }*
	'modules' => array(
		'admin' => array(
			'atjupgrade' => array('cpanel', 1)
		),
		'site' => array(
		)
	),
	// plugins => { (folder) => { (element) => (published) }* }*
	'plugins' => array(
		'system' => array(
			'admintools'			=> 1,
			'oneclickaction'		=> 0,
			'atoolsupdatecheck'		=> 0,
			'atoolsjupdatecheck'	=> 0
		)
	)
);

// =============================================================================
// Removed Files and Folders Configuration
// =============================================================================
$removeFiles = array(
	'administrator/components/com_admintools/controllers/default.php',
	'administrator/components/com_admintools/controllers/ipautoban.php',
	'administrator/components/com_admintools/models/base.php',
	'administrator/components/com_admintools/models/ipautoban.php',
	'administrator/components/com_admintools/models/ipbl.php',
	'administrator/components/com_admintools/models/ipwl.php',
	'administrator/components/com_admintools/models/log.php',
	'administrator/components/com_admintools/tables/badwords.php',
	'administrator/components/com_admintools/tables/base.php',
	'administrator/components/com_admintools/tables/customperms.php',
	'administrator/components/com_admintools/tables/redirs.php',
	'administrator/components/com_admintools/tables/wafexceptions.php',
	'administrator/components/com_admintools/views/badwords/view.html.php',
	'administrator/components/com_admintools/views/base.view.html.php',
	
	'administrator/components/com_jadmintools/fof/LICENSE.txt',
	'administrator/components/com_jadmintools/fof/controller.php',
	'administrator/components/com_jadmintools/fof/dispatcher.php',
	'administrator/components/com_jadmintools/fof/index.html',
	'administrator/components/com_jadmintools/fof/inflector.php',
	'administrator/components/com_jadmintools/fof/input.php',
	'administrator/components/com_jadmintools/fof/model.php',
	'administrator/components/com_jadmintools/fof/query.abstract.php',
	'administrator/components/com_jadmintools/fof/query.element.php',
	'administrator/components/com_jadmintools/fof/query.mysql.php',
	'administrator/components/com_jadmintools/fof/query.mysqli.php',
	'administrator/components/com_jadmintools/fof/query.sqlazure.php',
	'administrator/components/com_jadmintools/fof/query.sqlsrv.php',
	'administrator/components/com_jadmintools/fof/table.php',
	'administrator/components/com_jadmintools/fof/template.utils.php',
	'administrator/components/com_jadmintools/fof/toolbar.php',
	'administrator/components/com_jadmintools/fof/view.csv.php',
	'administrator/components/com_jadmintools/fof/view.html.php',
	'administrator/components/com_jadmintools/fof/view.json.php',
	'administrator/components/com_jadmintools/fof/view.php',
);
$removeFolders = array(
	'administrator/components/com_admintools/views/ipautoban',
	'administrator/components/com_admintools/views/ipbl',
	'administrator/components/com_admintools/views/ipwl',
	'administrator/components/com_admintools/views/log',
);

// Joomla! 1.6 Beta 13+ hack
if( version_compare( JVERSION, '1.6.0', 'ge' ) && !defined('_AKEEBA_HACK') ) {
	return;
} else {
	global $akeeba_installation_has_run;
	if($akeeba_installation_has_run) return;
}

$db = JFactory::getDBO();

// Version 1.0.b1 to 1.0.RC1 updates (performs autodection before running the commands)
// for #__admintools_ipblock
$sql = 'SHOW CREATE TABLE `#__admintools_ipblock`';
$db->setQuery($sql);
$ctableAssoc = $db->loadResultArray(1);
$ctable = empty($ctableAssoc) ? '' : $ctableAssoc[0];
if(!strstr($ctable, '`description`'))
{
	if($db->hasUTF())
	{
		$charset = 'CHARSET=utf8';
	}
	else
	{
		$charset = '';
	}

	$sql = <<<ENDSQL
DROP TABLE IF EXISTS `#__admintools_ipblock_bak`;
ENDSQL;
	$db->setQuery($sql);
	$status = $db->query();
	if(!$status && ($db->getErrorNum() != 1060)) {
		$errors[] = $db->getErrorMsg(true);
	}

$sql = <<<ENDSQL
CREATE TABLE IF NOT EXISTS `#__admintools_ipblock_bak` (
	`id` SERIAL,
	`ip` VARCHAR(255),
	`description` VARCHAR(255)
) DEFAULT COLLATE utf8_general_ci;

ENDSQL;
	$db->setQuery($sql);
	$status = $db->query();
	if(!$status && ($db->getErrorNum() != 1060)) {
		$errors[] = $db->getErrorMsg(true);
	}

	$sql = <<<ENDSQL
INSERT IGNORE INTO `#__admintools_ipblock_bak`
	(`id`,`ip`,`description`)
SELECT `id`,`ip`, '' as `description` FROM `#__admintools_ipblock`;
ENDSQL;
	$db->setQuery($sql);
	$status = $db->query();
	if(!$status && ($db->getErrorNum() != 1060)) {
		$errors[] = $db->getErrorMsg(true);
	}

	$sql = <<<ENDSQL
DROP TABLE IF EXISTS `#__admintools_ipblock`;
ENDSQL;
	$db->setQuery($sql);
	$status = $db->query();
	if(!$status && ($db->getErrorNum() != 1060)) {
		$errors[] = $db->getErrorMsg(true);
	}


	$sql = <<<ENDSQL
CREATE TABLE IF NOT EXISTS `#__admintools_ipblock` (
	`id` SERIAL,
	`ip` VARCHAR(255),
	`description` VARCHAR(255)
) DEFAULT COLLATE utf8_general_ci;

ENDSQL;

	$db->setQuery($sql);
	$status = $db->query();
	if(!$status && ($db->getErrorNum() != 1060)) {
		$errors[] = $db->getErrorMsg(true);
	}

	$sql = <<<ENDSQL
INSERT IGNORE INTO `#__admintools_ipblock` SELECT * FROM `#__admintools_ipblock_bak`;
ENDSQL;
	$db->setQuery($sql);
	$status = $db->query();
	if(!$status && ($db->getErrorNum() != 1060)) {
		$errors[] = $db->getErrorMsg(true);
	}

	$sql = <<<ENDSQL
DROP TABLE IF EXISTS `#__admintools_ipblock_bak`;
ENDSQL;
	$db->setQuery($sql);
	$status = $db->query();
	if(!$status && ($db->getErrorNum() != 1060)) {
		$errors[] = $db->getErrorMsg(true);
	}

}

// Version 1.0.b1 to 1.0.RC1 updates (performs autodection before running the commands)
// for #__admintools_adminiplist
$sql = 'SHOW CREATE TABLE `#__admintools_adminiplist`';
$db->setQuery($sql);
$ctableAssoc = $db->loadResultArray(1);
$ctable = empty($ctableAssoc) ? '' : $ctableAssoc[0];
if(!strstr($ctable, '`description`'))
{
	if($db->hasUTF())
	{
		$charset = 'CHARSET=utf8';
	}
	else
	{
		$charset = '';
	}

	$sql = <<<ENDSQL
DROP TABLE IF EXISTS `#__admintools_adminiplist_bak`;
ENDSQL;
	$db->setQuery($sql);
	$status = $db->query();
	if(!$status && ($db->getErrorNum() != 1060)) {
		$errors[] = $db->getErrorMsg(true);
	}

$sql = <<<ENDSQL
CREATE TABLE IF NOT EXISTS `#__admintools_adminiplist_bak` (
	`id` SERIAL,
	`ip` VARCHAR(255),
	`description` VARCHAR(255)
) DEFAULT COLLATE utf8_general_ci;

ENDSQL;
	$db->setQuery($sql);
	$status = $db->query();
	if(!$status && ($db->getErrorNum() != 1060)) {
		$errors[] = $db->getErrorMsg(true);
	}

	$sql = <<<ENDSQL
INSERT IGNORE INTO `#__admintools_adminiplist_bak`
	(`id`,`ip`,`description`)
SELECT `id`,`ip`, '' as `description` FROM `#__admintools_adminiplist`;
ENDSQL;
	$db->setQuery($sql);
	$status = $db->query();
	if(!$status && ($db->getErrorNum() != 1060)) {
		$errors[] = $db->getErrorMsg(true);
	}

	$sql = <<<ENDSQL
DROP TABLE IF EXISTS `#__admintools_adminiplist`;
ENDSQL;
	$db->setQuery($sql);
	$status = $db->query();
	if(!$status && ($db->getErrorNum() != 1060)) {
		$errors[] = $db->getErrorMsg(true);
	}


	$sql = <<<ENDSQL
CREATE TABLE IF NOT EXISTS `#__admintools_adminiplist` (
	`id` SERIAL,
	`ip` VARCHAR(255),
	`description` VARCHAR(255)
) DEFAULT COLLATE utf8_general_ci;

ENDSQL;

	$db->setQuery($sql);
	$status = $db->query();
	if(!$status && ($db->getErrorNum() != 1060)) {
		$errors[] = $db->getErrorMsg(true);
	}

	$sql = <<<ENDSQL
INSERT IGNORE INTO `#__admintools_adminiplist` SELECT * FROM `#__admintools_adminiplist_bak`;
ENDSQL;
	$db->setQuery($sql);
	$status = $db->query();
	if(!$status && ($db->getErrorNum() != 1060)) {
		$errors[] = $db->getErrorMsg(true);
	}

	$sql = <<<ENDSQL
DROP TABLE IF EXISTS `#__admintools_adminiplist_bak`;
ENDSQL;
	$db->setQuery($sql);
	$status = $db->query();
	if(!$status && ($db->getErrorNum() != 1060)) {
		$errors[] = $db->getErrorMsg(true);
	}

}

// Version 1.x/2.x to 2.1
// ALTER TABLE jos_admintools_log ADD COLUMN `extradata` mediumtext AFTER `reason`
$sql = 'SHOW CREATE TABLE `#__admintools_log`';
$db->setQuery($sql);
$ctableAssoc = $db->loadResultArray(1);
$ctable = empty($ctableAssoc) ? '' : $ctableAssoc[0];
if(!strstr($ctable, '`extradata`')) {
	if($db->hasUTF())
	{
		$charset = 'CHARSET=utf8';
	}
	else
	{
		$charset = '';
	}

	$sql = <<<ENDSQL
DROP TABLE IF EXISTS `#__admintools_log_bak`;
ENDSQL;
	$db->setQuery($sql);
	$status = $db->query();
	if(!$status && ($db->getErrorNum() != 1060)) {
		$errors[] = $db->getErrorMsg(true);
	}

$sql = <<<ENDSQL
CREATE TABLE IF NOT EXISTS `#__admintools_log_bak` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `logdate` datetime NOT NULL,
  `ip` varchar(40) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `reason` varchar(255) DEFAULT 'other',
  `extradata` mediumtext,
  UNIQUE KEY `id` (`id`)
) $charset;

ENDSQL;
	$db->setQuery($sql);
	$status = $db->query();
	if(!$status && ($db->getErrorNum() != 1060)) {
		$errors[] = $db->getErrorMsg(true);
	}

	$sql = <<<ENDSQL
INSERT IGNORE INTO `#__admintools_log_bak`
	(`id`,`logdate`,`ip`, `url`, `reason`)
SELECT `id`,`logdate`,`ip`, `url`, `reason` FROM `#__admintools_log`;
ENDSQL;
	$db->setQuery($sql);
	$status = $db->query();
	if(!$status && ($db->getErrorNum() != 1060)) {
		$errors[] = $db->getErrorMsg(true);
	}

	$sql = <<<ENDSQL
DROP TABLE IF EXISTS `#__admintools_log`;
ENDSQL;
	$db->setQuery($sql);
	$status = $db->query();
	if(!$status && ($db->getErrorNum() != 1060)) {
		$errors[] = $db->getErrorMsg(true);
	}


	$sql = <<<ENDSQL
CREATE TABLE IF NOT EXISTS `#__admintools_log` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `logdate` datetime NOT NULL,
  `ip` varchar(40) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `reason` varchar(255) DEFAULT 'other',
  `extradata` mediumtext,
  UNIQUE KEY `id` (`id`)
) $charset;

ENDSQL;

	$db->setQuery($sql);
	$status = $db->query();
	if(!$status && ($db->getErrorNum() != 1060)) {
		$errors[] = $db->getErrorMsg(true);
	}

	$sql = <<<ENDSQL
INSERT IGNORE INTO `#__admintools_log` SELECT * FROM `#__admintools_log_bak`;
ENDSQL;
	$db->setQuery($sql);
	$status = $db->query();
	if(!$status && ($db->getErrorNum() != 1060)) {
		$errors[] = $db->getErrorMsg(true);
	}

	$sql = <<<ENDSQL
DROP TABLE IF EXISTS `#__admintools_log_bak`;
ENDSQL;
	$db->setQuery($sql);
	$status = $db->query();
	if(!$status && ($db->getErrorNum() != 1060)) {
		$errors[] = $db->getErrorMsg(true);
	}
}

// Modules & plugins installation

jimport('joomla.installer.installer');
$db = & JFactory::getDBO();
$status = new JObject();
$status->modules = array();
$status->plugins = array();
if( version_compare( JVERSION, '1.6.0', 'ge' ) ) {
	if(!isset($parent))
	{
		$parent = $this->parent;
	}
	$src = $parent->getPath('source');
} else {
	$src = $this->parent->getPath('source');
}

// Modules installation
if(count($installation_queue['modules'])) {
	foreach($installation_queue['modules'] as $folder => $modules) {
		if(count($modules)) foreach($modules as $module => $modulePreferences) {
			// Install the module
			if(empty($folder)) $folder = 'site';
			$path = "$src/modules/$folder/$module";
			if(!is_dir($path)) {
				$path = "$src/modules/$folder/mod_$module";
			}
			if(!is_dir($path)) {
				$path = "$src/modules/$module";
			}
			if(!is_dir($path)) {
				$path = "$src/modules/mod_$module";
			}
			if(!is_dir($path)) continue;
			// Was the module already installed?
			$sql = 'SELECT COUNT(*) FROM #__modules WHERE `module`='.$db->Quote('mod_'.$module);
			$db->setQuery($sql);
			$count = $db->loadResult();
			$installer = new JInstaller;
			$result = $installer->install($path);
			$status->modules[] = array('name'=>'mod_'.$module, 'client'=>$folder, 'result'=>$result);
			// Modify where it's published and its published state
			if(!$count) {
				// A. Position and state
				list($modulePosition, $modulePublished) = $modulePreferences;
				if(version_compare(JVERSION, '2.5.0', 'ge') && ($modulePosition == 'cpanel')) {
					$modulePosition = 'icon';
				}
				$sql = "UPDATE #__modules SET position=".$db->Quote($modulePosition);
				if($modulePublished) $sql .= ', published=1';
				$sql .= ' WHERE `module`='.$db->Quote('mod_'.$module);
				$db->setQuery($sql);
				$db->query();
				if(version_compare(JVERSION, '1.7.0', 'ge')) {
					// B. Change the ordering of back-end modules to 1 + max ordering in J! 1.7+
					if($folder == 'admin') {
						$query = $db->getQuery(true);
						$query->select('MAX('.$db->nq('ordering').')')
							->from($db->nq('#__modules'))
							->where($db->nq('position').'='.$db->q($modulePosition));
						$db->setQuery($query);
						$position = $db->loadResult();
						$position++;
						
						$query = $db->getQuery(true);
						$query->update($db->nq('#__modules'))
							->set($db->nq('ordering').' = '.$db->q($position))
							->where($db->nq('module').' = '.$db->q('mod_'.$module));
						$db->setQuery($query);
						$db->query();
					}
					// C. Link to all pages on Joomla! 1.7+
					$query = $db->getQuery(true);
					$query->select('id')->from($db->nq('#__modules'))
						->where($db->nq('module').' = '.$db->q('mod_'.$module));
					$db->setQuery($query);
					$moduleid = $db->loadResult();
					
					$query = $db->getQuery(true);
					$query->select('*')->from($db->nq('#__modules_menu'))
						->where($db->nq('moduleid').' = '.$db->q($moduleid));
					$db->setQuery($query);
					$assignments = $db->loadObjectList();
					$isAssigned = !empty($assignments);
					if(!$isAssigned) {
						$o = (object)array(
							'moduleid'	=> $moduleid,
							'menuid'	=> 0
						);
						$db->insertObject('#__modules_menu', $o);
					}
				}
			}
		}
	}
}

// Plugins installation
if(count($installation_queue['plugins'])) {
	foreach($installation_queue['plugins'] as $folder => $plugins) {
		if(count($plugins)) foreach($plugins as $plugin => $published) {
			$path = "$src/plugins/$folder/$plugin";
			if(!is_dir($path)) {
				$path = "$src/plugins/$folder/plg_$plugin";
			}
			if(!is_dir($path)) {
				$path = "$src/plugins/$plugin";
			}
			if(!is_dir($path)) {
				$path = "$src/plugins/plg_$plugin";
			}
			if(!is_dir($path)) continue;
			
			// Was the plugin already installed?
			if( version_compare( JVERSION, '1.6.0', 'ge' ) ) {
				$query = "SELECT COUNT(*) FROM  #__extensions WHERE element=".$db->Quote($plugin)." AND folder=".$db->Quote($folder);
			} else {
				$query = "SELECT COUNT(*) FROM  #__plugins WHERE element=".$db->Quote($plugin)." AND folder=".$db->Quote($folder);
			}
			$db->setQuery($query);
			$count = $db->loadResult();
			
			$installer = new JInstaller;
			$result = $installer->install($path);
			$status->plugins[] = array('name'=>'plg_'.$plugin,'group'=>$folder, 'result'=>$result);
			
			if($published && !$count) {
				if( version_compare( JVERSION, '1.6.0', 'ge' ) ) {
					$query = "UPDATE #__extensions SET enabled=1 WHERE element=".$db->Quote($plugin)." AND folder=".$db->Quote($folder);
				} else {
					$query = "UPDATE #__plugins SET published=1 WHERE element=".$db->Quote($plugin)." AND folder=".$db->Quote($folder);
				}
				$db->setQuery($query);
				$db->query();
			}
		}
	}
}

// CLI files installation
jimport('joomla.filesystem.file');
if(version_compare(JVERSION, '1.6.0', 'ge')) {
	if(JFile::exists($src.'/cli/admintools-filescanner.php')) {
		JFile::copy($src.'/cli/admintools-filescanner.php', JPATH_ROOT.'/cli/admintools-filescanner.php');
	}
}

// Remove any old files/directories from version 1.x, 2.0 or 2.1
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
foreach($removeFiles as $removedFile) {
	$removePath = JPATH_SITE.'/'.$removedFile;
	$exists = @file_exists($removePath);
	if(!$exists) $exists = JFile::exists($removePath);
	if($exists) {
		$removed = @unlink($removePath);
		if(!$removed) JFile::delete($removePath);
	}
}
foreach($removeFolders as $removedFolder) {
	$removePath = JPATH_SITE.'/'.$removedFolder;
	$exists = is_dir($removePath);
	if(!$exists) $exists = JFolder::exists($removePath);
	if($exists) {
		$removed = @rmdir($removePath);
		if(!$removed) JFolder::delete($removePath);
	}
}

// Install the FOF framework
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');
jimport('joomla.utilities.date');
$source = $src.'/fof';
if(!defined('JPATH_LIBRARIES')) {
	$target = JPATH_ROOT.'/libraries/fof';
} else {
	$target = JPATH_LIBRARIES.'/fof';
}
$haveToInstallFOF = false;
if(!JFolder::exists($target)) {
	JFolder::create($target);
	$haveToInstallFOF = true;
} else {
	$fofVersion = array();
	if(JFile::exists($target.'/version.txt')) {
		$rawData = JFile::read($target.'/version.txt');
		$info = explode("\n", $rawData);
		$fofVersion['installed'] = array(
			'version'	=> trim($info[0]),
			'date'		=> new JDate(trim($info[1]))
		);
	} else {
		$fofVersion['installed'] = array(
			'version'	=> '0.0',
			'date'		=> new JDate('2011-01-01')
		);
	}
	$rawData = JFile::read($source.'/version.txt');
	$info = explode("\n", $rawData);
	$fofVersion['package'] = array(
		'version'	=> trim($info[0]),
		'date'		=> new JDate(trim($info[1]))
	);
	
	$haveToInstallFOF = $fofVersion['package']['date']->toUNIX() > $fofVersion['installed']['date']->toUNIX();
}

if($haveToInstallFOF) {
	$installedFOF = true;
	$files = JFolder::files($source);
	if(!empty($files)) {
		foreach($files as $file) {
			$installedFOF = $installedFOF && JFile::copy($source.'/'.$file, $target.'/'.$file);
		}
	}
}

$akeeba_installation_has_run = true;
?>

<?php $rows = 0;?>
<div style="margin: 1em; font-size: 14pt; background-color: #fffff9; color: black">
	You can download translation files <a href="http://akeeba-cdn.s3-website-eu-west-1.amazonaws.com/language/admintools/">directly from our CDN page</a>.
</div>
<img src="<?php echo rtrim(JURI::base(),'/') ?>/../media/com_admintools/images/admintools-48.png" width="48" height="48" alt="Admin Tools" align="right" />
<h2><?php echo JText::_('Admin Tools Installation Status'); ?></h2>
<table class="adminlist">
	<thead>
		<tr>
			<th class="title" colspan="2"><?php echo JText::_('Extension'); ?></th>
			<th width="30%"><?php echo JText::_('Status'); ?></th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<td colspan="3"></td>
		</tr>
	</tfoot>
	<tbody>
		<tr class="row0">
			<td class="key" colspan="2"><?php echo 'Admin Tools '.JText::_('Component'); ?></td>
			<td><strong><?php echo JText::_('Installed'); ?></strong></td>
		</tr>
		<tr class="row1">
			<td class="key" colspan="2">
				<strong>Framework on Framework (FOF)</strong>
			</td>
			<td><strong>
				<span style="color: <?php echo $haveToInstallFOF ? ($installedFOF?'green':'red') : '#660' ?>; font-weight: bold;">
					<?php echo $haveToInstallFOF ? ($installedFOF ?'Installed':'Not Installed') : 'Already up-to-date'; ?>
				</span>	
			</strong></td>
		</tr>
		<?php if (count($status->modules)) : ?>
		<tr>
			<th><?php echo JText::_('Module'); ?></th>
			<th><?php echo JText::_('Client'); ?></th>
			<th></th>
		</tr>
		<?php foreach ($status->modules as $module) : ?>
		<tr class="row<?php echo (++ $rows % 2); ?>">
			<td class="key"><?php echo $module['name']; ?></td>
			<td class="key"><?php echo ucfirst($module['client']); ?></td>
			<td><strong><?php echo ($module['result'])?JText::_('Installed'):JText::_('Not installed'); ?></strong></td>
		</tr>
		<?php endforeach;?>
		<?php endif;?>
		<?php if (count($status->plugins)) : ?>
		<tr>
			<th><?php echo JText::_('Plugin'); ?></th>
			<th><?php echo JText::_('Group'); ?></th>
			<th></th>
		</tr>
		<?php foreach ($status->plugins as $plugin) : ?>
		<tr class="row<?php echo (++ $rows % 2); ?>">
			<td class="key"><?php echo ucfirst($plugin['name']); ?></td>
			<td class="key"><?php echo ucfirst($plugin['group']); ?></td>
			<td><strong><?php echo ($plugin['result'])?JText::_('Installed'):JText::_('Not installed'); ?></strong></td>
		</tr>
		<?php endforeach; ?>
		<?php endif; ?>
	</tbody>
</table>