<?php
/**
 *
 * CSVI Controller
 *
 * @package 	CSVI
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2012 RolandD Cyber Produksi
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: controller.php 1924 2012-03-02 11:32:38Z RolandD $
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// Load the controller framework
jimport('joomla.application.component.controller');

/**
 * Base controller
 *
 * @package CSVI
 */
class CsviController extends JController {

	/**
	* Method to display the view
	*
	* @access	public
	*/
	public function display($cachable = false, $urlparams = false) {
		
		parent::display($cachable, $urlparams);

		return $this;
	}
}
?>
