<?php
/**

 * @package		com_contactenhanced
 * @copyright	Copyright (C) 2006 - 2010 Ideal Custom Software Development
 * @author     Douglas Machado {@link http://ideal.fok.com.br}
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;


/**
 * @package		com_contactenhanced
*/
class ContactenhancedTableContact extends JTable
{
	/**
	 * 
	 * @param unknown_type $db
	 * @author 
	 */
	public function __construct(& $db)
	{
		parent::__construct('#__ce_details', 'id', $db);
	}

	/**
	 * Overloaded bind function
	 *
	 * @param	array		Named array
	 * @return	null|string	null is operation was satisfactory, otherwise returns an error
	 * @since	1.6
	 */
	public function bind($array, $ignore = '')
	{
		if (isset($array['params']) && is_array($array['params'])) {
			
			// moved edit_metadata.php into the parameters because of notices 
			// now we are moving them back to the metadata
			if (!isset($array['metadata']) OR !is_array($array['metadata'])) {
				$array['metadata']	= array('robots' =>$array['params']['robots']
											,'rights'=>$array['params']['rights']
											);
				$registry = new JRegistry();
				$registry->loadArray($array['metadata']);
				$array['metadata'] = (string) $registry;
				
			}
			
			$registry = new JRegistry();
			$registry->loadArray($array['params']);
			$array['params'] = (string) $registry;
		}
		
		return parent::bind($array, $ignore);
	}

	/**
	 * Stores a contact
	 *
	 * @param	boolean	True to update fields even if they are null.
	 * @return	boolean	True on success, false on failure.
	 * @since	1.6
	 */
	public function store($updateNulls = false)
	{
		// Transform the params field
		if (is_array($this->params)) {
			$registry = new JRegistry();
			$registry->loadArray($this->params);
			$this->params = (string)$registry;
		}
		
		// Transform the metadata field
		if (is_array($this->metadata)) {
			$registry = new JRegistry();
			$registry->loadArray($this->metadata);
			$this->metadata = (string)$registry;
		}

		$date	= JFactory::getDate();
		$user	= JFactory::getUser();
		if ($this->id) {
			// Existing item
			$this->modified		= $date->toMySQL();
			$this->modified_by	= $user->get('id');
		} else {
			// New newsfeed. A feed created and created_by field can be set by the user,
			// so we don't touch either of these if they are set.
			if (!intval($this->created)) {
				$this->created = $date->toMySQL();
			}
			if (empty($this->created_by)) {
				$this->created_by = $user->get('id');
			}
		}
		// Attempt to store the data.
		return parent::store($updateNulls);
	}

	/**
	 * Overloaded check function
	 *
	 * @return boolean
	 * @see JTable::check
	 * @since 1.5
	 */
	function check($import=false)
	{
		$this->default_con = intval($this->default_con);

		if (JFilterInput::checkAttribute(array ('href', $this->webpage))) {
			$this->setError(JText::_('COM_CONTACTENHANCED_WARNING_PROVIDE_VALID_URL'));
			return false;
		}

		// check for http, https, ftp on webpage
		if ((strlen($this->webpage) > 0)
			&& (stripos($this->webpage, 'http://') === false)
			&& (stripos($this->webpage, 'https://') === false)
			&& (stripos($this->webpage, 'ftp://') === false))
		{
			$this->webpage = 'http://'.$this->webpage;
		}

		/** check for valid name */
		if (trim($this->name) == '') {
			$this->setError(JText::_('COM_CONTACTENHANCED_WARNING_PROVIDE_VALID_NAME'));
			return false;
		}
		
		$this->email_to	= trim($this->email_to);
		jimport('joomla.mail.helper');
		
		if(!$import){ // If we are not importing contacts from  Joomla core contact
			if ( !($this->user_id) AND !ceHelper::isEmailAddress(($this->email_to)) ) {
				$this->setError(JText::_('CE_CONTACT_ERROR_EMAIL_REQUIRED'));
				return false;
			}elseif ( $this->email_to != '' AND !ceHelper::isEmailAddress(($this->email_to)) ) {
				$this->setError(JText::_('CE_CONTACT_ERROR_EMAIL_INVALID'));
				return false;
			}
		}
		
				/** check for existing name */
		$query = 'SELECT id FROM #__ce_details WHERE name = '.$this->_db->Quote($this->name).' AND catid = '.(int) $this->catid;
		$this->_db->setQuery($query);

		$xid = intval($this->_db->loadResult());
		if ($xid && $xid != intval($this->id)) {
			$this->setError(JText::sprintf('COM_CONTACTENHANCED_WARNING_SAME_NAME', $this->id));
			return false;
		}

		if (empty($this->alias)) {
			$this->alias = $this->name;
		}
		$this->alias = JApplication::stringURLSafe($this->alias);
		if (trim(str_replace('-','',$this->alias)) == '') {
			$this->alias = JFactory::getDate()->format("Y-m-d-H-i-s");
		}
		/** check for valid category */
		if (trim($this->catid) == '') {
			$this->setError(JText::_('COM_CONTACTENHANCED_WARNING_CATEGORY'));
			return false;
		}

		// Check the publish down date is not earlier than publish up.
		if (intval($this->publish_down) > 0 && $this->publish_down < $this->publish_up) {
			// Swap the dates.
			$temp = $this->publish_up;
			$this->publish_up = $this->publish_down;
			$this->publish_down = $temp;
		}

		return true;
		// clean up keywords -- eliminate extra spaces between phrases
		// and cr (\r) and lf (\n) characters from string
		if (!empty($this->metakey)) {
			// only process if not empty
			$bad_characters = array("\n", "\r", "\"", "<", ">"); // array of characters to remove
			$after_clean = JString::str_ireplace($bad_characters, "", $this->metakey); // remove bad characters
			$keys = explode(',', $after_clean); // create array using commas as delimiter
			$clean_keys = array();
			foreach($keys as $key) {
				if (trim($key)) {  // ignore blank keywords
					$clean_keys[] = trim($key);
				}
			}
			$this->metakey = implode(", ", $clean_keys); // put array back together delimited by ", "
		}

		// clean up description -- eliminate quotes and <> brackets
		if (!empty($this->metadesc)) {
			// only process if not empty
			$bad_characters = array("\"", "<", ">");
			$this->metadesc = JString::str_ireplace($bad_characters, "", $this->metadesc);
		}
		return true;
	}
}
