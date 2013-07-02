<?php
/**
 *  @package AdminTools
 *  @copyright Copyright (c)2010-2011 Nicholas K. Dionysopoulos
 *  @license GNU General Public License version 3, or later
 *  @version $Id$
 */

// Protect from unauthorized access
defined('_JEXEC') or die('Restricted Access');

class AdmintoolsViewFixperms extends FOFViewHtml
{
	protected function onBrowse($tpl = null)
	{
		$model = $this->getModel();
		$state = $model->getState('scanstate',false);

		$total = $model->totalFolders;
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

			JToolBarHelper::back((ADMINTOOLS_JVERSION == '15') ? 'Back' : 'JTOOLBAR_BACK', 'index.php?option=com_admintools');
		}

		$this->assign('more', $more);
		$this->setLayout('default');

		$script = "window.addEvent( 'domready' ,  function() {\n";
		$script .= "$('progressbar-inner').setStyle('width', '$percent%');\n";
		if($more) {
			$script .= "document.forms.adminForm.submit();\n";
		}
		$script .= "});\n";
		JFactory::getDocument()->addScriptDeclaration($script);
	}
	
	public function onRun()
	{
		$this->onBrowse();
	}
}