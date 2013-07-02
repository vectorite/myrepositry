<?php
/**
 * product builder component
 * @package productbuilder
 * @version helpers/helper.php  2012-2-16 sakisTerz $
 * @author Sakis Terz (sakis@breakDesigns.net)
 * @copyright	Copyright (C) 2010-2012 breakDesigns.net. All rights reserved
 * @license	GNU/GPL v2
 */

defined('_JEXEC') or die();

class pbHelper{

/**
	 * Configure the Linkbar.
	 *
	 * @param	string	The name of the active view.
	 *
	 * @return	void
	 * @since	1.6
	 */
	public static function addSubmenu($vName)
	{
		JSubMenuHelper::addEntry(
			JText::_('COM_PRODUCTBUILDER_DASHBOARD'),
			'index.php?option=com_productbuilder&view=productbuilder',
			$vName == 'productbuilder'
		);

		JSubMenuHelper::addEntry(
			JText::_('COM_PRODUCTBUILDER_CONF_PRODUCTS'),
			'index.php?option=com_productbuilder&view=products',
			$vName == 'products'
		);
		
		JSubMenuHelper::addEntry(
			JText::_('COM_PRODUCTBUILDER_GROUPS'),
			'index.php?option=com_productbuilder&view=groups',
			$vName == 'groups'
		);

		JSubMenuHelper::addEntry(
			JText::_('COM_PRODUCTBUILDER_TAGS'),
			'index.php?option=com_productbuilder&view=tags',
			$vName == 'tags'
		);
		
		JSubMenuHelper::addEntry(
			JText::_('COM_PRODUCTBUILDER_COMPATIBILITY'),
			'index.php?option=com_productbuilder&view=compat',
			$vName == 'compat'
		);		
		
		JSubMenuHelper::addEntry(
			JText::_('COM_PRODUCTBUILDER_HELP'),
			'index.php?option=com_productbuilder&view=help',
			$vName == 'help'
		);
	}

}
class pbGroupsHelper{

	function prodName($prod_id){//returns the pbproduct name in which a group belongs

		$db=JFactory::getDBO();
		$query="SELECT p.name FROM #__pb_products AS p WHERE p.id =".$prod_id;

		$db->setQuery($query);
		$name=$db->loadResult();
		if($name) return $name;
		else return;
	}

	function groups_ordering($row){//RETURNS THE MAXordering for the next insertion
		$db=JFactory::getDBO();
		$query="SELECT MAX(ordering) AS Max, MIN(ordering) AS Min ,COUNT(ordering) AS Count FROM  #__pb_groups WHERE product_id=".$row->product_id;

		$db->setQuery($query);
		$order=$db->loadAssocList ();
		return $order;
	}

}//end class

class vmProductsHelper{
	/**
	 * Returns the categories of a specific product
	 * 
	 * @param integer $prod_id
	 */
	function getCatName_product($prod_id){
		$db=JFactory::getDbo();
		$query=$db->getQuery(true);
		$query->from('#__virtuemart_product_categories AS p_cat');
		$query->where('p_cat.virtuemart_product_id='.(int)$prod_id);
		$query->select('cat_l.category_name AS category_name');
		$query->join('LEFT', '#__virtuemart_categories_'.VMLANGPRFX.' AS cat_l ON p_cat.virtuemart_category_id=cat_l.virtuemart_category_id');

		$db->setQuery($query);
		$results=$db->loadColumn();
		$results=implode(', ',$results);
		return $results;
	}
	
	/**
	 * Get the product's main(1st) image
	 *
	 * @param 	integer $product_id
	 * @return	mixed Array on success, false on fail
	 * @author	Sakis Terzis
	 * @since	1.0
	 * @version	2.0
	 */
	function getImage($product_id,$parent_id){
		if(!$product_id)return;
		$db=JFactory::getDbo();
		$query=$db->getQuery(true);
		$query->select('m.file_url_thumb');
		$query->from('#__virtuemart_medias AS m');
		$query->innerJoin('#__virtuemart_product_medias AS pm ON pm.virtuemart_media_id=m.virtuemart_media_id');
		$query->where('m.file_type='.$db->quote('product').' AND m.published=1 AND pm.virtuemart_product_id='.$product_id);
		$db->setQuery($query);
		$result=$db->loadResult();
		
		if($result)return $result;
		// check if it is child item and get the parent's image
		else if($parent_id>0){
			$query=$db->getQuery(true);
			$query->select('m.file_url_thumb');
			$query->from('#__virtuemart_medias AS m');
			$query->innerJoin('#__virtuemart_product_medias AS pm ON pm.virtuemart_media_id=m.virtuemart_media_id');
			$query->where('m.file_type='.$db->quote('product').' AND m.published=1 AND pm.virtuemart_product_id='.$parent_id);
			$db->setQuery($query);
			$result=$db->loadResult();
			return $result;
		}
		return false;
	}

}//end class

