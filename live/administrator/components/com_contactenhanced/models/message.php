<?php
/**
 * @copyright	Copyright (C) 2006 - 2010 Ideal Custom Software Development
 * @author     Douglas Machado {@link http://ideal.fok.com.br}
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

/**
 * Item Model for Custom Fields.
 *
 * @package		com_contactenhanced
* @version		1.6
 */
class ContactenhancedModelMessage extends JModelAdmin
{
	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param	object	A record object.
	 * @return	boolean	True if allowed to delete the record. Defaults to the permission set in the component.
	 * @since	1.6
	 */
	protected function canDelete($record)
	{
		$user = JFactory::getUser();

		if ($record->catid) {
			return $user->authorise('core.delete', 'com_contactenhanced.category.'.(int) $record->catid);
		} else {
			return parent::canDelete($record);
		}
	}

	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param	object	A record object.
	 * @return	boolean	True if allowed to change the state of the record. Defaults to the permission set in the component.
	 * @since	1.6
	 */
	protected function canEditState($record)
	{
		$user = JFactory::getUser();

		if ($record->catid) {
			return $user->authorise('core.edit.state', 'com_contactenhanced.category.'.(int) $record->catid);
		} else {
			return parent::canEditState($record);
		}
	}

	/**
	 * Returns a Table object, always creating it
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 * @since	1.6
	 */
	public function getTable($type = 'Message', $prefix = 'ContactenhancedTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get the row form.
	 *
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return	mixed	A JForm object on success, false on failure
	 * @since	1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		jimport('joomla.form.form');
		JForm::addFieldPath('JPATH_ADMINISTRATOR/components/com_contactenhanced/models/fields');

		// Get the form.
		$form = $this->loadForm('com_contactenhanced.message', 'message', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}

		return $form;
	}

	
	function getRecordedFields()
	{
		$id		= JRequest::getVar('cid');
		$catid	= JRequest::getVar('catid');
		
		$query	= 'SELECT cf.* ' //, UNIX_TIMESTAMP(mf.date) AS unix_timestamp
					.	', (SELECT mf.id 	FROM #__ce_message_fields mf  WHERE mf.message_id = '.$this->_db->Quote($id[0]).' AND  cf.id = mf.field_id) AS field_id '
					.	', (SELECT mf.value	FROM #__ce_message_fields mf  WHERE mf.message_id = '.$this->_db->Quote($id[0]).' AND  cf.id = mf.field_id) AS field_value '
					.	' FROM #__ce_cf cf '
					.	' WHERE cf.type <> '.$this->_db->Quote('multiplefiles')
							.	' AND cf.iscore <> '.	$this->_db->Quote('1')
							.	' AND (cf.catid = '.		$this->_db->Quote($catid) .' OR cf.catid = 0)'
					.	' ORDER BY cf.ordering ASC '
				;
		
		$this->_db->setQuery($query);
		return ($this->_db->loadObjectList()); 
		
	}
	
