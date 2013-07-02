<?php
/**
 * @version 2.0.1 2012-08-17
 * @package Joomla
 * @subpackage Work Force
 * @copyright (C) 2012 the Thinkery
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_workforce')) {
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

require_once( JPATH_COMPONENT.DS.'controller.php' );
require_once (JPATH_COMPONENT.DS.'classes'.DS.'admin.class.php');
require_once (JPATH_COMPONENT.DS.'classes'.DS.'icon.class.php');
require_once (JPATH_COMPONENT.DS.'helpers'.DS.'workforce.php');
require_once (JPATH_COMPONENT_SITE.DS.'helpers'.DS.'html.helper.php');
require_once (JPATH_COMPONENT_SITE.DS.'helpers'.DS.'query.php');

// Include dependancies
jimport('joomla.application.component.controller');

// Require specific controller if requested
if($controller = JRequest::getWord('controller')) {
    
    $path = JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php';
    if (file_exists($path)) {
        require_once $path;
    } else {
        $controller = '';
    }

    // Create the controller
    $classname    = 'WorkforceController'.$controller;
    $controller   = new $classname( );

    // Perform the Request task
    $controller->execute( JRequest::getVar( 'task' ) );

    // Redirect if set by the controller
    $controller->redirect();
}else{
    // Execute the task.
    $controller	= JController::getInstance('Workforce');
    $controller->execute(JRequest::getCmd('task'));
    $controller->redirect();
}