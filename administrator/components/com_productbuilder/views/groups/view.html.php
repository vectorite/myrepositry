<?php
/**
 * product builder component
 * @package productbuilder
 * @version $Id:1 views/groups/view.html.php  2012-2-6 sakisTerz $
 * @author Sakis Terzis (sakis@breakDesigns.net)
 * @copyright	Copyright (C) 2010-2012 breakDesigns.net. All rights reserved
 * @license	GNU/GPL v2
 */


jimport( 'joomla.application.component.view' );

class productbuilderViewGroups extends JView
{
	protected $items;
	protected $pagination;
	protected $state;
	protected $filters;

	function display($tpl = null)
	{
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');
		$this->filters=$this->get('Filters');
		
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
		JToolBarHelper::title( JText::_( 'COM_PRODUCTBUILDER_GROUPS' ),'pb');

		if(file_exists(JPATH_SITE . '/administrator/components/com_virtuemart/virtuemart.xml')){
			JToolBarHelper::addNew('group.add');			
			JToolBarHelper::custom('groups.copy','copy.png', 'copy_f2.png', 'Copy');
			JToolBarHelper::deleteList('','groups.delete','JTOOLBAR_DELETE');
			JToolBarHelper::publish('groups.publish','JTOOLBAR_PUBLISH', true);
			JToolBarHelper::unpublish('groups.unpublish','JTOOLBAR_UNPUBLISH', true);
			JToolBarHelper::editList('group.edit');
		}		
	}
}
?>