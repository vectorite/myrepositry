<?php
/**
 * @version 2.0.1 2012-08-17
 * @package Joomla
 * @subpackage Work Force
 * @copyright (C) 2012 the Thinkery
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.application.component.modellist');

class WorkforceModelEmployees extends JModelList
{
	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'id', 'e.id',
				'fname', 'e.fname',
                'lname', 'e.lname',
                'department', 'e.department', 'department_title',
                'email', 'e.email',
                'phone1', 'e.phone1',
				'state', 'e.state',
				'ordering', 'e.ordering',
                'state', 'featured', 'e.featured',
                'user_name', 'u.name'
			);
		}

		parent::__construct($config);
	}

    function &getDepartmentOrders()
	{
		if (!isset($this->cache['departmentorders'])) {
			$db		= $this->getDbo();
			$query	= $db->getQuery(true);
			$query->select('MAX(ordering) as `max`, department');
			$query->select('department');
			$query->from('#__workforce_employees');
			$query->group('department');
			$db->setQuery($query);
			$this->cache['departmentorders'] = $db->loadAssocList('department', 0);
		}
		return $this->cache['departmentorders'];
	}

    protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':'.$this->getState('filter.search');
		$id	.= ':'.$this->getState('filter.state');
        $id	.= ':'.$this->getState('filter.department_id');

		return parent::getStoreId($id);
	}

	public function getTable($type = 'Employee', $prefix = 'WorkforceTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication('administrator');

		// Load the filter state.
		$search = $app->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$state = $app->getUserStateFromRequest($this->context.'.filter.state', 'filter_state', '', 'string');
		$this->setState('filter.state', $state);

        $state = $app->getUserStateFromRequest($this->context.'.filter.department_id', 'filter_department_id', '', 'int');
		$this->setState('filter.department_id', $state);

		// Load the parameters.
		$params = JComponentHelper::getParams('com_workforce');
		$this->setState('params', $params);

		// List state information.
		parent::populateState('department asc, ordering', 'asc');
	}

    protected function getListQuery()
	{
		// Initialise variables.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'e.id AS id, e.fname AS fname, e.lname AS lname,'.
				'e.department AS department, e.email AS email, CONCAT_WS(" ", e.phone1, ext1) AS phone,'.
                'e.featured AS featured,'.
				'e.state AS state, e.ordering AS ordering,'.
				'e.icon as icon'                
			)
		);
		$query->from('`#__workforce_employees` AS e');

        // Join over the department
		$query->select('d.name as department_title');
		$query->join('LEFT', '`#__workforce_departments` AS d ON d.id = e.department');
        
        // Join over user
        $query->select('u.username as user_username, u.id as user_id, u.name as user_name');
		$query->join('LEFT', '`#__users` AS u ON u.id = e.user_id');

		// Filter by published state
		$published = $this->getState('filter.state');
		if (is_numeric($published)) {
			$query->where('e.state = '.(int) $published);
		} else if ($published === '') {
			$query->where('(e.state IN (0, 1))');
		}

        // Filter by department.
		$departmentId = $this->getState('filter.department_id');
		if ($departmentId && is_numeric($departmentId)) {
			$query->where('e.department = '.(int) $departmentId);
		}

		// Filter by search in title
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$query->where('e.id = '.(int) substr($search, 3));
			}
			else {
				$search     = JString::strtolower($search);
                $search     = explode(' ', $search);
                $searchwhere   = array();
                if (is_array($search)){ //more than one search word
                    foreach ($search as $word){
                        $searchwhere[] = 'LOWER(e.fname) LIKE '.$db->Quote( '%'.$db->getEscaped( $word, true ).'%', false );
                        $searchwhere[] = 'LOWER(e.lname) LIKE '.$db->Quote( '%'.$db->getEscaped( $word, true ).'%', false );
                    }
                } else {
                    $searchwhere[] = 'LOWER(e.fname) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
                    $searchwhere[] = 'LOWER(e.lname) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
                }
                $query->where('('.implode( ' OR ', $searchwhere ).')');
			}
		}

		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering');
		$orderDirn	= $this->state->get('list.direction');
        if ($orderCol == 'ordering' || $orderCol == 'department_title') {
			$orderCol = 'department_title '.$orderDirn.', ordering';
		}
		$query->order($db->getEscaped($orderCol.' '.$orderDirn));
        //echo $query;

		return $query;
	}

    public function featureEmployee($cid = array(), $feature = 1)
    {
        if (count( $cid ))
		{
			$cids = implode( ',', $cid );
            $query = "UPDATE #__workforce_employees SET featured = ".(int) $feature." WHERE id IN ($cids)";
            $this->_db->setQuery($query);

            if (!$this->_db->query()) {
                $this->setError($this->_db->getErrorMsg());
				return false;
            }
        }
    }
}//Class end