<?php
/**
 * product builder component
 * @package productbuilder
 * @version tags.php  2012-2-16 sakisTerzis $
 * @author Sakis Terzis (sakis@breakDesigns.net)
 * @copyright	Copyright (C) 2010-2012 breakDesigns.net. All rights reserved
 * @license	GNU/GPL v2
 */


jimport( 'joomla.application.component.modellist' );

/**
 * @package productbuilder
 */
class productbuilderModelTags extends JModelList
{

	function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'id', 'gr.id',
				'name', 'gr.name',
				'published', 'gr.published',
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
	 * @since	2.0
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$app=JFactory::getApplication('administrator');
		// Adjust the context to support modal layouts.
		if ($layout = JRequest::getVar('layout')) {
			$this->context .= '.'.$layout;
		}
		// Load the filter search.
		$this->setState('filter.search', $app->getUserStateFromRequest($this->context.'.filter.search', 'filter_search', ''));
		parent::populateState('name','ASC');
	}


	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return	JDatabaseQuery
	 * @since	2.0
	 */
	function getListQuery() {
		$where=array();
		$where_q='';

		$db=JFactory::getDbo();
		$query=$db->getQuery(true);
		$query->select('*');
		$query->from('#__pb_tags ');

		//filters
		$search=$this->getState('filter.search');
		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$where[]='id = '.(int) substr($search, 3);
			} else {
				$search = $db->Quote('%'.$db->getEscaped($search, true).'%');
				$where[]='(name LIKE '.$search.')';
			}
		}
		if(count($where)>0) $where_q=implode(' AND ',$where);
		if($where_q)$query->where($where_q);

		$orderCol	= $this->state->get('list.ordering','ordering');
		$orderDirn	= $this->state->get('list.direction','asc');
		$query->order($db->getEscaped($orderCol.' '.$orderDirn));
		return $query;
	}


	function delete()
	{
		$cids = JRequest::getVar( 'cid', array(0), 'post', 'array' );
		$db=&$this->getDBO();


		if (count( $cids )>0)
		{
			$queryy='DELETE FROM #__pb_tag_xref_vmprod WHERE tag_id IN ('.implode(',',$cids).')';
			$db->setQuery($queryy);
			if(!$db->query()){
				return($db->getErrorMsg());
			}
			//delete the products
			$query='DELETE FROM #__pb_tags'.
			' WHERE id IN ('.implode(',',$cids).')';
			$db->setQuery($query);
			if(!$db->query()){
				return $db->getErrorMsg();
			}
		}
		return true;
	}


	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param	string		$id	A prefix for the store id.
	 * @return	string		A store id.
	 * @since	1.6
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':'.$this->getState('filter.search');
		return parent::getStoreId($id);
	}
}
?>