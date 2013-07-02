<?php

defined('JPATH_BASE') or die;

jimport('joomla.form.formfield');

class JFormFieldGradient extends JFormField
{
  var $_moduleName = '';
  
	protected $type = 'Gradient';

	protected function getInput()
	{
		$size = 'size="12"';

    $value = htmlspecialchars(html_entity_decode($this->value, ENT_QUOTES), ENT_QUOTES);
    
    $document =& JFactory::getDocument();
    $document->addScript(JURI::base().'../modules/'.$this->_moduleName.'/params/colorpicker/jscolor.js');
   

    $GLOBALS['themescripts'][] = 'dojo.byId("'.$this->id.'start").picker = new jscolor.color(dojo.byId("'.$this->id.'start"), {});';
    $GLOBALS['themescripts'][] = 'dojo.byId("'.$this->id.'stop").picker = new jscolor.color(dojo.byId("'.$this->id.'stop"), {});';
    $GLOBALS['themescripts'][] = 'dojo.byId("'.$this->id.'stop").onchange();';
    
    $changeGradient="
      var startc = dojo.byId('".$this->id."start');
      var stopc = dojo.byId('".$this->id."stop');
      dojo.byId('".$this->id."').value = startc.value+'-'+stopc.value;
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
    $f = '<input onchange="var vs = this.value.split(\'-\'); dojo.byId(\''.$this->id.'start\').value = vs[0]; dojo.byId(\''.$this->id.'stop\').value = vs[1]; dojo.byId(\''.$this->id.'start\').onchange(); dojo.byId(\''.$this->id.'stop\').onchange();" type="hidden" name="'.$this->name.'" id="'.$this->id.'" value="'.$value.'"/>';
    $v = explode('-', $value);
    $f.= '<input onchange="'.$changeGradient.'" type="text" name="'.$this->id.'start" id="'.$this->id.'start" value="'.$v[0].'" class="color" '.$size.' />';
    $f.= '<input onchange="'.$changeGradient.'" type="text" name="'.$this->id.'stop" id="'.$this->id.'stop" value="'.$v[1].'" class="color" '.$size.' style="float:right;" /><br />';
    
		return $f;
	}
}
