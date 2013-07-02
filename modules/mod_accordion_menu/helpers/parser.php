<?php
/*-------------------------------------------------------------------------
# mod_accordion_menu - Accordion Menu - Offlajn.com
# -------------------------------------------------------------------------
# @ author    Roland Soos
# @ copyright Copyright (C) 2012 Offlajn.com  All Rights Reserved.
# @ license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @ website   http://www.offlajn.com
-------------------------------------------------------------------------*/
?><?php

if(!class_exists('OfflajnValueParser')){
  class OfflajnValueParser {  
    function parse($s, $concat = false){
      $v = explode("|*|", $s);
      for($i = 0; $i < count($v);$i++){
        if(strpos($v[$i] ,"||") !== false){
          if($concat === false)
            $v[$i] = explode("||", $v[$i]);
          else
            $v[$i] = str_replace("||",$concat, $v[$i]);
        }
      }
      if($v[count($v)-1] == '') unset($v[count($v)-1]);
      return count($v) == 1 ? $v[0] : $v;
    }
    
    function parseUnit($v, $concat = ''){
      if(!is_array($v)) $v = self::parse($v);
    	$unit = $v[count($v)-1];
    	unset($v[count($v)-1]);
    	$r = '';
    	foreach($v AS $m){
          $r.= $m.$unit.$concat;
    	}
    	return $r;
    }
    
    function parseBorder($s){
    	$v = self::parse($s);
    	return array(self::parseUnit(array_splice($v,0,5),' '), '#'.$v[0], $v[1]);
    }
    
    function parseColorizedImage($s){
      global $ImageHelper;
      $v = self::parse($s);
      $img = '';
      return array($img,$v[1]);
    }
  
  }
}

?>