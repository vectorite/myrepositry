<?php
/**
 * @package LiveUpdate
 * @copyright Copyright ©2011 Nicholas K. Dionysopoulos / AkeebaBackup.com
 * @license GNU LGPLv3 or later <http://www.gnu.org/copyleft/lesser.html>
 */

defined('_JEXEC') or die();

/**
 * Configuration class for your extension's updates. Override to your liking.
 */
class LiveUpdateConfig extends LiveUpdateAbstractConfig
{
	var $_extensionName			= 'com_admintools';
	var $_versionStrategy		= 'different';
	/**
	var $_storageAdapter		= 'component';
	var $_storageConfig			= array(
			'extensionName'	=> 'com_admintools',
			'key'			=> 'liveupdate'
		);
	*/
	
	function __construct()
	{
		jimport('joomla.filesystem.file');
		$isPro = (ADMINTOOLS_PRO == 1);
		
		// Load the component parameters, not using JComponentHelper to avoid conflicts ;)
		jimport('joomla.html.parameter');
		jimport('joomla.application.component.helper');
		$db = JFactory::getDbo();
		if( version_compare(JVERSION,'1.6.0','ge') ) {
			$sql = $db->getQuery(true)
				->select($db->nameQuote('params'))
				->from($db->nameQuote('#__extensions'))
				->where($db->nameQuote('type').' = '.$db->quote('component'))
				->where($db->nameQuote('element').' = '.$db->quote('admintools'));
		} else {
			$sql = 'SELECT '.$db->nameQuote('params').' FROM '.$db->nameQuote('#__components').
				' WHERE '.$db->nameQuote('option').' = '.$db->Quote('admintools').
				" AND `parent` = 0 AND `menuid` = 0";
		}
		$db->setQuery($sql);
		$rawparams = $db->loadResult();
		if(version_compare(JVERSION, '1.6.0', 'ge')) {
			$params = new JRegistry();
			$params->loadJSON($rawparams);
		} else {
			$params = new JParameter($rawparams);
		}

		// Determine the appropriate update URL based on whether we're on Core or Professional edition
		if($isPro)
		{
			$this->_updateURL = 'http://cdn.akeebabackup.com/updates/atpro.ini';
			$this->_extensionTitle = 'Admin Tools Professional';
		}
		else
		{
			$this->_updateURL = 'http://cdn.akeebabackup.com/updates/atcore.ini';
			$this->_extensionTitle = 'Admin Tools Core';
		}
		
		// Set up the version strategy
		if(defined('ADMINTOOLS_VERSION')) {
			if(in_array(substr(ADMINTOOLS_VERSION, 0, 3), array('svn','dev','rev'))) {
				// Dev releases use the "newest" (date comparison) strategy.
				$this->_versionStrategy = 'newest';
			} else {
				// In all other cases, we check for a different version
				$this->_versionStrategy = 'different';
			}
		}
		
		// Get the minimum stability level for updates
		$this->_minStability = $params->getValue('minstability', 'alpha');
		
		// Should I use our private CA store?
		if(@file_exists(dirname(__FILE__).'/../assets/cacert.pem')) {
			$this->_requiresAuthorization = $isPro;
			$this->_cacerts = dirname(__FILE__).'/../assets/cacert.pem';
		}
		
		parent::__construct();		
	}
}