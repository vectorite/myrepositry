<?php
/**
 * @version		1.6.0
 * @copyright	Copyright (C) 2006 - 2010 Ideal Custom Software Development
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

jimport('joomla.application.component.model');

/**
 * This models supports retrieving lists of contact categories.
 *
 * @package		com_contactenhanced
* @since		1.6
 */
class ContactenhancedModelCategories extends JModel
{
	/**
	 * Model context string.
	 *
	 * @var		string
	 */
	public $_context = 'com_contactenhanced.categories';

	/**
	 * The category context (allows other extensions to derived from this model).
	 *
	 * @var		string
	 */
	protected $_extension = 'com_contactenhanced';

	private $_parent = null;

	private $_items = null;

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since	1.6
	 */
	protected function populateState()
	{
		$app = JFactory::getApplication();
		$params	= $app->getParams();
		
		$this->setState('filter.extension', $this->_extension);

		// Get the parent id if defined.
		$parentId = JRequest::getInt('id');
		$this->setState('filter.parentId', $parentId);

		$params = $app->getParams();
		$this->setState('params', $params);
		// List state information
		$format = JRequest::getWord('format');
		if ($format=='feed') {
			$limit = $app->getCfg('feed_limit');
		}
		else {
			$limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'));
		}

		// No limit for this view
		$limit = 99999;
		$limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $limit);
	//	echo $limit; exit;
		$this->setState('list.limit', $limit);


		$limitstart = JRequest::getVar('limitstart', 0, '', 'int');
		$this->setState('list.start', $limitstart);

		//Get default value from parameters
		$ordering	= explode(' ', $params->get('category_ordering','ordering ASC'));
		
		$orderCol	= JRequest::getCmd('filter_order', $ordering[0]);
		$this->setState('list.ordering', $orderCol);

		$listOrder	=  JRequest::getCmd('filter_order_Dir', $ordering[1]);
		$this->setState('list.direction', $listOrder);
		
		$this->setState('filter.published',	1);
		$this->setState('filter.access',	true);
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param	string		$id	A prefix for the store id.
	 *
	 * @return	string		A store id.
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':'.$this->getState('filter.extension');
		$id	.= ':'.$this->getState('filter.published');
		$id	.= ':'.$this->getState('filter.access');
		$id	.= ':'.$this->getState('filter.parentId');

		return parent::getStoreId($id);
	}

	/**
	 * redefine the function an add some properties to make the styling more easy
	 *
	 * @return mixed An array of data items on success, false on failure.
	 */
	public function getItems()
	{
		if(!count($this->_items))
		{
			$app = JFactory::getApplication();
			$menu = $app->getMenu();
			$active = $menu->getActive();
			$params = new JRegistry();
			if($active)
			{
				$params->loadJSON($active->params);
			}
			$options = array();
			$options['countItems'] = $params->get('show_cat_items_cat', 1) || !$params->get('show_empty_categories_cat', 0);
			$categories = JCategories::getInstance('Contactenhanced', $options);
			$this->_parent = $categories->get($this->getState('filter.parentId', 'root'));
			if(is_object($this->_parent))
			{
				$this->_items = $this->_parent->getChildren();
			} else {
				$this->_items = false;
			}
		}

		return $this->_items;
	}
	
	/**
	 * Gets a list of categories and contacts
	 * @param array
	 * @return array
	 */
	function getCategoriesContacts( $options=array() )
	{
		$app	= JFactory::getApplication();
		if(!count($this->_items))
		{
			$categories	= $this->getItems();
		}else
		{
			$categories	= $this->_items;
		}
		
		require_once (JPATH_SITE.'/components/com_contactenhanced/models/category.php');
		
		$k = 0;
		for($i=0;$i<count($categories); $i++){
			$category =& $categories[$i];
			JRequest::setVar('category_id', $category->id);
			$catModel	= new ContactenhancedModelCategory();
			//remove limit/pagination
			$limit = 99999;
			$limit = $app->setUserState('global.list.limit',  $limit);
			$this->setState('list.limit', $limit);
			$category->contacts =   $catModel->getItems();
			$category->link = JRoute::_('index.php?option=com_contactenhanced&view=category&catid='.$category->slug);
			$category->odd	= $k;
			$category->count= $i;
			$k = 1 - $k;
		}
		JRequest::setVar('category_id',null);
		return $categories;
	}

	public function getParent()
	{
		if(!is_object($this->_parent))
		{
			$this->getItems();
		}
		return $this->_parent;
	}
}