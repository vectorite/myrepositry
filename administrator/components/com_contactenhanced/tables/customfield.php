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
class ContactenhancedTableCustomfield extends JTable
{
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 * @since 1.0
	 */
	public function __construct(& $db)
	{
		parent::__construct('#__ce_cf', 'id', $db);
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
			$registry = new JRegistry();
			$registry->loadArray($array['params']);
			$array['params'] = (string) $registry;
		}
		$jform	= JRequest::getVar('jform'	, '', 'POST', 'none', JREQUEST_ALLOWRAW);
		if (isset($jform['value'])) {
			$array['value']	= $jform['value'];
		}
		if (isset($jform['tooltip'])) {
			$array['tooltip']	= $jform['tooltip'];
		}
		if (isset($jform['attributes'])) {
			$array['attributes']	= $jform['attributes'];
		}
	
		return parent::bind($array, $ignore);
	}

	/**
	 * Stores a custom field
	 *
	 * @param	boolean	True to update fields even if they are null.
	 * @return	boolean	True on success, false on failure.
	 * @since	1.6
	 */
	public function store($updateNulls = false)
	{
		$this->_getOrdering();
		
		// Transform the params field
		if (is_array($this->params)) {
			$registry = new JRegistry();
			$registry->loadArray($this->params);
			$this->params = (string)$registry;
		}
		//echo '<pre>'; print_r($this); echo '<pre>'; exit;
		
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
	function check()
	{
		
		/** check for valid category */
		if (trim($this->catid) == '') {
			$this->setError(JText::_('COM_CONTACTENHANCED_WARNING_CATEGORY'));
			return false;
		}

		return $this->canInsert();
	}
	
	/**
	 * 
	 */
	function canInsert(){
		$denyMultiple	= array('subject','email','name','username','surname','password','password_verify');
		
		if( !in_array($this->type, $denyMultiple) ){
			return true;
		}
		
		$where	= array();
		//
		if($this->id){
			$where[]= 'id <> '.$this->_db->Quote($this->id);
		}
		
		//ignore unpublished items:
		$where[]= 'published	 > 0';
		
		if($this->language == '*' AND $this->catid == '0'){
			$where[]= 'type		= '.$this->_db->Quote($this->type);
		}else{
			$where[]= 'type		= '.$this->_db->Quote($this->type);
			$where[]= '(catid	= '.$this->_db->Quote($this->catid) 	.' OR catid	= '.$this->_db->Quote('0'). ')';
			$where[]= '(language= '.$this->_db->Quote($this->language)	.' OR language = '.$this->_db->Quote('*'). ')';
		}
		
		
		$query	= 'SELECT * FROM '.$this->_tbl
				 . (count( $where ) ? ' WHERE '. implode( ' AND ', $where ) : '')
				 ;
		$this->_db->setQuery($query);
		// echo $this->_db->getQuery($query); exit;
		if( count($this->_db->loadResult()) > 0 ){
			$this->setError(JText::sprintf('CE_CF_WARNING_ONE_FIELD_PER_CATEGORY',ucfirst($this->type)));
			return false;
		}
		
		return true;	
	}
	private function _getOrdering() {
		if($this->ordering == 999){
			$query	= "SELECT ordering FROM #__ce_cf "
						. " WHERE catid =".$this->_db->Quote($this->catid)
							//. " OR catid =".$this->_db->Quote(0)
						. " ORDER BY ordering DESC"
						;
			$this->_db->setQuery($query);
			if (($ordering	= $this->_db->loadResult()) ) {
				$this->ordering = (++$ordering);
			}
		}
	}
}
