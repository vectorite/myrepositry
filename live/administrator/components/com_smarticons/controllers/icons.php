<?php
/**
 * @package SmartIcons Component for Joomla! 2.5
 * @version $Id: icons.php 9 2012-03-28 20:07:32Z Bobo $
 * @author SUTA Bogdan-Ioan
 * @copyright (C) 2011 SUTA Bogdan-Ioan
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 **/

// no direct access

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.controlleradmin');

class SmartIconsControllerIcons extends JControllerAdmin
{
	//Tasks
	public function getModel($name = 'Icon', $prefix = 'SmartIconsModel')
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}

	public function export() {
		
	}
	
}
?>