<?php

defined('JPATH_BASE') or die;

jimport('joomla.form.formfield');
jimport('joomla.form.fields.list');

class JFormFieldCombobox extends JFormFieldList
{
  var $_moduleName = '';
  
	protected $type = 'Combobox';

	protected function getInput()
	{
	  $options = array ();
		$options[] = JHTML::_('select.option', '', '');
		$options[] = JHTML::_('select.option', 'templatedefault', JText::_('Template default'));
		$options = array_merge($options, (array) $this->getOptions());
		
    return '<input type="text" name="'.$this->name.'" id="'.$this->id.'" value="'.$this->value.'" />'.
		    JHTML::_('select.genericlist',  $options, $this->id.'select', ' onchange="dojo.byId(\''.$this->id.'\').value = this.options[this.selectedIndex].value; this.selectedIndex=0;"', 'value', 'text', '', $this->id.'list');
	}
}
