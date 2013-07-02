<?php
/**
 *  @package AdminTools
 *  @copyright Copyright (c)2010-2011 Nicholas K. Dionysopoulos
 *  @license GNU General Public License version 3, or later
 *  @version $Id$
 */

// Protect from unauthorized access
defined('_JEXEC') or die('Restricted Access');

// Load framework base classes
jimport('joomla.application.component.view');

class AdmintoolsViewDbtools extends FOFViewHtml
{
	protected function onBrowse($tpl = null)
	{
		$lastTable = $this->getModel()->getState('lasttable','');
		$percent = $this->getModel()->getState('percent','');
		
		$this->setLayout('optimize');

		$document = JFactory::getDocument();
		$script = "window.addEvent( 'domready' ,  function() {\n";
		$script .= "$('progressbar-inner').setStyle('width', '$percent%');\n";
		if(!empty($lastTable)) {
			$script .= "document.forms.adminForm.submit();\n";
		} else {
			$script .= "window.setTimeout('parent.SqueezeBox.close();', 3000);\n";
		}
		$script .= "});\n";
		$document->addScriptDeclaration($script);
	}
}