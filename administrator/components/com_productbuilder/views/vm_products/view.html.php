<?php
/*
* product builder component
* @version $Id:1 view.html.php 1 17-Sept-2010 sakisTerz$
* @author Sakis Terz(sakis@breakDesigns.net)
* @copyright	Copyright (C) 2010-2012 breakDesigns.net. All rights reserved
* @license	GNU/GPL v3
*/


jimport( 'joomla.application.component.view' );

/**
 * @package Joomla
 * @subpackage Config
 */
class productbuilderViewVm_products extends JView
{
protected $items;
	protected $pagination;
	protected $state;

	function display($tpl = null)
	{
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');
		$this->group		= $this->get('Group');
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}	
		parent::display($tpl);
	}

}
?>