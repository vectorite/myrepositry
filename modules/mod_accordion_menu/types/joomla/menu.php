<?php 
/*------------------------------------------------------------------------
# mod_jo_accordion - Vertical Accordion Menu for Joomla 1.5 
# ------------------------------------------------------------------------
# author    Roland Soos 
# copyright Copyright (C) 2011 Offlajn.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.offlajn.com
-------------------------------------------------------------------------*/
?>
<?php
// no direct access
defined('_JEXEC') or die('Restricted access'); 
if(!defined('OfflajnJoomlaMenu')) {
  define("OfflajnJoomlaMenu", null);
  jimport('joomla.application.menu');
  jimport('joomla.html.parameter');
  
  require_once(dirname(__FILE__) . DS .'..'.DS.'..'.DS.'core'.DS.'MenuBase.php');

  class OfflajnJoomlaMenu extends OfflajnMenuBase{
    
    function OfflajnJoomlaMenu($module, $params){
      parent::OfflajnMenuBase($module, $params);
      $this->alias = array();
      if(version_compare(JVERSION,'1.6.0','ge')) {
        $this->parentName = 'parent_id';
        $this->name = 'title';
      }else{
        $this->parentName = 'parent';
        $this->name = 'name';
      }
    }
    
    function getAllItems(){
      $options = array();
      $menu =& JMenu::getInstance('site', $options);
      $items = $menu->getMenu();
      $keys = array_keys($items);
      $allItems = array();
      for($x = 0; $x < count($keys); $x++ ){
        $allItems[$keys[$x]] = clone($items[$keys[$x]]);
      }
      return $allItems;
    }
    
    function getActiveItem(){
      $options = array();
      $menu =& JMenu::getInstance('site', $options);
      return $menu->getActive();
    }
    
    function getItemsTree(){
      return $this->getItems();
    }
    
    function filterItems(){
      $this->helper = array();
      $user =& JFactory::getUser();
      if(version_compare(JVERSION,'1.6.0','ge')) {
        $aid = $user->getAuthorisedViewLevels();
      }else{
        $aid = $user->get('aid');
      }
      $menutype = $this->_params->get('joomlamenu');
      $ids = $this->_params->get('joomlamenutype');
      if(!is_array($ids) && is_string($ids)){
        $ids = array($ids);
      }
      
      if(!is_array($ids))
        $ids = array();

      if(!in_array(0, $ids) && count($ids) > 0){
        if(count($ids) == 1){
          $keys = array_keys($this->allItems);
          $newParent = $ids[0];
          for($x = 0; $x < count($keys); $x++ ){
            $el = &$this->allItems[$keys[$x]];
            if($el->{$this->parentName} == $newParent) $el->{$this->parentName} = version_compare(JVERSION,'1.6.0','ge') ? 1 : 0;
            elseif($el->{$this->parentName} == (version_compare(JVERSION,'1.6.0','ge') ? 1 : 0)) $el->{$this->parentName} = -1;
          }
        }else{
          $keys = array_keys($this->allItems);
          for($x = 0; $x < count($keys); $x++ ){
            $el = &$this->allItems[$keys[$x]];
            if(in_array($el->id, $ids)) $el->{$this->parentName} = version_compare(JVERSION,'1.6.0','ge') ? 1 : 0;
            elseif($el->{$this->parentName} == (version_compare(JVERSION,'1.6.0','ge') ? 1 : 0)) $el->{$this->parentName} = -1;
          }
        }
      }
      $keys = array_keys($this->allItems);
      for($x=0; $x < count($keys); $x++ ){
        $item = &$this->allItems[$keys[$x]];
        if (!is_object($item)) continue;
        $item->parent = version_compare(JVERSION,'1.6.0','ge') && $item->{$this->parentName} == 1 ? 0 : $item->{$this->parentName};
        version_compare(JVERSION,'1.6.0','ge') ? $item->ordering = $x : 0;
        if ($item->menutype == $menutype && (is_array($aid) ? in_array($item->access, $aid) : $item->access <= $aid) ){
          $item->p = false; // parent
          $item->fib = false; // First in Branch
          $item->lib = false; // Last in Branch
          if(!property_exists($item, 'opened')){
            if($this->opened == -1){
              $item->opened = true; // Opened
            }else{
              $item->opened = false; // Opened
            }
          }
          $item->active = false; // Active
          $this->helper[$item->parent][] = $item;
          $item->cparams = new JParameter($item->params);
          if($item->type == 'menulink' || $item->type == 'alias'){
            $itemid = version_compare(JVERSION,'1.6.0','ge') ?  $item->cparams->get('aliasoptions') : $item->cparams->get('menu_item');
            if(!isset($this->alias[$itemid]))
              $this->alias[$itemid] = $item->id;
          }
        }
      }
    }
    
        
    function filterItem(&$item){
      $item->cparams = new JParameter($item->params);
      if($item->type == 'menulink' || $item->type == 'alias'){
        $itemid = version_compare(JVERSION,'1.6.0','ge') ?  $item->cparams->get('aliasoptions') : $item->cparams->get('menu_item');
        if(isset($this->allItems[$itemid])){
          $newItem = $this->allItems[$itemid];
          $item->link = $newItem->link;
          $item->ttype = $newItem->type;
          $item->id = $newItem->id;
        }else{
          $item->ttype = 'separator';
        }
      }else{
        $item->ttype = $item->type;
      }
      $item->nname = '<span>'.$item->{$this->name}.'</span>';
      
      $image = '';
      
      $imgalign="";
      switch ($this->_params->get('menu_images_align', 0)){
  				case 0 : 
    				$imgalign="align='left'";
    				break;
  				case 1 :
    				$imgalign="align='right'";
    				break;
  				default :
    				$imgalign="";
    				break;
  		}
  		
      if ($this->_params->get('menu_images') && $item->cparams->get('menu_image') && $item->cparams->get('menu_image') != -1) {
        if(version_compare(JVERSION,'1.6.0','ge')){
          $image = '<img src="'.JURI::base(true)."/".$item->cparams->get('menu_image').'" '.$imgalign.' alt="'.$item->alias.'" />';
        }else{
          $image = '<img src="'.JURI::base(true).'/images/stories/'.$item->cparams->get('menu_image').'" '.$imgalign.' alt="'.$item->alias.'" />';
        }
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
  		
  		if($this->_params->get('parentlink') == 0 && $item->p){
        $item->ttype = 'separator';
      }
      
  		switch ($item->ttype){
  			case 'separator' :
  				$item->url = '';
  				return true;
  			case 'url' :
  				if ((strpos($item->link, 'index.php?') === 0) && (strpos($item->link, 'Itemid=') === false)) {
  					$item->url = $item->link.'&amp;Itemid='.$item->id;
  				} else {
  					$item->url = $item->link;
  				}
  				break;
  
  			default :
  				$router = JSite::getRouter();
  				$item->url = $router->getMode() == JROUTER_MODE_SEF ? 'index.php?Itemid='.$item->id : $item->link.'&Itemid='.$item->id;
  				break;
  		}
  		if ($item->url != ''){
  			// Handle SSL links
  			$iSecure = $item->cparams->def('secure', 0);
  			if ($item->home == 1) {
  				$item->url = JURI::base();
  			} elseif (strcasecmp(substr($item->url, 0, 4), 'http') && (strpos($item->link, 'index.php?') !== false)) {
  				$item->url = JRoute::_($item->url, true, $iSecure);
  			} else {
  				$item->url = str_replace('&', '&amp;', $item->url);
  			}
  
  			switch ($item->browserNav)
  			{
  				default:
  				case 0:
  					// _top
  					$item->nname = '<a href="'.$item->url.'">'.$item->nname.'</a>';
  					break;
  				case 1:
  					// _blank
  					$item->nname = '<a href="'.$item->url.'" target="_blank">'.$item->nname.'</a>';
  					break;
  				case 2:
  					// window.open
  					$attribs = 'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,'.$this->_params->get('window_open');
  					$link = str_replace('index.php', 'index2.php', $item->url);
  					$item->nname = '<a href="'.$link.'" onclick="window.open(this.href,\'targetWindow\',\''.$attribs.'\');return false;">'.$item->nname.'</a>';
  					break;
  			}
  		} else {
  			$item->nname = '<a>'.$item->nname.'</a>';
  		}
    }
    
    function menuOrdering(&$a, &$b){
      if ($a->ordering == $b->ordering) {
          return 0;
      }
      return ($a->ordering < $b->ordering) ? -1 : 1;
    }
  }
}
?>