<?php
/**
 * Product type parameter export class
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2012 RolandD Cyber Produksi
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: producttypeparametersexport.php 1892 2012-02-11 11:01:09Z RolandD $
 */

defined( '_JEXEC' ) or die;
 
/**
 * Processor for product type parameter exports
 */
class CsviModelProductTypeParametersExport extends CsviModelExportfile {
	
	/**
	 * Product type parameters export
	 *
	 * Exports product type parameters data to either csv, xml or HTML format 
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
		$jinput = JFactory::getApplication()->input;
		$db = JFactory::getDbo();
		$csvidb = new CsviDb();
		$jinput = JFactory::getApplication()->input;
		$csvilog = $jinput->get('csvilog', null, null);
		$template = $jinput->get('template', null, null);
		$exportclass =  $jinput->get('export.class', null, null);
		$export_fields = $jinput->get('export.fields', array(), 'array');
		$ignore = array();
		
		// Build something fancy to only get the fieldnames the user wants
		$userfields = array();
		foreach ($export_fields as $column_id => $field) {
			switch ($field->field_name) {
				case 'product_type_id':
					$userfields[] = '#__vm_product_type.product_type_id';
					break;
				case 'custom':
					$ignore[] = $field->field_name;
					break;
				default:
					if ($field->process) {
						$userfields[] = $db->quoteName($field->field_name);
					}
					break;
			}
		}
		
		// Execute the query
		$userfields = array_unique($userfields);
		$query = $db->getQuery(true);
		$query->select(implode(",\n", $userfields));
		$query->from('#__vm_product_type_parameter')
			->leftJoin('#__vm_product_type ON #__vm_product_type_parameter.product_type_id = #__vm_product_type.product_type_id');
		
		// Check if we need to group the results together
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
				while ($record = $csvidb->getRow()) {
					if ($template->get('export_file', 'general') == 'xml' || $template->get('export_file', 'general') == 'html') $this->addExportContent($exportclass->NodeStart());
					
					// Process all the export fields
					foreach ($export_fields as $column_id => $field) {
						if ($field->process) {
							$fieldname = $field->field_name;
							
							// Add the replacement
							if (isset($record->$fieldname)) $fieldvalue = CsviHelper::replaceValue($field->replace, $record->$fieldname);
							else $fieldvalue = '';
							
							switch ($fieldname) {
								default:
									// Check if we have any content otherwise use the default value
									if (strlen(trim($fieldvalue)) == 0) $fieldvalue = $field->default_value;
									$record->output[$column_id] = $fieldvalue;
									break;
							}
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