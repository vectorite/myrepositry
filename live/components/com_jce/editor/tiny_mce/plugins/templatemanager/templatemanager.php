<?php
/**
* @package JCE Template Manager
* @copyright Copyright (C) 2005 - 2010 Ryan Demmer. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see licence.txt
* JCE Template Manager is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
*/
require_once(dirname( __FILE__ ).DS.'classes'.DS.'templatemanager.php');

$plugin = WFTemplateManagerPlugin::getInstance();
$plugin->execute();
?>