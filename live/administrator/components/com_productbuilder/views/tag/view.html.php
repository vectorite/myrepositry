<?php
/**
 * product builder component
 * @version $Id:1.3 tag/view.html.php  4-Jan-2011 sakisTerz $
 * @author Sakis Terz(sakis@breakDesigns.net)
 * @copyright	Copyright (C) 2010 breakDesigns.net. All rights reserved
 * @license	GNU/GPL v3
 */



jimport( 'joomla.application.component.view' );
class productbuilderViewTag extends JView{
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
		JToolBarHelper::title('<small><small>[ ' .$text.' ]</small></small><small>'.JText::_('Tag :').$name.'</small>','pb'  );
		JToolBarHelper::apply('tag.apply','JTOOLBAR_APPLY');
		JToolBarHelper::save('tag.save','JTOOLBAR_SAVE');
		JToolBarHelper::save2new('tag.save2new');
		if(!$isNew){
			JToolBarHelper::cancel('tag.cancel','JTOOLBAR_CANCEL');
				
		}else{
			JToolBarHelper::cancel('tag.cancel','JTOOLBAR_CLOSE');
		}

		parent::display($tpl);
	}
}
