<?php
/**
 *  @package AdminTools
 *  @copyright Copyright (c)2010-2011 Nicholas K. Dionysopoulos
 *  @license GNU General Public License version 3, or later
 *  @version $Id$
 */

// Protect from unauthorized access
defined('_JEXEC') or die('Restricted Access');

class AdmintoolsControllerFixpermsconfig extends FOFController
{
	public function __construct($config = array()) {
		parent::__construct($config);
		
		$this->modelName = 'Fixpermsconfig';
	}
	
	public function execute($task) {
		if(!in_array($task, array('savedefaults','saveperms','saveapplyperms'))) $task = 'browse';
		$this->getThisModel()->setState('task',$task);
		parent::execute($task);
	}

	public function savedefaults()
	{
		// CSRF prevention
		if(!JRequest::getVar(JUtility::getToken(), false, 'POST')) {
			JError::raiseError('403', JText::_('Request Forbidden'));
		}
		
		$model = $this->getThisModel();
		$model->setState('dirperms', JRequest::getCmd('dirperms','0755'));
		$model->setState('fileperms', JRequest::getCmd('fileperms','0644'));
		$model->saveDefaults();

		$message = JText::_('ATOOLS_LBL_FIXPERMSCONFIG_DEFAULTSSAVED');
		$this->setRedirect('index.php?option=com_admintools&view=fixpermsconfig', $message);
	}

	public function onBeforeBrowse()
	{
		$path = JRequest::getVar('path','');

		$model = $this->getThisModel();
		$model->setState('path',$path);
		$model->applyPath();
		
		return true;
	}

	/**
	 * Saves the custom permissions and reloads the current view
	 */
	public function saveperms()
	{
		// CSRF prevention
		if(!JRequest::getVar(JUtility::getToken(), false, 'POST')) {
			JError::raiseError('403', JText::_('Request Forbidden'));
		}
		
		$this->save_custom_permissions();
		
		$message = JText::_('ATOOLS_LBL_FIXPERMSCONFIG_CUSTOMSAVED');
                $path = JRequest::getVar('path','');
		$this->setRedirect('index.php?option=com_admintools&view=fixpermsconfig&path='.urlencode($path), $message);
	}
	
	/**
	 * Saves the custom permissions, applies them and reloads the current view
	 */
	public function saveapplyperms()
	{
		// CSRF prevention
		if(!JRequest::getVar(JUtility::getToken(), false, 'POST')) {
			JError::raiseError('403', JText::_('Request Forbidden'));
		}
		
		$this->save_custom_permissions(true);
		
		$message = JText::_('ATOOLS_LBL_FIXPERMSCONFIG_CUSTOMSAVEDAPPLIED');
                $path = JRequest::getVar('path','');
		$this->setRedirect('index.php?option=com_admintools&view=fixpermsconfig&path='.urlencode($path), $message);
	}
	
	private function save_custom_permissions($apply = false)
	{
		$path = JRequest::getVar('path','');

		$model = $this->getThisModel();
		$model->setState('path',$path);
		$model->applyPath();

		$folders = JRequest::getVar('folders', array(), 'default', 'array', $mask = 2);
		$model->setState('folders', $folders);
		$files = JRequest::getVar('files', array(), 'default', 'array', $mask = 2);
		$model->setState('files', $files);

		$model->savePermissions($apply);
	}
}
