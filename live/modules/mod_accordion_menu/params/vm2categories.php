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

if (!class_exists( 'VmConfig' )) require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart'.DS.'helpers'.DS.'config.php');
$config= VmConfig::loadConfig();
if(!class_exists('TableCategories')) require(JPATH_VM_ADMINISTRATOR.DS.'tables'.DS.'categories.php');
if (!class_exists( 'VirtueMartModelCategory' )) require(JPATH_VM_ADMINISTRATOR.DS.'models'.DS.'category.php');

class fixedVirtueMartModelCategory extends VirtueMartModelCategory{
	function GetTreeCat($id=0,$maxLevel = 1000) {
		self::treeCat($id ,$maxLevel) ;
		return $this->container ;
	}
  
	function treeCat($id=0,$maxLevel =1000) {
		static $level = 0;
		static $num = -1 ;
		$db = & JFactory::getDBO();
		$q = 'SELECT `category_child_id`,`category_name` FROM `#__virtuemart_categories_'.VMLANG.'`
		LEFT JOIN `#__virtuemart_category_categories` on `#__virtuemart_categories_'.VMLANG.'`.`virtuemart_category_id`=`#__virtuemart_category_categories`.`category_child_id`
		WHERE `category_parent_id`='.(int)$id;
		$db->setQuery($q);
		$num ++;
		// if it is a leaf (no data underneath it) then return
		$childs = $db->loadObjectList();
		if ($level==$maxLevel) return;
		if ($childs) {
			$level++;
			foreach ($childs as $child) {
				$this->container[$num]->id = $child->category_child_id;
				$this->container[$num]->name = $child->category_name;
				$this->container[$num]->level = $level;
				self::treeCat($child->category_child_id,$maxLevel );
			}
			$level--;
		}
	}
}

class JElementVM2Categories extends JOfflajnFakeElementBase{

  var $_name = 'VM2categories';
  
  function universalFetchElement($name, $value, &$node){
    if($value == NULL){
      $value = array();
    }elseif(!is_array($value)){
      $value = array($value);
    }
    $values = array_flip($value);
    array_walk($values, 'ofSetOne');

    $categoryModel = new fixedVirtueMartModelCategory();
    $cats = $categoryModel->GetTreeCat();

    $multiple = 1 ? "multiple=\"multiple\"" : "";
    ob_start();
		echo "<select class=\"inputbox\" size=\"10\" $multiple name=\"".$name."[]\" id=\"".$this->generateId($name)."\">\n";
		if( 1 ) {
			$selected = (@$values[0] == "1") ? "selected=\"selected\"" : "";
			echo "<option ".$selected." value=\"0\">Top Level</option>\n";
		}
		foreach($cats AS $cat){
  		$selected = '';
  		if( $selected == "" && @$values[$cat->id] == "1") {
  		  $selected = "selected=\"selected\"";
  		}
      echo "<option $selected value=\"$cat->id\">\n";
  		for ($i=0;$i<$cat->level;$i++) {
  			echo "&#151;";
  		}
  		echo "|$cat->level|";
  		echo "&nbsp;" . $cat->name . "</option>";
		}
		echo "</select>\n";
    return ob_get_clean();
  }
}

if(!function_exists('ofSetOne')){
  function ofSetOne(&$item, $key){
      $item = 1;
  }
}

if(version_compare(JVERSION,'1.6.0','ge')) {
  class JFormFieldVM2Categories extends JElementVM2Categories {}
}