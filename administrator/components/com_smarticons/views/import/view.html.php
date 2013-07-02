<?php
/**
 * @package SmartIcons Component for Joomla! 2.5
 * @version $Id: view.html.php 4 2012-01-21 01:16:14Z smarticons $
 * @author SUTA Bogdan-Ioan
 * @copyright (C) 2011 SUTA Bogdan-Ioan
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

// no direct access

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

class SmartIconsViewImport extends JView
{
	function display($tpl = null) {
		// get the form
		$this->form = $this->get('Form');
		
		//set the document
		$this->setDocument();
			
		parent::display($tpl);
		
	}
	
	protected function setDocument() {
		$document = JFactory::getDocument();
		$document->setTitle(JText::_('COM_SMARTICONS_ADMINISTRATION'));
	}
}