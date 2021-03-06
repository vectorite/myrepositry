<?php
/**
 * @version		$Id: category.php 763 2012-01-04 15:07:52Z joomlaworks $
 * @package		Frontpage Slideshow
 * @author		JoomlaWorks http://www.joomlaworks.gr
 * @copyright	Copyright (c) 2006 - 2012 JoomlaWorks Ltd. All rights reserved.
 * @license		Commercial - This code cannot be redistributed without permission from JoomlaWorks Ltd.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

class FPSSModelCategory extends JModel {

	function getData() {
		$id = (int)$this->getState('id');
		$row = &JTable::getInstance('category', 'FPSS');
		$row->load($id);
		return $row;
	}

	function save() {
		$row = & JTable::getInstance('category', 'FPSS');
		if (!$row->bind($this->getState('data'))) {
			$this->setError($row->getError());
			return false;
		}
		if (!$row->check()) {
			$this->setError($row->getError());
			return false;
		}
		if(!$row->id) {
			$row->ordering = $row->getNextOrder();
		}
		if (!$row->store()) {
			$this->setError($row->getError());
			return false;
		}
		$this->setState('id', $row->id);
		return true;
	}

}
