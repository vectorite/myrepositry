<?php
/**
 * Shlib - Db query cache and programming library
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier 2012
 * @package     shlib
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     0.2.1.306
 * @date				2012-10-12
 */

// no direct access
defined('_JEXEC') or die;

/**
 * Provide a few string manipulation methods
 *
 * @since	0.2.1
 *
 */
class ShlSystem_Strings
{

	/**
	 * Performs a preg_replace, wrapping it to catch errors
	 * caused by bad characters or otherwise
	 *
	 * @param string $pattern	RegExp pattern
	 * @param string $replace	RegExp replacement
	 * @param string $subject	RegExp subject
	 * @param string $ref	Optional reference, to be logged in case of error
	 *
	 * @return	string	the result of preg_replace operation
	 */
	public static function pr($pattern, $replace, $subject, $ref = '')
	{
		static $pageUrl = null;

		$tmp = preg_replace($pattern, $replace, $subject);
		if (empty($tmp))
		{
			$pageUrl = is_null($pageUrl) ? (empty($_SERVER['REQUEST_URI']) ? '' : ' on page ' . $_SERVER['REQUEST_URI']) : $pageUrl;
			ShlSystem_Log::error('shlib', '%s::%s::%d: %s', __CLASS__, __METHOD__, __LINE__,
				'RegExp failed: invalid character' . $pageUrl . (empty($ref) ? '' : ' (' . $ref . ')'));
			return $subject;
		}
		else
		{
			return $tmp;
		}
	}

}
