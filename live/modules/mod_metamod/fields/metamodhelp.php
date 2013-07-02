<?php
/**
* @version		3.0
* @copyright	Copyright (C) 2007-2011 Stephen Brandon
* @license		GNU/GPL
*/

// Check to ensure this file is included in Joomla!
defined('JPATH_BASE') or die;

jimport('joomla.form.formfield');

class JFormFieldMetamodhelp extends JFormField
{
	/**
	 * Element name
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $type = 'Metamodhelp';

	function getLabel() {
		return "";
	}

	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 * @since	1.6
	 */
	protected function getInput() {
		
		return '<div style="clear:left"></div>' . JText::_('MM_HELP_TEXT_INTRO') .
'<pre>
if ( MM_NOT_LOGGED_IN ) return 65;
if ( MM_LOGGED_IN )
     return "advert1";// ' . JText::_('MM_HELP_ADVERT1') .'
if ( MM_DAY_OF_WEEK == 1 )
     return 67; // ' . JText::_('MM_HELP_MONDAY_1') .'
if ( MM_DAY_OF_MONTH == 1 )
     return 68; // ' . JText::_('MM_HELP_FIRST_DAY_MONTH') .'
if ( MM_MONTH == 5 )
     return 69; // ' . JText::_('MM_HELP_MONTH_MAY') .'
if ( MM_YEAR == 2012 )
     return "70,user2"; // ' . JText::_('MM_HELP_YEAR_2012') .'
if ( $core_genius->inTimeSpan( "22:25 - 02:25" ) )
     return 71; // ' . JText::_('MM_HELP_HOURS_MINS_DAILY') .'
if ( $core_genius->inTimeSpan( "09:30:10 - 17:15:00" ) )
     return 72; // ' . JText::_('MM_HELP_HOURS_MINS_SECS_DAILY') .'
if ( MM_DATE >= 20120101 && MM_DATE <= 20120723)
     return 73; // ' . JText::_('MM_HELP_DATES') .'

if ( $fromCountryId == "US" ) return 55;
if ( $fromCountryId == "GB" ) return "55,56,57";
if ( $fromCountryId == "NL" ) return array(58,59,73);
if ( $fromCountryName == "New Zealand" ) return "user1";</pre>

<p>' . JText::_('MM_HELP_PHP_VARIABLES') .'</p>
<ul class="mm-variables">
	<li><b>$fromCountryId</b> - ' . JText::_('MM_HELP_FROM_COUNTRY_ID') .'</li>
	<li><b>$fromCountryName</b> - ' . JText::_('MM_HELP_FROM_COUNTRY_NAME') .'</li>
	<li><b>$geoip</b> - ' . JText::_('MM_HELP_GEOIP') .'</li>
	<li>
		<ul>
			<li><b>$geoip-&gt;country_name</b> - ' . JText::_('MM_HELP_GEOIP_COUNTRY_NAME') .'</li>
			<li><b>$geoip-&gt;country_code</b> - ' . JText::_('MM_HELP_GEOIP_COUNTRY_CODE') .'</li>
			<li><b>$geoip-&gt;country_code3</b> - ' . JText::_('MM_HELP_GEOIP_COUNTRY_CODE3') .'</li>
			<li><b>$geoip-&gt;region</b> - ' . JText::_('MM_HELP_GEOIP_REGION') .'</li>
			<li><b>$geoip-&gt;city</b> - ' . JText::_('MM_HELP_GEOIP_CITY') .'</li>
			<li><b>$geoip-&gt;postal_code</b> - ' . JText::_('MM_HELP_GEOIP_POSTAL_CODE') . '</li>
			<li><b>$geoip-&gt;latitude</b></li>
			<li><b>$geoip-&gt;longitude</b></li>
			<li><b>$geoip-&gt;area_code</b> - ' . JText::_('MM_HELP_GEOIP_AREA_CODE') .'</li>
			<li><b>$geoip-&gt;metro_code</b> - ' . JText::_('MM_HELP_GEOIP_METRO_CODE') .'</li>
			<li><b>$geoip-&gt;continent_code</b> - ' . JText::_('MM_HELP_GEOIP_CONTINENT_CODE') .'</li>
		</ul>
	</li>
	<li><b>$Itemid</b> - ' . JText::_('MM_HELP_ITEMID') .'</li>
	<li><b>$option</b> - ' . JText::_('MM_HELP_OPTION') .'</li>
	<li><b>$view</b> - ' . JText::_('MM_HELP_VIEW') .'</li>
	<li><b>$id</b> - ' . JText::_('MM_HELP_ID') .'</li>
	<li><b>$db</b> - ' . JText::_('MM_HELP_DB') .'</li>
	<li><b>$language</b> - ' . JText::_('MM_HELP_LANGUAGE') .'</li>
	<li><b>$language_code</b> - ' . JText::_('MM_HELP_LANGUAGE_CODE') .'</li>
	<li><b>$language_region</b> - ' . JText::_('MM_HELP_LANGUAGE_REGION') .'</li>
	<li><b>$user</b> - ' . JText::_('MM_HELP_USER_INFO') .'</li>
	<li>
		<ul>
			<li><b>$user-&gt;id</b> - ' . JText::_('MM_HELP_USER_ID') . '</li>
			<li><b>$user-&gt;name</b></li>
			<li><b>$user-&gt;username</b></li>
			<li><b>$user-&gt;email</b></li>
			<li><b>$user-&gt;groups</b> - ' . JText::_('MM_HELP_USERGROUPS') .'</li>
			<li><b>$user-&gt;registerDate</b> - ' . JText::_('MM_HELP_REGISTERDATE') .'</li>
			<li><b>$user-&gt;lastvisitDate</b> - ' . JText::_('MM_HELP_LASTVISITDATE') .'</li>
		</ul>
	</li>
</ul>
<p>
' . JText::_('MM_HELP_FINAL_NOTE') .'
</p>';
	}
}