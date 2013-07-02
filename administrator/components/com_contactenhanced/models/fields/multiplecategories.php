<?php
/**
 * @copyright	Copyright (C) 2006 - 2011 Ideal Custm software development. All rights reserved.
 * @author     Douglas Machado {@link http://ideal.fok.com.br}
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

jimport('joomla.form.formfield');
jimport('joomla.html.html.category');

/**
 * Supports a modal contact picker.
 *
 * @package		MooFAQ
* @since		1.6
 */
class JFormFieldMultiplecategories extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'Multiplecategories';

	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 * @since	1.6
	 */
	protected function getInput()
	{
		// Load the javascript and css
		JHtml::_('behavior.framework');

		$ctrl	= $this->name;
		$attribs	= ' ';

		if ($v = $this->element['size']) {
			$attribs	.= 'size="'.$v.'"';
		}
		if ($v = $this->element['class']) {
			$attribs	.= 'class="'.$v.'"';
		} else {
			$attribs	.= 'class="inputbox"';
		}
		if ($m = $this->element['multiple'])
		{
			$attribs	.= 'multiple="multiple"';
			$ctrl		.= '[]';
		}
		
		$options	= JHtml::_('category.options',$this->element['extension']);
			
		if (!$this->element['hide_none']) {
			array_unshift($options, JHtml::_('select.option', '', JText::_('JOPTION_DO_NOT_USE')));
		}
		// Merge any additional groups in the XML definition.
		$options = array_merge($this->getOptions(), $options);

		return JHtml::_(
			'select.genericlist',
			$options,
			$ctrl,
			array(
				//'id' => $control_name.$name,
				'list.attr' => $attribs,
				'list.select' => $this->value
			)
		);
	}
	
	/**
	 * Method to get the field options.
	 *
	 * @return	array	The field option objects.
	 * @since	1.6
	 */
	protected function getOptions()
	{
		// Initialize variables.
		$options = array();

		foreach ($this->element->children() as $option) {

			// Only add <option /> elements.
			if ($option->getName() != 'option') {
				continue;
			}

			// Create a new option object based on the <option /> element.
			$tmp = JHtml::_('select.option', (string) $option['value'], JText::_(trim((string) $option)), 'value', 'text', ((string) $option['disabled']=='true'));

			// Set some option attributes.
			$tmp->class = (string) $option['class'];

			// Set some JavaScript option attributes.
			$tmp->onclick = (string) $option['onclick'];

			// Add the option object to the result set.
			$options[] = $tmp;
		}

		reset($options);

		return $options;
	}
}
