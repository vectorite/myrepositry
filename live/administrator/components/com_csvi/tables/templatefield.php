<?php
/**
 * Template fields table
 *
 * @package 	CSVI
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2012 RolandD Cyber Produksi
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: csvi_template_types.php 1924 2012-03-02 11:32:38Z RolandD $
 */

// No direct access
defined('_JEXEC') or die;

/**
* @package CSVI
 */
class TableTemplatefield extends JTable {

	/**
	 * Table constructor
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		public
	 * @param 		$db	object	A database connector object
	 * @return
	 * @since 		3.0
	 */
	public function __construct($db) {
		parent::__construct('#__csvi_template_fields', 'id', $db );
	}
}
?>