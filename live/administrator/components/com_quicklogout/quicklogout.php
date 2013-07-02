<?php
/**
 * @version     1.7.0
 * @package     com_quicklogout
 * @copyright   Copyright (C) 2011. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Created by com_combuilder - http://www.notwebdesign.com
 */


// no direct access
defined('_JEXEC') or die;

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_quicklogout')) {
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

// Include dependancies
jimport('joomla.application.component.controller');

//$controller	= JController::getInstance('Quicklogout');
//$controller->execute(JRequest::getCmd('task'));
//$controller->redirect();

echo "The Quick Logout component does not have any administrative options." ;
echo "<br /><br />" ;
echo "It's purpose is to simply provide a menu type that provides a quick, one click logout."  ;
echo "<br /><br />" ;
echo '<h3>How to use the Quick Logout Component</h3>' ;
echo "To use this component, simply create a new menu item and select Quick Logout from the menu item type.";
echo "<br /><br />" ;
echo "Then, set the access level to Registered so the option only appears when a user is logged in.";
echo "<br /><br />" ;
echo '<h3>Making your Login menu item disappear</h3>' ;

echo "If you have a Login menu item that you would like to make disappear when the user logs in, then ";
echo "refer to the Joomla documentation on how to implement a Guest mode. " ;
echo "<br /><br />" ;
echo "You can access the instructions on how to use the Joomla \"Guest Feature\" " ;
echo "<a href= \"http://docs.joomla.org/How_do_you_hide_something_from_logged_in_users%3F\" target = \"_blank\">here.</a>  " ;
echo "There is also a helpful \"Walk Through Tutorial\" " ;
echo "<a href= \"http://docs.joomla.org/Help16:Users_Options\" target = \"_blank\">here.</a>" ;

