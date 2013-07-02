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
class ContactenhancedModelCustomfields extends JModelList
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
				'id', 'cf.id',
				'name', 'cf.name',
				'title', 'cf.title',
				'alias', 'cf.alias',
				'checked_out', 'cf.checked_out',
				'checked_out_time', 'cf.checked_out_time',
				'catid', 'cf.catid', 'category_title',
				'user_id', 'cf.user_id',
				'state', 'cf.state',
				'published', 'cf.published',
				'access', 'cf.access', 'access_level',
				'created', 'cf.created',
				'created_by', 'cf.created_by',
				'ordering', 'cf.ordering',
				'featured', 'cf.featured',
				'language', 'cf.language',
				'publish_up', 'cf.publish_up',
				'publish_down', 'cf.publish_down',
				'ul.name', 'linked_user',
				'cf.type', 'type'
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
		parent::populateState('cf.ordering', 'asc');
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
				'cf.id, cf.name, cf.required, cf.value, cf.type, cf.published, cf.access, cf.ordering, cf.catid, cf.language')
		);
		$query->from('#__ce_cf AS cf');

		// Join over the language
		$query->select('l.title AS language_title');
		$query->join('LEFT', '`#__languages` AS l ON l.lang_code = cf.language');

		// Join over the asset groups.
		$query->select('ag.title AS access_level');
		$query->join('LEFT', '#__viewlevels AS ag ON ag.id = cf.access');

		// Join over the categories.
		$query->select('c.title AS category_title');
		$query->join('LEFT', '#__categories AS c ON c.id = cf.catid');


		// Filter by access level.
		if ($access = $this->getState('filter.access')) {
			$query->where('cf.access = ' . (int) $access);
		}

		// Filter by published state
		$published = $this->getState('filter.published');
		if (is_numeric($published)) {
			$query->where('cf.published = ' . (int) $published);
		} else if ($published === '') {
			$query->where('(cf.published = 0 OR cf.published = 1)');
		}

		// Filter by category.
		$categoryId = $this->getState('filter.category_id');
		if (is_numeric($categoryId)) {
			$query->where('cf.catid = ' . (int) $categoryId);
		}

		// Filter by search in  name
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$query->where('cf.id = '.(int) substr($search, 3));
			} else if (stripos($search, 'author:') === 0) {
				$search = $db->Quote('%'.$db->getEscaped(substr($search, 7), true).'%');
				$query->where('(ucf.name LIKE '.$search.' OR ucf.username LIKE '.$search.')');
			} else {
				$search = $db->Quote('%'.$db->getEscaped($search, true).'%');
				$query->where('(cf.name LIKE '.$search.')');				
			}
		}
		
		// Filter on the language.
		if ($language = $this->getState('filter.language')) {
			$query->where('cf.language = ' . $db->quote($language));
		}

		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering');
		$orderDirn	= $this->state->get('list.direction');
		if ($orderCol == 'cf.ordering' || $orderCol == 'category_title') {
			$orderCol = 'category_title '.$orderDirn.', cf.ordering';
		}
		$query->order($db->getEscaped($orderCol.' '.$orderDirn));
		
		//echo nl2br(str_replace('#__','jos_',$query)); exit;
		return $query;
	}
}
