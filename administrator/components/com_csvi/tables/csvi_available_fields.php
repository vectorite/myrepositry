<?php
/**
 * Available fields table
 *
 * @package 	CSVI
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2012 RolandD Cyber Produksi
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: csvi_available_fields.php 1924 2012-03-02 11:32:38Z RolandD $
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

/**
* @package CSVI
 */
class TableCsvi_available_fields extends JTable {
	
	/** @var integer */
	var $id = 0;
	/** @var string */
	var $csvi_name = null;
	/** @var string */
	var $vm_name = null;
	/** @var string */
	var $vm_table = null;
	
	/**
	* @param database A database connector object
	 */
	function __construct($db) {
		parent::__construct('#__csvi_available_fields', 'id', $db );
	}
}
?>