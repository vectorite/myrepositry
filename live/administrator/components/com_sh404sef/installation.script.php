<?php
/**
 * sh404SEF - SEO extension for Joomla!
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier 2012
 * @package     sh404sef
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     3.6.4.1481
 * @date		2012-11-01
 */

// Security check to ensure this file is being included by a parent file.
defined('_JEXEC') or die;

/**
 * Installation/Uninstallation script
 *
 */
class Com_Sh404sefInstallerScript
{
	const MIN_JOOMLA_VERSION = '2.5.6';
	const MAX_JOOMLA_VERSION = '4';

	private $_siteId = '';
	private $_preserveConfigFolder = '';
	private $_errorPageCatId = 0;
	private $_shlibVersion = '';
	private $_skipInstall = array();

	public function install($parent)
	{
	}

	public function uninstall($parent)
	{
		$this->_doUninstall($parent);
	}

	public function update($parent)
	{
	}

	public function preflight($route, $installer)
	{
		if ($route == 'install' || $route == 'update')
		{

			// check Joomla! version
			if (version_compare(JVERSION, self::MIN_JOOMLA_VERSION, '<') || version_compare(JVERSION, self::MAX_JOOMLA_VERSION, 'ge'))
			{
				JFactory::getApplication()
					->enqueueMessage(
						sprintf('sh404SEF requires Joomla! version between %s and %s (you are using %s). Aborting installation',
							self::MIN_JOOMLA_VERSION, self::MAX_JOOMLA_VERSION, JVERSION), 'error');
				return false;
			}

			// make sure resource manager is available, we'll need it during plugins installs
			if (!class_exists('ShlSystem_Resourcemanager'))
			{
				require_once $installer->getParent()->getPath('source') . '/admin/plugins/system/shlib/shl_packages/system/resourcemanager.php';
			}

			// check authorization to install for shared resources
			$newVersionFile = $installer->getParent()->getPath('source') . '/admin/plugins/system/shlib/shlib.xml';
			$this->_shlibVersion = ShlSystem_Resourcemanager::getXmlFileVersion($newVersionFile);
			$installCheckResult = ShlSystem_Resourcemanager::canInstall('shlib', $this->_shlibVersion);

			if ($installCheckResult->canInstall == 'no')
			{
				JFactory::getApplication()
					->enqueueMessage(
						'Cannot install sh404SEF: not allowed to install shLib version ' . $this->_shlibVersion . ': ' . $installCheckResult->reason,
						'error');
			}
			if ($installCheckResult->canInstall == 'skip')
			{
				$this->_shlibVersion = '';
				$this->_skipInstall[] = 'shlib';
				JFactory::getApplication()
					->enqueueMessage('shLib: skipping install of shLib version ' . $this->_shlibVersion . ': ' . $installCheckResult->reason);
			}

			$canInstall = $installCheckResult->canInstall != 'no';

			return $canInstall;
		}
	}

	public function postflight($type, $parent)
	{
		$this->_doInstallUpdate($parent);

		// installed a shared resource? register it with version
		if (!in_array('shlib', $this->_skipInstall))
		{
			ShlSystem_Resourcemanager::registerResource('shlib', $this->_shlibVersion);
		}

		// register that we are now using shLib
		ShlSystem_Resourcemanager::register(array('resource' => 'shlib', 'context' => 'com_sh404sef', 'min_version' => '0.2.0'));
	}

	// Implementation of install/uninstall scripts

