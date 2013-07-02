<?php
/**
 * Settings controller
 *
 * @package 	CSVI
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2012 RolandD Cyber Produksi
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: settings.php 1955 2012-03-30 21:57:05Z RolandD $
 */

defined( '_JEXEC' ) or die;

jimport('joomla.application.component.controllerform');

/**
 * Settings Controller
 *
 * @package    CSVI
 */
class CsviControllerSettings extends JControllerForm {

	/**
	 * Reset the settings
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		public
	 * @param
	 * @return
	 * @since 		3.1.1
	 */
	public function reset() {
		$model = $this->getModel('settings');

		if ($model->getResetSettings()) {
			$msg = JText::_('COM_CSVI_SETTINGS_RESET_SUCCESSFULLY');
			$msgtype = '';
		}
		else {
			$msg = JText::_('COM_CSVI_SETTINGS_NOT_RESET_SUCCESSFULLY');
			$msgtype = 'error';
		}
		$this->setRedirect('index.php?option=com_csvi&view=settings', $msg, $msgtype);
	}
}
?>
