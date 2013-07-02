<?php
/**
 *  @package AdminTools
 *  @copyright Copyright (c)2010-2011 Nicholas K. Dionysopoulos
 *  @license GNU General Public License version 3, or later
 *  @version $Id$
 */

// Protect from unauthorized access
defined('_JEXEC') or die('Restricted Access');

jimport('joomla.application.component.controller');

class AdmintoolsControllerWafexceptions extends FOFController
{
	protected function onBeforeApplySave(&$data)
	{
		$data['option'] = $data['foption'];
		$data['view'] = $data['fview'];
		$data['query'] = $data['fquery'];
	}
}
