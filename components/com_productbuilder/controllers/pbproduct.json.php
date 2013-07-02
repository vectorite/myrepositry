<?php
/**
 * productbuilder component
 * @version $Id: controllers/pbproduct.php 2012-3-8 sakisTerzis $
 * @package productbuilder
 * @author Sakis Terzis (sakis@breakDesigns.net)
 * @copyright	Copyright (C) 2009-2012 breakDesigns.net. All rights reserved
 * @license	GNU/GPL v2
 */

defined('_JEXEC')or die('Restricted access');

jimport('joomla.application.component.controllerform');

/**
 * Controller for the PB product
 * @author	Sakis Terzis
 * @since	2.0
 */

class ProductbuilderControllerPbproduct extends JControllerForm{

	/**
	 * Function that returns in json form some data concerning selected vmproducts
	 * @author	Sakis Terzis
	 * @since	2.0
	 */
	function getinfo(){
		
		$jinput=JFactory::getApplication('site')->input;
		$vmproduct_id=$jinput->get('product_id','0','int');
		$pbgroup_order=$jinput->get('order','0','int');
		$nocustomfields=$jinput->get('nocustomfields','0','int');
		if($nocustomfields==1)$customfields=false;
		else $customfields=true;
		$quantity=$jinput->get('quantity','1','int');
		$load_img=$jinput->get('img','0','int');
		$model=$this->getModel();
		$result=$model->getVmProductInfo($vmproduct_id,$pbgroup_order,$quantity,$load_img,$customfields);
		echo json_encode($result);
	}

	/**
	 * Function that returns in json form the current price, calculating the select custom variants
	 * @author	Sakis Terzis
	 * @since	2.0
	 */
	function getPrice(){
		$jinput=JFactory::getApplication('site')->input;

		$vmproduct_id=$jinput->get('product_id','0','int');
		$quantity=$jinput->get('quantity','1','int');
		$customPrices=$this->getCustomPrices();
	
		$model=$this->getModel();
		$result=$model->getVmProductPrice($vmproduct_id,$customPrices,$quantity);
		$result_array=array('product_price'=>$result['salesPrice'],'discountAmount'=>$result['discountAmount']);
		echo json_encode($result_array);
	}

	/**
	 * Gets the customPrice values from the existing customFields array
	 * @since 	2.0
	 * @author	Sakis Terzis
	 * @return	Array
	 */
   function getCustomPrices(){
   	 	$vm_version=VmConfig::getInstalledVersion();
   	 	$versionCompare=version_compare(strtolower($vm_version), '2.0.7');
   	 	
		$jinput=JFactory::getApplication('site')->input;
		$customVariants=$jinput->get('customPrice',array(),'array');
		$customPrices = array();		
		$customVariants = JRequest::getVar('customPrice',array());	//is sanitized then
		foreach($customVariants as $customVariant){
			foreach($customVariant as $priceVariant=>$selected){ 
				//case to follow the changes in the VM versions
				if($versionCompare>0)$customPrices[$selected]=$priceVariant;
				else $customPrices[$priceVariant]=$selected;
			}
		}
		return $customPrices;
	}
}