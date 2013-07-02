<?php 
/*------------------------------------------------------------------------
# mod_jo_accordion - Vertical Accordion Menu for Joomla 1.5 
# ------------------------------------------------------------------------
# author    Roland Soos 
# copyright Copyright (C) 2011 Offlajn.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.offlajn.com
-------------------------------------------------------------------------*/
?>
<?php
// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

class JElementPromo extends JElement
{
  var $_moduleName = '';
  
	var	$_name = 'Promo';

	function fetchElement($name, $value, &$node, $control_name){
	 
	  return '<iframe src="http://offlajn.com/index2.php?option=com_content&Itemid=23&id=103&lang=en&view=article" frameborder="no" style="border: 0;" width="440" height="228"></iframe>';
	}
}