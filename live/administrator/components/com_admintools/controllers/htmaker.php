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

class AdmintoolsControllerHtmaker extends FOFController
{
	public function __construct($config = array()) {
		parent::__construct($config);
		
		$this->modelName = 'htmaker';
	}
	
	public function execute($task) {
		if(!in_array($task, array('save','apply'))) $task = 'browse';
		parent::execute($task);
	}
	
	public function save()
	{
		// CSRF prevention
		if(!JRequest::getVar(JUtility::getToken(), false, 'POST')) {
			JError::raiseError('403', JText::_('Request Forbidden'));
		}
		
		$model = $this->getThisModel();
		$data = JRequest::get('POST',2);
		$model->saveConfiguration($data);

		$this->setRedirect('index.php?option=com_admintools&view=htmaker',JText::_('ATOOLS_LBL_HTMAKER_SAVED'));
	}

	public function apply()
	{
		$model = $this->getThisModel();
		$data = JRequest::get('POST',2);
		$model->saveConfiguration($data);
		$status = $model->writeHtaccess();
		if(!$status)
		{
			$this->setRedirect('index.php?option=com_admintools&view=htmaker',JText::_('ATOOLS_LBL_HTMAKER_NOTAPPLIED'),'error');
		}
		else
		{
			$this->setRedirect('index.php?option=com_admintools&view=htmaker',JText::_('ATOOLS_LBL_HTMAKER_APPLIED'));
		}
	}

}
