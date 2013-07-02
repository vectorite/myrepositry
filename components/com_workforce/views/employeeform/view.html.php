<?php
/**
 * @version 2.0.1 2012-08-17
 * @package Joomla
 * @subpackage Work Force
 * @copyright (C) 2012 the Thinkery
 * @license GNU/GPL see LICENSE.php
 */

// No direct access
defined('_JEXEC') or die;
jimport('joomla.application.component.view');

class WorkforceViewEmployeeForm extends JView
{
	protected $form;
	protected $item;
	protected $return_page;
	protected $state;
    protected $settings;
    protected $dispatcher;
    protected $wftitle;

	public function display($tpl = null)
	{
		// Initialise variables.
		$app		= JFactory::getApplication();
        $document   = JFactory::getDocument();
		$user		= JFactory::getUser();

		// Get model data.
		$this->state		= $this->get('State');
		$this->item			= $this->get('Item');
		$this->form			= $this->get('Form');
		$this->return_page	= $this->get('ReturnPage');
        $this->settings     = JComponentHelper::getParams('com_workforce');
        
        $document->addStyleSheet(JURI::root(true).'/components/com_workforce/assets/css/workforce.css');

        JHTML::_('behavior.tooltip');
        JHTML::_('behavior.modal');
        JPluginHelper::importPlugin( 'workforce' );
        $this->dispatcher = JDispatcher::getInstance();

		if (empty($this->item->id)) {
			$authorised = $user->authorise('core.admin', 'com_workforce');
		}
		else {
			$authorised = $authorised = (($user->get('id') == $this->item->user_id) || $user->authorise('core.admin', 'com_workforce'));
		}

		if (!$authorised) {
			JError::raiseError(403, JText::_('JERROR_ALERTNOAUTHOR'));
			return false;
		}

		if (!empty($this->item)) {
			$this->form->bind($this->item);         
		}

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseWarning(500, implode("\n", $errors));
			return false;
		}

		// Create a shortcut to the parameters.
		$params	= &$this->state->params;

		//Escape strings for HTML output
		$this->pageclass_sfx = htmlspecialchars($params->get('pageclass_sfx'));

		$this->params	= $params;
		$this->user		= $user;

		$this->_prepareDocument();
		parent::display($tpl);
	}

	protected function _prepareDocument()
	{
		$app		= JFactory::getApplication();
		$menus		= $app->getMenu();
		$pathway	= $app->getPathway();
		$title 		= null;

		$menu = $menus->getActive();
        if ($menu) {
            $this->params->def('page_heading', $this->params->get('page_title', $menu->title));
        } else {
            $this->params->def('page_heading', JText::_( 'COM_WORKFORCE_FORM_EDIT_EMPLOYEE' ));
        }

        $title = ($this->item->id) ? JText::_('JACTION_EDIT').': '.$this->item->name : JText::_( 'COM_WORKFORCE_FORM_ADD_EMPLOYEE' );
        $this->wftitle = $title;
        if (empty($title)) {
			$title = $app->getCfg('sitename');
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 1) {
			$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 2) {
			$title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
		}
		$this->document->setTitle($title);

		$pathway = $app->getPathWay();
		$pathway->addItem($title, '');

		if ($this->params->get('menu-meta_description'))
		{
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}

		if ($this->params->get('menu-meta_keywords'))
		{
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}

		if ($this->params->get('robots'))
		{
			$this->document->setMetadata('robots', $this->params->get('robots'));
		}
	}
}