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

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * sh404SEF system plugin
 *
 * @author
 */
class plgSystemSh404sef extends JPlugin
{

	static $_template = '';

	public function onAfterInitialise()
	{

		// prevent warning on php5.3+
		$this->_fixTimeWarning();

		// get joomla application object
		$app = JFactory::getApplication();

		// check shLib is available
		if (!defined('SHLIB_VERSION'))
		{
			$app->enqueuemessage('sh404SEF requires the shLib system plugin to be enabled, but you appear to have disabled it. Please enable it again!',
					'error');
			return;
		}

		// register our autoloader
		$this->_registerAutoloader();

		require_once(JPATH_ADMINISTRATOR . '/components/com_sh404sef/sh404sef.class.php');

		// get our configuration
		$sefConfig = &Sh404sefFactory::getConfig();

		// hook for a few SEO hacks
		if ($app->isSite())
		{
			$this->_hacks(JRequest::get(), $sefConfig);
		}

		// security layer
		if (!$app->isAdmin() && $sefConfig->shSecEnableSecurity)
		{
			require_once(JPATH_ROOT . '/components/com_sh404sef/shSec.php');
			// do security checks
			shDoSecurityChecks();
			shCleanUpSecLogFiles(); // see setting in class file for clean up frequency
		}

		// optionnally collect page creation time
		if (!$app->isAdmin() && $sefConfig->analyticsEnableTimeCollection)
		{
			jimport('joomla.error.profiler');
			// creating the profiler object will start the counter
			$profiler = JProfiler::getInstance('sh404sef_profiler');
		}

		// load plugins, as per configuration
		$this->_loadPlugins($type = 'sh404sefcore');

		// load extension plugins, created by others
		$this->_loadPlugins($type = 'sh404sefext');

		// hook to be able to install other SEF extension plugins
		Sh404sefHelperExtplugins::loadInstallAdapters();

		// another hook to allow other SEF extensions language file to be loaded
		Sh404sefHelperExtplugins::loadLanguageFiles();

		if (!$sefConfig->Enabled)
		{
			// go away if not enabled
			return;
		}

		// fake language filter
		if ($sefConfig->enableMultiLingualSupport)
		{
			$app->set('menu_associations', 1);
			if (!$app->isAdmin())
			{
				$app->setLanguageFilter(true);
			}
		}

		if (!defined('SH404SEF_IS_RUNNING'))
		{
			DEFINE('SH404SEF_IS_RUNNING', 1);
		}

		if (!$app->isAdmin())
		{
			// setup our JPagination replacement, so as to bring
			// back # of items per page in the url, in order
			// to properly calculate pagination
			// will only work if php > 5, so test for that
			if (version_compare(phpversion(), '5.0') >= 0)
			{
				// this register the old file, but do not load it if PHP5
				// will prevent further calls to the same jimport()
				// to actually do anything, because the 'joomla.html.pagination' key
				// is now registered statically in Jloader::import()
				jimport('joomla.html.pagination');
				// now we can register our own path
				JLoader::register('JPagination', JPATH_ADMINISTRATOR . '/components/com_sh404sef/pagination.php');

			}

			// create the unique page info object, and initialize it
			$pageInfo = Sh404sefFactory::getPageInfo();
			$pageInfo->init();

			// attach parse and build rules to Joomla router
			$joomlaRouter = $app->getRouter();
			$pageInfo->router = new Sh404sefClassRouter();
			$joomlaRouter->attachParseRule(array($pageInfo->router, 'parseRule'));
			$joomlaRouter->attachBuildRule(array($pageInfo->router, 'buildRule'));

			// force J! router config to SEF as user may or may not have
			// done it in configuration
			// this will ensure parent Joomla! router process start variables
			// to limitstart
			//$joomlaRouter->setMode(JROUTER_MODE_SEF);

			// pretend SEF is on, mostly for Joomla SEF plugin to work
			// as it checks directly 'sef' value in config, instead of
			// using $router->getMode()
			JFactory::$config->set('sef', 1);

			// kill Joomla suffix, so that it doesn't add or remove it in the parsing/building process
			JFactory::$config->set('sef_suffix', 0);

			// we use opposite setting from J!
			$mode = 1 - $sefConfig->shRewriteMode;
			JFactory::$config->set('sef_rewrite', $mode);

			// perform startup operations, such as detecting request caracteristics
			// and checking redirections
			$uri = JURI::getInstance();
			$pageInfo->router->startup($uri);

		}
	}

