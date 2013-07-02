<?php
/**
 * @version 2.0.1 2012-08-17
 * @package Joomla
 * @subpackage Work Force
 * @copyright (C) 2012 the Thinkery
 * @license GNU/GPL see LICENSE.php
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import joomla controller library
jimport('joomla.application.component.controller');

require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'classes'.DS.'admin.class.php');
require_once(JPATH_COMPONENT.DS.'helpers'.DS.'html.helper.php');
require_once(JPATH_COMPONENT.DS.'helpers'.DS.'employee.php');
require_once(JPATH_COMPONENT.DS.'helpers'.DS.'department.php');
require_once(JPATH_COMPONENT.DS.'helpers'.DS.'query.php');
require_once(JPATH_COMPONENT.DS.'helpers'.DS.'route.php');

// Get an instance of the controller
$controller = JController::getInstance('Workforce');

// Perform the Request task
$controller->execute(JRequest::getCmd('task'));

// Redirect if set by the controller
$controller->redirect();
?>

