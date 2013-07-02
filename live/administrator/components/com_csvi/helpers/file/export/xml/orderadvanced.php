<?php
/**
 * Custom XML class
 *
 * @package 	CSVI
 * @subpackage 	Export
 * @todo		Clean up class vars
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2012 RolandD Cyber Produksi
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: custom.php 1924 2012-03-02 11:32:38Z RolandD $
 */

defined('_JEXEC') or die;

/**
 * Custom XML Export Class
 *
 * @package 	CSVI
 * @subpackage 	Export
 */
class CsviOrderAdvanced {

	/** @var string contains the data to export */
	var $contents = "";
	/** @var string contains the XML node to export */
	var $node = "";
	private $_node = array();

	/**
	 * Creates the XML header
	 *
	 * @see $contents
	 * @return string XML header
	 */
	public function HeaderText() {
		$jinput = JFactory::getApplication()->input;
		$template = $jinput->get('template', null, null);
		return $template->get('header', 'layout', '', null, 2);
	}

	/**
	 * Creates the XML footer
	 *
	 * @see $contents
	 * @return string XML header
	 */
	public function FooterText() {
		$jinput = JFactory::getApplication()->input;
		$template = $jinput->get('template', null, null);
		return $template->get('footer', 'layout', '', null, 2);
	}
	
	/**
	 * Closes an XML item node
	 *
	 * @see $contents
	 * @return string XML node data
	 */
	public function NodeEnd() {
		$order = '';
		$orderlines = '';
		foreach ($this->_node as $key => $node) {
			if ($key == 0) $order = $node;
			else {
				$orderlines .= $node;
			}
		}
		// Empty the node
		$this->_node = array();
		
		return str_ireplace('[orderlines]', $orderlines, $order);
	}
	
	/**
	 * A full order template
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		private
	 * @param
	 * @return
	 * @since 		4.4.1
	 */
	public function Order() {
		$jinput = JFactory::getApplication()->input;
		$template = $jinput->get('template', null, null);
		$this->_node[] = $template->get('order', 'layout', '', null, 2);
	}

	/**
	 * A full orderline template
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		private
	 * @param
	 * @return
	 * @since 		4.4.1
	 */
	public function Orderline() {
		$jinput = JFactory::getApplication()->input;
		$template = $jinput->get('template', null, null);
		$this->_node[] = $template->get('orderline', 'layout', '', null, 2);
	}

	/**
	* Adds an XML element
	*
	* @see $node
	* @return string XML element
	 */
	private function _element($content, $fieldname, $cdata=false) {
		$data = '';
		if ($cdata) $data .= '<![CDATA[';
		$data .= $content;
		if ($cdata) $data .= ']]>';
		foreach ($this->_node as $key => $node) {
			$this->_node[$key] = str_ireplace('['.$fieldname.']', $data, $node);
		}
		return;
	}

	/**
	* Handles all content and modifies special cases
	*
	* @see $contents
	* @return string formatted XML element
	 */
	public function ContentText($content, $column_header, $fieldname, $cdata=false) {
		if (empty($column_header)) $column_header = $fieldname;
		return $this->_element($content, $column_header, $cdata);
	}
}
?>
