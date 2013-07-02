<?php
/**
 * sh404SEF - SEO extension for Joomla!
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier 2012
 * @package     sh404sef
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     3.6.4.1481
 * @date		2012-11-01
 */

// Security check to ensure this file is being included by a parent file.
if (!defined('_JEXEC'))
	die('Direct Access to this location is not allowed.');

class Sh404sefHelperGeneral
{

	const COM_SH404SEF_ALL_DUPLICATES = 0;
	const COM_SH404SEF_ONLY_DUPLICATES = 1;
	const COM_SH404SEF_NO_DUPLICATES = 2;

	const COM_SH404SEF_ALL_ALIASES = 0;
	const COM_SH404SEF_ONLY_ALIASES = 1;
	const COM_SH404SEF_NO_ALIASES = 2;

	const COM_SH404SEF_ALL_URL_TYPES = 0;
	const COM_SH404SEF_ONLY_CUSTOM = 1;
	const COM_SH404SEF_ONLY_AUTO = 2;

	const COM_sh404SEF_URLTYPE_404 = -2;
	const COM_sh404SEF_URLTYPE_NONE = -1;
	const COM_sh404SEF_URLTYPE_AUTO = 0;
	const COM_sh404SEF_URLTYPE_CUSTOM = 1;

	const COM_SH404SEF_URLTYPE_ALIAS = 0;
	const COM_SH404SEF_URLTYPE_PAGEID = 1;

	const COM_SH404SEF_ALL_TITLE = 0;
	const COM_SH404SEF_ONLY_TITLE = 1;
	const COM_SH404SEF_NO_TITLE = 2;

	const COM_SH404SEF_ALL_DESC = 0;
	const COM_SH404SEF_ONLY_DESC = 1;
	const COM_SH404SEF_NO_DESC = 2;

	/**
	 * Load components
	 *
	 * @access  public
	 * @param array exclude an array of component to exclude from result
	 * @return  array
	 */
	public static function getComponentsList($exclude = array())
	{

		static $components = null;

		if (is_null($components))
		{

			$db = ShlDbHelper::getDb();

			// exclude some and ourselves
			$exclude = array_merge(
				array('com_sh404sef', 'com_joomfish', 'com_falang', 'com_joomsef', 'com_acesef', 'com_admin', 'com_cache', 'com_categories',
					'com_checkin', 'com_cpanel', 'com_installer', 'com_languages', 'com_media', 'com_menus', 'com_messages', 'com_modules',
					'com_plugins', 'com_templates', 'com_config', 'com_redirect'), $exclude);

			$where = $db->quoteName('type') . ' = ? and ' . $db->quoteName('enabled') . ' = ? and ' . $db->quoteName('element') . ' <> ? ' . ' and '
				. $db->quoteName('element') . ' not in (' . ShlDbHelper::arrayToQuotedList($exclude) . ')';
			$whereData = array('component', 1, '');
			try
			{
				$components = ShlDbHelper::selectObjectList('#__extensions', array('*'), $where, $whereData, $orderBy = array('name'), $offset = 0,
					$lines = 0, $key = 'element');
			}
			catch (Exception $e)
			{
				JError::raiseWarning('SOME_ERROR_CODE', "Error loading Components: " . $e->getMessage());
				return false;
			}

		}

		return $components;

	}

	/**
	 * Get installed front end language list
	 *
	 * @access  private
	 * @return  array
	 */
	public static function getInstalledLanguagesList($site = true)
	{

		static $languages = null;

		if (is_null($languages))
		{

			$db = ShlDbHelper::getDb();

			// is there a languages table ?
			$languages = array();
			$languagesTableName = $db->getPrefix() . 'languages';
			$tablesList = $db->getTableList();
			if (is_array($tablesList) && in_array($languagesTableName, $tablesList))
			{

				try
				{
					$query = 'SELECT * FROM #__languages';
					$db->setQuery($query);
					$languages = $db->shlLoadObjectList();
				}
				catch (Exception $e)
				{
					JError::raiseWarning('SOME_ERROR_CODE', "Error loading languages lists: " . $e->getMessage());
					ShlSystem_Log::error('sh404sef', '%s::%s::%d: %s', __CLASS__, __METHOD__, __LINE__, $e->getMessage());
					return false;
				}
				// match fields name to what we need, those were changed in version 2.2 of JF
				foreach ($languages as $key => $language)
				{
					if (empty($language->id))
					{
						$languages[$key]->id = $language->lang_id;
					}
					if (empty($language->name))
					{
						$languages[$key]->name = $language->title;
					}
					if (empty($language->code))
					{
						$languages[$key]->code = $language->lang_code;
					}
					if (empty($language->shortcode))
					{
						$languages[$key]->shortcode = $language->sef;
					}
					if (empty($language->active) && empty($language->published))
					{
						// drop this language, it is not published
						unset($languages[$key]);
					}
				}
			}
		}

		return $languages;

	}

