<?php

/*
*
* @copyright Copyright (C) 2007 - 2012 RuposTel - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* One Page checkout is free software released under GNU/GPL and uses code from VirtueMart
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* 
*/
// no direct access
defined('_JEXEC') or die('Restricted access');

$_SESSION['startmem'] = memory_get_usage(true);
//get active language
$lang = JFactory::getLanguage();
$active_lang = trim($lang->get('backwardlang'));

/*
//get  language file
if( file_exists(dirname(__FILE__).DS.'languages'.DS.$active_lang.'.php')) {
	require_once(dirname(__FILE__).DS.'languages'.DS.$active_lang.'.php');
} 
else {
	require(dirname(__FILE__).DS.'languages'.DS.'english.php');
}
*/


//get base controller
require_once (JPATH_COMPONENT.DS.'controllerBase.php');
//get query variables
 
$cmd = JRequest::getCmd('task', 'display');
$task = JRequest::getCmd('task', 'display');
// will set default holder
if (file_exists(JPATH_COMPONENT.DS.'controllers'.DS.'config.php'))
$controller=JRequest::getCmd('view', 'config');
else $controller=JRequest::getcmd('view', 'orders');
//echo $controller; die();
$controllerPath    = JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php';
 
if (file_exists($controllerPath)) {
        require_once($controllerPath);
} else {
        JError::raiseError(500, 'Invalid Controller');
}
 
$controllerClass = 'JController'.ucfirst($controller);
if (class_exists($controllerClass)) {
    $controller = new $controllerClass();
} else {
    JError::raiseError(500, 'Invalid Controller Class');
}

$format = JRequest::getVar('format', ''); 

if (empty($task)) $task = 'display'; 
$controller->execute($task);

if($task != 'display')
{
	$controller->redirect();
}

?>

