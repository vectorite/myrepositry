<?php
/**
 * @version		2.5.7
 * @package		Simple Image Gallery Pro
 * @author		JoomlaWorks - http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2012 JoomlaWorks Ltd. All rights reserved.
 * @license		Commercial - This code cannot be redistributed without permission from JoomlaWorks Ltd.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

if(version_compare(JVERSION,'1.6.0','ge')) {
	jimport('joomla.form.formfield');
	class JFormFieldHeader extends JFormField {

		var	$type = 'header';

		function getInput(){
			return JElementHeader::fetchElement($this->name, $this->value, $this->element, $this->options['control']);
		}

		function getLabel(){
			return '';
		}

	}
}

jimport('joomla.html.parameter.element');

class JElementHeader extends JElement {

	var	$_name = 'header';

	function fetchElement($name, $value, &$node, $control_name){
		$document = & JFactory::getDocument();
		$document->addStyleDeclaration('
			.jwHeaderClr { clear:both; height:0; line-height:0; border:none; float:none; background:none; padding:0; margin:0; }
			.jwHeaderContainer { clear:both; font-weight:bold; font-size:12px; color:#369; margin:12px 0 4px; padding:0; background:#d5e7fa; border-bottom:2px solid #96b0cb; float:left; width:100%; }
			.jwHeaderContainer15 { clear:both; font-weight:bold; font-size:12px; color:#369; margin:0; padding:0; background:#d5e7fa; border-bottom:2px solid #96b0cb; float:left; width:100%; }
			.jwHeaderContent { padding:6px 8px; }
		');
		if(version_compare(JVERSION,'1.6.0','ge')) {
			return '<div class="jwHeaderContainer"><div class="jwHeaderContent">'.JText::_($value).'</div><div class="jwHeaderClr"></div></div>';
		} else {
			return '<div class="jwHeaderContainer15"><div class="jwHeaderContent">'.JText::_($value).'</div><div class="jwHeaderClr"></div></div>';
		}
	}

	function fetchTooltip($label, $description, &$node, $control_name, $name){
		return NULL;
	}
}