	private function _doInstallUpdate($parent)
	{
		$this->_definePaths();
		$this->_includeLibs();

		// V 1.2.4.q Copy existing config file from /media to current component. Used to recover configuration when upgrading
		// V 1.2.4.s check if old file exists before deleting stub config file
		$oldConfigFile = $this->_preserveConfigFolder . 'sh404_upgrade_conf_' . $this->_siteId . '.php';
		if (JFile::exists($oldConfigFile))
		{
			// update old config files from VALID_MOS check to _JEXEC
			$config = JFile::read($oldConfigFile);
			if ($config && strpos($config, 'VALID_MOS') !== false)
			{
				$config = str_replace('VALID_MOS', '_JEXEC', $config);
				JFile::write($oldConfigFile, $config); // write it back
			}
			// now get back old config
			if (JFile::exists(JPATH_ADMINISTRATOR . 'components/com_sh404sef/config/config.sef.php'))
			{
				JFile::delete(JPATH_ADMINISTRATOR . '/components/com_sh404sef/config/config.sef.php');
			}
			JFile::copy($oldConfigFile, JPATH_ADMINISTRATOR . '/components/com_sh404sef/config/config.sef.php');
		}

		// restore black/white lists
		$folder = $this->_preserveConfigFolder . 'sh404_upgrade_conf_security';
		if (JFolder::exists($folder))
		{
			$fileList = JFolder::files($folder);
			if (!empty($fileList))
			{
				foreach ($fileList as $file)
				{
					if (JFile::exists(JPATH_ADMINISTRATOR . '/components/com_sh404sef/security/' . $file))
					{
						JFile::delete(JPATH_ADMINISTRATOR . '/components/com_sh404sef/security/' . $file);
					}
					JFile::copy($this->_preserveConfigFolder . 'sh404_upgrade_conf_security/' . $file,
						JPATH_ADMINISTRATOR . '/components/com_sh404sef/security/' . $file);
				}
			}
		}
		// if upgrading rather than installing from scratch, or after an uninstall
		// we must not copy back saved configuration files and log files
		// as this would overwrite up to date current ones
		// note that above we restored main config file and
		// security data files becomes blank files come
		// with the extension, so they'll be deleted in any case
		// and we have to restore them
		$shouldRestore = $this->_shShouldRestore();

		if ($shouldRestore)
		{
			// restore log files
			$folder = $this->_preserveConfigFolder . 'sh404_upgrade_conf_logs';
			if (JFolder::exists($folder))
			{
				JFolder::copy($folder, JPATH_ROOT . '/logs/sh404sef', $path = '', $force = true);
			}

			// restore customized default params
			$oldCustomConfigFile = $this->_preserveConfigFolder . 'sh404_upgrade_conf_' . $this->_siteId . '.custom.php';
			if (is_readable($oldCustomConfigFile) && filesize($oldCustomConfigFile) > 1000)
			{
				// update old config files from VALID_MOS check to _JEXEC
				$config = JFile::read($oldCustomConfigFile);
				if ($config && strpos($config, 'VALID_MOS') !== false)
				{
					$config = str_replace('VALID_MOS', '_JEXEC', $config);
					JFile::write($oldCustomConfigFile, $config); // write it back
				}
				if (JFile::exists(JPATH_ADMINISTRATOR . '/components/com_sh404sef/custom.sef.php'))
				{
					JFile::delete(JPATH_ADMINISTRATOR . '/components/com_sh404sef/custom.sef.php');
				}
				$result = JFile::copy($oldCustomConfigFile, JPATH_ADMINISTRATOR . '/components/com_sh404sef/custom.sef.php');
			}

		}

		// install plugins
		if (defined('SHLIB_ROOT_PATH'))
		{
			// trick of the day: we must fetch an instance of the db using the db helper
			// before installing the newest version of shLib system plugin. This will
			// force a decorated db instance to be created and stored, using the shlib
			// db class version that matches that of the shlib db helper class
			// As there was interface changes betwen shLib 0.1.x and 0.2.x, this prevents
			// "method not existing" errors when installing a newer version over an old one
			// make sure resource manager is available, we'll need it during plugins installs
			$db = ShlDbHelper::getInstance();
			$status = $this->_shInstallPluginGroup('system');
		}
		else
		{
			$this->_shInstallPluginGroup('system');
			// shLib is not installed yet, let's make it available to us
			if (!JFile::exists(JPATH_ROOT . '/plugins/system/shlib/shlib.php'))
			{
				JFactory::getApplication()
					->enqueuemessage('shLib was not installed properly, cannot continue. Please try uninstalling and installing again');
				return false;
			}
			require_once JPATH_ROOT . '/plugins/system/shlib/shlib.php';
			$config = array('type' => 'system', 'name' => 'shlib', 'params' => '');
			$shLibPlugin = new plgSystemShlib(JDispatcher::getInstance(), $config);
			$shLibPlugin->onAfterInitialise();
			$status = true;
		}

		if (!$status)
		{
			JFactory::getApplication()
				->enqueuemessage('There was an error installing one or more system plugins. Please try uninstalling and installing again');
			return;
		}

		$status = $this->_shInstallPluginGroup('sh404sefcore');
		if (!$status)
		{
			JFactory::getApplication()
				->enqueuemessage('There was an error installing one or more sh404SEF core plugins. Please try uninstalling and installing again');
			return;
		}
		$status = $this->_shInstallPluginGroup('sh404sefextplugins');
		if (!$status)
		{
			JFactory::getApplication()
				->enqueuemessage('There was an error installing one or more sh404SEF extension plugins. Please try uninstalling and installing again');
			return;
		}

		// install module
		$installer = $parent->getParent();
		$source = $installer->getPath('source');
		$extensionConfig = array('enabled' => 1, 'access' => 3);
		$moduleConfig = array();
		$status = $this->_shInstallModule('mod_sh404sef_cpicon', $source, $extensionConfig, $moduleConfig);
		// custom language switcher module disabled for now, not needed
		//$extensionConfig = array();
		//$moduleConfig = array();
		//$status = $this->_shInstallModule( 'mod_sh_languages', $source, $extensionConfig, $moduleConfig);

		// now we insert the 404 error page into the database
		// from version 1.5.5, the default content of 404 page has been largely modified
		// to make use of the similar urls plugin (and potentially others)
		// so we want to make sure people will have the new version of the 404 error page
		$this->_shUpdateErrorPage();

		// apply various DB updates
		$this->_shUpdateDBStructure();

		// message
		// decide on help file language
		$languageCode = Sh404sefHelperLanguage::getFamily();
		$basePath = JPATH_ROOT . '/administrator/components/com_sh404sef/language/%s.postinstall.php';
		// fall back to english if language readme does not exist
		jimport('joomla.filesystem.file');
		if (!JFile::exists(sprintf($basePath, $languageCode)))
		{
			$languageCode = 'en';
		}

		include sprintf($basePath, $languageCode);
	}

	private function _getErrorPageCatId()
	{
		$this->_errorPageCatId = (int) ShlDbHelper::selectResult('#__categories', array('id'),
			array('parent_id' => 1, 'extension' => 'com_content', 'path' => 'sh404sef-custom-content', 'level' => 1));

		// we have a category, all good
		if (!empty($this->_errorPageCatId))
		{
			return $this->_errorPageCatId;
		}

		// no category for our content yet. Create it
		$basePath = JPATH_ADMINISTRATOR . '/components/com_categories';
		require_once $basePath . '/models/category.php';
		$config = array('table_path' => $basePath . '/tables');
		$catmodel = new CategoriesModelCategory($config);
		$catData = array('id' => 0, 'parent_id' => 1, 'level' => 1, 'path' => 'sh404sef-custom-content', 'extension' => 'com_content',
			'title' => 'sh404SEF custom content', 'alias' => 'sh404sef-custom-content', 'description' => 'Do not delete please!', 'published' => 1,
			'language' => '*');
		$status = $catmodel->save($catData);

		if (!$status)
		{
			throw new Sh404sefExceptionDefault('Unable to create sh404SEF content category!', 500);
		}

		// now get and store the id
		$this->_errorPageCatId = (int) ShlDbHelper::selectResult('#__categories', array('id'),
			array('parent_id' => 1, 'extension' => 'com_content', 'path' => 'sh404sef-custom-content', 'level' => 1));

		if (empty($this->_errorPageCatId))
		{
			throw new Sh404sefExceptionDefault('Unable to read sh404SEF content category!', 500);
		}

		return $this->_errorPageCatId;
	}

