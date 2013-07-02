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
		parent::display($cachable = false, $urlparams = false);
		return $this;
	}
	
	/**
	 * Delete the pbproduct from the cart
	 * 
	 */
	public function deletepbproduct(){
		$app=JFactory::getApplication();	
		$jinput=$app->input;
		$pb_product_cart_id=$jinput->get('pbproduct_id','','cmd');
		if(!empty($pb_product_cart_id)){
			$language=JFactory::getLanguage();
			$language->load('com_virtuemart');
			
			$model=$this->getModel('Pbproduct');
			if($model->deletepbproductCart($pb_product_cart_id)){
				$app->enqueueMessage(JText::_('COM_VIRTUEMART_PRODUCT_REMOVED_SUCCESSFULLY'));
			}else{
				$app->enqueueMessage(JText::_('COM_VIRTUEMART_PRODUCT_NOT_REMOVED_SUCCESSFULLY'), 'error');
			}			
		}
		$app->redirect(JRoute::_('index.php?option=com_virtuemart&view=cart'));		
	}
}
?>