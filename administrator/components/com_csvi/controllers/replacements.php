<?php
/**
 * Replacements controller
 *
 * @package 	CSVI
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2012 RolandD Cyber Produksi
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: replacements.php 1924 2012-03-02 11:32:38Z RolandD $
 */

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

jimport('joomla.application.component.controlleradmin');

/**
 * Replacements Controller
 *
 * @package    CSVI
 */
class CsviControllerReplacements extends JControllerAdmin {
	
	/**
	* Proxy for getModel
	*
	* @copyright
	* @author 		RolandD
	* @todo
	* @see
	* @access 		public
	* @param
	* @return 		object of a database model
	* @since 		4.0
	*/
	public function getModel($name = 'Replacement', $prefix = 'CsviModel') {
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}
	
}
?>