	/**
	 * Builds an internal urls
	 *
	 * @param <array> $elements an array of key,value pairs, should not have option=
	 * @param string value of component, if not default
	 * @return <string> the urls
	 */
	public static function buildUrl($elements, $option = 'com_sh404sef')
	{

		$url = 'index.php?option=' . $option;

		if (is_array($elements) && !empty($elements))
		{
			foreach ($elements as $key => $value)
			{
				$url .= '&' . $key . '=' . $value;
			}
		}

		return $url;
	}

	public static function getComponentUrl()
	{

		return 'administrator/components/com_sh404sef';
	}

	/**
	 * Create toolbar title for current view
	 *
	 * This one can ucstomize the class for styling
	 * plus the output can be used to
	 * simply display the title as opposed to
	 * using $mainframe to set the component
	 * title, which is not OK when used inside a modal box
	 *
	 * @param string $title text title
	 * @param string $icon the name of an image, which is used to calculate aclass name
	 * @param string $class the name of a wrapping class
	 */
	public static function makeToolbarTitle($title, $icon = 'generic.png', $class = 'header')
	{

		//strip the extension
		$icon = preg_replace('#\.[^.]*$#u', '', $icon);

		$html = "<div class=\"$class icon-48-$icon\">\n";
		$html .= "$title\n";
		$html .= "</div>\n";

		return $html;

	}

	/**
	 * Prepare an xml file content holding
	 * a standard record for returning result
	 * of an ajax request
	 *
	 * @param JView $view the view handling the request
	 */
	public static function prepareAjaxResponse($view)
	{

		// create a root node
		$base = '<?xml version="1.0" encoding="UTF-8" ?><item id="shajax-response"></item>';
		$xml = new SimpleXMLElement($base);

		$status = '_';
		$message = '_';
		$messagecode = '_';
		$taskexecuted = '_';

		// set their respective values
		$vErrors = array();
		$view = new stdClass();

		if (empty($vErrors))
		{
			// retrieve messagecode and task
			if (empty($view->messagecode))
			{
				$view->messagecode = 'COM_SH404SEF_OPERATION_COMPLETED';
			}
			if (empty($view->taskexecuted))
			{
				$view->taskexecuted = $taskexecuted;
			}

			// either a success or a redirect
			if (empty($view->redirectTo))
			{
				// no error
				$status = 'success';
				$msg = empty($view->message) ? JText::_('COM_SH404SEF_OPERATION_COMPLETED') : $view->message;
				$message = '<ul>' . $msg . '</ul>';
				$messagecode = 200;
			}
			else
			{
				$status = 'redirect';
				$glue = strpos($view->redirectTo, '?') === false ? '?' : '&';
				$message = $view->redirectTo . $glue . 'sh404sefMsg=' . $view->messagecode;
			}
			$taskexecuted = $view->taskexecuted;
		}
		else
		{
			$status = 'failure';
			$messageTxt = '';
			foreach ($vErrors as $error)
			{
				$messageTxt .= '<li>' . $error . '</li>';
			}
			$message = '<ul>' . $messageTxt . '</ul>';
		}

		// add children : status, message, message code, task
		$xml->addChild('status', $status);
		$xml->addChild('message', $message);
		$xml->addChild('messagecode', $messagecode);
		$xml->addChild('taskexecuted', $taskexecuted);

		// output resulting text, no need for a layout file I think
		$output = $xml->asXml();

		return $output;
	}

