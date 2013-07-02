<?php
/**
 * product builder component
 * @package productbuilder
 * @version $Id:2 product/view.html.php  2012-2-3 sakisTerz $
 * @author Sakis Terz(sakis@breakDesigns.net)
 * @copyright	Copyright (C) 2010-2012 breakDesigns.net. All rights reserved
 * @license	GNU/GPL v2
 */

jimport( 'joomla.application.component.view' );
class productbuilderViewProduct extends JView{
	protected $form;
	protected $item;
	protected $state;
	
	function display($tpl = null)
	{	
	$isNew=1;
	$name=null;

	// Get data from the model
	$this->item =& $this->get('Item');
	$this->form=$this->get('form');
	$this->state= $this->get('State');
	
	// Check for errors.
	if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
	}
	
	if($this->item){
		$isNew =0;
		$name=$this->item->name;
	}


	//create the buttons
	$text = $isNew ? JText::_( 'New' ) : JText::_( 'Edit' );
	JToolBarHelper::title('<small><small>[ ' .$text.' ]</small></small><small>'.JText::_('COM_PRODUCTBUILDER_CONF_PRODUCT').': '.$name.'</small>','pb'  );
	JToolBarHelper::apply('product.apply','JTOOLBAR_APPLY');
	JToolBarHelper::save('product.save','JTOOLBAR_SAVE');
	JToolBarHelper::save2new('product.save2new');
	if(!$isNew){
		JToolBarHelper::cancel('product.cancel','JTOOLBAR_CANCEL');
			
	}else{		
		JToolBarHelper::cancel('product.cancel','JTOOLBAR_CLOSE');
	}

	parent::display($tpl);
	}
}
?>