<?php
/**
 * product builder component
 * @package productbuilder
 * @version $Id:2 products.php  2012-2-2 sakisTerzis $
 * @author Sakis Terzis (sakis@breakDesigns.net)
 * @copyright	Copyright (C) 2010-2012 breakDesigns.net. All rights reserved
 * @license	GNU/GPL v2
 */


jimport( 'joomla.application.component.modellist' );

/**
 * The products Class
 * @package productbuilder
 */
class productbuilderModelProducts extends JModelList
{
	
	function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'id', 'id',
				'name', 'name',
				'sku', 'sku',				
				'ordering', 'ordering',				
				'published', 'published',
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
		
		// Load the filter search.
		$this->setState('filter.search', $app->getUserStateFromRequest($this->context.'.filter.search', 'filter_search', ''));
		parent::populateState('ordering','ASC');
	}
	
	
	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return	JDatabaseQuery
	 * @since	2.0
	 */
	function getListQuery(){
		$db=JFactory::getDbo();
		$query=$db->getQuery(true);
		$query->select('*');
		$query->from('#__pb_products');
		
		//published filter
		$published=$this->getState('filter.published');

		if (is_numeric($published)) {
			$where[] = 'published = ' . (int) $published;
		} else if ($published === '') {
			$where[] = '(published = 0 OR published = 1)';
		}

		//search filter
		$search=$this->getState('filter.search');
		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$where[]='id = '.(int) substr($search, 3);
			} else {
				$search = $db->Quote('%'.$db->escape($search, true).'%');
				$where[]='(name LIKE '.$search.' || sku LIKE '.$search.')';
			}
		}
		if(count($where)>0) $where_q=implode(' AND ',$where);
		if($where_q)$query->where($where_q);
		
		
		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering','ordering');
		$orderDirn	= $this->state->get('list.direction','asc');
		$query->order($db->escape($orderCol.' '.$orderDirn));
		//print_r((string)$query);
		return $query;
	}
	
		

	function orderItem($id, $movement)
	{
		$row =& $this->getTable('product');
		$row->load($id );
		if(!$row->move( $movement)) return false;
		return true;
	}



	function setOrder($items){
		$data=$this->getData();
		$total=count($data);
		$row		=& $this->getTable('product');

		$order		= JRequest::getVar( 'ordering', array(), 'post', 'array' );

		JArrayHelper::toInteger($order);
		// eliminate duplicate values
		for( $i=0; $i < $total; $i++ ) {
			for( $j=$total; $j >$i; $j-- ) {
				if($order[$i]==$order[$j]){
					$order[$j]+=1;
				}
			}
		}
			
		for( $i=0; $i < $total; $i++ ) {
			$row->load( $items[$i] );
			// track parents
			if ($row->ordering != $order[$i]) {					
				$row->ordering = $order[$i];
				if (!$row->store()) {
					$this->setError($row->getError());
					return false;
				}// if
			} // if
		} // for
		return $order[0];
	}

}
?>