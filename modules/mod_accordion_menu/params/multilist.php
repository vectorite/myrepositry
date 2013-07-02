<?php 
/*------------------------------------------------------------------------
# mod_accordion_menu - Accordion Menu - Offlajn.com 
# ------------------------------------------------------------------------
# author    Roland Soos 
# copyright Copyright (C) 2012 Offlajn.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.offlajn.com
-------------------------------------------------------------------------*/
?>
<?php
/**
* @copyright	Copyright (C) 2009 Open Source Matters. All rights reserved.
* @license	GNU/GPL
*/
 
// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();
 
/**
 * Renders a multiple item select element
 *
 */
 
class JElementMultiList extends JOfflajnFakeElementBase
{
	/**
	* Element name
	*
	* @access	protected
	* @var		string
	*/
	var	$_name = 'MultiList';
 
	function universalfetchElement($name, $value, &$node)
	{
		// Base name of the HTML control.
		$ctrl	= $name;
 
		// Construct an array of the HTML OPTION statements.
		$options = array ();
		foreach ($node->children() as $option)
		{
			$val	= (!method_exists($option, 'getAttribute') ? $option->attributes('value') : $option->getAttribute('value'));
			$text	= $option->data();
			$options[] = JHTML::_('select.option', $val, JText::_($text));
		}
 
		// Construct the various argument calls that are supported.
		$attribs	= ' ';
		if ($v = $node->attributes( 'size' )) {
			$attribs	.= 'size="'.$v.'"';
		}
		if ($v = (!method_exists($node, 'getAttribute') ? $node->attributes('class') : $node->getAttribute('class')) ) {
			$attribs	.= 'class="'.$v.'"';
		} else {
			$attribs	.= 'class="inputbox"';
		}
    
		if ($m = (!method_exists($node, 'getAttribute') ? $node->attributes('multiple') : $node->getAttribute('multiple')))
		{
			$attribs	.= ' multiple="multiple"';
      if(version_compare(JVERSION,'1.6.0','l')) {
			 $ctrl		.= '[]';
      }
		}
		// Render the HTML SELECT list.
		return JHTML::_('select.genericlist', $options, $ctrl, $attribs, 'value', 'text', $value, $this->generateId($name) );
	}
}

if(version_compare(JVERSION,'1.6.0','ge')) {
  class JFormFieldMultiList extends JElementMultiList {}
}