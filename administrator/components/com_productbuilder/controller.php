<?php
/**
 * productbuilder component
 * @version $Id: controller.php 1 2012-2-22 sakisTerzis $
 * @package productbuilder
 * @author Sakis Terzis (sakis@breakDesigns.net)
 * @copyright	Copyright (C) 2009-2012 breakDesigns.net. All rights reserved
 * @license	GNU/GPL v2
 */

defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controller');


class ProductbuilderController extends JController{

	public function display($cachable = false, $urlparams = false){
		require_once JPATH_COMPONENT.'/helpers/helper.php';
		// Load the submenu.
		$layout=JRequest::getVar('layout');
		$view=JRequest::getVar('view','productbuilder');
		if(($layout!='edit' && ($view!='productbuilder')) || $view=='config')pbHelper::addSubmenu(JRequest::getCmd('view', 'productbuilder'));
		parent::display($cachable = false, $urlparams = false);
		return $this;
	}
	
	/**
	 * Function to get version info
	 */
	public function getVersionInfo(){
		$model=$this->getModel(); 
		$html_result=$model->getVersionInfo();
		if($html_result)echo json_encode($html_result);
		else echo '';
		jexit();
	}
	
}
?>