<?php
/**
* @version		3.0
* @copyright	Copyright (C) 2007-2011 Stephen Brandon
* @license		GNU/GPL
*/

// no direct access
defined('_JEXEC') or die('Restricted access');
include_once 'changecache.php';

class modMetaModHelper
{
	function version() {
		return modMetaModHelper::versionMajor() . '.' . modMetaModHelper::versionMinor(); 
	}
	function versionMajor() { return 3; }
	function versionMinor() { return 9; }
	
	function locateGeoIPInclude($use_geoip) {

		// determine which file we are going to import, based on MetaMod parameter
		$geoIPDataFiles = array('','GeoIP.dat','GeoLiteCity.dat','GeoIPCity.dat');

		$geoip_folders = //MetaModParameters::geoIPFolders();
			array(
				"administrator".DS."components".DS."com_chameleon".DS."geoip",
				"geoip",
				"GeoIP",
				"geoIP",
				"GEOIP",
				"GEO IP",
				'',
				"geo_ip",
				"geo_IP",
				"Geo_IP"
				);
		$file = '';
		// find the file in any of the standard locations
		foreach ($geoip_folders as $folder) {
			$target = $geoIPDataFiles[$use_geoip];
			$proposed_file = JPATH_SITE.DS.$folder.DS.$target;
			if (is_file($proposed_file) && is_readable($proposed_file)) {
				$file = $proposed_file;
				break;
			}
		}
		return $file;		
	}
	
	function languages(&$params)
	{
		$debug 			= trim( $params->get( 'debug', '0' ) );
		$opt_option		= trim( $params->get( 'language_option', '0' ) );
		$opt_compare_strict	= trim( $params->get( 'language_compare_strict', '1' ) );
		$opt_preferred		= trim( $params->get( 'language_preferred', '' ) );
		// for $language_preferred, order doesn't matter. MetaMod will just
		// look through the browser's list of languages one at a time, and
		// use the first one that also appears in $language_preferred.

		$language = '';
		$language_code = '';
		$language_region = '';
		$languages = explode(",",array_key_exists('HTTP_ACCEPT_LANGUAGE',$_SERVER) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : '');


		if ($opt_option == 0) {
			// main browser lang
			if (count($languages)) {
				$language = strtolower($languages[0]);
			}
		}

		if ($opt_option == 1) { // preferred language from list
			// want to go through each of the languages in the browser string, from the 1st one,
			// and see if any of these are in the list that the admin supplied.
			$preferred_languages = explode(",",$opt_preferred);
			for ($i = 0 ; $i < count($preferred_languages); $i++) {
				$preferred_languages[$i] = trim(strtolower( $preferred_languages[$i] ));
			}

			for ($i = 0 ; $i < count($languages); $i++) {
				// first get rid of ";q=XX" suffixes 
				$l = explode( ";", trim(strtolower( $languages[$i] )));
				$try_this = $l[0];
				preg_match("/([a-z]+)-/",$try_this,$try_this_matches);
				if (
					// strict or not strict, both need this
					in_array($try_this, $preferred_languages)
					||
					// if not strict, try the browser "major" type against "preferred" ones
					( !$opt_compare_strict && count($try_this_matches) > 0 && in_array($try_this_matches[1], $preferred_languages) ) 

				) {
					$language = $try_this;
					break;
				}
			}
		}

		if ($opt_option == 2) {
			// The language of the Joomla front-end
			$langObj =& JFactory::getLanguage();
			$language = strtolower($langObj->getTag());			
		}

		if ($language != '') {
			preg_match("/([a-z]+)(-([a-z]+))?/",$language,$language_matches);
			$language_code = $language_matches[1];
			if (count($language_matches) > 3) $language_region = $language_matches[3];
		}
		
		if ($debug) {
			echo '<i>$language:</i> '.htmlentities($language).'<br>';
			echo '<i>$language_code:</i> '.htmlentities($language_code).'<br>';
			echo '<i>$language_region:</i> '.htmlentities($language_region).'<br>';
		}
		
		return array($language,$language_code,$language_region);
	}
	
	function move_to_end_of_array( $key, &$array ) {
		if (array_key_exists( $key, $array ) ) {
			$item = $array[$key];
			unset( $array[$key] );
			$array[$key] = $item; /* it will get reversed */
		}
	}
	
