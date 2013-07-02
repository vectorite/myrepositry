<?php 
/*------------------------------------------------------------------------
# mod_ajax_dockcart - AJAX Dock Cart for VirtueMart 
# ------------------------------------------------------------------------
# author    Balint Polgarfi 
# copyright Copyright (C) 2011 Offlajn.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.offlajn.com
-------------------------------------------------------------------------*/
?>
<?php
// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

class JElementColor extends JElement
{
  var $_moduleName = '';
  
	var	$_name = 'Color';

	function fetchElement($name, $value, &$node, $control_name)
	{
	  $this->setModuleName();
		$size = 'size="12"';
    $value = htmlspecialchars(html_entity_decode($value, ENT_QUOTES), ENT_QUOTES);
    
    $document =& JFactory::getDocument();
    $document->addScript(JURI::base().'../modules/'.$this->_moduleName.'/params/colorpicker/jscolor.js');
    $GLOBALS['themescripts'][] = 'dojo.byId("'.$control_name.$name.'").picker = new jscolor.color(dojo.byId("'.$control_name.$name.'"), {});';
		return '<input onchange="this.picker.fromString(this.value);" type="text" name="'.$control_name.'['.$name.']" id="'.$control_name.$name.'" value="'.$value.'" class="color" '.$size.' />';
	}
	
	function setModuleName(){
    preg_match('/modules\\'.DS.'(.*?)\\'.DS.'/', $this->_parent->_elementPath[0], $matches); 
    $this->_moduleName = $matches[1];
  }
}