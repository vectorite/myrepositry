<?php
/**
 * List the available fields
 *
 * @package 	CSVI
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2012 RolandD Cyber Produksi
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: csvitemplates.php 1924 2012-03-02 11:32:38Z RolandD $
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

jimport('joomla.form.helper');
JFormHelper::loadFieldClass('CsviForm');

/**
 * Select list form field with templates
 *
 * @package CSVI
 */
class JFormFieldCsviAvailableFields extends JFormFieldCsviForm {

	protected $type = 'CsviAvailableFields';

	/**
	 * Get the available fields
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		protected
	 * @param
	 * @return 		array	an array of options
	 * @since 		4.3
	 */
	protected function getOptions() {
		// Get the template ID
		$jinput = JFactory::getApplication()->input;
		$session = JFactory::getSession();
		$sess_template_id = $session->get('com_csvi.select_template', 0);
		if ($sess_template_id !== 0) $sess_template_id = unserialize($sess_template_id);
		$template_id = $jinput->get('template_id', $sess_template_id, 'int');
		
		// Load the selected template
		require_once(JPATH_COMPONENT_ADMINISTRATOR.'/helpers/template.php');
		$template = new CsviTemplate();
		$template->load($template_id);
		
		// Load the available fields
		require_once(JPATH_COMPONENT_ADMINISTRATOR.'/models/availablefields.php');
		$availablefields_model = new CsviModelAvailablefields();
		$fields = $availablefields_model->getAvailableFields($template->get('operation', 'options'), $template->get('component', 'options'), 'array');
		if (!is_array($fields)) $avfields = array();
		else {
			$avfields = array();
			foreach ($fields as $field) {
				$avfields[$field] = $field;
			}
		}
		return array_merge(parent::getOptions(), $avfields);
	}
}
?>
