<?php
/**
 * Product type names export class
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2012 RolandD Cyber Produksi
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: producttypenamesexport.php 2043 2012-07-20 19:49:55Z RolandD $
 */

defined( '_JEXEC' ) or die;

/**
 * Processor for product type names exports
 */

class CsviModelProductTypenamesExport extends CsviModelExportfile {

	/**
	 * Product type names export
	 *
	 * Exports product type names data to either csv, xml or HTML format
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
		$producttypeid = $template->get('producttypenames', 'producttypename', false);
		
		// Create ID list for searching
		$typeids = array();
		foreach ($producttypeid as $key => $value) {
			$typeids[] = $db->quote('vm_product_type_'.$value);
		}
		$component_tables = implode(',', $typeids);
		
		// Build something fancy to only get the fieldnames the user wants
		$userfields = array();
		$vmtables = array();
		$vmids = array();
		$ignore = array();

		foreach ($export_fields as $column_id => $field) {
			switch ($field->field_name) {
				case 'product_sku':
					$userfields[] = '#__virtuemart_products.product_sku';
					break;
				case 'product_id':
					$userfields[] = '#__virtuemart_products.virtuemart_product_id AS product_id';
					break;
				case 'product_type_name':
					$userfields[] = '#__vm_product_type.product_type_name';
					break;
				case 'product_type_id':
					$userfields[] = '#__vm_product_type.product_type_id';
					break;
				// Man made fields, do not export them
				case 'custom':
					$ignore[] = $field->field_name;
					break;
				default:
					// Check which product type table belongs to the field
					$table = null;
					$query = $db->getQuery(true);
					$query->select('component_table')->from('#__csvi_available_fields')->where('csvi_name = '.$db->quote($field->field_name))->where('component_table IN ('.$component_tables.')');
					$db->setQuery($query, 0, 1);
					$table = $db->loadResult();
					if ($table) {
						$vmtables[$table][] = $field->field_name;
						$vmids[] = $table;
					}
					else $ignore[] = $field->field_name; 
					break;
			}
		}

		// Check if we have any product type tables to export
		$vmids = array_unique($vmids);

		if (!empty($vmids)) {
			/**
			 * Export SQL Query
			 * Get all products - including items as well as products without a price
			 */
			$queries = array();
			$filterid = '';
			$userfields = array_unique($userfields);
			foreach ($vmids AS $vmidkey => $vmid) {
				$q = "(SELECT ".implode(",\n", $userfields);
				foreach ($vmtables as $vmtableskey => $vmfields) {
					if ($vmid == $vmtableskey) {
						$filterid = str_replace('vm_product_type_', '', $vmid);
						foreach ($vmfields AS $vmfieldkey => $vmfield) {
							$q .= ",\n".$db->nameQuote('#__'.$vmid).'.'.$db->nameQuote($vmfield).' AS '.$db->nameQuote($vmfield);
						}
					}
					else {
						foreach ($vmfields AS $vmfieldkey => $vmfield) {
							$q .= ",\n '' AS ".$db->nameQuote($vmfield);
						}
					}
				}
				$q .= ' FROM #__vm_product_type
					LEFT JOIN #__vm_product_product_type_xref
					ON #__vm_product_product_type_xref.product_type_id = #__vm_product_type.product_type_id
					LEFT JOIN #__virtuemart_products
					ON #__vm_product_product_type_xref.product_id = #__virtuemart_products.virtuemart_product_id ';

				// Add the product type X tables
				$q .= "\nLEFT JOIN #__".$vmid." ON #__".$vmid.".product_id = #__virtuemart_products.virtuemart_product_id "."\n";

				// Check if there are any selectors
				$selectors = array();

				// Add product type ID checks
				if (is_int($filterid)) $selectors[] = '#__vm_product_type.product_type_id = '.$filterid;

				// Filter by product type name
				if ($producttypeid) {
					$selectors[] = '#__vm_product_type.product_type_id IN ('.implode(',', $producttypeid).')';
				}

				// Check if we need to attach any selectors to the query
				if (count($selectors) > 0 ) $q .= ' WHERE '.implode(' AND ', $selectors)."\n";

				// Special field treatment
				$special = array();
				$special['product_sku'] = $db->nameQuote('#__virtuemart_products').'.'.$db->nameQuote('product_sku');
				$special['product_id'] = $db->nameQuote('#__virtuemart_products').'.'.$db->nameQuote('virtuemart_product_id');
				$special['product_type_name'] = $db->nameQuote('#__vm_product_type').'.'.$db->nameQuote('product_type_name');
				$special['product_type_id'] = $db->nameQuote('#__vm_product_type').'.'.$db->nameQuote('product_type_id');

				// Check if we need to group the orders together
				$groupby = $template->get('groupby', 'general', false, 'bool');
				if ($groupby) {
					$filter = $this->getFilterBy('groupby', $ignore, $special);
					if (!empty($filter)) $q .= " GROUP BY ".$filter;
				}

				// Order by set field
				$orderby = $this->getFilterBy('sort', $ignore, $special);
				if (!empty($orderby)) $q .= " ORDER BY ".$orderby;

				$queries[] = $q.')';
			}

			// Create the full query
			$q = implode("\nUNION\n", $queries);
			
			// Add export limits
			$limits = $this->getExportLimit();
			
			// Execute the query
			$csvidb->setQuery($q, $limits['offset'], $limits['limit']);
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
		else {
			$this->addExportContent(JText::_('COM_CSVI_NO_DATA_FOUND'));
			$this->writeOutput();
			$csvilog->AddStats('incorrect', $db->getErrorMsg());
		}
	}
}
?>