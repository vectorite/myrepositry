<?php
/**
 * Joomla Components table
 *
 * @package 	CSVI
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2012 RolandD Cyber Produksi
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: csvimproved.php 1924 2012-03-02 11:32:38Z RolandD $
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

/**
* @package CSVI
 */
class TableCsvimproved extends JTable {
	
	function bind($array, $ignore = '') {
        if (key_exists( 'params', $array ) && is_array( $array['params'] )) {
                $registry = new JRegistry();
                $registry->loadArray($array['params']);
                $array['params'] = $registry->toString();
        }
        return parent::bind($array, $ignore);
    }
}
?>