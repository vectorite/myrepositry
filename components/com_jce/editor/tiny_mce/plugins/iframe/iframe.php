<?php
/**
 * @package     JCE
 * @copyright   Copyright (C) 2005 - 2012 Ryan Demmer. All rights reserved.
 * @author	Ryan Demmer
 * @license     GNU/GPL
 * JCE is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */
require_once(dirname(__FILE__) .DS. 'classes' .DS. 'iframe.php');

$plugin = WFIframePlugin::getInstance();
$plugin->execute();