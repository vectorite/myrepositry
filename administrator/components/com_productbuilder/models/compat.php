<?php
/**
 * product builder component
 * @package productbuilder
 * @version $Id:models/groups.php  2012-2-17 sakisTerz $
 * @author Sakis Terz (sakis@breakDesigns.net)
 * @copyright	Copyright (C) 2010-2012 breakDesigns.net. All rights reserved
 * @license	GNU/GPL v2
 */


jimport( 'joomla.application.component.modellist' );

/**
 * Model class
 * @package productbuilder
 */
class productbuilderModelCompat extends JModelList
{

	function __construct($config=array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'virtuemart_product_id', 'p.virtuemart_product_id',
				'product_name', 'l.product_name',
				'product_sku', 'p.product_sku',				
				'created_on', 'p.created_on',
				'mf_name','mnf_l.mf_name',
				'product_price','pr.product_price',
				'tag_id','p_tag.tag_id',				
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
	function populateState($ordering = null, $direction = null){
		$app=JFactory::getApplication('administrator');
		// Adjust the context to support modal layouts.
		if ($layout=JRequest::getCmd('layout','default')) {
			$this->context .= '.'.$layout;}

			// Load the filters.
			$this->setState('filter.pb_group_id',$app->getUserStateFromRequest($this->context.'.filter.pb_group_id', 'filter_pb_group_id', ''));
			$this->setState('filter.virtuemart_manufacturer', $app->getUserStateFromRequest($this->context.'.filter.virtuemart_manufacturer', 'filter_virtuemart_manufacturer', ''));
			$this->setState('filter.virtuemart_category', $app->getUserStateFromRequest($this->context.'.filter.virtuemart_category', 'filter_virtuemart_category', ''));
			$this->setState('filter.tag', $app->getUserStateFromRequest($this->context.'.filter.tag', 'filter_tag', ''));

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

		$where_gr='';
		$pb_group_id=$this->getState('filter.pb_group_id');
		if($pb_group_id) $where_gr=' AND group_id='.$pb_group_id;

		$where[]='(p_cat.virtuemart_category_id IN
		(SELECT gr_cat.vm_cat_id FROM #__pb_group_vm_cat_xref AS gr_cat 
		INNER JOIN #__pb_groups AS g ON g.id=gr_cat.group_id 
		WHERE g.connectWith=0'.$where_gr.') 
		OR p.virtuemart_product_id IN (SELECT vm_product_id FROM #__pb_group_vm_prod_xref AS gr_p 
		INNER JOIN #__pb_groups AS g ON g.id=gr_p.group_id WHERE g.connectWith=1'.$where_gr.'))'; 
		//$where[]='p.product_parent_id=0';


		//filters
		//search filter
		$search=$this->getState('filter.search');
		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$where[]='p.virtuemart_product_id = '.(int) substr($search, 3);
			}elseif (stripos($search, 'name:') === 0) {
				$search = $db->Quote('%'.$db->getEscaped($search, true).'%');
				$query->where('l.product_name LIKE '.$search);
			}
			else {
				$search = $db->Quote('%'.$db->getEscaped($search, true).'%');
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

		//tag
		$tag=$this->getState('filter.tag');
		if(!empty($tag)){
			$where[]='p_tag.tag_id='.(int)$tag;
			$query->join('INNER', '#__pb_tag_xref_vmprod AS p_tag ON p.virtuemart_product_id=p_tag.vm_prod_id');
		}

		if(count($where)>0) $where_q=implode(' AND ',$where);
		if($where_q)$query->where($where_q);


		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering','p.virtuemart_product_id');
		$orderDirn	= $this->state->get('list.direction','asc');
		$query->order($db->escape($orderCol.' '.$orderDirn));
		//print_r((string)$query);
		return $query;
	}


	function getChildprod($p_id){
		$db=JFactory::getDBO();
		$query="SELECT prd.product_id, prd.product_parent_id, prd.product_sku, prd.product_name, prd.vendor_id, prd.product_s_desc, prd.product_desc,prd.cdate FROM #__vm_product AS prd WHERE prd.product_parent_id=".$p_id;
		$db->setQuery($query);
		$child_prod=$db->loadObjectList();

		if($child_prod) return  $child_prod;
		else return;
	}

	//set the tags for a prod
	function setTags($prod_id){
		$db=JFactory::getDbo();
		$tag_ids=JRequest::getVar('tag_id',array(0));

		$query="DELETE FROM #__pb_tag_xref_vmprod WHERE vm_prod_id=".$prod_id;
		$db->setQuery($query);
		if(!$db->query()){
			$model->setError($db->getErrorMsg());
			return false;
		}

		foreach($tag_ids as $tid){
			$query="INSERT INTO #__pb_tag_xref_vmprod VALUES (".$tid.",".$prod_id.")";
			$db->setQuery($query);
			if(!$db->query()){
				$model->setError($db->getErrorMsg());
				return false;
			}
		}
		return true;
	}

	function getTags($prod_id){
		$html='';
		$db=JFactory::getDBO();
		$query="SELECT t.id , t.name, t.color FROM #__pb_tags AS t ORDER BY t.name ASC";
		$db->setQuery($query);
		$tags=$db->loadObjectList();

		$q="SELECT tag_id FROM #__pb_tag_xref_vmprod WHERE vm_prod_id=".$prod_id;
		$db->setQuery($q);
		$selTags=$db->loadResultArray();

		if($tags){
			$html='<ul id="tagList'.$prod_id.'" class="tagList">';
			foreach($tags as $t){
				$checked='';
				if(in_array($t->id,$selTags)) $checked=' checked="checked"';
				$html.='<li style="color:#'.$t->color.'">
        <input type="checkbox" name="tag'.$prod_id.'"  value="'.$t->id.'" id="'.$prod_id.'_'.$t->id.'"'.$checked.'/>
        <label class="tag_selection" for="'.$prod_id.'_'.$t->id.'">'.$t->name.'</label></li>';
			}
			$html.='</ul>';
			$html.='<input type="button" onclick="updateTags('.$prod_id.')" value="'.JText::_('Update').'" class="updateTags"/>';
		}
		return $html;
	}

	function getProdTagNames($prod_id){
		$db=JFactory::getDBO();
		$string='';
		$q="SELECT t.name, t.color FROM #__pb_tags AS t INNER JOIN  #__pb_tag_xref_vmprod AS pt ON pt.tag_id=t.id WHERE pt.vm_prod_id=".$prod_id.' ORDER BY t.name ASC';
		$db->setQuery($q);
		$tags=$db->loadObjectList();

		foreach($tags as $t){
			$string.='<div class="tagnames" style="background:#'.$t->color.'">'.$t->name.'</div>';
		}
		return $string;
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
		$id	.= ':'.$this->getState('filter.pb_group_id');
		$id	.= ':'.$this->getState('filter.virtuemart_manufacturer');
		$id	.= ':'.$this->getState('filter.virtuemart_category');
		$id	.= ':'.$this->getState('filter.tag');
		$id	.= ':'.$this->getState('filter.search');
		return parent::getStoreId($id);
	}

}
