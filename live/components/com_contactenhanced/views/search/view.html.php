<?php
/**
 * @version		1.6.0
 * @package		com_contactenhanced
 * @copyright	Copyright (C) 2006 - 2010 Ideal Custom Software Development
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');
jimport('joomla.mail.helper');

/**
 * HTML View class for the Contacts component
 *
 * @package		com_contactenhanced
* @since		1.5
 */
class ContactenhancedViewSearch extends JView
{
	protected $state;
	protected $items;
	protected $pagination;

	function display($tpl = null)
	{
		$app		= JFactory::getApplication();
		$params		= $app->getParams();

		// Get some data from the models
		$state		= $this->get('State');
		$items		= $this->get('Items');
		
		/**
		 * Search result only one item
		 */
		if(count($items) == 1){
			$link 	= JRoute::_('index.php?option=com_contactenhanced&view=contact&id='.$items[0]->slug, false);
			$msg	= JText::_('COM_CONTACTENHANCED_SEARCH_SINGLE_RESULT');
			//JRequest::setVar('Itemid','');
			$app->redirect($link, $msg);
			$app->close();
		}
		
		
		
		$pagination	= $this->get('Pagination');
		$pagination->setAdditionalUrlParam('view','search');
		if(JRequest::getInt('Itemid')){
			$pagination->setAdditionalUrlParam('Itemid',JRequest::getInt('Itemid'));
		}
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		$catids	= array();
		// Prepare the data.
		// Compute the contact slug.
		for ($i = 0, $n = count($items); $i < $n; $i++)
		{
			$item		= &$items[$i];
			$item->slug	= $item->alias ? ($item->id.':'.$item->alias) : $item->id;
			$temp		= new JRegistry();
			$temp->loadString($item->params, 'JSON');
			$item->params = clone($params);
			$item->params->merge($temp);

			if ($item->params->get('show_email', 0) == 1) {
				$item->email_to = trim($item->email_to);

				if (!empty($item->email_to) && JMailHelper::isEmailAddress($item->email_to)) {
					$item->email_to = JHtml::_('email.cloak', $item->email_to);
				}
				else {
					$item->email_to = '';
				}
			}
			$catids[]	= $item->catid;
		}
		
		$state->set('com_contactenhanced.search.catids',$catids);
		//echo ceHelper::print_r($items); exit;
		if(JRequest::getVar('layout', $params->get('search_results_layout')) == 'categories'){
			$categories		= $this->get('Categories');
			if ($categories) {
				JRequest::setVar('layout','categories');
			}else{
				JRequest::setVar('layout',null);
			}
		}
		
		$contactModel = JModel::getInstance('Contact', 'ContactenhancedModel', array('ignore_request' => true));
		
		// Manage the display mode for contact detail groups
		$contactModel->displayParamters($params,$item);
		
		$this->assignRef('state',		$state);
		$this->assignRef('categories',	$categories);
		$this->assignRef('items',		$items);
		$this->assignRef('params',		$params);
		$this->assignRef('pagination',	$pagination);

		if (isset($active->query['layout'])) {
			// We need to set the layout in case this is an alternative menu item (with an alternative layout)
			$this->setLayout($active->query['layout']);
		}

		$this->_prepareDocument();

		parent::display($tpl);
	}

	/**
	 * Prepares the document
	 */
	protected function _prepareDocument()
	{
		$app		= JFactory::getApplication();
		$menus		= $app->getMenu();
		$title 		= null;

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();

		if ($menu) {
			$this->params->def('page_heading', $menu->title);
		}
		else {
			$this->params->def('page_heading', JText::_('COM_CONTACTENHANCED_DEFAULT_PAGE_TITLE'));
		}


		$title = $this->params->get('page_title', '');

		if (empty($title)) {
			$title = htmlspecialchars_decode($app->getCfg('sitename'));
		}
		elseif ($app->getCfg('sitename_pagetitles', 0)) {
			$title = JText::sprintf('JPAGETITLE', htmlspecialchars_decode($app->getCfg('sitename')), $title);
		}

		$this->document->setTitle($title);

	}
}
