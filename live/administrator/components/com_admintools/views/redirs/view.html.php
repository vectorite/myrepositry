<?php
/**
 *  @package AdminTools
 *  @copyright Copyright (c)2010-2011 Nicholas K. Dionysopoulos
 *  @license GNU General Public License version 3, or later
 *  @version $Id$
 */

// Protect from unauthorized access
defined('_JEXEC') or die('Restricted Access');

class AdmintoolsViewRedirs extends FOFViewHtml
{
	protected function onBrowse($tpl = null)
	{
		// Add toolbar buttons
		JToolBarHelper::back((ADMINTOOLS_JVERSION == '15') ? 'Back' : 'JTOOLBAR_BACK', 'index.php?option='.JRequest::getCmd('option'));
		
		$model = $this->getModel();
		$urlredirection = $model->getRedirectionState();
		$this->assign('urlredirection',$urlredirection);

		require_once JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'select.php';

		// Run the parent method
		parent::onDisplay();
	}
}