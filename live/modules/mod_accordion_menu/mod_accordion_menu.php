<?php 
/*------------------------------------------------------------------------
# mod_jo_accordion - Vertical Accordion Menu for Joomla 1.5 
# ------------------------------------------------------------------------
# author    Roland Soos 
# copyright Copyright (C) 2011 Offlajn.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.offlajn.com
-------------------------------------------------------------------------*/
$revision = '7.219.282';

?>
<?php
// Report all PHP errors (see changelog)
defined('_JEXEC') or die('Restricted access');

global $ImageHelper, $bgHelper; 

@ini_set('memory_limit','260M');
if (!extension_loaded('gd') || !function_exists('gd_info')) {
    echo "The Accordion Menu needs the <a href='http://php.net/manual/en/book.image.php'>GD module</a> enabled in your PHP runtime 
    environment. Please consult with your System Administrator and he will 
    enable it!";
    return;
}
require_once(dirname(__FILE__).DS.'helpers'.DS.'functions.php');

/* For demo parameter editor */
if(defined('DEMO')){
  $_SESSION['module_id'] = $module->id;
  if(!isset($_SESSION[$module->module.'a'][$module->id])){
    $_SESSION[$module->module.'a'] = array();
    $a = $params->toArray();
    $a['params'] = $a;
    $params->loadArray($a);
    $_SESSION[$module->module."_orig"] = $params->toString();
    $_SESSION[$module->module.'a'][$module->id] = true;
    $_SESSION[$module->module."_params"] = $params->toString();
    header('LOCATION: '.$_SERVER['REQUEST_URI']);
  }
  if(isset($_SESSION[$module->module."_params"])){				
    $params = new JRegistry();
    $params->loadJSON($_SESSION[$module->module."_params"]);
  }
  $a = $params->toArray();
  require_once(dirname(__FILE__).DS.'params'.DS.'offlajndashboard'.DS.'library'.DS.'flatArray.php');
  $params->loadArray(o_flat_array($a['params']));
}
$module->navClassPrefix = 'off-nav-';
$module->instanceid = 'offlajn-accordion-'.$module->id.'-1';
$module->containerinstanceid = $module->instanceid.'-container';

if(version_compare(JVERSION,'1.6.0','ge')) {
  $params->loadArray(o_flat_array($params->toArray()));
}

if(defined('DEMO')){
  $themesdir = JPATH_SITE.DS.'modules'.DS.$module->module.DS.'themes'.DS;
  $xmlFile = $themesdir.$params->get('theme', 'default').'/theme.xml';
  //$xml->loadFile( $xmlFile );
  $xml = new SimpleXMLElement(file_get_contents($xmlFile));
  $skins = $xml->params[0]->param[0];
  $sks = array();
  foreach($skins->children() AS $skin){
    $sks[] = $skin->getName();
  }
  DojoLoader::addScript('window.skin = new Skinchanger({theme: "'.$params->get('theme', 'default').'",skins: '.json_encode($sks).'});');
  if(isset($_REQUEST['skin']) && $skins->{$_REQUEST['skin']}){
    $skin = $skins->{$_REQUEST['skin']}[0];
    foreach($skin AS $s){
      $name = $s->getName();
      $value = (string)$s;
      $params->set($name, $value);
    }
    $_SESSION[$module->module."_params"] = $params->toString();
  }
}

/*
Loading the right class for the menu type
*/
$type = preg_replace("/[^A-Za-z0-9]/", '', $params->get('menutype'));
if($type == '' or !file_exists(dirname(__FILE__).DS.'types'.DS.$type.DS.'menu.php')){
  echo JText::_('Menu type not exists!');
  return;
}

require_once(dirname(__FILE__).DS.'types'.DS.$type.DS.'menu.php');

$class = 'Offlajn'.ucfirst($type).'Menu';
if(!class_exists($class)) return;
$menu = new $class($module, $params);
$menu->generateItems();

/*
Loading the template file for the theme
*/
$templateDir = dirname(__FILE__).DS.'template'.DS;

$theme = $params->get('theme', 'default');
$tmpl = $templateDir.$theme.'.php';

if(!file_exists($tmpl)){
  $tmpl = $templateDir.'default.php';
  if(!file_exists($tmpl)){
    echo JText::_('Template file missing for Accordion menu. Please reinstall the module.');
    return;
  }
}

/*
Loading the template container file for the theme
*/
$containerTmpl = $templateDir.$theme.'-cont.php';

if(!file_exists($containerTmpl)){
  $containerTmpl = $templateDir.'default-cont.php';
  if(!file_exists($containerTmpl)){
    echo JText::_('Template file missing for Accordion menu. Please reinstall the module.');
    return;
  }
}
?>
<div class="noscript">
<?php
/*
Render the menu
*/
include($containerTmpl);
?>
</div>
<?php
/*
Build the Javascript cache and scopes
*/ 
require_once(dirname(__FILE__).DS.'classes'.DS.'cache.class.php');
$cache = new OfflajnMenuThemeCache('default', $module, $params);

DojoLoader::r('dojo.fx.easing');
DojoLoader::r('dojo.regexp');
DojoLoader::r('dojo.cookie');

DojoLoader::addScriptFile(DS.'modules'.DS.$module->module.DS.'js'.DS.'accordionmenu.js');

$document =& JFactory::getDocument();

/*
Build the CSS
*/ 
$cache->addCss(dirname(__FILE__) .DS. 'themes' .DS. 'clear.css.php');
$cache->addCss(dirname(__FILE__) .DS. 'themes' .DS. $theme .DS. 'theme.css.php');

$cache->assetsAdded();

/*
Load image helper
*/
//require_once(dirname(__FILE__).DS.'classes'.DS.'ImageHelper.php');

/*
Set up enviroment variables for the cache generation
*/
$module->url = JUri::root(true).'/modules/'.$module->module.'/';
$cache->addCssEnvVars('module', $module);

//$cache->addCssEnvVars('helper', new OfflajnAccordionHelper7($cache->cachePath, $cache->cacheUrl));

$ImageHelper = new OfflajnImageHelper($cache->cachePath, $cache->cacheUrl);
$bgHelper = new OfflajnBgHelper($cache->cachePath, $cache->cacheUrl);

/*
Add cached contents to the document
*/
$cacheFiles = $cache->generateCache();
$document->addStyleSheet($cacheFiles[0]);
$document->addStyleDeclaration('
.noscript div#'.$module->instanceid.'-container dl.level1 dl{
  position: static;
}
.noscript div#'.$module->instanceid.'-container dl.level1 dd.parent{
  height: auto !important;
  display: block;
  visibility: visible;
}
');

$interval = (array)OfflajnValueParser::parse($params->get( 'duration', '500' ));

DojoLoader::addScript("
  dojo.query('.noscript').removeClass('noscript');
  new AccordionMenu({
    node: dojo.byId('".$module->instanceid."'),
    instance: '".$module->instanceid."',
    classPattern: /".$module->navClassPrefix."[0-9]+/,
    mode: '".$params->get( 'mode', 'onclick' )."', 
    interval: '".$interval[0]."', 
    level: 1,
    easing:  ".$params->get( 'easing', 'dojo.fx.easing.cubicInOut' ).",
    closeeasing:  ".$params->get( 'closeeasing', 'dojo.fx.easing.cubicInOut' ).",
    accordionmode:  ".$params->get( 'accordionmode', 1 )."
  });
");

?>