	function advanced_debug() {
		$gets = $_GET;  
		$posts = $_POST; 
		//print_r($gets);
		//print_r($posts);
		//print_r($_SERVER);
		$operand = '<span style="color:#0000FF">';
		$color_end = '</span>';
		$var = '<span style="color:rgb(49,132,149)">';
		$constant = '<span style="color:rgb(3,106,7)">';
		
		echo '<div style="padding-bottom: 10px;">';
		echo '<b>' . JText::_('PAGE_IDENTIFICATION') . "</b><br />\n";
				
		if ( $gets != null ) {

			echo '<small>';
			echo JText::_('MM_ADVANCED_DEBUG_HELP');
			echo '</small>';

			/* make an array of items that were present in _GET, but were *not* present
			 * in the original query string. These will be marked as "optional".
			 * $_GET often contains extra variables that were not actually passed
			 * into the application - they are added by various components etc.
			 * We may or may not want to include these extra ones in the rule.
			 * Therefore we get a list of the *actual* ones that the user passed
			 * in, via the apache server string $_SERVER['QUERY_STRING'].
			 * Any that are not on this list get tagged to tell the operator that
			 * they may not be required.
			 */
			
			$qs_orig = explode( '&', $_SERVER['QUERY_STRING'] );
			$qs = array();
			foreach ( $qs_orig as $q ) {
				$qs[] = ( ( $where = strpos($q, '=') ) === false ) ? $q : substr( $q, 0, $where ) ;
			}
			
			$optional = array(); /* this array will hold all the items that were not explicitly requested */
			foreach ( array_keys($gets) as $get) {
				if ( array_search( $get, $qs ) === false 
				 && $get != 'option'
				 && $get != 'Itemid'
				 && $get != 'view'
				 && $get != 'id'
				) $optional[] = $get;
			}

			/* add post data, if the page was a post */
			if ( $posts != null && count($posts) ) {
				$gets = $gets + $posts;
			}

			/* we always want 'option' to be first in the list, for usability reasons - primarily because
			 * we can almost guarantee that it's there every single time.
			 */
			
			foreach ( array( 'Itemid','category_id','catid','section_id','sectionid','id','page','view','option') as $key ) {
				modMetaModHelper::move_to_end_of_array ( $key, $gets );				
			}

			$gets = array_reverse( $gets );

			echo '<div style="background-color: white; color:black; margin:0; padding:2px; background-image:none; font-size:80%; ' .
				'font-family:\'Andale Mono\', \'Courier New\', courier-new, Courier, courier, monospace; border:1px solid #b0b0b0;">';
			echo "{$operand}if{$color_end} ( <br>\n";

			$first = true;
			$found_optional = false;
			
			foreach( array_keys($gets) as $get ) {
				// get rid of common problems with URLs, namely what happens with "%22" in URLs...
				if ( substr( $get, 0, 5 ) == 'quot;' ) continue;
				if ( substr( $get, 0, 3 ) == 'lt;' ) continue;
				if ( substr( $get, 0, 3 ) == 'gt;' ) continue;
				
				echo '<div style="padding-left:0.5em">';
				if ( $first ) {
					$first = false;
				} else {
					echo " {$operand}and{$color_end} ";
				}
				$val = JRequest::getVar($get); // put in a var_export here somewhere?
//				$val = str_replace("'", '\\\'', $val); // escape single quotes
				$get = str_replace("'", '\\\'', $get); // escape single quotes
				
				if ($get == 'Itemid' or $get == 'option' or $get == 'view' or $get == 'id') {
					echo $var . '$' . htmlentities( $get, ENT_QUOTES, 'UTF-8' ) . $color_end;
				} else {
					echo "JRequest::getVar({$constant}'" . htmlentities( $get, ENT_QUOTES, 'UTF-8' ) . "'{$color_end}) ";
				}
				echo " $operand==$color_end $constant" . htmlentities( var_export( $val, true ), ENT_QUOTES, 'UTF-8' ) . "$color_end ";
				if ( array_search( $get, $optional ) !== false ) {
					echo '/*!*/';
					$found_optional = true;
				}
				echo '</div>';
			}
			echo " ) {$operand}return{$color_end} XXX; <i>/* " . JText::_('MM_ADVANCED_DEBUG_REPLACE_XXX') . ' */</i>';
			echo '</div>';
			
			if ($found_optional) {
				echo '<small><i>' . JText::_( 'MM_ADVANCED_DEBUG_OPTIONAL_RULES') . '</i></small>';
			}
		}
		echo '</div>';
		
	}


