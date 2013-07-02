<?php
/**
 * @version		1.7.0
 * @copyright	Copyright (C) 2006 - 2010 Ideal Custom Software Development
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * This models supports retrieving lists of contacts.
 *
 * @package		Joomla.Site
 * @subpackage	com_contactenhanced
 * @since		1.7
 */
class ContactenhancedModelContacts extends JModelList
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
				'id', 'a.id',
				'name', 'a.name',
				'alias', 'a.alias',
				'checked_out', 'a.checked_out',
				'checked_out_time', 'a.checked_out_time',
				'catid', 'a.catid', 'category_title',
				'state', 'a.published',
				'access', 'a.access', 'access_level',
				'created', 'a.created',
				'created_by', 'a.created_by',
				'ordering', 'a.ordering',
				'featured', 'a.featured',
				'language', 'a.language',
				'hits', 'a.hits',
				'publish_up', 'a.publish_up',
				'publish_down', 'a.publish_down',
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return	void
	 * @since	1.6
	 */
	protected function populateState($ordering = 'ordering', $direction = 'ASC')
	{
		$app = JFactory::getApplication();

		// List state information
		//$value = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'));
		$value = JRequest::getVar('limit', $app->getCfg('list_limit', 0), 'default', 'uint');
		$this->setState('list.limit', $value);

		//$value = $app->getUserStateFromRequest($this->context.'.limitstart', 'limitstart', 0);
		$value = JRequest::getVar('limitstart', 0, 'default', 'uint');
		$this->setState('list.start', $value);

		$orderCol	= JRequest::getCmd('filter_order', 'a.ordering');
		if (!in_array($orderCol, $this->filter_fields)) {
			$orderCol = 'a.ordering';
		}
		$this->setState('list.ordering', $orderCol);

		$listOrder	=  JRequest::getCmd('filter_order_Dir', 'ASC');
		if (!in_array(strtoupper($listOrder), array('ASC', 'DESC', ''))) {
			$listOrder = 'ASC';
		}
		$this->setState('list.direction', $listOrder);

		$params = $app->getParams();
		$this->setState('params', $params);
		$user		= JFactory::getUser();

		if ((!$user->authorise('core.edit.state', 'com_contactenhanced')) &&  (!$user->authorise('core.edit', 'com_contactenhanced'))){
			// filter on published for those who do not have edit or edit.state rights.
			$this->setState('filter.published', 1);
		}

		$this->setState('filter.language',$app->getLanguageFilter());

		// process show_noauth parameter
		if (!$params->get('show_noauth')) {
			$this->setState('filter.access', true);
		}
		else {
			$this->setState('filter.access', false);
		}

		$this->setState('layout', JRequest::getCmd('layout'));
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
	 * @since	1.6
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':'.$this->getState('filter.published');
		$id .= ':'.$this->getState('filter.access');
		$id .= ':'.$this->getState('filter.featured');
		$id .= ':'.$this->getState('filter.contact_id');
		$id .= ':'.$this->getState('filter.contact_id.include');
		$id .= ':'.$this->getState('filter.category_id');
		$id .= ':'.$this->getState('filter.category_id.include');
		$id .= ':'.$this->getState('filter.author_id');
		$id .= ':'.$this->getState('filter.author_id.include');
		$id .= ':'.$this->getState('filter.author_alias');
		$id .= ':'.$this->getState('filter.author_alias.include');
		$id .= ':'.$this->getState('filter.date_filtering');
		$id .= ':'.$this->getState('filter.date_field');
		$id .= ':'.$this->getState('filter.start_date_range');
		$id .= ':'.$this->getState('filter.end_date_range');
		$id .= ':'.$this->getState('filter.relative_date');

		return parent::getStoreId($id);
	}

	/**
	 * Get the master query for retrieving a list of contacts subject to the model state.
	 *
	 * @return	JDatabaseQuery
	 * @since	1.6
	 */
	function getListQuery()
	{
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'a.id, a.name, a.alias, a.name AS title, a.alias AS title_alias, a.params, ' .
				'a.checked_out, a.checked_out_time, ' .
				'a.misc, a.con_position, a.address, a.suburb, a.state , a.country, a.postcode,'.
				' a.telephone, a.fax, a.mobile, a.skype, a.twitter, a.facebook, a.linkedin, a.webpage, a.image,'.
				' a.extra_field_1, a.extra_field_2, a.extra_field_3, a.extra_field_4, a.extra_field_5,'.
				'a.lat, a.lng, a.email_to,'.
				'a.catid, a.created, a.created_by, a.created_by_alias, ' .
				' CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(\':\', a.id, a.alias) ELSE a.id END as slug, ' .
				// use created if modified is 0
				'CASE WHEN a.modified = 0 THEN a.created ELSE a.modified END as modified, ' .
					'a.modified_by, uam.name as modified_by_name,' .
				// use created if publish_up is 0
				'CASE WHEN a.publish_up = 0 THEN a.created ELSE a.publish_up END as publish_up, ' .
					'a.publish_down, a.metadata, a.metakey, a.metadesc, a.access, '.
					'a.xreference, a.featured '
			)
		);

		// Process an Archived Contact layout
		if ($this->getState('filter.published') == 2) {
			// If badcats is not null, this means that the contact is inside an archived category
			// In this case, the state is set to 2 to indicate Archived (even if the contact state is Published)
			$query->select($this->getState('list.select','CASE WHEN badcats.id is null THEN a.published ELSE 2 END AS published'));
		}
		else {
			// Process non-archived layout
			// If badcats is not null, this means that the contact is inside an unpublished category
			// In this case, the state is set to 0 to indicate Unpublished (even if the contact state is Published)
			$query->select($this->getState('list.select','CASE WHEN badcats.id is not null THEN 0 ELSE a.published END AS published'));
		}

		$query->from('#__ce_details AS a');

				// Join over the categories.
		$query->select('c.title AS category_title, c.path AS category_route, c.access AS category_access, c.alias AS category_alias');
		$query->join('LEFT', '#__categories AS c ON c.id = a.catid');

		// Join over the users for the author and modified_by names.
		$query->select("CASE WHEN a.created_by_alias > ' ' THEN a.created_by_alias ELSE ua.name END AS author");
		$query->select("ua.email AS author_email");

		$query->join('LEFT', '#__users AS ua ON ua.id = a.created_by');
		$query->join('LEFT', '#__users AS uam ON uam.id = a.modified_by');

	// Join over the categories to get parent category titles
		$query->select('parent.title as parent_title, parent.id as parent_id, parent.path as parent_route, parent.alias as parent_alias');
		$query->join('LEFT', '#__categories as parent ON parent.id = c.parent_id');

		
		// Join to check for category published state in parent categories up the tree
		$query->select('c.published, CASE WHEN badcats.id is null THEN c.published ELSE 0 END AS parents_published');
		$subquery = 'SELECT cat.id as id FROM #__categories AS cat JOIN #__categories AS parent ';
		$subquery .= 'ON cat.lft BETWEEN parent.lft AND parent.rgt ';
		$subquery .= 'WHERE parent.extension = ' . $db->quote('com_contactenhanced');

		if ($this->getState('filter.published') == 2) {
			// Find any up-path categories that are archived
			// If any up-path categories are archived, include all children in archived layout
			$subquery .= ' AND parent.published = 2 GROUP BY cat.id ';
			// Set effective state to archived if up-path category is archived
			$publishedWhere = 'CASE WHEN badcats.id is null THEN a.published ELSE 2 END';
		}
		else {
			// Find any up-path categories that are not published
			// If all categories are published, badcats.id will be null, and we just use the contact state
			$subquery .= ' AND parent.published != 1 GROUP BY cat.id ';
			// Select state to unpublished if up-path category is unpublished
			$publishedWhere = 'CASE WHEN badcats.id is null THEN a.published ELSE 0 END';
		}
		$query->join('LEFT OUTER', '(' . $subquery . ') AS badcats ON badcats.id = c.id');

		// Filter by access level.
		if ($access = $this->getState('filter.access')) {
			$user	= JFactory::getUser();
			$groups	= implode(',', $user->getAuthorisedViewLevels());
			$query->where('a.access IN ('.$groups.')');
		}

		// Filter by published state
		$published = $this->getState('filter.published');

		if (is_numeric($published)) {
			// Use contact state if badcats.id is null, otherwise, force 0 for unpublished
			$query->where($publishedWhere . ' = ' . (int) $published);
		}
		else if (is_array($published)) {
			JArrayHelper::toInteger($published);
			$published = implode(',', $published);
			// Use contact state if badcats.id is null, otherwise, force 0 for unpublished
			$query->where($publishedWhere . ' IN ('.$published.')');
		}

		// Filter by featured state
		$featured = $this->getState('filter.featured');
		switch ($featured)
		{
			case 'hide':
				$query->where('a.featured = 0');
				break;

			case 'only':
				$query->where('a.featured = 1');
				break;

			case 'show':
			default:
				// Normally we do not discriminate
				// between featured/unfeatured items.
				break;
		}

		// Filter by a single or group of contacts.
		$contactId = $this->getState('filter.contact_id');

		if (is_numeric($contactId)) {
			$type = $this->getState('filter.contact_id.include', true) ? '= ' : '<> ';
			$query->where('a.id '.$type.(int) $contactId);
		}
		else if (is_array($contactId)) {
			JArrayHelper::toInteger($contactId);
			$contactId = implode(',', $contactId);
			$type = $this->getState('filter.contact_id.include', true) ? 'IN' : 'NOT IN';
			$query->where('a.id '.$type.' ('.$contactId.')');
		}

		// Filter by a single or group of categories
		$categoryId = $this->getState('filter.category_id');

		if (is_numeric($categoryId)) {
			$type = $this->getState('filter.category_id.include', true) ? '= ' : '<> ';

			// Add subcategory check
			$includeSubcategories = $this->getState('filter.subcategories', false);
			$categoryEquals = 'a.catid '.$type.(int) $categoryId;

			if ($includeSubcategories) {
				$levels = (int) $this->getState('filter.max_category_levels', '1');
				// Create a subquery for the subcategory list
				$subQuery = $db->getQuery(true);
				$subQuery->select('sub.id');
				$subQuery->from('#__categories as sub');
				$subQuery->join('INNER', '#__categories as this ON sub.lft > this.lft AND sub.rgt < this.rgt');
				$subQuery->where('this.id = '.(int) $categoryId);
				if ($levels >= 0) {
					$subQuery->where('sub.level <= this.level + '.$levels);
				}

				// Add the subquery to the main query
				$query->where('('.$categoryEquals.' OR a.catid IN ('.$subQuery->__toString().'))');
			}
			else {
				$query->where($categoryEquals);
			}
		}
		else if (is_array($categoryId) && (count($categoryId) > 0)) {
			JArrayHelper::toInteger($categoryId);
			$categoryId = implode(',', $categoryId);
			if (!empty($categoryId)) {
				$type = $this->getState('filter.category_id.include', true) ? 'IN' : 'NOT IN';
				$query->where('a.catid '.$type.' ('.$categoryId.')');
			}
		}

		// Filter by author
		$authorId = $this->getState('filter.author_id');
		$authorWhere = '';

		if (is_numeric($authorId)) {
			$type = $this->getState('filter.author_id.include', true) ? '= ' : '<> ';
			$authorWhere = 'a.created_by '.$type.(int) $authorId;
		}
		else if (is_array($authorId)) {
			JArrayHelper::toInteger($authorId);
			$authorId = implode(',', $authorId);

			if ($authorId) {
				$type = $this->getState('filter.author_id.include', true) ? 'IN' : 'NOT IN';
				$authorWhere = 'a.created_by '.$type.' ('.$authorId.')';
			}
		}

		// Filter by author alias
		$authorAlias = $this->getState('filter.author_alias');
		$authorAliasWhere = '';

		if (is_string($authorAlias)) {
			$type = $this->getState('filter.author_alias.include', true) ? '= ' : '<> ';
			$authorAliasWhere = 'a.created_by_alias '.$type.$db->Quote($authorAlias);
		}
		else if (is_array($authorAlias)) {
			$first = current($authorAlias);

			if (!empty($first)) {
				JArrayHelper::toString($authorAlias);

				foreach ($authorAlias as $key => $alias)
				{
					$authorAlias[$key] = $db->Quote($alias);
				}

				$authorAlias = implode(',', $authorAlias);

				if ($authorAlias) {
					$type = $this->getState('filter.author_alias.include', true) ? 'IN' : 'NOT IN';
					$authorAliasWhere = 'a.created_by_alias '.$type.' ('.$authorAlias .
						')';
				}
			}
		}

		if (!empty($authorWhere) && !empty($authorAliasWhere)) {
			$query->where('('.$authorWhere.' OR '.$authorAliasWhere.')');
		}
		else if (empty($authorWhere) && empty($authorAliasWhere)) {
			// If both are empty we don't want to add to the query
		}
		else {
			// One of these is empty, the other is not so we just add both
			$query->where($authorWhere.$authorAliasWhere);
		}

		// Filter by start and end dates.
		$nullDate	= $db->Quote($db->getNullDate());
		$nowDate	= $db->Quote(JFactory::getDate()->toMySQL());

		$query->where('(a.publish_up = '.$nullDate.' OR a.publish_up <= '.$nowDate.')');
		$query->where('(a.publish_down = '.$nullDate.' OR a.publish_down >= '.$nowDate.')');

		// Filter by Date Range or Relative Date
		$dateFiltering = $this->getState('filter.date_filtering', 'off');
		$dateField = $this->getState('filter.date_field', 'a.created');

		switch ($dateFiltering)
		{
			case 'range':
				$startDateRange = $db->Quote($this->getState('filter.start_date_range', $nullDate));
				$endDateRange = $db->Quote($this->getState('filter.end_date_range', $nullDate));
				$query->where('('.$dateField.' >= '.$startDateRange.' AND '.$dateField .
					' <= '.$endDateRange.')');
				break;

			case 'relative':
				$relativeDate = (int) $this->getState('filter.relative_date', 0);
				$query->where($dateField.' >= DATE_SUB('.$nowDate.', INTERVAL ' .
					$relativeDate.' DAY)');
				break;

			case 'off':
			default:
				break;
		}

		// process the filter for list views with user-entered filters
		$params = $this->getState('params');

		if ((is_object($params)) && ($params->get('filter_field') != 'hide') && ($filter = $this->getState('list.filter'))) {
			// clean filter variable
			$filter = JString::strtolower($filter);
			$hitsFilter = intval($filter);
			$filter = $db->Quote('%'.$db->getEscaped($filter, true).'%', false);

			switch ($params->get('filter_field'))
			{
				case 'author':
					$query->where(
						'LOWER( CASE WHEN a.created_by_alias > '.$db->quote(' ').
						' THEN a.created_by_alias ELSE ua.name END ) LIKE '.$filter.' '
					);
					break;

				case 'hits':
					$query->where('a.hits >= '.$hitsFilter.' ');
					break;

				case 'title':
				default: // default to 'title' if parameter is not valid
					$query->where('LOWER( a.name ) LIKE '.$filter);
					break;
			}
		}

		// Filter by language
		if ($this->getState('filter.language')) {
			$query->where('a.language in ('.$db->quote(JFactory::getLanguage()->getTag()).','.$db->quote('*').')');
			$query->where('(a.language in ('.$db->quote(JFactory::getLanguage()->getTag()).','.$db->quote('*').') OR a.language IS NULL)');
		}

		// Add the list ordering clause.
		$query->order($this->getState('list.ordering', 'a.ordering').' '.$this->getState('list.direction', 'ASC'));
