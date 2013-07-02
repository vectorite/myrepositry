<?php
/**
 * @package SmartIcons Component for Joomla! 2.5
 * @version $Id: script.php 9 2012-03-28 20:07:32Z Bobo $
 * @author SUTA Bogdan-Ioan
 * @copyright (C) 2011 SUTA Bogdan-Ioan
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 **/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

class com_SmartIconsInstallerScript
{
	/**
	 * method to install the component
	 *
	 * @return void
	 */
	function install($parent)
	{
		//Create category
		require_once JPATH_SITE.DS.'libraries'.DS.'joomla'.DS.'database'.DS.'table'.DS.'category.php';

		$db = JFactory::getDbo();
		$user = JFactory::getUser();
		$category = array();
		$category['parent_id'] = '1';
		$category['extension'] = "com_smarticons";
		$category['title'] = 'Standard';
		$category['published'] = '1';
		$category['access'] = '1';
		$category['level'] = '1';
		$category['description'] = "<p>Standard icons that come with every Joomla! installation.</p>";
		$category['language'] = "*";
		$category['created_user_id'] = $user->id;

		$categoryTable = new JTableCategory($db);
		$categoryTable->setLocation($category['parent_id'], 'last-child');
		$categoryTable->setRules('{"core.view":{"1":1},"core.delete":[],"core.edit":[],"core.edit.state":[]}');
		if ($categoryTable->save($category)) {

			//Get category ID
			$query = $db->getQuery(true);
			$query->select('id');
			$query->from('#__categories');
			$query->where('extension = \'com_smarticons\'');
			$query->where('title = \'Standard\'');

			$db->setQuery($query);
			if ($id = (int)$db->loadResult()) {
				echo '<p>'. JText::_('COM_SMARTICONS_INSTALLER_CATEGORY_ADDSUCCESS') .'</p>';;
			} else {
				echo '<p>'. JText::_('COM_SMARTICONS_INSTALLER_CATEGORY_ADDFAIL') .'</p>';
			}
		} else {
			echo '<p>'. JText::_('COM_SMARTICONS_INSTALLER_CATEGORY_ADDFAIL') .'</p>';
		}

		//Create icons
		$icons = array();
		$icons[] = array('catid' => $id, 'Name' => 'MOD_QUICKICON_ADD_NEW_ARTICLE', 'Title' => 'MOD_QUICKICON_ADD_NEW_ARTICLE', 'Target' => 'index.php?option=com_content&task=article.add', 'Icon' => 'images/smarticons/icon-48-article-add.png', 'Display' => 1, 'published' => 1, 'ordering' => 1);
		$icons[] = array('catid' => $id, 'Name' => 'MOD_QUICKICON_ARTICLE_MANAGER', 'Title' => 'MOD_QUICKICON_ARTICLE_MANAGER', 'Target' => 'index.php?option=com_content', 'Icon' => 'images/smarticons/icon-48-article.png', 'Display' => 1, 'published' => 1, 'ordering' => 2);
		$icons[] = array('catid' => $id, 'Name' => 'MOD_QUICKICON_CATEGORY_MANAGER', 'Title' => 'MOD_QUICKICON_CATEGORY_MANAGER', 'Target' => 'index.php?option=com_categories&extension=com_content', 'Icon' => 'images/smarticons/icon-48-category.png', 'Display' => 1, 'published' => 1, 'ordering' => 3);
		$icons[] = array('catid' => $id, 'Name' => 'MOD_QUICKICON_MEDIA_MANAGER', 'Title' => 'MOD_QUICKICON_MEDIA_MANAGER', 'Target' => 'index.php?option=com_media', 'Icon' => 'images/smarticons/icon-48-media.png', 'Display' => 1, 'published' => 1, 'ordering' => 4);
		$icons[] = array('catid' => $id, 'Name' => 'MOD_QUICKICON_MENU_MANAGER', 'Title' => 'MOD_QUICKICON_MENU_MANAGER', 'Target' => 'index.php?option=com_menus', 'Icon' => 'images/smarticons/icon-48-menumgr.png', 'Display' => 1, 'published' => 1, 'ordering' => 5);
		$icons[] = array('catid' => $id, 'Name' => 'MOD_QUICKICON_USER_MANAGER', 'Title' => 'MOD_QUICKICON_USER_MANAGER', 'Target' => 'index.php?option=com_users', 'Icon' => 'images/smarticons/icon-48-user.png', 'Display' => 1, 'published' => 1, 'ordering' => 6);
		$icons[] = array('catid' => $id, 'Name' => 'MOD_QUICKICON_MODULE_MANAGER', 'Title' => 'MOD_QUICKICON_MODULE_MANAGER', 'Target' => 'index.php?option=com_modules', 'Icon' => 'images/smarticons/icon-48-module.png', 'Display' => 1, 'published' => 1, 'ordering' => 7);
		$icons[] = array('catid' => $id, 'Name' => 'MOD_QUICKICON_EXTENSION_MANAGER', 'Title' => 'MOD_QUICKICON_EXTENSION_MANAGER', 'Target' => 'index.php?option=com_installer', 'Icon' => 'images/smarticons/icon-48-extension.png', 'Display' => 1, 'published' => 1, 'ordering' => 8);
		$icons[] = array('catid' => $id, 'Name' => 'MOD_QUICKICON_LANGUAGE_MANAGER', 'Title' => 'MOD_QUICKICON_LANGUAGE_MANAGER', 'Target' => 'index.php?option=com_languages', 'Icon' => 'images/smarticons/icon-48-language.png', 'Display' => 1, 'published' => 1, 'ordering' => 9);
		$icons[] = array('catid' => $id, 'Name' => 'MOD_QUICKICON_GLOBAL_CONFIGURATION', 'Title' => 'MOD_QUICKICON_GLOBAL_CONFIGURATION', 'Target' => 'index.php?option=com_config', 'Icon' => 'images/smarticons/icon-48-config.png', 'Display' => 1, 'published' => 1, 'ordering' => 10);
		$icons[] = array('catid' => $id, 'Name' => 'MOD_QUICKICON_TEMPLATE_MANAGER', 'Title' => 'MOD_QUICKICON_TEMPLATE_MANAGER', 'Target' => 'index.php?option=com_templates', 'Icon' => 'images/smarticons/icon-48-themes.png', 'Display' => 1, 'published' => 1, 'ordering' => 11);
		$icons[] = array('catid' => $id, 'Name' => 'MOD_QUICKICON_PROFILE', 'Title' => 'MOD_QUICKICON_PROFILE', 'Target' => 'index.php?option=com_admin&task=profile.edit', 'Icon' => 'images/smarticons/icon-48-info.png', 'Display' => 1, 'published' => 1, 'ordering' => 12);

		require_once JPATH_BASE.DS.'components'.DS.'com_smarticons'.DS.'tables'.DS.'icon.php';
		$iconsSaved = 0;
		foreach ($icons as $icon) {
			$iconsTable = new SmartIconsTableIcon($db);
			$iconsTable->setRules('{"core.view":{"1":1},"core.delete":[],"core.edit":[],"core.edit.state":[]}');
			if ($iconsTable->save($icon)) {
				$iconsSaved++;
			}
		}
		echo '<p>'.JText::plural('COM_SMARTICONS_INSTALLER_N_ICONS_SAVED', $iconsSaved).'</p>';

		//Copy images to image folder
		$sourceDir = JPATH_BASE.DS.'components'.DS.'com_smarticons'.DS.'images';
		$targetDir = JPATH_SITE.DS.'images'.DS.'smarticons';
		if (!JFolder::exists($targetDir)) {
			if (JFolder::copy($sourceDir, $targetDir)) {
				JFolder::delete($sourceDir);
				echo '<p>'. JText::_('COM_SMARTICONS_INSTALLER_IMAGES_COPYSUCCESS') .'</p>';
			} else {
				echo '<p>'. JText::_('COM_SMARTICONS_INSTALLER_IMAGES_COPYFAIL') .'</p>';
			}
		} else {
			echo '<p>'. JText::_('COM_SMARTICONS_INSTALLER_IMAGES_EXISTS') .'</p>';
		}
		//Install module
		jimport( 'joomla.installer.installer' );

		$modulePath = JPATH_BASE. DS . 'components'. DS. 'com_smarticons'.DS .'module';

		$installer = new JInstaller();
		$installer->setOverwrite(true);
		if ($installer->install($modulePath)) {
			JFolder::delete($modulePath);
			echo '<p>'. JText::_('COM_SMARTICONS_INSTALLER_MODULE_INSTALL_SUCCESS') .'</p>';
		} else {
			echo '<p style="color:red">'. JText::_('COM_SMARTICONS_INSTALLER_MODULE_INSTALL_FAIL') .'</p>';
			echo nl2br($installer->message);
		}
		//Enable module

		$query = $db->getQuery(true);
		$query->select('id');
		$query->from('#__modules');
		$query->where('module = '. $db->Quote('mod_smarticons'));

		$db->setQuery($query);

		if ($id = $db->loadResult()) {
			$query = $db->getQuery(true);
			$query->update('#__modules');
			$query->set('published = 1');
			$query->set('position = \'icon\'');
			$query->set('ordering = 1');
			$query->set('access = 3');
			$query->where('id = ' .$db->Quote($id));
			$db->setQuery($query);
			if($db->query()) {
				$query = $db->getQuery(true);
				$query->insert('#__modules_menu');
				$query->set('moduleid = '.$db->Quote($id));
				$query->set('menuid = 0');
				$db->setQuery($query);
				if($db->query()) {
					echo '<p>'. JText::_('COM_SMARTICONS_INSTALLER_MODULE_ENABLE_SUCCESS') .'</p>';
				} else {
					echo '<p>'. JText::_('COM_SMARTICONS_INSTALLER_MODULE_ENABLE_FAIL') .'</p>';
				}
			} else {
				echo '<p>'. JText::_('COM_SMARTICONS_INSTALLER_MODULE_ENABLE_FAIL') .'</p>';
			}

		} else {
			echo '<p>'. JText::_('COM_SMARTICONS_INSTALLER_MODULE_ENABLE_FAIL') .'</p>';
		};
		//Disable mod_quickicons
		$query = $db->getQuery(true);

		$query->select('id');
		$query->from('#__modules');
		$query->where('module = '. $db->Quote('mod_quickicon'));

		$db->setQuery($query);
		if($id=$db->loadResult()) {
			$query = $db->getQuery(true);
			$query->update('#__modules');
			$query->set('published = 0');
			$query->where('id = '. $db->Quote($id));
			$db->setQuery($query);
			if ($db->query()) {
				echo '<p>'. JText::_('COM_SMARTICONS_INSTALLER_QUICKICON_DISABLE_SUCCESS') .'</p>';
			} else {
				echo '<p>'. JText::_('COM_SMARTICONS_INSTALLER_QUICKICON_DISABLE_FAIL') .'</p>';
			}
		} else {
			echo '<p>'. JText::_('COM_SMARTICONS_INSTALLER_QUICKICON_DISABLE_FAIL') .'</p>';
		}

	}

