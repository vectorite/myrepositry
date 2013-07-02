<?php
/**
 *  @package AdminTools
 *  @copyright Copyright (c)2010-2011 Nicholas K. Dionysopoulos
 *  @license GNU General Public License version 3, or later
 *  @version $Id$
 */

// Protect from unauthorized access
defined('_JEXEC') or die('Restricted Access');

class AdmintoolsControllerLog extends FOFController
{
	public function ban()
	{
		if(!JRequest::getVar(JUtility::getToken(), false, 'GET')) {
			JError::raiseError('403', JText::_('Request Forbidden'));
		}
		
		$id = JRequest::getString('id','');
		if(empty($id)) {
			JError::raiseError('500', JText::_('ATOOLS_ERR_LOG_BAN_NOID'));
		}
		
		$model = $this->getThisModel();
		$model->setIds(array($id));
		$item = $model->getItem();
		
		$banModel = FOFModel::getTmpInstance('Ipbls','AdmintoolsModel');
		$data = array(
			'id'			=> 0,
			'ip'			=> $item->ip,
			'description'	=> JText::_('ATOOLS_LBL_REASON_'.strtoupper($item->reason))
		);
		$banModel->getTable()->save($data);
		
		$this->setRedirect('index.php?option=com_admintools&view=logs', JText::_('ATOOLS_LBL_IPBL_SAVED'));
	}
	
	public function unban()
	{
		if(!JRequest::getVar(JUtility::getToken(), false, 'GET')) {
			JError::raiseError('403', JText::_('Request Forbidden'));
		}
		
		$id = JRequest::getString('id','');
		if(empty($id)) {
			JError::raiseError('500', JText::_('ATOOLS_ERR_LOG_BAN_NOID'));
		}
		
		$model = $this->getThisModel();
		$model->setIds(array($id));
		$item = $model->getItem();
		
		$banModel = FOFModel::getTmpInstance('Ipbls','AdmintoolsModel')
				->ip($item->ip);
		$items = $banModel->getItemList();
		$ids = array();
		foreach($items as $banItem) {
			$ids[] = $banItem->id;
		}
		$banModel->setIds($ids);
		$banModel->delete();
		$this->setRedirect('index.php?option=com_admintools&view=logs', JText::_('ATOOLS_LBL_IPBL_DELETED'));
	}	
}