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
class ContactenhancedModelCustomvalues extends JModelList
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
				'id', 'cv.id',
				'name', 'cv.name',
				'alias', 'cv.alias',
				'title', 'cv.title',
				'checked_out', 'cv.checked_out',
				'checked_out_time', 'cv.checked_out_time',
				'catid', 'cv.catid', 'category_title',
				'user_id', 'cv.user_id',
				'state', 'cv.state',
				'access', 'cv.access', 'access_level',
				'created', 'cv.created',
				'created_by', 'cv.created_by',
				'ordering', 'cv.ordering',
				'featured', 'cv.featured',
				'language', 'cv.language',
				'publish_up', 'cv.publish_up',
				'publish_down', 'cv.publish_down',
				'description', 'cv.description',
				'value', 'cv.value',
				'type', 'cv.type',
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
		parent::populateState('cv.value', 'asc');
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
				'cv.*')
		);
		$query->from('#__ce_cv AS cv');

		// Join over the language
		$query->select('l.title AS language_title');
		$query->join('LEFT', '`#__languages` AS l ON l.lang_code = cv.language');

		// Join over the asset groups.
		$query->select('ag.title AS access_level');
		$query->join('LEFT', '#__viewlevels AS ag ON ag.id = cv.access');


		// Filter by access level.
		if ($access = $this->getState('filter.access')) {
			$query->where('cv.access = ' . (int) $access);
		}

		// Filter by published state
		$published = $this->getState('filter.published');
		if (is_numeric($published)) {
			$query->where('cv.published = ' . (int) $published);
		} else if ($published === '') {
			$query->where('(cv.published = 0 OR cv.published = 1)');
		}

	

		// Filter by search in  name
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			$search = $db->Quote('%'.$db->getEscaped($search, true).'%');
			$query->where('(cv.value LIKE '.$search.' OR cv.name LIKE '.$search.')');				
		}
		
		// Filter on the language.
		if ($language = $this->getState('filter.language')) {
			$query->where('cv.language = ' . $db->quote($language));
		}

		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering');
		$orderDirn	= $this->state->get('list.direction');
		if ($orderCol == 'cv.ordering' || $orderCol == 'cv.category') {
			$orderCol = 'cv.category '.$orderDirn.', cv.ordering';
		}
		$query->order($db->getEscaped($orderCol.' '.$orderDirn));
		
		//echo nl2br(str_replace('#__','jos_',$query)); exit;
		return $query;
	}
}
