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
	 * Gets the URL arguments to append to an item redirect. 
	 * 
	 * @copyright 
	 * @author		RolandD 
	 * @todo 
	 * @see 
	 * @access 		protected
	 * @param 
	 * @return		string with the append data 
	 * @since 		4.3
	 */
	protected function getRedirectToItemAppend($recordId = null, $urlVar = 'id') {
		$jinput = JFactory::getApplication()->input;
		$append = parent::getRedirectToItemAppend($recordId, $urlVar);
		$append .= '&template_id='.$jinput->get('template_id', 0, 'int');
		$append .= '&process='.$jinput->get('process');
		return $append;	
	}
	
	/**
	 * Save a template field
	 *
	 * @copyright
	 * @author 		RolanD
	 * @todo
	 * @see
	 * @access 		public
	 * @param
	 * @return
	 * @since 		4.3
	 */
	public function save($key = null, $urlVar = null) {
		if (parent::save($key, $urlVar)) {
			$this->setRedirect('');
			JFactory::getDocument()->addScriptDeclaration('window.parent.location.href = window.parent.location.href;');
			JFactory::getDocument()->addScriptDeclaration('window.parent.SqueezeBox.close();');
		}
	}
}
?>
