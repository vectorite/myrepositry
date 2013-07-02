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

if(!function_exists('offflat_array')){

  /* Multidimensional to flat array */
  function offflat_array($array){
    if(!is_array($array)) return array();
   $out=array();
   foreach($array as $k=>$v){
    if(is_array($array[$k]) && offisAssoc($array[$k])){
     $out+=offflat_array($array[$k]);
    }else{
     $out[$k]=$v;
    }
   }
   return $out;
  }
  
  function offisAssoc($arr)
  {
      return array_keys($arr) !== range(0, count($arr) - 1);
  }

}

?>