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

class AdmintoolsControllerAdminpw extends FOFController
{
	public function __construct($config = array()) {
		parent::__construct($config);
		
		$this->modelName = 'adminpw';
	}
	
	public function execute($task) {
		if(!in_array($task, array('protect','unprotect'))) $task = 'browse';
		parent::execute($task);
	}


	public function protect()
	{
		// CSRF prevention
		if(!JRequest::getVar(JUtility::getToken(), false, 'POST')) {
			JError::raiseError('403', JText::_('Request Forbidden'));
		}
		
		$username = JRequest::getVar('username','');
		$password = JRequest::getVar('password','');
		$password2 = JRequest::getVar('password2','');

		if(empty($username)) {
			$this->setRedirect('index.php?option=com_admintools&view=adminpw',JText::_('ATOOLS_ERR_ADMINPW_NOUSERNAME'),'error');
			return;
		}

		if(empty($password)) {
			$this->setRedirect('index.php?option=com_admintools&view=adminpw',JText::_('ATOOLS_ERR_ADMINPW_NOPASSWORD'),'error');
			return;
		}

		if($password != $password2) {
			$this->setRedirect('index.php?option=com_admintools&view=adminpw',JText::_('ATOOLS_ERR_ADMINPW_PASSWORDNOMATCH'),'error');
			return;
		}

		$model = $this->getThisModel();

		$model->username = $username;
		$model->password = $password;

		$status = $model->protect();
		$url = 'index.php?option=com_admintools';
		if($status)
		{
			$this->setRedirect($url,JText::_('ATOOLS_LBL_ADMINPW_APPLIED'));
		}
		else
		{
			$this->setRedirect($url,JText::_('ATOOLS_ERR_ADMINPW_NOTAPPLIED'),'error');
		}
	}

	public function unprotect()
	{
		// CSRF prevention
		if(!JRequest::getVar(JUtility::getToken(), false, 'POST')) {
			JError::raiseError('403', JText::_('Request Forbidden'));
		}
		
		$model = $this->getThisModel();
		$status = $model->unprotect();
		$url = 'index.php?option=com_admintools';
		if($status)
		{
			$this->setRedirect($url,JText::_('ATOOLS_LBL_ADMINPW_UNAPPLIED'));
		}
		else
		{
			$this->setRedirect($url,JText::_('ATOOLS_ERR_ADMINPW_NOTUNAPPLIED'),'error');
		}
	}
}
