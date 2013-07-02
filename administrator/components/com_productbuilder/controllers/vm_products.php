<?php
/**
 * product builder component
 * @version $Id:1 controllers/vm_products.php 1 22 Sept-2010 sakisTerz$
 * @author Sakis Terz(sakis@breakDesigns.net)
 * @copyright	Copyright (C) 2010 breakDesigns.net. All rights reserved
 * @license	GNU/GPL v3
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controlleradmin');
//require_once(VMF_ADMINISTRATOR.DS.'controllers'.DS.'default.php');

class productbuilderControllerVm_products extends JControllerAdmin
{


	function setGrProducts(){
		$model = $this->getModel('Vm_products');
		$msgtype='';
		
		if ($model->setGrProducts()) {
			$msg = JText::_( 'COM_PRODUCTBUILDER_VMPRODUCTS_ASSIGNMENT_SUCCESS' );
		} else {
			$errors=$model->getErrors();
			$msg = JText::sprintf( 'COM_PRODUCTBUILDER_VMPRODUCTS_ASSIGNMENT_ERROR_SAVING' ,implode('</br>',$errors));
			//$msg = implode('</br>',$errors);
			$msgtype='error';			
		}

		// Check the table in so it can be edited.... we are done with it anyway
		$link = 'index.php?option=com_productbuilder&view=vm_products&tmpl=component&viewtype=assignproducts&pb_group_id='.$_SESSION['pb_group_id'];
		$this->setRedirect($link, $msg,$msgtype);
		return;
	}

	function setDefProduct(){
		$model = $this->getModel('Vm_products');

		if ($model->setDefProduct()) {
			$msg = JText::_( 'COM_PRODUCTBUILDER_VMPRODUCT_ASSIGNMENT_SUCCESS' );
		} else {
			$errors=$model->getErrors();
			$msg = JText::sprintf( 'COM_PRODUCTBUILDER_VMPRODUCTS_ASSIGNMENT_ERROR_SAVING' ,implode('</br>',$errors));
			//$msg = implode('</br>',$errors);
			$msgtype='error';
		}

		$editable=JRequest::getInt('editable',1);
		$query_string='&editable='.$editable;
		if($editable==1){
			$connectWith=JRequest::getInt('conectwith',0);
			$query_string.='&conectwith='.$connectWith;
			//get the connected categories
			$cat_query='';
			if($connectWith==0){
				$vm_categories=JRequest::getVar('cat_ids',array(),'','array');
				JArrayHelper::toInteger($vm_categories);

				foreach($vm_categories as $vmc){
					$query_string.='&cat_ids[]='.$vmc;
				}
			}
		}
		// Check the table in so it can be edited.... we are done with it anyway
		$link = 'index.php?option=com_productbuilder&view=vm_products&tmpl=component&viewtype=assigndefproduct&pb_group_id='.$_SESSION['pb_group_id'].$query_string;
		$this->setRedirect($link, $msg,$msgtype);
		return;
	}

	function error(){
		echo '<h3>'.JText::_('You can select products only if you choose the').'<br/><i>'.JText::_('connect with:VM products option').'</i></h3>';
		return;
	}
}
?>