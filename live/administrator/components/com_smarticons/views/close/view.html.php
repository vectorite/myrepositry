<?php
/**
 * @package SmartIcons Component for Joomla! 2.5
 * @version $Id$
 * @author SUTA Bogdan-Ioan
 * @copyright (C) 2011 SUTA Bogdan-Ioan
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

// no direct access

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

class SmartIconsViewClose extends JView {

	/**
	 * Display the view
	 */
	function display($tpl = null) {
		// close a modal window
		JFactory::getDocument()->addScriptDeclaration('
			window.parent.location.href=window.parent.location.href;
			window.parent.SqueezeBox.close();
		');
	}
}
