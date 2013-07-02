<?php
/*
* product builder component
* @version $Id:1 views/group/view.html.php  20-Sept-2010 sakisTerz $
* @author Sakis Terz (sakis@breakDesigns.net)
* @copyright	Copyright (C) 2010 breakDesigns.net. All rights reserved
* @license	GNU/GPL v3
*/


defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');

class productbuilderViewGroup extends JView{
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
	JToolBarHelper::title('<small><small>[ ' .$text.' ]</small></small><small>'.JText::_('COM_PRODUCTBUILDER_GROUP').': '.$name.'</small>','pb'  );
	JToolBarHelper::apply('group.apply','JTOOLBAR_APPLY');
	JToolBarHelper::save('group.save','JTOOLBAR_SAVE');
	JToolBarHelper::save2new('group.save2new');
	if(!$isNew){
		JToolBarHelper::cancel('group.cancel','JTOOLBAR_CANCEL');
			
	}else{		
		JToolBarHelper::cancel('group.cancel','JTOOLBAR_CLOSE');
	}

	parent::display($tpl);
	}
}