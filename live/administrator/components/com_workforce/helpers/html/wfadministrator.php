<?php
/**
 * @version 2.0.1 2012-08-17
 * @package Joomla
 * @subpackage Work Force
 * @copyright (C) 2012 the Thinkery
 * @license GNU/GPL see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die;

abstract class JHtmlWfAdministrator
{
	static function featured($value = 0, $i, $canChange = true)
	{
		// Array of image, task, title, action
		$states	= array(
			0	=> array('disabled.png',	'employees.feature',	'COM_WORKFORCE_UNFEATURED',	'COM_WORKFORCE_FEATURE'),
			1	=> array('featured.png',	'employees.unfeature',	'COM_WORKFORCE_FEATURED',		'COM_WORKFORCE_UNFEATURE'),
		);
		$state	= JArrayHelper::getValue($states, (int) $value, $states[1]);
		$html	= JHtml::_('image','admin/'.$state[0], JText::_($state[2]), NULL, true);
		if ($canChange) {
			$html	= '<a href="#" onclick="return listItemTask(\'cb'.$i.'\',\''.$state[1].'\')" title="'.JText::_($state[3]).'">'.$html.'</a>';
		}

		return $html;
	}
}