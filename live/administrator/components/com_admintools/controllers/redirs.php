<?php
/**
 *  @package AdminTools
 *  @copyright Copyright (c)2010-2011 Nicholas K. Dionysopoulos
 *  @license GNU General Public License version 3, or later
 *  @version $Id$
 */

// Protect from unauthorized access
defined('_JEXEC') or die('Restricted Access');

class AdmintoolsControllerRedirs extends FOFController
{
	public function copy()
	{
		// CSRF prevention
		if(!JRequest::getVar(JUtility::getToken(), false, 'POST')) {
			JError::raiseError('403', JText::_('Request Forbidden'));
		}

		$model = $this->getThisModel();
		$model->setIDsFromRequest();
		$id = $model->getId();

		$item = $model->getItem();
		$key = $item->getKeyName();
		if($item->$key == $id)
		{
			$item->id = 0;
			$item->published = 0;
		}
		$status = $model->save($item);

		// redirect
		$option = JRequest::getCmd('option');
		$view = JRequest::getCmd('view');
		$url = 'index.php?option='.$option.'&view='.$view;
		if(!$status)
		{
			$this->setRedirect($url, $model->getError(), 'error');
		}
		else
		{
			$this->setRedirect($url);
		}
		$this->redirect();
	}
	
	public function applypreference() {
		$newState = JRequest::getInt('urlredirection', 1);
		$model = $this->getThisModel();
		$model->setRedirectionState($newState);
		
		$option = JRequest::getCmd('option');
		$view = JRequest::getCmd('view');
		$url = 'index.php?option='.$option.'&view='.$view;
		$this->setRedirect($url, JText::_('ATOOLS_LBL_REDIRS_PREFERENCE_SAVED'));
		
		$this->redirect();
	}
}
