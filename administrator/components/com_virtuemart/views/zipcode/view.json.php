<?php
/**
*
* State View
* @package	VirtueMart
* @subpackage State
* @author RickG, RolandD
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: view.json.php 6043 2012-05-21 21:40:56Z Milbo $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// Load the view framework
jimport( 'joomla.application.component.view');

/**
 * HTML View class for maintaining the state
 *
 * @package	VirtueMart
 * @subpackage State
 * @author RolandD, jseros
 */
class VirtuemartViewZipcode extends JView {

	function display($tpl = null) {

		$states = array();
		$db = JFactory::getDBO();
		//retrieving countries id
		$state_ids = JRequest::getString('virtuemart_state_id');
		$state_ids = explode(',', $state_ids);
		
		foreach($state_ids as $state_id){
			$q= 'SELECT `virtuemart_zipcode_id`, `zipcode` FROM `#__virtuemart_zipcode`  WHERE `virtuemart_state_id`= "'.(int)$state_id.'" 
				ORDER BY `#__virtuemart_states`.`zipcode`';
			$db->setQuery($q);
			
			$zipcodes[$state_id] = $db->loadAssocList();
		}
		
		echo json_encode($zipcodes);
	}
}
// pure php no closing tag
