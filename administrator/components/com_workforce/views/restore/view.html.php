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

class WorkforceViewRestore extends JView
{
	function display($tpl = null)
	{
		$app = &JFactory::getApplication();
        JHTML::_('behavior.tooltip');

        $canDo	= WorkforceHelper::getActions();
        if(!$canDo->get('core.admin')){
            $msg = JText::_('COM_WORKFORCE_NO_EDIT_PERMISSION');
			$app->redirect('index.php?option=com_workforce', $msg, 'error');
            return false;
        }
		
		JToolBarHelper::title('<span class="wf_adminHeader">'.JText::_('COM_WORKFORCE').'</span> <span class="wf_adminSubheader">['.JText::_('COM_WORKFORCE_RESTORE_FROM_BACKUP_COPY').']</span>', 'workforce');
		JToolBarHelper::custom('restore.restoreDB', 'restore.png', 'restore_f2.png', JText::_('COM_WORKFORCE_RESTORE'), false, false);
		
		$sql_bak_file_list = JFolder::files(JPATH_SITE.DS.'media'.DS.'com_workforce', '.sql.gz');
		$sql_bak_options   = array();
		$i                 = 1;
        
		foreach ($sql_bak_file_list as $sqlfl){
			$sql_bak_options[] = JHTML::_('select.option', $i, $sqlfl);
			$i++;
		}
		
		$lists = array();
		$lists['Sql_bak_files'] = JHTML::_('select.genericlist', $sql_bak_options, 'bak_file', 'size="5" class="inputbox" style="width: 250px;"', 'text', 'text');
		$this->assignRef('lists', $lists);
        
		parent::display($tpl);
	}	
}
?>