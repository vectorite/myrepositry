<?php
/**
 * @package SmartIcons Component for Joomla! 2.5
 * @version $Id: smarticons.php 9 2012-03-28 20:07:32Z Bobo $
 * @author SUTA Bogdan-Ioan
 * @copyright (C) 2011 SUTA Bogdan-Ioan
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

// No direct access to this file
defined('_JEXEC') or die;

abstract class SmartIconsHelper
{
	/**
	 * Configure the Linkbar.
	 */
	public static function addSubmenu($submenu) {
		JSubMenuHelper::addEntry(JText::_('COM_SMARTICONS_SUBMENU_ICONS'), 'index.php?option=com_smarticons', $submenu == 'icons');
		JSubMenuHelper::addEntry(JText::_('COM_SMARTICONS_SUBMENU_CATEGORIES'), 'index.php?option=com_categories&view=categories&extension=com_smarticons', $submenu == 'categories');
		
		$app = JFactory::getApplication();
		$template = $app->getTemplate();

		// set some global property
		$document = JFactory::getDocument();
		$document->addStyleDeclaration('.icon-48-smarticons {background-image: url(../media/com_smarticons/images/SmartIcons48x48.png);}');
		$document->addStyleDeclaration('.icon-32-download {background-image: url("'. JURI::base(true) . '/templates/' . $template. '/images/toolbar/icon-32-download.png");}');
		
		if ($submenu == 'categories')
		{
			$document->setTitle(JText::_('COM_SMARTICONS_ADMINISTRATION_CATEGORIES'));
		}
	}
	/**
	 * Get the actions
	 */
	public static function getActions($idIcon = 0)
	{
		$user  = JFactory::getUser();
		$result        = new JObject;

		if (empty($idIcon)) {
			$assetName = 'com_smarticons';
		}
		else {
			$assetName = 'com_smarticons.icon.'.(int) $idIcon;
		}

		$actions = array(
			'core.view',
			'core.admin', 
			'core.manage', 
			'core.create', 
			'core.edit', 
			'core.edit.state', 
			'core.delete'
		);

		foreach ($actions as $action) {
			$result->set($action, $user->authorise($action, $assetName));
		}

		return $result;
	}
}