<?php
/**
 * @package AkeebaBackup
 * @subpackage BackupIconModule
 * @copyright Copyright (c)2009-2013 Nicholas K. Dionysopoulos
 * @license GNU General Public License version 3, or later
 * @since 2.2
 *
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

// Check for PHP4
if(defined('PHP_VERSION')) {
	$version = PHP_VERSION;
} elseif(function_exists('phpversion')) {
	$version = phpversion();
} else {
	// No version info. I'll lie and hope for the best.
	$version = '5.0.0';
}

// Old PHP version detected. EJECT! EJECT! EJECT!
if(!version_compare($version, '5.3.0', '>=')) return;

// Make sure Akeeba Backup is installed
if(!file_exists(JPATH_ADMINISTRATOR.'/components/com_akeeba')) {
	return;
}

if(!defined('FOF_INCLUDED')) {
	include_once JPATH_LIBRARIES.'/fof/include.php';
	if(!defined('FOF_INCLUDED') || !class_exists('FOFForm', true))
	{
		return;
	}
}

// Deactivate ourselves. This way if the installation of the component is hosed
// we won't bring down the entire site.
$db = JFactory::getDbo();
$query = $db->getQuery(true)
	->select($db->qn('id'))
	->from($db->qn('#__modules'))
	->where($db->qn('published').' = '.$db->q('1'))
	->where($db->qn('module').' = '.$db->q('mod_akadmin'))
	->where($db->qn('client_id').' = '.$db->q('1'));
$db->setQuery($query);
$ids = $db->loadColumn(0);
$id = $ids[0];

$query = $db->getQuery(true)
	->update($db->qn('#__modules'))
	->set($db->qn('published').' = '.$db->q('0'))
	->where($db->qn('id').' IN ('.implode(',',$ids).')');
$db->setQuery($query);
$db->execute();

// Load FOF
if(!defined('FOF_INCLUDED')) {
	include_once JPATH_SITE.'/libraries/fof/include.php';
}
		
// Timezone fix; avoids errors printed out by PHP 5.3.3+ (thanks Yannick!)
if(function_exists('date_default_timezone_get') && function_exists('date_default_timezone_set')) {
	if(function_exists('error_reporting')) {
		$oldLevel = error_reporting(0);
	}
	$serverTimezone = @date_default_timezone_get();
	if(empty($serverTimezone) || !is_string($serverTimezone)) $serverTimezone = 'UTC';
	if(function_exists('error_reporting')) {
		error_reporting($oldLevel);
	}
	@date_default_timezone_set( $serverTimezone);
}

/*
 * Hopefuly, if we are still here, the site is running on at least PHP5. This means that
 * including the Akeeba Backup factory class will not throw a White Screen of Death, locking
 * the administrator out of the back-end.
 */

// Make sure Akeeba Backup is installed, or quit
$akeeba_installed = @file_exists(JPATH_ADMINISTRATOR.'/components/com_akeeba/akeeba/factory.php');
if(!$akeeba_installed) return;

// Make sure Akeeba Backup is enabled
JLoader::import('joomla.application.component.helper');
if (!JComponentHelper::isEnabled('com_akeeba', true))
{
	//JError::raiseError('E_JPNOTENABLED', JText('MOD_AKADMIN_AKEEBA_NOT_ENABLED'));
	return;
}

// Joomla! 1.6 or later - check ACLs (and not display when the site is bricked,
// hopefully resulting in no stupid emails from users who think that somehow
// Akeeba Backup crashed their site). It also not displays the button to people
// who are not authorised to take backups - which makes perfect sense!
$continueLoadingIcon = true;
$user = JFactory::getUser();
if (!$user->authorise('akeeba.backup', 'com_akeeba')) {
	$continueLoadingIcon = false;
}

// Do we really, REALLY have Akeeba Engine?
if($continueLoadingIcon) {
	if(!defined('AKEEBAENGINE')) {
		define('AKEEBAENGINE', 1); // Required for accessing Akeeba Engine's factory class
	}
	try {
		@include_once JPATH_ADMINISTRATOR.'/components/com_akeeba/akeeba/factory.php';
		if(!class_exists('AEFactory', false)) {
			$continueLoadingIcon = false;
		}
	} catch(Exception $e) {
		$continueLoadingIcon = false;
	}
}

if(!$continueLoadingIcon) {
	// Reenable ourselves
	$query = $db->getQuery(true)
		->update($db->qn('#__modules'))
		->set($db->qn('published').' = '.$db->q('1'))
		->where($db->qn('id').' IN ('.implode(',',$ids).')');
	$db->setQuery($query);
	$db->execute();

	return;
}
unset($continueLoadingIcon);

// Load custom CSS
$document = JFactory::getDocument();
$document->addStyleSheet(JURI::base().'modules/mod_akadmin/css/mod_akadmin.css');

// Load the language files
$jlang = JFactory::getLanguage();
$jlang->load('mod_akadmin', JPATH_ADMINISTRATOR, 'en-GB', true);
$jlang->load('mod_akadmin', JPATH_ADMINISTRATOR, $jlang->getDefault(), true);
$jlang->load('mod_akadmin', JPATH_ADMINISTRATOR, null, true);

