<?php
defined('_JEXEC') or die('Restricted access');

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