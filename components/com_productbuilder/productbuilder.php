<?php
/**
 * product builder component
 * @version $Id:productbuilder.php 2012-2-24 12:27 sakisTerz $
 * @package product builder  front-end
 * @author Sakis Terzis (sakis@breakDesigns.net)
 * @copyright	Copyright (C) 2009-2012 breakDesigns.net. All rights reserved
 * @license	GNU/GPL v2
 */


defined('_JEXEC')or die('Restricted access');
// Include dependencies
jimport('joomla.application.component.controller');
JHTML::_('behavior.framework');
JHTML::_('behavior.tooltip');
JHTML::_('behavior.modal');
$document=JFactory::getDocument();

if(!defined ("PB")) define ( 'PB', JPATH_COMPONENT.DIRECTORY_SEPARATOR);
if(!defined('JPATH_PBVM_ADMIN')) define('JPATH_PBVM_ADMIN',JPATH_ROOT.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart');
if(!defined('JPATH_PBVM_SITE')) define('JPATH_PBVM_SITE',JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart');

//load the Virtuemart configuration and helpers
require(JPATH_PBVM_ADMIN.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php');
VmConfig::loadConfig();
require(JPATH_PBVM_SITE.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'shopfunctionsf.php');
if (!class_exists( 'VmModel' )) require(JPATH_PBVM_ADMIN.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'vmmodel.php');

/*
 * check if VM is using scripts
 * if yes we should load JQuery
 * as the vmproductmodel->getProducts is always generating a jquery script at the doc's head
 */
if (VmConfig::get('jquery',true )){
	$document->addScript(JURI::root(true).'/components/com_virtuemart/assets/js/jquery.min.js');
	$document->addScript(JURI::root(true).'/components/com_virtuemart/assets/js/jquery-ui.min.js');
	$document->addScript(JURI::base().'components/com_virtuemart/assets/js/jquery.noConflict.js'); 
}

//set the language prefix that PB uses
if(!defined('PBLANG')){
	jimport('joomla.language.helper');
	$jlang= JFactory::getLanguage();
	$siteLang=$jlang->getTag();
	define('PBLANG',$siteLang);
}
//set the language prefix that VM uses
if(!defined('PBVMLANG'))define('PBVMLANG',strtolower(str_ireplace('-', '_', PBLANG)));

$mycontroller=JController::getInstance('Productbuilder');
$task=JRequest::getCmd('task','display');
$mycontroller->execute($task);
$mycontroller->redirect();

?>
