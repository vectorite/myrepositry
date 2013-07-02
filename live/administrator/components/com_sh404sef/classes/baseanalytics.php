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

/**
 * Implement analytics handling
 *
 * @author shumisha
 *
 */
class Sh404sefClassBaseanalytics
{

	// snippet of code to be inserted in pages where
	// analytics data is to be sent out
	// must contain an {tracking-code} tag
	// whihc will be replaced by the account id
	// provided by user
	protected $_snippet = '';

	// default end point for the analytics service
	protected $_endPoint = '';

	// default authorization url
	protected $_authPoint = '';

	// application key
	protected $_appKeys = array();

	// account list
	protected $_accounts = array();

	// SEF configuration
	protected $_config = null;

	// options for current request (ie account id, format
	protected $_options = null;

	// authorization token, to be cached
	protected $_Auth = '';

	/**
	 * Get tracking snippet
	 *
	 */
	public function getSnippet()
	{

		// get config
		$sefConfig = &Sh404sefFactory::getConfig();
		$pageInfo = &Sh404sefFactory::getPageInfo();

		// should we insert tracking code snippet ?
		if (!$this->_shouldInsertSnippet())
		{
			return '';
		}

		// finalize snippet : add user tracking code
		$snippet = str_replace('{tracking_code}', trim($sefConfig->analyticsId), $this->_snippet);

		// prepare empty array to collect custom vars from plugins
		$customVars = array();

		// fire event so that plugin(s) attach custom vars
		$dispatcher = JDispatcher::getInstance();
		$dispatcher->trigger('onShInsertAnalyticsSnippet', array(&$customVars, $sefConfig));

		// put custom vars into snippet
		for ($i = 1; $i < 6; $i++)
		{
			$marker = '{customVar' . $i . '}';
			if (!empty($customVars[$i]) && !empty($customVars[$i]->name))
			{
				$replace = "_gaq.push(['_setCustomVar', " . $i . ", '" . htmlentities($customVars[$i]->name, ENT_QUOTES, 'UTF-8') . "', '"
					. htmlentities($customVars[$i]->value, ENT_QUOTES, 'UTF-8') . "', 3]);";
			}
			else
			{
				$replace = '';
			}
			$snippet = str_replace($marker, $replace, $snippet);
		}

		// in case of 404, we use a custom page url so that 404s can also be tracked in GA
		$marker = '{customUrl}';
		if (!empty($pageInfo->httpStatus) && $pageInfo->httpStatus == 404)
		{
			//$customUrl = ", '/__404__?page=' + document.location.pathname + document.location.search + '&amp;from=' + document.referrer";
			$customUrl = ", '/__404__'";
		}
		else
		{
			$customUrl = '';
		}
		$snippet = str_replace($marker, $customUrl, $snippet);

		return $snippet;

	}

	public function fetchAnalytics($config, $options)
	{

		// store parameters
		$this->_config = $config;
		$this->_options = $options;

		// prepare a default response object
		$response = new stdClass();
		$response->status = true;
		$response->statusMessage = JText::_('COM_SH404SEF_CLICK_TO_CHECK_ANALYTICS');
		$response->note = '';

		// connect to server and fetch data
		try
		{
			$rawResponse = $this->_fetchData();
		}
		catch (Exception $e)
		{
			$response->status = false;
			$response->statusMessage = $e->getMessage();
			return $response;
		}

		// return response
		$response->analyticsData = $rawResponse;

		// attach html select list or input boxes to response, to allow user to filter the data viewed
		$response->filters = $this->_prepareFilters();

		// update date/time display
		$response->statusMessage = JText::sprintf('COM_SH404SEF_UPDATED_ON', strftime('%c'));

		return $response;
	}

	protected function _fetchData()
	{

		// first try to connect, if we don't already have a token
		$this->_getAuthToken();

		// get the http client
		$hClient = Sh404sefHelperAnalytics::getHttpClient();

		// fetch account list from supplier
		$this->_fetchAccountsList();

		// and find about which one to use (use first one is none selected from a previous request
		if (empty($this->_options['accountId']))
		{
			$this->_options['accountId'] = Sh404sefHelperAnalytics::getDefaultAccountId($this->_accounts);
		}

		// check in case we don' have valid account ID
		if (empty($this->_options['accountId']))
		{
			throw new Sh404sefExceptionDefault('Empty account ID to query analytics API. Contact admin.');
		}

		// create a report object
		$className = 'Sh404sefAdapterAnalytics' . strtolower($this->_config->analyticsType) . 'report' . strtolower($this->_options['report']);
		$report = new $className();

		// ask it to perform API requests as needed,
		$dataResponse = $report->fetchData($this->_config, $this->_options, $this->_Auth, $this->_endPoint, $this->_getAppKey());

		// return data response for further processing
		return $dataResponse;

	}

