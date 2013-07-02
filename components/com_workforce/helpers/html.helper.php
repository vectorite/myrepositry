<?php
/**
 * @version 2.0.1 2012-08-17
 * @package Joomla
 * @subpackage Work Force
 * @copyright (C) 2012 the Thinkery
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

class workforceHTML 
{
    function snippet($text, $length = 200, $tail = "...")
    {
       $text = trim($text);
       $text = strip_tags($text);
       $txtl = strlen($text);
       if($txtl > $length) {
           for($i=1;$text[$length-$i]!=" ";$i++) {
               if($i == $length) {
                   return substr($text,0,$length) . $tail;
               }
           }
           $text = substr($text,0,$length-$i+1) . $tail;
       }
       return $text;
    }
	
	function buildNoResults($wrapper = null, $cols = null)
    {
		$cols = ($cols) ? $cols : 2;
        $html = '';
        if( $wrapper ) $html .= '<table class="wftable">';
		$html .= '<tr>
					 <td colspan="'.$cols.'" align="center">
						<div class="wf_noresults">
							'.JHTML::_('image.site', 'workforce1.jpg','/administrator/components/com_workforce/assets/images/','','','').'
                            <div>'.JText::_('COM_WORKFORCE_NO_RECORDS_TEXT').'</div>
						</div>
				    </td>
				 </tr>';
        if( $wrapper ) $html .= '</table>';
		
		return $html;
	}
	
	function buildThinkeryFooter()
    {
		return '<div class="wf_footer">
                    '. JText::_('COM_WORKFORCE_WORK_FORCE_FOOTER').' <a href="http://www.thethinkery.net" target="_blank">theThinkery.net</a>. v'.workforceAdmin::_getversion().'
                </div>';
	}

    function buildEmployeeSortList($filter_order, $attrib = null, $view = null)
    {
        $sortbys    = array();
        $sortbys[]  = JHTML::_('select.option', 'e.ordering', JText::_( 'COM_WORKFORCE_SELECT' ) );
		$sortbys[]  = JHTML::_('select.option', 'e.lname', JText::_( 'COM_WORKFORCE_LAST_NAME' ) );
        $sortbys[]  = JHTML::_('select.option', 'e.fname', JText::_( 'COM_WORKFORCE_FIRST_NAME' ) );
        if($view == 'allemployees') $sortbys[] = JHTML::_('select.option', 'd.name', JText::_( 'COM_WORKFORCE_DEPARTMENT' ) ); //ADDED 2/11/10
        return JHTML::_('select.genericlist', $sortbys, 'filter_order', $attrib, 'value', 'text', $filter_order );
    }

    function buildOrderList($filter_order_dir, $attrib = null)
    {
        $orderbys   = array();
		$orderbys[] = JHTML::_('select.option', 'ASC', JText::_( 'COM_WORKFORCE_ASCENDING' ) );
		$orderbys[] = JHTML::_('select.option', 'DESC', JText::_( 'COM_WORKFORCE_DESCENDING' ) );
        return JHTML::_('select.genericlist', $orderbys, 'filter_order_dir', $attrib, 'value', 'text', $filter_order_dir );
    }

    function getStateName($state)
    {
		$db         = JFactory::getDbo();
        
        $query = $db->getQuery(true);
        $query->select('id, title')
                ->from('#__workforce_states')
                ->where('id = '.(int)$state);
		
        $db->setQuery($query, 0, 1);
		$result = $db->loadObject();
        return $result->title;
	}
    
    function getCountryName($country)
    {
		$db         = JFactory::getDbo();
        
        $query = $db->getQuery(true);
        $query->select('id, title')
                ->from('#__workforce_countries')
                ->where('id = '.(int)$country);
		
        $db->setQuery($query, 0, 1);
        $result = $db->loadObject();
        return $result->title;
	}    

    function getDepartmentName($id)
    {
		$db         = JFactory::getDbo();
        
        $query = $db->getQuery(true);
        $query->select('id, name')
                ->from('#__workforce_departments')
                ->where('id = '.(int)$id);
		
        $db->setQuery($query, 0, 1);
        $result = $db->loadObject();
        return $result->name;
	}

    function country_select_list($tag, $attrib, $sel = null, $show_available = false, $listonly = false)
    {
		$db             = JFactory::getDbo();
        $countries      = array();
        $countries[]    = JHTML::_('select.option', '', JText::_( 'COM_WORKFORCE_COUNTRY' ), "value", "text" );
        
        $query = $db->getQuery(true);
        $query->select('DISTINCT(c.id), c.id AS value, c.title AS text')
            ->from('#__workforce_countries as c');
        if($show_available){
            $query->join('INNER','#__workforce_employees AS e ON e.country = c.id');
        }
        $query->order('c.title ASC');
        
        $db->setQuery($query);
        $result = $db->loadObjectList();

        foreach($result as $r){
            $countries[] = JHTML::_('select.option', $r->value, JText::_($r->text), "value", "text" );
        }

        if($listonly){
            return $countries;
        }else{
            return JHTML::_('select.genericlist', $countries, $tag, $attrib, "value", "text", $sel );
        }
	}

    function state_select_list($tag, $attrib = null, $sel = null, $show_available = false, $listonly = false)
    {
		$db         = JFactory::getDbo();
        $states     = array();
        $states[]   = JHTML::_('select.option', '', JText::_( 'COM_WORKFORCE_STATE' ), "value", "text" );
        
        $query = $db->getQuery(true);
        $query->select('DISTINCT(s.id), s.id AS value, s.title AS text')
            ->from('#__workforce_states as s');
        if($show_available){
            $query->join('INNER','#__workforce_employees AS e ON e.locstate = s.id');
        }
        $query->order('s.title ASC');
        
        $db->setQuery($query);
        $result = $db->loadObjectList();

        foreach($result as $r){
            $states[] = JHTML::_('select.option', $r->value, JText::_($r->text), "value", "text" );
        }

        if($listonly){
            return $states;
        }else{
            return JHTML::_('select.genericlist', $states, $tag, $attrib, "value", "text", $sel );
        }
	}

    function checkbox( $name, $tag_attribs = null, $value = null, $text = null, $showtext = null, $checked = null ) 
    {
        $text       = ($showtext) ? "&nbsp;".$text : '';
        $checked    = ($checked) ? " checked=\"checked\"" : '';
        return "<input type=\"checkbox\" name=\"".$name."\" ".$tag_attribs." value=\"".$value."\"".$checked." />".$text;
    }

    function getEmployeeName($employee_id)
	{
		$db         = JFactory::getDbo();
        
        $query = $db->getQuery(true);
        $query->select('id, CONCAT_WS(" ",fname, lname) AS name')
                ->from('#__workforce_employees')
                ->where('id = '.(int)$employee_id);
		
        $db->setQuery($query, 0, 1);
        $result = $db->loadObject();
        return $result->name;
	}

    function departmentSelectList($tag, $attrib = null, $sel = null)
    {
		$db         = JFactory::getDbo();       
		$dep        = array();
		$dep[]      = JHTML::_('select.option', '', JText::_('COM_WORKFORCE_DEPARTMENT'), "value","text" );
        
        $query = $db->getQuery(true);
        $query->select('id AS value, name AS text')
                ->from('#__workforce_departments')
                ->where('state = 1')
                ->order('name ASC');
        
        $db->setQuery($query);
		$dep = array_merge( $dep, $db->loadObjectList() );

		return  JHTML::_('select.genericlist', $dep, $tag, $attrib, "value", "text", $sel);
	}

    function getDeptCount($id)
    {
        $db         = JFactory::getDbo();
        
        $query = $db->getQuery(true);
        $query->select('COUNT(id)')
                ->from('#__workforce_employees')
                ->where('department = '.(int)$id)
                ->where('state = 1');
        
        $db->setQuery($query);
        return $db->loadResult();
    }

    function print_popup($employee, $attribs = array())
    {
        $url  = 'index.php?view=employee';
        $url .= '&id='.$employee->id.'&tmpl=component&print=1';

        $status = 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no';

        // checks template image directory for image, if non found default are loaded
        $text = JHTML::_('image','components/com_workforce/assets/images/print.png', JText::_('COM_WORKFORCE_PRINT'), 'class="hasTip" title="'.JText::_('COM_WORKFORCE_PRINT').'::'.JText::_('COM_WORKFORCE_PRINT_TIP').'"');

        $attribs['title']   = JText::_( 'COM_WORKFORCE_PRINT' );
        $attribs['onclick'] = "window.open(this.href,'win2','".$status."'); return false;";

        return JHTML::_('link', JRoute::_($url), $text, $attribs);
    }
    
    function getUrl($url)
    {
        if(!$url) return;
        if (!preg_match('#http[s]?://|index[2]?\.php#', $url)) {
			$url = "http://$url";
		}
        return $url;
    }
}