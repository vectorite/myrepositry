<?php
/**
 * @version 2.0.1 2012-08-17
 * @package Joomla
 * @subpackage Work Force
 * @copyright (C) 2012 the Thinkery
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

class WorkforceHelperQuery extends JObject
{
	var $_sort	= null;
	var $_order	= null;

    function buildEmployeesQuery($where, $limitstart = 0, $limit = 9999, $debug = null)
	{
        $sql = "SELECT e.*, d.id AS departmentid, d.name AS departmentname,"
              ." CONCAT_WS(' ',fname, lname) AS name,"
              ." CONCAT_WS(' ',street, street2) AS street_address"
              ." FROM #__workforce_employees AS e"
              ." LEFT JOIN #__workforce_departments AS d ON d.id = e.department"
              ." WHERE e.state = 1 AND d.state = 1";

		if( !empty($where) ) {
			if(is_array($where)) {
				$sql .= ' AND ' . implode( ' AND ', $where );
			} else {
				$sql .= ' AND ' . $where;
			}
		}

		if(!isset($this->_sort) || !isset($this->_order)) {
			$sql .= ' ORDER BY d.name, e.ordering ASC';
		} else {
			$sql .= ' ORDER BY ' . $this->_sort . ' ' . $this->_order;
		}
		$sql .= ' LIMIT ' . $limitstart . ', ' . $limit;

		//echo $sql . '<br /><br />';
		return $sql;
	}

    function buildEmployeesCount($where = '')
	{
        $sql = "SELECT COUNT(*) FROM #__workforce_employees AS e"
               ." LEFT JOIN #__workforce_departments AS d ON d.id = e.department"
               ." WHERE e.state = 1 AND d.state = 1";

		if( !empty($where) ) {
			if(is_array($where)) {
				$sql .= ' AND ' . implode( ' AND ', $where );
			} else {
				$sql .= ' AND ' . $where;
			}
		}
		return $sql;
	}

    function buildEmployee($id)
    {
        $database = JFactory::getDBO();
        $query = 'SELECT e.*, d.id AS departmentid, d.name AS departmentname,'
                .' CONCAT_WS(" ",fname, lname) AS name,'
                .' CONCAT_WS(" ",street, street2) AS street_address'
                .' FROM #__workforce_employees AS e'
                .' LEFT JOIN #__workforce_departments AS d ON d.id = e.department'
                .' WHERE e.id = '.(int)$id.' LIMIT 1';
		$database->setQuery( $query );
		return $database->loadObject();
    }

    function buildDepartment($id)
    {
		$database   = JFactory::getDBO();
		$query      = "SELECT * FROM #__workforce_departments WHERE id = ".(int)$id." LIMIT 1";
		$database->setQuery($query);
		return $database->loadObject();
	}

    function buildDepartmentsQuery($where, $limitstart = 0, $limit = 9999, $debug = null)
	{
        $sql = "SELECT d.*, COUNT(e.id) AS count FROM #__workforce_departments d"
             ." JOIN #__workforce_employees e ON d.id = e.department"
             ." WHERE d.state = 1 AND e.state = 1";

		if( !empty($where) ) {
			if(is_array($where)) {
				$sql .= ' AND ' . implode( ' AND ', $where );
			} else {
				$sql .= ' AND ' . $where;
			}
		}

		$sql .= ' GROUP BY d.id';

		if(!isset($this->_sort) || !isset($this->_order)) {
			$sql .= ' ORDER BY d.name, e.ordering ASC';
		} else {
			$sql .= ' ORDER BY ' . $this->_sort . ' ' . $this->_order;
		}
		$sql .= ' LIMIT ' . $limitstart . ', ' . $limit;

		return $sql;
	}

    function buildDepartmentsCount($where = '')
	{
        $sql = "SELECT COUNT(DISTINCT(d.id)) FROM #__workforce_departments AS d"
              ." JOIN #__workforce_employees e ON d.id = e.department"
              ." WHERE d.state = 1 AND e.state = 1";

		if( !empty($where) ) {
			if(is_array($where)) {
				$sql .= ' AND ' . implode( ' AND ', $where );
			} else {
				$sql .= ' AND ' . $where;
			}
		}
		return $sql;
	}
}
?>