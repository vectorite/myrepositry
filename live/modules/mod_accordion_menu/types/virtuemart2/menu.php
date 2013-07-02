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
  
if(!defined('OfflajnVirtuemart2Menu')) {
  define("OfflajnVirtuemart2Menu", null);
  
  if (!class_exists( 'VmConfig' )) require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart'.DS.'helpers'.DS.'config.php');
  $config= VmConfig::loadConfig();
  if(!class_exists('TableCategories')) require(JPATH_VM_ADMINISTRATOR.DS.'tables'.DS.'categories.php');
  if (!class_exists( 'VirtueMartModelCategory' )) require(JPATH_VM_ADMINISTRATOR.DS.'models'.DS.'category.php');
  if (!class_exists( 'VirtueMartModelProduct' )) require(JPATH_VM_ADMINISTRATOR.DS.'models'.DS.'product.php');
  
  require_once(dirname(__FILE__). DS .'..'. DS .'..'. DS .'core'. DS .'MenuBase.php');
  
  class OfflajnVirtuemart2Menu extends OfflajnMenuBase{
  
    function OfflajnVirtuemart2Menu($module, $params){
      parent::OfflajnMenuBase($module, $params);
    }
    
    function getAllItems(){
      $options = array();
      
      $db = & JFactory::getDBO();
      
      $categoryid = $this->_params->get('categoryid');
      
      $query = "SELECT DISTINCT 
        a.virtuemart_category_id AS id, 
        a.category_description  AS description, 
        a.category_name AS name, ";
         
      if(!is_array($categoryid) && $categoryid != 0){
        $query.="IF(f.category_parent_id = ".$categoryid.", 0 , IF(f.category_parent_id = 0, -1, f.category_parent_id)) AS parent, ";
      }elseif(count($categoryid) && is_array($categoryid) && !in_array('0', $categoryid)){
        $query.="IF(a.virtuemart_category_id in (".implode(',', $categoryid)."), 0 , IF(f.category_parent_id = 0, -1, f.category_parent_id)) AS parent, ";
      }else{
        $query.="f.category_parent_id AS parent, ";
      }
      
      $query.="'cat' AS typ, ";
        if($this->_params->get('displaynumprod', 0) != 0){
          $query.= "(SELECT COUNT(*) FROM #__virtuemart_product_categories AS ax LEFT JOIN #__virtuemart_products AS bp ON ax.virtuemart_product_id = bp.virtuemart_product_id WHERE ax.virtuemart_category_id = a.virtuemart_category_id";
          if( VmConfig::get('check_stock') && Vmconfig::get('show_out_of_stock_products') != '1') {
        		$query.= " AND bp.product_in_stock > 0 ";
        	}
          $query.= ") AS productnum";
        }else{
          $query.= "0 AS productnum";
        }
        $query.= " FROM #__virtuemart_categories_".VMLANG." AS a
                LEFT JOIN #__virtuemart_category_categories AS f ON a.virtuemart_category_id = f.category_child_id
                LEFT JOIN #__virtuemart_categories AS b ON a.virtuemart_category_id = b.virtuemart_category_id
                WHERE b.published='1' AND a.virtuemart_category_id = f.category_child_id ";
        if ($this->_params->get('elementorder', 0) == 0)
          $query.="ORDER BY b.ordering ASC";
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
            a.virtuemart_product_id, 
            concat(a.virtuemart_category_id,'-',a.virtuemart_product_id) AS id, 
            c.product_name AS name, 
            a.virtuemart_category_id AS parent, 
            'prod' AS typ,
            0 AS productnum
                  FROM #__virtuemart_product_categories AS a
                  LEFT JOIN #__virtuemart_products AS b ON a.virtuemart_product_id = b.virtuemart_product_id 
                  LEFT JOIN #__virtuemart_products_".VMLANG." AS c ON a.virtuemart_product_id = c.virtuemart_product_id
                   
                  WHERE b.product_parent_id = 0 AND b.published = '1'";
          if( VmConfig::get('check_stock') && Vmconfig::get('show_out_of_stock_products') != '1') {
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
        $product_id = JRequest::getInt('virtuemart_product_id');
        $category_id = JRequest::getInt('virtuemart_category_id');
        if($product_id > 0 && $this->_params->get('showproducts')){
          if($category_id > 0){
            $active = new stdClass();
            $active->id = $category_id.'-'.$product_id;
          }else{
            $active = new stdClass();
            $productModel = new VirtueMartModelProduct();
            $r = $productModel->getProductSingle($product_id)->categories;
            if(is_array($r)){
              $r = $r[0];
            }
            $active->id = $r.'-'.$product_id;
          }
        }else{
          if($category_id > 0){
            $active = new stdClass();
            $active->id = $category_id;
          }elseif($product_id > 0){
            $active = new stdClass();
            $productModel = new VirtueMartModelProduct();
            $r = $productModel->getProductSingle($product_id)->categories;
            if(is_array($r)){
              $r = $r[0];
            }
            $active->id = $r;
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
      
      $item->nname = '<span>'.$item->nname.'</span>';
      
      $image = '';
      if ($this->_params->get('menu_images') && $item->description != '') {
        preg_match('/<img.*?src=["\'](.*?((jpg)|(png)|(jpeg)))["\'].*?>/i',$item->description, $out);
        $image = '<img src="'.JURI::base(true).$out[1].'" '.$imgalign.' />';
  			if($this->_params->get('menu_images_link')){
  			  $item->nname = null;
        }
        
        switch ($this->_params->get('menu_images_align', 0)){
  				case 0 : 
    				$item->nname = $image.$item->nname;
    				break;
  				case 1 :
    				$item->nname = $item->nname.$image;
    				break;
  				default :
    				$item->nname = $image.$item->nname;
    				break;
  			}
  		}
      
      if($item->typ == 'cat'){
        if($this->_params->get('parentlink') == 0 && $item->p){
          $item->nname = '<a>'.$item->nname.'</a>';
        }else{
          $url = JRoute::_('index.php?option=com_virtuemart&view=category&virtuemart_category_id='.$item->id);
          if(defined('DEMO') && strstr($url, 'Itemid') === false ){
            $url.='&Itemid='.$_REQUEST['Itemid'];
          }
          $item->nname = '<a href="'.$url.'">'.$item->nname.'</a>';
        }
      }elseif($item->typ == 'prod'){
        $ids = explode('-', $item->id);
        $url = JRoute::_('index.php?option=com_virtuemart&view=productdetails&virtuemart_category_id='.$ids[0].'&virtuemart_product_id='.$ids[1]);
        if(defined('DEMO') && strstr($url, 'Itemid') === false ){
          $url.='&Itemid='.$_REQUEST['Itemid'];
        }
        $item->nname = '<a href="'.$url.'">'.$item->nname.'</a>';
      }
    }
    
  }
}
?>