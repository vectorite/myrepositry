<?php
/**
 * product builder component
 * @package productbuilder
 * @version $Id: models/group.php  2012-2-7 sakisTerz $
 * @author Sakis Terz (sakis@breakDesigns.net)
 * @copyright	Copyright (C) 2010-2012 breakDesigns.net. All rights reserved
 * @license	GNU/GPL v2
 */


defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.application.component.modeladmin' );

/**
 * @package Joomla
 * @subpackage Config
 */
class productbuilderModelGroup extends JModelAdmin
{
	/**
	 * Auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since	2.0
	 */
	protected function populateState()
	{
		// Load the User state.
		$app=JFactory::getApplication('administrator');
		$pk = (int) JRequest::getInt('id');
		$this->setState($this->getName().'.id', $pk);
		
		//reset the filters applied in the vm_products view(modal)-otherwise we may get no products after changing categories or connected products
		$vm_products_contentext_a='com_productbuilder.vm_products.assignproducts';
		$vm_products_contentext_b='com_productbuilder.vm_products.assigndefproduct';

		$app->setUserState($vm_products_contentext_a.'.filter.virtuemart_manufacturer','');
		$app->setUserState($vm_products_contentext_b.'.filter.virtuemart_manufacturer','');

		$app->setUserState($vm_products_contentext_a.'.filter.virtuemart_category','');
		$app->setUserState($vm_products_contentext_b.'.filter.virtuemart_category','');

		$app->setUserState($vm_products_contentext_a.'.filter.published','');
		$app->setUserState($vm_products_contentext_b.'.filter.published','');

		$app->setUserState($vm_products_contentext_a.'.filter.search','');
		$app->setUserState($vm_products_contentext_b.'.filter.search','');
	}


	/**
	 *
	 * Gets the item
	 * @author Sakis Terz
	 * @param integer An optional id of the object to get, otherwise the id from the model state is used.
	 */
	function getItem($pk = null)
	{
	 $result=parent::getItem($pk);
	 //get the quantity options
	 if($result->id){
		 $db=JFactory::getDbo();
		 $query=$db->getQuery(true);
		 $query->select('*');
		 $query->from('#__pb_quant_group');
		 $query->where('group_id='.$db->quote($result->id));
		 $db->setQuery($query);
		 $quantObj=$db->loadObject();
		 $result=(object)array_merge((array)$result,(array)$quantObj);
	 }
	 return $result;
	}


	/**
	 * Returns a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 *
	 * @return	JTable	A database object
	 */
	public function getTable($type = 'Group', $prefix = 'Table', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get the record form.
	 *
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 *@author Sakis Terzis
	 * @return	mixed	A JForm object on success, false on failure
	 * @since	2.0
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_productbuilder.group', 'group', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}
		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_productbuilder.edit.'.$this->getName().'.data', array());

		if (empty($data)) {
			$data = $this->getItem();
		}

		return $data;
	}

	/**
	 * Method to get Data after the select of a pbproduct in a group
	 */
	public function getGroupInfo($prod_id){
		$newOrdering=$this->getMaxOrder($prod_id);
		$pbproductlang=$this->getPbproductlang($prod_id);
		if($newOrdering && $pbproductlang){
		$info=array('ordering'=>$newOrdering,'pbprodlang'=>$pbproductlang);
		$infojson=json_encode($info);
		return $infojson;
		}return false;
	}
	
	/**
	 * Method to get the lang of the pbproduct of that group
	 *
	 * @return	object
	 */
	protected function getPbproductlang($prod_id)
	{
		$db=JFactory::getDbo();
		$query=$db->getQuery(true);
		$query->select('language');
		$query->from('#__pb_products');
		$query->where('id='.(int)$prod_id);
		$db->setQuery($query);
		$lang=$db->loadResult();
		if($lang)return $lang;
		return '*';
	}


	/**
	 * get the max ordering for the selected bundle
	 * @param integer the bundle id
	 * @return integer
	 * @since 1.0
	 * @author	Sakis
	 */
	function getMaxOrder($prod_id)
	{
		$db=&JFactory::getDBO();
		$query=' SELECT MAX(ordering) FROM #__pb_groups WHERE product_id='.$prod_id;
		$db->setQuery($query);
		$this->result=$db->loadResult();
		if($this->result)return $this->result+1;
		else return 1;
	}