	/**
	 * Fetch list of accounts, to be overloaded
	 */
	protected function _fetchAccountsList()
	{

	}

	/**
	 * Cache authorization token
	 */
	protected function _getAuthToken()
	{

		// create cache Id and get cache object
		$cacheId = md5($this->_config->analyticsPassword . $this->_config->analyticsUser . 'sdfhk546548-(}=])))');

		$cache = JFactory::getCache('sh404sef_analytics_auth');
		$cache->setLifetime(10080); // cache result for 7 days
		$cache->setCaching(1); // force caching on

		$this->_Auth = $cache->get(array($this, 'doGetAuthToken'), $args = array(), $cacheId);

	}

	/**
	 * performs actuall request to get token
	 */
	public function doGetAuthToken()
	{

		$this->_prepareConnectRequest();
		$connectReponse = $this->_connect();
		$this->_handleConnectResponse($connectReponse);

		return $this->_Auth;
	}

	/**
	 * Connects to analytics supplier
	 *
	 * Meant to be overloaded by adapter
	 *
	 * @param $config , sef config object, holding connecton parameters
	 */
	protected function _connect()
	{

		// get the http client
		$hClient = Sh404sefHelperAnalytics::getHttpClient();

		// establish connection with available methods
		$adapters = array('Zendshl_Http_Client_Adapter_Curl', 'Zendshl_Http_Client_Adapter_Socket');
		$rawResponse = null;

		// perform connect request
		foreach ($adapters as $adapter)
		{
			try
			{
				$hClient->setAdapter($adapter);
				$rawResponse = $hClient->request();
				break;
			}
			catch (Exception $e)
			{
				// we failed, let's try another method
				//echo '<br />exception in _connect' . $e->getMessage();
			}
		}

		// return if error
		if (empty($rawResponse))
		{
			$msg = 'unknown code';
			throw new Sh404sefExceptionDefault(JText::sprintf('COM_SH404SEF_ERROR_CHECKING_ANALYTICS', $msg));
		}
		if (!is_object($rawResponse) || $rawResponse->isError())
		{
			$msg = method_exists($rawResponse, 'getStatus') ? $rawResponse->getStatus() : 'unknown code';
			throw new Sh404sefExceptionDefault(JText::sprintf('COM_SH404SEF_ERROR_CHECKING_ANALYTICS', $msg));
		}

		// success, return response
		return $rawResponse;

	}

	/**
	 * Set client object to perform request
	 * for connection to analytics service
	 *
	 * To be oveloaded
	 */
	protected function _prepareConnectRequest()
	{

		return true;
	}

	/**
	 * Handle response from connect request
	 *
	 * To be overloaded
	 *
	 */
	protected function _handleConnectResponse($response)
	{
		return true;
	}

	/**
	 * Check if user set parameters and request
	 * data allow inserting tracking snippet
	 */
	protected function _shouldInsertSnippet()
	{
		// get config
		$sefConfig = &Sh404sefFactory::getConfig();

		// check if we have a tracking code, no need to insert snippet if no tracking code
		if (empty($sefConfig->analyticsId))
		{
			return false;
		}

		// check if we are set to include tracking code for current user
		if (!sh404sefHelperGeneral::isInGroupList(JAccess::getGroupsByUser(JFactory::getUser()->id, $recursive = true), $sefConfig->analyticsUserGroups))
		{
			return false;
		}
		// check if current IP is on exclusion list
		if (!empty($sefConfig->analyticsExcludeIP))
		{
			$ip = empty($_SERVER['REMOTE_ADDR']) ? '' : $_SERVER['REMOTE_ADDR'];
			$exclude = Sh404sefHelperGeneral::checkIPList($ip, $sefConfig->analyticsExcludeIP);
			if ($exclude)
			{
				return false;
			}
		}

		return true;
	}

	protected function _prepareCommonHeaders()
	{
	}

	/**
	 * prepare html filters to allow user to select the way she likes
	 * to view reports
	 */
	protected function _prepareFilters()
	{
		// array to hold various filters
		$filters = array();

		return $filters;
	}

	protected function _getAppKey()
	{
		static $_key = null;

		if (is_null($_key))
		{
			$remoteConfig = Sh404sefHelperUpdates::getRemoteConfig($forced = false);
			$keys = empty($remoteConfig->config['google_analytics_api_keys']) ? $this->_appKeys : $remoteConfig->config['google_analytics_api_keys'];
			$_key = $keys[mt_rand(0, count($keys) - 1)];
		}

		return $_key;

	}

}
