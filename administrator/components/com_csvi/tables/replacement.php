<?php
/**
 * Replacements table
 *
 * @package 	CSVI
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2012 RolandD Cyber Produksi
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: replacement.php 1924 2012-03-02 11:32:38Z RolandD $
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

/**
* @package CSVI
 */
class TableReplacement extends JTable {

	/**
	* @param database A database connector object
	*/
	public function __construct($db) {
		parent::__construct('#__csvi_replacements', 'id', $db );
	}
}
?>