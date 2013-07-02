<?php
/**
 * @version $Id$
 * @package    Contact_Enhanced
 * @author     Douglas Machado {@link http://ideal.fok.com.br}
 * @author     Created on 28-Jul-09
 * @license		GNU/GPL, see license.txt
 * Contact Enhanced  is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');

/**
 * Renders a SQL element
 *
 * @package 	Contact_Enhanced
 * @since		1.5.7.1
 */

class JFormFieldCESQL extends JFormField
{
	/**
	* Element name
	*
	* @access	protected
	* @var		string
	*/
	protected $type = 'CESQL';

	protected function getInput()
	{
		$db		= & JFactory::getDBO();
		$db->setQuery($this->element['query']);
		$key 	= ($this->element['key_field'] ? $this->element['key_field'] : 'value');
		$val 	= ($this->element['value_field'] ? $this->element['value_field'] : $name);
		$result	= $db->loadObjectList();

				
		$class		= $this->element['class'];
		if (!$class) {
			$class = "inputbox";
		}
		$attribs	= 'class="'.$class.'" ';
		$ctrl	= $this->name;

		if ($m = $this->element['multiple'])
		{
			$attribs	.= ' multiple="multiple" ';
			//$ctrl		.= '[]';
			$attribs	.= ' size="'.($this->element['size'] ? $this->element['size'] : '6').'" ';
		}else{
			$opt[0]	= new stdClass();
			$opt[0]->$key	= '';
			$opt[0]->$val	= JText::_('CE_PLEASE_SELECT_ONE') ;
			$result	= array_merge((array)$opt,$result);
		}
				
		return JHTML::_('select.genericlist', $result , $ctrl, $attribs, $key, $val, $this->value, $this->id);
	}
}