	private function _changeErrorPageCategory($pageTitle = '__404__')
	{
		// try find this old category
		$catid = ShlDbHelper::selectResult('#__categories', array('id'), 'parent_id > 0 and extension = ? and path = ? and level = ?',
			array('com_content', 'uncategorised', 1));

		if (empty($catid))
		{
			return false;
		}

		// we have cat id, try to read article
		$ids = ShlDbHelper::selectAssoc('#__content', array('id'), array('catid' => $catid, 'title' => $pageTitle));

		if (empty($ids))
		{
			return false;
		}

		// we indeed have one or more such articles (possibly one per language: move them to new category)
		ShlDbHelper::updateIn('#__content', array('catid' => $this->_errorPageCatId), 'id', $ids);

		return true;
	}

	/**
	 * Insert into the content database an uncategorized article
	 * which serves as a basis for the 404 error page
	 * Article title is __404__
	 * Prior to version 1.5.5, the article displayed for 404 errors
	 * was titled 404. The new name ensures users who customized
	 * will keep their old design in the db. They can either reselect it
	 * from the control panel, or customize as well the new __404__ page
	 * @return unknown_type
	 */
	private function _shUpdateErrorPage($pageTitle = '__404__')
	{
		// do we already have a __404__ article?
		try
		{
			$catid = $this->_getErrorPageCatId();

			// we have cat id, try to read article
			$id = ShlDbHelper::selectResult('#__content', array('id'), array('catid' => $catid, 'title' => $pageTitle));

			// if required page is already there, go away
			if (!empty($id))
			{
				return;
			}

			// maybe there's already an error page in the 'uncategorised' category we were using prior to 3.4
			// we can move it to the new category
			$updated = $this->_changeErrorPageCategory();
			if ($updated)
			{
				return true;
			}

			// find about the default page content
			$lang = JFactory::getLanguage();
			$lang->load('com_sh404sef');

			// now we can insert the new page content into the db
			$status = $this->_insertContent($pageTitle, JText::_('COM_SH404SEF_DEF_404_MSG'));

			return $status;

		}
		catch (Exception $e)
		{
			$app = JFactory::getApplication();
			$app->enqueueMessage('Error: ' . $e->getMessage());
			return false;
		}
	}

