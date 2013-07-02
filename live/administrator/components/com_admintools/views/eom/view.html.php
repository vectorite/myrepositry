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

class AdmintoolsViewEom extends FOFViewHtml
{
	protected function onBrowse($tpl = null)
	{
		$model = $this->getModel();
		
		$isOffline = $model->isOffline();
		$htaccess = $model->getHtaccess();
		
		$this->assign('offline', $isOffline);
		$this->assign('htaccess', $htaccess);

		$this->setLayout('default');

		return true;
	}
}