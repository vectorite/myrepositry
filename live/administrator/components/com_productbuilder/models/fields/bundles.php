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
Class JFormFieldBundles extends JFormField{
	/**
	 * Method to get the field input markup.
	 *
	 *
	 * @return	string	The field input markup.
	 * @since	1.6
	 */
	protected function getInput()
	{	$html='';
		$bundles=$this->getBundles();
		if($bundles && count($bundles)>0){
			
			$attr = '';

			// Initialize some field attributes.
			$attr .= $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : '';
			$attr .= $this->element['size'] ? ' size="'.(int) $this->element['size'].'"' : '';
			$options=array();
			$options[]=JHtml::_('select.option', '', JText::_('JSELECT'));
			$finalBundles=array_merge($options,$bundles);
			$html.=JHtml::_('select.genericlist',$finalBundles,$this->name,trim($attr),'value','text',$this->value,$this->id);
		}
		return $html;
	}

	protected function getBundles(){
		$db=JFactory::getDbo();
		$query=$db->getQuery(true);
		$query->select('id AS value, name AS text');
		$query->from('#__pb_products');
		$query->order('name ASC');
		$db->setQuery($query);
		return $db->loadObjectList();
	}
}