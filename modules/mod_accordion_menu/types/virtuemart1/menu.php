<?php 
/*------------------------------------------------------------------------
# mod_accordion_menu - Accordion Menu - Offlajn.com 
# ------------------------------------------------------------------------
# author    Roland Soos 
# copyright Copyright (C) 2012 Offlajn.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.offlajn.com
-------------------------------------------------------------------------*/
?>
<?php
// no direct access
defined('_JEXEC') or die('Restricted access'); 
  
global $mosConfig_absolute_path, $VM_LANG, $database;
  
if(!defined('OfflajnVirtuemart1Menu')) {
  define("OfflajnVirtuemart1Menu", null);
  if( !isset( $mosConfig_absolute_path ) ) {
  	$mosConfig_absolute_path = $GLOBALS['mosConfig_absolute_path']	= JPATH_SITE;
  }
  if(!file_exists($mosConfig_absolute_path.'/components/com_virtuemart/virtuemart_parser.php')){
    echo JText::_("This component is not installed!");
    return;
  }
  require_once($mosConfig_absolute_path.'/components/com_virtuemart/virtuemart_parser.php');
  require_once($mosConfig_absolute_path."/administrator/components/com_virtuemart/classes/ps_product_category.php");
  require_once(dirname(__FILE__). DS .'..'. DS .'..'. DS .'core'. DS .'MenuBase.php');
  
  class OfflajnVirtuemart1Menu extends OfflajnMenuBase{
  
    function OfflajnVirtuemart1Menu($module, $params){
      parent::OfflajnMenuBase($module, $params);
    }
    
    function getAllItems(){
      $db = new ps_DB;
      $categoryid = $this->_params->get('categoryid');
      $query = "
        SELECT DISTINCT 
          a.category_id AS id, 
          a.category_name AS name, "; 
 
        if(!is_array($categoryid) && $categoryid != 0){
          $query.="IF(f.category_parent_id = ".$categoryid.", 0 , IF(f.category_parent_id = 0, -1, f.category_parent_id)) AS parent, ";
        }elseif(count($categoryid) && is_array($categoryid) && !in_array('0', $categoryid)){
          $query.="IF(a.category_id in (".implode(',', $categoryid)."), 0 , IF(f.category_parent_id = 0, -1, f.category_parent_id)) AS parent, ";
        }else{
          $query.="f.category_parent_id AS parent, ";
        }

        $query.="'cat' AS typ, 
          a.category_flypage,";
        if($this->_params->get('displaynumprod', 0) != 0){
          $query.= "(SELECT COUNT(*) FROM #__{vm}_product_category_xref AS ax LEFT JOIN #__{vm}_product AS bp ON ax.product_id = bp.product_id WHERE ax.category_id = a.category_id";
          if( CHECK_STOCK && PSHOP_SHOW_OUT_OF_STOCK_PRODUCTS != "1") {
        		$query.= " AND bp.product_in_stock > 0 ";
        	}
          $query.= ") AS productnum";
        }else{
          $query.= "0 AS productnum";
        }
        $query.= " FROM #__{vm}_category AS a, #__{vm}_category_xref AS f  
        WHERE a.category_publish='Y' AND a.category_id = f.category_child_id ";
        if ($this->_params->get('elementorder', 0) == 0)
          $query.="ORDER BY f.category_parent_id ASC, a.list_order ASC";
        else if($this->_params->get('elementorder', 0)==1)
          $query.="ORDER BY a.category_name ASC";
        else if($this->_params->get('elementorder', 0)==2)
          $query.="ORDER BY a.category_name DESC";  
      $db->setQuery($query);

      $allItems = $db->loadObjectList('id');
      
      /*
      Get products for the categories
      */
      if($this->_params->get('showproducts', 0)){
        $query = "
          SELECT DISTINCT 
            b.product_id, 
            concat(a.category_id,'-',a.product_id) AS id, 
            b.product_name AS name, 
            a.category_id AS parent, 
            'prod' AS typ,
            0 AS productnum
                  FROM #__{vm}_product_category_xref AS a
                  LEFT JOIN #__{vm}_product AS b ON a.product_id = b.product_id 
                  WHERE b.product_parent_id = 0 AND b.product_publish = 'Y'";
        if( CHECK_STOCK && PSHOP_SHOW_OUT_OF_STOCK_PRODUCTS != "1") {
      		$query.= " AND b.product_in_stock > 0 ";
      	}
        if($this->_params->get('elementorder', 0)==1)
          $query.=" ORDER BY name ASC";
        else 
          $query.=" ORDER BY name DESC"; 
        $db->setQuery($query);
        $allItems += $db->loadObjectList('id');
      }
      
      return $allItems;
    }
    
    function getActiveItem(){  
      $active = null;
      if(JRequest::getVar('option') == 'com_virtuemart'){
        $product_id = JRequest::getInt('product_id');
        $category_id = JRequest::getInt('category_id');
        if($product_id > 0 && $this->_params->get('showproducts')){
          if($category_id > 0){
            $active = new stdClass();
            $active->id = $category_id.'-'.$product_id;
          }else{
            require_once(CLASSPATH.'ps_product_category.php');
            $ps_product_category = new ps_product_category();
            $active = new stdClass();
            $active->id = $ps_product_category->get_cid($product_id).'-'.$product_id;
          }
        }else{
          if($category_id > 0){
            $active = new stdClass();
            $active->id = $category_id;
          }elseif($product_id > 0){
            $ps_product_category = new ps_product_category();
            $active = new stdClass();
            $active->id = $ps_product_category->get_cid($product_id);
          }
        }
      }
      return $active;
    }
    
    function getItemsTree(){
      $items = $this->getItems();
      if($this->_params->get('displaynumprod', 0) == 2){
        for($i = count($items)-1; $i >= 0; $i--){
            $items[$i]->parent->productnum+= $items[$i]->productnum;
        }
      }
      return $items;
    }
    
    function filterItem(&$item){
    	global $sess;
      $item->nname = stripslashes($item->name);
       $length = "";
      if (strlen($item->productnum) == 1 ) {
        $length = "one";
      } elseif(strlen($item->productnum) >= 2) {
        $length = "more";
      }
      if($this->_params->get('displaynumprod', 0) == 1 && $item->typ == 'cat' && $item->productnum > 0){
        $item->nname.= '<span class="productnum '.$length.'">'.$item->productnum.'</span>'; 
      }elseif($this->_params->get('displaynumprod', 0) == 2 && $item->typ == 'cat'){
        $item->nname.= '<span class="productnum '.$length.'">'.$item->productnum.'</span>'; 
      }
      
      if($this->_params->get('showdescasmenuimg', 0) == 1) {
        $item->nname = '<span>'.$item->description.$item->nname.'</span>';
      } else {
        $item->nname = '<span>'.$item->nname.'</span>';
      }    
      
      if($item->typ == 'cat'){
        if($this->_params->get('parentlink') == 0 && $item->p){
          $item->nname = '<a>'.$item->nname.'</a>';
        }else{
          $url = JRoute::_($sess->url('index.php?page=shop.browse&category_id='.$item->id));
          if(defined('DEMO') && strstr($url, 'Itemid') === false ){
            $url.='&Itemid='.$_REQUEST['Itemid'];
          }
          $item->nname = '<a href="'.$url.'">'.$item->nname.'</a>';
        }
      }elseif($item->typ == 'prod'){
        $ids = explode('-', $item->id);
        $url = JRoute::_($sess->url('index.php?page=shop.product_details&category_id='.$ids[0].'&flypage='.$item->parent->category_flypage .'&product_id='.$ids[1]));
        if(defined('DEMO') && strstr($url, 'Itemid') === false ){
          $url.='&Itemid='.$_REQUEST['Itemid'];
        }
        $item->nname = '<a href="'.$url.'">'.$item->nname.'</a>';
      }
    }
    
  }
}
?>