	function timezone_offset( $tz ) {
		if ( function_exists( 'date_offset_get' ) and function_exists( 'date_create' ) and function_exists( 'timezone_open' ) ) {
			$offset = date_offset_get( date_create( "now", timezone_open( $tz ) ) );
		} else {
			$old_tz = getenv('TZ');
			putenv("TZ=$tz");
			$offset = date('Z',time());
			putenv("TZ=$old_tz");
		}
    	return $offset;
	}

	function strtotime_in_tz( $time, $tz ) {
		if ( function_exists( 'date_default_timezone_get' ) ) {
			$old_tz = date_default_timezone_get();
			date_default_timezone_set( $tz );
			$stamp = strtotime($time);
			date_default_timezone_set( $old_tz );
		} else {
			$old_tz = getenv('TZ');
			putenv("TZ=$tz");
			$stamp = strtotime($time);
			putenv("TZ=$old_tz");
		}
    	return $stamp;
	}
	
	function getUserIP( $params ) {
		$ip = $params->get("ip_override", '');
		if ($ip != '') {
			return $ip;
		}
		if ( isset( $_SERVER ) ) {
			if ( isset( $_SERVER["HTTP_X_FORWARDED_FOR"]) ) {
				$ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
				if ( $ip != '' && strtolower($ip) != 'unknown' ) {
					$addresses = explode( ',', $ip);
					return $addresses[ count($addresses) - 1 ];
				}
			}

			if (isset($_SERVER["HTTP_CLIENT_IP"]) && $_SERVER["HTTP_CLIENT_IP"] != '' )
				return $_SERVER["HTTP_CLIENT_IP"];

			return $_SERVER["REMOTE_ADDR"];
		}

		if ( $ip = getenv( 'HTTP_X_FORWARDED_FOR' )) {
			if ( strtolower($ip) != 'unknown' ) {
				$addresses = explode( ',', $ip);
				return $addresses[ count($addresses) - 1 ];
			}
		}

		if ($ip = getenv('HTTP_CLIENT_IP')) {
			return $ip;
		}

		return getenv('REMOTE_ADDR');
	}

	function convertToUtf8( &$geoip ) {
		if ($geoip == null) return;
		$attributes			= get_object_vars($geoip);
		foreach($attributes as $key => $value) $geoip->$key = utf8_encode($value);
	}
		
	function setDateConstants( $now, $timezone ) {
		$now_time = $now + modMetaModHelper::timezone_offset( $timezone );

		if (!defined("MM_DAY_OF_WEEK"))		define("MM_DAY_OF_WEEK", gmdate("w",$now_time)); // 0=Sunday, 6-Saturday
		if (!defined("MM_DAY_OF_MONTH"))	define("MM_DAY_OF_MONTH", gmdate("j",$now_time)); // 1-31
		if (!defined("MM_MONTH"))			define("MM_MONTH", gmdate("n",$now_time)); // 1-12
		if (!defined("MM_YEAR"))			define("MM_YEAR", gmdate("Y",$now_time));
		if (!defined("MM_HOUR"))			define("MM_HOUR", gmdate("G",$now_time)); // 0-23
		if (!defined("MM_MINUTE"))			define("MM_MINUTE", (int)gmdate("i",$now_time)); // 0-59
		if (!defined("MM_SECOND"))			define("MM_SECOND", (int)gmdate("s",$now_time)); // 0-59
		if (!defined("MM_TIME"))			define("MM_TIME", gmdate("Gis",$now_time)); // 164523 = 16:45:23 = 4:45:23 PM
		if (!defined("MM_DATE"))			define("MM_DATE", gmdate("Ymd",$now_time)); // 20081225 = 25th Dec 2008
	}
	