// Initialize defaults
$image = "akeeba-48.png";
$label = JText::_('MOD_AKADMIN_LBL_AKEEBA');

if( $params->get('enablewarning', 0) == 0 )
{
	// Process warnings
	$warning = false;

	require_once JPATH_ADMINISTRATOR.'/components/com_akeeba/akeeba/factory.php';
	$aeconfig = AEFactory::getConfiguration();
	AEPlatform::getInstance()->load_configuration();
	
	// Get latest non-SRP backup ID
	$filters = array(
		array(
			'field'			=> 'tag',
			'operand'		=> '<>',
			'value'			=> 'restorepoint'
		)
	);
	$ordering = array(
		'by'		=> 'backupstart',
		'order'		=> 'DESC'
	);
	require_once JPATH_ADMINISTRATOR.'/components/com_akeeba/models/statistics.php';
	$model = new AkeebaModelStatistics();
	$list = $model->getStatisticsListWithMeta(false, $filters, $ordering);
	
	if(!empty($list)) {
		$record = (object)array_shift($list);
	} else {
		$record = null;
	}
	
	// Process "failed backup" warnings, if specified
	if( $params->get('warnfailed', 0) == 0 )
	{
		if(!is_null($record))
		{
			$warning = (($record->status == 'fail') || ($record->status == 'run'));
		}
	}

	// Process "stale backup" warnings, if specified
	if(is_null($record))
	{
		$warning = true;
	}
	else
	{
		$maxperiod = $params->get('maxbackupperiod', 24);
		JLoader::import('joomla.utilities.date');
		$lastBackupRaw = $record->backupstart;
		$lastBackupObject = new JDate($lastBackupRaw);
		$lastBackup = $lastBackupObject->toUnix(false);
		$maxBackup = time() - $maxperiod * 3600;
		if(!$warning) $warning = ($lastBackup < $maxBackup);
	}

	if($warning)
	{
		$image = 'akeeba-warning-48.png';
		$label = JText::_('MOD_AKADMIN_LBL_BACKUPREQUIRED');
	}
}

// Load the Akeeba Backup configuration and check user access permission
if(!defined('AKEEBAENGINE'))
{
	define('AKEEBAENGINE', 1); // Required for accessing Akeeba Engine's factory class
}
require_once JPATH_ADMINISTRATOR.'/components/com_akeeba/akeeba/factory.php';
$aeconfig = AEFactory::getConfiguration();
$user = JFactory::getUser();
$showModule = true;
unset($aeconfig);

// Administrator access allowed
$extraclass = 'icon16';

unset($user);

if($showModule):?>

<?php if(version_compare(JVERSION, '2.5', 'ge')):?>
<div class="alert alert-info">
	<?php echo JText::_('MOD_AKADMIN_LBL_NOTSUPPORTEDINJOOMLA3') ?>
</div>
<?php
// Enable our Joomla! version check plugin, disable Joomla!'s own plugin (which,
// incidentally, was written by me too!) and disable this module as well. Ready?
// Fight!

$db = JFactory::getDbo();

// Step 1 - Enable our plugin
$query = $db->getQuery(true)
	->update($db->qn('#__extensions'))
	->set($db->qn('enabled').' = '.$db->q('1'))
	->where($db->qn('element').' = '.$db->q('akeebabackup'))
	->where($db->qn('folder').' = '.$db->q('quickicon'));
$db->setQuery($query);
$db->execute();

// Step 2 - Disable this module
$query = $db->getQuery(true)
	->update($db->qn('#__modules'))
	->where($db->qn('module').' = '.$db->q('mod_akadmin'))
	->set($db->qn('published').' = '.$db->q('0'));
$db->setQuery($query);
$db->execute();

else:?>

<div class="icon-wrapper" id="akadminicon">
	<div class="akcpanel">
		<div class="icon-wrapper">
			<div class="icon <?php echo $extraclass ?>">
				<a href="index.php?option=com_akeeba&view=backup">
					<img src="../media/com_akeeba/icons/<?php echo $image ?>" />
					<span><?php echo $label; ?></span>
				</a>
			</div>
		</div>
	</div>
</div>
<script lang="text/javascript" language="javascript">
	var akeebabackupIcon = $('akadminicon');
	try {
		var akeebabackupIconParent = $('akadminicon').getParent().getParent();
		if(akeebabackupIconParent.attributes.class.textContent == 'panel') {
			akeebabackupIconParent.setStyle('display','none');
		}
	} catch(e) {	
	}
	try {
		$$('div.cpanel')[0].grab(akeebabackupIcon)
	} catch(e) {
	}
</script>

<?php
// Reenable ourselves
$query = $db->getQuery(true)
	->update($db->qn('#__modules'))
	->set($db->qn('published').' = '.$db->q('1'))
	->where($db->qn('id').' IN ('.implode(',',$ids).')');
$db->setQuery($query);
$db->execute();
?>

<?php endif;?>
<?php endif;?>