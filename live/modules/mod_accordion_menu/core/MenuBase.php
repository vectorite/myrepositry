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
defined( '_JEXEC' ) or die( 'Restricted access' ); 
 
if(!defined('OfflajnMenuBase')) {
  define("OfflajnMenuBase", null);

  class OfflajnMenuBase{
    
    var $_template;
    
    var $_module;
    
    var $_params;
    
    var $items;
    
    var $allItems;
    
    var $active;
    
    var $pointer;
    
    var $itemsCount;
    
    var $stack;
    
    var $level;
    
    var $endLevel;
    
    var $startLevel;
    
    var $improvedStartLevel;
    
    var $opened;
    
    var $openedlevels;
    
    function OfflajnMenuBase($module, $params){
      $this->_module = $module;
      $this->_params = $params;
      $this->endLevel = $params->get('endLevel', 1000);
      if($this->endLevel == 0) $this->endLevel = 1000;
      $this->startLevel = $params->get('startLevel', 0);
      $this->improvedStartLevel = $params->get('improvedstartlevel', 1);
      $this->opened = $params->get('opened', 2);
      $ol = $params->get('openedlevels', 0);
      if(!is_array($ol)){
        $ol = array($ol);
      }
      $this->openedlevels = array_flip($ol);
    }
    
    function generateItems(){
      $options = array();
      $cache = & JFactory::getCache();
      $cache->setCaching( $this->_params->get('caching', 0) );
      $this->allItems = $cache->call(array( $this, 'getAllItems' ));
      $this->active = &$this->getActiveItem();
      $this->items = $cache->call(array( $this, 'getItemsTree' ));
    }
    
  	function getItems(){
      /*
      If COOKIE tracking enabled
      */
      if($this->opened == 3){
        foreach($_COOKIE AS $k => $v){
          if($v == 1 && strpos($k, $this->_module->instanceid) !== false){
            $val = (int)str_replace($this->_module->instanceid.'-'.$this->_module->navClassPrefix, '', $k);
        //print_r($this->allItems[$val]);
            if($val > 0 && isset($this->allItems[$val]) ){
              $this->allItems[$val]->opened = true;
            }
          }
        }
      }
      
      $this->filterItems();
    	$root = 0;
  		if(isset($this->active)){
        if(@$this->alias[$this->active->id]){
          $this->active = & $this->allItems[$this->alias[$this->active->id]];
        }
        $i = $this->active->id;
  		  $stack = array($this->active->id);
  		  $el = $this->active;
        while($i > 0){
          $el = $this->allItems[$i];
          $i = $el->parent;
          $stack[] = $i;
        }
        $c = count($stack);
        if($c > 0){
          switch($this->_params->get('active', 1)){
            case 1:
              $this->allItems[$stack[0]]->active = true;
              break;
            case 2:
              foreach($stack AS $s){
                $this->allItems[$s]->active = true;
              }
              break;
          }
          
          switch($this->opened){
            case 1:
              $this->allItems[$stack[0]]->opened = true;
              break;
            case 2:
              foreach($stack AS $s){
                $this->allItems[$s]->opened = true;
              }
              break;
          }
        }
        
    		if($this->startLevel > 0){
          if($this->improvedStartLevel){
            while($this->startLevel != 0){
              if(isset($stack[$c-$this->startLevel-1]) && isset($this->helper[$stack[$c-$this->startLevel-1]])){
                $root = $stack[$c-$this->startLevel-1];
                break;
              }
              $this->startLevel--;
            }
          }else{
            $root = -1;
            if(isset($stack[$c-$this->startLevel-1])){
              $root = $stack[$c-$this->startLevel-1];
            }
          }
        }
      }
      
      $p = new stdClass();
      if($root > 0 && isset($this->allItems[$root])){
        $p = $this->allItems[$root];
      }else{
        $p->id = $root;
      }
  		return $this->getChilds($p, 1);
  	}
    
    function filterItems(){
  		$this->helper = array();
  		foreach ($this->allItems as $item){
  			if (!is_object($item)) continue;
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
  		}
    }
    
  	function getChilds(&$parent, $level){
  	  $items = array();
  	  if(isset($this->helper[$parent->id])){
        $helper = &$this->helper[$parent->id];
        //usort($helper, array($this, "menuOrdering")); // It can slow down the proccess. Not required every time... With this the process half as fast...
        $helper[0]->fib = true;
        $helper[count($helper)-1]->lib = true;
        if($level <= $this->endLevel){
          $i = 0;
          $keys = array_keys($helper);
          for($j = 0; $j < count($keys); $j++){
            $h = &$helper[$keys[$j]];
            $h->parent = &$parent;
            $childs =& $this->getChilds($h, $level+1);
            if(count($childs) > 0) $h->p = true;
            $h->level = $level;
            $items[] = &$h;
            $i = count($items);
            array_splice($items, $i, 0, $childs);
          }
        }
      }
      return $items;
    }
    
    function filterItem(&$item){
      $item->nname = '<span>'.stripslashes($item->name).'</span>';
    }
    
    function menuOrdering(&$a, &$b){
        return 0;
    }
  	
  	function render($template){
  	  $this->pointer = 0;
  	  $this->itemsCount = count($this->items);
  	  $this->_template = $template;
      $this->stack = array();
      $this->level = 1;
      $this->up = false;
      $this->renderItem();
      /*$level = 0;
      foreach($this->items as $item){
        include $template;
      }*/
    }
    
    function renderItem(){
      while($this->pointer < $this->itemsCount){
        $item =& $this->items[$this->pointer++];
        $this->filterItem($item);
        include $this->_template;
      }
      if($this->up){
        while($this->level > 1){
          echo "</dl></dd>";
          array_pop($this->stack);
          $this->level = count($this->stack);
        }
        $this->up = false;
      }
    }
  }
}
?>