<?php
/**
 * product builder component
 * @package productbuilder
 * @version $Id:2 product.php  2012-2-2 sakisTerzis $
 * @author Sakis Terzis (sakis@breakDesigns.net)
 * @copyright	Copyright (C) 2010-2012 breakDesigns.net. All rights reserved
 * @license	GNU/GPL v2
 */

defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.application.component.modeladmin' );

//used for the image file upload
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.archive');


/**
 * Product Model
 * @package productbuilder
 */
class productbuilderModelProduct extends JModelAdmin
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
		$pk = (int) JRequest::getInt('id');
		$this->setState($this->getName().'.id', $pk);
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
	 //get the Image
	 if(isset($result->id)){
	 	/*$img_path=$result->image_path;
	 	if(JFile::exists($img_path)) {  $result->image_path=JURI::root().$result->image_path;}
	 	else $result->image_path='';*/
	 }else {
	 	$result->ordering=$this->getMaxOrder()+1;
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
	public function getTable($type = 'Product', $prefix = 'Table', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get the record form.
	 *
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 *
	 * @return	mixed	A JForm object on success, false on failure
	 * @since	1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_productbuilder.product', 'product', array('control' => 'jform', 'load_data' => $loadData));
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
	 * Gives the max order to the ordering field
	 * @return int maxorder
	 */
	function getMaxOrder()
	{
		$db=&JFactory::getDBO();

		$query=' SELECT MAX(ordering) FROM #__pb_products ';
		$db->setQuery($query);
		$this->result=$db->loadResult();
		return $this->result;
	}

	function getMaxCatId()
	{
		$db=&JFactory::getDBO();

		$query=' SELECT MAX(id) FROM #__pb_products';
		$db->setQuery($query);
		$this->result=$db->loadResult();
		return $this->result;
	}


	function save($data){
		//vars initilization

		//$data['description']=JRequest::getVar('description', '', 'post', 'string', JREQUEST_ALLOWHTML );

		if(!trim($data['alias'])){
			$data['alias']=$data['name'];
		}
		//sanitize alias
		$data['alias']=JFilterOutput::stringURLSafe($data['alias']);
		if(!$this->checkAlias($data['alias'],$data['id']))return false;
		if(parent::save($data))return true;
		return false;
	}

	/**
	 * Check if there is another alias which is the same
	 * @author	Sakis Terzis
	 * @since	2.0
	 * @param	String	The alias
	 * @return	Boolean
	 */
	function checkAlias($alias,$id){
		$db=JFactory::getDbo();
		$query=$db->getQuery(true);
		$query->select('id');
		$query->from('#__pb_products');
		$query->where('alias='.$db->quote($alias).' AND id!='.$id);
		$db->setQuery($query);
		if(count($db->loadResultArray())>0){
			$this->setError(JText::_('COM_PRODUCTBUILDER_ALIAS_EXIST'));
			return false;
		}
		else return true;
	}

	function delete(&$pks)
	{
		$cids =$pks;
		$db=&$this->getDBO();


		if (count( $cids )>0)
		{

			//clean the other tables first as the products needs to exist to do the delete

			$query2='DELETE FROM #__pb_group_vm_cat_xref WHERE group_id IN(SELECT id FROM #__pb_groups WHERE product_id IN ('.implode(',',$cids).'))';
			$db->setQuery($query2);
			if(!$db->query()){
				return($db->getErrorMsg());

			}
			$query3='DELETE FROM #__pb_group_vm_prod_xref WHERE group_id IN(SELECT id FROM #__pb_groups WHERE product_id IN ('.implode(',',$cids).'))';
			$db->setQuery($query3);
			if(!$db->query()){
				return($db->getErrorMsg());

			}
			$queryy='DELETE FROM #__pb_groups WHERE product_id IN ('.implode(',',$cids).')';
			$db->setQuery($queryy);
			if(!$db->query()){
				return($db->getErrorMsg());
			}
			//delete the products
			$query='DELETE FROM #__pb_products'.
			' WHERE id IN ('.implode(',',$cids).')';
			$db->setQuery($query);
			if(!$db->query()){
				return $db->getErrorMsg();
			}
		}
		return true;
	}
}