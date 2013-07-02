<?php
/**

 * @copyright	Copyright (C) 2006 - 2010 Ideal Custom Software Development
 * @author     Douglas Machado {@link http://ideal.fok.com.br}
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * About Page Model
 *
 * @package		com_contactenhanced
*/
class ContactenhancedModelMessages extends JModelList
{
	/**
	 * Constructor.
	 *
	 * @param	array	An optional associative array of configuration settings.
	 * @see		JController
	 * @since	1.6
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'id', 'msg.id',
				'name', 'msg.name',
				'alias', 'msg.alias',
				'checked_out', 'msg.checked_out',
				'checked_out_time', 'msg.checked_out_time',
				'catid', 'msg.catid', 'category_title',
				'user_id', 'msg.user_id',
				'state', 'msg.state',
				'access', 'msg.access', 'access_level',
				'created', 'msg.created',
				'created_by', 'msg.created_by',
				'ordering', 'msg.ordering',
				'featured', 'msg.featured',
				'language', 'msg.language',
				'publish_up', 'msg.publish_up',
				'publish_down', 'msg.publish_down',
				'value', 'msg.value',
				'subject', 'msg.subject',
				'contact', 'msg.contact',
				'contact_name', 'msg.contact_name',
				'from_email', 'msg.from_email',
				'from_name', 'msg.from_name',
				'ul.name', 'linked_user'
			);
		}

		parent::__construct($config);
	}
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

		// Adjust the context to support modal layouts.
		if ($layout = JRequest::getVar('layout', 'default')) {
			$this->context .= '.'.$layout;
		}

		$search = $app->getUserStateFromRequest($this->context.'.search', 'filter_search');
		$this->setState('filter.search', $search);

		$access = $app->getUserStateFromRequest($this->context.'.filter.access', 'filter_access', 0, 'int');
		$this->setState('filter.access', $access);

		$published = $app->getUserStateFromRequest($this->context.'.published', 'filter_published', '');
		$this->setState('filter.published', $published);

		$categoryId = $app->getUserStateFromRequest($this->context.'.category_id', 'filter_category_id');
		$this->setState('filter.category_id', $categoryId);

		$language = $app->getUserStateFromRequest($this->context.'.filter.language', 'filter_language', '');
		$this->setState('filter.language', $language);

		// List state information.
		parent::populateState('msg.id', 'desc');
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
		$id.= ':' . $this->getState('filter.search');
		$id.= ':' . $this->getState('filter.access');
		$id.= ':' . $this->getState('filter.published');
		$id.= ':' . $this->getState('filter.category_id');
		$id.= ':' . $this->getState('filter.language');

		return parent::getStoreId($id);
	}

	/**
	 * @param	boolean	True to join selected foreign information
	 *
	 * @return	string
	 */
	protected function getListQuery($resolveFKs = true)
	{
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'msg.*')
		);
		$query->from('#__ce_messages AS msg');
		
		// Join over the category
		$query->select('cat.title AS category_title');
		$query->join('LEFT', '`#__categories` AS cat ON cat.id = msg.catid');
		
		// Join over the Contact
		$query->select('con.name AS contact_name');
		$query->join('LEFT', '`#__ce_details` AS con ON con.id = msg.contact_id');
		
		// Join over the language
		$query->select('l.title AS language_title');
		$query->join('LEFT', '`#__languages` AS l ON l.lang_code = msg.language');

		// Join over the asset groups.
		$query->select('ag.title AS access_level');
		$query->join('LEFT', '#__viewlevels AS ag ON ag.id = msg.access');

		// Join over the categories.
	//	$query->select('c.title AS category_title');
	//	$query->join('LEFT', '#__categories AS c ON c.id = msg.catid');
		
		$query->where('msg.parent= ' . 0);
		
		// Filter by access level.
		if ($access = $this->getState('filter.access')) {
			$query->where('msg.access = ' . (int) $access);
		}

		// Filter by published state
		$published = $this->getState('filter.published');
		if (is_numeric($published)) {
			$query->where('msg.published = ' . (int) $published);
		} else if ($published === '') {
			$query->where('(msg.published = 0 OR msg.published = 1)');
		}

		// Filter by category.
		$categoryId = $this->getState('filter.category_id',JRequest::getVar('filter_category_id'));
		if (($categoryId)) {
			$query->where('msg.catid = ' . (int) $categoryId);
		}

		// Filter by search in  name
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$query->where('msg.id = '.(int) substr($search, 3));
			} else if (stripos($search, 'sender:') === 0) {
				$search = $db->Quote('%'.$db->getEscaped(substr($search, 7), true).'%');
				$query->where('(msg.from_name LIKE '.$search.' OR msg.from_email LIKE '.$search.')');
			} else {
				$search = $db->Quote('%'.$db->getEscaped($search, true).'%');
				$query->where('(msg.message LIKE '.$search.')');				
			}
		}
		
		// Filter on the language.
		if ($language = $this->getState('filter.language')) {
			$query->where('msg.language = ' . $db->quote($language));
		}

		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering', 'msg.id');
		$orderDirn	= $this->state->get('list.direction','DESC');
		$query->order($db->getEscaped($orderCol.' '.$orderDirn));
		
		//echo nl2br(str_replace('#__','jos_',$query)); exit;
		return $query;
	}
	
	function getDataToExport() 
	{
		$query = $this->getListQuery();
		$this->_db->setQuery($query);
     	$this->_data = $this->_db->loadAssocList();

		return $this->_data;
	}	
}
