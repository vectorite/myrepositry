<?php
/**
 * redSHOP helper file
 *
 * @package 	CSVI
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2012 RolandD Cyber Produksi
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: com_redshop.php 1924 2012-03-02 11:32:38Z RolandD $
 */

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

/**
 * The VirtueMart Config Class
 *
* @package CSVI
 */
class Com_Redshop {

	private $_csvidata = null;
	private $_vendor_id = null;
	private $_related_id = null;
	private $_catsep = null;

	/**
	 * Constructor
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		public
	 * @param
	 * @return
	 * @since 		4.0
	 */
	public function __construct() {
		$jinput = JFactory::getApplication()->input;
		$this->_csvidata = $jinput->get('csvi_data', null, null);
	}

	/**
	 * Get the pipe delimited category path of category IDs of a product
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		protected
	 * @param 		int		$product_id	the product ID to create the path for
	 * @return 		string	the category path for the product
	 * @since 		3.0
	 */
	protected function getCategoryPathIdRedshop($product_id) {
		$db = JFactory::getDBO();
		$q = "SELECT category_id FROM #__redshop_product_category_xref WHERE product_id = ".$product_id;
		$db->setQuery($q);
		return implode('|', $db->loadResultArray());
	}

	/**
	 * Create a category path for a product
	 *
	 * @copyright
	 * @author 		RolandD, soeren
	 * @todo
	 * @see
	 * @access 		protected
	 * @param 		int		$product_id	the product ID to create the path for
	 * @return 		string	the category path for the product
	 * @since
	 */
	protected function getCategoryPathRedshop($product_id) {
		$db = JFactory::getDBO();

		// Load the category separator
		if (is_null($this->_catsep)) {
			$jinput = JFactory::getApplication()->input;
			$template = $jinput->get('template', null, null);
			$this->_catsep = $template->get('category_separator', 'general', '/');
		}

		// Build the query to get the category path
		$q = "SELECT #__redshop_product.product_id, #__redshop_product.product_parent_id, category_name, #__redshop_category.category_id, #__redshop_category_xref.category_parent_id
			FROM #__redshop_category, #__redshop_product, #__redshop_product_category_xref,#__redshop_category_xref
			WHERE #__redshop_product.product_id = ".$product_id."
			AND #__redshop_category_xref.category_child_id = #__redshop_category.category_id
			AND #__redshop_category_xref.category_child_id = #__redshop_product_category_xref.category_id
			AND #__redshop_product.product_id = #__redshop_product_category_xref.product_id";
		$db->setQuery($q);
		$rows = $db->loadObjectList();
		$k = 1;
		$category_path = "";

		foreach ($rows as $row) {
			$category_name = array();

			// Check for product or item
			if ($row->category_name) {
				$category_parent_id = $row->category_parent_id;
				$category_name[] = $this->_getJoomFishCategory($row->category_id, $row->category_name);
			}
			else {
				// Find the category path of the parent product
				$q = "SELECT product_parent_id FROM #__redshop_product WHERE product_id='".$product_id."'";
				$db->setQuery($q);
				$ppi = $db->loadResult();

				$q  = "SELECT #__redshop_product.product_id, #__redshop_product.product_parent_id, category_name, #__redshop_category.category_id, #__redshop_category_xref.category_parent_id "
				."FROM #__redshop_category, #__redshop_product, #__redshop_product_category_xref,#__redshop_category_xref "
				."WHERE #__redshop_product.product_id='".$ppi."' "
				."AND #__redshop_category_xref.category_child_id=#__redshop_category.category_id "
				."AND #__redshop_category_xref.category_child_id = #__redshop_product_category_xref.category_id "
				."AND #__redshop_product.product_id = #__redshop_product_category_xref.product_id";
				$db->setQuery($q);
				$cat_details = $db->loadObject();
				$category_parent_id = $cat_details->category_parent_id;
				$category_name[] = $this->_getJoomFishCategory($cat_details->category_id, $cat_details->category_name);
			}

			// Check if the parent ID is not empty
			if ($category_parent_id == "") $category_parent_id = "0";

			// Load the individual category details
			while ($category_parent_id != "0") {
				$q = "SELECT category_name, category_parent_id "
				."FROM #__redshop_category, #__redshop_category_xref "
				."WHERE #__redshop_category_xref.category_child_id = #__redshop_category.category_id "
				."AND #__redshop_category.category_id = ".$category_parent_id;
				$db->setQuery($q);
				$cat_details = $db->loadObject();
				$category_name[] = $this->_getJoomFishCategory($category_parent_id, $cat_details->category_name);

				// Get the new parent ID
				$category_parent_id = $cat_details->category_parent_id;
			}

			// Construct the category path
			if (sizeof($category_name) > 1) {
				for ($i = sizeof($category_name)-1; $i >= 0; $i--) {
					$category_path .= $category_name[$i];
					if( $i >= 1) $category_path .= $this->_catsep;
				}
			}
			else $category_path .= $category_name[0];

			if( $k++ < sizeof($rows) )
			$category_path .= "|";
		}
		return $category_path;
	}

	/**
	 * Creates the category path based on a category ID
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		protected
	 * @param 		int	$category_id the ID to create the category path from
	 * @return 		string	the category path
	 * @since 		3.0
	 */
	protected function createCategoryPathRedshop($category_id) {
		$db = JFactory::getDBO();
		$catpaths = array();

		// Load the category separator
		if (is_null($this->_catsep)) {
			$jinput = JFactory::getApplication()->input;
			$template = $jinput->get('template', null, null);
			$this->_catsep = $template->get('category_separator', 'general', '/');
		}

		// Create the path
		while ($category_id > 0) {
			$q = "SELECT category_parent_id, category_name FROM #__redshop_category_xref x, #__redshop_category c
				WHERE x.category_child_id = c.category_id
				AND category_child_id = ".$category_id;
			$db->setQuery($q);
			$path = $db->loadObject();
			$catpaths[] = $this->_getJoomFishCategory($category_id, trim($path->category_name));
			$category_id = $path->category_parent_id;
		}
		$catpaths = array_reverse($catpaths);
		return implode($this->_catsep, $catpaths);
	}
}
?>