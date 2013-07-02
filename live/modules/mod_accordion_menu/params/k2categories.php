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

class JElementK2Categories extends JElement{

  var $_name = 'VM1categories';
  
  function fetchElement($name, $value, &$node, $control_name){
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
