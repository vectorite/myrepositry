<?php
/**
 * @version 2.0.1 2012-08-17
 * @package Joomla
 * @subpackage Work Force
 * @copyright (C)  2010 the Thinkery
 * @license see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

class plgSearchWorkforce extends JPlugin
{
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}

    function onContentSearchAreas()
	{
		static $areas = array(
			'workforce' => 'PLG_WF_SEARCH_EMPLOYEES'
		);
		return $areas;
	}
	
	function onContentSearch( $text, $phrase='', $ordering='', $areas=null )
	{	
		require_once(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_search'.DS.'helpers'.DS.'search.php');
        require_once(JPATH_SITE.DS.'components'.DS.'com_workforce'.DS.'helpers'.DS.'html.helper.php');
        require_once(JPATH_SITE.DS.'components'.DS.'com_workforce'.DS.'helpers'.DS.'route.php');
	
		$db		= JFactory::getDbo();
		$app	= JFactory::getApplication();
		$user	= JFactory::getUser();
		$groups	= implode(',', $user->getAuthorisedViewLevels());

		if (is_array($areas)) {
			if (!array_intersect($areas, array_keys($this->onContentSearchAreas()))) {
				return array();
			}
		}
	
		// load plugin params info	
		$wfname 		= $this->params->get( 'search_name', 		1 );
        $wfbio 			= $this->params->get( 'search_bio', 		1 );
		$limit 			= $this->params->def( 'search_limit', 		50 );
	
		$text = trim($text);
		if ($text == '') {
			return array();
		}
	
		$wheres = array();
		switch ($phrase) {
			case 'exact':
				$text		= $db->Quote( '%'.$db->getEscaped( $text, true ).'%', false );
				$wheres2 	= array();
				if($wfname ) {
                    $wheres2[] 	= 'e.fname LIKE '. $text;
                    $wheres2[] 	= 'e.lname LIKE '. $text;
                }
                if($wfbio ) {
                    $wheres2[] 	= 'e.bio LIKE '. $text;
                }
				$where 		= '(' . implode( ') OR (', $wheres2 ) . ')';
				break;
			case 'all':
			case 'any':
			default:
				$words = explode( ' ', $text );
				$wheres = array();
				foreach ($words as $word) {
					$word		= $db->Quote( '%'.$db->getEscaped( $word, true ).'%', false );
					$wheres2 	= array();
					if($wfname ) {
                        $wheres2[] 	= 'e.fname LIKE '. $word;
                        $wheres2[] 	= 'e.lname LIKE '. $word;
                    }
                    if($wfbio ) {
                        $wheres2[] 	= 'e.bio LIKE '. $word;
                    }
					$wheres[] 	= implode( ' OR ', $wheres2 );
				}
				$where = '(' . implode( ($phrase == 'all' ? ') AND (' : ') OR ('), $wheres ) . ')';
				break;
		}
	
		$morder = '';
		switch ($ordering) {	
			case 'alpha':
				$morder = 'e.lname ASC';
				break;
			
			case 'category':
                default:
				$morder = 'd.name ASC, e.lname ASC';
                break;
		}
	
		$rows = array();		
		if ( $limit > 0 )
		{
            $query = 'SELECT CONCAT_WS(": ",d.name,CONCAT_WS(" ",e.fname,e.lname)) AS title,'
				. ' e.bio AS text,'
				. ' d.name AS section,'
				. ' e.id AS employee_id,'
				. ' d.id AS department_id,'
				. ' "2" AS browsernav'
				. ' FROM #__workforce_employees AS e'
				. ' LEFT JOIN #__workforce_departments AS d ON d.id = e.department'
				. ' WHERE ( '.$where.' )'
				. ' AND e.state = 1'
                . ' AND d.state = 1'
				. ' GROUP BY e.id'
				. ' ORDER BY '. $morder;
			
			$db->setQuery( $query, 0, $limit );
			$list = $db->loadObjectList();
			$limit -= count($list);
	
			if(isset($list))
			{
				foreach($list as $key => $item)
				{
					$wfroute = workforceHelperRoute::getEmployeeRoute($item->employee_id, $item->department_id);
                    $list[$key]->href = JRoute::_( $wfroute );
				}
			}
			$rows[] = $list;
		}
	
	
		$results = array();
		if(count($rows))
		{
			foreach($rows as $row)
			{
				$new_row = array();
				foreach($row AS $key => $post) {
					if(searchHelper::checkNoHTML($post, $text, array('text', 'title'))) {
						$new_row[] = $post;
					}
				}
				$results = array_merge($results, (array) $new_row);
			}
		}
	
		return $results;
	}
}