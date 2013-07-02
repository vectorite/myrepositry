<?php
/**
 *  @package AdminTools
 *  @copyright Copyright (c)2010-2011 Nicholas K. Dionysopoulos
 *  @license GNU General Public License version 3, or later
 *  @version $Id$
 */

// Protect from unauthorized access
defined('_JEXEC') or die('Restricted Access');

class AdmintoolsViewWafconfig extends FOFViewHtml
{
	protected function onBrowse($tpl = null)
	{
		// Set the toolbar title
		$model = $this->getModel();
		$config = $model->getConfig();

		$this->assign('wafconfig',			$config);
	}
}