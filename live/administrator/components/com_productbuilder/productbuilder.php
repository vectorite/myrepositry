<?php
/* 
* product builder component
* @package productbuilder
* @version $Id: admin.productbulder.php 2 2-Feb-2012 20:00:41Z sakisTerzis $
* @author Sakis Terzis (sakis@breakDesigns.net)
* @copyright	Copyright (C) 2008-2012 breakDesigns.net. All rights reserved
* @license	GNU/GPL v2
* see administrator/components/com_vmfiltering/COPYING.txt
*/

defined('_JEXEC') or die('Restricted access');
// Include dependencies
jimport('joomla.application.component.controller');

if ( ! defined('PB_ADMINISTRATOR') )	define ( 'PB_ADMINISTRATOR', JPATH_COMPONENT_ADMINISTRATOR.DS);
if(!defined('JPATH_PBVM_ADMIN')) define('JPATH_PBVM_ADMIN',JPATH_ROOT.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_virtuemart');

 require_once(PB_ADMINISTRATOR.DS.'helpers'.DS.'helper.php');

//get the language
if(!defined('VMLANG')){
jimport('joomla.language.helper');
$languages = JLanguageHelper::getLanguages('lang_code');
$jlang= JFactory::getLanguage();
$siteLang=$jlang->getTag();
//echo $siteLang;
$siteLang=strtolower(strtr($siteLang,'-','_'));
}else $siteLang=VMLANG;

//set the language that VM uses
/*@todo: check inside the functions that return the categories and the products if they return something
 * Maybe the tables exist but are empty
 */

//check if there are tables for the current language
//if not use the lang set as default in the VM config
if(!defined('VMLANGPRFX')){
	$db=JFactory::getDbo();
	$prefix=$db->getPrefix();
	//check for product tables
	$q="SHOW TABLES LIKE '{$prefix}virtuemart_products_{$siteLang}'";
	$db->setQuery($q);
	$result=$db->loadRow();
	
	
	if(!empty($result))define('VMLANGPRFX', $siteLang);
	else{
		require(JPATH_PBVM_ADMIN.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'config.php');
		VmConfig::loadConfig();
		$siteLang=VmConfig::get('vmlang');
		define('VMLANGPRFX', $siteLang);
	}
}

 
$document=JFactory::getDocument();
$jinput = JFactory::getApplication()->input;

//add scripts and styles
$document->addStyleSheet(JURI::root().'administrator/components/com_productbuilder/assets/css/stylesheet.css');
// Create the controller
$controller = JController::getInstance('productbuilder');
$controller->execute($jinput->get('task'));
$controller->redirect();
?>

<div class="footer">Copyright 2008-2012 <a href="http://breakdesigns.net">breakdesigns</a> under the <a href="http://www.gnu.org/licenses/gpl-2.0.html">GNU/GPL License</a></div>