	/**
	 * method to uninstall the component
	 *
	 * @return void
	 */
	function uninstall($parent)
	{
		jimport( 'joomla.installer.installer' );

		$db = JFactory::getDbo();

		$query = $db->getQuery(true);
		$query->select('extension_id');
		$query->from('#__extensions');
		$query->where('element = \'mod_smarticons\'');

		$db->setQuery($query);
		if ($id = $db->loadResult()) {
			$installer = new JInstaller();
			if ($installer->uninstall('module', $id)) {
				echo '<p>'. JText::_('COM_SMARTICONS_INSTALLER_MODULE_UNINSTALL_SUCCESS') .'</p>';
			} else {
				echo '<p>'. JText::_('COM_SMARTICONS_INSTALLER_MODULE_UNINSTALL_FAIL') .'</p>';
			}
		} else {
			echo '<p>'. JText::_('COM_SMARTICONS_INSTALLER_MODULE_UNINSTALL_FAIL') .$id.'</p>';
		}

		//Enable mod_quickicons
		$query = $db->getQuery(true);

		$query->select('id');
		$query->from('#__modules');
		$query->where('module = '. $db->Quote('mod_quickicon'));

		$db->setQuery($query);
		if($id=$db->loadResult()) {
			$query = $db->getQuery(true);
			$query->update('#__modules');
			$query->set('published = 1');
			$query->where('id = '. $db->Quote($id));
			$db->setQuery($query);
			if ($db->query()) {
				echo '<p>'. JText::_('COM_SMARTICONS_INSTALLER_QUICKICON_ENABLE_SUCCESS') .'</p>';
			} else {
				echo '<p>'. JText::_('COM_SMARTICONS_INSTALLER_QUICKICON_ENABLE_FAIL') .'</p>';
			}
		} else {
			echo '<p>'. JText::_('COM_SMARTICONS_INSTALLER_QUICKICON_ENABLE_FAIL') .'</p>';
		}

		//Delete assets
		$query = $db->getQuery(true);
		$query->delete('#__assets');
		$query->where('name LIKE \'%com_smarticons.category%\'');

		$db->setQuery($query);
		$db->query();

		//Delete categories
		$query = $db->getQuery(true);
		$query->delete('#__categories');
		$query->where('extension = \'com_smarticons\'');

		$db->setQuery($query);
		if ($db->query()) {
			echo '<p>'. JText::_('COM_SMARTICONS_INSTALLER_CATEGORY_DELETESUCCESS') .'</p>';
		} else {
			echo '<p>'. JText::_('COM_SMARTICONS_INSTALLER_CATEGORY_DELETEFAIL') .'</p>';
		}
		// $parent is the class calling this method
		echo '<p>' . JText::_('COM_SMARTICONS_UNINSTALL_TEXT') . '</p>';
	}