	/**
	 * Calculate MD5 of a set of data
	 *
	 * @param array $dataSet the data, as an array of objects or arrays
	 * @param array $columns, hold the names of the object properties to be used in calculation
	 * @param boolean $asObject if true, dataSet is an array of objects, else an array of array
	 */
	public static function getDataMD5($dataSet, $columns, $asObject = true)
	{

		$md5 = null;
		$sum = '';

		if (!empty($dataSet) && !empty($columns))
		{
			foreach ($dataSet as $record)
			{
				foreach ($columns as $column)
				{
					$sum .= $asObject ? $record->$column : $record[$column];
				}
			}
			$md5 = md5($sum);
		}

		return $md5;
	}

	/**
	 * Returns either the full set or just one
	 * header line to be used in an export file
	 * Also needed when importing, to recognize
	 * import type
	 *
	 * @param string $type the data type being imported
	 */
	public static function getExportHeaders($type = null)
	{

		static $_headers = array('aliases' => '"Nbr","Alias","Sef url","Non sef url","Type","Hits"',
			'urls' => '"Nbr","Sef url","Non sef url","Hits","Rank","Date added","Page title","Page description","Page keywords","Page language","Robots tag"',
			'metas' => '"Nbr","Sef url","Non sef url","Hits","Rank","Date added","Page title","Page description","Page keywords","Page language","Robots tag"',
			'pageids' => '"Nbr","pageId","Sef url","Non sef url","Type","Hits"',
			'view404' => '"Nbr","Sef url","Non sef url","Hits","Rank","Date added","Page title","Page description","Page keywords","Page language","Robots tag"'
			// legacy files
			, 'sh404sefurls' => '"id","Count","Rank","SEF URL","non-SEF URL","Date added"',
			'sh404sefmetas' => '"id","newurl","metadesc","metakey","metatitle","metalang","metarobots"');

		if (is_null($type))
		{
			return $_headers;
		}

		if (isset($_headers[$type]))
		{
			return $_headers[$type];
		}

		return false;
	}

	public static function checkIpRange($ip, $ipExp)
	{
		if (empty($ip) || empty($ipExp))
			return false;
		$exp = '/' . str_replace('\*', '[0-9]{1,3}', preg_quote($ipExp)) . '/'; // allow * wild card
		return preg_match($exp, $ip);
	}

	public static function checkIPList($ip, $ipList)
	{
		if (empty($ip) || empty($ipList))
			return false;
		foreach ($ipList as $ipInList)
		{
			if (self::checkIpRange($ip, $ipInList))
			{
				return true;
			}
		}
		return false;
	}

	public static function getUserGroups($format = 'all')
	{

		static $_groups = null;

		if (is_null($_groups))
		{
			$groups_['all'] = array();
			$groups_['id'] = array();
			$groups_['title'] = array();

			// read groups from DB
			$rawGroups = array();
			try
			{
				$rawGroups = ShlDbHelper::selectObjectList('#__usergroups', array('id', 'title'));
			}
			catch (Exception $e)
			{
				ShlSystem_Log::error('sh404sef', '%s::%s::%d: %s', __CLASS__, __METHOD__, __LINE__, $e->getMessage());
			}

			// store groups by format: id&name, id only, names only
			foreach ($rawGroups as $group)
			{
				$_groups['all'][$group->id] = $group->title;
				$_groups['id'][] = $group->id;
				$_groups['title'][] = $group->title;
			}
		}

		return $_groups[$format];
	}

	public static function isInGroupList($groups, $groupsList)
	{
		if (empty($groups) || empty($groupsList))
		{
			return false;
		}

		foreach ($groups as $groupId)
		{
			if (in_array($groupId, $groupsList))
			{
				return true;
			}
		}

		return false;
	}

	public static function stripTrackingVarsFromNonSef($url)
	{

		$trackingVars = self::_getTrackingVars();

		return self::stripVarsFromNonSef($url, $trackingVars);
	}

