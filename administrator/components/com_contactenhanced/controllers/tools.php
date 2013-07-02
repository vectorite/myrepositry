<?php
/**
 * @copyright	Copyright (C) 2006 - 2012 Ideal Custom Software Development
 * @author     Douglas Machado {@link http://ideal.fok.com.br}
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');

/**
 * @package		com_contactenhanced
 * @since	1.6
 */
class ContactenhancedControllerTools extends JControllerAdmin
{
	
	/**
	 * Constructor.
	 *
	 * @param	array	$config	An optional associative array of configuration settings.
	 *
	 * @return	ContactControllerContacts
	 * @see		JController
	 * @since	1.6
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);

	}
	
	/**
	 * 
	 * @author Douglas Machado <http>//idealextensions.com>
	 * @copyright
	 */
	public function importcsv() {
		$app	= &JFactory::getApplication();
		$db		= &JFactory::getDbo();
		
		require_once (JPATH_COMPONENT.DS.'helpers'.DS.'csvhandler.php');
		
		$modelContact=$this->getModel('Contact'		,'ContactenhancedModel', array('ignore_request' => true));
		
		$file	= JRequest::getVar('csv_file', false, 'FILES');
		jimport('joomla.filesystem.file');
		//echo 'test<pre>'; print_r($file); exit;
		
		if(JFile::getExt($file['name']) != 'csv'){
			throw new Exception(JText::_('COM_CONTACTENHANED_TOOLS_IMPORT_FILE_EXTENSION_IS_NOT_CSV'));
		}else{
			$csv	= new csvHandler();
			$csv->readFile($file['tmp_name']);
			
			$contactsCount	= 0;
			//echo '<pre>'; print_r($csv->itemList); exit;
			foreach ($csv->itemList as $contact) {
				if(!isset($contact['name'])){
					continue;
				}
				$table		= $modelContact->getTable();
				
				//$contact		= ceHelper::objectToArray($contact);
				
				if(!isset($contact['catid']) OR !$contact['catid']){
					$contact['catid']	= JRequest::getVar('catid');
				}
				if(!isset($contact['published']) OR (int)$contact['published'] != 0){
					$contact['published']	= 1;
				}
				
				if(!isset($contact['language']) OR $contact['language'] != 0){
					$contact['language']	= '*';
				}
				
				
				$contact['id']	= null;
				
				// Bind the data.
				if (!$table->bind($contact)) {
					$modelContact->setError($table->getError());
					JError::raiseWarning(0, JText::sprintf('COM_CONTACTENHANCED_IMPORT_ERROR_CONTACT_NOT_IMPORTED',$contact['name'],$table->getError()));
					continue;
				}
			
				// Check the data.
				if (!$table->check(true)) {
					$modelContact->setError($table->getError());
					JError::raiseWarning(0, JText::sprintf('COM_CONTACTENHANCED_IMPORT_ERROR_CONTACT_NOT_IMPORTED',$contact['name'],$table->getError()));
					continue;
				}
				
				// Store the data.
				if (!$table->store()) {
					$modelContact->setError($table->getError());
					JError::raiseWarning(0, JText::sprintf('COM_CONTACTENHANCED_IMPORT_ERROR_CONTACT_NOT_IMPORTED',$contact['name'],$table->getError()));
					continue;
				}
				$contactsCount++;
			}
			
			JError::raiseNotice('', JText::sprintf('COM_CONTACTENHANCED_IMPORT_UPLOAD_FROM_CSV_RESULT', $contactsCount,$file['name']));
		}		
		$this->setRedirect('index.php?option=com_contactenhanced&view=tools');
	}
}