	/**
	 * method to update the component
	 *
	 * @return void
	 */
	function update($parent) {

		// $parent is the class calling this method

		echo '<p>' . JText::_('COM_SMARTICONS_UPDATE_TEXT') . '</p>';

		jimport( 'joomla.installer.installer' );

		/*
		 * Uninstall old module
		 */
		$db = JFactory::getDbo();

		$query = $db->getQuery(true);
		$query->select('extension_id');
		$query->from('#__extensions');
		$query->where('element = \'mod_smarticons\'');

		$db->setQuery($query);
		if ($id = $db->loadResult()) {
			$installer = new JInstaller();
			if ($installer->uninstall('module', $id)) {
				echo '<p>'. JText::_('COM_SMARTICONS_INSTALLER_MODULE_UNINSTALL_SUCCESS') .'</p>';
			} else {
				echo '<p>'. JText::_('COM_SMARTICONS_INSTALLER_MODULE_UNINSTALL_FAIL') .'</p>';
			}
		} else {
			echo '<p>'. JText::_('COM_SMARTICONS_INSTALLER_MODULE_UNINSTALL_FAIL') .$id.'</p>';
		}
		/*
		 * Unistall complete
		 */

		/*
		 * Install new module
		 */
		$modulePath = JPATH_BASE. DS . 'components'. DS. 'com_smarticons'.DS .'module';

		$installer = new JInstaller();
		$installer->setOverwrite(true);
		if ($installer->install($modulePath)) {
			JFolder::delete($modulePath);
			echo '<p>'. JText::_('COM_SMARTICONS_INSTALLER_MODULE_INSTALL_SUCCESS') .'</p>';
		} else {
			echo '<p style="color:red">'. JText::_('COM_SMARTICONS_INSTALLER_MODULE_INSTALL_FAIL') .'</p>';
			echo nl2br($installer->message);
		}
		/*
		 * Install complete
		 */

		/*
		 * Enable module
		 */

		$query = $db->getQuery(true);
		$query->select('id');
		$query->from('#__modules');
		$query->where('module = '. $db->Quote('mod_smarticons'));

		$db->setQuery($query);

		if ($id = $db->loadResult()) {
			$query = $db->getQuery(true);
			$query->update('#__modules');
			$query->set('published = 1');
			$query->set('position = \'icon\'');
			$query->set('ordering = 1');
			$query->set('access = 3');
			$query->where('id = ' .$db->Quote($id));
			$db->setQuery($query);
			if($db->query()) {
				$query = $db->getQuery(true);
				$query->insert('#__modules_menu');
				$query->set('moduleid = '.$db->Quote($id));
				$query->set('menuid = 0');
				$db->setQuery($query);
				if($db->query()) {
					echo '<p>'. JText::_('COM_SMARTICONS_INSTALLER_MODULE_ENABLE_SUCCESS') .'</p>';
				} else {
					echo '<p>'. JText::_('COM_SMARTICONS_INSTALLER_MODULE_ENABLE_FAIL') .'</p>';
				}
			} else {
				echo '<p>'. JText::_('COM_SMARTICONS_INSTALLER_MODULE_ENABLE_FAIL') .'</p>';
			}

		} else {
			echo '<p>'. JText::_('COM_SMARTICONS_INSTALLER_MODULE_ENABLE_FAIL') .'</p>';
		};
		/*
		 * Enable complete
		 */
	}

	/**
	 * method to run before an install/update/uninstall method
	 *
	 * @return void
	 */
	function preflight($type, $parent) {
		/**
		 * Work arround to not have missign folder on update
		 */
		JFolder::create(JPATH_BASE.DS.'components'.DS.'com_smarticons'.DS.'module');
		// $parent is the class calling this method
		// $type is the type of change (install, update or discover_install)
		echo '<p>' . JText::_('COM_SMARTICONS_PREFLIGHT_' . $type . '_TEXT') . '</p>';
	}

	/**
	 * method to run after an install/update/uninstall method
	 *
	 * @return void
	 */
	function postflight($type, $parent) {
		$db = JFactory::getDbo();

		// $parent is the class calling this method
		// $type is the type of change (install, update or discover_install)
		echo '<p>' . JText::_('COM_SMARTICONS_POSTFLIGHT_' . $type . '_TEXT') . '</p>';
	}
}