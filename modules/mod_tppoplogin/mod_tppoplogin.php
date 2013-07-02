<?php
/**
 * @version		$Id: mod_tppoplogin.php 2.0 - December 2011
 * @converted by Rony S Y Zebua
 * @www.templateplazza.com
 * @package		Joomla.Site
 * @joomla version: Joomla 1.7.x
 * @subpackage	mod_tppoplogin
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

// Include the syndicate functions only once
require_once dirname(__FILE__).'/helper.php';

$params->def('greeting', 1);

$type	= modTpPopLoginHelper::getType();
$return	= modTpPopLoginHelper::getReturnURL($params, $type);
$user	= JFactory::getUser();

require JModuleHelper::getLayoutPath('mod_tppoplogin', $params->get('layout', 'default'));
