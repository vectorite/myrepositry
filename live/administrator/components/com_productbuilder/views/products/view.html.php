<?php
/**
 * product builder component
 * @package productbuilder
 * @version $Id:view.html.php 1 2012-3-2 sakisTerz$
 * @author Sakis Terzis(sakis@breakDesigns.net)
 * @copyright	Copyright (C) 2010-2012 breakDesigns.net. All rights reserved
 * @license	GNU/GPL v2
 */


jimport( 'joomla.application.component.view' );


class productbuilderViewProducts extends JView
{
	protected $items;
	protected $pagination;
	protected $state;

	function display($tpl = null)
	{
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}


		if ($this->getLayout() !== 'modal') {
			$this->addToolbar();
		}

		parent::display($tpl);
	}

	protected function addToolbar()
	{
		JToolBarHelper::title( JText::_( 'COM_PRODUCTBUILDER_CONF_PRODUCTS' ),'pb');

		if(file_exists(JPATH_SITE . '/administrator/components/com_virtuemart/virtuemart.xml')){
			JToolBarHelper::addNew('product.add');
			JToolBarHelper::deleteList('','products.delete','JTOOLBAR_DELETE');
			JToolBarHelper::publish('products.publish','JTOOLBAR_PUBLISH', true);
			JToolBarHelper::unpublish('products.unpublish','JTOOLBAR_UNPUBLISH', true);
			JToolBarHelper::editList('product.edit');}

	}
}
?>