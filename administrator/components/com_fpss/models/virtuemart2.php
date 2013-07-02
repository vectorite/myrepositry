<?php
/**
 * @version		$Id: virtuemart2.php 763 2012-01-04 15:07:52Z joomlaworks $
 * @package		Frontpage Slideshow
 * @author		JoomlaWorks http://www.joomlaworks.gr
 * @copyright	Copyright (c) 2006 - 2012 JoomlaWorks Ltd. All rights reserved.
 * @license		Commercial - This code cannot be redistributed without permission from JoomlaWorks Ltd.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

class FPSSModelVirtuemart2 extends JModel {

	function getData() {		
		$db = $this->getDBO();
		$query = "SELECT categoryData.category_name, 
		productData.product_name, 
		product.virtuemart_product_id, 
		product.product_sku, product.published AS product_publish 
		FROM #__virtuemart_products AS product
		LEFT JOIN #__virtuemart_products_".VMLANG." AS productData ON product.virtuemart_product_id = productData.virtuemart_product_id
		LEFT JOIN #__virtuemart_product_categories AS category ON product.virtuemart_product_id = category.virtuemart_product_id
		LEFT JOIN #__virtuemart_categories_".VMLANG." AS categoryData ON category.virtuemart_category_id = categoryData.virtuemart_category_id";
		$conditions = array();
		$conditions[] = "product.product_parent_id=0";
		if ($this->getState('published')!=-1) {
			$conditions[]= "product.published = ".$this->getState('published');
		}
		if ($this->getState('catid')) {
			$conditions[]= "category.virtuemart_category_id=".$this->getState('catid');
		}
		if ($this->getState('search')) {
			$conditions[]= "(LOWER(productData.product_name) LIKE ".$db->Quote("%".$db->getEscaped($this->getState('search'), true)."%", false)." OR product.product_sku = ".$db->Quote($this->getState('search')).")";
		}
		if (count($conditions)) {
			$query.= " WHERE ".implode(' AND ', $conditions);
		}
		$query .= " ORDER BY ".$this->getState('ordering')." ".$this->getState('orderingDir');
		$db->setQuery($query, $this->getState('limitstart'), $this->getState('limit'));
		$rows = $db->loadObjectList();
		return $rows;
	}

	function getTotal() {
		$db = $this->getDBO();
		$query = "SELECT COUNT(product.virtuemart_product_id)
		FROM #__virtuemart_products AS product
		LEFT JOIN #__virtuemart_products_".VMLANG." AS productData ON product.virtuemart_product_id = productData.virtuemart_product_id
		LEFT JOIN #__virtuemart_product_categories AS category ON product.virtuemart_product_id = category.virtuemart_product_id
		LEFT JOIN #__virtuemart_categories_".VMLANG." AS categoryData ON category.virtuemart_category_id = categoryData.virtuemart_category_id";
		$conditions = array();
		$conditions[] = "product.product_parent_id=0";
		if ($this->getState('published')!=-1) {
			$conditions[]= "product.published = ".$this->getState('published');
		}
		if ($this->getState('catid')) {
			$conditions[]= "category.virtuemart_category_id=".$this->getState('catid');
		}
		if ($this->getState('search')) {
			$conditions[]= "(LOWER(productData.product_name) LIKE ".$db->Quote("%".$db->getEscaped($this->getState('search'), true)."%", false)." OR product.product_sku = ".$db->Quote($this->getState('search')).")";
		}
		if (count($conditions)) {
			$query.= " WHERE ".implode(' AND ', $conditions);
		}
		$db->setQuery($query);
		$total = $db->loadresult();
		return $total;
	}

	function getCategories() {
		JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'tables');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'config.php');
		require(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'shopfunctions.php');
		JRequest::setVar('filter_order', 'c.ordering');
		JRequest::setVar('filter_order_Dir', 'ASC');
		$list = ShopFunctions::categoryListTree(array($this->getState('catid')));
		return '<select onchange="this.form.submit();" id="catid" name="catid"><option value="0">'.JText::_('FPSS_ANY').'</option>'.$list.'</select>';
	}
}
