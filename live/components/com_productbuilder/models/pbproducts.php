<?php
/**
 * productbuilder component
 * @version $Id:Pbproducts.php 2.0 2012-3-22 21:33 sakisTerz $
 * @package productbuilder  front-end
 * @author Sakis Terzis (sakis@breakDesigns.net)
 * @copyright	Copyright (C) 2009-2012 breakDesigns.net. All rights reserved
 * @license	GNU/GPL v2
 * see administrator/components/com_productbuilder/COPYING.txt
 */

defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.modellist');


class ProductbuilderModelPbproducts extends JModelList{


	/**
	 * Constructor.
	 *
	 * @param	array	An optional associative array of configuration settings.
	 * @see		JController
	 * @since	2.0
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'id', 'pb.id',
				'name', 'pb.name',
				'ordering', 'pb.ordering',
			);
		}
		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 * Note. Calling getState in this method will result in recursion.
	 * @since	2.0
	 */
	protected function populateState($ordering = 'ordering', $direction = 'ASC')
	{
		$app = JFactory::getApplication();
		$jinput=$app->input;
		// Load the parameters.
		$params = $app->getParams();
		$this->setState('params', $params);

		$limit = $jinput->get('limit', $params->get('pbproducts_limit',$app->getCfg('list_limit', 0)),'int');
		$this->setState('list.limit', $limit);
		$limitstart = $jinput->get('limitstart', 0,'int');
		$this->setState('list.start', $limitstart);		
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
	 * @since	2.0
	 */
	protected function getStoreId($id = '')
	{
		return parent::getStoreId($id);
	}

	/**
	 * Get the master query for retrieving a list of articles subject to the model state.
	 *
	 * @return	JDatabaseQuery
	 * @since	2.0
	 */
	function getListQuery()
	{	$params = $this->getState('params');
		$db=JFactory::getDbo();
		$query=$db->getQuery(true);
		$query->select('*');
		$query->from('#__pb_products');
		$query->where('published=1');
		$query->where('(language='.$db->quote('*').' OR language='.$db->quote(PBLANG).')');
		//get and sanitize the $pb_product_ids
		$pb_product_ids_str=$params->get('pbproduct_ids');
		if($pb_product_ids_str){
			$pb_product_ids=explode(',', $pb_product_ids_str);
			JArrayHelper::toInteger($pb_product_ids);
		}
		if(isset($pb_product_ids))$query->where('id IN('.implode(',',$pb_product_ids).')');
		$query->order($this->getState('list.ordering', 'ordering').' '.$this->getState('list.direction', 'ASC'));
		return $query;
	}	

}//class