<?php
/**
 *  @package AdminTools
 *  @copyright Copyright (c)2010-2013 Nicholas K. Dionysopoulos
 *  @license GNU General Public License version 3, or later
 *  @version $Id$
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

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
		if(is_array($this->input)) {
			$data = $this->input;
		} elseif($this->input instanceof FOFInput) {
			$data = $this->input->getData();
		} else {
			$data = JRequest::get('POST',2);
		}
		$model->saveConfig($data);

		$this->setRedirect('index.php?option=com_admintools&view=wafconfig',JText::_('ATOOLS_LBL_WAF_CONFIGSAVED'));
	}
	
	public function save()
	{
		$model = $this->getThisModel();
		if(is_array($this->input)) {
			$data = $this->input;
		} elseif($this->input instanceof FOFInput) {
			$data = $this->input->getData();
		} else {
			$data = JRequest::get('POST',2);
		}
		$model->saveConfig($data);

		$this->setRedirect('index.php?option=com_admintools&view=waf',JText::_('ATOOLS_LBL_WAF_CONFIGSAVED'));
	}
	
	protected function onBeforeBrowse()
	{
		return $this->checkACL('admintools.security');
	}
	
	protected function onBeforeApply()
	{
		return $this->checkACL('admintools.security');
	}
	
	protected function onBeforeSave()
	{
		return $this->checkACL('admintools.security');
	}
}
