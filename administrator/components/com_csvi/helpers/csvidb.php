<?php
/**
 * CSVI Database class
 *
 * @package 	CSVI
 * @subpackage 	Database
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2012 RolandD Cyber Produksi
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: csvidb.php 2039 2012-07-14 18:16:58Z RolandD $
 */

defined('_JEXEC') or die;

/**
* @package CSVI
* @subpackage Database
 */
class CsviDb {

	private $_database = null;
	private $_error = null;

	public function __construct() {
		$this->_database = JFactory::getDBO();
	}

	public function setQuery($sql, $offset = 0, $limit = 0) {
		$this->_database->setQuery($sql, $offset, $limit);
		if (!$this->cur = $this->_database->query()) {
			$this->_error = $this->_database->getErrorMsg();
		}
	}

	public function getRow() {
		if (!is_object($this->cur)) $array = mysql_fetch_object($this->cur);
		else $array = $this->cur->fetch_object();
		if ($array) {
			return $array;
		}
		else {
			if (!is_object($this->cur)) mysql_free_result( $this->cur );
			else $this->cur->free_result();
			return false;
		}
	}

	public function getErrorMsg() {
		return $this->_error;
	}

	public function getNumRows() {
		return $this->_database->getNumRows($this->cur);
	}

	public function getQuery() {
		return $this->_database->getQuery();
	}
}
?>