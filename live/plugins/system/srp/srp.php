<?php
/**
 * @package AkeebaBackup
 * @subpackage SRP
 * @copyright Copyright (c)2009-2013 Nicholas K. Dionysopoulos
 * @license GNU General Public License version 3, or later
 *
 * @since 3.3
 */

defined('_JEXEC') or die();

// PHP version check
if(defined('PHP_VERSION')) {
	$version = PHP_VERSION;
} elseif(function_exists('phpversion')) {
	$version = phpversion();
} else {
	$version = '5.0.0'; // all bets are off!
}
if(!version_compare($version, '5.3.0', '>=')) return;

// Make sure Akeeba Backup is installed
if(!file_exists(JPATH_ADMINISTRATOR.'/components/com_akeeba')) {
	return;
}

// Load FOF
if(!defined('FOF_INCLUDED')) {
	include_once JPATH_SITE.'/libraries/fof/include.php';
}
if(!defined('FOF_INCLUDED') || !class_exists('FOFLess', true))
{
	return;
}

// If this is not the Professional release, bail out. So far I have only
// received complaints about this feature from users of the Core release
// who never bothered to read the documentation. FINE! If you are bitching
// about it, you don't get this feature (unless you are a developer who can
// come here and edit the code). Fair enough.
JLoader::import('joomla.filesystem.file');
$db = JFactory::getDBO();

// Is Akeeba Backup enabled?
$query = $db->getQuery(true)
	->select($db->qn('enabled'))
	->from($db->qn('#__extensions'))
	->where($db->qn('element').' = '.$db->q('com_akeeba'))
	->where($db->qn('type').' = '.$db->q('component'));
$db->setQuery($query);
$enabled = $db->loadResult();
if(!$enabled) return;

// Is it the Pro release?
include_once JPATH_ADMINISTRATOR.'/components/com_akeeba/version.php';

if(!defined('AKEEBA_PRO')) return;
if(!AKEEBA_PRO) return;

JLoader::import('joomla.application.plugin');

class plgSystemSRP extends JPlugin
{
	private $_enabled = true;
	
	public function __construct(&$subject, $config = array()) {
		parent::__construct($subject, $config);
		
		// Akeeba Backup version check
		JLoader::import('joomla.filesystem.file');
		$file = JPATH_ROOT.'/administrator/components/com_akeeba/version.php';
		if(!JFile::exists($file)) {
			// My local dev build doesn't have this file, so I cheat
			if(!JFile::exists(dirname($file).'/akeeba.xml')) {
				$this->_enabled = false;
			}
		} else {
			require_once $file;
			if(!version_compare(AKEEBA_VERSION, '3.3.a1', 'ge')) {
				// Check for dev release
				if(substr(AKEEBA_VERSION,0,3) == 'svn') {
					$svnVersion = (int)substr(AKEEBA_VERSION,3);
					$this->_enabled = $svnVersion >= 620;
				} else {
					$this->_enabled = false;
				}
			}
		}
		
		// SRP doesn't support non-MySQL databases yet
		if(!$this->isMySQL()) $this->_enabled = false;
	}
	
	public function onSRPEnabled()
	{
		return $this->_enabled;
	}
	
	public function onAfterInitialise()
	{
		// Make sure we are enabled (supported Akeeba Backup version)
		if(!$this->_enabled) return;
		
		// Make sure this is the back-end
		$app = JFactory::getApplication();
		if(!in_array($app->getName(),array('administrator','admin'))) return;
		
		// If the user tried to access Joomla!'s com_installer, hijack his
		// request and forward him to our private, improved implementation!
		if(class_exists('JInput')) {
			// I need to do this because Gantry is crappily coded and uses
			// option=com_gantry even though it installs no such component.
			// Using a new JInput instance works around their code. Dang!
			$ji = new JInput();
			$component = $ji->getCmd('option','');
			$task = $ji->getCmd('task','installform');
			$skipsrp = $ji->getInt('skipsrp', 0);
			$type = $ji->getCmd('type', '');
			$view = $ji->getCmd('view', '');
		} else {
			$component = JRequest::getCmd('option','');
			$task = JRequest::getCmd('task','installform');
			$skipsrp = JRequest::getInt('skipsrp', 0);
			$type = JRequest::getCmd('type', '');
			$view = JRequest::getCmd('view', '');
		}
		
		if( ($component == 'com_installer') && (($task == 'installform')||($task == 'installer')) && ($skipsrp != 1) && (empty($type)) ) {
			if(!empty($view) && ($view != 'install') && ($view != 'install.install')) {
				return;
			}
			
			JRequest::setVar('option','com_akeeba','GET');
			JRequest::setVar('view','installer','GET');
			JRequest::setVar('task','installform','GET');
		} elseif(($component == 'com_akeeba') && ($task == 'manage') && !empty($type)) {
			$app = JFactory::getApplication();
			$app->redirect('index.php?option=com_installer&task=manage&type='.$type);
		}
	}
	
	private function isMySQL()
	{
		$db = JFactory::getDbo();
		return strtolower(substr($db->name, 0, 5)) == 'mysql';
	}
}
