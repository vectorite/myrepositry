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

class JElementOfflajnK2Categories extends JElementOfflajnMultiSelectList {

function getItems() {
		$db = &JFactory::getDBO();

		$query = 'SELECT m.*, m.name AS title, m.parent AS parent, m.parent AS parent_id  FROM #__k2_categories m WHERE published = 1 ORDER BY parent, ordering';
		$db->setQuery( $query );
		$menuItems = $db->loadObjectList();
		$children = array();
    if ( $menuItems ){
			foreach ($menuItems as $v){
			  $pt 	= $v->parent_id;	
        $list 	= @$children[$pt] ? $children[$pt] : array();
				array_push( $list, $v );
				$children[$pt] = $list;
			}
		}		
		$list = JHTML::_('menu.treerecurse', 0, '', array(), $children, 9999, 0, 0 );
    
		// assemble into menutype groups
		$n = count( $list );
		$groupedList = array();
		foreach ($list as $k => $v) {
			$groupedList["k2"][] = &$list[$k];
		}
  return $groupedList;
  }
}

if(version_compare(JVERSION,'1.6.0','ge')) {
  class JFormFieldOfflajnK2Categories extends JElementOfflajnK2Categories {}
}