	/**
	 * Performs update to db stucture on existing setups
	 */
	private function _shUpdateDBStructure()
	{
		// get a db instance
		$db = JFactory::getDBO();

		/* version 3.4: added OpenGraph data columns to meta data table
		 CREATE TABLE IF NOT EXISTS `#__sh404sef_metas` (
		     `id` int(11) NOT NULL auto_increment,
		     `newurl` varchar(255) NOT NULL default '',
		     `metadesc` varchar(255) default '',
		     `metakey` varchar(255) default  '',
		     `metatitle` varchar(255) default '',
		     `metalang` varchar(30) default '',
		     `metarobots` varchar(30) default '',

		     `og_enable` tinyint(3) NOT NULL DEFAULT '0',
		     `og_type` varchar(30) default '',
		     `og_image` varchar(255) default '',
		     `og_enable_description` tinyint(3) NOT NULL DEFAULT '0',
		     `og_enable_site_name` tinyint(3) NOT NULL DEFAULT '0',
		     `og_site_name` varchar(255) default '',
		     `fb_admin_ids` varchar(255) default '',
		     `og_enable_location` tinyint(3) NOT NULL DEFAULT '0',
		     `og_latitude` varchar(30) default '',
		     `og_longitude` varchar(30) default '',
		     `og_street_address` varchar(255) default '',
		     `og_locality` varchar(255) default '',
		     `og_postal_code` varchar(30) default '',
		     `og_region` varchar(255) default '',
		     `og_country_name` varchar(255) default '',
		     `og_enable_contact` tinyint(3) NOT NULL DEFAULT '0',
		     `og_email` varchar(255) default '',
		     `og_phone_number` varchar(255) default '',
		     `og_fax_number` varchar(255) default '',
		     `og_enable_fb_admin_ids` tinyint(3) NOT NULL DEFAULT '0',
		     `canonical` varchar(255) default '',
		     PRIMARY KEY (`id`),
		     KEY `newurl` (`newurl`)
		 ) DEFAULT CHARSET=utf8;*/

		// when upgrading from older version, these values could be missing at the time of install
		defined('SH404SEF_OPTION_VALUE_NO') or define('SH404SEF_OPTION_VALUE_NO', 0);
		defined('SH404SEF_OPTION_VALUE_YES') or define('SH404SEF_OPTION_VALUE_YES', 1);
		defined('SH404SEF_OPTION_VALUE_USE_DEFAULT') or define('SH404SEF_OPTION_VALUE_USE_DEFAULT', 2);

		// get list of columns
		// platform 12.1
		if (method_exists($db, 'getTableFields'))
		{
			$columns = $db->getTableFields('#__sh404sef_metas');
			$columns = empty($columns['#__sh404sef_metas']) ? array() : $columns['#__sh404sef_metas'];
		}
		else
		{
			$columns = $db->getTableColumns('#__sh404sef_metas');
		}

		// build required statements
		$subQueries = array();

		if (empty($columns['canonical']))
		{
			$subQueries[] = "add `canonical` varchar(255) default ''";
		}

		if (empty($columns['og_enable']))
		{
			$subQueries[] = "add `og_enable` tinyint(3) NOT NULL DEFAULT '" . SH404SEF_OPTION_VALUE_USE_DEFAULT . "'";
		}
		if (empty($columns['og_type']))
		{
			$subQueries[] = "add `og_type` varchar(30) default ''";
		}
		if (empty($columns['og_image']))
		{
			$subQueries[] = "add `og_image` varchar(255) default ''";
		}
		if (empty($columns['og_enable_description']))
		{
			$subQueries[] = "add `og_enable_description` tinyint(3) NOT NULL DEFAULT '" . SH404SEF_OPTION_VALUE_USE_DEFAULT . "'";
		}
		if (empty($columns['og_enable_site_name']))
		{
			$subQueries[] = "add `og_enable_site_name` tinyint(3) NOT NULL DEFAULT '" . SH404SEF_OPTION_VALUE_USE_DEFAULT . "'";
		}
		if (empty($columns['og_site_name']))
		{
			$subQueries[] = "add `og_site_name` varchar(255) default ''";
		}
		if (empty($columns['fb_admin_ids']))
		{
			$subQueries[] = "add `fb_admin_ids` varchar(255) default ''";
		}
		if (empty($columns['og_enable_location']))
		{
			$subQueries[] = "add `og_enable_location` tinyint(3) NOT NULL DEFAULT '" . SH404SEF_OPTION_VALUE_USE_DEFAULT . "'";
		}
		if (empty($columns['og_latitude']))
		{
			$subQueries[] = "add `og_latitude` varchar(30) default ''";
		}
		if (empty($columns['og_longitude']))
		{
			$subQueries[] = "add `og_longitude` varchar(30) default ''";
		}
		if (empty($columns['og_street_address']))
		{
			$subQueries[] = "add `og_street_address` varchar(255) default ''";
		}
		if (empty($columns['og_locality']))
		{
			$subQueries[] = "add `og_locality` varchar(255) default ''";
		}
		if (empty($columns['og_postal_code']))
		{
			$subQueries[] = "add `og_postal_code` varchar(30) default ''";
		}
		if (empty($columns['og_region']))
		{
			$subQueries[] = "add `og_region` varchar(255) default ''";
		}
		if (empty($columns['og_country_name']))
		{
			$subQueries[] = "add `og_country_name` varchar(255) default ''";
		}
		if (empty($columns['og_enable_contact']))
		{
			$subQueries[] = "add `og_enable_contact` tinyint(3) NOT NULL DEFAULT '" . SH404SEF_OPTION_VALUE_USE_DEFAULT . "'";
		}
		if (empty($columns['og_email']))
		{
			$subQueries[] = "add `og_email` varchar(255) default ''";
		}
		if (empty($columns['og_phone_number']))
		{
			$subQueries[] = "add `og_phone_number` varchar(255) default ''";
		}
		if (empty($columns['og_fax_number']))
		{
			$subQueries[] = "add `og_fax_number` varchar(255) default ''";
		}
		if (empty($columns['og_enable_fb_admin_ids']))
		{
			$subQueries[] = "add `og_enable_fb_admin_ids` tinyint(3) NOT NULL DEFAULT '" . SH404SEF_OPTION_VALUE_USE_DEFAULT . "'";
		}

		if (!empty($subQueries))
		{
			// aggregate sub-queries
			$subQueries = implode(', ', $subQueries);

			// prepend query
			$query = 'alter table ' . $db->quoteName('#__sh404sef_metas') . ' ' . $subQueries;

			// run query
			try
			{
				$db->setQuery($query);
				$db->query();
				$error = $db->getErrorNum();
				if (!empty($error))
				{
					throw new Exception($db->getErrorMsg());
				}
			}
			catch (Exception $e)
			{
				$app = JFactory::getApplication();
				$app
					->enqueueMessage(
						'Error while upgrading the database : ' . $e->getMessage()
							. '. Sh404SEF will probably not operate properly. Please uninstall it, then try again after checking your database server setup. Contact us in case this happens again.');
			}
		}

	}

	private function _shInstallModule($module, $source, $extensionConfig, $moduleConfig)
	{
		$app = JFactory::getApplication();

		$path = $source . '/admin/modules/' . $module;
		$installer = new JInstaller;
		$result = $installer->install($path);

		if ($result)
		{

			// if files moved to destination, setup module in Joomla database

			$shouldRestore = $this->_shShouldRestore();

			if ($shouldRestore)
			{

				// read stored params from disk
				$this->_shGetExtensionSavedParams($module . '_extension', $extensionConfig);

			}

			// update elements in db, only if we need to restore past configuration
			try
			{
				if (!empty($extensionConfig))
				{
					// load module details from extension table
					$moduleDetails = ShlDbHelper::selectAssoc('#__extensions', array('*'), array('type' => 'module', 'element' => $module));

					// merge with saved details and write back to disk
					$details = array_merge($moduleDetails, $extensionConfig);
					ShlDbHelper::update('#__extensions', $details, array('extension_id' => (int) $moduleDetails['extension_id']));

				}
			}
			catch (Exception $e)
			{
				$app->enqueueMessage('Error: ' . $e->getMessage());
			}

			if ($shouldRestore)
			{

				// read stored params from disk
				$this->_shGetExtensionSavedParams($module . '_modules', $moduleConfig);

			}

			// update elements in db, if we need to restore past configuration
			try
			{
				$instanceDetails = ShlDbHelper::selectAssoc('#__modules', array('*'), array('module' => $module));

				// merge with saved details and write back to disk
				$details = array_merge($instanceDetails, $moduleConfig);
				ShlDbHelper::update('#__modules', $details, array('id' => (int) $instanceDetails['id']));

			}
			catch (Exception $e)
			{
				$app->enqueueMessage('Error: ' . $e->getMessage());
			}

			// and finally we make sure there is a menu item associated with the module
			$details = array('menuid' => 0);

			if ($shouldRestore)
			{

				// read stored params from disk
				$this->_shGetExtensionSavedParams($module . '_modules_menu', $details);

			}
			$details = array_merge($details, array('moduleid' => (int) $instanceDetails['id']));

			// insert or update elements in db, if we need to restore past configuration
			try
			{

				ShlDbHelper::insertUpdate('#__modules_menu', $details, array('moduleid' => (int) $instanceDetails['id']));

			}
			catch (Exception $e)
			{
				$app->enqueueMessage('Error: ' . $e->getMessage());
			}

		}
		else
		{
			$app->enqueueMessage('Error installing sh404sef module: ' . $module);
		}
		return $result;
	}