//echo nl2br(str_replace('#__','dj17_',$query)); exit;
		return $query;
	}

	/**
	 * Method to get a list of contacts.
	 *
	 * Overriden to inject convert the attribs field into a JParameter object.
	 *
	 * @return	mixed	An array of objects on success, false on failure.
	 * @since	1.6
	 */
	public function getItems()
	{
		$items	= parent::getItems();
		if(!isset($items) OR !$items){
			return array();
		}
	//	echo '<pre>'; print_r(JFactory::getDbo()->getLog()); exit();
		$user	= JFactory::getUser();
		$userId	= $user->get('id');
		$guest	= $user->get('guest');
		$groups	= $user->getAuthorisedViewLevels();

		// Get the global params
		$globalParams = JComponentHelper::getParams('com_contactenhanced', true);

		// Convert the parameter fields into objects.
		foreach ($items as &$item)
		{
			$contactParams = new JRegistry;
			$contactParams->loadString($item->params);

			$item->layout = $contactParams->get('layout');

			$item->params = clone $this->getState('params');

			// For blogs, contact params override menu item params only if menu param = 'use_contact'
			// Otherwise, menu item params control the layout
			// If menu item is 'use_contact' and there is no contact param, use global
			if ((JRequest::getString('layout') == 'blog') || (JRequest::getString('view') == 'featured')
				|| ($this->getState('params')->get('layout_type') == 'blog')) {
				// create an array of just the params set to 'use_contact'
				$menuParamsArray = $this->getState('params')->toArray();
				$contactArray = array();

				foreach ($menuParamsArray as $key => $value)
				{
					if ($value === 'use_contact') {
						// if the contact has a value, use it
						if ($contactParams->get($key) != '') {
							// get the value from the contact
							$contactArray[$key] = $contactParams->get($key);
						}
						else {
							// otherwise, use the global value
							$contactArray[$key] = $globalParams->get($key);
						}
					}
				}

				// merge the selected contact params
				if (count($contactArray) > 0) {
					$contactParams = new JRegistry;
					$contactParams->loadArray($contactArray);
					$item->params->merge($contactParams);
				}
			}
			else {
				// For non-blog layouts, merge all of the contact params
				$item->params->merge($contactParams);
			}

			// get display date
			switch ($item->params->get('show_date'))
			{
				case 'modified':
					$item->displayDate = $item->modified;
					break;

				case 'published':
					$item->displayDate = ($item->publish_up == 0) ? $item->created : $item->publish_up;
					break;

				default:
				case 'created':
					$item->displayDate = $item->created;
					break;
			}

			// Compute the asset access permissions.
			// Technically guest could edit an contact, but lets not check that to improve performance a little.
			if (!$guest) {
				$asset	= 'com_contactenhanced.contact.'.$item->id;

				// Check general edit permission first.
				if ($user->authorise('core.edit', $asset)) {
					$item->params->set('access-edit', true);
				}
				// Now check if edit.own is available.
				else if (!empty($userId) && $user->authorise('core.edit.own', $asset)) {
					// Check for a valid user and that they are the owner.
					if ($userId == $item->created_by) {
						$item->params->set('access-edit', true);
					}
				}
			}

			$access = $this->getState('filter.access');

			if ($access) {
				// If the access filter has been set, we already have only the contacts this user can view.
				$item->params->set('access-view', true);
			}
			else {
				// If no access filter is set, the layout takes some responsibility for display of limited information.
				if ($item->catid == 0 || $item->category_access === null) {
					$item->params->set('access-view', in_array($item->access, $groups));
				}
				else {
					$item->params->set('access-view', in_array($item->access, $groups) && in_array($item->category_access, $groups));
				}
			}
		}

		return $items;
	}
	public function getStart()
	{
		return $this->getState('list.start');
	}
}
