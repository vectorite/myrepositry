<?php 
/*------------------------------------------------------------------------
# mod_vm_accordion - Accordion Menu for Virtuemart 
# ------------------------------------------------------------------------
# author    Roland Soos 
# copyright Copyright (C) 2011 Offlajn.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.offlajn.com
-------------------------------------------------------------------------*/
?>
<?php
// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );

global $mosConfig_absolute_path;
if( !isset( $mosConfig_absolute_path ) ) {
 $mosConfig_absolute_path = $GLOBALS['mosConfig_absolute_path']	= JPATH_SITE;
}

if (!class_exists( 'categoryModelcategory' )) require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_redshop'.DS.'models'.DS.'category.php');

class JElementRedshopCategories extends JElement{

  var $_name = 'redshopcategories';
  
  function fetchElement($name, $value, &$node, $control_name){
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
		$mitems = array();
		$mitems [] = JHTML::_ ( 'select.option', '0', '- ' . JText::_ ( 'All items' ) . ' -' );

		foreach ( $list as $item ) {
			$mitems[] = JHTML::_('select.option',  $item->id, version_compare(JVERSION,'1.6.0','ge') ? str_replace("&#160;",'-',( $item->treename)) : $item->treename );
		}

		$attributes = 'class="inputbox" size=10';
		if(1){
			$attributes.=' multiple="multiple"';
		}

		return JHTML::_('select.genericlist',  $mitems, ''.$control_name.'['.$name.'][]', $attributes, 'value', 'text', $value );
  }
}

if(!function_exists('ofSetOne')){
  function ofSetOne(&$item, $key){
      $item = 1;
  }
}
