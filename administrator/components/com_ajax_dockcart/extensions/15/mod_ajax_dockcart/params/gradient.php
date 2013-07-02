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

class JElementGradient extends JElement
{
  var $_moduleName = '';
  
	var	$_name = 'Gradient';

	function fetchElement($name, $value, &$node, $control_name)
	{
	  $this->setModuleName();
		$size = ( $node->attributes('size') ? 'size="'.$node->attributes('size').'"' : '' );

    $value = htmlspecialchars(html_entity_decode($value, ENT_QUOTES), ENT_QUOTES);
    
    $document =& JFactory::getDocument();
    $document->addScript(JURI::base().'../modules/'.$this->_moduleName.'/params/colorpicker/jscolor.js');
   

    $GLOBALS['themescripts'][] = 'dojo.byId("'.$control_name.$name.'start").picker = new jscolor.color(dojo.byId("'.$control_name.$name.'start"), {});';
    $GLOBALS['themescripts'][] = 'dojo.byId("'.$control_name.$name.'stop").picker = new jscolor.color(dojo.byId("'.$control_name.$name.'stop"), {});';
    $GLOBALS['themescripts'][] = 'dojo.byId("'.$control_name.$name.'stop").onchange();';
    
    $changeGradient="
      var startc = dojo.byId('".$control_name.$name."start');
      var stopc = dojo.byId('".$control_name.$name."stop');
      dojo.byId('".$control_name.$name."').value = startc.value+'-'+stopc.value;
      if(dojo.isIE){
        dojo.style(startc.parentNode, 'zoom', '1');
        var a = dojo.style(startc.parentNode, 'filter', 'progid:DXImageTransform.Microsoft.Gradient(GradientType=1,StartColorStr=#'+startc.value+',EndColorStr=#'+stopc.value+')');
      }else if (dojo.isFF ) {
        dojo.style(startc.parentNode, 'background', '-moz-linear-gradient( left, #'+startc.value+', #'+stopc.value+')');
      } else if (dojo.isMozilla ) {
        dojo.style(startc.parentNode, 'background', '-moz-linear-gradient( left, #'+startc.value+', #'+stopc.value+')');
      } else if (dojo.isOpera ) {
        dojo.style(startc.parentNode, 'background-image', '-o-linear-gradient(right, #'+startc.value+', #'+stopc.value+')');
      }else{
        dojo.style(startc.parentNode, 'background', '-webkit-gradient( linear, left top, right top, from(#'+startc.value+'), to(#'+stopc.value+'))');
      }
      
      this.picker.fromString(this.value);
    ";
    
    $changeGradient = str_replace(array("\n","\r"),'',$changeGradient);
    $f = '<input onchange="var vs = this.value.split(\'-\'); dojo.byId(\''.$control_name.$name.'start\').value = vs[0]; dojo.byId(\''.$control_name.$name.'stop\').value = vs[1]; dojo.byId(\''.$control_name.$name.'start\').onchange(); dojo.byId(\''.$control_name.$name.'stop\').onchange();" type="hidden" name="'.$control_name.'['.$name.']" id="'.$control_name.$name.'" value="'.$value.'"/>';
    $v = explode('-', $value);
    $f.= '<input onchange="'.$changeGradient.'" type="text" name="'.$control_name.'['.$name.'start]" id="'.$control_name.$name.'start" value="'.$v[0].'" class="color" '.$size.' />';
    $f.= '<input onchange="'.$changeGradient.'" type="text" name="'.$control_name.'['.$name.'stop]" id="'.$control_name.$name.'stop" value="'.$v[1].'" class="color" '.$size.' style="float:right;" />';
    
		return $f;
	}
	
	function setModuleName(){
    preg_match('/modules\\'.DS.'(.*?)\\'.DS.'/', $this->_parent->_elementPath[0], $matches); 
    $this->_moduleName = $matches[1];
  }
}