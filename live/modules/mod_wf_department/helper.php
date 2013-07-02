<?php
/**
 * @version 2.0.1 2012-08-17
 * @package Joomla
 * @subpackage Work Force
 * @copyright (C) 2012 the Thinkery
 * @license GNU/GPL see LICENSE.php
 */

defined('_JEXEC') or die('Restricted access');
require_once(JPATH_SITE.DS.'components'.DS.'com_workforce'.DS.'helpers'.DS.'employee.php');
require_once(JPATH_SITE.DS.'components'.DS.'com_workforce'.DS.'helpers'.DS.'html.helper.php');
require_once(JPATH_SITE.DS.'components'.DS.'com_workforce'.DS.'helpers'.DS.'query.php');
require_once(JPATH_SITE.DS.'components'.DS.'com_workforce'.DS.'helpers'.DS.'route.php');
require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_workforce'.DS.'classes'.DS.'admin.class.php');

jimport('joomla.utilities.date');

class modWFDepartmentHelper
{
	function prepareContent( $text, $length=300 ) 
    {
		// strips tags won't remove the actual jscript
		$text = preg_replace( "'<script[^>]*>.*?</script>'si", "", $text );
		$text = preg_replace( '/{.+?}/', '', $text);
		// replace line breaking tags with whitespace
		$text = preg_replace( "'<(br[^/>]*?/|hr[^/>]*?/|/(div|h[1-6]|li|p|td))>'si", ' ', $text );
		$text = strip_tags( $text );
		if (strlen($text) > $length) $text = substr($text, 0, $length) . "...";
		return $text;
	}

    function getEmployeesList( $where, $limitstart = 0, $limit = 9999, $sort = 'e.ordering', $order = 'ASC' )
	{
		$db = &JFactory::getDBO();
        $department = new workforceHelperEmployee($db);
        $department->setType('employees');
        $department->setWhere( $where );
        $department->setOrderBy( $sort, $order );
        $employees = $department->getEmployee($limitstart,$limit);
        return $employees;
	}

	function getList(&$params)
	{
		$count                  = (int) $params->get('count', 5);
		$text_length            = intval($params->get( 'preview_count', 75) );
        $dept                   = (int) $params->get('department', 0);
        $featured               = (bool) $params->get('featured', 0);

        if($params->get('random', 1)){
            $sort                   = 'RAND()';
            $order                  = '';
        }else{
            $sort                   = 'ordering';
            $order                  = 'ASC';
        }

        $where = array();
        if( $dept )     $where[] = 'e.department = '.$dept;
        if( $featured)  $where[] = 'e.featured = 1';

        $rows = modWFDepartmentHelper::getEmployeesList($where,0,$count, $sort, $order);

        $i		= 0;
        $lists	= array();
        if( $rows ){
            foreach ( $rows as $row )
            {
                $lists[$i]->link            = JRoute::_(WorkforceHelperRoute::getEmployeeRoute($row->id, $row->departmentid));
                $lists[$i]->name            = $row->name;
                $lists[$i]->title           = $row->position;
                $lists[$i]->address         = $row->street_address;
                $lists[$i]->departmentname  = $row->departmentname;
                $lists[$i]->mainimage       = $row->icon;

                $prepared_text = modWFDepartmentHelper::prepareContent($row->bio, $text_length);
                if($params->get('clean_desc', 0)){
                    $lists[$i]->introtext = modWFDepartmentHelper::sentence_case($prepared_text);
                }else{
                    $lists[$i]->introtext = $prepared_text;
                }
                $i++;

                $prepared_text = '';
            }
        }

		return $lists;
	}

    function sentence_case($string) 
    {
		$sentences = preg_split('/([.?!]+)/', $string, -1, PREG_SPLIT_NO_EMPTY|PREG_SPLIT_DELIM_CAPTURE);
		$new_string = '';
		foreach ($sentences as $key => $sentence) {
			$new_string .= ($key & 1) == 0?
            ucfirst(strtolower(trim($sentence))) :
            $sentence.' ';
		}
		return trim($new_string);
	}
}