	/**
	 * Install all sh404sef plugins available in a given
	 * group
	 *
	 * @param string $group name of group
	 * @return boolean, true if success
	 */
	private function _shInstallPluginGroup($group)
	{
		$app = JFactory::getApplication();

		$sourcePath = JPATH_ADMINISTRATOR . '/components/com_sh404sef/plugins/' . $group;
		if (!JFolder::exists($sourcePath))
		{
			$app->enqueueMessage('Trying to install empty plugin group: ' . $group);
			return true;
		}

		// if each plugin resides in its own subDir, we must iterate over all sub dirs
		$folderList = JFolder::folders($sourcePath);
		if (empty($folderList))
		{
			$app->enqueueMessage('Trying to install empty plugin group, folder is empty: ' . $sourcePath);
			return true;
		}

		// process each plugin
		$errors = false;
		foreach ($folderList as $folder)
		{
			// install the plugin itself
			$status = $this->_shInstallPlugin($group, $folder, $sourcePath);
			// set flag if an error happened, but keep installing
			// other plugins
			$errors = $errors || !$status;
			// also display status
			if (!$status)
			{
				$app->enqueueMessage('Error installing sh404sef plugin from ' . $folder);
			}
		}

		// return true if no error at all
		return $errors == false;
	}

	/**
	 * Insert in the db the previously retrieved parameters for a plugin
	 * including publication information. Also move files as required
	 *
	 * @param string $basePath , the base path to get original files from
	 */

	/**
	 * Insert in the db the previously retrieved parameters for a plugin
	 * including publication information. Also move files as required
	 *
	 * @param string $pluginFolder
	 * @param string $pluginElement
	 * @param string $basePath
	 */
	private function _shInstallPlugin($pluginFolder, $pluginElement, $sourcePath)
	{
		if ($pluginFolder == 'system' && $pluginElement == 'shlib')
		{
			if (in_array('shlib', $this->_skipInstall))
			{
				return true;
			}
		}

		$app = JFactory::getApplication();

		// use J! installer to fully install the plugin
		$installer = new JInstaller;
		$result = $installer->install($sourcePath . '/' . $pluginElement);

		if ($result)
		{
			$overrides = array('ordering' => 10, 'enabled' => 1);
			// make sure main library is loaded first
			if ($pluginElement == 'shlib')
			{
				$overrides['ordering'] = -100;
			}

			$shouldRestore = $this->_shShouldRestore();

			if ($shouldRestore)
			{
				// read stored params from disk
				$saved = array();
				$this->_shGetExtensionSavedParams($pluginFolder . '.' . $pluginElement, $saved);
				$overrides = array_merge($overrides, $saved);

			}

			// overrides data in extension table, possibly overriding some columns from saved data
			if (!empty($overrides))
			{
				try
				{
					$db = JFactory::getDbo();

					$query = $db->getQuery(true);
					$query->select('extension_id')->from('#__extensions')->where($db->quoteName('type') . '=' . $db->quote('plugin'))
						->where($db->quoteName('element') . '=' . $db->quote($pluginElement))
						->where($db->quoteName('folder') . '=' . $db->quote($pluginFolder));
					$db->setQuery($query);
					$pluginId = $db->loadResult();
					$error = $db->getErrorNum();
					if (!empty($error))
					{
						throw new Exception($db->getErrorMsg());
					}

					if (!empty($pluginId))
					{
						jimport('joomla.database.table.extension');
						$extension = JTable::getInstance('Extension');
						$extension->load($pluginId);
						$extension->bind($overrides);
						$status = $extension->store();
						if (!$status)
						{
							$app->enqueueMessage('Error: ' . $extension->getError());
						}
					}
					else
					{
						$app->enqueueMessage('Error updating plugin DB record: ' . $pluginFolder . ' / ' . $pluginElement);
					}
				}
				catch (Exception $e)
				{
					$status = false;
					$app->enqueueMessage('Error: ' . $e->getMessage());
				}
			}
		}
		else
		{
			$app->enqueueMessage('Error installing sh404sef plugin: ' . $pluginFolder . ' / ' . $pluginElement);
			$status = false;
		}

		return $status;
	}

	/**
	 * Retrieves stored params of a given extension (module or plugin)
	 * (as saved upon uninstall)
	 *
	 * @param string $extName the module name, including mod_ if a module
	 * @param array $shConfig an array holding the database columns of the extension
	 * @param array $shPub, an array holding the publication information of the module (only for modules)
	 * @return boolean, true if any stored parameters were found for this extension
	 */
	private function _shGetExtensionSavedParams($extName, &$shConfig, &$shPub = null, $useId = false)
	{
		static $fileList = array();

		// prepare default return value
		$status = false;

		// read all file names in /media/sh404_upgrade_conf dir, for easier processing
		$baseFolder = $this->_preserveConfigFolder . 'sh404_upgrade_conf';
		if (JFolder::exists($baseFolder) && (empty($fileList) || !isset($fileList[$extName])))
		{
			$baseName = $extName . ($useId ? '_[0-9]{1,10}' : '') . '_' . $this->_siteId . '.php';
			$fileList[$extName] = JFolder::files($baseFolder, $baseName);
		}

		// extract filename from list we've established previously
		$extFile = isset($fileList[$extName]) && $fileList[$extName] !== false ? array_shift($fileList[$extName]) : '';
		if (empty($fileList[$extName]))
		{
			// prevent infinite loop
			$fileList[$extName] = false;
		}

		if (!empty($extFile) && JFile::exists($baseFolder . '/' . $extFile))
		{
			$status = true; // operation was successful
			include($baseFolder . '/' . $extFile);
		}

		return $status;
	}

