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

class JElementHikashopCategories extends JElement{

  var $_name = 'Hikashopcategories';
  
  function fetchElement($name, $value, &$node, $control_name){
		$db = &JFactory::getDBO();

		$query = 'SELECT m.category_id AS id, m.category_name AS name, m.category_name as title, m.category_parent_id AS parent,  m.category_parent_id as parent_id
              FROM #__hikashop_category m
              WHERE (m.category_published =1 AND m.category_type="product") OR (m.category_type="root" AND m.category_published =0)
              ORDER BY m.category_ordering'; 
 
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
		$mitems = array();
		$mitems [] = JHTML::_ ( 'select.option', '0', '- ' . JText::_ ( 'All items' ) . ' -' );

		foreach ( $list as $item ) {
			$mitems[] = JHTML::_('select.option',  $item->id, '&nbsp;&nbsp;&nbsp;'.$item->treename );
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
