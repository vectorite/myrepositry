<?php
/**
 * @package AkeebaBackup
 * @copyright Copyright (c)2009-2013 Nicholas K. Dionysopoulos
 * @license GNU General Public License version 3, or later
 *
 * @since 3.0
 */

// Protect from unauthorized access
defined('_JEXEC') or die();
/**
 * Regular expression based db filters management controller class
 *
 */
class AkeebaControllerRegexdbfilter extends AkeebaControllerDefault
{
	public function  __construct($config = array()) {
		parent::__construct($config);

		$base_path = JPATH_COMPONENT_ADMINISTRATOR.'/plugins';
		$model_path = $base_path.'/models';
		$view_path = $base_path.'/views';
		$this->addModelPath($model_path);
		$this->addViewPath($view_path);
	}

	public function execute($task) {
		if($task != 'ajax') {
			$task = 'browse';
		}

		parent::execute($task);
	}

	/**
	 * Display the main list
	 *
	 */
	public function browse($cachable = false, $urlparams = false)
	{
		parent::display($cachable, $urlparams);
	}

	/**
	 * AJAX proxy.
	 */
	public function ajax()
	{
		// Parse the JSON data and reset the action query param to the resulting array
		$action_json = $this->input->get('action', '', 'none', 2);
		$action = json_decode($action_json, true);

		$model = $this->getThisModel();
		$model->setState('action', $action);

		$ret = $model->doAjax();
		@ob_end_clean();
		echo '###'.json_encode($ret).'###';
		flush();
		JFactory::getApplication()->close();
	}
}