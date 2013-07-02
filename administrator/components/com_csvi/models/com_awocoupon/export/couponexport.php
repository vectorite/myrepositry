<?php
/**
 * AwoCoupon Coupon export class
 *
 * @package 	CSVI
 * @subpackage 	Export
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2012 RolandD Cyber Produksi
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: couponexport.php 2052 2012-08-02 05:44:47Z RolandD $
 */

defined('_JEXEC') or die;

/**
 * Processor for coupons exports
 *
 * @package 	CSVI
 * @subpackage 	Export
 */
class CsviModelCouponExport extends CsviModelExportfile {

	// Private variables
	private $_exportmodel = null;

	/**
	 * Subscription export
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
		require_once (JPATH_COMPONENT_ADMINISTRATOR.'/helpers/com_virtuemart.php');
		$helper = new Com_VirtueMart();

		// Build something fancy to only get the fieldnames the user wants
		$userfields = array();
		foreach ($export_fields as $column_id => $field) {
			if ($field->process) {
				switch ($field->field_name) {
					case 'shoppergroup':
					case 'username':
					case 'manufacturer_name':
					case 'product_sku':
					case 'category_path':
						$userfields[] = $db->quoteName('#__awocoupon_vm').'.'.$db->quoteName('id');
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
		$query->from('#__awocoupon_vm');
		//$query->leftJoin('#__awocoupon_vm_category ON #__awocoupon_vm_category.coupon_id = #__awocoupon_vm.id');
		//$query->leftJoin('#__users ON #__users.id = #__akeebasubs_subscriptions.user_id');

		// Check if there are any selectors
		$selectors = array();

		// Filter by published state
		$publish_state = $template->get('publish_state', 'general');
		if ($publish_state !== '' && ($publish_state == 1 || $publish_state == 0)) {
			$selectors[] = '#__awocoupon_vm.published = '.$publish_state;
		}
		
		// Filter on function type
		$function_type = $template->get('function_type', 'coupon');
		if ($function_type !== '') {
			$selectors[] = '#__awocoupon_vm.function_type = '.$db->quote($function_type);
		}
		
		// Filter on function type 2
		$function_type2 = $template->get('function_type2', 'coupon');
		if ($function_type2 !== '') {
			$selectors[] = '#__awocoupon_vm.function_type2 = '.$db->quote($function_type2);
		}

		// Filter on coupon value type
		$coupon_value_type = $template->get('coupon_value_type', 'coupon');
		if ($coupon_value_type !== '') {
			switch ($coupon_value_type) {
				case 'empty':
					$selectors[] = '#__awocoupon_vm.coupon_value_type IS NULL';
					break;
				default:
					$selectors[] = '#__awocoupon_vm.coupon_value_type = '.$db->quote($coupon_value_type);
					break;
			}
		}
		
		// Filter on discount type
		$discount_type = $template->get('discount_type', 'coupon');
		if ($discount_type !== '') {
			switch ($discount_type) {
				case 'empty':
					$selectors[] = '#__awocoupon_vm.discount_type IS NULL';
					break;
				default:
					$selectors[] = '#__awocoupon_vm.discount_type = '.$db->quote($discount_type);
					break;
			}
		}

		// Check if we need to attach any selectors to the query
		if (count($selectors) > 0 ) $query->where(implode("\n AND ", $selectors));

		// Any fields to ignore
		$ignore = array('category_path', 'product_sku', 'manufacturer_name', 'username', 'shoppergroup');

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
							case 'category_path':
								// Get all linked category IDs
								$query = $db->getQuery(true);
								$query->select('category_id');
								$query->from('#__awocoupon_vm_category');
								$query->where('coupon_id = '.$record->id);
								$db->setQuery($query);
								$catids = $db->loadColumn();

								// Create the paths
								$category_path = trim($helper->createCategoryPathById($catids));
								if (strlen(trim($category_path)) == 0) $category_path = $field->default_value;
								$category_path = CsviHelper::replaceValue($field->replace, $category_path);
								$record->output[$column_id] = $category_path;
								break;
							case 'product_sku':
								// Get all linked product SKUs
								$query = $db->getQuery(true);
								$query->select('product_sku');
								$query->from('#__virtuemart_products');
								$query->leftJoin('#__awocoupon_vm_product on #__awocoupon_vm_product.product_id = #__virtuemart_products.virtuemart_product_id');
								$query->where('#__awocoupon_vm_product.coupon_id = '.$record->id);
								$db->setQuery($query);
								$skus = $db->loadColumn();

								// Create the SKUs
								$product_sku = implode('|', $skus);
								if (strlen(trim($product_sku)) == 0) $product_sku = $field->default_value;
								$product_sku = CsviHelper::replaceValue($field->replace, $product_sku);
								$record->output[$column_id] = $product_sku;
								break;
							case 'manufacturer_name':
								$query = $db->getQuery(true);
								$query->select('mf_name');
								$query->from('#__virtuemart_manufacturers_'.$template->get('language', 'general'));
								$query->leftJoin('#__virtuemart_product_manufacturers ON #__virtuemart_product_manufacturers.virtuemart_manufacturer_id = #__virtuemart_manufacturers_'.$template->get('language', 'general').'.virtuemart_manufacturer_id');
								$query->leftJoin('#__awocoupon_vm_manufacturer on #__awocoupon_vm_manufacturer.manufacturer_id = #__virtuemart_manufacturers_'.$template->get('language', 'general').'.virtuemart_manufacturer_id');
								$query->where('#__awocoupon_vm_manufacturer.coupon_id = '.$record->id);
								$query->group('mf_name');
								$db->setQuery($query);
								$manufacturers = $db->loadColumn();

								// Create the manufacturer name
								$manufacturer_name = implode('|', $manufacturers);
								if (strlen(trim($manufacturer_name)) == 0) $manufacturer_name = $field->default_value;
								$manufacturer_name = CsviHelper::replaceValue($field->replace, $manufacturer_name);
								$record->output[$column_id] = $manufacturer_name;
								break;
							case 'username':
								// Get all linked product SKUs
								$query = $db->getQuery(true);
								$query->select('username');
								$query->from('#__users');
								$query->leftJoin('#__awocoupon_vm_user on #__awocoupon_vm_user.user_id = #__users.id');
								$query->where('#__awocoupon_vm_user.coupon_id = '.$record->id);
								$db->setQuery($query);
								$ids = $db->loadColumn();
									
								// Create the SKUs
								$username = implode('|', $ids);
								if (strlen(trim($username)) == 0) $username = $field->default_value;
								$username = CsviHelper::replaceValue($field->replace, $username);
								$record->output[$column_id] = $username;
								break;
							case 'shoppergroup':
								// Get all linked product SKUs
								$query = $db->getQuery(true);
								$query->select('shopper_group_name');
								$query->from('#__virtuemart_shoppergroups');
								$query->leftJoin('#__awocoupon_vm_usergroup on #__awocoupon_vm_usergroup.shopper_group_id = #__virtuemart_shoppergroups.virtuemart_shoppergroup_id');
								$query->where('#__awocoupon_vm_usergroup.coupon_id = '.$record->id);
								$db->setQuery($query);
								$ids = $db->loadColumn();

								// Create the SKUs
								$usergroup = implode('|', $ids);
								if (strlen(trim($usergroup)) == 0) $usergroup = $field->default_value;
								$usergroup = CsviHelper::replaceValue($field->replace, $usergroup);
								$record->output[$column_id] = $user_group;
								break;
							case 'coupon_value':
								if (!empty($fieldvalue)) {
									$fieldvalue =  number_format($fieldvalue, $template->get('export_price_format_decimal', 'general', 2, 'int'), $template->get('export_price_format_decsep', 'general'), $template->get('export_price_format_thousep', 'general'));
								}
								if (strlen(trim($fieldvalue)) == 0) $fieldvalue = $field->default_value;
								$record->output[$column_id] = $fieldvalue;
								break;
							case 'startdate':
							case 'expiration':
								if (!empty($record->$fieldname)) {
									$date = JFactory::getDate($record->$fieldname);
									$fieldvalue = CsviHelper::replaceValue($field->replace, date($template->get('export_date_format', 'general'), $date->toUnix()));
								}
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