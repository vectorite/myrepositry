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

require_once(JPATH_SITE.DS.'components'.DS.'com_virtuemart'.DS.'virtuemart_parser.php');
require_once JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'classes'.DS.'ps_product_category.php';

class JElementVM1Categories extends JElement{

  var $_name = 'VM1categories';
  
  function fetchElement($name, $value, &$node, $control_name){
    if(!is_array($value)){
      $value = array($value);
    }
    $values = array_flip($value);
    array_walk($values, 'ofSetOne');
    $pc = new of_ps_product_category();
    ob_start();
    $pc->list_all('params['.$name.'][]', 0, $values, 10, true, true);
    return ob_get_clean();
  }
}

if(!function_exists('ofSetOne')){
  function ofSetOne(&$item, $key){
      $item = 1;
  }
}

class of_ps_product_category extends ps_product_category{
	function list_all($name, $category_id, $selected_categories=Array(), $size=1, $toplevel=true, $multiple=false, $disabledFields=array() ) {
		global $VM_LANG;
		
		$db = new ps_DB;

		$q  = "SELECT category_parent_id FROM #__{vm}_category_xref ";
		if( $category_id ) {
			$q .= "WHERE category_child_id='$category_id'";
		}
		$db->query( $q );
		$db->next_record();
		$category_id=$db->f("category_parent_id");
		$multiple = $multiple ? "multiple=\"multiple\"" : "";
		$id = str_replace('[]', '', $name );
		echo "<select class=\"inputbox\" size=\"$size\" $multiple name=\"$name\" id=\"$id\">\n";
		if( $toplevel ) {
			$selected = (@$selected_categories[0] == "1") ? "selected=\"selected\"" : "";
			echo "<option ".$selected." value=\"0\">".$VM_LANG->_('VM_DEFAULT_TOP_LEVEL')."</option>\n";
		}
		$this->list_tree($category_id, '0', '0', $selected_categories, $disabledFields );
		echo "</select>\n";
	}
}