<?php
/**
 * Yandex XML class
 *
 * @package 	CSVI
 * @subpackage 	Export
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2012 RolandD Cyber Produksi
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: google.php 2039 2012-07-14 18:16:58Z RolandD $
 */

defined( '_JEXEC' ) or die;

/**
 * Google XML Export Class
 *
* @package CSVI
 * @subpackage Export
 */
class CsviYandex {

	/** @var string contains the data to export */
	var $contents = "";
	/** @var string contains the XML node to export */
	var $node = "";

	/**
	* Creates the XML header
	*
	* @see $contents
	* @todo take settings from the global array
	* @return string XML header
	 */
	function HeaderText() {
		$this->contents = '<?xml version="1.0" encoding="UTF-8"?>'.chr(10);
		$this->contents .= '<!DOCTYPE yml_catalog SYSTEM "shops.dtd">'.chr(10);
		$this->contents .= '<yml_catalog date="'.date('Y-m-d H:i:s', time()).'">'.chr(10);
		// Yandex Custom Namespace
		$this->contents .= '<shop>'.chr(10);
		// Get the XML channel header
		$settings = JRequest::getVar('settings');
		$this->contents .= '<name>'.$settings->get('yandex.ya_name').'</name>'.chr(10);
		$this->contents .= '<company>'.$settings->get('yandex.ya_company').'</company>'.chr(10);
		$this->contents .= '<url>'.$settings->get('yandex.ya_link').'</url>'.chr(10);
		$this->contents .= '<currencies>'.chr(10);
		$this->contents .= '<currency id="'.$settings->get('yandex.ya_currency').'" rate="'.$settings->get('yandex.ya_currency_rate').'" plus="'.$settings->get('yandex.ya_currency_plus').'"/>'.chr(10);
		$this->contents .= '</currencies>'.chr(10);
		return $this->contents;
	}

	/**
	* Creates the XML footer
	*
	* @see $contents
	* @return string XML header
	 */
	function FooterText() {
		$this->contents = '</shop>'.chr(10);
		$this->contents .= '</yml_catalog>'.chr(10);
		return $this->contents;
	}
	
	/**
	 * Add the categories to the export file
	 * 
	 * @copyright 
	 * @author 		RolandD
	 * @todo 
	 * @see 
	 * @access 		public
	 * @param 
	 * @return 
	 * @since 		5.0
	 */
	public function categories($categories) {
		$jinput = JFactory::getApplication()->input;
		$settings = $jinput->get('settings', null, null);
		$cats = '<categories>'.chr(10);
		foreach ($categories as $category) {
			$cats .= '<category id="'.$category->id.'"';
			if ($category->parent_id > 0) $cats .= ' parentId="'.$category->parent_id.'"';
			$cats .= '>'.$category->catname.'</category>'.chr(10);
		}
		$cats .= '</categories>'.chr(10);
		$cats .= '<local_delivery_cost>'.$settings->get('yandex.ya_delivery_cost').'</local_delivery_cost>';
		return $cats;
	}

	/**
	* Opens an XML item node
	*
	* @see $contents
	* @return string XML node data
	 */
	function NodeStart($product_id, $type="vendor.model") {
		// $this->contents = '<offer id="'.$product_id.'" type="'.$type.'">'.chr(10);
		$this->contents = '<offer id="'.$product_id.'">'.chr(10);
		return $this->contents;
	}

	/**
	* Closes an XML item node
	*
	* @see $contents
	* @return string XML node data
	 */
	function NodeEnd() {
		$this->contents = '</offer>'.chr(10);
		return $this->contents;
	}

	/**
	* Adds an XML element
	*
	* @see $node
	* @return string XML element
	 */
	function Element($column_header, $cdata=false) {
		if ($column_header == 'categoryId') {
			$this->node = '<'.$column_header.' type="Own">';
		}
		else {
			$this->node = '<'.$column_header.'>';
		}
		if ($cdata) $this->node .= '<![CDATA[';
		$this->node .= $this->contents;
		if ($cdata) $this->node .= ']]>';
		$this->node .= '</'.$column_header.'>';
		$this->node .= "\n";
		return $this->node;
	}

	/**
	* Handles all content and modifies special cases
	*
	* @see $contents
	* @return string formatted XML element
	 */
	function ContentText($content, $column_header, $fieldname, $cdata=false) {
		if (!empty($content)) {
			switch ($fieldname) {
				default:
					// Replace certain characters
					if (!$cdata) {
						$find = array();
						$find[] = '&';
						$find[] = '>';
						$find[] = '<';
						$replace = array();
						$replace[] = '&amp;';
						$replace[] = '&gt;';
						$replace[] = '&lt;';
						$this->contents = str_replace($find, $replace, $content);
					}
					else $this->contents = $content;
					break;
			}
			if (empty($column_header)) $column_header = $fieldname;
			return $this->Element($column_header, $cdata);
		}
		return '';
	}
}
?>
