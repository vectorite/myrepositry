<?php
/**
 * product builder component
 * @package productbuilder
 * @version $Id: views/tags/view.html.php  2012-2-16 sakisTerz $
 * @author Sakis Terz (sakis@breakDesigns.net)
 * @copyright	Copyright (C) 2010-2012 breakDesigns.net. All rights reserved
 * @license	GNU/GPL v2
 */


jimport( 'joomla.application.component.view' );

class productbuilderViewTags extends JView
{
	protected $items;
	protected $pagination;
	protected $state;

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
		JToolBarHelper::title( JText::_( 'COM_PRODUCTBUILDER_TAGS' ),'pb');

		if(file_exists(JPATH_SITE . '/administrator/components/com_virtuemart/virtuemart.xml')){
			JToolBarHelper::addNew('tag.add');
			JToolBarHelper::deleteList('','tags.delete','JTOOLBAR_DELETE');
			JToolBarHelper::publish('tags.publish','JTOOLBAR_PUBLISH', true);
			JToolBarHelper::unpublish('tags.unpublish','JTOOLBAR_UNPUBLISH', true);
			JToolBarHelper::editList('tag.edit');
		}
	}
}
?>