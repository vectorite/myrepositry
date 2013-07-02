<?php 
/*------------------------------------------------------------------------
# smartslider - Smart Slider
# ------------------------------------------------------------------------
# author    Roland Soos 
# copyright Copyright (C) 2011 Offlajn.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.offlajn.com
-------------------------------------------------------------------------*/
?>
<?php
defined('_JEXEC') or die('Restricted access');

class JElementEasing extends JOfflajnFakeElementBase{
	var	$_name = 'Easing';

	function universalfetchElement($name, $value, &$node){


		// path to images directory
		$path		= JPATH_ROOT.DS.$node->attributes('directory');
		$filter		= $node->attributes('filter');
		$exclude	= $node->attributes('exclude');
		$stripExt	= $node->attributes('stripext');
		$easings = array(
      "dojo.fx.easing.linear" => "Linear",
      "dojo.fx.easing.quadIn" => "Quad In",
      "dojo.fx.easing.quadOut" => "Quad Out",
      "dojo.fx.easing.quadInOut" => "Quad In Out",
      "dojo.fx.easing.cubicIn" => "Cubic In",
      "dojo.fx.easing.cubicOut" => "Cubic Out",
      "dojo.fx.easing.cubicInOut" => "Cubic In Out",
      "dojo.fx.easing.quartIn" => "Quart In",
      "dojo.fx.easing.quartOut" => "Quart Out",
      "dojo.fx.easing.quartInOut" => "Quart In Out",
      "dojo.fx.easing.quintIn" => "Quint In",
      "dojo.fx.easing.quintOut" => "Quint Out",
      "dojo.fx.easing.quintInOut" => "Quint In Out",
      "dojo.fx.easing.sineIn" => "Sine In",
      "dojo.fx.easing.sineOut" => "Sine Out",
      "dojo.fx.easing.sineInOut" => "Sine In Out",
      "dojo.fx.easing.expoIn" => "Expo In",
      "dojo.fx.easing.expoOut" => "Expo Out",
      "dojo.fx.easing.expoInOut" => "Expo In Out",
      "dojo.fx.easing.circIn" => "Circ In",
      "dojo.fx.easing.circOut" => "Circ Out",
      "dojo.fx.easing.circInOut" => "Circ In Out",
      "dojo.fx.easing.backIn" => "Back In",
      "dojo.fx.easing.backOut" => "Back Out",
      "dojo.fx.easing.backInOut" => "Back In Out",
      "dojo.fx.easing.bounceIn" => "Bounce In",
      "dojo.fx.easing.bounceOut" => "Bounce Out",
      "dojo.fx.easing.bounceInOut" => "Bounce In Out"
    );

		$options = array();

		if (!$node->attributes('hide_default'))
		{
		  $ks = array_keys($easings);
			$options[] = JHTML::_('select.option', $ks[0], '- '.JText::_('Use default').' -');
		}

		if ( is_array($easings) )
		{
			foreach ($easings as $k => $easing)
			{
				if ($exclude)
				{
					if (preg_match( chr( 1 ) . $exclude . chr( 1 ), $easing ))
					{
						continue;
					}
				}
				$options[] = JHTML::_('select.option', $k, $easing);
			}
		}

		return JHTML::_('select.genericlist',  $options, $name, 'class="inputbox"', 'value', 'text', $value, $this->generateId($name));
	}
}

if(version_compare(JVERSION,'1.6.0','ge')) {
  class JFormFieldEasing extends JElementEasing {}
}
