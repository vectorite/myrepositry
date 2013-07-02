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

class JElementOfflajnMultiSelectList extends JOfflajnFakeElementBase {

 var $_name = 'OfflajnMultiSelectList';
  
  function universalfetchElement($name, $value, &$node) {
    $attrs = $node->attributes();
    $this->allitem = 1;
    $html = "";
    $height = isset($attrs['height']) ? $attrs['height'] : '10';
    $this->loadFiles('offlajnmultiselectlist');
    $this->name = $this->generateId($name);
    $this->ids = array();
    $this->options = array();
    $this->key = "";
    $items = $this->getItems($node);
    $this->keys = array();
    $this->tnum = 0;
      $divs = $this->makeItemDivsExtended($items);
    $joomlaMenu = 1;
    if($this->tnum == 1) {
      $joomlaMenu = 0;
      $menuType = $this->key;
    } else {
      (in_array($value, $this->keys)) ? $menuType = $value : $menuType = $this->key;
    }
    $scroller = 0;
    $number = 0;
    $html .= '<div id="offlajnmultiselect'.$this->name.'" class="gk_hack offlajnmultiselect" style="height: '.$height.'px">';
    $html .= '</div>'; 
    (!isset($value)) ? $value = 0 : $value = $value;
    $html .= '<input type="hidden" id="'.$this->generateId($name).'" name="'.$name.'" value="'.$value.'"/>';
    DojoLoader::addScript('new OfflajnMultiSelectList({
      name: '.json_encode($this->name).',
      height: '.json_encode($height).',
      type: '.json_encode($menuType).',
      data: '.json_encode($divs).',
      options: '.json_encode($this->options).',
      joomla: '.json_encode($joomlaMenu).',
      ids: '.json_encode($this->ids).',
      mode: '.(isset($attrs['mode']) ? $attrs['mode'] : 1).'
    });');
    return $html;
  }
    
  function makeItemDivsExtended($items) {
    $_temp = array();
    $menuTypes = array();
    $data = array();
    $div = "";
   	foreach ($items as $k => $type){
      $div = "";		
      if (isset( $items[$k] )){
				$n = count( $items[$k] );
				if($this->allitem) {
				  $div .= '<div class="gk_hack multiselectitem">All items</div>';
				  $this->ids[$k][0] = 0;
				}
				for ($i = 0; $i < $n; $i++) {
					$item = $items[$k][$i];
					$this->ids[$k][$i] = $item->id;
          $itemname = version_compare(JVERSION,'1.6.0','ge') ? str_replace("&#160;",'-',( $item->treename)) : $item->treename;
          $div .= '<div class="gk_hack multiselectitem">'.$itemname.'</div>';     
				} 
        $data[$k] = $div;
			}
		  $this->key = $k;
      $this->tnum++;
    }
		return $data;
  }
  
  function getItems(&$node) {
   	$list = array();
  	$this->key = 'simple';
  	$i = 0;
    foreach ($node->children() as $option) {
  		$val	= (!method_exists($option, 'getAttribute') ? $option->attributes('value') : $option->getAttribute('value'));
  		$text	= $option->data();
  		$this->ids[$this->key][] = $val;
  		$this->options[$i]['value'] = $val;
  		$this->options[$i]['text'] = JTEXT::_($text);
  		$list[$i] = new stdClass();
  		$list[$i]->id = $val;
  		$list[$i]->treename = JTEXT::_($text);
  		$i++;
  	}
		$n = count( $list );
		$groupedList = array();
		foreach ($list as $k => $v) {
			@$groupedList[$this->key][] = &$list[$k];
		}
		$this->allitem = 0;
    return $groupedList;
  }
}

if(version_compare(JVERSION,'1.6.0','ge')) {
  class JFormFieldOfflajnMultiSelectList extends JElementOfflajnMultiSelectList {}
}

