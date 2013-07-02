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

class AdmintoolsViewCleantmp extends JView
{
	public function display($tpl = null)
	{
		$model = $this->getModel();
		$state = $model->getState('scanstate',false);

		$total = max(1, $model->totalFolders);
		$done = $model->doneFolders;

		if($state)
		{
			if($total > 0)
			{
				$percent = min(max(round(100 * $done / $total),1),100);
			}

			$more = true;
		}
		else
		{
			$percent = 100;
			$more = false;
		}

		$this->assign('more', $more);
		$this->setLayout('default');

		JHTML::_('behavior.mootools');

		$script = "window.addEvent( 'domready' ,  function() {\n";
		$script .= "$('progressbar-inner').setStyle('width', '$percent%');\n";
		if($more) {
			$script .= "document.forms.adminForm.submit();\n";
		}
		$script .= "});\n";
		JFactory::getDocument()->addScriptDeclaration($script);

		parent::display();
	}
}