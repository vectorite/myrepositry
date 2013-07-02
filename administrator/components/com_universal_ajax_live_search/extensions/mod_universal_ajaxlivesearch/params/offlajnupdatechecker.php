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
// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

error_reporting(E_ALL);
include_once(dirname(__FILE__).DS.'library'.DS.'fakeElementBase.php');

class JElementOfflajnupdatechecker extends JOfflajnFakeElementBase
{
  
	var	$_name = 'Offlajnupdatechecker';
  
	function universalfetchElement($name, $value, &$node){

    $document =& JFactory::getDocument();
    $document->addStyleSheet(JURI::base().'../modules/'.$this->_moduleName.'/params/css/offlajn.css');
    
    $xml = dirname(__FILE__).DS.'../'.$this->_moduleName.'.xml';
  	if(!file_exists($xml)){
      $xml = dirname(__FILE__).DS.'../install.xml';
      if(!file_exists($xml)){
        return;
      }
    }
    $xml = simplexml_load_file($xml);
    $hash = (string)$xml->hash;
    if($hash == '') return;
    
	  return '<iframe src="http://offlajn.com/index2.php?option=com_offlajn_update&hash='.base64_url_encode($hash).'&v='.$xml->version.'&u='.JURI::root().'" frameborder="no" style="border: 0;" width="100%" height="30"></iframe>';
	}
}


function base64_url_encode($input) {
 return strtr(base64_encode($input), '+/=', '-_,');
}

if(version_compare(JVERSION,'1.6.0','ge')) {
        class JFormFieldOfflajnupdatechecker extends JElementOfflajnupdatechecker {}
}

if (!function_exists('json_encode')){
  function json_encode($a=false){
    if (is_null($a)) return 'null';
    if ($a === false) return 'false';
    if ($a === true) return 'true';
    if (is_scalar($a)){
      if (is_float($a)){
        // Always use "." for floats.
        return floatval(str_replace(",", ".", strval($a)));
      }

      if (is_string($a))
      {
        static $jsonReplaces = array(array("\\", "/", "\n", "\t", "\r", "\b", "\f", '"'), array('\\\\', '\\/', '\\n', '\\t', '\\r', '\\b', '\\f', '\"'));
        return '"' . str_replace($jsonReplaces[0], $jsonReplaces[1], $a) . '"';
      }
      else
        return $a;
    }
    $isList = true;
    for ($i = 0, reset($a); $i < count($a); $i++, next($a)){
      if (key($a) !== $i){
        $isList = false;
        break;
      }
    }
    $result = array();
    if ($isList){
      foreach ($a as $v) $result[] = json_encode($v);
      return '[' . join(',', $result) . ']';
    }else{
      foreach ($a as $k => $v) $result[] = json_encode($k).':'.json_encode($v);
      return '{' . join(',', $result) . '}';
    }
  }
}