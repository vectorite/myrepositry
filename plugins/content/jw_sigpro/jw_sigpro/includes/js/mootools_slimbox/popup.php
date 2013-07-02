<?php
/**
 * @version		2.5.7
 * @package		Simple Image Gallery Pro
 * @author		JoomlaWorks - http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2012 JoomlaWorks Ltd. All rights reserved.
 * @license		Commercial - This code cannot be redistributed without permission from JoomlaWorks Ltd.
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

$relName = 'lightbox';

if(version_compare(JVERSION,'1.6.0','ge')){
	$mtVersion = 'slimbox-1.8';
} else {
	if(JPluginHelper::isEnabled('system','mtupgrade')){
		$mtVersion = 'slimbox-1.71a';
	} else {
		$mtVersion = 'slimbox-1.58';
	}
}

$stylesheets = array($mtVersion.'/css/slimbox.css');
$stylesheetDeclarations = array();
$scripts = array($mtVersion.'/js/slimbox.js');
$scriptDeclarations = array();
