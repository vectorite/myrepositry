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
// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

class JElementOfflajnupdatechecker extends JElement
{
  var $_moduleName = '';
  
	var	$_name = 'Offlajnupdatechecker';
	function fetchElement($name, $value, &$node, $control_name){
  	$module = $this->getModule();
  	$xml = dirname(__FILE__).DS.'../'.$module.'.xml';
  	if(!file_exists($xml)){
      $xml = dirname(__FILE__).DS.'../install.xml';
      if(!file_exists($xml)){
        return;
      }
    }
    $xml = simplexml_load_file($xml);
    $hash = (string)$xml->hash;
    if($hash == '') return;
	  return '<b style="float:left;line-height:60px;">'.$xml->version.'</b> <iframe style="float:right;" src="http://offlajn.com/index2.php?option=com_offlajn_update&hash='.base64_url_encode($hash).'&v='.$xml->version.'&u='.JURI::root().'" frameborder="no" style="border: 0;" width="90%" height="60"></iframe>';
	}
	
	function getModule(){
    $d = explode(DS, dirname(__FILE__));
    return $d[count($d)-2];
  }
}

function base64_url_encode($input) {
 return strtr(base64_encode($input), '+/=', '-_,');
}