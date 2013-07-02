<?php
/**
* @version		3.0
* @copyright	Copyright (C) 2007-2011 Stephen Brandon
* @license		GNU/GPL
*/

// Check to ensure this file is included in Joomla!
defined('JPATH_BASE') or die;

jimport('joomla.form.formfield');

class JFormFieldGeoipcheck extends JFormField
{
	/**
	 * Element name
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $type = 'Geoipcheck';

	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 * @since	1.6
	 */
	protected function getInput() {
		
		$files = $this->geoIPFolders();
		$foundcountry = $foundlitecity = $foundcity = false;
		$messages = array();
		
		foreach ($files as $file) {
			$proposed_file = JPATH_SITE.DS.$file.'GeoIP.dat';
			$country = is_file($proposed_file) && is_readable($proposed_file);
			$proposed_file = JPATH_SITE.DS.$file.'GeoLiteCity.dat';
			$litecity = is_file($proposed_file) && is_readable($proposed_file);
			$proposed_file = JPATH_SITE.DS.$file.'GeoIPCity.dat';
			$city = is_file($proposed_file) && is_readable($proposed_file);
			
			if ($country && !$foundcountry) {
				$age = intval((time() - filemtime(JPATH_SITE.DS.$file.'GeoIP.dat'))/(24*60*60));
				if ($age > 30) $age = "<span style='color:red;'>" . JText::sprintf("MM_GEOIP_PLEASE_UPDATE", $age,
					"<a href='http://www.maxmind.com/app/ip-location' target='_blank'>MaxMind</a>") . "</span>";
				else $age = "";
				$messages[] = $file . JText::_("MM_GEOIP_COUNTRY_CHECK_FOUND") . " $age";
				$foundcountry = true;
			}
			if ($litecity && !$foundlitecity) {
				$age = intval((time() - filemtime(JPATH_SITE.DS.$file.'GeoLiteCity.dat'))/(24*60*60));
				if ($age > 30) $age = "<span style='color:red;'>" . JText::sprintf("MM_GEOIP_PLEASE_UPDATE", $age,
					"<a href='http://www.maxmind.com/app/ip-location' target='_blank'>MaxMind</a>") . "</span>";
				else $age = "";
				$messages[] = $file . JText::_("MM_GEOIP_LITECITY_CHECK_FOUND") . " $age";
				$foundlitecity = true;
			}
			if ($city && !$foundcity) {
				$age = intval((time() - filemtime(JPATH_SITE.DS.$file.'GeoIPCity.dat'))/(24*60*60));
				if ($age > 30) $age = "<span style='color:red;'>" . JText::sprintf("MM_GEOIP_PLEASE_UPDATE", $age,
					"<a href='http://www.maxmind.com/app/ip-location' target='_blank'>MaxMind</a>") . "</span>";
				else $age = "";
				$messages[] = $file . JText::_("MM_GEOIP_CITY_CHECK_FOUND") . " $age";
				$foundcity = true;
			}
			
		}
		if ($foundcountry || $foundlitecity || $foundcity) {
			return "<b>".implode("<br/>",$messages).'</b>
			<br />' . JText::sprintf("MM_GEOIP_KEEP_UPTODATE",
				'<a href="http://www.maxmind.com/app/ip-location" target="_blank">MaxMind</a>');
		}
		return JText::_('GEOIP_DOWNLOAD_HELPTEXT');
	}
	
	function geoIPFolders() {
		return array(
			"administrator".DS."components".DS."com_chameleon".DS."geoip".DS,
			"geoip".DS,
			"GeoIP".DS,
			"geoIP".DS,
			"GEOIP".DS,
			"GEO IP".DS,
			"",
			"geo_ip".DS,
			"geo_IP".DS,
			"Geo_IP".DS
			);
		
	}
	
}