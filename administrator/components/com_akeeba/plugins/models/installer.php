<?php
/**
 * @package AkeebaBackup
 * @copyright Copyright (c)2009-2013 Nicholas K. Dionysopoulos
 * @license GNU General Public License version 3, or later
 *
 * @since 3.3
 */

defined('_JEXEC') or die('');

JLoader::import('joomla.application.component.model');
JLoader::import('joomla.installer.installer');
JLoader::import('joomla.installer.helper');

if(!class_exists('JoomlaCompatModel')) {
	if(interface_exists('JModel')) {
		abstract class JoomlaCompatModel extends JModelLegacy {}
	} else {
		class JoomlaCompatModel extends JModel {}
	}
}

class AkeebaModelInstaller extends JoomlaCompatModel
{
	/** @var object JTable object */
	var $_table = null;

	/** @var object JTable object */
	var $_url = null;

	/**
	 * Overridden constructor
	 * @access	protected
	 */
	public function __construct()
	{
		parent::__construct();

	}
	
	/**
	 * Fetches a package from the upload for and saves it to the temporary directory
	 */
	public function upload()
	{
		// Get the uploaded file information
		$userfile = JRequest::getVar('install_package', null, 'files', 'array' );

		// Make sure that file uploads are enabled in php
		if (!(bool) ini_get('file_uploads')) {
			JError::raiseWarning('', JText::_('COM_INSTALLER_MSG_INSTALL_WARNINSTALLFILE'));
			return false;
		}

		// Make sure that zlib is loaded so that the package can be unpacked
		if (!extension_loaded('zlib')) {
			JError::raiseWarning('', JText::_('COM_INSTALLER_MSG_INSTALL_WARNINSTALLZLIB'));
			return false;
		}

		// If there is no uploaded file, we have a problem...
		if (!is_array($userfile) ) {
			JError::raiseWarning('', JText::_('COM_INSTALLER_MSG_INSTALL_NO_FILE_SELECTED'));
			return false;
		}

		// Check if there was a problem uploading the file.
		if ( $userfile['error'] || $userfile['size'] < 1 )
		{
			JError::raiseWarning('', JText::_('COM_INSTALLER_MSG_INSTALL_WARNINSTALLUPLOADERROR'));
			return false;
		}

		// Build the appropriate paths
		$config = JFactory::getConfig();
		if(version_compare(JVERSION, '3.0', 'ge')) {
			$tmp_dest 	= $config->get('tmp_path').'/'.$userfile['name'];
		} else {
			$tmp_dest 	= $config->getValue('config.tmp_path').'/'.$userfile['name'];
		}
		$tmp_src	= $userfile['tmp_name'];

		// Move uploaded file
		JLoader::import('joomla.filesystem.file');
		$uploaded = JFile::upload($tmp_src, $tmp_dest);

		// Store the uploaded package's location
		$session = JFactory::getSession();
		$session->set('compressed_package', $tmp_dest, 'akeeba');
		
		return true;
	}
	
	public function download()
	{
		// Get a database connector
		$db = JFactory::getDBO();

		// Get the URL of the package to install
		$url = JRequest::getString('install_url');

		// Did you give us a URL?
		if (!$url) {
			JError::raiseWarning('', JText::_('COM_INSTALLER_MSG_INSTALL_ENTER_A_URL'));
			return false;
		}

		// Download the package at the URL given
		$p_file = JInstallerHelper::downloadPackage($url);

		// Was the package downloaded?
		if (!$p_file) {
			JError::raiseWarning('', JText::_('COM_INSTALLER_MSG_INSTALL_INVALID_URL'));
			return false;
		}
		
		$config = JFactory::getConfig();
		if(version_compare(JVERSION, '3.0', 'ge')) {
			$tmp_dest 	= $config->get('tmp_path');
		} else {
			$tmp_dest 	= $config->getValue('config.tmp_path');
		}

		// Store the uploaded package's location
		$session = JFactory::getSession();
		$session->set('compressed_package', $tmp_dest.'/'.$p_file, 'akeeba');
		
		return true;
	}
	
	function extract()
	{
		$session = JFactory::getSession();
		$compressed_package = $session->get('compressed_package', null, 'akeeba');
		
		// Do we have a compressed package?
		if(is_null($compressed_package)) {
			JError::raiseWarning('', JText::_('@todo - TRANSLATE: No package specified'));
			return false;
		}
		
		// Extract the package
		$package = JInstallerHelper::unpack($compressed_package);
		$session->set('package', $package, 'akeeba');
		
		return true;
	}
	
	function fromDirectory()
	{
		// Get the path to the package to install
		$p_dir = JRequest::getString('install_directory');
		$p_dir = JPath::clean( $p_dir );

		// Did you give us a valid directory?
		if (!is_dir($p_dir)) {
			JError::raiseWarning('', JText::_('COM_INSTALLER_MSG_INSTALL_PLEASE_ENTER_A_PACKAGE_DIRECTORY'));
			return false;
		}

		// Detect the package type
		$type = JInstallerHelper::detectType($p_dir);

		// Did you give us a valid package?
		if (!$type) {
			JError::raiseWarning('', JText::_('COM_INSTALLER_MSG_INSTALL_PATH_DOES_NOT_HAVE_A_VALID_PACKAGE'));
			return false;
		}

		$package['packagefile'] = null;
		$package['extractdir'] = null;
		$package['dir'] = $p_dir;
		$package['type'] = $type;
		
		$session = JFactory::getSession();
		$session->set('package', $package, 'akeeba');

		return true;
	}

