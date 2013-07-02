<?php

/**

*

* Data module for shop countries

*

* @package	VirtueMart

* @subpackage Country

* @author RickG, Max Milbers, jseros

* @link http://www.virtuemart.net

* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.

* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php

* VirtueMart is free software. This version may have been modified pursuant

* to the GNU General Public License, and as distributed it includes or

* is derivative of works licensed under the GNU General Public License or

* other free or open source software licenses.

* @version $Id: state.php 6008 2012-05-07 14:23:48Z Milbo $

*/



// Check to ensure this file is included in Joomla!

defined('_JEXEC') or die('Restricted access');



// Load the model framework

jimport( 'joomla.application.component.model');



if(!class_exists('VmModel'))require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'vmmodel.php');



/**

 * Model class for shop countries

 *

 * @package	VirtueMart

 * @subpackage State

 * @author RickG, Max Milbers

 */

class VirtueMartModelZipcode extends VmModel {





	/**

	 * constructs a VmModel

	 * setMainTable defines the maintable of the model

	 * @author Max Milbers

	 */

	function __construct() {

		parent::__construct('virtuemart_zipcode_id');

		$this->setMainTable('zipcodes');

		$this->_selectedOrderingDir = 'ASC';

	}



    /**

     * Retrieve the detail record for the current $id if the data has not already been loaded.

     *

     * Renamed to getSingleState to avoid overwriting by jseros

     *

     * @author Max Milbers

     */

	function getSingleZipcode(){



		if (empty($this->_data)) {

   			$this->_data = $this->getTable('zipcodes');

   			$this->_data->load((int)$this->_id);

  		}



		return $this->_data;

	}





	/**

	 * Retireve a list of countries from the database.

	 *

     * @author RickG, Max Milbers

	 * @return object List of state objects

	 */

	public function getZipcodes($stateId, $noLimit=false)

	{

		$quer= 'SELECT * FROM `#__virtuemart_zipcode`  WHERE `virtuemart_state_id`= "'.(int)$stateId.'"

				ORDER BY `#__virtuemart_zipcode`.`zip_code`';



		if ($noLimit) {

		    $this->_data = $this->_getList($quer);

		}

		else {

		    $this->_data = $this->_getList($quer, $this->getState('limitstart'), $this->getState('limit'));

		}



		if(count($this->_data) >0){

			$this->_total = $this->_getListCount($quer);

		}



		return $this->_data;

	}



	/**

	 * Tests if a state and country fits together and if they are published

	 *

	 * @author Max Milbers

	 * @return String Attention, this function gives a 0=false back in case of success

	 */

	public function testZipcodeState($stateId,$zipcodeId)

	{

		$zipcodeId = (int)$zipcodeId;

		$stateId = (int)$stateId;



		$db = JFactory::getDBO();

		$q = 'SELECT * FROM `#__virtuemart_states` WHERE `virtuemart_state_id`= "'.$stateId.'"';

		$db->setQuery($q);

		if($db->loadResult()){

			//Test if country has states

			$q = 'SELECT * FROM `#__virtuemart_zipcode`  WHERE `virtuemart_zipcode_id`= "'.$zipcodeId.'" ';

			$db->setQuery($q);

			if($db->loadResult()){

				//Test if virtuemart_state_id fits to virtuemart_country_id

				$q = 'SELECT * FROM `#__virtuemart_zipcode` WHERE `virtuemart_state_id`= "'.$stateId.'" AND `virtuemart_zipcode_id`="'.$zipcodeId.'"';

				$db->setQuery($q);

				if($db->loadResult()){

					return true;

				} else {

					//There is a country, but the state does not exist or is unlisted

					return false;

				}

			} else {

				//This country has no states listed

				return true;

			}



		} else {

			//The given country does not exist, this can happen, when no country was choosen, which maybe valid.

			return true;

		}

	}



}

// pure php no closing tag