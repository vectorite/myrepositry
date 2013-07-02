<?php
/**
 *  @package AdminTools
 *  @copyright Copyright (c)2010-2011 Nicholas K. Dionysopoulos
 *  @license GNU General Public License version 3, or later
 *  @version $Id$
 */

// Protect from unauthorized access
defined('_JEXEC') or die('Restricted Access');

class AdmintoolsControllerScan extends FOFController
{
	/**
	 * Apply hard-coded filters before rendering the Browse page
	 * @return bool
	 */
	protected function onBeforeBrowse() {
		$result = parent::onBeforeBrowse();
		if($result) {
			$limitstart = FOFInput::getInt('limitstart', null, $this->input);
			if(is_null($limitstart)) {
				$total = $this->getThisModel()->getTotal();
				$limitstart = $this->getThisModel()->getState('limitstart',0);
				if($limitstart > $total) {
					$this->getThisModel()->limitstart(0);
				}
			}
			
			$this->getThisModel()
				->status('complete')
				->profile_id(1);
		}
		return $result;
	}
	
	protected function onAfterBrowse()
	{
		$this->getThisModel()->removeIncompleteScans();
		
		return true;
	}
	
	public function add() {
		JError::raiseError('403', version_compare(JVERSION, '1.6.0', 'ge') ? JText::_('JLIB_APPLICATION_ERROR_ACCESS_FORBIDDEN') : JText::_('Request Forbidden'));
	}
	
	public function edit() {
		JError::raiseError('403', version_compare(JVERSION, '1.6.0', 'ge') ? JText::_('JLIB_APPLICATION_ERROR_ACCESS_FORBIDDEN') : JText::_('Request Forbidden'));
	}
	
	public function save() {
		JError::raiseError('403', version_compare(JVERSION, '1.6.0', 'ge') ? JText::_('JLIB_APPLICATION_ERROR_ACCESS_FORBIDDEN') : JText::_('Request Forbidden'));
	}
	
	public function remove()
	{
		$this->getThisModel()->setIDsFromRequest();
		
		return parent::remove();
	}
	
	public function startscan()
	{
		if(!$this->checkACL('core.manage')) {
			JError::raiseError('403', version_compare(JVERSION, '1.6.0', 'ge') ? JText::_('JLIB_APPLICATION_ERROR_ACCESS_FORBIDDEN') : JText::_('Request Forbidden'));
		}
		
		FOFInput::setVar('layout','scan',$this->input);
		$this->getThisView()->assign('retarray', $this->getThisModel()->startScan());
		$this->getThisView()->setLayout('scan');
		$this->layout = 'scan';
		
		parent::display(false);
	}
	
	public function stepscan()
	{
		if(!$this->checkACL('core.manage')) {
			JError::raiseError('403', version_compare(JVERSION, '1.6.0', 'ge') ? JText::_('JLIB_APPLICATION_ERROR_ACCESS_FORBIDDEN') : JText::_('Request Forbidden'));
		}
		
		FOFInput::setVar('layout','scan',$this->input);
		$this->getThisView()->assign('retarray', $this->getThisModel()->stepScan());
		$this->getThisView()->setLayout('scan');
		$this->layout = 'scan';
		
		parent::display(false);
	}
}