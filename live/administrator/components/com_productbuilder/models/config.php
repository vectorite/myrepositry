<?php
/**
 * product builder component
 * @package productbuilder
 * @version $Id: models/config.php  2012-2-20 sakisTerz $
 * @author Sakis Terz (sakis@breakDesigns.net)
 * @copyright	Copyright (C) 2010-2012 breakDesigns.net. All rights reserved
 * @license	GNU/GPL v2
 */


jimport( 'joomla.application.component.modeladmin' );

/**
 * Configuration class
 * @package productbuilder
 */
class productbuilderModelConfig extends JModelAdmin
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
	}


	/**
	 * Returns a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.	
	 * @return	JTable	A database object
	 * @since	2.0
	 */
	public function getTable($type = 'Config', $prefix = 'Table', $config = array())
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
	 * @since	2.0
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_productbuilder.config', 'config', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 * @return	mixed	The data for the form.
	 * @since	2.0
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

}
?>