	// just give an IP address if you want to check the cache for that address.
	// Otherwise, pass it an IP address and any info you have to cache that info.
	function geoip_cache_ip_countryid_name_city( $ip, $country_id = null, $country_name = null, $city = null ) {
		global $geoip_cache;
		if ( $geoip_cache == null ) $geoip_cache = array();
		if ( $country_id == null and $country_name == null and $city == null ) {
			return array( @$geoip_cache[$ip]['id'], @$geoip_cache[$ip]['name'], @$geoip_cache[$ip]['city'] );
		}
		if (! array_key_exists( $ip,$geoip_cache )) $geoip_cache[$ip] = array();
		if ($country_id)	$geoip_cache[$ip]['id']		= $country_id;
		if ($country_name)	$geoip_cache[$ip]['name']	= $country_name;
		if ($city)			$geoip_cache[$ip]['city']	= $city;
	}
	
	// params:   the params of the MetaMod we are expanding
	// position: the module position that the MetaMod is in. We give it this so we can filter this out
	//           of the list of module positions returned (so we don't get an infinite loop of the MetaMod including
	//           its own position)
	function moduleIdsAndChanges( &$params, $my_module = null )
	{
		$Itemid				= JRequest::getVar('Itemid');
		$position			= $my_module->position;
		$my_id				= $my_module->id;
		$id					= JRequest::getVar('id');
		$view				= JRequest::getWord('view');
		$option				= JRequest::getVar('option');
		
		// Retrieve parameters
		$php 				= trim(   $params->get( 'php', '' ) );
		$logged_in_users	= intval( $params->get( 'logged_in_users', '0' ) );
		$start_datetime 	= trim(   $params->get( 'start_datetime', '' ) );
		$end_datetime 		= trim(   $params->get( 'end_datetime', '' ) );
		$timezone 			= trim(   $params->get( 'timezone', 'default' ) );
		$use_geoip 			= intval( $params->get( 'use_geoip', '0' ) );
		$module_ids 		= trim(   $params->get( 'module_ids', '0' ) );
		$debug 				= trim(   $params->get( 'debug', '0' ) );

		if ($debug == 2) { /* advanced debug */
			modMetaModHelper::advanced_debug();
		}
		
		if ($timezone == "default") {
			$config			=& JFactory::getConfig();
			$timezone		= $config->getValue('offset');
		}

		$now				= time();

		if ($debug) {
			echo '<b>' . JText::_('MM_DEBUG_INFO') .'</b><br />';
			if ( $my_id ) echo '<i>Module ID:</i> '.htmlentities($my_id).'<br />';
			echo '<i>$option:</i> '.htmlentities($option).'<br />';
			echo '<i>$view:</i> '.htmlentities($view).'<br />';
			echo '<i>$id:</i> '.htmlentities($id).'<br />';
			echo '<i>$Itemid:</i> '.htmlentities($Itemid).'<br />';
			echo '<i>$timezone:</i> '.htmlentities($timezone).'<br />';
		}
		
		// LANGUAGE HANDLING
		list($language,$language_code,$language_region) = modMetaModHelper::languages($params);

		// START AND END TIMES
		// quit if we're not to start yet

		if ($start_datetime != '') {
		 	if ( modMetaModHelper::strtotime_in_tz( $start_datetime, $timezone ) > $now) {
				if ($debug) {
					echo JText::_('MM_DEBUG_START_NOT_REACHED') . '<br />';
				}
				return;
			}
			if ($debug) {
				echo JText::_('MM_DEBUG_START_REACHED') . '<br />';
			}
		}

		// quit if we are too late to display this item
		if ($end_datetime != '') {
		 	if ( modMetaModHelper::strtotime_in_tz( $end_datetime, $timezone ) <= $now) {
				if ($debug) {
					echo JText::_('MM_DEBUG_END_REACHED') . '<br />';
				}
				return;
			}
			if ($debug) {
				echo JText::_('MM_DEBUG_END_NOT_REACHED') . '<br />';
			}
		}
		
		// INCLUDE AND EXCLUDE COUNTRIES, & GEOIP FUNCTIONALITY
		$include_countries	= strtoupper(trim( $params->get( 'include_countries', '' ) ));
		$exclude_countries	= strtoupper(trim( $params->get( 'exclude_countries', '' ) ));

		$ip = modMetaModHelper::getUserIP( $params );

		if ($use_geoip) {
			
			// if we have already calculated any info for the current IP address, retrieve it.
			list( $fromCountryId, $fromCountryName, $geoip) = modMetaModHelper::geoip_cache_ip_countryid_name_city( $ip );		

			$geoIPFiles = array('','geoip.inc.php','geoipcity.inc.php','geoipcity.inc.php');
			
			// case 1: looking for a country, and we don't already have one
			if ($fromCountryId == '' and $fromCountryName == '' and $use_geoip == 1 ) {

				// find the location of the geoip data file, depending on which we are to use
				$include_file = modMetaModHelper::locateGeoIPInclude($use_geoip);
				if ($include_file == null) {
					$use_geoip = 0;
					if ($debug) {
						echo '<b>' . JText::_('MM_DEBUG_GEOIP_CANNOT_LOCATE') . '</b><br />';
					}
				} else {
				
					// grab the appropriate code for whichever geoip stuff we have 
					if (!function_exists("geoip_open")) {
						include_once(JPATH_SITE . DS . "modules" . DS . "mod_metamod" . DS . "geoip" . DS . $geoIPFiles[$use_geoip]);
					}
				
					// open the data file
					$gi = geoip_open( $include_file, GEOIP_STANDARD );

					$fromCountryId		= utf8_encode(geoip_country_code_by_addr($gi, $ip));
					$fromCountryName	= utf8_encode(geoip_country_name_by_addr($gi, $ip));
					
					modMetaModHelper::geoip_cache_ip_countryid_name_city( $ip, $fromCountryId, $fromCountryName );		
					
					geoip_close( $gi );

				} // we found the geoip data file
			} // we needed to cache the geoip for the current request
			

			// case 2: looking for a city record, and we don't already have one
			if ($geoip == null and $use_geoip > 1 ) {

				// find the location of the geoip data file, depending on which we are to use
				$include_file = modMetaModHelper::locateGeoIPInclude($use_geoip);
				if ($include_file == null) {
					$use_geoip = 0;
					if ($debug) {
						echo '<b>' . JText::_('MM_DEBUG_GEOIP_CANNOT_LOCATE') . '</b><br />';
					}
				} else {
				
					// grab the appropriate code for whichever geoip stuff we have 
					if (!function_exists("geoiprecord")) {
						include_once(JPATH_SITE . DS . "modules" . DS . "mod_metamod" . DS . "geoip" . DS . $geoIPFiles[$use_geoip]);
					}
				
					// open the data file
					$gi = geoip_open( $include_file, GEOIP_STANDARD );

					$geoip				= geoip_record_by_addr($gi, $ip);
					if ( $geoip != null ) {
						modMetaModHelper::convertToUtf8($geoip);
						$fromCountryId		= $geoip->country_code;
						$fromCountryName	= $geoip->country_name;
						modMetaModHelper::geoip_cache_ip_countryid_name_city( $ip, $fromCountryId, $fromCountryName, $geoip );
					}
					if ($geoip == null && $debug) {
						echo JText::sprintf('MM_DEBUG_GEOIP_NO_CITY_FOUND', htmlentities($ip) ) . '<br />';
					}
					geoip_close( $gi );

				} // we found the geoip data file
			} // we needed to cache the geoip for the current request
			
			// set some defaults if necessary (probably can't happen)
			if ($fromCountryId == '')	$fromCountryId = "GB";
			if ($fromCountryName == '')	$fromCountryName = "United Kingdom";
			
			if (($fromCountryId || $fromCountryName) && $debug) {
				echo "<i>" . JText::_("MM_DEBUG_GEOIP_COUNTRY") ."</i> " .$fromCountryId . "<br />";
				echo "<i>" . JText::_("MM_DEBUG_GEOIP_COUNTRY_NAME") ."</i> " . $fromCountryName . "<br />";
			}

			if($geoip && $debug)
			{
				echo "<i>" . JText::_("Country Code 3:") ."</i> " . $geoip->country_code3 . "<br />";
				echo "<i>" . JText::_("Region:") ."</i> " .$geoip->region . "<br />";
				echo "<i>" . JText::_("City:") ."</i> " .$geoip->city . "<br />";
				echo "<i>" . JText::_("Postal Code:") ."</i> " .$geoip->postal_code . "<br />";
				echo "<i>" . JText::_("Latitude:") ."</i> " .$geoip->latitude . "<br />";
				echo "<i>" . JText::_("Longitude:") ."</i> " .$geoip->longitude . "<br />";
				echo "<i>" . JText::_("Area Code:") ."</i> " .$geoip->area_code . "<br />";
				echo "<i>" . JText::_("Metro Code:") ."</i> " .$geoip->metro_code . "<br />";
				echo "<i>" . JText::_("Continent Code:") ."</i> " .$geoip->continent_code . "<br />";
			}						
			
			if ( strlen( $include_countries ) ) {
				// reject if fromCountryId is not in the list
				if ( strpos( $include_countries, $fromCountryId ) === false) {
					if ( $debug ) {
						echo JText::sprintf('MM_DEBUG_GEOIP_REJECTING_INCLUDE', $fromCountryId ) . '<br />';
					}
					return;
				}
				if ( $debug ) {
					echo JText::sprintf('MM_DEBUG_GEOIP_ACCEPTING_INCLUDE', $fromCountryId ) . '<br />';
				}
			}
			if ( strlen( $exclude_countries ) ) {
				// reject if fromCountryId is in the list
				if (! ( strpos( $exclude_countries, $fromCountryId ) === false ) ) {
					if ( $debug ) {
						echo JText::sprintf('MM_DEBUG_GEOIP_REJECTING_EXCLUDE', $fromCountryId ) . '<br />';
					}
					return;
				}
				if ( $debug ) {
					echo JText::sprintf('MM_DEBUG_GEOIP_ACCEPTING_EXCLUDE', $fromCountryId ) . '<br />';
				}
			}
		}

		// for access by eval'ed script
		$db 	=& JFactory::getDBO();
		$user 	=& JFactory::getUser();

		// set some useful for access by eval'ed script
		$user_types = array("Public", "Registered", "Author",
				"Editor", "Publisher", "Manager", "Administrator", "Super Users", "Customer Group", "Shop Suppliers");
		foreach ($user_types as $u) {
			$constant = "MM_USER_" . strtoupper(str_replace(" ","_",$u));
			$constant_not = "MM_USER_NOT_" . strtoupper(str_replace(" ","_",$u));
			
			if (!defined($constant))		define($constant, array_key_exists( $u, $user->groups));
			if (!defined($constant_not))	define($constant_not, !array_key_exists( $u, $user->groups));;
		}
		
		if (!defined("MM_NOT_LOGGED_IN"))	define("MM_NOT_LOGGED_IN", ($user->guest == 1));
		if (!defined("MM_LOGGED_IN"))		define("MM_LOGGED_IN", ! MM_NOT_LOGGED_IN);
		
		
		modMetaModHelper::setDateConstants( $now, $timezone );
		
		// now check the logged-in-user status.
		// If MM is set up for "only logged in users", and the user is not logged in, quit immediately.
		if ($logged_in_users == 1 ) {
			if ( MM_NOT_LOGGED_IN ) {
				if ($debug) {
					echo JText::_('MM_LOGGED_IN_ONLY_FAIL') . '<br />';
				}
				return;
			}
			if ($debug) {
				echo JText::_('MM_LOGGED_IN_ONLY_SUCCEED') . '<br />';
			}
		}
		// If MM is set up for "only non-logged-in users", and the user is logged in, quit immediately.
		if ($logged_in_users == 2 ) {
			if ( MM_LOGGED_IN ) {
				if ($debug) {
					echo JText::_('MM_NON_LOGGED_IN_ONLY_FAIL') . '<br />';
				}
				return;
			}
			if ($debug) {
				echo JText::_('MM_NON_LOGGED_IN_ONLY_SUCCEED') . '<br />';
			}
		}
		
		// set up the changecache
		// this can now be used inside the rules like this:
		// $changes->mod(N)->accessLevel("1")->showTitle()->title("New Title")
		$changes = new MMChangeCache;
		
		// finally, set up some JomGeniuses for use in the script.
		include_once dirname(__FILE__) . DS . 'JomGenius.class.php';
		$content_genius = JomGenius( 'content' );
		$menu_genius = JomGenius( 'menu' );
		$core_genius = JomGenius( 'core' );
		$core_genius->setTimezone( $timezone );
		

		// get results from php script. Add leading and trailing spaces so it's possible to end with < ? p h p
		$mods = strlen($php) ? eval( " " . $php . " " ) : '';

		
		// convert comma-separated list of module identifiers (specified statically) to an array of identifiers.
		// identifiers can be module ids, or module positions.
		$static_ids = strlen( $module_ids ) ? array_map( "trim", explode( ",", $module_ids ) ) : array();

		// convert comma-separated list of module ids (returned from PHP code) to an array of identifiers
		if ( !is_array( $mods ) ) {
			if ( is_numeric( $mods ) ) $mods = array( $mods );
			if ( is_string( $mods) )  $mods = array_map( "trim", explode( ",", $mods ) );
			if ( !is_array( $mods ) )  $mods = array();
		}

		// combine the 2 sets of identifiers
		$mods = array_merge( $static_ids, $mods );

		// finally, ensure that we are not including the module position that this MetaMod is in.
		if ( strlen( $position ) ) {
			for ( $i = 0; $i < count($mods); $i++ ) {
				if ( (string)$mods[$i] == (string)$position ) {
					unset( $mods[$i] );
					if ( $debug ) {
						echo JText::sprintf('MM_DEBUG_IGNORING_MODULE_POSITION', $position) . '<br>';
					}
				}
			}
		}

		 // remove everything equating to "0" / false / ""
		$mods = array_filter( $mods );
		
		if ($debug) {
			if ( is_array($mods) and count($mods) ) {
				$mod_report = implode( ', ', $mods );
			} else {
				$mod_report = "<i>" . JText::_("JNONE") . "</i>";
			}
			echo JText::_('MM_DEBUG_INCLUDING_MODULES') . ' ' . $mod_report .'<br />';
		}
		
		return array( $mods, $changes ); 
	}
	
