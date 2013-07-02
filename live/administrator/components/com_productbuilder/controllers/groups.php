<?php
/**
* product builder component
* @package productbuilder
* @version controllers/groups.php  2012-2-6 sakisTerz $
* @author Sakis Terzis (sakis@breakDesigns.net)
* @copyright	Copyright (C) 2010-2012 breakDesigns.net. All rights reserved
* @license	GNU/GPL v2
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controlleradmin');
//require_once(VMF_ADMINISTRATOR.DS.'controllers'.DS.'default.php');

class productbuilderControllerGroups extends JControllerAdmin
{

	/**
	 * Proxy for getModel.
	 *
	 * @param	string	$name	The name of the model.
	 * @param	string	$prefix	The prefix for the PHP class name.
	 *
	 * @return	JModel
	 * @since	1.6
	 */
	public function getModel($name = 'Group', $prefix = 'productbuilderModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);

		return $model;
	}

		
       
	//copies the groups and all the related records
	function copy(){
	    $model = $this->getModel();
	    $result=$model->copy();
	    $type='message';
	
	    if($result==1) $msg = JText::_( 'COM_PRODUCTBUILDER_GROUPS_COPIED_SUCCESSFULLY' );
	    else {$msg = $result; $type='error';}
	
	    $link = 'index.php?option=com_productbuilder&view=groups';
	    $this->setRedirect($link, $msg,$type);
	}
   
}
?>