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
class JFormFieldCecontactfields extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'Cecontactfields';

	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 * @since	1.6
	 */
	protected function getInput()
	{
		
		$lang	= JFactory::getLanguage();
		$lang->load('com_contactenhanced',JPATH_ADMINISTRATOR);
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
		
		
		$options	= array();
		$options[] = ( JHtml::_('select.option', 'name', 			JText::_('COM_CONTACTENHANCED_FIELD_NAME_LABEL')));
		$options[] = ( JHtml::_('select.option', 'con_position',	JText::_('COM_CONTACTENHANCED_FIELD_INFORMATION_POSITION_LABEL')));
		$options[] = ( JHtml::_('select.option', 'address',			JText::_('COM_CONTACTENHANCED_FIELD_INFORMATION_ADDRESS_LABEL')));
		$options[] = ( JHtml::_('select.option', 'suburb',			JText::_('COM_CONTACTENHANCED_FIELD_INFORMATION_SUBURB_LABEL')));
		$options[] = ( JHtml::_('select.option', 'state',			JText::_('COM_CONTACTENHANCED_FIELD_INFORMATION_STATE_LABEL')));
		$options[] = ( JHtml::_('select.option', 'postcode',		JText::_('COM_CONTACTENHANCED_FIELD_INFORMATION_POSTCODE_LABEL')));
		$options[] = ( JHtml::_('select.option', 'country',			JText::_('COM_CONTACTENHANCED_FIELD_INFORMATION_COUNTRY_LABEL')));
		$options[] = ( JHtml::_('select.option', 'telephone',		JText::_('COM_CONTACTENHANCED_FIELD_INFORMATION_TELEPHONE_LABEL')));
		$options[] = ( JHtml::_('select.option', 'mobile',			JText::_('COM_CONTACTENHANCED_FIELD_INFORMATION_MOBILE_LABEL')));
		$options[] = ( JHtml::_('select.option', 'fax',				JText::_('COM_CONTACTENHANCED_FIELD_INFORMATION_FAX_LABEL')));
		$options[] = ( JHtml::_('select.option', 'skype',			JText::_('COM_CONTACTENHANCED_FIELD_INFORMATION_SKYPE_LABEL')));
		$options[] = ( JHtml::_('select.option', 'webpage',			JText::_('COM_CONTACTENHANCED_FIELD_INFORMATION_WEBPAGE_LABEL')));
		$options[] = ( JHtml::_('select.option', 'misc',			JText::_('COM_CONTACTENHANCED_FIELD_INFORMATION_MISC_LABEL')));
		$options[] = ( JHtml::_('select.option', 'sidebar',			JText::_('COM_CONTACTENHANCED_FIELD_INFORMATION_SIDEBAR_LABEL')));
		$options[] = ( JHtml::_('select.option', 'metakey',			JText::_('JFIELD_META_KEYWORDS_LABEL')));
		$options[] = ( JHtml::_('select.option', 'metadesc',		JText::_('JFIELD_META_DESCRIPTION_LABEL')));
		$options[] = ( JHtml::_('select.option', 'extra_field_1',	JText::_('CE_CONTACT_EF1')));
		$options[] = ( JHtml::_('select.option', 'extra_field_2',	JText::_('CE_CONTACT_EF2')));
		$options[] = ( JHtml::_('select.option', 'extra_field_3',	JText::_('CE_CONTACT_EF3')));
		$options[] = ( JHtml::_('select.option', 'extra_field_4',	JText::_('CE_CONTACT_EF4')));
		$options[] = ( JHtml::_('select.option', 'extra_field_5',	JText::_('CE_CONTACT_EF5')));
		$options[] = ( JHtml::_('select.option', 'extra_field_6',	JText::_('CE_CONTACT_EF6')));
		$options[] = ( JHtml::_('select.option', 'extra_field_7',	JText::_('CE_CONTACT_EF7')));
		$options[] = ( JHtml::_('select.option', 'extra_field_8',	JText::_('CE_CONTACT_EF8')));
		$options[] = ( JHtml::_('select.option', 'extra_field_9',	JText::_('CE_CONTACT_EF9')));
		$options[] = ( JHtml::_('select.option', 'extra_field_10',	JText::_('CE_CONTACT_EF10')));
		
		//Sort an array;
		//asort($options);
		
		if (!$this->element['hide_all']) {
			array_unshift($options, JHtml::_('select.option', 'all', JText::_('JALL')));
		}	
		
		
		// Merge any additional groups in the XML definition.
		$options = array_merge($this->getOptions(), $options);
	//	echo '<pre>'; print_r($this->getOptions()); exit;
		
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
