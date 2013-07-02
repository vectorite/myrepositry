<?php
/**
 * @version		1.6.0
 * @package		com_contactenhanced
 * @copyright	Copyright (C) 2006 - 2010 Ideal Custom Software Development
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

// Component Helper
jimport('joomla.application.component.helper');
jimport('joomla.application.categories');

/**
 * Contact Component Category Tree
 *
 * @static
 * @package		com_contactenhanced
* @since 1.6
 */
class ContactenhancedCategories extends JCategories
{
	public function __construct($options = array())
	{
		$options['table'] = '#__ce_details';
		$options['extension'] = 'com_contactenhanced';
		$options['statefield'] = 'published';
		parent::__construct($options);
	}
}