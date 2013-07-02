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
  if(version_compare(JVERSION,'1.6.0','l')) {
    global $mosConfig_absolute_path;
    if( !isset( $mosConfig_absolute_path ) ) {
     $mosConfig_absolute_path = $GLOBALS['mosConfig_absolute_path']	= JPATH_SITE;
    }
    
    if(!class_exists('ps_product_category') && file_exists(JPATH_SITE . DS . 'components' . DS . 'com_virtuemart'.DS.'virtuemart_parser.php')) {
      require_once(JPATH_SITE.DS.'components'.DS.'com_virtuemart'.DS.'virtuemart_parser.php');
      require_once JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'classes'.DS.'ps_product_category.php';
    }
  }
class JElementOfflajnVm1Categories extends JElementOfflajnMultiSelectList {

function getItems() {

    $pc = new of_ps_product_category();
    ob_start();
    $pc->list_all('', 0, '', 10, true, true);
    $elements = ob_get_clean();
    $el = explode("///", $elements);
 
    $items = array();

    for($i=1;$i<count($el);$i += 2) {
      $items[$el[$i]] = $el[$i+1];
    }
    
    $groupedList = array();
    $i = 0;
    foreach($items as $k => $element) {
      $item = new stdClass();
      $item->treename = $element;
      $item->id = $k;
      //$groupedList['vm1'][$k] = $item;
      $groupedList['vm1'][$i] = $item;
      $i++;
    }
    
    
    
 return $groupedList;
  }
  
}

if(version_compare(JVERSION,'1.6.0','ge')) {
  class JFormFieldOfflajnVm1Categories extends JElementOfflajnVm1Categories {}
}
  if(version_compare(JVERSION,'1.6.0','l') && file_exists(JPATH_SITE . DS . 'components' . DS . 'com_virtuemart'.DS.'virtuemart_parser.php')) {
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
    	//	echo "<select class=\"inputbox\" size=\"$size\" $multiple name=\"$name\" id=\"$id\">\n";
    		if( $toplevel ) {
    			$selected = (@$selected_categories[0] == "1") ? "selected=\"selected\"" : "";
    		//	echo "<option ".$selected." value=\"0\">".$VM_LANG->_('VM_DEFAULT_TOP_LEVEL')."</option>\n";
    	
    		}
    		$this->list_tree($category_id, '0', '0', $selected_categories, $disabledFields );
    	//	echo "</select>\n";
    	}
    	
    		function list_tree($category_id="", $cid='0', $level='0', $selected_categories=Array(), $disabledFields=Array() ) {
    
      		$ps_vendor_id = $_SESSION["ps_vendor_id"];
      		$db = new ps_DB;
      
      		$level++;
      
      		$q = "SELECT category_id, category_child_id,category_name FROM #__{vm}_category,#__{vm}_category_xref ";
      		$q .= "WHERE #__{vm}_category_xref.category_parent_id='$cid' ";
      		$q .= "AND #__{vm}_category.category_id=#__{vm}_category_xref.category_child_id ";
      		$q .= "AND #__{vm}_category.vendor_id ='$ps_vendor_id' ";
      		$q .= "ORDER BY #__{vm}_category.list_order, #__{vm}_category.category_name ASC";
      		$db->setQuery($q);   $db->query();
            $st = 0;
      		while ($db->next_record()) {
      			$child_id = $db->f("category_child_id");
      			if ($child_id != $cid) {
      				$selected = ($child_id == $category_id) ? "selected=\"selected\"" : "";
      				if( $selected == "" && @$selected_categories[$child_id] == "1") {
      					$selected = "selected=\"selected\"";
      				}
      				$disabled = '';
      				if( in_array( $child_id, $disabledFields )) {
      					$disabled = 'disabled="disabled"';
      				}
      				if( $disabled != '' && stristr($_SERVER['HTTP_USER_AGENT'], 'msie')) {
      					// IE7 suffers from a bug, which makes disabled option fields selectable
      				} else {
      					//echo "<option $selected $disabled value=\"$child_id\">\n";
      					//VALUE
      					  echo "///".$child_id."///";
      				//	$this->ids['vm1'][] = $child_id;
      					for ($i=0;$i<$level;$i++) {
      						echo "&#160;";
      					}
      					echo "|$level|";
      					//echo "&nbsp;" . $db->f("category_name") . "</option>";
      					echo "&nbsp;" . $db->f("category_name")."\n";
      				}
      			}
      			$this->list_tree($category_id, $child_id, $level, $selected_categories, $disabledFields);
      	   $st++;
        	}
    	}
    	
    }
  }