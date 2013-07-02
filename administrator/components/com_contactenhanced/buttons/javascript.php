<?php
/**
* @package		Contact Enhanced
* @copyright	Copyright (C) 2006 - 2010 Ideal Custom Software Development
 * @license		GNU/GPL, see license.txt
 */

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

/**
 * Renders a link button
 *
 * @package 	Joomla.Framework
* @since		1.5
 */
class JButtonJavascript extends JButton
{
	/**
	 * Button type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'Javascript';

	function fetchButton( $type='Javascript', $name = 'back', $text = '', $url = null, $attributes=null )
	{
		$text	= JText::_($text);
		$class	= $this->fetchIconClass($name);
		$doTask	= $this->_getCommand($url);

		$html	= '<a href="'.$doTask.'" '.$attributes.' >';
		$html .= "<span class=\"$class\" title=\"$text\">\n";
		$html .= "</span>";
		$html	.= "$text";
		$html	.= "</a>\n";

		return $html;
	}

	/**
	 * Get the button CSS Id
	 *
	 * @access	public
	 * @return	string	Button CSS Id
	 * @since	1.5
	 */
	function fetchId($name)
	{
		return $this->_parent->getName().'-'.$name;
	}

	/**
	 * Get the JavaScript command for the button
	 *
	 * @access	private
	 * @param	object	$definition	Button definition
	 * @return	string	JavaScript command string
	 * @since	1.5
	 */
	function _getCommand($url) {
		if(!$url){
			$url ='#';
		}
		return $url;
	}
}