	function quotedEscapedListFromModuleArray( &$db, $all_mod_array ) {
		return "'". implode( "','", array_map( array( $db, 'getEscaped'), $all_mod_array ) ) . "'";
	}
		
	function displayModules( &$all_mod_array, &$params, $metamod_module, $attribs = array(), $changeObject = null) {

		if (is_array($all_mod_array) && count($all_mod_array)) {

			$app	=& JFactory::getApplication();
			$db		= JFactory::getDbo();
			$user 	=& JFactory::getUser();
			$aid	=  $user->get('aid', 0);

			$quoted_mod_identifiers = modMetaModHelper::quotedEscapedListFromModuleArray( $db, $all_mod_array );
			
			// If we are using the changecache system, then we
			// select all available modules EVEN IF THEY ARE DISABLED. Then we
			// trim the list again at the end, removing any that are (still)
			// disabled.
			// This allows the MetaMod rules to include modules that are known
			// to be disabled, and it enables them automatically.

			$autoenable = $params->get( 'autoenable', 'all' );
			$groups		= implode(',', $user->getAuthorisedViewLevels());

			$query = $db->getQuery(true); 

			$query->select('id, title, module, position, content, showtitle, params, published, ordering'
				. ", m.id in ( $quoted_mod_identifiers ) as in_id"
				. ", m.position in ( $quoted_mod_identifiers ) as in_position"
				);
				
			$query->from('#__modules AS m');
			if ( !$changeObject && $autoenable == 'none') {
				$query->where('m.published = 1');
			}
			$query->where('m.access IN ('.$groups.')');
			$query->where( "( m.id in ( $quoted_mod_identifiers ) OR m.position in ( $quoted_mod_identifiers ) )" );
			$query->order( "( field( "
				. "\n  case when m.position in ( $quoted_mod_identifiers ) then m.position else m.id end,"
				. "\n  $quoted_mod_identifiers )), ordering"
				);
			$db->setQuery($query);
			$modules = array();

			if ( !( $modules = $db->loadObjectList() ) ) {
				JError::raiseWarning( 'SOME_ERROR_CODE', "Error loading Modules: " . $db->getErrorMsg());
				return false; // FIXME - what to do here? Ignore?
			}

			// do some stuff that is found in libraries/joomla/application/module/helper.php
			$total = count( $modules );
			
			for( $i = 0; $i < $total; $i++ ) {
				// autoenabling, according to our parameters
				if ( $autoenable == 'all'
					|| ( $autoenable == 'id' && $modules[$i]->in_id == 1)
					|| ( $autoenable == 'position' && $modules[$i]->in_position == 1 ) )  {
					$modules[$i]->published = 1;
				}
				// determine if this is a custom module
				$file					= $modules[$i]->module;
				$custom 				= substr( $file, 0, 4 ) == 'mod_' ?  0 : 1;
				$modules[$i]->user  	= $custom;
				// CHECK: custom module name is given by the title field, otherwise it's just 'om' ??
				$modules[$i]->name		= $custom ? $modules[$i]->title : substr( $file, 4 );
				$modules[$i]->style		= null;
				$modules[$i]->position	= strtolower( $metamod_module->position );
			}

			modMetaModHelper::applyParamChanges( $modules, $changeObject );

			$document			= &JFactory::getDocument();
			$renderer			= $document->loadRenderer( 'module' );
			$style 				= trim( $params->get( 'force_style', trim( $params->get( 'style', 'table' ) ) ) );

			$contents = '';
			if ( !is_array( $attribs ) ) {
				$attribs		= array();
			}
			$attribs['style']	= $style;

			foreach ( $modules as $mod )  {
				if ( $mod->published ) {
					$contents		.= $renderer->render($mod, $attribs);
				}
			}
			return $contents;

		}
	}
	
