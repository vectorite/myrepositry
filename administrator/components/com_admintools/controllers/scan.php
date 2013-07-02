<?php
/**
 *  @package AdminTools
 *  @copyright Copyright (c)2010-2013 Nicholas K. Dionysopoulos
 *  @license GNU General Public License version 3, or later
 *  @version $Id$
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

class AdmintoolsControllerScan extends FOFController
{
	/**
	 * Apply hard-coded filters before rendering the Browse page
	 * @return bool
	 */
	protected function onBeforeBrowse() {
		$result = $this->checkACL('admintools.security');
		if($result) {
			$limitstart = $this->input->getInt('limitstart', null);
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
		JError::raiseError('403', JText::_('JLIB_APPLICATION_ERROR_ACCESS_FORBIDDEN'));
	}

	public function edit() {
		JError::raiseError('403', JText::_('JLIB_APPLICATION_ERROR_ACCESS_FORBIDDEN'));
	}

	public function save() {
		JError::raiseError('403', JText::_('JLIB_APPLICATION_ERROR_ACCESS_FORBIDDEN'));
	}

	public function remove()
	{
		$this->getThisModel()->setIDsFromRequest();

		return parent::remove();
	}

	public function startscan()
	{
		if(!$this->checkACL('admintools.security')) {
			JError::raiseError('403', JText::_('JLIB_APPLICATION_ERROR_ACCESS_FORBIDDEN'));
		}

		$this->input->set('layout','scan');
		$this->getThisView()->assign('retarray', $this->getThisModel()->startScan());
		$this->getThisView()->setLayout('scan');
		$this->layout = 'scan';

		parent::display(false);
	}

	public function stepscan()
	{
		if(!$this->checkACL('admintools.security')) {
			JError::raiseError('403', JText::_('JLIB_APPLICATION_ERROR_ACCESS_FORBIDDEN'));
		}

		$this->input->set('layout','scan');
		$this->getThisView()->assign('retarray', $this->getThisModel()->stepScan());
		$this->getThisView()->setLayout('scan');
		$this->layout = 'scan';

		parent::display(false);
	}

	protected function onBeforeRemove() {
		return $this->checkACL('admintools.security');
	}
}