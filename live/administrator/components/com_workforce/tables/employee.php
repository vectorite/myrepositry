<?php
/**
 * @version 2.0.1 2012-08-17
 * @package Joomla
 * @subpackage Work Force
 * @copyright (C) 2012 the Thinkery
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

class WorkforceTableEmployee extends JTable
{
    function __construct(&$_db)
	{
		parent::__construct('#__workforce_employees', 'id', $_db);
	}

	function check()
	{
		jimport('joomla.filter.output');

		// Set name
		$this->fname = htmlspecialchars_decode($this->fname, ENT_QUOTES);
        $this->lname = htmlspecialchars_decode($this->lname, ENT_QUOTES);       

        $this->website  = ($this->website) ? $this->_getUrl($this->website) : '';
        $this->twitter  = ($this->twitter) ? $this->_getUrl($this->twitter) : '';
        $this->facebook = ($this->facebook) ? $this->_getUrl($this->facebook) : '';
        $this->youtube  = ($this->youtube) ? $this->_getUrl($this->youtube) : '';
        $this->linkedin = ($this->linkedin) ? $this->_getUrl($this->linkedin) : '';

		// Set ordering
		if ($this->state < 0) {
			// Set ordering to 0 if state is archived or trashed
			$this->ordering = 0;
		} else if (empty($this->ordering)) {
			// Set ordering to last if ordering was 0
			$this->ordering = self::getNextOrder('`department`=' . $this->_db->Quote($this->department).' AND state>=0');
		}

		return true;
	}

	public function bind($array, $ignore = array())
	{
		if (isset($array['params']) && is_array($array['params'])) {
			$registry = new JRegistry();
			$registry->loadArray($array['params']);

			// Get value of submitted form input
			$sample = $registry->get('sample', 0);

			// Sets the value to store in the db
			$registry->set('sample', ($sample ? $sample : ''));

			$array['params'] = (string)$registry;
		}
		return parent::bind($array, $ignore);
	}

	function store($updateNulls = false)
	{
		if (empty($this->id))
		{
			// Store the row
			parent::store($updateNulls);
		}
		else
		{
			// Get the old row
			$oldrow = JTable::getInstance('Employee', 'WorkforceTable');
			if (!$oldrow->load($this->id) && $oldrow->getError())
			{
				$this->setError($oldrow->getError());
			}

			// Store the new row
			parent::store($updateNulls);

			// Need to reorder ?
			if ($oldrow->state >= 0)
			{
				// Reorder the oldrow
				$this->reorder('`department`=' . $this->_db->Quote($this->department).' AND state>=0');
			}
		}
		return count($this->getErrors())==0;
	}

	public function publish($pks = null, $state = 1, $userID = 0)
	{
		// Initialise variables.
		$k = $this->_tbl_key;

		// Sanitize input.
		JArrayHelper::toInteger($pks);
		$state  = (int) $state;

		// If there are no primary keys set check to see if the instance key is set.
		if (empty($pks))
		{
			if ($this->$k) {
				$pks = array($this->$k);
			}
			// Nothing to set publishing state on, return false.
			else {
				$this->setError(JText::_('JLIB_DATABASE_ERROR_NO_ROWS_SELECTED'));
				return false;
			}
		}

		// Get an instance of the table
		$table = JTable::getInstance('Employee','WorkforceTable');

		// For all keys
		foreach ($pks as $pk)
		{
			// Load the banner
			if(!$table->load($pk))
			{
				$this->setError($table->getError());
			}
            // Change the state
            $table->state = $state;

            // Check the row
            $table->check();

            // Store the row
            if (!$table->store())
            {
                $this->setError($table->getError());
            }
		}
		return count($this->getErrors())==0;
	}

    public function feature($pks = null, $state = 1, $userId = 0)
	{
		// Initialise variables.
		$k = $this->_tbl_key;

		// Sanitize input.
		JArrayHelper::toInteger($pks);
		$userId = (int) $userId;
		$state  = (int) $state;

		// If there are no primary keys set check to see if the instance key is set.
		if (empty($pks))
		{
			if ($this->$k) {
				$pks = array($this->$k);
			}
			// Nothing to set publishing state on, return false.
			else {
				$this->setError(JText::_('JLIB_DATABASE_ERROR_NO_ROWS_SELECTED'));
				return false;
			}
		}

		// Get an instance of the table
		$table = JTable::getInstance('Employee','WorkforceTable');

		// For all keys
		foreach ($pks as $pk)
		{
			// Load the banner
			if(!$table->load($pk))
			{
				$this->setError($table->getError());
			}

            // Change the state
            $table->featured = $state;

            // Check the row
            $table->check();

            // Store the row
            if (!$table->store())
            {
                $this->setError($table->getError());
            }
		}
		return count($this->getErrors())==0;
	}
    
    function _getUrl($url){ 
        if(!$url) return;
        if (!preg_match('#http[s]?://|index[2]?\.php#', $url)) {
            $url = "http://$url";
        }
        return $url;
    }
}
?>