class listsHelper{
	/**
	 * get the categories list
	 * @param	Integer the group_id with which the categories are connected
	 * @param	String Attributes of the generated select
	 * @author 	Sakis Terz
	 * @since 	1.0
	 */
	function getCategories($group_id=NULL,$name='vm_cat[]',$attr=NULL,$default=NULL,$displayOnly=NULL){
		$db=JFactory::getDBO();
		$where=array();
		$where_str='';
		$innerJoin=array();
		$innerJoin_str='';
		if($displayOnly)$where_str='WHERE c.virtuemart_category_id IN('.implode(',',$displayOnly).')';

		$orderBy='cx.category_child_id';
		$innerJoin[]="INNER JOIN #__virtuemart_category_categories AS cx ON cx.category_child_id=c.virtuemart_category_id ";
		$fields=',cx.category_parent_id ,cx.category_child_id';

		if(count($innerJoin)>0)$innerJoin_str=implode(" ",$innerJoin);

		//format the final query
		$query="SELECT c.category_name AS text, c.virtuemart_category_id AS value $fields FROM #__virtuemart_categories_".VMLANGPRFX." AS c $innerJoin_str  $where_str ORDER BY $orderBy";
		$db->setQuery($query);
		$result=$db->loadObjectList();
		//print_r($query);
		if(!$displayOnly){
			$results=$this->orderVMcats($result);
			$spaces=$this->getVmCatsParents($result);

			//add the spaces
			for($a=0; $a<count($results); $a++){
				for($i=0; $i<$spaces[$results[$a]->value]; $i++){
					$results[$a]->text='&nbsp;'.$results[$a]->text;	//add the blanks
				}
			}
		}else $results=$result;

		$blank_item[] = JHTML::_('select.option', '0', JText::_( 'COM_PRODUCTBUILDER_SELECT_VM_CATEGORY' ));
		$items = array_merge($blank_item,$results);

		//set the default
		$selected=$blank_item;
		if($default)$selected=$default;
		else if($group_id)$selected=$this->getGroupCategories($group_id);


		$output = JHTML::_('select.genericlist', $items, $name, 'class="inputbox" '.$attr, 'value', 'text',  $selected);
		return $output;
	}


	//create spaces according to the categories hierarhy
	function getVmCatsParents($results){
		if(!$results)return;
		$blank=0;
		$blanks=array();
		$blanks[0]=0;
		foreach($results as $res){
			@$blanks[$res->category_child_id]=@$blanks[$res->category_parent_id];
			$blanks[$res->category_child_id]+=2;
		}
		return $blanks;
	}

	//order the categories
	function orderVMcats(&$categoryArr) {
		// Copy the Array into an Array with auto_incrementing Indexes
		$categCount=count($categoryArr);
		if($categCount>0){
			for($i=0; $i<$categCount; $i++){
				$resultsKey[$categoryArr[$i]->category_child_id]=$categoryArr[$i];
			}
			$key = array_keys($resultsKey); // Array of category table primary keys
			$nrows = $size = sizeOf($key); // Category count

			// Order the Category Array and build a Tree of it
			$id_list = array();
			$row_list = array();
			$depth_list = array();

			$children = array();
			$parent_ids = array();
			$parent_ids_hash = array();

			//Build an array of category references
			$category_temp = array();
			for ($i=0; $i<$size; $i++)
			{
				$category_tmp[$i] = &$resultsKey[$key[$i]];
				$parent_ids[$i] = $category_tmp[$i]->category_parent_id;

				if($category_tmp[$i]->category_parent_id == 0)
				{
					array_push($id_list,$category_tmp[$i]->category_child_id);
					array_push($row_list,$i);
					array_push($depth_list,0);
				}

				$parent_id = $parent_ids[$i];
				if (isset($parent_ids_hash[$parent_id]))
				{
					$parent_ids_hash[$parent_id][$i] = $parent_id;
				}
				else
				{
					$parent_ids_hash[$parent_id] = array($i => $parent_id);
				}
			}

			$loop_count = 0;
			$watch = array(); // Hash to store children
			while(count($id_list) < $nrows) {
				if( $loop_count > $nrows )
				break;
				$id_temp = array();
				$row_temp = array();

				for($i = 0 ; $i < count($id_list) ; $i++) {
					$id = $id_list[$i];
					$row = $row_list[$i];

					array_push($id_temp,$id);
					array_push($row_temp,$row);

					$children = @$parent_ids_hash[$id];

					if (!empty($children))
					{
						foreach($children as $key => $value) {
							if( !isset($watch[$id][$category_tmp[$key]->category_child_id])) {
								$watch[$id][$category_tmp[$key]->category_child_id] = 1;
								array_push($id_temp,$category_tmp[$key]->category_child_id);
								array_push($row_temp,$key);
							}
						}
					}
				}
				$id_list = $id_temp;
				$row_list = $row_temp;
				$loop_count++;
			}
			$orderedArray=array();
			for($i=0; $i<count($resultsKey); $i++){
				$orderedArray[$i]=$resultsKey[$id_list[$i]];
			}
			return $orderedArray;
		}return;
	}


