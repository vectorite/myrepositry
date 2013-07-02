<?php
/**
 * @version		$Id: fpss.php 763 2012-01-04 15:07:52Z joomlaworks $
 * @package		Frontpage Slideshow
 * @author		JoomlaWorks http://www.joomlaworks.gr
 * @copyright	Copyright (c) 2006 - 2012 JoomlaWorks Ltd. All rights reserved.
 * @license		Commercial - This code cannot be redistributed without permission from JoomlaWorks Ltd.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');
jimport('joomla.application.component.model');
jimport('joomla.application.component.view');
jimport('joomla.application.module.helper');
jimport('joomla.filesystem.file');
$language = &JFactory::getLanguage();
$language->load('com_fpss', JPATH_ADMINISTRATOR);
require_once(JPATH_COMPONENT.DS.'controller.php');
$controller = new FPSSController();
$controller->execute(JRequest::getWord('task'));
$controller->redirect();