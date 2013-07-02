<?php
/**
 * @package SmartIcons Module for Joomla! 2.5
 * @version $Id: helper.php 8 2011-08-28 15:07:19Z bobo $
 * @author SUTA Bogdan-Ioan
 * @copyright (C) 2011 SUTA Bogdan-Ioan
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 **/

// No direct access.
defined('_JEXEC') or die;

abstract class SmartIconsHelper
{
	/**
	 * Stack to hold default buttons
	 *
	 * @since	1.3
	 */
	protected static $buttons = array();

	/**
	 * Stack to hold buttons from plugins
	 *
	 * @since	1.3
	 */
	protected static $plugins = array();

	/**
	 * Variable to hold the global configuration
	 */
	protected static $globalConfig = array();

	/**
	 * Helper method to generate a button in administrator panel
	 *
	 * @param	array	A named array with keys link, image, text, access and imagePath
	 *
	 * @return	string	HTML for button
	 * @since	1.6
	 */
	public static function button($button) {
		/*
		 * Verificari de acces pentru buton
		*/
		$globals = self::getComponentConfig();

		// Load the JSON string
		$params = new JRegistry;
		$params->loadString($button->params);
		$button->params = $params;

		// Merge global params with item params
		$params = clone $globals;
		$params->merge($button->params);
		$button->params = $params->toObject();

		$access = true;

		if (isset($button->params->accessCheck)) {
			if (!$button->params->accessCheck) {
				$access = SmartIconsHelper::checkAccess($button);
			}
		} else {
			$access = SmartIconsHelper::checkAccess($button);
		}
		
		if (!$access) {
			return;
		}
		
		ob_start();
		require JModuleHelper::getLayoutPath('mod_smarticons', 'default_button');
		$html = ob_get_clean();
		return $html;
	}
	
	public static function checkAccess($button) {
		$task = array();
		$component = array();
		$access = array();
		$access[] = array('core.view', 'com_smarticons.icon.' . $button->idIcon);
		if (preg_match('/option=(\b[a-zA-Z0-9_]*)/', $button->Target, $component)) {
			$access[] = array('core.manage', $component[1]);
		}
		if (preg_match('/task=(\b[a-zA-Z0-9\.]*\b)/', $button->Target, $task)) {
			$task = explode('.',$task[1]);
			switch ($task[1]) {
				case 'add':
					$access[] = array('core.create', $component[1]);
					break;
				case 'edit':
					$access[] = array('core.edit', $component[1]);
					break;
				default:
					break;
			}
		}
		foreach ($access as $permision) {
			if(!JFactory::getUser()->authorise($permision[0], $permision[1])) {
				return false;
			}
		};
		
		return true;
	} 

	public static function plugin($button) {
		$globals = self::getComponentConfig();

		// Load the JSON string
		$params = new JRegistry;
		$params->loadString($button->params);
		$button->params = $params;

		ob_start();
		require JModuleHelper::getLayoutPath('mod_smarticons', 'default_button');
		$html = ob_get_clean();
		return $html;
	}

	public static function plugins() {
		$pluginIcons = array();

		//Extract the quickicon plugins icons
		JPluginHelper::importPlugin('quickicon');
		$app = JFactory::getApplication();
			
		//Default quickicon plugins only render icons only if the context is "mod_quickicon"
		//We simulate this by passing a simulated context
		$pluginArray = (array) $app->triggerEvent('onGetIcons', array('mod_quickicon'));

		if (!empty($pluginArray)) {
			//Once we have the icons, we parse them to get them to a format the template understands
			foreach ($pluginArray as $plugin) {
				foreach ($plugin as $icon) {
					$button = new stdClass();
					$button->id = $icon['id'];
					$button->Icon = $icon['image'];
					$button->Target = $icon['link'];
					$button->Name = $icon['text'];
					$button->Display = 1;
					$button->params = "";
					$pluginIcons[] = $button;
				};
			}

			foreach ($pluginIcons as $plugin) {
				echo self::plugin($plugin);
			}
		}
	}

	/**
	 * Helper method to return button list.
	 *
	 * This method returns the array by reference so it can be
	 * used to add custom buttons or remove default ones.
	 *
	 * @return	array	An array of buttons
	 * @since	1.6
	 */
	public static function &getButtons() {
		if (empty(self::$buttons)) {
			//Load the translation of the QuickIcon module to have standard trnaslations available
			JFactory::getLanguage()->load('mod_quickicon');

			$db = JFactory::getDbo();
			$query = $db->getQuery(true);

			//Create the query to extract the plugins
			$query->select('Tab.title AS Tab, Tab.id as TabId, Icon.idIcon, Icon.Name, Icon.Title');
			$query->select('Icon.Text, Icon.Target, Icon.Icon, Icon.Display, Icon.params');
			$query->from('#__com_smarticons AS Icon');
			$query->innerjoin('#__categories AS Tab ON Icon.catid = Tab.id');
			$query->where('Icon.published = 1');
			$query->where('Tab.published = 1');
			$query->order('Tab.lft, Icon.ordering');

			$db->setQuery($query);

			if($icons = $db->loadObjectList()) {
				self::$buttons = $icons;
			};

		}
		return self::$buttons;
	}

	public static function getComponentConfig() {
		if (empty(self::$globalConfig)) {
			self::$globalConfig = JComponentHelper::getParams('com_smarticons');
		}
		return self::$globalConfig;
	}
}
