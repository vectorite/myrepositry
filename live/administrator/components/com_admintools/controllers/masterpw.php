<?php
/**
 *  @package AdminTools
 *  @copyright Copyright (c)2010-2011 Nicholas K. Dionysopoulos
 *  @license GNU General Public License version 3, or later
 *  @version $Id$
 */

// Protect from unauthorized access
defined('_JEXEC') or die('Restricted Access');

jimport('joomla.application.component.controller');

class AdmintoolsControllerMasterpw extends FOFController
{
	public function __construct($config = array()) {
		parent::__construct($config);
		$this->modelName = 'masterpw';
	}
	
	public function execute($task) {
		if($task != 'save') $task = 'browse';
		parent::execute($task);
	}
	
	public function save()
	{
		// CSRF prevention
		if(!JRequest::getVar(JUtility::getToken(), false, 'POST')) {
			JError::raiseError('403', JText::_('Request Forbidden'));
		}
		
		$masterpw = JRequest::getVar('masterpw','');
		$views = JRequest::getVar('views', array(), 'default', 'array', 2);
		
		$restrictedViews = array();
		foreach($views as $view => $locked)
		{
			if($locked == 1) $restrictedViews[] = $view;
		}
		
		$model = $this->getModel('Masterpw');
		$model->saveSettings($masterpw, $restrictedViews);
		
		$this->setRedirect('index.php?option=com_admintools', JText::_('ATOOLS_LBL_MASTERPW_SAVED'));
	}
}