	/**
	 * Various operations :
	 *  - load our plugins
	 * @return unknown_type
	 */
	public function onAfterRoute()
	{

		if (defined('SH404SEF_IS_RUNNING'))
		{

			// set template, to perform alternate template output, if set to
			$app = JFactory::getApplication();
			if (!$app->isAdmin())
			{
				$this->_setAlternateTemplate();
			}

		}

	}

	public function onAfterDispatch()
	{

		if (defined('SH404SEF_IS_RUNNING'))
		{
			$app = JFactory::getApplication();

			if (!$app->isAdmin())
			{
				// reset alternate template
				$this->_resetAlternateTemplate();

				// create shurl on the fly for this page
				// if not already done
				if (JFactory::getDocument()->getType() == 'html')
				{
					// shortlinks
					Sh404sefHelperShurl::updateShurls();
				}
			}
		}
	}

	/* page rewriting features */
	public function onAfterRender()
	{

		if (defined('SH404SEF_IS_RUNNING'))
		{
			if (JFactory::getApplication()->isAdmin())
			{
				return;
			}

			$sefConfig = Sh404sefFactory::getConfig();

			// return if no seo optim to perform
			if ($sefConfig->shMetaManagementActivated || $sefConfig->analyticsEnabled)
			{
				// go away if not enabled
				$include = JPATH_ROOT . '/components/com_sh404sef/shPageRewrite.php';
				require_once($include);
			}
		}
	}

	/**
	 * A set of SEO hacks that don't fit elsewhere
	 * as we usually want a very quick response and
	 * avoid wasted resources
	 *
	 * @param object $sefConfig our global configuration
	 */
	protected function _hacks($sefConfig)
	{

		$getRequest = JRequest::get('GET');

		// facebook: provide a channelUrl to like/Send buttons
		$option = empty($getRequest['option']) ? '' : $getRequest['option'];
		$view = empty($getRequest['view']) ? '' : $getRequest['view'];
		$format = empty($getRequest['format']) ? 'raw' : $getRequest['format'];
		$langtag = empty($getRequest['langtag']) ? 'en_GB' : $getRequest['langtag'];

		if ($option == 'com_sh404sef' && $view == 'channelurl' && $format == 'raw')
		{
			// this is a request for the channelUrl
			$pageContent = '<script src="//connect.facebook.net/' . $langtag . '/all.js"></script>';
			if (!headers_sent())
			{
				$cache_expire = 60 * 60 * 24 * 365;
				header("Pragma: public");
				header("Cache-Control: max-age=" . $cache_expire);
				header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $cache_expire) . ' GMT');
			}
			echo $pageContent;
			jexit();
		}

	}

	/**
	 * Load and register the plugins currently activated by webmaster
	 *
	 * @return none
	 */
	protected function _loadPlugins($type)
	{

		// required joomla library
		jimport('joomla.plugin.helper.php');

		// import the plugin files
		$status = JPluginHelper::importPlugin($type);

		return $status;

	}

	/**
	 * Register our autoloader function with PHP
	 */
	protected function _registerAutoloader()
	{

		// get Joomla autloader out
		spl_autoload_unregister("__autoload");

		// add our own
		include JPATH_ADMINISTRATOR . '/components/com_sh404sef/helpers/autoloader.php';
		$registered = spl_autoload_register(array('Sh404sefAutoloader', 'doAutoload'));

		// stitch back Joomla's at the end of the list
		if (function_exists("__autoload"))
		{
			spl_autoload_register("__autoload");
		}

		if (!defined('SH404SEF_AUTOLOADER_LOADED'))
		{
			define('SH404SEF_AUTOLOADER_LOADED', 1);
		}

	}

	protected function _fixTimeWarning()
	{
		// prevent timezone not set warnings to appear all over,
		// especially for PHP 5.3.3+
		$serverTimezone = @date_default_timezone_get();
		@date_default_timezone_set($serverTimezone);
	}

	protected function _setAlternateTemplate()
	{

		$app = JFactory::getApplication();
		$sefConfig = Sh404sefFactory::getConfig();

		if (!defined('SHMOBILE_MOBILE_TEMPLATE_SWITCHED') && !empty($sefConfig->alternateTemplate))
		{
			// global on/off switch
			self::$_template = $app->getTemplate(); // save current template
			$app->setTemplate($sefConfig->alternateTemplate);
		}
	}

	protected function _resetAlternateTemplate()
	{

		$app = JFactory::getApplication();
		$sefConfig = Sh404sefFactory::getConfig();

		if (!defined('SHMOBILE_MOBILE_TEMPLATE_SWITCHED') && !empty($sefConfig->alternateTemplate))
		{
			// global on/off switch
			if (empty(self::$_template))
			{
				return;
			}
			$app->setTemplate(self::$_template); // restore old template
		}
	}

}

