<?php
/**
 * Form XML nodes handler
 *
 * @package 	CSVI
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2012 RolandD Cyber Produksi
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: csvi.php 1961 2012-04-06 09:23:02Z RolandD $
 */
 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

/**
 * Class for the category field in de product edit view.
 *
 * @package		ePahali.fields
 * @author		RolandD, Edward Dalmulder
 * @since		1.0
 */
class JFormFieldCsviXmlconfig extends JFormFieldList {
        
	/**
	 * The field type.
	 * @var string
	 */
	protected $type = 'CsviXmlconfig';
	
	
	/**
	 * Collect the options that will be available in the list 
	 * 
	 * @copyright 
	 * @author 		RolandD
	 * @todo 
	 * @see 
	 * @access 		public
	 * @param 
	 * @return 		array of available options
	 * @since 		1.0
	 */
	public function getOptions() {
		// Initialize variables.
		$options = array();
		$showRoot = $this->element['show_root'];
		if (($showRoot <> 'true') && ($showRoot <> 'false')) {
			$showRoot = 'true';
		}

		$db		= JFactory::getDbo();		
		$query	= $db->getQuery(true);
		
		// Get all categories
		$query->select('a.id AS value, a.title AS text, a.level');
		$query->from('#__csvi_xmlconfigs AS a');
		$query->join('LEFT', '`#__csvi_xmlconfigs` AS b ON a.lft > b.lft AND a.rgt < b.rgt');
		if ($showRoot == 'true') {
			$query->where('a.published IN (0,1)');
		}
		else {
			$query->where('a.published IN (0,1) AND a.level > 0');
		}
		$query->group('a.id');
		$query->order('a.lft ASC');

		// Get the options.
		$db->setQuery($query);
		$options = $db->loadObjectList();
		
		// Indent all categories after first level for readability
		for ($i = 0, $n = count($options); $i < $n; $i++)
		{
			// Translate ROOT
			if (($showRoot == 'true') && ($options[$i]->level == 0)) {
				$options[$i]->text = JText::_('JGLOBAL_ROOT_PARENT');
			}			
			else if ($options[$i]->level == 1) {
				$options[$i]->text = $options[$i]->text;
			}
			else {
				$options[$i]->text = str_repeat('- ',$options[$i]->level).$options[$i]->text;
				$disable = $this->element['disable_children'];
				if (($disable <> 'true') && ($disable <> 'false')) $disable = 'false';
				if ($disable == 'true') $options[$i]->disable = 'true';
			}
		}		
		
		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);
		
		return $options; 
	}
}
?>
