<?php
/**
 * @version		$Id: controller.php 763 2012-01-04 15:07:52Z joomlaworks $
 * @package		Frontpage Slideshow
 * @author		JoomlaWorks http://www.joomlaworks.gr
 * @copyright	Copyright (c) 2006 - 2012 JoomlaWorks Ltd. All rights reserved.
 * @license		Commercial - This code cannot be redistributed without permission from JoomlaWorks Ltd.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

class FPSSController extends JController {

	function display() {
		$document = &JFactory::getDocument();
		$viewType = $document->getType();
		$viewLayout = JRequest::getCmd('layout', 'default');
		$view = &$this->getView('slideshow', $viewType);
		$view->setLayout($viewLayout);
		if($viewType != 'feed') {
			$cache =& JFactory::getCache('com_fpss', 'view');
			$cache->get($view, 'display');
		}
		else {
			$view->display();
		}

	}

	function module() {
		$document = &JFactory::getDocument();
		$viewType = $document->getType();
		$viewLayout = JRequest::getCmd('layout', 'default');
		$view = &$this->getView('slideshow', $viewType);
		$view->setLayout($viewLayout);
		if($viewType != 'feed') {
			$cache =& JFactory::getCache('com_fpss', 'view');
			$cache->get($view, 'module');
		}
		else {
			$view->module();
		}
	}

	function track() {
		$params = &JComponentHelper::getParams('com_fpss');
		if(!$params->get('stats', 1)){
			JError::raiseError(404, JText::_('FPSS_PAGE_NOT_FOUND'));
		}
		JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.DS.'tables');
		$mainframe = &JFactory::getApplication();
		$id = JRequest::getInt('id');
		$url = JRequest::getString('url');
		$url = base64_decode($url);
		$slide = &JTable::getInstance('slide', 'FPSS');
		$slide->load($id);
		if(!$slide->id){
			$mainframe->redirect(JURI::root());
		}
		if($slide->referenceType == 'custom'){
			$url = $slide->custom;
		}
		else {
			if(!JURI::isInternal($url)){
				$mainframe->redirect(JURI::root());
			}
		}
		$slide->hit();
		$date = &JFactory::getDate();
		$now = $date->toMySQL();
		$db = &JFactory::getDBO();
		$query = "INSERT INTO #__fpss_stats VALUES('',{$id}, ".$db->Quote($now).")";
		$db->setQuery($query);
		$db->query();
		$mainframe->redirect($url);
	}

}






