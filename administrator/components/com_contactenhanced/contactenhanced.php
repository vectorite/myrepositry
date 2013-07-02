<?php
/**
 * @package		com_contactenhanced
* @copyright	Copyright (C) 2006 - 2010 Ideal Custom Software Development
 * @author     Douglas Machado {@link http://ideal.fok.com.br}
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
	
JHTML::_('stylesheet','administrator/components/com_contactenhanced/assets/css/contact_enhanced.css', array(), false);

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_contactenhanced')) {
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}
require_once (JPATH_ROOT.'/components/com_contactenhanced/helpers/helper.php');

// Include dependancies
jimport('joomla.application.component.controller');

$controller	= JController::getInstance('Contactenhanced');
$controller->execute(JRequest::getCmd('task'));

$controller->redirect();