	/**
	 * $modules		The list of all modules on the page (after processing by the MetaMod Pro plugin, where appropriate),
	 *				or enclosed in this MetaMod (when not using MetaMod Pro). These are OBJECTS as per the db table.
	 *				No more modules should be added to this list after processing here.
	 * $changeObject	An object with 2 arrays:
	 *				->disabled_positions						// an array of position names that are to be disabled
	 *				->modules									// the array of new parameters that are to be substituted
	 *															// into the list of modules.
	 *				It's an array keyed on "modX" where X is the module number to be modified.
	 *				$changes->modules['modX']['title']		// string
	 *				$changes->modules['modX']['showtitle']	// 0 = don't show, 1 = show
	 *				$changes->modules['modX']['published']	// 0 = not published, 1 = published
	 *				$changes->modules['modX']['access']		// 0 = public, 1 = registered, 2 = special
	 *				$changes->modules['modX']['position']	// e.g. right
	 *				$changes->modules['modX']['module']		// e.g. mod_mainmenu or mod_custom
	 *				$changes->modules['modX']['content']	// The HTML content of mod_custom
	 *				$changes->modules['modX']['ordering']	// Numerical ordering of the modules in the position.
	 *														// You probably don't want to change this since it
	 *														// only affects the modules inside this particular
	 *														// MetaMod (though with MetaMod Pro may be more useful).
	 *				$changes->modules['modX']['params']	// Another array where the key is the name of the
	 *														// parameter, and value is the new value.
	 */
	function applyParamChanges( &$modules, &$changeObject ) {
		if ( $changeObject == null || $modules == null ) return;
		
		$c = count($modules);
		for ( $i = 0; $i < $c; $i++ ) {
			$module = &$modules[$i];
			$modX = 'mod' . $module->id;
			if ( array_key_exists( $modX, $changeObject->modules )) {
				/* the admin wants to apply changes to this module id.
				 * Now we need to make those changes.
				 */
				$changes = $changeObject->modules[ $modX ]->cache;
				// apply basic changes
				foreach ( array(
					'title', 'showtitle', 'published',
					'access', 'position', 'module',
					'content', 'ordering') as $type ) {
						if ( array_key_exists( $type, $changes ) ) {
							$module->$type = $changes[ $type ];
						}
				}
				
				// now apply changes to the parameter string.
				// do we even have a $changes['params] for this modX?
				if ( array_key_exists( 'params', $changes ) ) {
					$paramchanges = $changes[ 'params' ];

					if ( is_array( $paramchanges ) && count( $paramchanges ) ) { // no point in messing with it if no changes were requested
						$registry = new JRegistry( $module->params );
						$registry->merge( new JRegistry( $paramchanges ) );
						$module->params = $registry->toString();
					}

				}
			}
		}
		// now process any deletions from the list of modules.
		// 
		for ( $i = 0; $i < count($modules); $i++ ) {
			if (
					isset( $modules[$i]->published ) and $modules[$i]->published == 0
				or
					in_array( $modules[$i]->position, $changeObject->disabled_positions)
				) {
				array_splice( $modules, $i, 1 );
				$i--;
			}
		}
	}
	
}