	/**
	 * Decide whether backed up params should be restore (and
	 * plugins reinstalled).
	 * This should happen only when the extension is NOT already
	 * installed. Most of times, as we are using updagre install
	 * that should not happen and we jst overwrite
	 * but if user uninstalled the extension, we must restore
	 * data saved when he uninstalled
	 *
	 */
	private function _shShouldRestore()
	{
		// IMPORTANT: the check is done once, and only once
		// as for later calls, the system plugin will have been installed
		// and thus the test will not be valid anymore
		static $restore = null;

		if (is_null($restore))
		{
			// search for base xml file to decide if already installed
			$restore = !JFile::exists(JPATH_ROOT . '/plugins/system/sh404sef/sh404sef.xml');
		}

		return $restore;
	}

	/**
	 * Performs pre-uninstall backup of configuration
	 *
	 * @param object $parent
	 */
	private function _doUninstall($parent)
	{
		$this->_definePaths();
		$this->_includeLibs();

		// V 1.2.4.t before uninstalling modules, save their settings, if told to do so
		$sef_config_class = JPATH_ADMINISTRATOR . '/components/com_sh404sef/sh404sef.class.php';
		// Make sure class was loaded.
		if (!class_exists('shSEFConfig'))
		{
			if (is_readable($sef_config_class))
			{
				require_once($sef_config_class);
			}
			else
			{
				JError::RaiseError(500, JText::_('COM_SH404SEF_NOREAD') . "( $sef_config_class )<br />" . JText::_('COM_SH404SEF_CHK_PERMS'));
			}
		}
		$sefConfig = new shSEFConfig();
		if (!$sefConfig->shKeepStandardURLOnUpgrade && !$sefConfig->shKeepCustomURLOnUpgrade)
		{
			$this->_shDeleteTable('sh404sef_urls');
			$this->_shDeleteTable('sh404sef_aliases');
			$this->_shDeleteTable('sh404sef_pageids');
		}
		elseif (!$sefConfig->shKeepStandardURLOnUpgrade)
		{
			$this->_shDeleteAllSEFUrl('Standard');
		}
		elseif (!$sefConfig->shKeepCustomURLOnUpgrade)
		{
			$this->_shDeleteAllSEFUrl('Custom');
			$this->_shDeleteTable('sh404sef_aliases');
			$this->_shDeleteTable('sh404sef_pageids');
		}

		if (!$sefConfig->shKeepMetaDataOnUpgrade)
		{
			$this->_shDeleteTable('sh404sef_metas');
		}

		// remove admin quick icon module
		$this->_shSaveDeleteModuleParams('mod_sh404sef_cpicon', $client = 1);

		// remove language switcher module
		// custom language switcher module disabled for now, not needed
		//$this->_shSaveDeleteModuleParams( 'mod_sh_languages', $client = 0);

		// remove system plugins
		$this->_shSaveDeletePluginParams('sh404sef', 'system');
		$this->_shSaveDeletePluginParams('shmobile', 'system');

		// unregister from shLib, then possibly uninstall it
		if (JFile::exists(JPATH_ROOT . '/plugins/system/shlib/shl_packages/system/resourcemanager.php'))
		{
			require_once JPATH_ROOT . '/plugins/system/shlib/shl_packages/system/resourcemanager.php';
			ShlSystem_Resourcemanager::unregister('shlib', 'com_sh404sef');
			if (ShlSystem_Resourcemanager::canUninstall('shlib'))
			{
				$this->_shSaveDeletePluginParams('shlib', 'system');
			}
		}

		// remove core plugins
		$this->_shSaveDeletePluginGroup('sh404sefcore');
		$this->_shSaveDeletePluginGroup('sh404sefextplugins');

		// delete analytics cached data, to force update
		// in case this part of sh404sef has changed
		$cache = JFactory::getCache('sh404sef_analytics');
		$cache->clean();

		// preserve configuration or not ?
		if (!$sefConfig->shKeepConfigOnUpgrade)
		{
			// main config file
			$fileName = $this->_preserveConfigFolder . 'sh404_upgrade_conf_' . $this->_siteId . '.php';
			if (JFile::exists($fileName))
			{
				JFile::delete($fileName);
			}

			// user custom config file
			$fileName = $this->_preserveConfigFolder . 'sh404_upgrade_conf_' . $this->_siteId . '.custom.php';
			if (JFile::exists($fileName))
			{
				JFile::delete($fileName);
			}

			// related extensions (plugins) config files folder
			if (JFolder::exists($this->_preserveConfigFolder . 'sh404_upgrade_conf'))
			{
				JFolder::delete($this->_preserveConfigFolder . 'sh404_upgrade_conf');
			}

			// log files folder
			if (JFolder::exists($this->_preserveConfigFolder . 'sh404_upgrade_conf_logs'))
			{
				JFolder::delete($this->_preserveConfigFolder . 'sh404_upgrade_conf_logs');
			}

			// security log files folder
			if (JFolder::exists($this->_preserveConfigFolder . 'sh404_upgrade_conf_security'))
			{
				JFolder::delete($this->_preserveConfigFolder . 'sh404_upgrade_conf_security');
			}
		}
		else
		{ // if we keep config

			if (JFolder::exists(JPATH_ROOT . '/logs/sh404sef'))
			{
				JFolder::copy(JPATH_ROOT . '/logs/sh404sef', $this->_preserveConfigFolder . 'sh404_upgrade_conf_logs', $path = '', $force = true);
			}

			if (JFolder::exists(JPATH_ADMINISTRATOR . '/components/com_sh404sef/security'))
			{
				JFolder::copy(JPATH_ADMINISTRATOR . '/components/com_sh404sef/security',
					$this->_preserveConfigFolder . 'sh404_upgrade_conf_security', $path = '', $force = true);
			}
		}

		// delete folder in /media
		// display results
		echo '<h3>sh404SEF has been succesfully uninstalled. </h3>';
		echo '<br />';
		if ($sefConfig->shKeepStandardURLOnUpgrade)
		{
			echo '- automatically generated SEF url have not been deleted (table #__sh404sef_urls)<br />';
		}
		else
		{
			echo '- automatically generated SEF url have been deleted<br />';
		}
		echo '<br />';
		if ($sefConfig->shKeepCustomURLOnUpgrade)
		{
			echo '- custom SEF url, aliases and pageIds have not been deleted (tables #__sh404sef_urls, #__sh404sef_aliases and #__sh404sef_pageids)<br />';
		}
		else
		{
			echo '- custom SEF url, aliases and pageIds have been deleted<br />';
		}
		echo '<br />';
		if ($sefConfig->shKeepMetaDataOnUpgrade)
		{
			echo '- Custom Title and META data have not been deleted (table #__sh404sef_metas)<br />';
		}
		else
		{
			echo '- Custom Title and META data have been deleted<br />';
		}
		echo '<br />';

	}