	function realInstall()
	{
		$this->setState('action', 'install');
		
		$session = JFactory::getSession();
		$package = $session->get('package', null, 'akeeba');

		// Was the package unpacked?
		if (!$package || empty($package)) {
			$this->setState('message', JText::_('COM_INSTALLER_UNABLE_TO_FIND_INSTALL_PACKAGE'));
			return false;
		}

		// Get an installer instance
		$installer = JInstaller::getInstance();

		// Install the package
		if (!$installer->install($package['dir'])) {
			// There was an error installing the package
			$msg = JText::sprintf('COM_INSTALLER_INSTALL_ERROR', JText::_('COM_INSTALLER_TYPE_TYPE_'.$package['type']));
			$result = false;
		} else {
			// Package installed sucessfully
			$msg = JText::sprintf('COM_INSTALLER_INSTALL_SUCCESS', JText::_('COM_INSTALLER_TYPE_TYPE_'.$package['type']));
			$result = true;
		}

		// Set some model state values
		JFactory::getApplication()->enqueueMessage($msg);
		$this->setState('name', $installer->get('name'));
		$this->setState('result', $result);
		$this->setState('message', $installer->message);
		$this->setState('extension.message', $installer->get('extension.message'));
		$this->setState('extension_message', $installer->get('extension_message'));
		JFactory::getApplication()->setUserState('com_installer.redirect_url', $installer->get('redirect_url'));

		return $result;
	}

	function cleanUp()
	{
		$session = JFactory::getSession();
		$package = $session->get('package', '', 'akeeba');

		// Was the package unpacked?
		if (!$package || empty($package)) {
			$this->setState('message', JText::_('COM_INSTALLER_UNABLE_TO_FIND_INSTALL_PACKAGE'));
			return false;
		}
		
		// Cleanup the install files
		if (!is_file($package['packagefile'])) {
			$config = JFactory::getConfig();
			if(version_compare(JVERSION, '3.0', 'ge')) {
				$package['packagefile'] = $config->get('tmp_path').'/'.$package['packagefile'];
			} else {
				$package['packagefile'] = $config->getValue('config.tmp_path').'/'.$package['packagefile'];
			}
		}

		JInstallerHelper::cleanupInstall($package['packagefile'], $package['extractdir']);
		
		return true;
	}
	
	public function getExtensionName($p_dir)
	{
		// Search the install dir for an XML file
		$files = JFolder::files($p_dir, '\.xml$', 1, true);

		if ( ! count($files))
		{
			JError::raiseWarning(1, JText::_('JLIB_INSTALLER_ERROR_NOTFINDXMLSETUPFILE'));
			return false;
		}

		$cname = '';
		foreach ($files as $file)
		{
			try {
				$xml = new SimpleXMLElement($file, LIBXML_NONET, true);
			} catch(Exception $e) {
				continue;
			}
			
			if(($xml->getName() != 'install') && ($xml->getName() != 'extension'))
			{
				unset($xml);
				continue;
			}

			$type = (string)$xml->attributes()->type;
			
			// Get the name
			switch($type) {
				case 'component':
				case 'template':
					$name = (string)$xml->name;
					$name = JFilterInput::clean($name, 'cmd');
					if($type == 'template') {
						$cname = (string)$xml->attributes()->client;
					}
					break;

				case 'module':
				case 'plugin':
					$cname = (string)$xml->attributes()->client;
					$group = (string)$xml->attributes()->group;
					$element = $xml->files;
					if ( ($element instanceof SimpleXMLElement) && $element->count()) {
						foreach ($element->children() as $file) {
							if ($file->attributes()->$type) {
								$name = (string)$file->attributes()->$type;
								break;
							}
						}
					}
					break;
			}

			if(empty($name)) $name = false;

			if($name !== false) {
				// Make sure the extension is laready installed - otherwise there is no point!
				JLoader::import('joomla.filesystem.file');
				JLoader::import('joomla.filesystem.folder');
				switch($type) {
					case 'component':
						$name = strtolower($name);
						$name = substr($name,0,4) == 'com_' ? substr($name,4) : $name;
						if(
							!JFolder::exists(JPATH_ROOT.'/components/com_'.$name)
							&& !JFolder::exists(JPATH_ROOT.'/administrator/components/com_'.$name)
						) $name = false;
						break;

					case 'template':
						$base = ($cname == 'site') ? JPATH_ROOT : JPATH_ADMINISTRATOR;
						$base .= '/templates/';
						if(
							!JFolder::exists($base.$name)
						) {
							$name = strtolower($name);
							if(
								!JFolder::exists($base.$name)
							) $name = false;
						}
						break;

					case 'module':
						$base = ($cname == 'site') ? JPATH_ROOT : JPATH_ADMINISTRATOR;
						$base .= '/modules/';
						if(
							!JFolder::exists($base.'mod_'.$name)
						) {
							$name = strtolower($name);
							if(
								!JFolder::exists($base.'mod_'.$name)
							) $name = false;
						}
						
						break;

					case 'plugin':
						$base = JPATH_ROOT.'/plugins/'.$group.'/';
						if(
							!JFile::exists($base.'plg_'.$name.'.php')
							&& !JFile::exists($base.$name.'.php')
							&& !JFolder::exists($base.$name)
							&& !JFolder::exists($base.'plg_'.$name)
						) {
							$name = strtolower($name);
							if(
								!JFile::exists($base.'plg_'.$name.'.php')
								&& !JFile::exists($base.$name.'.php')
								&& !JFolder::exists($base.$name)
								&& !JFolder::exists($base.'plg_'.$name)
							) $name = false;
						}
						break;
						
					default:
						$name = false;
				}
			} else {
				return false;
			}

			// Free up memory from SimpleXML parser
			unset ($xml);
			
			if($name === false) {
				return false;
			}
			
			// Return the name
			return array(
				'name' => $name, 
				'client' => $cname,
				'group' => $group
			);
			
			// Free up memory
			unset ($xml);
			return $type;
		}

		return false;
	}
}