	public static function stripTrackingVarsFromSef($url)
	{

		// do we have query vars?
		$parts = explode('?', $url);
		if (empty($parts[1]))
		{
			// no variable parts, return identical
			return $url;
		}

		$trackingVars = self::_getTrackingVars();
		$cleaned = self::stripVarsFromNonSef('?' . $parts[1], $trackingVars);

		// rebuild and return
		$cleaned = JString::ltrim($cleaned, '?&');
		$cleaned = $parts[0] . (empty($cleaned) ? '' : '?' . $cleaned);

		return $cleaned;
	}

	public static function extractTrackingVarsFromNonSef($url, &$existingVars, $keepThem = false)
	{

		$trackingVars = self::_getTrackingVars();
		foreach ($trackingVars as $var)
		{
			// collect existing value, if any
			$value = shGetURLVar($url, $var, null);
			if (!is_null($value))
			{
				// store extracted value into passed array
				$existingVars[$var] = $value;
			}
			// still remove var from url
			if (!$keepThem)
			{
				$url = shCleanUpVar($url, $var);
			}
		}
		return $url;
	}

	protected static function _getTrackingVars()
	{

		$remoteConfig = Sh404sefHelperUpdates::getRemoteConfig($forced = false);
		// hardcoded default tracking vars
		// NOTE: when modifyng this list, be sure to update the online config file, so that existing users are updated immediately
		$defaultTrackingVars = array('utm_source', 'utm_medium', 'utm_term', 'utm_content', 'utm_id', 'utm_campaign', 'gclid', 'fb_xd_bust',
			'fb_xd_fragment');
		// try to read from remote, central, configuration file
		$remoteTrackingVars = empty($remoteConfig->config['trackingvars']) ? array() : $remoteConfig->config['trackingvars'];
		// merge default and remote
		$trackingVars = array_merge($remoteTrackingVars, $defaultTrackingVars);

		return $trackingVars;
	}

	public static function stripVarsFromNonSef($url, $vars = array())
	{

		if (!empty($vars))
		{
			foreach ($vars as $var)
			{
				$url = shCleanUpVar($url, $var);
			}
		}

		return $url;
	}

	/**
	 * Returns the sh404SEF SEF url for a give non-sef url,
	 * creating it on the fly if not already in the database
	 *
	 * @param string $nonSefUrl non-sef url, starting with index.php?...
	 * @param boolean $fullyQualified if true, return a fully qualified url, including protocol and host
	 * @param boolean $xhtml
	 * @param  $ssl
	 */
	public static function getSefFromNonSef($nonSefUrl, $fullyQualified = true, $xhtml = false, $ssl = null)
	{

		if (!defined('SH404SEF_IS_RUNNING'))
		{
			return false;
		}

		$pageInfo = Sh404sefFactory::getPageInfo();

		if (empty($nonSefUrl))
		{
			return $pageInfo->getDefaultFrontLiveSite();
		}

		$newUri = new JURI($nonSefUrl);
		$originalUri = clone $newUri;

		$route = shSefRelToAbs($nonSefUrl, $shLanguageParam = '', $newUri, $originalUri);
		$route = ltrim(str_replace($pageInfo->getDefaultFrontLiveSite(), '', $route), '/');
		$route = $route == '/' ? '' : $route;

		// find path
		$nonSefVars = $newUri->getQuery($asArray = true);
		if (strpos($route, '?') !== false && !empty($nonSefVars))
		{
			$parts = explode('?', $route);
			// there are some query vars, just use the path
			$path = $parts[0];
		}
		else
		{
			$path = $route;
		}
		$newUri->setPath($path);

		if ($fullyQualified || (int) $ssl === 1)
		{
			// remove protocol, host, etc, only keep relative-to-site part

			$liveSite = $pageInfo->getDefaultFrontLiveSite();
			if ((int) $ssl === 1 && substr($liveSite, 0, 7) == 'http://')
			{
				$liveSite = str_replace('http://', 'https://', $liveSite);
			}
			$sefUrl = $liveSite . '/' . $newUri->toString();
		}
		else
		{
			$sefUrl = '/' . $newUri->toString(array('path', 'query', 'fragment'));
		}

		if ($xhtml)
		{
			$sefUrl = htmlspecialchars($sefUrl);
		}

		return $sefUrl;
	}

}
