<?php
/**
 * @version 2.0.1 2012-08-17
 * @package Joomla
 * @subpackage Work Force
 * @copyright (C) 2012 the Thinkery
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.application.component.model');

class workforceModeldepartments extends JModel
{
	var $_departments   = null;
	var $_where         = null;
	var $_total         = null;
    var $_featured      = null;

	function __construct()
	{
		parent::__construct();

		$app        = &JFactory::getApplication();
        $option     = JRequest::getCmd('option', 'com_workforce');
        
		$settings   = &JComponentHelper::getParams( 'com_workforce' );
        $perpage    = $settings->get('perpage', 20);

        // Get the pagination request variables
        $this->setState('limit', $app->getUserStateFromRequest('com_workforce.limit', 'limit', $perpage, 'int'));
        $this->setState('limitstart', JRequest::getVar('limitstart', 0, '', 'int'));
        $this->setState('limitstart', ($this->getState('limit') != 0 ? (floor($this->getState('limitstart') / $this->getState('limit')) * $this->getState('limit')) : 0));
	}

	function getData()
	{
		$app        = &JFactory::getApplication();
        $option     = JRequest::getCmd('option', 'com_workforce');
        
		$debug      = '';
        $settings   = &JComponentHelper::getParams( 'com_workforce' );
		$perpage    = $settings->get('perpage', 20);

		if (empty($this->_departments))
		{
			// Get the WHERE and ORDER BY clauses for the query
			$where		= $this->_buildContentWhere();
			$sort		= $app->getUserStateFromRequest( $option.'.department.filter_order', 'filter_order', 'd.ordering', 'cmd' );
			$order	    = $app->getUserStateFromRequest( $option.'.department.filter_order_dir', 'filter_order_dir', 'ASC', 'word' );

			$this->_departments = new WorkforceHelperDepartment($this->_db);
			$this->_departments->setType('departments');
			$this->_departments->setWhere( $where );
			$this->_departments->setOrderBy( $sort, $order );
			$this->_departments = $this->_departments->getDepartment($this->getState('limitstart'),$this->getState('limit'), $debug);
		}
		return $this->_departments;
	}

    function getFeatured()
	{
	    $app        = &JFactory::getApplication();
        $option     = JRequest::getCmd('option', 'com_workforce');
        
		$settings   = &JComponentHelper::getParams( 'com_workforce' );
		$fperpage   = $settings->get('num_featured', 5);
        $where      = $this->_where;
        $where[]    = 'e.featured = 1';

		$this->_featured = new WorkforceHelperDepartment($this->_db);
		$this->_featured->setType('departments');
        $this->_featured->setWhere( $where );
		$this->_featured->setOrderBy('RAND()', '');
		$this->_featured = $this->_featured->getDepartment(0,$fperpage);

		return $this->_featured;
	}

	function getTotal()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_total))
		{
			$this->_total = $this->_db->setQuery(WorkforceHelperQuery::buildDepartmentsCount($this->_where));
            $this->_total = $this->_db->loadResult();
		}
		return $this->_total;
	}

	function getPagination()
	{
	  // Lets load the content if it doesn't already exist
	  if (empty($this->_pagination))
	  {
		 jimport('joomla.html.pagination');
		 $this->_pagination = new JPagination( $this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
	  }
	  return $this->_pagination;
	}

	function _buildContentWhere()
	{
        $where = array();

        $this->_where = $where;
		return $this->_where;
	}
}

?>