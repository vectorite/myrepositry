<?php
/**
 * @package SmartIcons Module for Joomla! 2.5
 * @version $Id: mod_smarticons.php 8 2011-08-28 15:07:19Z bobo $
 * @author SUTA Bogdan-Ioan
 * @copyright (C) 2011 SUTA Bogdan-Ioan
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
// No direct access.
defined('_JEXEC') or die;

//Add styling
$document = JFactory::getDocument();
$document->addStyleSheet("modules/mod_smarticons/CSS/smarticons.css");

require_once dirname(__FILE__).DS.'helper.php';
require JModuleHelper::getLayoutPath('mod_smarticons', $params->get('layout', 'default'));