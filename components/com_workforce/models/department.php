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

class workforceModeldepartment extends JModel
{
	var $_employees  = null;
	var $_id         = null;
	var $_where      = null;
	var $_total      = null;
    var $_featured   = null;
    var $_department = null;

    //search criteria
    var $_searchword = null;
	var $_searchwhere = null;
	
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

		// Set id for department
		$id = JRequest::getVar('id', 0, '', 'int');
		$this->setId($id);        
	}
	
	function setId($id)
	{
		$this->_id          = $id;
		$this->_employees	= null;
	}
	
	function getData()
	{
		$app        = &JFactory::getApplication();
        $option     = JRequest::getCmd('option', 'com_workforce');
        
		$debug = '';
        $settings   = &JComponentHelper::getParams( 'com_workforce' );
		$perpage    = $settings->get('perpage', 20);

		if (empty($this->_employees))
		{
			// Get the WHERE and ORDER BY clauses for the query
			$where		= $this->_buildContentWhere();
			$sort		= $app->getUserStateFromRequest( $option.'.department.filter_order', 'filter_order', 'e.ordering', 'cmd' );
			$order	    = $app->getUserStateFromRequest( $option.'.department.filter_order_dir', 'filter_order_dir', 'ASC', 'word' );

			$this->_employees = new WorkforceHelperEmployee($this->_db);
			$this->_employees->setType('employees');
			$this->_employees->setWhere( $where );
			$this->_employees->setOrderBy( $sort, $order );
			$this->_employees = $this->_employees->getEmployee($this->getState('limitstart'),$this->getState('limit'), $debug);
		}
		return $this->_employees;
	}

    function getFeatured()
	{        
		$settings   = &JComponentHelper::getParams( 'com_workforce' );
		$fperpage   = $settings->get('num_featured', 5);
        $where      = $this->_where;
        $where[]    = 'e.featured = 1';
		
		$this->_featured = new WorkforceHelperEmployee($this->_db);
		$this->_featured->setType('employees');
        $this->_featured->setWhere( $where );
		$this->_featured->setOrderBy('RAND()', '');
		$this->_featured = $this->_featured->getEmployee(0,$fperpage);

		return $this->_featured;
	}
	
	function getTotal()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_total))
		{
			$this->_total = $this->_db->setQuery(WorkforceHelperQuery::buildEmployeesCount($this->_where));
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
        $app        = &JFactory::getApplication();
        $option     = JRequest::getCmd('option', 'com_workforce');
        $currid     = JRequest::getInt('id', 0).':'.JRequest::getInt('Itemid', 0);

        $db         = &JFactory::getDbo();
        $this->_searchword = $app->getUserStateFromRequest( $option.'.department.search'.$currid, 'search', '', 'string' );

        if ( $this->_searchword ){
            $this->_searchword    = explode(' ', $this->_searchword);
            $this->_searchwhere   = array();
            if (is_array($this->_searchword)){ //more than one search word
                foreach ($this->_searchword as $word){
                    $this->_searchwhere[] = 'e.fname LIKE '.$db->Quote( '%'.$db->getEscaped( $word, true ).'%', false );
                    $this->_searchwhere[] = 'e.lname LIKE '.$db->Quote( '%'.$db->getEscaped( $word, true ).'%', false );
                }
            } else {
                $this->_searchwhere[] = 'e.fname LIKE '.$db->Quote( '%'.$db->getEscaped( $this->_searchword, true ).'%', false );
                $this->_searchwhere[] = 'e.lname LIKE '.$db->Quote( '%'.$db->getEscaped( $this->_searchword, true ).'%', false );
            }
        }

        $where = array();
        
        if( $this->_id ) $where[]           = 'e.department = '.(int)$this->_id;
        if( $this->_searchwhere ) $where[]  = "(".implode( ' OR ', $this->_searchwhere ).")";

        $this->_where = $where;
		return $this->_where;
	}

    function getDepartment()
    {
        $this->_department = WorkforceHelperQuery::buildDepartment($this->_id);
        return $this->_department;
    }
}

?>