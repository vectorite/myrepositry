<?php 
/*------------------------------------------------------------------------
# smartslider - Smart Slider
# ------------------------------------------------------------------------
# author    Roland Soos 
# copyright Copyright (C) 2011 Offlajn.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.offlajn.com
-------------------------------------------------------------------------*/
?>
<?php 
defined('_JEXEC') or die('Restricted access');

class JElementColor extends JOfflajnFakeElementBase
{
  var $_moduleName = '';
  
	var	$_name = 'Color';

	function universalfetchElement($name, $value, &$node)
	{
		$size = 'size="12"';
    $value = htmlspecialchars(html_entity_decode($value, ENT_QUOTES), ENT_QUOTES);
    $id = $this->generateId($name);
    
    $alpha = $node->attributes('alpha') == 1 ? true : false;
    $document =& JFactory::getDocument();
    $document->addScript(JURI::base().'../modules/'.$this->_moduleName.'/params/jpicker/jquery-1.4.4.min.js');
    $document->addScript(JURI::base().'../modules/'.$this->_moduleName.'/params/jpicker/jpicker-1.1.6.min.js');
    $document->addStyleSheet(JURI::base().'../modules/'.$this->_moduleName.'/params/jpicker/css/jPicker-1.1.6.min.css');
    if(version_compare(JVERSION,'1.6.0','>=')) {
      $document->addStyleDeclaration('.jPicker{float:left;}');
    }
    $GLOBALS['themescripts'][] = 'jQuery(document).ready(function(){jQuery.fn.jPicker.defaults.images.clientPath="'.JURI::base().'../modules/'.$this->_moduleName.'/params/jpicker/images/";dojo.byId("'.$id.'").alphaSupport='.($alpha ? 'true' : 'false').'; jQuery("#'.$id.'").jPicker({window:{expandable: true,alphaSupport: '.($alpha ? 'true' : 'false').'}});});';
		return '<input style="padding: 2px 0;" type="text" name="'.$name.'" id="'.$id.'" value="'.$value.'" class="color" '.$size.' />';
	}
	
}

if(version_compare(JVERSION,'1.6.0','ge')) {
  class JFormFieldColor extends JElementColor {}
}