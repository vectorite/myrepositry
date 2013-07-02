<?php
/**
 * @version 2.0.1 2012-08-17
 * @package Joomla
 * @subpackage Work Force
 * @copyright (C) 2012 the Thinkery
 * @license GNU/GPL see LICENSE.php
 */
 
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.application.component.controlleradmin');

class WorkforceControllerBackup extends JControllerAdmin
{
    protected $text_prefix = 'COM_WORKFORCE';
    
    public function backupDB()
	{
        // Check for request forgeries
		JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$model = $this->getModel('backup');
        if($msg = $model->backup_now()){
            $this->setMessage(sprintf(JText::_('COM_WORKFORCE_BACKUP_SUCCESSFUL'), $msg));
        }else{
            $this->setMessage($model->getError(), 'error');
        }

		$cache = &JFactory::getCache('com_workforce');
		$cache->clean();

		$this->setRedirect(JRoute::_('index.php?option=com_workforce&view=backup', false));
	}
}
?>
