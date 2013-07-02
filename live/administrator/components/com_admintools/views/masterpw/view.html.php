<?php
/**
 *  @package AdminTools
 *  @copyright Copyright (c)2010-2011 Nicholas K. Dionysopoulos
 *  @license GNU General Public License version 3, or later
 *  @version $Id$
 */

// Protect from unauthorized access
defined('_JEXEC') or die('Restricted Access');

class AdmintoolsViewMasterpw extends FOFViewHtml
{
	protected function onBrowse($tpl = null)
	{
		$model = JModel::getInstance('Masterpw','AdmintoolsModel');
		$masterpw = $model->getMasterPassword();
		
		$this->assign('masterpw',			$masterpw);
		
		return parent::onBrowse($tpl);
	}
}