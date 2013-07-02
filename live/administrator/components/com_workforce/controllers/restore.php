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
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.archive');

class WorkforceControllerRestore extends JControllerAdmin
{
	function restoreDB( )
	{
        $database      = JFactory::getDBO();
        $prefix        = JRequest::getString('db_prefix');		
        
        if(!JRequest::getVar('bak_file')){
            $this->setMessage(JText::_('COM_WORKFORCE_NO_FILE_SELECTED'), 'error');
            $this->setRedirect(JRoute::_('index.php?option=com_workforce&view=restore', false));
            return false;
        }  
        
        
        //if can't extract file, return with error
        $bak_file      = JPATH_SITE.DS.'media'.DS.'com_workforce'.DS.JRequest::getVar('bak_file');
        if(!JArchive::extract($bak_file, JPATH_SITE.DS.'media'.DS.'com_workforce')){            
            $this->setMessage(sprintf(JText::_('COM_WORKFORCE_COULD_NOT_EXTRACT_FILE'), $bak_file), 'error');
            $this->setRedirect(JRoute::_('index.php?option=com_workforce&view=restore', false));
            return false;
        }
        
        // confirm that we're able to read back up file
        $text_bak_file = substr($bak_file, 0, strlen($bak_file)-3);        
        if(!$bquery = JFile::read($text_bak_file)){            
            $this->setMessage(sprintf(JText::_('COM_WORKFORCE_COULD_NOT_READ_BACKUP'), $text_bak_file), 'error');
            $this->setRedirect(JRoute::_('index.php?option=com_workforce&view=restore', false));
            return false;
        }
        
        // if a prefix was entered, make sure that the prefix exists in the backup file content before executing any changes
        if($prefix && !strpos($bquery, $prefix.'workforce')){
            $this->setMessage(sprintf(JText::_('COM_WORKFORCE_DB_PREFIX_NOT_FOUND'), $prefix), 'error');
            $this->setRedirect(JRoute::_('index.php?option=com_workforce&view=restore', false));
            return false;
        }else if(!$prefix && !strpos($bquery, $database->getPrefix().'workforce')){ // if no prefix was entered, make sure that current db prefix exists in the backup file content before executing any changes
            $this->setMessage(sprintf(JText::_('COM_WORKFORCE_DB_PREFIX_NOT_FOUND'), $database->getPrefix()), 'error');
            $this->setRedirect(JRoute::_('index.php?option=com_workforce&view=restore', false));
            return false;
        }
        
		JFile::delete($text_bak_file);
		
		// Empty tables
        $emptying_query  = 'TRUNCATE TABLE #__workforce_departments;';
		$emptying_query .= 'TRUNCATE TABLE #__workforce_employees;';
        $emptying_query .= 'TRUNCATE TABLE #__workforce_states;';		
		$database->setQuery ($emptying_query);			
        if(!$database->queryBatch()) {
            $this->setMessage(sprintf(JText::_('COM_WORKFORCE_QUERIES_EXECUTION_FAILED'), $database->getErrorMsg()), 'error');
            $this->setRedirect(JRoute::_('index.php?option=com_workforce&view=restore', false));
        }        
        
        // Check if this is a 1.5 backup - if so, replaces necessary db field names with current
        $legacy = (strpos($bquery, '`published`')) ? true : false;
        if($legacy){            
            $bquery = str_replace('`state`', '`locstate`',$bquery);
            $bquery = str_replace('`published`', '`state`', $bquery);  
            $bquery = str_replace('`id`,`title`,`desc`', '`id`,`name`,`desc`', $bquery);
        }
        if($prefix) $bquery = str_replace($prefix, $database->getPrefix(), $bquery);
		
		// Execute backup sql to restore wf data
        $database->setQuery($bquery);
        
		if(!$database->queryBatch()) {
            $this->setMessage(sprintf(JText::_('COM_WORKFORCE_QUERIES_EXECUTION_FAILED'), $database->getErrorMsg()), 'error');
		}else{
    		$this->setMessage(JText::_('COM_WORKFORCE_QUERIES_EXECUTED_SUCCESSFULLY'));
		}
		$this->setRedirect(JRoute::_('index.php?option=com_workforce&view=restore', false));
	}
}
?>
