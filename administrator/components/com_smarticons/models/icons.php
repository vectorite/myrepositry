<?php
/**
 * @package SmartIcons Component for Joomla! 2.5
 * @version $Id: icons.php 9 2012-03-28 20:07:32Z Bobo $
 * @author SUTA Bogdan-Ioan
 * @copyright (C) 2011 SUTA Bogdan-Ioan
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport( 'joomla.application.component.modellist' );

class SmartIconsModelIcons extends JModelList {
	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'Icon.idIcon', 'Icon.ordering',
				'Icon.Title', 'CategoryTitle',
				'Icon.Display', 'Icon.published'
			);
		}

		parent::__construct($config);
	}
	protected function getListQuery() {
		// Create a new query object.
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		// Select icon fields
		$query->select('Icon.idIcon, Icon.asset_id, Icon.catid, Icon.Name, Icon.Title, Icon.Target');
		$query->select('Icon.Display, Icon.published, Icon.ordering, Icon.checked_out, Icon.checked_out_time');
		// From the icon table
		$query->from('#__com_smarticons AS Icon');
		
		//Select category fields
		$query->select('Category.title AS CategoryTitle');
		//Join with categories
		$query->join('LEFT OUTER', '#__categories AS Category ON Icon.catid = Category.id');

		//Select the checked out user
		$query->select('User.name AS editor');
		//Join with the users table
		$query->join('LEFT OUTER', '#__users AS User ON Icon.checked_out = User.id');
		
		// Filter by a single or group of categories.
		$categoryId = $this->getState('filter.category_id');
		if (is_numeric($categoryId)) {
			$query->where('Icon.catid = '.(int) $categoryId);
		}
		
		// Filter by search in title.
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$query->where('Icon.idIcon = '.(int) substr($search, 3));
			}
			else if (stripos($search, 'tab:') === 0) {
				$search = $db->Quote('%'.$db->getEscaped(substr($search, 4), true).'%');
				$query->where('(Category.title LIKE '.$search.')');
			}
			else {
				$search = $db->Quote('%'.$db->getEscaped($search, true).'%');
				$query->where('(Icon.Title LIKE '.$search.' OR Icon.Target LIKE '.$search.')');
			}
		}
		
		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering');
		$orderDirn	= $this->state->get('list.direction');
		if ($orderCol == 'Icon.ordering' || $orderCol == 'Category.title') {
			$orderCol = 'Category.title '.$orderDirn.', Icon.ordering';
		}
		$query->order($db->getEscaped($orderCol.' '.$orderDirn));
		return $query;
	}
	
	protected function populateState($ordering = null, $direction = null)
	{
		$search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);
		
		$categoryId = $this->getUserStateFromRequest($this->context.'.filter.category_id', 'filter_category_id');
		$this->setState('filter.category_id', $categoryId);
		
		// List state information.
		parent::populateState('Icon.ordering', 'ASC');
	}
	
	public function getItemsForExport() {
		// Create a new query object.
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		
		// Select icons
		$query->select('*');
		
		// From the icon table
		$query->from('#__com_smarticons');
		
		$icons = $this->_getList($query);
		
		//Select categories
		$query = $db->getQuery(true);
		$query->select('id, title, alias, description');
		
		// From the icon table
		$query->from('#__categories');
		
		//Limit results only to our extension
		$query->where('extension = \'com_smarticons\'');
		
		$categories = $this->_getList($query);
		
		$result = array();
		$result['icons'] = $icons;
		$result['categories'] = $categories;
		
		return $result;
	}
}
