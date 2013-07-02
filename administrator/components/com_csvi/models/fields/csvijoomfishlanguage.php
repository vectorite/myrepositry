<?php
/**
 * List JoomFish languages
 *
 * @package 	CSVI
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2012 RolandD Cyber Produksi
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: csvijoomfishlanguage.php 1924 2012-03-02 11:32:38Z RolandD $
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.helper');
JFormHelper::loadFieldClass('CsviForm');

/**
 * Select list form field with JoomFish languages
 *
 * @package CSVI
 */
class JFormFieldCsviJoomfishLanguage extends JFormFieldCsviForm {

	protected $type = 'CsviJoomfishLanguage';

	/**
	 * Specify the options to load
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		protected
	 * @param
	 * @return 		array	an array of options
	 * @since 		4.0
	 */
	protected function getOptions() {
		$conf = JFactory::getConfig();
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('table_name');
		$query->from('information_schema.tables');
		$query->where('table_schema = '.$db->Quote($conf->getValue('config.db')));
		$query->where('table_name = '.$db->Quote($conf->getValue('config.dbprefix').'languages'));
		$db->setQuery($query);
		$total = $db->loadResult();

		if (!empty($total)) {
			$query = $db->getQuery(true);
			$query->select($db->quoteName('title')." AS ".$db->quoteName('text'));
			$query->select($db->quoteName('lang_id')." AS ".$db->quoteName('value'));
			$query->from('#__languages');
			$query->order('title');
			$db->setQuery($query);
			return $db->loadObjectList();
		}
		else return array(JText::_('COM_CSVI_NO_LANGUAGES_FOUND'));
	}
}
?>
