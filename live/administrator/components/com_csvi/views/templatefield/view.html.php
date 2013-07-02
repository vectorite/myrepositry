<?php
/**
 * Template field editing view
 *
 * @package 	CSVI
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2012 RolandD Cyber Produksi
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: view.html.php 1924 2012-03-02 11:32:38Z RolandD $
 */

defined( '_JEXEC' ) or die;

jimport( 'joomla.application.component.view' );

/**
 * Template field edit View
 *
* @package CSVI
 */
class CsviViewTemplatefield extends JView {

	/**
	 * Show a template field edit screen
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		public
	 * @param 		string $tpl template file to use
	 * @return 		void
	 * @since 		1.0
	 */
	public function display($tpl = null) {
		// Load the data
		$this->form		= $this->get('Form');
		$this->item		= $this->get('Item');
		$this->state	= $this->get('State');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}
		
		// Display it all
		parent::display($tpl);
	}
}
?>