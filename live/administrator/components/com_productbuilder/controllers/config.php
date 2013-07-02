<?php
/**
 * product builder component
 * @package productbuilder
 * @version $Id: controllers/config.php  2012-2-20 sakisTerzis $
 * @author Sakis Terzis (sakis@breakDesigns.net)
 * @copyright	Copyright (C) 2010-2012 breakDesigns.net. All rights reserved
 * @license	GNU/GPL v2
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controllerform');
//require_once(VMF_ADMINISTRATOR.DS.'controllers'.DS.'default.php');

class productbuilderControllerConfig extends JcontrollerForm
{
	function close(){
		$this->setRedirect('index.php?option=com_productbuilder');
	}
}
?>