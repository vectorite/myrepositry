<?php
// Doesn't work on Joomla 1.6.3
// defined('JPATH_PLATFORM') or die;
defined('JPATH_BASE') or die;

JFormHelper::loadFieldClass('list');

class JFormFieldFSQL extends JFormFieldList
{
	public $type = 'FSQL';

	protected function getOptions()
	{
/*
		(include_once JPATH_ROOT . "/components/com_foxcontact/helpers/flogger.php") or die(JText::sprintf("JLIB_FILESYSTEM_ERROR_READ_UNABLE_TO_OPEN_FILE", "flogger.php"));
		$log = new FLogger($this->type, "debug");
		$log->Write($this->element["name"] . " getOptions()");
*/
		// Initialize variables.
		$options = array();

		// Initialize some field attributes.
		$key = $this->element['key_field'] ? (string) $this->element['key_field'] : 'value';
		$value = $this->element['value_field'] ? (string) $this->element['value_field'] : (string) $this->element['name'];
		$query = (string) $this->element['query'];

		// Get the database object.
		$db = JFactory::getDBO();

		// Set the query and get the result list.
		$db->setQuery($query);
		$items = $db->loadObjectlist() or $items = new stdClass();

		foreach ($items as $item)
		{
			$options[] = JHtml::_('select.option', $item->$key, $item->$value);
		}

		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}
