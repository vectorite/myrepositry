<?php
/**
 * @version		$Id: admin.fpss.php 769 2012-01-05 17:03:14Z joomlaworks $
 * @package		Frontpage Slideshow
 * @author		JoomlaWorks http://www.joomlaworks.gr
 * @copyright	Copyright (c) 2006 - 2012 JoomlaWorks Ltd. All rights reserved.
 * @license		Commercial - This code cannot be redistributed without permission from JoomlaWorks Ltd.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');
jimport('joomla.application.component.model');
jimport('joomla.application.component.view');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');
jimport('joomla.html.pagination');

if(version_compare(JVERSION,'1.6.0','ge')) {
	require_once(JPATH_COMPONENT.DS.'helpers'.DS.'permissions.php');
	FPSSHelperPermissions::checkAccess();
}

JTable::addIncludePath(JPATH_COMPONENT.DS.'tables');
JModel::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_fpss'.DS.'models');

$document = JFactory::getDocument();

// CSS
$document->addStyleSheet(JURI::base(true).'/components/com_fpss/css/uniform.default.css');
$document->addStyleSheet(JURI::base(true).'/components/com_fpss/css/jquery.ui.custom.css');
$document->addStyleSheet(JURI::base(true).'/components/com_fpss/css/style.css');

// JS
if(version_compare(JVERSION,'1.6.0','ge')) {
	JHtml::_('behavior.framework');
} else {
	JHTML::_('behavior.mootools');
}
$document->addScript(JURI::base(true).'/components/com_fpss/js/jquery.min.js');
$document->addScript(JURI::base(true).'/components/com_fpss/js/jquery.ui.custom.min.js');
$document->addScript(JURI::base(true).'/components/com_fpss/js/jquery.uniform.min.js');
$document->addScript(JURI::base(true).'/components/com_fpss/js/fpss.js');

$view = JRequest::getWord('view', 'slides');
if (JFile::exists(JPATH_COMPONENT.DS.'controllers'.DS.$view.'.php')) {
	require_once (JPATH_COMPONENT.DS.'controllers'.DS.$view.'.php');
	$classname = 'FPSSController'.$view;
	$controller = new $classname();
	$controller->execute(JRequest::getWord('task'));
	$controller->redirect();
}

?>

<div id="fpssAdminFooter">
	<a href="https://www.frontpageslideshow.net" target="_blank">Frontpage Slideshow v3.1.0</a> | Copyright &copy; 2006-<?php echo date('Y'); ?> <a href="https://www.joomlaworks.gr/" target="_blank">JoomlaWorks Ltd.</a>
</div>
