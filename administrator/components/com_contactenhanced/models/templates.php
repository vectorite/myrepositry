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
class ContactenhancedModelTemplates extends JModelList
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
				'id', 'tpl.id',
				'name', 'tpl.name',
				'alias', 'tpl.alias',
				'title', 'tpl.title',
				'checked_out', 'tpl.checked_out',
				'checked_out_time', 'tpl.checked_out_time',
				'catid', 'tpl.catid', 'category_title',
				'user_id', 'tpl.user_id',
				'state', 'tpl.state',
				'access', 'tpl.access', 'access_level',
				'created', 'tpl.created',
				'created_by', 'tpl.created_by',
				'ordering', 'tpl.ordering',
				'featured', 'tpl.featured',
				'language', 'tpl.language',
				'publish_up', 'tpl.publish_up',
				'publish_down', 'tpl.publish_down',
				'type', 'tpl.type',
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
		
		$tpl_type = $app->getUserStateFromRequest($this->context.'.filter.template_type', 'filter_template_type', '');
		$this->setState('filter.template_type', $tpl_type);
		//echo $this->getState('filter.template_type'); exit;
		$language = $app->getUserStateFromRequest($this->context.'.filter.language', 'filter_language', '');
		$this->setState('filter.language', $language);

		// List state information.
		parent::populateState('tpl.name', 'asc');
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
		$id.= ':' . $this->getState('filter.template_type');
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
				'tpl.*')
		);
		$query->from('#__ce_template AS tpl');

		// Join over the language
		$query->select('l.title AS language_title');
		$query->join('LEFT', '`#__languages` AS l ON l.lang_code = tpl.language');

		// Join over the asset groups.
		$query->select('ag.title AS access_level');
		$query->join('LEFT', '#__viewlevels AS ag ON ag.id = tpl.access');

	

		// Filter by access level.
		if ($access = $this->getState('filter.access')) {
			$query->where('tpl.access = ' . (int) $access);
		}

		// Filter by published state
		$published = $this->getState('filter.published');
		if (is_numeric($published)) {
			$query->where('tpl.published = ' . (int) $published);
		} else if ($published === '') {
			$query->where('(tpl.published = 0 OR tpl.published = 1)');
		}

		// Filter by search in  name
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			$search = $db->Quote('%'.$db->getEscaped($search, true).'%');
			$query->where('(tpl.name LIKE '.$search.' )');				
		}
		
		// Filter by template_type in  name
		$template_type = $this->getState('filter.template_type');
		if (!empty($template_type)) {
			$template_type = $db->Quote($db->getEscaped($template_type, true));
			$query->where('(tpl.type = '.$template_type.' )');				
		}
		
		// Filter on the language.
		if ($language = $this->getState('filter.language')) {
			$query->where('tpl.language = ' . $db->quote($language));
		}

		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering','tpl.name');
		$orderDirn	= $this->state->get('list.direction','ASC');
		$query->order($db->getEscaped($orderCol.' '.$orderDirn));
		
		//echo nl2br(str_replace('#__','jos_',$query)); //exit;
		return $query;
	}
}
