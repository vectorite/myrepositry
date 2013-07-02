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

class AdmintoolsControllerWafconfig extends FOFController
{
	public function __construct($config = array()) {
		parent::__construct($config);
		
		$this->modelName = 'wafconfig';
	}
	
	public function execute($task) {
		if(!in_array($task, array('save','apply'))) $task = 'browse';
		parent::execute($task);
	}
	
	public function apply()
	{
		$model = $this->getThisModel();
		$data = JRequest::get('post');
		$model->saveConfig($data);

		$this->setRedirect('index.php?option=com_admintools&view=wafconfig',JText::_('ATOOLS_LBL_WAF_CONFIGSAVED'));
	}
	
	public function save()
	{
		$model = $this->getThisModel();
		$data = JRequest::get('post');
		$model->saveConfig($data);

		$this->setRedirect('index.php?option=com_admintools&view=waf',JText::_('ATOOLS_LBL_WAF_CONFIGSAVED'));
	}
}
