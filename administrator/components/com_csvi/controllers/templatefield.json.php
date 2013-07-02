<?php
/**
 * Template field controller
 *
 * @package 	CSVI
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2012 RolandD Cyber Produksi
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: settings.php 1924 2012-03-02 11:32:38Z RolandD $
 */

defined( '_JEXEC' ) or die;

jimport('joomla.application.component.controllerform');

/**
 * Settings Controller
 *
 * @package    CSVI
 */
class CsviControllerTemplatefield extends JControllerForm {
	
	/**
	 * Construct the class
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		public
	 * @param
	 * @return
	 * @since 		4.3
	 */
	public function __construct($config = array()) {
	
		parent::__construct($config);
	
		// Define mappings
		$this->registerTask('unpublish', 'publish');
	}
	
	/**
	 * Store the template field order 
	 * 
	 * @copyright 
	 * @author 		RolandD
	 * @todo 
	 * @see 
	 * @access 		public
	 * @param 
	 * @return 
	 * @since 		4.3
	 */
	public function saveOrder() {
		$model = $this->getModel();
		$model->saveOrder();
	}
	
	/**
	 * Reorder the template field order
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		public
	 * @param
	 * @return
	 * @since 		4.3
	 */
	public function renumberFields() {
		$jinput = JFactory::getApplication()->input;
		$template_id = $jinput->get('template_id', 0, 'int');
		$model = $this->getModel();
		$model->renumberFields($template_id);
	}
	
	/**
	 * Store a template field in the database
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		public
	 * @param
	 * @return
	 * @since 		4.2
	 */
	public function storeTemplateField() {
		$model = $this->getModel();
		$model->storeTemplateField();
	}
	
	/**
	 * Delete a template field in the database
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		public
	 * @param
	 * @return
	 * @since 		4.2
	 */
	public function deleteTemplateField() {
		$model = $this->getModel();
		$model->deleteTemplateField();
	}
	
	/**
	 * Publish/unpublish the process field 
	 * 
	 * @copyright 
	 * @author 		RolandD
	 * @todo 
	 * @see 
	 * @access 		public
	 * @param 
	 * @return 
	 * @since 		4.3
	 */
	public function publish() {
		$task = $this->getTask();
		$model = $this->getModel();
		$model->switchState($task);
	}
}
?>
