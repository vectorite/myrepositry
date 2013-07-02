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

global $mosConfig_absolute_path;
if( !isset( $mosConfig_absolute_path ) ) {
 $mosConfig_absolute_path = $GLOBALS['mosConfig_absolute_path']	= JPATH_SITE;
}

class JElementOfflajnTiendaCategories extends JElementOfflajnMultiSelectList {

  function getItems() {
    		$db = &JFactory::getDBO();

		$query = 'SELECT m.category_id AS id, m.category_name AS name, m.parent_id AS parent,  m.parent_id as parent_id
              FROM #__tienda_categories m
              WHERE m.category_enabled =1
              ORDER BY m.ordering';
          
		$db->setQuery( $query );
		$mitems = $db->loadObjectList();
		$children = array();
		
		if ( $mitems )
		{
			foreach ( $mitems as $v )
			{
				$pt 	= $v->parent;
				$list = @$children[$pt] ? $children[$pt] : array();
				array_push( $list, $v );
				$children[$pt] = $list;
			}
		}
		$list = JHTML::_('menu.treerecurse', 0, '', array(), $children, 9999, 0, 0 );
		
		$n = count( $list );
		$groupedList = array();
		foreach ($list as $k => $v) {
			@$groupedList["tienda"][] = &$list[$k];
		}
    return $groupedList;  
  }

}

if(version_compare(JVERSION,'1.6.0','ge')) {
  class JFormFieldOfflajnTiendaCategories extends JElementOfflajnTiendaCategories {}
}
