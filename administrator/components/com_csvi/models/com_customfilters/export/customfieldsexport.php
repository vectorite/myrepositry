<?php
/**
 * Customfields table export class
 *
 * @package 	CSVI
 * @subpackage 	Export
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2012 RolandD Cyber Produksi
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: customfieldsexport.php 2052 2012-08-02 05:44:47Z RolandD $
 */

defined('_JEXEC') or die;

/**
 * Processor for custom exports
 *
 * @package 	CSVI
 * @subpackage 	Export
 */
class CsviModelCustomfieldsExport extends CsviModelExportfile {

	/**
	 * Customfields tables export
	 *
	 * Exports category details data to either csv, xml or HTML format
	 *
	 * @copyright
	 * @author		RolandD
	 * @todo
	 * @see
	 * @access 		public
	 * @param
	 * @return 		void
	 * @since 		3.0
	 */
	public function getStart() {
		// Get some basic data
		$db = JFactory::getDbo();
		$csvidb = new CsviDb();
		$jinput = JFactory::getApplication()->input;
		$csvilog = $jinput->get('csvilog', null, null);
		$template = $jinput->get('template', null, null);
		$exportclass =  $jinput->get('export.class', null, null);
		$export_fields = $jinput->get('export.fields', array(), 'array');
		

		// Build something fancy to only get the fieldnames the user wants
		$userfields = array();
		foreach ($export_fields as $column_id => $field) {
			switch ($field->field_name) {
				case 'id':
					$userfields[] = $db->quoteName('c').'.'.$db->quoteName('id');
					break;
				case 'custom_title':
					$userfields[] = $db->quoteName('c').'.'.$db->quoteName('vm_custom_id');
					break;
				case 'display_type':
					$userfields[] = $db->quoteName('c').'.'.$db->quoteName('type_id');
					$userfields[] = $db->quoteName('f').'.'.$db->quoteName('type').' AS '.$db->nameQuote('display_type');
					break;
				case 'custom':
					break;
				default:
					$userfields[] = $db->quoteName($field->field_name);
					break;
			}
		}

		// Export SQL Query
		// Build the query
		$userfields = array_unique($userfields);
		$query = $db->getQuery(true);
		$query->select(implode(",\n", $userfields));
		$query->from($db->quoteName("#__cf_customfields", "c"));
		$query->leftJoin($db->quoteName("#__cf_filtertypes", "f")." ON ".$db->quoteName("c").'.'.$db->quoteName("type_id").' = '.$db->quoteName("f").'.'.$db->quoteName("id"));

		// Ingore fields
		$ignore = array('custom', 'custom_title', 'display_type');
		
		// Check if we need to group the orders together
		$groupby = $template->get('groupby', 'general', false, 'bool');
		if ($groupby) {
			$filter = $this->getFilterBy('groupby', $ignore);
			if (!empty($filter)) $query->group($filter);
		}

		// Order by set field
		$orderby = $this->getFilterBy('sort', $ignore);
		if (!empty($orderby)) $query->order($orderby);

		// Add a limit if user wants us to
		$limits = $this->getExportLimit();

		// Execute the query
		$csvidb->setQuery($query, $limits['offset'], $limits['limit']);
		$csvilog->addDebug(JText::_('COM_CSVI_EXPORT_QUERY'), true);
		// There are no records, write SQL query to log
		if (!is_null($csvidb->getErrorMsg())) {
			$this->addExportContent(JText::sprintf('COM_CSVI_ERROR_RETRIEVING_DATA', $csvidb->getErrorMsg()));
			$this->writeOutput();
			$csvilog->AddStats('incorrect', $csvidb->getErrorMsg());
		}
		else {
			$logcount = $csvidb->getNumRows();
			$jinput->set('logcount', $logcount);
			if ($logcount > 0) {
				while ($record = $csvidb->getRow()) {
					if ($template->get('export_file', 'general') == 'xml' || $template->get('export_file', 'general') == 'html') $this->addExportContent($exportclass->NodeStart());
					foreach ($export_fields as $column_id => $field) {
						$fieldname = $field->field_name;
						$fieldreplace = $field->field_name.$field->column_header;
						// Add the replacement
						if (isset($record->$fieldname)) $fieldvalue = CsviHelper::replaceValue($field->replace, $record->$fieldname);
						else $fieldvalue = '';
						switch ($fieldname) {
							case 'custom_title':
								// Get the custom title
								$query = $db->getQuery(true);
								$query->select($db->quoteName('custom_title'));
								$query->from($db->quoteName('#__virtuemart_customs'));
								$query->where($db->quoteName('virtuemart_custom_id').' = '.$db->quote($record->vm_custom_id));
								$db->setQuery($query);
								$title = $db->loadResult();
								$fieldvalue = CsviHelper::replaceValue($field->replace, $title);
								if (strlen(trim($fieldvalue)) == 0) $fieldvalue = $field->default_value;
								$record->output[$column_id] = $fieldvalue;
								break;
							case 'custom':
								if (strlen(trim($fieldvalue)) == 0) $fieldvalue = $field->default_value;
								$fieldvalue = CsviHelper::replaceValue($field->replace, $fieldvalue);
								$record->output[$column_id] = $fieldvalue;
								break;
							default:
								// Check if we have any content otherwise use the default value
								if (strlen(trim($fieldvalue)) == 0) $fieldvalue = $field->default_value;
								$record->output[$column_id] = $fieldvalue;
								break;
						}
					}
					// Output the data
					$this->addExportFields($record);
					
					if ($template->get('export_file', 'general') == 'xml' || $template->get('export_file', 'general') == 'html') {
						$this->addExportContent($exportclass->NodeEnd());
					}

					// Output the contents
					$this->writeOutput();
				}
			}
			else {
				$this->addExportContent(JText::_('COM_CSVI_NO_DATA_FOUND'));
				// Output the contents
				$this->writeOutput();
			}
		}
	}
}
?>