	// V 1.2.4.t improved upgrading
	private function _shDeletetable($tableName)
	{
		$db = ShlDbHelper::getDb();
		$query = 'drop table ' . $db->quoteName('#__' . $tableName);
		try
		{
			ShlDbHelper::query($query);
		}
		catch (Exception $e)
		{
			echo $e->getMessage() . '<br />';
		}
	}

	private function _shDeleteAllSEFUrl($kind)
	{
		If ($kind == 'Custom')
		{
			$where = '`dateadd` > \'0000-00-00\' and `newurl` != \'\';';
		}
		else
		{
			$where = '`dateadd` = \'0000-00-00\';';
		}
		try
		{
			ShlDbHelper::delete('#__sh404sef_urls', $where);
		}
		catch (Exception $e)
		{
			echo $e->getMessage() . '<br />';
		}
	}

	/**
	 *
	 * utility functions
	 *
	 */
	/**
	 * Writes an extension parameter to a disk file, located
	 * in the /media directory
	 *
	 * @param string $extName the extension name
	 * @param array $shConfig associative array of parameters of the extension, to be written to disk
	 * @param array $pub, optional, only if module, an array of the menu item id where the module is published
	 * @return boolean, true if no error
	 */
	private function _shWriteExtensionConfig($extName, $params)
	{
		if (empty($params))
		{
			return;
		}

		// calculate target file name
		$extPath = $this->_preserveConfigFolder . 'sh404_upgrade_conf';

		// if it does not exists, lets create it first
		if (!JFolder::exists($extPath))
		{
			JFolder::create($extPath);
		}

		// make sure we have an index.html file in that folder
		$target = JPath::clean($extPath . '/' . 'index.html');
		if (!JFile::exists($target))
		{
			// copy one Joomla's index.html file to the backup directory
			$source = JPath::clean(JPATH_ROOT . '/plugins/index.html');
			$success = JFile::copy($source, $target);
		}

		// now build full path file name to save config
		$extFile = $extPath . '/' . $extName . '_' . $this->_siteId . '.php';

		// remove previous if any
		if (JFile::exists($extFile))
		{
			JFile::delete($extFile);
		}

		// prepare data for writing
		$data = '<?php // Extension params save file for sh404sef
    //
    if (!defined(\'_JEXEC\')) die(\'Direct Access to this location is not allowed.\');';
		$data .= "\n";

		if (!empty($params))
		{
			foreach ($params as $key => $value)
			{
				$data .= '$' . $key . ' = ' . var_export($value, true) . ';';
				$data .= "\n";
			}
		}

		// write to disk
		$success = JFile::write($extFile, $data);

		return $success !== false;
	}

	/**
	 * Save parameters, then delete a module
	 * Would not work on additional copies made by user
	 *
	 * @param string $moduleName, the module name, matching 'module' column in modules table
	 * @param string $client (ie : site or administrator
	 */
	private function _shSaveDeleteModuleParams($moduleName, $client)
	{
		// read plugin param from db
		try
		{
			$result = ShlDbHelper::selectAssoc('#__extensions', array('*'),
				array('type' => 'module', 'element' => $moduleName, 'client_id' => $client));

			if (empty($result))
			{
				// invalid module name?
				return false;
			}
			// remove module db id
			unset($result['extension_id']);

			// write everything on disk
			$this->_shWriteExtensionConfig($moduleName . '_extension', array('shConfig' => $result));

			// now remove plugin details from db
			ShlDbHelper::delete('#__extensions', array('type' => 'module', 'element' => $moduleName, 'client_id' => $client));

			// do the same for the module instance, in #__module table
			$result = ShlDbHelper::selectAssoc('#__modules', array('*'), array('module' => $moduleName, 'client_id' => $client));

			if (empty($result))
			{
				// invalid module name?
				return false;
			}
			// save and remove module db id
			$moduleId = $result['id'];
			unset($result['id']);

			// write everything on disk
			$this->_shWriteExtensionConfig($moduleName . '_modules', array('shConfig' => $result));

			// now remove plugin details from db
			ShlDbHelper::delete('#__modules', array('module' => $moduleName, 'client_id' => $client));

			// remove module/menu affectation
			$result = ShlDbHelper::selectAssoc('#__modules_menu', array('*'), array('moduleid' => $moduleId));

			// remove module db id
			unset($result['moduleid']);

			// write everything on disk
			$this->_shWriteExtensionConfig($moduleName . '_modules_menu', array('shConfig' => $result));

			// now remove plugin details from db
			ShlDbHelper::delete('#__modules_menu', array('moduleid' => $moduleId));

		}
		catch (Exception $e)
		{
			echo $e->getMessage() . '<br />';
		}

		// delete the module files
		$path = JPATH_ROOT . '/' . ($client ? 'administrator' . '/' : '') . 'modules/' . $moduleName;
		if (JFolder::exists($path))
		{
			JFolder::delete($path);
		}

	}

	/**
	 * Save parameters, then delete a plugin
	 *
	 * @param string $pluginName, the plugin name, mathcing 'element' column in plugins table
	 * @param string $folder, the plugin folder (ie : 'content', 'search', 'system',...
	 */
	private function _shSaveDeletePluginParams($pluginName, $folder, $folders = null)
	{
		try
		{
			$result = ShlDbHelper::selectAssoc('#__extensions', array('*'), array('type' => 'plugin', 'element' => $pluginName, 'folder' => $folder));

			if (empty($result))
			{
				// invalid plugin name?
				return false;
			}

			// remove plugin db id
			$pluginId = $result['extension_id'];
			unset($result['extension_id']);

			// write everything on disk
			$this->_shWriteExtensionConfig($pluginName, array('shConfig' => $result));

			// now uninstall
			$installer = new JInstaller;
			$result = $installer->uninstall('plugin', $pluginId);

			// now remove plugin details from db
			//ShlDbHelper::delete( '#__extensions', array( 'type' => 'plugin', 'element' => $pluginName, 'folder' => $folder));

		}
		catch (Exception $e)
		{

		}

		// delete the plugin files
		//$basePath = JPATH_ROOT. '/plugins/' . $folder . '/' . $pluginName;
		//if (JFolder::exists($basePath)) {
		//  JFolder::delete( $basePath);
		//}
	}

	/**
	 * Save params, then delete plugin, for all plugins
	 * in a given group
	 *
	 * @param $group the group to be deleted
	 * @return none
	 */
	private function _shSaveDeletePluginGroup($group)
	{
		$unsafe = array('authentication', 'content', 'editors', 'editors-xtd', 'search', 'system', 'xmlrpc');
		if (in_array($group, $unsafe))
		{
			// safety net : we don't want to delete the whole system or content folder
			return false;
		}

		// read plugin param from db
		try
		{
			$pluginList = ShlDbHelper::selectAssocList('#__extensions', array('*'), array('type' => 'plugin', 'folder' => $group));

			if (empty($pluginList))
			{
				return true;
			}

			// for each plugin
			foreach ($pluginList as $plugin)
			{
				// remove plugin db id
				unset($plugin['id']);

				// write everything on disk
				$this->_shWriteExtensionConfig($plugin['folder'] . '.' . $plugin['element'], array('shConfig' => $plugin));

				// now remove plugin details from db
				ShlDbHelper::delete('#__extensions', array('type' => 'plugin', 'element' => $plugin['element'], 'folder' => $plugin['folder']));
			}
		}
		catch (Exception $e)
		{
			echo $e->getMessage() . '<br />';
		}

		// now delete the files for the whole group
		if (JFolder::exists(JPATH_ROOT . '/plugins/' . $group))
		{
			JFolder::delete(JPATH_ROOT . '/plugins/' . $group);
		}
	}

	/**
	 * Insert an intro text into the content table
	 *
	 * @param strng $shIntroText
	 * @return boolean, true if success
	 */
	function _insertContent($pageTitle, $shIntroText)
	{
		// result storage
		$status = false;

		jimport('joomla.database.table');
		try
		{
			$catid = $this->_errorPageCatId;
			if (empty($catid))
			{
				$this->setError(JText::_('COM_SH404SEF_CANNOT_SAVE_404_NO_UNCAT'));
				return;
			}
			$contentTable = JTable::getInstance('content');
			$content = array('title' => $pageTitle, 'alias' => $pageTitle, 'title_alias' => $pageTitle, 'introtext' => $shIntroText, 'state' => 1,
				'language' => '*', 'catid' => $catid,
				'attribs' => '{"menu_image":"-1","show_title":"0","show_section":"0","show_category":"0","show_vote":"0","show_author":"0","show_create_date":"0","show_modify_date":"0","show_pdf_icon":"0","show_print_icon":"0","show_email_icon":"0","pageclass_sfx":""');

			$status = $contentTable->save($content);
		}
		catch (Exception $e)
		{
		}

		return $status;
	}

	private function _definePaths()
	{
		$this->_siteId = rtrim(str_replace('/administrator', '', JURI::base()), '/');
		$this->_siteId = str_replace('/', '_', str_replace('http://', '', $this->_siteId));
		$this->_preserveConfigFolder = JPATH_ROOT . '/media/sh404sef/';
	}

	private function _includeLibs()
	{

		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.path');
		jimport('joomla.html.parameter');
		jimport('joomla.filter.filterinput');
		jimport('joomla.utilities.string');

		// we use require instead of require_once so that the NEW, just installed, version of the file is
		// reloaded. If any new define has been added in the new version, they will thus become available
		// This requires that defines in defines.php are protected again redifining the same constant
		require JPATH_ROOT . '/administrator/components/com_sh404sef/defines.php';
		// if these files have been included already, we will use the old version of the file
		require_once JPATH_ROOT . '/administrator/components/com_sh404sef/exceptions/default.php';
		require_once JPATH_ROOT . '/administrator/components/com_sh404sef/helpers/language.php';

	}

}