	function getCustomFields($catid){
		$db			= & JFactory::getDBO();
		$query = "SELECT cf.id, cf.* "
		. "\n FROM #__ce_cf AS cf"
		. "\n WHERE (cf.published > 0 OR (cf.published = 0 AND cf.iscore = 1) ) "
			. "\n AND (cf.catid = ". (int) $catid . " OR  cf.catid = 0 )"
		. "\n ORDER BY cf.catid, cf.ordering ASC"
		;
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	function getAttachments(){
		$id		= JRequest::getVar('id');
		$query	= 'SELECT * FROM #__ce_message_fields '
					. 	' WHERE message_id ='.$this->_db->Quote($id)
						. ' AND field_type = '.$this->_db->Quote('multiplefiles')
						;
		return $this->_getList($query);
	}
	
	function getReplies(){
		$id		= JRequest::getVar('id');
		
		$query	= 'SELECT * FROM #__ce_messages '
					. 	' WHERE parent ='.$this->_db->Quote($id)
					.	' ORDER BY date '
					;
		return $this->_getList($query);
	}
	
	function store($data)
	{	
		$row =& $this->getTable();

		if (!$row->bind($data)) {
			return false;
		}

		if (!$row->check()) {
			return false;
		}

		if (!$row->store()) {
			return false;
		}

		return $row->id;
	}
	
	/**
	 * Method to delete one or more records.
	 *
	 * @param	array	$pks	An array of record primary keys.
	 *
	 * @return	boolean	True if successful, false if an error occurs.
	 * @since	1.6
	 */
	
	public function delete($pks){
		// Initialise variables.
		$dispatcher	= JDispatcher::getInstance();
		$db			= & JFactory::getDBO();
		$user		= JFactory::getUser();
		$pks		= (array) $pks;
		$table		= $this->getTable();
		$mfields	=& $this->getTable('Messagefields');

		jimport('joomla.filesystem.file');

		// Include the content plugins for the on delete events.
		JPluginHelper::importPlugin('content');

		// Iterate the items to delete each one.
		foreach ($pks as $i => $pk) {

			if ($table->load($pk)) {

				if ($this->canDelete($table)) {

					$context = $this->option.'.'.$this->name;

					// Trigger the onContentBeforeDelete event.
					$result = $dispatcher->trigger($this->event_before_delete, array($context, $table));
					if (in_array(false, $result, true)) {
						$this->setError($table->getError());
						return false;
					}

					if (!$table->delete($pk)) {
						$this->setError($table->getError());
						return false;
					}

					// Trigger the onContentAfterDelete event.
					$dispatcher->trigger($this->event_after_delete, array($context, $table));
					
					// Get custom fields to delete
					$query	= 'SELECT * FROM #__ce_message_fields '
								. ' WHERE message_id ='.$db->Quote($pk)
								;
					$db->setQuery($query);
					$fields		= $db->loadObjectList();
					
					if(is_array($fields)){
						foreach($fields as $field){
							if($field->field_type=='multiplefiles'){
								$attachments	= explode('|',$field->value);
								if(is_array($attachments)){
									foreach($attachments as $attachment){
										JFile::delete(CE_UPLOADED_FILE_PATH.trim($attachment));
									}
								}
							}
							if (!$mfields->delete( $field->id )) {
								$this->setError( $mfields->getErrorMsg() );
								return false;
							}
						}
					}
					
					
					if (!$table->delete( $pk)) {
						$this->setError( $table->getErrorMsg() );
						return false;
					}
					
					// Get children (replies) delete
					$query	= 'SELECT id FROM #__ce_messages '
								. ' WHERE parent ='.$db->Quote($pk)
								;
					$db->setQuery($query);
					$fields		= $db->loadResultArray();
					if( count($fields) > 0 ){
						$this->delete($fields);
					}

				} else {

					// Prune items that you can't change.
					unset($pks[$i]);
					$error = $this->getError();
					if ($error) {
						JError::raiseWarning(500, $error);
					}
					else {
						JError::raiseWarning(403, JText::_('JLIB_APPLICATION_ERROR_DELETE_NOT_PERMITTED'));
					}
				}

			} else {
				$this->setError($table->getError());
				return false;
			}
		}

		// Clear the component's cache
		$cache = JFactory::getCache($this->option);
		$cache->clean();
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 * @since	1.6
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_contactenhanced.edit.message.data', array());

		if (empty($data)) {
			$data = $this->getItem();
		}

		return $data;
	}

	/**
	 * Method to perform batch operations on a category or a set of contacts.
	 *
	 * @param	array	An array of commands to perform.
	 * @param	array	An array of category ids.
	 * @return	boolean	Returns true on success, false on failure.
	 */
	function batch($commands, $pks)
	{
		// Sanitize user ids.
		$pks = array_unique($pks);
		JArrayHelper::toInteger($pks);

		// Remove any values of zero.
		if (array_search(0, $pks, true)) {
			unset($pks[array_search(0, $pks, true)]);
		}

		if (empty($pks)) {
			$this->setError(JText::_('COM_CONTACTENHANCED_NO_CONTACT_SELECTED'));
			return false;
		}

		$done = false;

		if (!empty($commands['assetgroup_id'])) {
			if (!$this->_batchAccess($commands['assetgroup_id'], $pks)) {
				return false;
			}
			$done = true;
		}

		if (!empty($commands['menu_id'])) {
			$cmd = JArrayHelper::getValue($commands, 'move_copy', 'c');

			if ($cmd == 'c' && !$this->_batchCopy($commands['menu_id'], $pks)) {
				return false;
			}
			else if ($cmd == 'm' && !$this->_batchMove($commands['menu_id'], $pks)) {
				return false;
			}
			$done = true;
		}

		if (!$done) {
			$this->setError('COM_MENUS_ERROR_INSUFFICIENT_BATCH_INFORMATION');
			return false;
		}

		return true;
	}

	/**
	 * Batch access level changes for a group of rows.
	 *
	 * @param	int		The new value matching an Asset Group ID.
	 * @param	array	An array of row IDs.
	 * @return	booelan	True if successful, false otherwise and internal error is set.
	 */
	protected function _batchAccess($value, $pks)
	{
		$table = $this->getTable();
		foreach ($pks as $pk) {
			$table->reset();
			$table->load($pk);
			$table->access = (int) $value;
			if (!$table->store()) {
				$this->setError($table->getError());
				return false;
			}
		}

		return true;
	}

	/**
	 * A protected method to get a set of ordering conditions.
	 *
	 * @param	object	A record object.
	 * @return	array	An array of conditions to add to add to ordering queries.
	 * @since	1.6
	 */
	protected function getReorderConditions($table = null)
	{
		$condition = array();
		$condition[] = 'catid = '.(int) $table->catid;
		return $condition;
	}
	
}