<?php
/**
 * @version 2.0.1 2012-08-17
 * @package Joomla
 * @subpackage Work Force
 * @copyright (C) 2012 the Thinkery
 * @license GNU/GPL see LICENSE.php
 */

defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');
require_once (JPATH_SITE.DS.'components'.DS.'com_workforce'.DS.'helpers'.DS.'html.helper.php' );

class JFormFieldCountry extends JFormFieldList
{
	protected $type = 'Country';

	public function getOptions()
	{
        $db		= JFactory::getDbo();

        $options = array();
        $options = workforceHTML::country_select_list('','','194', false, true);

		// Check for a database error.
		if ($db->getErrorNum()) {
			JError::raiseWarning(500, $db->getErrorMsg());
		}

		array_unshift($options, JHtml::_('select.option', '0', JText::_('COM_WORKFORCE_COUNTRY_SELECT')));

		return $options;
    }
}
