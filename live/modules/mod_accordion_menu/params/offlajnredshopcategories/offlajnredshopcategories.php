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

class JElementOfflajnRedshopCategories extends JElementOfflajnMultiSelectList {

  function getItems() {
    $db = &JFactory::getDBO();

		$query = 'SELECT m.category_id AS id, m.category_name AS title, m.category_name AS name, f.category_parent_id AS parent_id, f.category_parent_id as parent
              FROM #__redshop_category m
              LEFT JOIN #__redshop_category_xref AS f
              ON m.category_id = f.category_child_id
              WHERE m.published =1
              ORDER BY m.ordering';
		$db->setQuery( $query );
		$menuItems = $db->loadObjectList();
		$children = array();
		if ( $menuItems )
		{
			foreach ($menuItems as $v){
			  $pt 	= $v->parent_id;
				
        $list 	= @$children[$pt] ? $children[$pt] : array();
				array_push( $list, $v );
				$children[$pt] = $list;
			}
		}
		$list = JHTML::_('menu.treerecurse', 0, '', array(), $children, 9999, 0, 0 );
		
		$n = count( $list );
		$groupedList = array();
		foreach ($list as $k => $v) {
			@$groupedList["redshop"][] = &$list[$k];
		}
  return $groupedList;
  }

}

if(version_compare(JVERSION,'1.6.0','ge')) {
  class JFormFieldOfflajnRedshopCategories extends JElementOfflajnRedshopCategories {}
}
