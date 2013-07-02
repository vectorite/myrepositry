<?php
/**
 * product builder component
 * @package productbuilder
 * @version $Id: fields/bundles.php  2012-2-6 sakisTerzis $
 * @author Sakis Terzis (sakis@breakDesigns.net)
 * @copyright	Copyright (C) 2010-2012 breakDesigns.net. All rights reserved
 * @license	GNU/GPL v2
 */

defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.access.access');
jimport('joomla.form.formfield');

/**
 *
 * Class that generates the bundles drop-down
 * @author Sakis Terzis
 */
Class JFormFieldColor extends JFormField{
	/**
	 * Method to get the field input markup.
	 *
	 *
	 * @return	string	The field input markup.
	 * @since	1.6
	 */
	protected function getInput()
	{	$document=JFactory::getDocument();
		//add scripts and styles
		$document->addStyleSheet(JURI::root().'administrator/components/com_productbuilder/assets/css/stylesheet.css');
		JHTML::script ('jscolor.js','administrator/components/com_productbuilder/assets/js/');
		JHTML::script ('config.js','administrator/components/com_productbuilder/assets/js/');
		$html='';

		$attr = '';

		// Initialize some field attributes.
		$attr .= $this->element['size'] ? ' size="'.(int) $this->element['size'].'"' : '';
		$attr .= $this->element['maxlength'] ? ' maxlength="'.(int) $this->element['maxlength'].'"' : '';
		$html.='<input type="text" name="'.$this->name.'" class="inputbox color {required:false}" value="'.$this->value.'"'.$attr.'/>';
		return $html;
	}
}