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

class AdmintoolsControllerJupdate extends FOFController
{	
	public function display($cachable = false, $urlparams = false)
	{
		$model = $this->getModel('Jupdate','AdmintoolsModel');
		$view = $this->getThisView();
		$view->setModel($model, false);

		parent::display();
	}
	
	public function force()
	{
		FOFInput::setVar('task','display',$this->input);
		$this->display();
	}
	
	public function download()
	{
		$model = $this->getModel('Jupdate','AdmintoolsModel');
		$item = trim(strtolower(JRequest::getCmd('item','upgrade')));
		
		if( $file = $model->download($item) )
		{
			$file = urlencode($file);
			$url = 'index.php?option=com_admintools&view=jupdate&task=preinstall&file='.$file;
			$this->setRedirect($url);
		}
		else
		{
			$url = 'index.php?option=com_admintools&view=jupdate';
			$message = JText::_('ATOOLS_ERR_JUPDATE_DOWNLOADFAILED');
			$this->setRedirect($url, $message, 'error');
		}
	}

	public function preinstall()
	{
		$model = $this->getModel('Jupdate','AdmintoolsModel');
		$view = $this->getThisView();
		$view->setModel($model, false);
		
		if(version_compare(JVERSION, '1.7.0', 'ge')) {
			$model->checkSchemasTable();
		}

		parent::display();
	}

	public function install()
	{
		$model = $this->getModel('Jupdate','AdmintoolsModel');
		$view = $this->getThisView();
		$view->setModel($model, false);

		$act = JRequest::getCmd('act','nobackup');

		if($act != 'afterbackup')
		{
			if( !$model->createRestorationINI() )
			{
				$url = 'index.php?option=com_admintools&view=jupdate';
				$message = JText::_('ATOOLS_ERR_JUPDATE_CANTINSTALL');
				$this->setRedirect($url, $message, 'error');
			}
		}

		if($act == 'backup')
		{
			$returnurl = 'index.php?option=com_admintools&view=jupdate&task=install&act=afterbackup';
			$url = 'index.php?option=com_akeeba&view=backup&returnurl='.urlencode($returnurl);
			$this->setRedirect($url);
		}
		
		parent::display();
	}

	public function finalize()
	{
		$model = $this->getModel('Jupdate','AdmintoolsModel');
		$file = JRequest::getString('file','');
		$model->finalize($file);
		
		$url = 'index.php?option=com_admintools&view=jupdate';
		$this->setRedirect($url);
	}
}
