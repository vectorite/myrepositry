<?php
/**
 * AwoCoupon Gift certificate export class
 *
 * @package 	CSVI
 * @subpackage 	Export
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2012 RolandD Cyber Produksi
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: couponexport.php 1925 2012-03-02 11:51:51Z RolandD $
 */

defined('_JEXEC') or die;

/**
 * Processor for coupons exports
 *
 * @package 	CSVI
 * @subpackage 	Export
 */
class CsviModelGiftcertificateExport extends CsviModelExportfile {

	// Private variables
	private $_exportmodel = null;

	/**
	 * Gift certificate export
	 *
	 * Exports subscription details data to either csv, xml or HTML format
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
					case 'id':
						$userfields[] = $db->quoteName('#__awocoupon_vm_giftcert_product').'.'.$db->quoteName('id');
						break;
					case 'product_id':
					case 'published':
						$userfields[] = $db->quoteName('#__awocoupon_vm_giftcert_product').'.'.$db->quoteName($field->field_name);
						break;
					case 'coupon_code':
						$userfields[] = $db->quoteName('#__awocoupon_vm_giftcert_product').'.'.$db->quoteName('coupon_template_id');
						break;
					case 'profile_image':
						$userfields[] = $db->quoteName('#__awocoupon_vm_giftcert_product').'.'.$db->quoteName('profile_id');
						break;
					case 'custom':
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
		$query->from('#__awocoupon_vm_giftcert_product');
		$query->leftJoin('#__awocoupon_vm_giftcert_code ON #__awocoupon_vm_giftcert_code.product_id = #__awocoupon_vm_giftcert_product.product_id');
		$query->leftJoin('#__virtuemart_products ON #__virtuemart_products.virtuemart_product_id = #__awocoupon_vm_giftcert_product.product_id');

		// Check if there are any selectors
		$selectors = array();

		// Filter by published state
		$publish_state = $template->get('publish_state', 'general');
		if ($publish_state !== '' && ($publish_state == 1 || $publish_state == 0)) {
			$selectors[] = '#__awocoupon_vm_giftcert_product.published = '.$publish_state;
		}
		
		// Filter on product SKU
		$productskufilter = $template->get('product_sku', 'giftcertificate');
		if ($productskufilter !== '') {
			$productskufilter .= ',';
			if (strpos($productskufilter, ',')) {
				$skus = explode(',', $productskufilter);
				$wildcard = '';
				$normal = array();
				foreach ($skus as $sku) {
					if (!empty($sku)) {
						if (strpos($sku, '%')) {
							$wildcard .= "#__virtuemart_products.product_sku LIKE ".$db->Quote($sku)." OR ";
						}
						else $normal[] = $db->Quote($sku);
					}
				}
				if (substr($wildcard, -3) == 'OR ') $wildcard = substr($wildcard, 0, -4);
				if (!empty($wildcard) && !empty($normal)) {
					$selectors[] = "(".$wildcard." OR #__virtuemart_products.product_sku IN (".implode(',', $normal)."))";
				}
				else if (!empty($wildcard)) {
					$selectors[] = "(".$wildcard.")";
				}
				else if (!empty($normal)) {
					$selectors[] = "(#__virtuemart_products.product_sku IN (".implode(',', $normal)."))";
				}
			}
		}
		
		// Filter on template
		$awotemplate = $template->get('template', 'giftcertificate');
		if ($awotemplate !== '') {
			$selectors[] = '#__awocoupon_vm_giftcert_product.coupon_template_id = '.$db->quote($awotemplate);
		}

		// Filter on coupon value type
		$profile = $template->get('profile', 'giftcertificate');
		if ($profile !== '') {
			$selectors[] = '#__awocoupon_vm_giftcert_product.profile_id = '.$db->quote($profile);
		}
		
		// Check if we need to attach any selectors to the query
		if (count($selectors) > 0 ) $query->where(implode("\n AND ", $selectors));

		// Any fields to ignore
		$ignore = array('product_sku', 'coupon_code', 'profile_image');

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
						// Add the replacement
						if (isset($record->$fieldname)) $fieldvalue = CsviHelper::replaceValue($field->replace, $record->$fieldname);
						else $fieldvalue = '';
						switch ($fieldname) {
							case 'coupon_code':
								// Get all linked product SKUs
								$query = $db->getQuery(true);
								$query->select('coupon_code');
								$query->from('#__awocoupon_vm');
								$query->where('id = '.$record->coupon_template_id);
								$db->setQuery($query);
								$code = $db->loadResult();
									
								// Create the SKUs
								if (strlen(trim($code)) == 0) $username = $field->default_value;
								$code = CsviHelper::replaceValue($field->replace, $code);
								$record->output[$column_id] = $code;
								break;
							case 'profile_image':
								// Get all linked product SKUs
								$query = $db->getQuery(true);
								$query->select('title');
								$query->from('#__awocoupon_vm_profile');
								$query->where('id = '.$record->profile_id);
								$db->setQuery($query);
								$code = $db->loadResult();
									
								// Create the SKUs
								if (strlen(trim($code)) == 0) $username = $field->default_value;
								$code = CsviHelper::replaceValue($field->replace, $code);
								$record->output[$column_id] = $code;
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