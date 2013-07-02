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
if(!class_exists('OfflajnFontHelper')){
  if(!isset($GLOBALS['googlefontsloaded']))
    $GLOBALS['googlefontsloaded'] = array();
  
  class OfflajnFontHelper {
    function OfflajnFontHelper($params){
      $this->_params = &$params;
      $this->_parser = new OfflajnValueParser();
    }
    
    function parseFonts(){
      $imports = '';
      foreach($this->_params->toArray() AS $k => $f){
        if(strpos($k, 'font')!==false && isset($f[0]) && $f[0] == '{'){
          $f = json_decode($f, true);
  
          $tabs = array_keys($f);
          $default_tab = $tabs[0];
          $f['default_tab'] = $default_tab;
          
          $this->_params->setValue($k,$f);
          if(!isset($f[$default_tab]['bold'])) $f[$default_tab]['bold'] = 400;
          $weight = $f[$default_tab]['bold'] ? 700 : 400;
          if(!isset($f[$default_tab]['italic'])) $f[$default_tab]['italic'] = '';
          $italic = $f[$default_tab]['italic'] ? 'italic' : '';
          $subset = $this->_getSubset(isset($f[$default_tab]['subset']) ? $f[$default_tab]['subset'] : 'latin');
          foreach($f AS $k => $t){
            if($k == 'default_tab') continue;
            if(isset($t['type']) && $t['type'] == '0' || !isset($t['type']) && !isset($f[$default_tab])) continue;
          }
        }
      }
      return $imports;
    }
    
    /*
    Ha $loadDefaultTab true, akkor az aktuális tab hiányzó értékeibe beletölti a default tabból az értékeket.
    Ha a $justValue true, akkor csak az adott css tulajdonság értékét jeleníti meg.
    */
    
    function _printFont($name, $tab, $excl = null, $incl=null, $loadDefaultTab = false, $justValue = false){
      $f = $this->_params->get($name);
      if(!$tab) $tab = $f['default_tab']; 
      $t = $f[$tab];
      if($loadDefaultTab && $tab != $f['default_tab']){
        foreach($f[$f['default_tab']] AS $k => $v){
          if(!isset($t[$k])) $t[$k] = $v;
        }
      }
      $family = '';
      if(isset($t['type']) && $t['type'] != '0' && isset($t['family'])) $family = "'".$t['family']."'";
      if(isset($t['afont']) && $t['afont'] != ''){
        $afont = $this->_parser->parse($t['afont']);
        if($afont[1]){
          if($family != '') $family.= ',';
          $family.=$afont[0];
        }
      }
      if((!$excl || !in_array('font-family', $excl)) && (!$incl || in_array('font-family', $incl)))
        if($family != '') 
          if(!$justValue) echo 'font-family: '.$family.";\n";
            else echo $family;
      
      if((!$excl || !in_array('font-size', $excl)) && (!$incl || in_array('font-size', $incl)))
        if(isset($t['size']) && $t['size'] != '') 
          if(!$justValue) echo 'font-size: '.$this->_parser->parse($t['size'],'').";\n";
            else echo $this->_parser->parse($t['size'],'');
      
      if((!$excl || !in_array('color', $excl)) && (!$incl || in_array('color', $incl)))
        if(isset($t['color']) && $t['color'] != '') 
          if(!$justValue) echo 'color: #'.$t['color'].";\n";
            else echo '#'.$t['color'];
      
      if((!$excl || !in_array('font-weight', $excl)) && (!$incl || in_array('font-weight', $incl)))
        if(isset($t['bold'])) 
          if(!$justValue) echo 'font-weight: '.($t['bold'] == '1' ? 'bold' : 'normal').";\n";
            else echo ($t['bold'] == '1' ? 'bold' : 'normal');
      
      if((!$excl || !in_array('font-style', $excl)) && (!$incl || in_array('font-style', $incl)))
        if(isset($t['italic'])) 
          if(!$justValue) echo 'font-style: '.($t['italic'] == '1' ? 'italic' : 'normal').";\n";
            else echo ($t['italic'] == '1' ? 'italic' : 'normal');
      
      if((!$excl || !in_array('text-decoration', $excl)) && (!$incl || in_array('text-decoration', $incl)))
        if(isset($t['underline'])) 
          if(!$justValue) echo 'text-decoration: '.($t['underline'] == '1' ? 'underline' : 'none').";\n";
            else echo ($t['underline'] == '1' ? 'underline' : 'none');
      
      if((!$excl || !in_array('text-align', $excl)) && (!$incl || in_array('text-align', $incl)))
        if(isset($t['align'])) 
          if(!$justValue) echo 'text-align: '.$t['align'].";\n";
            else echo $t['align'];
      
      if((!$excl || !in_array('text-shadow', $excl)) && (!$incl || in_array('text-shadow', $incl)))
        echo isset($t['tshadow']) ? $this->getTextShadow($t['tshadow']) : '';
      
      if((!$excl || !in_array('line-height', $excl)) && (!$incl || in_array('line-height', $incl)))
        if(isset($t['lineheight'])) 
          if(!$justValue) echo 'line-height: '.$t['lineheight'].";\n";
            else echo $t['lineheight'];
    }
    
    function printFont($name, $tab, $loadDefaultTab = false){
      $this->_printFont($name, $tab, null, null, $loadDefaultTab);
    }
    
    function printFontExcl($name, $tab, $excl, $loadDefaultTab = false){
      $this->_printFont($name, $tab, $excl, null, $loadDefaultTab);
    }
    
    function printFontIncl($name, $tab, $incl, $loadDefaultTab = false){
      $this->_printFont($name, $tab, null, $incl, $loadDefaultTab);
    }
    
    function getTextShadow($s){
      $ts = $this->_parser->parse($s,'');
      if(!$ts[4]) return '';
      if (strlen($ts[3]) > 6) {
        preg_match('/(..)(..)(..)(..)/', $ts[3], $m);
        $ts[3] = 'rgba('.hexdec($m[1]).','.hexdec($m[2]).','.hexdec($m[3]).','.round(hexdec($m[4])/255.0, 2).')';
      } else $ts[3] = '#'.$ts[3];
      while(count($ts) > 4) array_pop($ts);
      return 'text-shadow: '.implode(' ',$ts).";\n";
    }
    
    function _getSubset($subset){
      if($subset == 'LatinExtended'){
        $subset = 'latin,latin-ext';
      }else if($subset == 'CyrillicExtended'){
        $subset = 'cyrillic,cyrillic-ext';
      }else if($subset == 'GreekExtended'){
        $subset = 'greek,greek-ext';
      }
      return $subset;
    }

    function printPropertyValue($name, $tab, $prop, $loadDefaultTab = false){
      $this->_printFont($name, $tab, null, array($prop), $loadDefaultTab, true);
    }
  }
}

?>