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

class AdmintoolsViewAdminpw extends FOFViewHtml
{
	protected function onBrowse($tpl = null)
	{
		$model = $this->getModel();

		$this->assign('username',		JRequest::getVar('username',''));
		$this->assign('password',		JRequest::getVar('password',''));
		$this->assign('adminLocked',	$model->isLocked());
	}
}