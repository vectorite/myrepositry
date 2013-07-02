<?php
/**
 *  @package AdminTools
 *  @copyright Copyright (c)2010-2013 Nicholas K. Dionysopoulos
 *  @license GNU General Public License version 3, or later
 *  @version $Id$
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

class AdmintoolsViewLogs extends FOFViewJson
{
	function onBrowse($tpl = null)
	{
		// I have to override parent method or I'll FOFViewHtml will always save data,
		// overwriting incoming parameters
		return $this->onDisplay($tpl);
	}
}