<?php
/**
 * product builder component
 * @version $Id: models/vm_products.php  2012-2-9 sakisTerz $
 * @author Sakis Terz (sakis@breakDesigns.net)
 * @copyright	Copyright (C) 2010-2012 breakDesigns.net. All rights reserved
 * @license	GNU/GPL v2
 */


jimport( 'joomla.application.component.modellist' );

/**
 * @package productbuilder
 */
class productbuilderModelVm_products extends JModelList
{
	function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'virtuemart_product_id', 'p.virtuemart_product_id',
				'product_name', 'l.product_name',
				'product_sku', 'p.product_sku',				
				'created_on', 'p.created_on',
				'mf_name','mnf_l.mf_name',
				'product_price','pr.product_price',				
			);
		}
		parent::__construct($config);
	}


	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 * @author	Sakis Terzis
	 * @return	void
	 * @since	2.0
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$app=JFactory::getApplication('administrator');
		// Adjust the context to support modal layouts.
		if ($viewtype=JRequest::getCmd('viewtype','assignproducts')) {
			$this->context .= '.'.$viewtype;
		}

		//2 view types-1 for assigning products and 1 for setting the default product
		$this->setState('viewtype',$viewtype);

		//group_id
		$this->setState('pb_group_id', JRequest::getInt('pb_group_id',0));

		// Load the filter published.
		$this->setState('filter.published', $app->getUserStateFromRequest($this->context.'.filter.published', 'filter_published', ''));
		$this->setState('filter.virtuemart_manufacturer', $app->getUserStateFromRequest($this->context.'.filter.virtuemart_manufacturer', 'filter_virtuemart_manufacturer', ''));
		$this->setState('filter.virtuemart_category', $app->getUserStateFromRequest($this->context.'.filter.virtuemart_category', 'filter_virtuemart_category', ''));

		// Load the filter search.
		$this->setState('filter.search', $app->getUserStateFromRequest($this->context.'.filter.search', 'filter_search', ''));
		parent::populateState('p.virtuemart_product_id','ASC');
	}


	/**
	 * Build an SQL query to load the list data.
	 * @author	Sakis Terzis
	 * @return	JDatabaseQuery
	 * @since	2.0
	 */
	function getListQuery(){
		$pb_group_id=$this->getState('pb_group_id');
		$where=array();
		$where_q='';

		//build the query
		$db=JFactory::getDbo();
		$query=$db->getQuery(true);
		$query->select('DISTINCT p.virtuemart_product_id AS virtuemart_product_id, p.product_parent_id AS product_parent_id , p.product_sku AS product_sku,p.created_on AS created_on, p.modified_on AS modified_on');
		$query->from('#__virtuemart_products AS p');
		
		$query->select('l.product_name AS product_name, l.product_s_desc AS product_s_desc,l.product_desc AS product_desc');
		$query->join('LEFT', '#__virtuemart_products_'.VMLANGPRFX.' AS l ON p.virtuemart_product_id=l.virtuemart_product_id');
		
		//get the parent name
		$query->select('pl.product_name AS parent_name');
		$query->join('LEFT', '#__virtuemart_products_'.VMLANGPRFX.' AS pl ON p.product_parent_id=pl.virtuemart_product_id');
		
		//join categories
		$query->join('LEFT', '#__virtuemart_product_categories AS p_cat ON p.virtuemart_product_id=p_cat.virtuemart_product_id');
		//$query->select('cat_l.category_name AS category_name');
		$query->join('LEFT', '#__virtuemart_categories_'.VMLANGPRFX.' AS cat_l ON p_cat.virtuemart_category_id=cat_l.virtuemart_category_id');

		//join manufacturers
		$query->join('LEFT', '#__virtuemart_product_manufacturers AS p_mnf ON p.virtuemart_product_id=p_mnf.virtuemart_product_id');
		$query->select('mnf_l.mf_name AS mf_name');
		$query->join('LEFT', '#__virtuemart_manufacturers_'.VMLANGPRFX.' AS mnf_l ON p_mnf.virtuemart_manufacturer_id=mnf_l.virtuemart_manufacturer_id');

		//join prices-Use the base price
		$query->select('pr.product_price AS product_price');
		$query->join('LEFT', '#__virtuemart_product_prices AS pr ON p.virtuemart_product_id=pr.virtuemart_product_id');

		//this concern the "set default products" viewtype.
		//Only the products of that groupd should be displayed, to be set as default
		if($this->getState('viewtype')=='assigndefproduct'){
			//get the editable or not
			if(JRequest::getInt('editable',0)==1){
				$connectWith=JRequest::getInt('conectwith',0);

				//connect with categories
				if($connectWith==0){
					$vm_categories=JRequest::getVar('cat_ids',array(),'get','array');
					JArrayHelper::toInteger($vm_categories);
					if(count($vm_categories)>0)$where[]='p_cat.virtuemart_category_id IN('.implode(',', $vm_categories).')';
				}
				//connect with products viewtype
				else{					
					$query->join('INNER', '#__pb_group_vm_prod_xref AS pb_vmpr ON p.virtuemart_product_id=pb_vmpr.vm_product_id');
					$where[]='pb_vmpr.group_id='.$pb_group_id;					
				}
			}
		}else{
			//join the group's products
			//$where[]='p.product_parent_id=0';
			$query->select('pbp.vm_product_id AS is_selected');
			$query->join('LEFT', '(SELECT vm_product_id FROM #__pb_group_vm_prod_xref WHERE group_id='.$pb_group_id.') AS pbp ON p.virtuemart_product_id=pbp.vm_product_id');
		}
		
		//filters
		$published=$this->getState('filter.published');
		if (is_numeric($published)) {
			$where[] = 'p.published = ' . (int) $published;
		}

		//search filter
		$search=$this->getState('filter.search');
		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$where[]='p.virtuemart_product_id = '.(int) substr($search, 3);
			}elseif (stripos($search, 'name:') === 0) {
				$search = $db->Quote('%'.$db->escape($search, true).'%');
				$query->where('l.product_name LIKE '.$search);
			}
			else {
				$search = $db->Quote('%'.$db->escape($search, true).'%');
				$where[]='(l.product_name LIKE '.$search.' || p.product_sku LIKE '.$search.')';
			}
		}

		//category
		$vm_category=$this->getState('filter.virtuemart_category');
		if(!empty($vm_category)){
			$where[]='p_cat.virtuemart_category_id='.(int)$vm_category;
		}

		//manufacturer
		$vm_manuf=$this->getState('filter.virtuemart_manufacturer');
		if(!empty($vm_manuf)){
			$where[]='p_mnf.virtuemart_manufacturer_id='.(int)$vm_manuf;
		}

		if(count($where)>0) $where_q=implode(' AND ',$where);
		if($where_q)$query->where($where_q);


		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering','p.virtuemart_product_id');
		$orderDirn	= $this->state->get('list.direction','asc');
		$query->order($db->escape($orderCol.' '.$orderDirn));
		return $query;
	}



	/**
	 * Gets the child products of a specific product
	 * @param integer The parent product id
	 * @return	Array	The child products
	 * @author	Sakis Terzis
	 * @since	1.0
	 */
	function getChildprod($p_id){
		$pb_group_id=$this->getState('pb_group_id');
		
		$db=JFactory::getDBO();
		$query=$db->getQuery(true);
		$query->select('p.virtuemart_product_id AS virtuemart_product_id,p.product_parent_id AS product_parent_id,p.product_sku AS product_sku, p.created_on AS created_on');
		$query->from('#__virtuemart_products AS p');

		$query->select('l.product_name AS product_name, l.product_s_desc AS product_s_desc,l.product_desc AS product_desc');
		$query->join('LEFT', '#__virtuemart_products_'.VMLANGPRFX.' AS l ON p.virtuemart_product_id=l.virtuemart_product_id');

		
		//join manufacturers
		$query->join('LEFT', '#__virtuemart_product_manufacturers AS p_mnf ON p.virtuemart_product_id=p_mnf.virtuemart_product_id');
		$query->select('mnf_l.mf_name AS mf_name');
		$query->join('LEFT', '#__virtuemart_manufacturers_'.VMLANGPRFX.' AS mnf_l ON p_mnf.virtuemart_manufacturer_id=mnf_l.virtuemart_manufacturer_id');

		//join prices-Use the base price
		$query->select('pr.product_price AS product_price');
		$query->join('LEFT', '#__virtuemart_product_prices AS pr ON p.virtuemart_product_id=pr.virtuemart_product_id');

		//this concern the "set default products" viewtype.
		//Only the products of that groupd should be displayed, to be set as default
		if($this->getState('viewtype')=='assigndefproduct'){
			//get the editable or not
			if(JRequest::getInt('editable',0)==1){
				$connectWith=JRequest::getInt('conectwith',0);

				//connect with categories
				if($connectWith==0){
					$vm_categories=JRequest::getVar('cat_ids',array(),'get','array');
					JArrayHelper::toInteger($vm_categories);
					if(count($vm_categories)>0)$where[]='p_cat.virtuemart_category_id IN('.implode(',', $vm_categories).')';
				}
				//connect with products viewtype
				else{
					$query->join('INNER', '#__pb_group_vm_prod_xref AS pb_vmpr ON p.virtuemart_product_id=pb_vmpr.vm_product_id');
					$where[]='pb_vmpr.group_id='.$pb_group_id;
				}
			}
		}else{
			//join the group's products
			$query->select('pbp.vm_product_id AS is_selected');
			$query->join('LEFT', '(SELECT vm_product_id FROM #__pb_group_vm_prod_xref WHERE group_id='.$pb_group_id.') AS pbp ON p.virtuemart_product_id=pbp.vm_product_id');
		}
		
		$query->where('p.product_parent_id='.$p_id);
		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering','p.virtuemart_product_id');
		$orderDirn	= $this->state->get('list.direction','asc');
		$query->order($db->escape($orderCol.' '.$orderDirn));

		$db->setQuery($query);
		$child_prod=$db->loadObjectList();

		if($child_prod) return  $child_prod;
		else return;
	}



	//---------------Tasks--------------------//
	/**
	 * Stores the products assignment to the group
	 * @author	Sakis Terzis
	 * @since	2.0
	 * @return	boolean
	 */
	function setGrProducts(){
		$db=JFactory::getDBO();
		$group_id=$this->getState('pb_group_id');
		//products part
		$selectedProd=JRequest::getVar('prd_id',array(),'post','array');//the products selected
		JArrayHelper::toInteger($selectedProd);
		$existProds=array_unique($selectedProd);

		$existProds=$_SESSION['vm_prod'];
		$existProds=array_unique($existProds);
		$willbeDeleted=array_diff($existProds,$selectedProd);

		if($existProds && $willbeDeleted){
			foreach($willbeDeleted as $del){
				$query="DELETE FROM #__pb_group_vm_prod_xref WHERE vm_product_id=$del AND group_id = $group_id";
				$db->setQuery($query);
				//$this->setError($query);return false;
				if(!$db->query()){
					$this->setError($db->getErrorMsg());
				}
			}
		}

		$willbeInserted=array_diff( $selectedProd,$existProds);//returns the values of array1 that are not in array2
		if($selectedProd && $willbeInserted){
			foreach($willbeInserted as $ins){
				$query="INSERT IGNORE INTO #__pb_group_vm_prod_xref VALUES (".$group_id.",".$ins.")";
				$db->setQuery($query);
				if(!$db->query()){
					$this->setError($db->getErrorMsg());
				}
			}
		}
		if(count($this->getErrors())>0)return false;
		return true;
	}

	/**
	 * Stores the default product to the group
	 * @author	Sakis Terzis
	 * @since	2.0
	 * @return	boolean
	 */
	function setDefProduct(){
		$group_id=$this->getState('pb_group_id');
		$db=JFactory::getDBO();
		$defPr=JRequest::getVar('prd_id',array(),'','array');
		$defPr=(int)$defPr[0];
		if($defPr){
			$query="UPDATE #__pb_groups SET defaultProd=$defPr WHERE id=$group_id";

			$db->setQuery($query);
			if(!$db->query()){
				$this->setError($db->getErrorMsg());
				return false;
			}
		}
		return true;
	}

	/**
	 * Get the group Object
	 * @author Sakis Terzis
	 * @since 2.0
	 * @return	Object The group
	 */
	function getGroup(){
		$pb_group_id=$this->getState('pb_group_id');
		$db=JFactory::getDbo();
		$query=$db->getQuery(true);
		$query->select('*');
		$query->from('#__pb_groups');
		$query->where('id='.(int)$pb_group_id);
		$db->setQuery($query);
		return $db->loadObject();
	}

}
?>

