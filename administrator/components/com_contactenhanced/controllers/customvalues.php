<?php
/**
 * @copyright	Copyright (C) 2006 - 2010 Ideal Custom Software Development
 * @author     Douglas Machado {@link http://ideal.fok.com.br}
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');

/**
 * @package		com_contactenhanced
* @since	1.6
 */
class ContactenhancedControllerCustomvalues extends JControllerAdmin
{
	
	/**
	 * Proxy for getModel
	 * @since	1.6
	 */
	function &getModel($name = 'Customfields', $prefix = 'ContactenhancedModel')
	{
		$tasks = array('saveorder','publish','unpublish','archive', 'trash','report', 'orderup', 'orderdown', 'delete');
		if( in_array($this->getTask(), $tasks) ){
			$model = parent::getModel('Customvalue', $prefix, array('ignore_request' => true));
		}else{
			$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		}
		
		return $model;
	}
	
	
}