<?php
/*------------------------------------------------------------------------
# offlajnlist - Offlajn List Parameter
# ------------------------------------------------------------------------
# author    Jeno Kovacs 
# copyright Copyright (C) 2012 Offlajn.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.offlajn.com
-------------------------------------------------------------------------*/

defined('_JEXEC') or die('Restricted access');

JOfflajnParams::load('offlajnmultiselectlist');

class JElementOfflajnMenuItemExtended extends JElementOfflajnMultiSelectList {
  
  var $_name = 'OfflajnMenuItemExtended';

function getItems() {
    $this->loadFiles();
    $this->loadFiles('OfflajnList');
    
    $where = "";
    $db =& JFactory::getDBO();
    
		// load the list of menu types
		// TODO: move query to model
		$query = 'SELECT menutype, title' .
				' FROM #__menu_types' .
				' ORDER BY title';
		$db->setQuery( $query );
		$menuTypes = $db->loadObjectList();

    $where = ' WHERE 1 ';
		
		if (!empty($menuType)) {
			$where .= ' AND menutype = '.$db->Quote($menuType);
		}
    
			$where .= ' AND published = 1 ';

		// load the list of menu items
		// TODO: move query to model
		if(version_compare(JVERSION,'1.6.0','ge')) 
  		$query = 'SELECT id, parent_id, parent_id as parent, title, menutype, type' .
  			' FROM #__menu' .
  			$where .
  			' ORDER BY menutype, lft, parent_id, ordering'
  		;
		else
  		$query = 'SELECT id, parent AS parent_id, parent, name, menutype, type' .
  			' FROM #__menu' .
  			$where .
  			' ORDER BY menutype, parent, ordering'
  		;		
		$db->setQuery($query);
		$menuItems = $db->loadObjectList();
		// establish the hierarchy of the menu
		// TODO: use node model
		$children = array();

		if ($menuItems){
			// first pass - collect children
			foreach ($menuItems as $v){
			  $pt 	= $v->parent_id;
				
        $list 	= @$children[$pt] ? $children[$pt] : array();
				array_push( $list, $v );
				$children[$pt] = $list;
			}
		}

		// second pass - get an indent list of the items
		$list = JHTML::_('menu.treerecurse', 0, '', array(), $children, 9999, 0, 0 );
    
		// assemble into menutype groups
		$n = count( $list );
		$groupedList = array();
		foreach ($list as $k => $v) {
			$groupedList[$v->menutype][] = &$list[$k];
		}
  return $groupedList;
  }
}

if(version_compare(JVERSION,'1.6.0','ge')) {
  class JFormFieldOfflajnMenuItemExtended extends JElementOfflajnMenuItemExtended {}
}