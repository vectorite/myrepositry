<?php
/**
 * Shipping rate details export class
 *
 * @package 	CSVI
 * @subpackage 	Export
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2012 RolandD Cyber Produksi
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: manufacturerexport.php 1924 2012-03-02 11:32:38Z RolandD $
 */

defined('_JEXEC') or die;

/**
 * Processor for shipping rate exports
 *
 * @package 	CSVI
 * @subpackage 	Export
 */
class CsviModelShippingrateExport extends CsviModelExportfile {

	// Private variables
	private $_exportmodel = null;

	/**
	 * Shipping rate export
	 *
	 * Exports shipping rates data to either csv, xml or HTML format
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
			if ($field->process) {
				switch ($field->field_name) {
					case 'virtuemart_shipmentmethod_id':
					case 'shipment_name':
					case 'shipment_desc':
					case 'custom':
					case 'slug':
					case 'shopper_group_name':
						$userfields[] = $db->quoteName('#__virtuemart_shipmentmethods').'.'.$db->quoteName('virtuemart_shipmentmethod_id');
						break;
					case 'shipment_logos':
					case 'countries':
					case 'zip_start':
					case 'zip_stop':
					case 'weight_start':
					case 'weight_stop':
					case 'weight_unit':
					case 'nbproducts_start':
					case 'nbproducts_stop':
					case 'orderamount_start':
					case 'orderamount_stop':
					case 'cost':
					case 'package_fee':
					case 'tax_id':
					case 'tax':
					case 'free_shipment':
						$userfields[] = $db->quoteName('#__virtuemart_shipmentmethods').'.'.$db->quoteName('shipment_params');
						break;
					default:
						$userfields[] = $db->quoteName($field->field_name);
						break;
				}
			}
		}

		// Build the query
		$userfields = array_unique($userfields);
		$query = $db->getQuery(true);
		$query->select(implode(",\n", $userfields));
		$query->from('#__virtuemart_shipmentmethods');
		$query->leftJoin('#__virtuemart_shipmentmethods_'.$template->get('language', 'general').' ON #__virtuemart_shipmentmethods_'.$template->get('language', 'general').'.virtuemart_shipmentmethod_id = #__virtuemart_shipmentmethods.virtuemart_shipmentmethod_id');

		// Check if there are any selectors
		$selectors = array();

		// Filter by published state
		$publish_state = $template->get('publish_state', 'general');
		if ($publish_state !== '' && ($publish_state == 1 || $publish_state == 0)) {
			$selectors[] = '#__virtuemart_manufacturers.published = '.$db->Quote($publish_state);
		}

		// Check if we need to attach any selectors to the query
		if (count($selectors) > 0 ) $query->where(implode("\n AND ", $selectors));

		// Fields to ignore
		$ignore = array('shipment_name', 'shipment_desc', 'custom', 'slug','shipment_logos', 'countries', 'zip_start', 'zip_stop', 'weight_start', 'weight_stop', 'weight_unit', 'nbproducts_start', 'nbproducts_stop', 'orderamount_start',
							'orderamount_stop', 'cost', 'package_fee', 'tax_id', 'free_shipment', 'tax', 'shopper_group_name');

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
				$linenumber = 1;
				$shipment_params = array();
				while ($record = $csvidb->getRow()) {
					$csvilog->setLinenumber($linenumber++);
					if ($template->get('export_file', 'general') == 'xml' || $template->get('export_file', 'general') == 'html') $this->addExportContent($exportclass->NodeStart());
					
					// Check if the shipment params need to be converted
					if (isset($record->shipment_params)) {
						$ship_params = explode('|', $record->shipment_params);
						array_pop($ship_params);
						foreach ($ship_params as $param) {
							list($name, $value) = explode('=', $param);
							$shipment_params[$name] = $value;
						}
					}
					foreach ($export_fields as $column_id => $field) {
					$fieldname = $field->field_name;
						// Add the replacement
						if (isset($record->$fieldname)) $fieldvalue = CsviHelper::replaceValue($field->replace, $record->$fieldname);
						else $fieldvalue = '';
						switch ($fieldname) {
							case 'shipment_name':
							case 'shipment_desc':
							case 'slug':
								$query = $db->getQuery(true);
								$query->select($fieldname);
								$query->from('#__virtuemart_shipmentmethods_'.$template->get('language', 'general'));
								$query->where('virtuemart_shipmentmethod_id = '.$record->virtuemart_shipmentmethod_id);
								$db->setQuery($query);
								$fieldvalue = $db->loadResult();
								// Check if we have any content otherwise use the default value
								if (strlen(trim($fieldvalue)) == 0) $fieldvalue = $field->default_value;
								$record->output[$column_id] = $fieldvalue;
								break;
							case 'shipment_logos':
							case 'countries':
								$fieldvalue = json_decode($shipment_params[$fieldname]);
								if (!empty($fieldvalue)) $fieldvalue = implode(',', $fieldvalue);
								$fieldvalue = CsviHelper::replaceValue($field->replace, $fieldvalue);
								if (strlen(trim($fieldvalue)) == 0) $fieldvalue = $field->default_value;
								$record->output[$column_id] = $fieldvalue;
								break;
							case 'zip_start':
							case 'zip_stop':
							case 'weight_start':
							case 'weight_stop':
							case 'weight_unit':
							case 'nbproducts_start':
							case 'nbproducts_stop':
							case 'orderamount_start':
							case 'orderamount_stop':
							case 'cost':
							case 'tax_id':
							case 'package_fee':
							case 'free_shipment':
								$fieldvalue = json_decode($shipment_params[$fieldname]);
								$fieldvalue = CsviHelper::replaceValue($field->replace, $fieldvalue);
								if (strlen(trim($fieldvalue)) == 0) $fieldvalue = $field->default_value;
								$record->output[$column_id] = $fieldvalue;
								break;
							case 'shopper_group_name':
								$query = $db->getQuery(true);
								$query->select($fieldname);
								$query->from('#__virtuemart_shoppergroups g');
								$query->leftJoin('#__virtuemart_shipmentmethod_shoppergroups s ON g.virtuemart_shoppergroup_id = s.virtuemart_shoppergroup_id');
								$query->where('s.virtuemart_shipmentmethod_id = '.$record->virtuemart_shipmentmethod_id);
								$db->setQuery($query);
								$fieldvalue = implode('|', $db->loadResultArray());
								// Check if we have any content otherwise use the default value
								if (strlen(trim($fieldvalue)) == 0) $fieldvalue = $field->default_value;
								$record->output[$column_id] = $fieldvalue;
								break;
							case 'tax':
								$fieldvalue = json_decode($shipment_params['tax_id']);
								switch ($fieldvalue) {
									case '-1':
										$fieldvalue = 'norule';
										break;
									case '0':
										$fieldvalue = 'default';
										break;
									default:
										$query = $db->getQuery(true);
										$query->select('calc_name');
										$query->from('#__virtuemart_calcs');
										$query->where($db->quoteName('virtuemart_calc_id').' = '.$fieldvalue);
										$db->setQuery($query);
										$fieldvalue = $db->loadResult();
										break;
								}									
								$fieldvalue = CsviHelper::replaceValue($field->replace, $fieldvalue);
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
					
					// Empty the shipment params
					$shipment_params = null;
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