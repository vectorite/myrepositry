<?php
/**
 * Shopperfields export class
 *
 * @package 	CSVI
 * @subpackage 	Export
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2012 RolandD Cyber Produksi
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: shopperfieldexport.php 2061 2012-08-07 09:20:26Z RolandD $
 */

defined('_JEXEC') or die;

/**
 * Processor for userfields
 *
 * @package 	CSVI
 * @subpackage 	Export
 */
class CsviModelShopperfieldExport extends CsviModelExportfile {

	/**
	 * Userfields export
	 *
	 * Exports puserfields data to either csv, xml or HTML format
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
				// Man made fields, do not export them
				case 'custom':
					break;
				default:
					$userfields[] = $db->quoteName($field->field_name);
					break;
			}
		}

		/** Export SQL Query
		 * Get all products - including items
		 * as well as products without a price
		 */
		$userfields = array_unique($userfields);
		$query = $db->getQuery(true);
		$query->select(implode(",\n", $userfields));
		$query->from('#__virtuemart_userfields');
		
		// Check if there are any selectors
		$selectors = array();
		
		// Filter by published state
		$publish_state = $template->get('publish_state', 'general');
		if ($publish_state !== '' && ($publish_state == 1 || $publish_state == 0)) {
			$selectors[] = '#__virtuemart_userfields.published = '.$publish_state;
		}
		
		// Check if we need to attach any selectors to the query
		if (count($selectors) > 0 ) $query->where(implode("\n AND ", $selectors));
		
		// Ignore some fields
		$ignore = array('custom');
		
		// Check if we need to group the orders together
		$groupby = $template->get('groupby', 'general', false, 'bool');
		if ($groupby) {
			$filter = $this->getFilterBy('groupby', $ignore);
			if (!empty($filter)) $query->group($filter);
		}

		// Order by set field
		$orderby = $this->getFilterBy('sort', $ignore);
		if (!empty($orderby)) $query->order($orderby);

		// Add export limits
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
				$linenumber = 1;
				while ($record = $csvidb->getRow()) {
					$csvilog->setLinenumber($linenumber++);
					if ($template->get('export_file', 'general') == 'xml' || $template->get('export_file', 'general') == 'html') $this->addExportContent($exportclass->NodeStart());
					foreach ($export_fields as $column_id => $field) {
						$fieldname = $field->field_name;
						// Add the replacement
						if (isset($record->$fieldname)) $fieldvalue = CsviHelper::replaceValue($field->replace, $record->$fieldname);
						else $fieldvalue = '';
						switch ($fieldname) {
							case 'created_on':
							case 'modified_on':
							case 'locked_on':
								$date = JFactory::getDate($record->$fieldname);
								$fieldvalue = CsviHelper::replaceValue($field->replace, date($template->get('export_date_format', 'general'), $date->toUnix()));
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