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
if (!defined('_JEXEC'))
	die('Direct Access to this location is not allowed.');

// sometimes users disable our plugin
if (!defined('SH404SEF_AUTOLOADER_LOADED'))
{
	echo 'sh404SEF system plugin has been disabled or has failed initializing. Please enable it again to use sh404SEF, with Joomla! <a href="index.php?option=com_plugins">plugin manager</a>';
	return;
}

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_sh404sef'))
{
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

// Setup paths.
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

JHtml::_('behavior.framework');

// include sh404sef default language file
shIncludeLanguageFile();

// find about specific controller requested
$cName = JRequest::getCmd('c');

// get controller from factory
$controller = Sh404sefFactory::getController($cName);

Sh404sefHelperHtml::addSubmenu(JRequest::get());
// read and execute task
$controller->execute(JRequest::getCmd('task'));
$controller->redirect();

