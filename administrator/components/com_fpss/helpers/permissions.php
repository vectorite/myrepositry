<?php
/**
 * @version		$Id: permissions.php 766 2012-01-05 12:05:29Z lefteris.kavadas $
 * @package		Frontpage Slideshow
 * @author		JoomlaWorks http://www.joomlaworks.gr
 * @copyright	Copyright (c) 2006 - 2012 JoomlaWorks Ltd. All rights reserved.
 * @license		Commercial - This code cannot be redistributed without permission from JoomlaWorks Ltd.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

class FPSSHelperPermissions {

	function checkAccess() {
		// Set some variables
		$mainframe = JFactory::getApplication();
		$user = JFactory::getUser();
		$option = JRequest::getCmd('option');
		$view = JRequest::getCmd('view');
		$task = JRequest::getCmd('task');
		$id = JRequest::getInt('id');

		//Generic manage check
		if (!$user->authorise('core.manage', $option)) {
			JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
			$mainframe->redirect('index.php');
		}

		// Determine action for rest checks
		$action = false;
		if($view == 'slides' || $view == 'categories') {
			switch($task){
				case 'add':
					$action = 'core.create';
					break;
				case 'edit':
					if($id) {
						$action = FPSSHelperPermissions::determineEditAction($id);
					}
					else {
						$action = 'core.edit';
					}
					break;
				case 'remove':
					$action = 'core.delete';
					break;
				case 'publish':
				case 'unpublish':
					$action = 'core.edit.state';
			}
		}
		else if($view == 'slide' || $view == 'category') {
			switch($task){
				case '':
				case 'save':
				case 'saveAndNew':
				case 'apply':
					if(!$id){
						$action = 'core.create';
					}
					else {
						$action = FPSSHelperPermissions::determineEditAction($id);
					}
					break;
			}
				
		}

		// Check the determined action
		if($action){
			if(!$user->authorize($action, $option)){
				JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
				$mainframe->redirect('index.php?option=com_fpss');
			}
		}

	}

	function determineEditAction($id) {
		$user = JFactory::getUser();
		JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.DS.'tables');
		$slide = JTable::getInstance('Slide', 'FPSS');
		$slide->load($id);
		if($slide->created_by == $user->id){
			return 'core.edit.own';
		}
		else {
			return 'core.edit';
		}
	}
}