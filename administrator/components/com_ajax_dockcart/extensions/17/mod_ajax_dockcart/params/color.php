<?php

defined('JPATH_BASE') or die;

jimport('joomla.form.formfield');

class JFormFieldColor extends JFormField
{
  var $_moduleName = '';
  
	protected $type = 'Color';

	protected function getInput()
	{
		$size = 'size="12"';
    $this->value = htmlspecialchars(html_entity_decode($this->value, ENT_QUOTES), ENT_QUOTES);

    $document =& JFactory::getDocument();
    $document->addScript(JURI::base().'../modules/'.$this->_moduleName.'/params/colorpicker/jscolor.js');
    
    $GLOBALS['themescripts'][] = 'dojo.byId("'.$this->id.'").picker = new jscolor.color(dojo.byId("'.$this->id.'"), {});';


		return '<input onchange="this.picker.fromString(this.value);" type="text" name="'.$this->name.'" id="'.$this->id.'"' .
				' value="'.htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8').'"' .
				$size.'/>';
	}
}
