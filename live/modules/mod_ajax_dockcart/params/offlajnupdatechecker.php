<?php 
/*------------------------------------------------------------------------
# mod_jo_accordion - Vertical Accordion Menu for Joomla 1.7 
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
jimport('joomla.form.formfield');

class JFormFieldOfflajnupdatechecker extends JFormField
{
  
	protected $type = 'Offlajnupdatechecker';

	protected function getInput(){
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
    return '<iframe src="http://offlajn.com/index2.php?option=com_offlajn_update&hash='.base64_url_encode($hash).'&v='.$xml->version.'&u='.JURI::root().'" frameborder="no" style="border: 0;" width="100%" height="30"></iframe>';
	}
	
	protected function getLabel(){
    return;
  }
	
	protected function getModule(){
    $d = explode(DS, dirname(__FILE__));
    return $d[count($d)-2];
  }
}

function base64_url_encode($input) {
 return strtr(base64_encode($input), '+/=', '-_,');
}