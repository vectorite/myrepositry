<?php
/**
 * Property export class
 *
 * @package 	CSVI
 * @subpackage 	Export
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2012 RolandD Cyber Produksi
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: customfieldsexport.php 1924 2012-03-02 11:32:38Z RolandD $
 */

defined('_JEXEC') or die;

/**
 * Processor for property exports
 *
 * @package 	CSVI
 * @subpackage 	Export
 */
class CsviModelPropertyExport extends CsviModelExportfile {

	/**
	 * Property tables export
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
	 * @since 		3.4
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
		$classname = 'CsviCom_Ezrealty_Config';
		if (class_exists($classname)) $config = new $classname;
		
		// Build something fancy to only get the fieldnames the user wants
		$userfields = array();
		foreach ($export_fields as $column_id => $field) {
			switch ($field->field_name) {
				case 'id':
				case 'alias':
				case 'checked_out':
				case 'checked_out_time':
				case 'editor':
				case 'ordering':
				case 'published':
					$userfields[] = $db->quoteName('e').'.'.$db->quoteName($field->field_name);
					break;
				case 'category':
					$userfields[] = $db->quoteName('c').'.'.$db->quoteName('name');
					break;
				case 'country':
					$userfields[] = $db->quoteName('cn').'.'.$db->quoteName('name', 'country');
					break;
				case 'state':
					$userfields[] = $db->quoteName('st').'.'.$db->quoteName('name', 'state');
					break;
				case 'city':
					$userfields[] = $db->quoteName('loc').'.'.$db->quoteName('ezcity', 'city');
					break;
				case 'custom':
					break;
				default:
					$userfields[] = $db->quoteName($field->field_name);
					break;
			}
		}

		// Build the query
		$userfields = array_unique($userfields);
		$query = $db->getQuery(true);
		$query->select(implode(",\n", $userfields));
		$query->from($db->quoteName("#__ezrealty", "e"));
		$query->leftJoin('#__ezrealty_catg AS c ON e.cid = c.id');
		$query->leftJoin('#__ezrealty_country AS cn ON e.cnid = cn.id');
		$query->leftJoin('#__ezrealty_state AS st ON e.stid = st.id');
		$query->leftJoin('#__ezrealty_locality AS loc ON e.locid = loc.id');

		$selectors = array();
		
		// Filter by published state
		$publish_state = $template->get('publish_state', 'general');
		if ($publish_state != '' && ($publish_state == 1 || $publish_state == 0)) {
			$selectors[] = $db->quoteName('e').'.'.$db->quoteName('published').' = '.$publish_state;
		}
		
		// Filter by transaction type
		$transaction_type = $template->get('transaction_type', 'property');
		if ($transaction_type[0] != '') {
			$selectors[] = $db->quoteName('e').'.'.$db->quoteName('type').' IN ('.implode(',', $transaction_type).')';
		}
		
		// Filter by property type
		$property_type = $template->get('property_type', 'property');
		if ($property_type[0] != '') {
			$selectors[] = $db->quoteName('e').'.'.$db->quoteName('cid').' IN ('.implode(',', $property_type).')';
		}
		
		// Filter by street
		$street = $template->get('street', 'property');
		if ($street[0] != '') {
			$selectors[] = $db->quoteName('e').'.'.$db->quoteName('address2')." IN ('".implode("','", $street)."')";
		}
		
		// Filter by locality
		$locality = $template->get('locality', 'property');
		if ($locality[0] != '') {
			$selectors[] = $db->quoteName('e').'.'.$db->quoteName('locality')." IN ('".implode("','", $locality)."')";
		}
		
		// Filter by states
		$state = $template->get('state', 'property');
		if ($state[0] != '') {
			$selectors[] = $db->quoteName('e').'.'.$db->quoteName('state')." IN ('".implode("','", $state)."')";
		}
		
		// Filter by countries
		$country = $template->get('country', 'property');
		if ($country[0] != '') {
			$selectors[] = $db->quoteName('e').'.'.$db->quoteName('country')." IN ('".implode("','", $country)."')";
		}
		
		// Filter by owner
		$owner = $template->get('owner', 'property');
		if ($owner[0] != '') {
			$selectors[] = $db->quoteName('e').'.'.$db->quoteName('owner').' IN ('.implode(',', $owner).')';
		}
		
		// Check if we need to attach any selectors to the query
		if (count($selectors) > 0 ) $query->where(implode("\n AND ", $selectors));
		
		// Ingore fields
		$ignore = array('custom', 'category', 'country', 'state', 'city');
		
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
							case 'aucdate':
							case 'availdate':
							case 'checked_out_time':
							case 'listdate':
							case 'ohdate':
							case 'ohdate2':
								$date = JFactory::getDate($record->$fieldname);
								$fieldvalue = CsviHelper::replaceValue($field->replace, date($template->get('export_date_format', 'general'), $date->toUnix()));
								$record->output[$column_id] = $fieldvalue;
								break;
							case 'expdate':
							case 'lastupdate':
								$fieldvalue = CsviHelper::replaceValue($field->replace, date($template->get('export_date_format', 'general'), $record->$fieldname));
								$record->output[$column_id] = $fieldvalue;
								break;
							case 'bond':
							case 'closeprice':
							case 'offpeak':
							case 'price':
								$fieldvalue =  number_format($fieldvalue, $template->get('export_price_format_decimal', 'general', 2, 'int'), $template->get('export_price_format_decsep', 'general'), $template->get('export_price_format_thousep', 'general'));
								if ($template->get('add_currency_to_price', 'general')) {
									$fieldvalue = $config->get('er_currencycode').' '.$fieldvalue;
								}
								$fieldvalue = CsviHelper::replaceValue($field->replace, $fieldvalue);
								// Check if we have any content otherwise use the default value
								if (strlen(trim($fieldvalue)) == 0) $fieldvalue = $field->default_value;
								$record->output[$column_id] = $fieldvalue;
								break;
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
							case 'category':
								$fieldvalue = $record->name;
								// Check if we have any content otherwise use the default value
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