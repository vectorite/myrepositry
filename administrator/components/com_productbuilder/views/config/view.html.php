<?php
/**
 * product builder component
 * @package productbuilder
 * @version $Id:config/view.html.php  2012-2-20 sakisTerz $
 * @author Sakis Terzis(sakis@breakDesigns.net)
 * @copyright	Copyright (C) 2010-2012 breakDesigns.net. All rights reserved
 * @license	GNU/GPL v2
 */

jimport( 'joomla.application.component.view' );
class productbuilderViewConfig extends JView{
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
	
	//create the buttons	
	JToolBarHelper::title(JText::_('COM_PRODUCTBUILDER_SETTINGS'),'pb');
	JToolBarHelper::apply('config.apply','JTOOLBAR_APPLY');
	JToolBarHelper::cancel('config.close','JTOOLBAR_CLOSE');

	parent::display($tpl);
	}
}
?>