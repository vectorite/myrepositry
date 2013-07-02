<?php
/**
 * @package AkeebaBackup
 * @copyright Copyright (c)2009-2013 Nicholas K. Dionysopoulos
 * @license GNU General Public License version 3, or later
 *
 * @since 3.3
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

class AkeebaControllerUpload extends AkeebaControllerDefault
{
	public function  __construct($config = array()) {
		parent::__construct($config);

		$base_path = JPATH_COMPONENT_ADMINISTRATOR.'/plugins';
		$model_path = $base_path.'/models';
		$view_path = $base_path.'/views';
		$this->addModelPath($model_path);
		$this->addViewPath($view_path);
	}

	/**
	 * This controller does not support a default task, thank you.
	 */
	public function display($cachable = false, $urlparams = false)
	{
		JError::raiseError(500, 'Invalid task');
		return false;
	}

	public function upload($cachable = false, $urlparams = false)
	{
		// Get the parameters
		$id = $this->getAndCheckId();
		$part = $this->input->get('part', 0, 'int');
		$frag = $this->input->get('frag', 0, 'int');

		// Check the backup stat ID
		if($id === false) {
			$url = 'index.php?option=com_akeeba&view=upload&tmpl=component&task=cancelled&id='.$id;
			$this->setRedirect($url, JText::_('AKEEBA_TRANSFER_ERR_INVALIDID'), 'error');
			return true;
		}

		$model = $this->getThisModel();
		$model->setState('id', $id);
		$model->setState('part', $part);
		$model->setState('frag', $frag);

		$model->upload();

		$view = $this->getThisView();

		$id = $model->getState('id');
		$part = $model->getState('part');
		$frag = $model->getState('frag');
		$stat = $model->getState('stat');
		$remote_filename = $model->getState('remotename');

		if($part >= 0) {
			if($part < $stat['multipart']) {
				$view->setLayout('uploading');
				$view->assign('parts',$stat['multipart']);
				$view->assign('part', $part);
				$view->assign('frag', $frag);
				$view->assign('id', $id);
				$view->assign('done', 0);
				$view->assign('error', 0);
			} else {
				$view->setLayout('done');
				$view->assign('done', 1);
				$view->assign('error', 0);
			}
		} else {
			$view->assign('done', 0);
			$view->assign('error', 1);
			$view->setLayout('error');
		}

		parent::display($cachable, $urlparams);
	}

	public function cancelled($cachable = false, $urlparams = false)
	{
		$view = $this->getThisView();

		$view->setLayout('error');

		parent::display($cachable, $urlparams);
	}

	public function start($cachable = false, $urlparams = false)
	{
		$id = $this->getAndCheckId();

		// Check the backup stat ID
		if($id === false) {
			$url = 'index.php?option=com_akeeba&view=upload&tmpl=component&task=cancelled&id='.$id;
			$this->setRedirect($url, JText::_('AKEEBA_TRANSFER_ERR_INVALIDID'), 'error');
			return true;
		}

		$view = $this->getThisView();

		$view->assign('done', 0);
		$view->assign('error', 0);

		$view->assign('id', $id);
		$view->setLayout('default');

		parent::display($cachable, $urlparams);
	}

	/**
	 * Gets the stats record ID from the request and checks that it does exist
	 *
	 * @return bool|int False if an invalid ID is found, the numeric ID if it's valid
	 */
	private function getAndCheckId()
	{
		$id = $this->input->get('id', 0, 'int');

		if($id <= 0) return false;

		$statObject = AEPlatform::getInstance()->get_statistics($id);
		if(empty($statObject) || !is_array($statObject)) return false;

		return $id;
	}
}