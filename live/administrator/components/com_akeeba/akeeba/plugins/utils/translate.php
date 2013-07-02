<?php
/**
 * Akeeba Engine
 * The modular PHP5 site backup engine
 * @copyright Copyright (c)2009-2013 Nicholas K. Dionysopoulos
 * @license GNU GPL version 3 or, at your option, any later version
 * @package akeebaengine
 *
 */

/**
 * Custom translation class, simulating a subset of Joomla!'s JText functionality
 * @author Nicholas
 */
class AEUtilTranslate
{
	static $language = null;
	
	private static function getLanguageFilename( $default = false, $frontend = true )
	{
		// We'll try to fetch the active front-end language from the database
		if($default)
		{
			$lang = 'en-GB';
		}
		else
		{
			$lang = AEUtilJconfig::getValue('language', 'en-GB');
		}
		
		if($frontend) {
			$lang_base = JPATH_SITE.'/language';
		} else {
			$lang_base = JPATH_ADMINISTRATOR.'/language';
		}
		
		$lang_file = $lang_base.'/'.$lang.'/'.$lang.'.com_akeeba.ini';
		if( file_exists( $lang_file ) )
		{
			return $lang_file;
		}
		else
		{
			return $lang_base.'/en-GB/en-GB.com_akeeba.ini';
		}
	}

	private static function loadLanguage( $default = false )
	{
		$filename = self::getLanguageFilename( $default );
		if( !file_exists($filename) )
		{
			return array();
		}
		else
		{
			return AEUtilINI::parse_ini_file( $filename, false );
		}
	}

	/**
	 * Translates a string into the current language
	 * @param	string $string The string to translate
	 * @return	string
	 */
	public static function _( $string )
	{
		if(is_null(self::$language))
		{
			$lang_default = self::loadLanguage(true, true);
			$lang_local = self::loadLanguage(false, true);
			$lang_default_admin = self::loadLanguage(true, false);
			$lang_local_admin = self::loadLanguage(false, false);
			self::$language = array_merge($lang_default_admin, $lang_default, $lang_local_admin, $lang_local );
			unset($lang_default);
			unset($lang_local);
			unset($lang_default_admin);
			unset($lang_local_admin);
		}

		if(array_key_exists($string, self::$language))
		{
			return self::$language[$string];
		}
		else
		{
			return $string;
		}

	}

	/**
	 * Passes a string through a printf
	 * @param	mixed Mixed number of arguments for the sprintf function
	 */
	public static function sprintf( $string )
	{
		$args = func_get_args();
		if (count($args) > 0) {
			$args[0] = self::_($args[0]);
			return call_user_func_array('sprintf', $args);
		}
		return '';
	}
}

if(!class_exists('JText'))
{
	class JText
	{
		public static function _( $string )
		{
			return AEUtilTranslate::_( $string );
		}

		public static function sprintf( $string )
		{
			return AEUtilTranslate::sprintf( $string );
		}
	}
}