	function getGroupCategories($group_id){
		$db=JFactory::getDBO();
		$query="SELECT vm_cat_id FROM #__pb_group_vm_cat_xref WHERE group_id=".$group_id;
		$db->setQuery($query);
		$cats=$db->loadColumn();
		return $cats;
	}

	/**
	 * Returns a manufacturers list
	 * @author Sakis Terzis
	 * @since 2.0
	 */
	function getVMmanuf($default=NULL){
		$db=JFactory::getDBO();
		$query=$db->getQuery(true);
		$query->select('mnf.virtuemart_manufacturer_id AS value, mnf_l.mf_name AS text');
		$query->from('`#__virtuemart_manufacturers` AS mnf');
		$query->join('LEFT', '#__virtuemart_manufacturers_'.VMLANGPRFX.' AS mnf_l ON mnf.virtuemart_manufacturer_id=mnf_l.virtuemart_manufacturer_id');
		$query->order('text ASC');

		$db->setQuery($query);
		$vm_manuf=$db->loadObjectList();

		//create the generic list
		$blank_field[]=JHTML::_('select.option','0', JText::_('COM_PRODUCTBUILDER_SELECT_VM_MANUFACTURER'));

		if(count($vm_manuf)>0){
			foreach($vm_manuf as $vm){
				//give values to the select option
				$lists[]= JHTML::_('select.option', $vm->value, $vm->text);
			}
		}else return;

		$lists=array_merge($blank_field,$lists);
		//set the default
		if (isset($default))$selected=$default;
		else $selected=$blank_field;

		$input=JHTML::_('select.genericlist',$lists,'filter_virtuemart_manufacturer','class="inpubox" size="1" onchange="this.form.submit();"','value','text',$selected);//creates the select tag
		return $input;
	}

	/**
	 * Returns a list with the avialable tags
	 * @author Sakis Terzis
	 * @since 2.0
	 */
	function getPBtags($default=NULL){
		$lists=array();
		$db=JFactory::getDbo();
		$query=$db->getQuery(true);
		$query->select('id AS value , name AS text');
		$query->from('#__pb_tags');
		$query->order('text ASC');
		$db->setQuery($query);
		$tags=$db->loadObjectList();

		//create the generic list
		$blank_field[]=JHTML::_('select.option','0', JText::_( 'COM_PRODUCTBUILDER_SELECT_TAGS' ));

		if(count($tags)>0){
			foreach($tags as $t){
				$lists[]= JHTML::_('select.option',$t->value,  $t->text);
		 }
	 }else return;

	 $lists=array_merge($blank_field,$lists);

	 if ($default){//decide the value thats being viewed
	 	$selected=$default;
	 	//JRequest::setVar('vm_cat',$this->VM_cat);
	 }
	 else $selected=$blank_field;

	 $input=JHTML::_('select.genericlist',$lists,'filter_tag','class="inpubox" size="1" onchange="submitform();"','value','text',$selected);//creates the select tag
	 return $input;
	}


	/**
	 * Returns a list with the avialable groups
	 * @author Sakis Terzis
	 * @since 2.0
	 */
	function getPBgroups($default=NULL){
		$lists=array();
		$db=JFactory::getDbo();
		$query=$db->getQuery(true);
		$query->select('id AS value , name AS text, product_id AS prod');
		$query->from('#__pb_groups');
		$query->order('prod,ordering ASC');
		$db->setQuery($query);
		$gr=$db->loadObjectList();

		//create the generic list
		$blank_field[]=JHTML::_('select.option','0', JText::_( 'COM_PRODUCTBUILDER_SELECT_PB_GROUP' ));

		if(count($gr)>0){
			foreach($gr as $g){
				//give values to the select option
				$query="SELECT name FROM #__pb_products WHERE id=$g->prod";
				$db->setQuery($query);
				$pb_prod=$db->loadResult();
				$lists[]= JHTML::_('select.option',$g->value,  $pb_prod.'/'.$g->text);
		 }

	 }else return;

	 $lists=array_merge($blank_field,$lists);

	 if ($default){//decide the value thats being viewed
	 	$selected=$default;
	 	//JRequest::setVar('vm_cat',$this->VM_cat);
	 }
	 else $selected=$blank_field;

	 $input=JHTML::_('select.genericlist',$lists,'filter_pb_group_id','class="inpubox" size="1" onchange="submitform();"','value','text',$selected);//creates the select tag
	 return $input;
	}
}
