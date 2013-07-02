<?php
/**
 *  @package AdminTools
 *  @copyright Copyright (c)2010-2013 Nicholas K. Dionysopoulos
 *  @license GNU General Public License version 3, or later
 */

defined('_JEXEC') or die;

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
if(!version_compare($version, '5.0.0', '>=')) return;

// Make sure Admin Tools is installed, otherwise bail out
if(!file_exists(JPATH_ADMINISTRATOR.'/components/com_admintools')) {
	return;
}

// Joomla! version check
if(version_compare(JVERSION, '2.5', 'lt') && version_compare(JVERSION, '1.6.0', 'ge')) {
	// Joomla! 1.6.x and 1.7.x: sorry fellas, no go.
	return;
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

// Joomla! 1.6 detection
if(!defined('ADMINTOOLS_JVERSION'))
{
	JLoader::import('joomla.filesystem.file');
	if(!version_compare( JVERSION, '1.6.0', 'ge' )) {
		define('ADMINTOOLS_JVERSION','15');
	} else {
		define('ADMINTOOLS_JVERSION','16');
	}
}

/*
 * Hopefuly, if we are still here, the site is running on at least PHP5. This means that
 * including the Admin Tools model will not throw a White Screen of Death, locking
 * the administrator out of the back-end.
 */

// If JSON functions don't exist, load our compatibility layer
if( (!function_exists('json_encode')) || (!function_exists('json_decode')) )
{
	include_once JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_admintools'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'jsonlib.php';
}

// Make sure Admin Tools is installed, or quit
$at_installed = @file_exists(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_admintools'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'jupdate.php');
if(!$at_installed) return;

// Make sure Admin Tools is enabled
JLoader::import('joomla.application.component.helper');
if (!JComponentHelper::isEnabled('com_admintools', true))
{
	return;
}

// Joomla! 1.6 and later ACL check
if(version_compare(JVERSION, '1.6.0', 'ge')) {
	$user = JFactory::getUser();
	if (!$user->authorise('core.manage', 'com_admintools')) {
		return;
	}
}

/**
 * Joomla! udpate notification plugin
 *
 * @package     Joomla.Plugin
 * @subpackage  Quickicon.Joomlaupdate
 * @since       2.5
 */
class plgQuickiconAtoolsjupdatecheck extends JPlugin
{
	/**
	 * Constructor
	 *
	 * @param       object  $subject The object to observe
	 * @param       array   $config  An array that holds the plugin configuration
	 *
	 * @since       2.5
	 */
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}

	/**
	 * This method is called when the Quick Icons module is constructing its set
	 * of icons. You can return an array which defines a single icon and it will
	 * be rendered right after the stock Quick Icons.
	 *
	 * @param  $context  The calling context
	 *
	 * @return array A list of icon definition associative arrays, consisting of the
	 *				 keys link, image, text and access.
	 *
	 * @since       2.5
	 */
	public function onGetIcons($context)
	{
		if ($context != $this->params->get('context', 'mod_quickicon') || !JFactory::getUser()->authorise('core.manage', 'com_installer')) {
			return;
		}
		
		require_once JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_admintools'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'storage.php';
		require_once JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_admintools'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'jupdate.php';
		$model = new AdmintoolsModelJupdate();
		$uinfo = $model->getUpdateInfo();

		$ret = array(
			'link' => 'index.php?option=com_admintools&view=jupdate',
			'image' => 'download',
			'text' => JText::_('PLG_QUICKICON_ATOOLSJUPDATECHECK_OK'),
			'id' => 'plg_quickicon_atoolsjupdatecheck'
		);
		
		if(version_compare(JVERSION, '3.0', 'lt')) {
			$ret['image'] = 'header/icon-48-jupdate-uptodate.png';
		}
		
		if(is_null($uinfo->status)) {
			$image = "update_manual-32.png";
			$label = JText::_('PLG_QUICKICON_ATOOLSJUPDATECHECK_MANUAL');
			if(version_compare(JVERSION, '3.0', 'lt')) {
				$ret['image'] = 'header/icon-48-alert.png';
			}
		} elseif($uinfo->status == true) {
			$image = "update_warning-32.png";
			$label = JText::_('PLG_QUICKICON_ATOOLSJUPDATECHECK_WARNING');
			if(version_compare(JVERSION, '3.0', 'lt')) {
				$ret['image'] = 'header/icon-48-jupdate-updatefound.png';
			}
		}

		return array($ret);
	}
}
