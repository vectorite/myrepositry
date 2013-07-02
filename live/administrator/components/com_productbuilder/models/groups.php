<?php
/**
 * product builder component
 * @package productbuilder
 * @version models/groups.php  2012-2-6 sakisTerz $
 * @author Sakis Terzis (sakis@breakDesigns.net)
 * @copyright	Copyright (C) 2010 breakDesigns.net. All rights reserved
 * @license	GNU/GPL v2
 */


jimport( 'joomla.application.component.modellist' );

/**
 * @package productbuilder
 */
class productbuilderModelGroups extends JModelList
{

	
	function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'id', 'gr.id',
				'name', 'gr.name',
				'product_id', 'gr.product_id',				
				'ordering', 'gr.ordering',				
				'published', 'gr.published',
				'editable', 'gr.editable',
				'language', 'gr.language',
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
		
				
		// Load the filter published.
		$this->setState('filter.published', $app->getUserStateFromRequest($this->context.'.filter.published', 'filter_published', ''));
		//Load the filter pb_prod
		$this->setState('filter.pb_prod',$app->getUserStateFromRequest($this->context.'.filter.pb_prod', 'filter_pb_prod', ''));
		// Load the filter search.
		$this->setState('filter.search', $app->getUserStateFromRequest($this->context.'.filter.search', 'filter_search', ''));
		parent::populateState('ordering','ASC');
		
		//save the ordering of the product_id field-this way the ordering of the bundles won't change with the chaneg of the order of the other fields
		$orderCol	= $this->state->get('list.ordering','ordering');
		$orderDirn	= $this->state->get('list.direction','asc');
		if($orderCol=='product_id')$app->setUserState ($this->context.'.product_id_order_dir',$orderDirn);
		$this->setState('product_id_order_dir',$app->getUserState ($this->context.'.product_id_order_dir'),'ASC');
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return	JDatabaseQuery
	 * @since	2.0
	 */
	function getListQuery() {
		$db=JFactory::getDbo();
		$query=$db->getQuery(true);
		$query->select('gr.*,prd.name AS bundleName');
		$query->from('#__pb_groups AS gr');
		$query->join('LEFT', '#__pb_products AS prd ON gr.product_id=prd.id');
		//published filter
		$published=$this->getState('filter.published');

		if (is_numeric($published)) {
			$where[] = 'gr.published = ' . (int) $published;
		} else if ($published === '') {
			$where[] = '(gr.published = 0 OR gr.published = 1)';
		}

		//search filters
		$pb_prod=$this->getState('filter.pb_prod');
		if(!empty($pb_prod)){
			$where[]='gr.product_id='.(int)$pb_prod;		
		}
		
		$search=$this->getState('filter.search');
		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$where[]='id = '.(int) substr($search, 3);
			} else {
				$search = $db->Quote('%'.$db->getEscaped($search, true).'%');
				$where[]='(gr.name LIKE '.$search.' OR prd.name LIKE '.$search.')';
			}
		}
		if(count($where)>0) $where_q=implode(' AND ',$where);
		if($where_q)$query->where($where_q);
		
		// Add the list ordering clause.
		
		$orderCol	= $this->state->get('list.ordering','ordering');
		$orderDirn	= $db->getEscaped($this->state->get('list.direction','asc'));
		
		if($orderCol=='product_id'){
			$second_order=',gr.ordering ASC';
			$product_id_order_dir=$orderDirn;
		}
		else {
			$second_order=$db->getEscaped(','.$orderCol.' '.$orderDirn);
			$product_id_order_dir=$this->getState('product_id_order_dir');	
		}		
		$query->order('prd.name '.$product_id_order_dir.$second_order);
		//print_r((string)$query);
		
		return $query;
	}

	
	/**
	 * Function that returns the filters in an object
	 *@author	Sakis Terzis
	 *@since	2.0
	 *@return	object
	 */
	function getFilters(){
		$fltObj=new stdClass();
		$db=JFactory::getDbo();
		$query=$db->getQuery(true);
		$query->select('id AS value, name AS text');
		$query->from('#__pb_products');
		$query->order('name ASC');
		$db->setQuery($query);
		$fltObj->bundles=$db->loadObjectList();
		return $fltObj;
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
		$id	.= ':'.$this->getState('filter.search');
		$id	.= ':'.$this->getState('filter.pb_prod');
		$id	.= ':'.$this->getState('filter.published');
		return parent::getStoreId($id);
	}
}
?>

