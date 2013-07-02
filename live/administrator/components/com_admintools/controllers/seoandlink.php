<?php
/**
 *  @package AdminTools
 *  @copyright Copyright (c)2010-2011 Nicholas K. Dionysopoulos
 *  @license GNU General Public License version 3, or later
 *  @version $Id$
 */

// Protect from unauthorized access
defined('_JEXEC') or die('Restricted Access');

class AdmintoolsControllerSeoandlink extends FOFController
{
	public function __construct($config = array()) {
		parent::__construct($config);
		
		$this->modelName = 'seoandlink';
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
		$data = JRequest::get('post');
		$model->saveConfig($data);

		$this->setRedirect('index.php?option=com_admintools&view=cpanel',JText::_('ATOOLS_LBL_SEOANDLINK_CONFIGSAVED'));
	}
	
	public function apply()
	{
		$this->save();
		$this->setRedirect('index.php?option=com_admintools&view=seoandlink',JText::_('ATOOLS_LBL_SEOANDLINK_CONFIGSAVED'));
	}
}