	/**
	 * Saves the item and the related fields
	 * @param array the post data
	 * @return boolean
	 * @since 2.0
	 * @author	Sakis
	 */
	function save($data){
		$pk	= (!empty($data['id'])) ? $data['id'] : (int)$this->getState($this->getName().'.id');
		$vm_cats=JRequest::getVar('vm_cat',0,'','array');
		JArrayHelper::toInteger($vm_cats);
		$table=$this->getTable();

		/*return the data for dubug
		 $this->setError(json_encode($data));
		 return false;*/

		// Bind the form fields to the pb_groups table
		if (!$table->bind($data)) {
			$this->setError($table->getError());
			return false;
		}

		// Make sure the group record is valid
		if (!$table->check()) {
			$this->setError($table->getError());
			return false;
		}

		// Store the web link table to the database
		if (!$table->store()) {
			$this->setError($table->getError());
			return false;
		}

		$this->setState($this->getName().'.id', $table->id);
		$group_id=(int)$table->id;

		$db=JFactory::getDbo();
		//if the has_default_product option is set to false set also the default product to 0
		if($table->defOption==0){
			$query="UPDATE `#__pb_groups` SET defaultProd=0 WHERE id=".$group_id;
			$db->setQuery($query);
			if(!$db->query()){
				$this->setError($db->getErrorMsg());
				return false;
			}
		}

		//connect categories
		if($vm_cats && $table->editable){
			//firts delete the vmcats connected with that group
			$query="DELETE FROM `#__pb_group_vm_cat_xref` WHERE group_id IN(".$group_id.")";
			$db->setQuery($query);
			if(!$db->query()){
				return $this->_db->getErrorMsg();
			}

			//then insert any new selection
			foreach($vm_cats as $vmc){
				$query="INSERT INTO `#__pb_group_vm_cat_xref` (group_id,vm_cat_id) VALUES (".$group_id.','.$vmc.')';
				$db->setQuery($query);
				if(!$db->query()){
					return $this->_db->getErrorMsg();
				}
			}//foreach
		}//if vm_cats

		//quantity
		$query="DELETE FROM #__pb_quant_group WHERE group_id=".$group_id;
		$db->setQuery($query);
		$db->query();

		$query="INSERT INTO #__pb_quant_group (group_id,displ_qbox,def_quantity,q_box_type,start,end,pace) VALUES (".$group_id.",".$data['displ_qbox'].",".$data['def_quantity'].",".$data['q_box_type'].",".$data['start'].",".$data['end'].",".$data['pace'].")";
		$db->setQuery($query);
		$db->query();
		return true;
	}

	/**
	 Create a copy of the selected groups
	 @author Sakis Terz
	 @since 1.3.4
	 */
	function copy(){
		$db=JFactory::getDBO();
		$cids=JRequest::getVar('cid',array());
		JArrayHelper::toInteger($cids);  //santitize to be integer

		foreach($cids as $cid) {
			$row =& $this->getTable();
			$row->load($cid);
			$item=$row;
			$item->id=NULL;
			$item->name=$item->name.' ('.JText::_('Copy').')';
			$result=$item->store();
			if(!$result) return JText::_('COM_PRODUCTBUILDER_ERROR_COPYING').':'.$cid;  //return on error

			//store also the relationships from other tables

			//get the categories relationship
			if($row->connectWith==0){ //if connected with categories
				$query="INSERT INTO `#__pb_group_vm_cat_xref` (`group_id`,`vm_cat_id`) SELECT '{$item->id}' AS group_id, `vm_cat_id` FROM `#__pb_group_vm_cat_xref` WHERE `group_id`=$cid";
				$db->setQuery($query);
				if(!$db->query()){
					//return($db->getErrorMsg());
					return JText::_('COM_PRODUCTBUILDER_ERROR_SAVING_CATEGORIES_RELATIONSHIPS');
				}

			}else{//connected with products
				$query2="INSERT INTO `#__pb_group_vm_prod_xref` (`group_id`,`vm_product_id`) SELECT '{$item->id}' AS group_id, `vm_product_id` FROM `#__pb_group_vm_prod_xref` WHERE `group_id`=$cid";
				$db->setQuery($query2);
				if(!$db->query()){
					//return($db->getErrorMsg());
					return JText::_('COM_PRODUCTBUILDER_ERROR_SAVING_PRODUCT_RELATIONSHIPS');
				}
			}

			//set the quantity data for the copy
			$query3="INSERT INTO `#__pb_quant_group` SELECT '{$item->id}' AS group_id, `displ_qbox`,`q_box_type`,`def_quantity`,`start`,`end`,`pace` FROM `#__pb_quant_group` WHERE `group_id`=$cid";
			$db->setQuery($query3);
			if(!$db->query()){
				//return($db->getErrorMsg());
				return JText::_('COM_PRODUCTBUILDER_ERROR_SAVING_QUANTITY_RELATIONSHIPS');
			}

		}
		return true;
	}

	/**
	 * Deletes the group and all the records from related tables
	 * @author	Sakis Terzis
	 * @since	1.3.4
	 */
	function delete()
	{
		$cids = JRequest::getVar( 'cid', array(0), 'post', 'array' );
		$db=&$this->getDBO();

		$row =& $this->getTable();

		if (count( $cids )>0)
		{
			foreach($cids as $cid) {

				if (!$row->delete( $cid )) {
					$this->setError( $row->getErrorMsg() );
					return false;
				}

				//clean the other tables
				$query2="DELETE FROM #__pb_group_vm_cat_xref WHERE group_id=".$cid;
				$db->setQuery($query2);
				if(!$db->query()){
					$this->setError($db->getErrorMsg());
					return false;
				}
				$query3="DELETE FROM #__pb_group_vm_prod_xref WHERE group_id=".$cid;
				$db->setQuery($query3);
				if(!$db->query()){
					$this->setError($db->getErrorMsg());
					return false;
				}
				$query4="DELETE FROM #__pb_quant_group WHERE group_id=".$cid;
				$db->setQuery($query4);
				if(!$db->query()){
					$this->setError($db->getErrorMsg());
					return false;
				}
			}
		}
		return true;
	}

	/**
	 * A protected method to get a set of ordering conditions --
	 * Groups items based on a specific field and order the rows of each group seperately
	 * @param	object	A record object.
	 *
	 * @return	array	An array of conditions to add to add to ordering queries.
	 * @since	2.0
	 */
	protected function getReorderConditions($table)
	{
		$condition = array();
		$condition[] = 'product_id = '.(int) $table->product_id;
		return $condition;
	}
}
?>