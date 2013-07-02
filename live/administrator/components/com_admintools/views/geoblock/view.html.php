<?php
/**
 *  @package AdminTools
 *  @copyright Copyright (c)2010-2011 Nicholas K. Dionysopoulos
 *  @license GNU General Public License version 3, or later
 *  @version $Id$
 */

// Protect from unauthorized access
defined('_JEXEC') or die('Restricted Access');

class AdmintoolsViewGeoblock extends FOFViewHtml
{
	protected function onBrowse($tpl = null)
	{
		$model = $this->getModel();
		$model->getConfig();
		
		$this->assign('countries',		$model->getCountries());
		$this->assign('continents',		$model->getContinents());
		
		$this->assign('hasDat',			$model->hasDat());
	}
}