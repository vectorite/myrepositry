<?php
/**
 * @version 2.0.1 2012-08-17
 * @package Joomla
 * @subpackage Work Force
 * @copyright (C) 2012 the Thinkery
 * @license GNU/GPL see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.helper');

class WorkforceHelperRoute
{
    // all departments
    function getAllDepartmentsRoute()
	{
		$needles = array(
            'departments' => '',
            'allemployees' => ''
        );

		$link = 'index.php?option=com_workforce&view=departments';

		if($item = WorkforceHelperRoute::_findItem($needles)) {
			if(isset($item->query['layout'])) {
				$link .= '&layout='.$item->query['layout'];
			}
			$link .= '&Itemid='.$item->id;
		};
		return $link;
	}
    
    // all employees
    function getAllEmployeesRoute()
	{
		$needles = array(
            'allemployees' => '',
            'departments' => ''
        );

		$link = 'index.php?option=com_workforce&view=allemployees';

		if($item = WorkforceHelperRoute::_findItem($needles)) {
			if(isset($item->query['layout'])) {
				$link .= '&layout='.$item->query['layout'];
			}
			$link .= '&Itemid='.$item->id;
		};
		return $link;
	}

    // department
    function getDepartmentRoute($depid = null)
	{
		$needles = array(
            'department' => (int) $depid,
            'departments' => '',
            'allemployees' => ''
        );

		$link = 'index.php?option=com_workforce&view=department&id='.(int)$depid;

		if($item = WorkforceHelperRoute::_findItem($needles)) {
			if(isset($item->query['layout'])) {
				$link .= '&layout='.$item->query['layout'];
			}
			$link .= '&Itemid='.$item->id;
		};
		return $link;
	}

    // employee
    function getEmployeeRoute($empid = null, $depid = null)
	{
		$needles = array(
            'employee' => (int) $empid,
            'department' => (int) $depid,
            'departments' => '',
            'allemployees' => ''
        );

		$link = 'index.php?option=com_workforce&view=employee&id='.(int)$empid;

		if($item = WorkforceHelperRoute::_findItem($needles)) {
			$link .= '&Itemid='.$item->id;
		};
		return $link;
	}

	function _findItem($needles)
	{
        $component  = &JComponentHelper::getComponent('com_workforce');
		$menus      = &JApplication::getMenu('site', array());
		$items      = $menus->getItems('component_id', $component->id);
		$match      = null;

		if($items){
            foreach($needles as $needle => $id)
            {
                foreach($items as $item)
                {
                    if(isset($id)){
                        if ((@$item->query['view'] == $needle) && (@$item->query['id'] == $id)) {
                            $match = $item;
                            break;
                        }
                    }else{
                        if ((@$item->query['view'] == $needle)) {
                            $match = $item;
                            break;
                        }
                    }
                }

                if(isset($match)) {
                    break;
                }
            }
            return $match;
        }
	}
}
?>
