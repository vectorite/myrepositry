<?php
/**
 * product builder component
 * @package productbuilder
 * @version $Id:products.php  2012-2-16 sakisTerzis $
 * @author Sakis Terzis (sakis@breakDesigns.net)
 * @copyright	Copyright (C) 2010-2012 breakDesigns.net. All rights reserved
 * @license	GNU/GPL v2
 */


jimport( 'joomla.application.component.modeladmin' );

/**
 * @package productbuilder
 */
class productbuilderModelTag extends JModelAdmin
{
	/**
	 * Auto-populate the model state.
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
	 * Returns a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 *
	 * @return	JTable	A database object
	 */
	public function getTable($type = 'Tag', $prefix = 'Table', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get the record form.
	 *
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @author Sakis Terzis
	 * @return	mixed	A JForm object on success, false on failure
	 * @since	2.0
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_productbuilder.tag', 'tag', array('control' => 'jform', 'load_data' => $loadData));
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

	public function save ($data){
		//create a random color for the tag
		$r=rand(0,9);
		$g=rand(0,9);
		$b=rand(0,9);

		$color=$r.$r.$g.$g.$b.$b;
		$data['color']=$color;
		return parent::save($data);
	}


}

?>