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

class JElementGradient extends JOfflajnFakeElementBase
{
  var $_moduleName = '';
  
	var	$_name = 'Gradient';

	function universalfetchElement($name, $value, &$node)
	{
		$size = ( $node->attributes('size') ? 'size="'.$node->attributes('size').'"' : '' );

    $value = htmlspecialchars(html_entity_decode($value, ENT_QUOTES), ENT_QUOTES);
    
    $document =& JFactory::getDocument();
    $document->addScript(JURI::base().'../modules/'.$this->_moduleName.'/params/jpicker/jquery-1.4.4.min.js');
    $document->addScript(JURI::base().'../modules/'.$this->_moduleName.'/params/jpicker/jpicker-1.1.6.min.js');
    $document->addStyleSheet(JURI::base().'../modules/'.$this->_moduleName.'/params/jpicker/css/jPicker-1.1.6.min.css');
    
    $id = $this->generateId($name);
    
    $GLOBALS['themescripts'][] = 'jQuery(document).ready(function(){jQuery.fn.jPicker.defaults.images.clientPath="'.JURI::base().'../modules/'.$this->_moduleName.'/params/jpicker/images/";dojo.byId("'.$id.'start").alphaSupport=false; jQuery("#'.$id.'start").jPicker({window:{expandable: true,alphaSupport: false}});});';

    $GLOBALS['themescripts'][] = 'jQuery(document).ready(function(){jQuery.fn.jPicker.defaults.images.clientPath="'.JURI::base().'../modules/'.$this->_moduleName.'/params/jpicker/images/";dojo.byId("'.$id.'stop").alphaSupport=false; jQuery("#'.$id.'stop").jPicker({window:{expandable: true,alphaSupport: false}});});';
    
    $GLOBALS['themescripts'][] = 'dojo.byId("'.$id.'stop").onchange();';
    
    $changeGradient="
      var checkbox = dojo.byId('".$id."checkbox');
      if(checkbox.checked){
        dojo.style(checkbox.nextSibling,'display', 'block');
      }else{
        dojo.style(checkbox.nextSibling,'display', 'none');
      }
      var startc = dojo.byId('".$id."start');
      var stopc = dojo.byId('".$id."stop');
      dojo.byId('".$id."').value = (checkbox.checked ? '1-':'0-')+startc.value+'-'+stopc.value;
      if(dojo.isIE){
        dojo.style(startc.parentNode.parentNode, 'zoom', '1');
        var a = dojo.style(startc.parentNode.parentNode, 'filter', 'progid:DXImageTransform.Microsoft.Gradient(GradientType=1,StartColorStr=#'+startc.value+',EndColorStr=#'+stopc.value+')');
      }else if (dojo.isFF ) {
        dojo.style(startc.parentNode.parentNode, 'background', '-moz-linear-gradient( left, #'+startc.value+', #'+stopc.value+')');
      } else if (dojo.isMozilla ) {
        dojo.style(startc.parentNode.parentNode, 'background', '-moz-linear-gradient( left, #'+startc.value+', #'+stopc.value+')');
      } else if (dojo.isOpera ) {
        dojo.style(startc.parentNode.parentNode, 'background-image', '-o-linear-gradient(right, #'+startc.value+', #'+stopc.value+')');
      }else{
        dojo.style(startc.parentNode.parentNode, 'background', '-webkit-gradient( linear, left top, right top, from(#'+startc.value+'), to(#'+stopc.value+'))');
      }
      var e = startc;
      if(e.color){
        e.color.active.val('ahex', e.value);
      }
      e = stopc;
      if(e.color){
        e.color.active.val('ahex', e.value);
      }
      
    ";
    
    $changeGradient = str_replace(array("\n","\r"),'',$changeGradient);
    $v = explode('-', $value);
    $f = '<input id="'.$id.'checkbox" '.($v[0] == '1' ? 'checked="checked"':'').' onchange="'.$changeGradient.'" type="checkbox" name="a'.$name.'[checkbox]" style="height: 34px;margin:0 0 0 6px;float: left;" value="1">'; 
    $f.= '<div style="height: 24px;margin-left: 30px; padding: 5px;"><input onchange="var vs = this.value.split(\'-\');var cb = dojo.byId(\''.$id.'checkbox\'); if(vs[0] == 1){cb.checked = true;dojo.style(cb.nextSibling,\'display\', \'block\');}else{cb.checked = false;dojo.style(cb.nextSibling,\'display\', \'none\');} dojo.byId(\''.$id.'start\').value = vs[1]; dojo.byId(\''.$id.'stop\').value = vs[2]; dojo.byId(\''.$id.'start\').onchange(); dojo.byId(\''.$id.'stop\').onchange();" type="hidden" name="'.$name.'" id="'.$id.'" value="'.$value.'"/>';
    $f.= '<div style="float:left;"><input onchange="'.$changeGradient.'" type="text" name="a'.$name.'[start]" id="'.$id.'start" value="'.@$v[1].'" class="color" '.$size.' /></div>';
    $f.= '<div style="float:right;"><input onchange="'.$changeGradient.'" type="text" name="a'.$name.'[stop]" id="'.$id.'stop" value="'.@$v[2].'" class="color" '.$size.' /></div>';
    $f.= '<div style="clear: both;"></div></div><div style="clear: both;"></div>';
		return $f;
	}
}

if(version_compare(JVERSION,'1.6.0','ge')) {
  class JFormGradient extends JElementGradient {}
}