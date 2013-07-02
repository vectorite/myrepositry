<?php
/**
 * @version 2.0.1 2012-08-17
 * @package Joomla
 * @subpackage Work Force
 * @copyright (C) 2012 the Thinkery
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.application.component.view');

class WorkforceViewBackup extends JView
{    
	function display($tpl = null)
	{
		$app = &JFactory::getApplication();

        $canDo	= WorkforceHelper::getActions();
        if(!$canDo->get('core.admin')){
            $msg = JText::_('COM_WORKFORCE_NO_EDIT_PERMISSION');
			$app->redirect('index.php?option=com_workforce', $msg, 'error');
            return false;
        }

        JToolBarHelper::title('<span class="wf_adminHeader">'.JText::_('COM_WORKFORCE').'</span> <span class="wf_adminSubheader">['.JText::_('COM_WORKFORCE_BACK_UP').']</span>', 'workforce');
        JToolBarHelper::custom('backup.backupDB', 'restore.png', 'restore_f2.png', JText::_('COM_WORKFORCE_BACK_UP'), false, false);
        
		parent::display($tpl);
	}	
}
?>