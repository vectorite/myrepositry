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

class JElementFourfield extends JOfflajnFakeElementBase
{
  var $_moduleName = '';
  
	var	$_name = 'fourfield';

	function universalfetchElement($name, $value, &$node)
	{
		$size = 'size="4"';

    $value = htmlspecialchars(html_entity_decode($value, ENT_QUOTES), ENT_QUOTES);
    
    $document =& JFactory::getDocument();
    
    $id = $this->generateId($name);

    $change="if(this.value == '') this.value=0;dojo.byId('".$id."').value = dojo.byId('".$id."1').value+' '+dojo.byId('".$id."2').value+' '+dojo.byId('".$id."3').value+' '+dojo.byId('".$id."4').value;";
    
    $change = str_replace(array("\n","\r"),'',$change);
    $v = explode(' ', $value);
    $f= '<input onchange="this.onload();" onload="var vs = this.value.split(\' \'); dojo.byId(\''.$id.'1\').value = vs[0];dojo.byId(\''.$id.'2\').value = vs[1];dojo.byId(\''.$id.'3\').value = vs[2];dojo.byId(\''.$id.'4\').value = vs[3]; " type="hidden" name="'.$name.'" id="'.$id.'" value="'.$value.'"/>';
    $f.= '<span>'.$node->attributes( 'first' ).': </span><input style="margin-right:2px;" onchange="'.$change.'" type="text" name="a'.$name.'[1]" id="'.$id.'1" value="'.$v[0].'" class="color" '.$size.' />';
    $f.= '<span>'.$node->attributes( 'second' ).': </span><input style="margin-right:2px;" onchange="'.$change.'" type="text" name="a'.$name.'[2]" id="'.$id.'2" value="'.$v[1].'" class="color" '.$size.' />';
    $f.= '<span>'.$node->attributes( 'third' ).': </span><input style="margin-right:2px;" onchange="'.$change.'" type="text" name="a'.$name.'[3]" id="'.$id.'3" value="'.$v[2].'" class="color" '.$size.' />';
    $f.= '<span>'.$node->attributes( 'fourth' ).': </span><input style="margin-right:2px;" onchange="'.$change.'" type="text" name="a'.$name.'[4]" id="'.$id.'4" value="'.$v[3].'" class="color" '.$size.' />';
		return $f;
	}
	
}

if(version_compare(JVERSION,'1.6.0','ge')) {
  class JFormFourfield extends JElementFourfield {}
}