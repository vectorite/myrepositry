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
require_once JPATH_COMPONENT.'/models/category.php';

/**
 * HTML Contact View class for the Contact component
 *
 * @package		com_contactenhanced
* @since 		1.5
 */
class ContactenhancedViewContact extends JView
{
	protected $state;
	public 	$item;

	function display($tpl = null)
	{
		
		// Initialise variables.
		$app		= JFactory::getApplication();
		$user		= JFactory::getUser();
		$dispatcher = JDispatcher::getInstance(); 
		$state		= $this->get('State');
		$item		= $this->get('Item');
		$model		= &$this->getModel();
		$customfields	= $model->getCustomFields( $item->catid);
		
	// Get the parameters of the active menu item
		$menus	= $app->getMenu();
		$menu	= $menus->getActive();
		$params = JComponentHelper::getParams('com_contactenhanced');

		$params->merge($item->params);
		
		if(is_object($menu) AND isset($menu->params) ){
			$params->merge($menu->params);
		}
		
		// Get Category Model data
		if ($item) {
			$categoryModel = JModel::getInstance('Category', 'ContactenhancedModel', array('ignore_request' => true));
			$categoryModel->setState('category.id', $item->catid);
			$ordering	= explode(' ',$params->get('contact_ordering','a.name ASC'));
			$categoryModel->setState('list.ordering', $ordering[0]);
			$categoryModel->setState('list.direction', $ordering[1]);
			$contacts = $categoryModel->getItems();
		}

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseWarning(500, implode("\n", $errors));

			return false;
		}

		
		
		//echo ceHelper::print_r($menu->params); exit;
		// check if access is not public
		$groups	= $user->getAuthorisedViewLevels();

		$return = '';

		if ((!in_array($item->access, $groups)) || (!in_array($item->category_access, $groups))) {
			$uri		= JFactory::getURI();
			$return		= (string)$uri;

			JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));
			return;
		}

		$options['category_id']	= $item->catid;
		$options['order by']	= 'a.default_con DESC, a.ordering ASC';


		// Handle email cloaking
		if ($item->email_to && $params->get('show_email')) {
			$item->email_to = JHtml::_('email.cloak', $item->email_to);
		}

		
		// Manage the display mode for contact detail groups
		$model->displayParamters($params,$item);

		// Add links to contacts
		if ($params->get('show_contact_list') && count($contacts) > 1) {
			foreach($contacts as &$contact)
			{
				$contact->link = JRoute::_(ContactenchancedHelperRoute::getContactRoute($contact->slug, $contact->catid));
			}
			$item->link = JRoute::_(ContactenchancedHelperRoute::getContactRoute($item->slug, $item->catid));
		}

		//JHtml::_('behavior.formvalidation');

		

		// Override the layout only if this is not the active menu item
		// If it is the active menu item, then the view and item id will match
		$active	= $app->getMenu()->getActive();
		if ((!$active) 
			|| ((strpos($active->link, 'view=contact') === false) 
			|| (strpos($active->link, '&id=' . (string) $item->id) === false))) {
			if ($layout = $params->get('contact_layout')) {
				$this->setLayout($layout);
			}
		}
		elseif (isset($active->query['layout'])) {
			// We need to set the layout in case this is an alternative menu item (with an alternative layout)
			$this->setLayout($active->query['layout']);
		}
		
		if($item->params->get('processplugins')){
			if($item->sidebar){
				$item->text = $item->sidebar;
				ceHelper::processContentPlugin($item->params, $item);
				$item->sidebar = $item->text;
			}
			if($item->misc){
				$item->text = $item->misc;
				ceHelper::processContentPlugin($item->params, $item);
				$item->misc = $item->text;
			}
		}
		
		$this->assignRef('contact',		$item);
		$this->assignRef('params',		$params);
		$this->assignRef('return',		$return);
		$this->assignRef('state', 		$state);
		$this->assignRef('item', 		$item);
		$this->assignRef('user', 		$user);
		$this->assignRef('contacts', 	$contacts);
		$this->assignRef('customfields', 	$customfields);
		
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
		$pathway	= $app->getPathway();
		$title 		= null;

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();

		if ($menu) {
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		}
		else {
			$this->params->def('page_heading', JText::_('COM_CONTACTENHANCED_DEFAULT_PAGE_TITLE'));
		}
		
		$title = $this->params->get('page_title', '');
		
		$id = (int) @$menu->query['id'];

		// if the menu item does not concern this contact
		if ($menu && ($menu->query['option'] != 'com_contactenhanced' || $menu->query['view'] != 'contact' || $id != $this->item->id)) 
		{
			
			// If this is not a single contact menu item, set the page title to the contact title
			if ($this->item->name) {
				$title = $this->item->name;
			}
			$path = array(array('title' => $this->contact->name, 'link' => ''));
			$category = JCategories::getInstance('Contactenhanced')->get($this->contact->catid);

			while ($category && ($menu->query['option'] != 'com_contactenhanced' || $menu->query['view'] == 'contact' || $id != $category->id) && $category->id > 1)
			{
				$path[] = array('title' => $category->title, 'link' => ContactenchancedHelperRoute::getCategoryRoute($this->contact->catid));
				$category = $category->getParent();
			}

			$path = array_reverse($path);

			foreach($path as $item)
			{
				$pathway->addItem($item['title'], $item['link']);
			}
		}

		if (empty($title)) {
			$title = htmlspecialchars_decode($app->getCfg('sitename'));
		}
		elseif ($app->getCfg('sitename_pagetitles', 0)) {
			$title = JText::sprintf('JPAGETITLE', htmlspecialchars_decode($app->getCfg('sitename')), $title);
		}

		if (empty($title)) {
			$title = $this->item->name;
		}
		$this->document->setTitle($title);		
		
		// Menu item prevail
		if ($this->params->get('menu-meta_description',$this->item->metadesc))
		{
			$this->document->setDescription($this->params->get('menu-meta_description',$this->item->metadesc));
		}

		if ($this->params->get('menu-meta_keywords',$this->item->metakey))
		{
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords',$this->item->metakey));
		}

		if ($this->params->get('robots'))
		{
			$this->document->setMetadata('robots', $this->params->get('robots'));
		}

		$mdata = $this->item->metadata->toArray();

		foreach ($mdata as $k => $v)
		{
			if ($v) {
				$this->document->setMetadata($k, $v);
			}
		}
	}
}
