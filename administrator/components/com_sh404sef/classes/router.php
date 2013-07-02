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
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.model');
jimport( 'joomla.utilities.string');

/**
 * Class to create and parse routes for the site application
 *
 * @author    Yannick Gaultier
 * @package   Sh404SEF
 * @since   1.6
 */
class Sh404sefClassRouter extends JRouterSite {

  const ROUTER_MODE_SH404SEF = 999;

  // flag, to make sure we know when we are parsing the request
  // parsing can be called upon several times by Joomla
  // however some operations associated with parsing (mostly
  // automated redirects) should be done only once
  public static $requestParsed = false;

  public static $parsedWithJoomlaRouter = false;

  protected $_shId = 'sh404SEF for Joomla 1.6';

  /**
   * Perform startup operations such as detecting environment
   * and checking automated redirections
   */
  public function startup( & $uri) {

    // check some SEO related redirects
    $this->_checkSeoRedirects( $uri);

    // check www vs non-www and other domain related stuff
    // may redirect to another url
    $this->_checkDomain( $uri);

    // load common language strings
    $language = JFactory::getLanguage();
    $language->load('com_sh404sef.sys', JPATH_ADMINISTRATOR);

  }

  /**
   * Method attached to J! main router object
   * and processed as a parseRule
   *
   * @param object J! router object reference
   * @param object uri object provided by Joomla. Will represent request upon
   * first call of this method, but any uri upon subsequent calls
   * @return array $vars list of query variables as decoded by us, if any
   */
  public function parseRule( & $jRouter, &$uri) {

    static $_vars = array();

    // calculate signature of uri, avoiding multiple parsing of same uri
    $signature = md5( $this->getFullUrl( $uri));

    // don't parse twice, this would break J! 1.6 Router mode handling
    if(isset($_vars[$signature])) {
      if(!self::$parsedWithJoomlaRouter) {
        $uri->setPath( '');
        $uri->setQuery( $_vars[$signature]);
      }
      return $_vars[$signature];
    }

    // debug info
    ShlSystem_Log::debug( 'sh404sef', 'Starting to parse %s', print_r( $uri, true));

    // fix the path before checking url. Joomla unfortunately remove
    // the trailing slash from the path, thus we're not working on the actual requested URL
    if(!self::$requestParsed) {
      $path = $uri->getPath();
      if(!empty( $path)) {
        if($this->_hasTrailingSlash() & JString::substr( $path, -1) != '/') {
          // if trailing slash on original request, but not on path
          // put it back
          $uri->setPath( $path . '/');
        }
      }
    }

    // check if user has set any alias for this - possibly non-sef - url
    $this->_checkAliases( $uri);

    // check SEO redirects, such as index.php on home page
    $this->_checkHomepageRedirects( $uri);

    // do the parsing
    $_vars[$signature] = $this->_parseSefRoute( $uri);

    if(!empty( $_vars[$signature])) {
      // merge our decoded vars with those that could be passed as query string
      $_vars[$signature] = array_merge( $_vars[$signature], $uri->getQuery( $asArray = true));
      if(!self::$parsedWithJoomlaRouter) {
        $uri->setQuery( $_vars[$signature]);
      }

      // kill the path, so that J! router don't try to keep parsing
      // the sef url
      if(!self::$parsedWithJoomlaRouter) {
        $uri->setPath( '');

        // when J! will try parse this as RAW route, for some reasons it
        // tries to get the Itemid from the request, so we have to fake having one
        if(!empty( $_vars[$signature]['Itemid'])) {
          JRequest::setVar( 'Itemid', (int) $_vars[$signature]['Itemid']);
        }
      }

      // now store the decoded current non-sef url
      $nonSefUrl = 'index.php' . $uri->toString( array( 'query', 'fragment'));
      Sh404sefFactory::getPageInfo()->currentNonSefUrl = $nonSefUrl;

    }

    $this->_tryToHandleJoomlaBloodyMultilingualFeat( $uri, $_vars[$signature]);

    // mark request as parsed, so that we don't try to do
    // redirects and such if the router parse method is
    // called several times
    self::$requestParsed = true;

    // Send that back to J! router to put everything together
    if(self::$parsedWithJoomlaRouter) {
      return array();
    } else {
      return $_vars[$signature];
    }

  }

  protected function _tryToHandleJoomlaBloodyMultilingualFeat( &$uri, $vars) {

    if( self::$requestParsed || !Sh404sefFactory::getConfig()->enableMultiLingualSupport) {
      return;
    }

    $currentLanguageTag = JFactory::getLanguage()->getTag();
    $cookieTag = JRequest::getString(JApplication::getHash('language'), null ,'cookie');

    // what's language extracted from URL?
    $langCode = empty( $vars['lang']) ? $currentLanguageTag : shGetNameFromIsoCode($vars['lang']);
    $pageInfo = Sh404sefFactory::getPageInfo();
    // Create a cookie, if target different from current language
    if( $langCode != $cookieTag) {
      $conf = JFactory::getConfig();
      $cookie_domain 	= $conf->get('config.cookie_domain', '');
      $cookie_path 	= $conf->get('config.cookie_path', '/');
      setcookie(JApplication::getHash('language'), $langCode, time() + 365 * 86400, $cookie_path, $cookie_domain);
      // set the request var
      JRequest::setVar('language',$langCode);
    }

    // try to redirect users to its own language homepage, upon first visit
    $this->_languageRedirect( $uri, $langCode);

    // set current language, if different from current
    if( $langCode != $currentLanguageTag) {
      jimport('joomla.language.language');
      $conf	= JFactory::getConfig();
      $debug	= $conf->get('debug_lang');
      $lang	= JLanguage::getInstance( $langCode, $debug);

      // Reload the library language strings, in the correct language
      $lang->load('lib_joomla', JPATH_SITE, $langCode, false, false);

      // now set language object
      JFactory::$language = $lang;
      $pageInfo = Sh404sefFactory::getPageInfo();
      $pageInfo->currentLanguageTag = $langCode;
      $pageInfo->currentLanguageShortTag = $vars['lang'];
    }

  }

  /**
   * Method attached to J! main router object
   * and processed as a buildRule
   *
   * @param object J! router object reference
   * @param object uri object provided by Joomla. We must set the path of this
   * object, and adjust its query vars list, removing those that are represented
   * in the path and thus don't need anymore to be specified as query vars
   */
  public function buildRule( &$jRouter, &$uri) {

    // some component may be set to simply use J! router. Detect and return w/o doing anything
    $option = $uri->getVar( 'option');
    $option = str_replace( 'com_', '', $option);
    if(!empty( $option) && in_array( $option, Sh404sefFactory::getConfig()->useJoomlaRouter)) {
      return;
    }

    // Make sure any menu vars are used if no others are specified
    // Joomla already does that, but does not detect paginated versions
    $limit = $uri->getVar('limit');
    $limitStart = $uri->getVar('limitstart');
    if (($this->_mode != JROUTER_MODE_SEF) && $uri->getVar('Itemid') && $uri->getVar('limitstart') && $uri->getVar('limit') && count($uri->getQuery(true)) == 4) {

      $app	= JApplication::getInstance('site');
      $menu	= $app->getMenu();

      // Get the active menu item
      $itemid = $uri->getVar('Itemid');
      $item = $menu->getItem($itemid);

      if ($item) {
        $uri->setQuery($item->query);
      }
      $uri->setVar('Itemid', $itemid);
      $uri->setVar('limit', $limit);
      $uri->setVar('limitstart', $limitStart);
    }

    // build the path
    $this->_buildSefRoute( $uri);

  }

  /**
   * Public wrapper around encode route segments
   * from J! router method
   *
   * @param	array	An array of route segments
   * @return  array
   */
  public function encodeSegments($segments)	{

    return $this->_encodeSegments($segments);

  }

  /**
   * Actual method performing parsing of a request as described
   * by an JURI object
   *
   * Also performs additional duties, like checking on aliases,
   * searching alternates syntax (add/remove trailing slash),
   * auto-redirect from non-sef to sef, etc
   * Possibly trigger a 404 if url can't be parsed to a valid
   * non sef url
   *
   * @see JRouter::_parseSefRoute()
   */
  protected function _parseSefRoute( & $uri) {

    // will hold query vars parsed
    $vars = array();

    // general config
    $sefConfig = Sh404sefFactory::getConfig();
    $pageInfo = Sh404sefFactory::getPageInfo();

    // get request path and try to decode it
    $path = $uri->getPath();

    // home page
    if(empty( $path)) {

      // check more redirects: from non sef to sef
      $this->_checkNonSefToSefRedirects( $uri);

      // now if no query vars, this is really home page
      $uriVars = $uri->getQuery( true);
      if(empty($uriVars)) {
        // Create the query array.
        $homeLink = $pageInfo->homeLink;
        $homeLink = str_replace('index.php?', '', $homeLink);
        $homeLink = str_replace('&amp;', '&', $homeLink);

        parse_str( $homeLink, $item->query);
        $vars = $item->query;

        // Get its Itemid
        $vars['Itemid'] = $pageInfo->homeItemid;
      } else {
        // if some query vars, just pass them thru
        $vars = $uriVars;
      }

    }

    // non home page
    if(!empty( $path)) {

      // lookup db for this sef
      $lookUp = $this->_lookupSef( $uri, $vars, $path);

      // store the fetched data, if any
      $vars = $lookUp->vars;

      // and take additional actions based on the result of the lookup
      switch ($lookUp->urlType) {

        // existing matching url
        case sh404SEF_URLTYPE_AUTO:
        case sh404SEF_URLTYPE_CUSTOM:
          break;

          // 404 or some kind of redirect
        default:

          // check more redirects: from Joomla SEF to our SEF
          $this->_checkJoomlaSefToSefRedirects( $uri);

          // try find similar urls to redirect to: with or without trailing slash
          $this->_checkTrailingSlash( $uri);

          // there might be an alias we're supposed to redirect current request to
          $this->_checkAliases( $uri);

          // check if this is a short url
          $this->_checkShurls( $uri);

          // try using J! router
          // if at least one extension uses Joomla! router, we must first try to use that
          if(!empty(Sh404sefFactory::getConfig()->useJoomlaRouter)) {

            // use parent parser
            $vars = parent::_parseSefRoute($uri);

            // if we found something, raise a flag
            self::$parsedWithJoomlaRouter = true;
            // collect vars that may have been stored by J! such as Itemid
            $vars = array_merge( Sh404sefFactory::getPageInfo()->router->getVars(), $vars);
            $this->setVars( array());

            // and cut through the rest of the processing
            break;
          }

          // if no alternative found, issue a 404
          $vars = $this->_do404( $uri);
          break;
      }

    }

    // Set the menu item as active, if we found any
    // this would normally be done by Joomla own _parseSefRoute() method
    // except it's not gonna be run as we changed the routing mode from
    // JROUTER_MODE_SEF to our own (ROUTER_MODE_SH404SEF)
    if(!self::$requestParsed && ($pageInfo->isMultilingual === false || $pageInfo->isMultilingual == 'joomla')) {
      if(!empty( $vars['Itemid'])) {
        JFactory::getApplication()->getMenu()->setActive( $vars['Itemid']);
      }
    }

    // do security checks after decoding url
    if ($sefConfig->shSecEnableSecurity) {
      require_once( JPATH_ROOT. '/components/com_sh404sef/shSec.php');
      // do security checks
      shDoSecurityChecks( $this->getFullUrl( $uri), false);// check this newly created URL
    }

    return $vars;
  }

  /**
   * Put back the base path that was removed by Joomla router prior to sending
   * uri to parserule
   *
   * @param JURI $uri
   */
  protected function getFullUrl( $uri) {

    $clone = clone $uri;
    $basePath = $clone->base( true);
    if(!empty( $basePath)) {
      $currentPath = $clone->getPath();
      $clone->setPath( $basePath . '/' . $currentPath);
    }

    $fullUrl = $clone->toString();
    return $fullUrl;
  }

  /**
   * Redirect user, on first visit, to its own language
   * as detected in browser
   * Happens only on home page, as otherwise we wouldn't know
   * on which page to redirect him/her
   *
   * @param JURI $uri
   */
  protected function _languageRedirect( $uri, $currentLanguageCode) {

    if( !$this->_canRedirectFrom($uri)) {
      return;
    }

    $pageInfo = Sh404sefFactory::getPageInfo();

    // decide whether this is homepage request
    $path = JString::trim( str_replace( $pageInfo->getDefaultFrontLiveSite() . '/', '', $pageInfo->currentSefUrl), '/');
    $possibleUrls = array();
    foreach( $pageInfo->homeLinks as $language => $link) {
      $possibleUrls[] = shGetIsoCodeFromName($language);
    }
    if(!empty( $path) && !in_array( $path, $possibleUrls)) {
      return;
    }

    $cookieTag = JRequest::getString(JApplication::getHash('language'), null ,'cookie');

    // no cookie - try autoredirect to user language
    if(empty( $cookieTag)) {
      // get language filter plugin params
      try {
        $rawParams = ShlDbHelper::selectObject('#__extensions', 'params', array('type' => 'system', 'name' => 'languagefilter'));
      } catch (Exception $e) {
        return;
      }
      $langFilterParams = new JRegistry;
      $langFilterParams->loadString( $rawParams);
      if ($langFilterParams->get('detect_browser', 1)){
        $userLanguage = JLanguageHelper::detectLanguage();
        if(!empty( $userLanguage) && $userLanguage != $currentLanguageCode) {
          ShlSystem_Log::debug( 'sh404sef', 'User lang: ' . $userLanguage . ' langcode: ' . $currentLanguageCode);
          // user has a specific language, let's redirect to the home page in that language
          $target = JRoute::_( $pageInfo->homeLinks[$userLanguage]);
          shRedirect($target);
        }
      }
    }
  }

  protected function _buildSefRoute(&$uri) {

    $pageInfo = & Sh404sefFactory::getPageInfo();
    $sefConfig = Sh404sefFactory::getConfig();
    $menu = JFactory::getApplication()->getMenu();
    // keep a copy of  Joomla original URI, which has article names in it (ie: 43:article-title)
    $originalUri = clone( $uri);
    shNormalizeNonSefUri( $uri, $menu);
    //shNormalizeNonSefUri( $originalUri, $menu, $removeSlugs = false);

    // do our job!
    $query = $uri->getQuery(false);

    // shortcut for components that must be left non-sef
    $option = shGetURLVar($query, 'option');
    if(!empty( $option)) {
      $option = str_replace('com_', '', $option);
      if(in_array( $option, $sefConfig->skip)) {
        return;
      }
    }

    // no shortcut, normal route
    $route = shSefRelToAbs( 'index.php?' . $query, null, $uri, $originalUri);
    $route = ltrim(str_replace( $pageInfo->getDefaultFrontLiveSite(), '',$route), '/');
    $route = $route == '/' ? '' : $route;
    // find path
    $nonSefVars = $uri->getQuery( $asArray = true);
    if (strpos( $route, '?') !== false && !empty( $nonSefVars)) {
      $parts = explode( '?', $route);
      if ($sefConfig->shRewriteMode == 2) {
        // '/index.php?/' - removed from V 3.0+, but code left, as maybe we can put it back later?
        // need to extract the first part of the query, which is actually the path
        // and store it as the path
        $tmpParts = explode( '/index.php?/', $route);
        $tmpPath = '';
        if (isset( $tmpParts[1])) {
          // there is somethings after the /index.php?/
          if( strpos( $tmpParts[1], '?') !== false) {
            // but this can also have parameters, so only get the path
            $tmpPathParts = explode( '?', $tmpParts[1]);
            $tmpPath = $tmpPathParts[0];
          } else {
            $tmpPath = $tmpParts[1];
          }
        }
        $path = $tmpParts[0] . '/index.php?/' . $tmpPath;
      } else {
        // there are some query vars, just use the path
        $path = $parts[0];
      }
    } else {
      $path = $route;
    }
    $uri->setPath( $path);
  }

  /**
   *
   * Lookup a SEF url in the cache and database, searching
   * for a non-sef associated url
   * Returns a record holding query vars and a status code
   * If no non-sef is found, incoming query vars are returned untouched
   * If a non-sef is found, a query var array is built, merged with incoming vars
   * and returned instead of the incoming one
   *
   * @param JURI object full request details
   * @param array $vars key/value pairs of query vars, usually empty
   * @param string $sefUrl the sef url to search for
   */
  protected function _lookupSef( $uri, $vars, $sefUrl) {

    // object to hold result
    $result = new stdClass();

    // identify Sh404sefClassBaseextplugin::TYPE_SIMPLE URLs, ie simple encoding, DB bypass
    $isSimpleUrl = JString::substr($sefUrl, 0, 14) == 'component/com_';
    if($isSimpleUrl) {
      // handle manual decoding
      $vars = $this->_parseSimpleUrls( $sefUrl);
      $urlType = sh404SEF_URLTYPE_AUTO;
    }

    if(!$isSimpleUrl) {
      // get a model and check if we've seen this request before
      $nonSefUrl = '';
      $urlType = ShlMvcModel_Base::getInstance( 'Sefurls', 'Sh404sefModel')->getNonSefUrlFromDatabase( $sefUrl, $nonSefUrl);

      switch ($urlType) {

        // existing matching url
        case sh404SEF_URLTYPE_AUTO:
        case sh404SEF_URLTYPE_CUSTOM:

          // our db lookup is case insensitive, which allows
          // doing a 301 to the correct url case, avoiding duplicate content
          $this->_checkRedirectToCorrectCase( $uri, $sefUrl);

          // collect the query vars, using a JURI instance
          $newUri = new JURI( $nonSefUrl);
          $vars = array_merge( $vars, $newUri->getQuery(true));
          break;

          // 404 or some kind or error
        default:
          break;
      }
    }

    // store result
    $result->vars = $vars;
    $result->urlType = $urlType;

    return $result;
  }

  /**
   *
   * Parse sef urls built using the TYPE_SIMPLE method
   * ie: simply combining each argument, comma separated
   *
   * @param string $nonSefUrl starting with /component/com_xxxxx/...
   */
  protected function _parseSimpleUrls( $sefUrl) {

    $vars = array();

    // create an array
    $urlArray = explode('/', $sefUrl);
    $urlArray = array_filter( $urlArray);
    $urlArray = array_values( $urlArray);

    // remove 'component'
    array_shift( $urlArray);

    // start by extracting option, which is always first
    $option = array_shift( $urlArray);

    // sanitize
    jimport('joomla.filter.filterinput');
    $vars['option'] = JFilterInput::clean( $option, 'cmd');

    // process remaining vars, if any
    if(!empty( $urlArray)) {
      foreach( $urlArray as $segment) {
        $tmp = explode( ',', $segment, 2);
        if(count( $tmp) == 2) {
          $vars[$tmp[0]] = $tmp[1];
        }
      }
    }

    return $vars;
  }

  /**
   *
   * Performs some checks, and possibly some redirects based
   * on the current request domain
   *
   * Typically, ensure access through either www or non-www version of domain
   *
   * In the future, will also allow for language determination based on
   * domain or sub domain
   *
   * @param JURI object $uri
   */
  protected function _checkDomain( $originalUri) {

    // break reference
    $uri = clone $originalUri;

    // get configuration
    $sefConfig = Sh404sefFactory::getConfig();
    $pageInfo = & Sh404sefFactory::getPageInfo();

    // get request infos:
    $rootUrl = $pageInfo->getDefaultFrontLiveSite();
    $host = $uri->getHost();
    $canRedirect = $this->_canRedirectFrom($uri) && !empty($host) && $host != 'localhost';
    $targetUrl = '';

    // first check if there are some settings for per language domain, and apply them
    // search for live site in our list of per language root url
    foreach( $sefConfig->liveSites as $language => $langRootUrl) {
      if( !empty( $langRootUrl) && $langRootUrl == $rootUrl) {

        // TODO: switch to that language

        // stop
        break;
      }
    }

    // if we have not already
    if (empty($targetUrl) && $canRedirect && $sefConfig->shAutoRedirectWww != shSEFConfig::DONT_ENFORCE_WWW) {
      if ( substr( $host, 0, 4) != 'www.' && $sefConfig->shAutoRedirectWww == shSEFConfig::ENFORCE_WWW) {
        ShlSystem_Log::debug( 'sh404sef', 'Redirecting from non www to wwww');
        $uri->setHost( 'www.' . $host);
        $targetUrl = $this->getFullUrl( $uri);
      }
      if (substr( $host, 0, 4) == 'www.' && $sefConfig->shAutoRedirectWww == shSEFConfig::ENFORCE_NO_WWW) {
        ShlSystem_Log::debug( 'sh404sef', 'Redirecting from www to non wwww');
        $uri->setHost( str_replace( 'www.', '', $host));
        $targetUrl = $this->getFullUrl( $uri);
      }
    }

    // Redirect if needed
    if (!empty( $targetUrl)) {
      shRedirect( $targetUrl);
    }
  }

  protected function _checkSeoRedirects( $uri) {
    // facebook: it may happen that FB cause an URL that has been liked or otherise shared
    // to be linked to and thus indexed with an added query parameter. This will cause
    // duplicate content issue, so if it happens, we want to 301 redirect to the same URL
    // without that parameter
    $fb_xd_bust = isset( $_GET['fb_xd_bust']);
    $fb_xd_fragment = isset( $_GET['fb_xd_fragment']);
    if(!empty( $fb_xd_bust) || !empty( $fb_xd_fragment)) {

      // sanity checks, like don't redirect if there is some POST data
      if( !$this->_canRedirectFrom($uri)) {
        return;
      }

      // need to redirect, let's kill the faulty params
      $uri->delVar( 'fb_xd_bust');
      $uri->delVar( 'fb_xd_fragment');

      // finally redirect
      shRedirect( $this->getFullUrl( $uri));

    }
  }

  protected function _checkRedirectToCorrectCase( $uri, $path) {

    if(!$this->_canRedirectFrom( $uri)) {
      return;
    }

    // get config object
    $sefConfig = Sh404sefFactory::getConfig();

    if ($sefConfig->redirectToCorrectCaseUrl) {
      // if initial query exactly matches oldurl found in db, then case is correct
      // else we redirect to the url found in db, but we also need to append query string to it !
      $originalPath = $uri->getPath();

      // now the only difference between the two can be the case
      if ( $originalPath != $path) {
        // can only be different from case, change case in uri, and add rewrite mode prefix, if any
        $uri->setPath( $path);
        $targetUrl = $uri->base() . ltrim( $sefConfig->shRewriteStrings[$sefConfig->shRewriteMode], '/') . $uri->toString( array('path', 'query', 'fragment'));
        // perform redirect
        ShlSystem_Log::debug( 'sh404sef', 'Redirecting to correct url case : from ' . $this->getFullUrl( $uri) . ' to ' . $targetUrl);
        shRedirect( $targetUrl);
      }
    }
  }

  /**
   *
   * Performs various seo redirect checks, in case where the request is
   * for the home page. A home page request only means the request path is empty;
   * such request may have query vars - ie site.com/index.php?option=com_content&id=12&view=article
   * is still a home page request
   *
   * Include redirecting site.com/index.php to site.com and, in the future
   * site.com/index.php?lang=xx to site.com/xx or the correct sef url for that language
   *
   * @param JURI object $uri
   */
  protected function _checkHomepageRedirects( $uri) {

    if(self::$requestParsed || !$this->_canRedirectFromNonSef($uri)) {
      return;
    }

    // check if we already did all the redirections we can
    $pageInfo = Sh404sefFactory::getPageInfo();

    // basic data
    $sefConfig = Sh404sefFactory::getConfig();
    $path = $uri->getPath();
    $url = $this->getFullUrl( $uri);
    $vars = $uri->getQuery( true);

    // 1 - check index.php on home page
    $indexString = explode( '?', $url);
    $indexString = JString::substr( $indexString[0], -9);
    // IIS sometimes adds index.php to uri, even if user did not request it.
    $IIS = !empty($_SERVER['SERVER_SOFTWARE']) && strpos( $_SERVER['SERVER_SOFTWARE'], 'Microsoft-IIS') !== false;

    if(sh404SEF_REDIRECT_IF_INDEX_PHP && !$IIS && empty( $path) && empty( $vars) && JString::substr( $url, -9) == 'index.php') {
      // redirect to home page
      $targetUrl = JString::substr( $url, 0, JString::strlen($url) - 9);
      ShlSystem_Log::debug( 'sh404sef', 'Redirecting home page request with index.php to home page: ' . $targetUrl);
      shRedirect( $targetUrl);
    }

    // 2 - Home page, some query vars, but we don't have an option var, ie dunno what component to go to
    // just drop the index.php
    if(empty( $path) && !empty( $vars) && empty( $vars['option']) && empty( $vars['lang']) && $indexString == 'index.php') {
      $query = $uri->getQuery();
      $targetUrl = $uri->base() . (empty( $query) ? '' : '?' . $query);
      ShlSystem_Log::debug( 'sh404sef', 'Redirecting home page request with index.php and query vars to home page: ' . $targetUrl);
      shRedirect( $targetUrl);
    }

    // 3 - request for home page with a language element
    if(empty( $path) && !empty( $vars) && empty( $vars['option']) && !empty( $vars['lang']) && $indexString == 'index.php') {
      $query = $uri->getQuery();
      $targetUrl = $uri->base() . (empty( $query) ? '' : '?' . $query);
      ShlSystem_Log::debug( 'sh404sef', 'Redirecting home page request to non-default language home page: ' . $targetUrl);
      shRedirect( $targetUrl);
    }

    // 4 - Still home page, ie empty path, but some query vars, lookup db to find it
    if(empty($path) && !empty($vars) && !empty( $vars['option'])) {
      // need an url model
      $urlModel = ShlMvcModel_Base::getInstance( 'Sefurls', 'Sh404sefModel');
      // rebuild the non-sef url requested
      $nonSefUrl = 'index.php' . $uri->toString( array( 'query'));
      $sefUrl = '';
      // try to get it from our url store
      $urlType = shGetSefURLFromCacheOrDB( $nonSefUrl, $sefUrl);
      if ($urlType == sh404SEF_URLTYPE_AUTO || $urlType == sh404SEF_URLTYPE_CUSTOM) {
        // found a match in database
        $sefUrl = $uri->base() . $sefUrl;
        ShlSystem_Log::debug( 'sh404sef', 'redirecting non-sef to existing SEF : '.$sefUrl);
        shRedirect( $sefUrl);
      }

      // 5 - no success yet, we'll try SEF-y the non-sef url
      if ( $sefConfig->shRedirectNonSefToSef && !empty( $nonSefUrl) && (!shIsMultilingual()
          || ((shIsMultilingual() !== false)
              && shGetNameFromIsoCode(shDecideRequestLanguage())
              == $pageInfo->currentLanguageTag ))) {
        $sefUrl = $uri->toString( array('scheme', 'host', 'port')) . JRoute::_( $nonSefUrl, $xhtml = false);
        if (!shIsHomepage($sefUrl) && strpos( $sefUrl, 'option=com') === false && $sefUrl != $url) {
          ShlSystem_Log::debug( 'sh404sef', 'Homepage redirect to newly created SEF : '.$sefUrl . ' from ' . $url);
          shRedirect( $sefUrl);
        }
      }
    }

  }

  /**
   *
   * Redirects a non-sef request to its SEF equivalent
   * if any can be found or built
   * @param unknown_type $uri
   */
  protected function _checkNonSefToSefRedirects( $uri) {

    // don't redirect if this is simply J! trying to parse a URL
    // or if this is an ajax call or similar
    if(!$this->_canRedirectFromNonSef( $uri)) {
      return;
    }

    // check this is really a non-sef request
    $path = $uri->getPath();
    if(!empty($path)) {
      // we only try to redirect fully non sef requests, too hard otherwise
      return;
    }

    // prevent homepage loops
    $query = $uri->getQuery();
    if(empty($query)) {
      // empty path and empty query, this is just root url
      return;
    }

    // break linkage
    $newUri = clone $uri;

    // collect languages information
    $languages = JLanguageHelper::getLanguages('lang_code');

    // make sure we have a language code
    $langCode = $newUri->getVar( 'lang');
    if (empty( $langCode)) {
      $currentLang = JFactory::getLanguage()->getTag();
      $newUri->setVar( 'lang', empty( $languages[$currentLang]) || empty($languages[$currentLang]->sef) ? 'en' : $languages[$currentLang]->sef);
    }

    //redirect
    $this->_redirectNonSef($newUri);

  }

  protected function _redirectNonSef( $uri) {

    if( !$this->_canRedirectFromNonSef($uri)) {
      return;
    }

    // search cache and db for a sef url
    $nonSefUrl = shSortURL( 'index.php?' . $uri->getQuery());
    $targetSefUrl = '';
    $urlType = ShlMvcModel_Base::getInstance( 'Sefurls', 'Sh404sefModel')->getSefURLFromCacheOrDB( $nonSefUrl, $targetSefUrl);

    $pageInfo = Sh404sefFactory::getPageInfo();

    // found a match : redirect
    if ($urlType == sh404SEF_URLTYPE_AUTO || $urlType == sh404SEF_URLTYPE_CUSTOM) {
      $tmpUri = new JURI( $uri->base() . $targetSefUrl);
      $targetSefUrl = $tmpUri->toString();
      if( $targetSefUrl != $pageInfo->currentSefUrl) {
        ShlSystem_Log::debug( 'sh404sef', 'redirecting non-sef to existing SEF : '. $targetSefUrl);
        shRedirect( $targetSefUrl);
      }
    }

    // haven't found a SEF in the cache or DB, maybe we can just create it on the fly ?
    if ( !empty( $nonSefUrl) && (!shIsMultilingual()
        || shIsMultilingual() == 'joomla')) {
      // $currentLanguageTag is still deafult lang, as language has not been discovered yet
      $GLOBALS['mosConfig_defaultLang'] = Sh404sefFactory::getPageInfo()->currentLanguageTag;  // V 1.2.4.t joomfish not initialised so we must do
      // create new sef url
      $targetSefUrl = $uri->toString( array('scheme', 'host', 'port')) . JRoute::_( $nonSefUrl, $xhtml = false);
      if( $targetSefUrl != $pageInfo->currentSefUrl) {
        $sourceUrl = $this->getFullUrl( $uri);
        if (strpos( $targetSefUrl, 'option=com') === false && $targetSefUrl != $sourceUrl) {
          ShlSystem_Log::debug( 'sh404sef', 'Redirecting non-sef to newly created SEF : '.$targetSefUrl . ' from ' . $nonSefUrl);
          shRedirect( $targetSefUrl);
        }
      }
    }
  }

  protected function _checkJoomlaSefToSefRedirects( $uri) {

    if(self::$requestParsed) {
      return;
    }

  }

  protected function _checkTrailingSlash( $uri) {

    if(!$this->_canRedirectFrom( $uri)) {
      return;
    }

    // get config object
    $sefConfig = Sh404sefFactory::getConfig();

    // get the url path and try add or remove a trailing slash
    $path = $uri->getPath();

    // if path ends with current suffix, stop here
    if(JString::substr( $path, - JString::strlen( $sefConfig->suffix)) == $sefConfig->suffix) {
      return;
    }

    // same with optional index file
    if(JString::substr( $path, - JString::strlen( $sefConfig->addFile)) == $sefConfig->addFile) {
      return;
    }

    // now add or remove trailing slash. Must check existence of trailing slash
    // on the $uri->_uri, as J! always remove it from $uri->_path
    $trailingSlash = $this->_hasTrailingSlash($uri);
    if($trailingSlash) {
      $path = JString::rtrim( $path, '/');
    } else {
      $path .= '/';
    }

    // and check db again
    $vars = array(); // dummy, we don't care about the actual vars retrieved
    $lookUp = $this->_lookupSef( $uri, $vars, $path);

    // if url exists with slash added or removed, 301 to that valid url
    if($lookUp->urlType == sh404SEF_URLTYPE_AUTO || $lookUp->urlType == sh404SEF_URLTYPE_CUSTOM) {
      $query = $uri->getQuery();
      $targetSefUrl = $uri->base() . $path . (empty( $query) ? '' : '?' . $query);
      ShlSystem_Log::debug( 'sh404sef', 'Redirecting to same with trailing slash added: ' . $targetSefUrl);
      shRedirect( $targetSefUrl);
    }

  }

  protected function _checkAliases( $uri) {

    if(self::$requestParsed) {
      return;
    }

    // our configuration object
    $sefConfig = Sh404sefFactory::getConfig();

    // separate path and full request
    $path = $uri->getPath();
    $requestPath = str_replace( JURI::root() . ltrim( $sefConfig->shRewriteStrings[$sefConfig->shRewriteMode], '/') , '', $this->getFullUrl( $uri));
    $queryString = $uri->getQuery();

    // build sql query, we may check both path and full query
    $sql = 'select ??,?? from ?? where ?? = ?';
    $nameQuoted = array( 'alias', 'newurl', '#__sh404sef_aliases', 'alias');
    $quoted = array( $requestPath);

    // path different from full url requested, means there is a query string
    if(!empty( $path) && $path != $requestPath) {
      $sql .= ' or ?? = ?';
      $nameQuoted[] = 'alias';
      $quoted[]= $path;
      if(JString::substr($path, -1) != '/') {
        // getPath will trim trailing / so we must try also with it
        $sql .= ' or ?? = ?';
        $nameQuoted[] = 'alias';
        $quoted[]= $path . '/';
      }
    }

    // finally order by alias desc, just have to choose one ordering
    $sql .= ' order by ?? desc';
    $nameQuoted[] = 'alias';

    try {
      $dest = ShlDbHelper::quoteQuery( $sql, $nameQuoted, $quoted)->loadObject();

      // append query string if some params were added to the alias
      if (!empty( $dest) && !empty( $dest->newurl) && !empty( $queryString) && $dest->alias != $requestPath) {
        $dest->newurl .= JString::strpos( $dest->newurl, '?') !== false ? '&' . $queryString : '?' . $queryString;
      }

      // do the redirect, after checking a few conditions
      if(!empty( $dest)) {
        shCheckRedirect( $dest->newurl, $this->getFullUrl( $uri) );
      }

    } catch (Sh404sefException $e) {
      // if error, just log
      ShlSystem_Log::error( 'sh404sef', '%s::%s::%d: %s', __CLASS__, __METHOD__, __LINE__, ' Database error reading aliases: ' . $e->getMessage());
    }
  }

  protected function _checkShurls( $uri) {

    if(self::$requestParsed) {
      return;
    }

    // sanitize
    $path = $uri->getPath();
    if(empty( $path)) {
      // no path in request, no possible short url
      return;
    }

    // our configuration object
    $sefConfig = Sh404sefFactory::getConfig();

    // check short url based on request path
    if ($sefConfig->enablePageId ) {

      try {
        $dest = ShlDbHelper::selectResult( '#__sh404sef_pageids', array('newurl'), array( 'pageid' => $path));

        // check on $dest: if empty, prevent loop, plus stitch back query string, if any
        if(!empty( $dest)) {
          $queryString = $uri->getQuery();
          if(!empty( $queryString)) {
            $dest .= JString::strpos( $dest, '?') !== false ? '&' . $queryString : '?' . $queryString;
          }
          shCheckRedirect( $dest, $this->getFullUrl( $uri));
        }
      } catch (Sh404sefException $e) {
        // if error, just log
        ShlSystem_Log::error( 'sh404sef', '%s::%s::%d: %s', __CLASS__, __METHOD__, __LINE__, ' Database error reading shurl: ' . $e->getMessage());
      }
    }

  }

  protected function _do404( & $uri) {

    if(self::$requestParsed) {
      return array();
    }

    // get config objects
    $pageInfo = & Sh404sefFactory::getPageInfo();
    $sefConfig = Sh404sefFactory::getConfig();

    // store the status
    $pageInfo->httpStatus = 404;

    // request path
    $reqPath = $uri->getPath();
    $reqUrl = $this->getFullUrl( $uri);

    // optionnally log the 404 details
    if ($sefConfig->shLog404Errors && !empty( $reqPath)) {
      try {
        $record = ShlDbHelper::selectObject( '#__sh404sef_urls', '*', array( 'oldurl' => $reqPath));

        if (!empty($record)) {
          // we have, so update counter
          ShlDbHelper::runQuotedQuery( 'update ?? set cpt=(cpt+1) where ?? = ?', array( '#__sh404sef_urls', 'oldurl'), array( $reqPath));
        }
        else {
          // record the 404
          ShlDbHelper::insert( '#__sh404sef_urls', array( 'cpt' => 1, 'rank' => 0, 'oldurl' => $reqPath, 'newurl' => '', 'dateadd' => ShlSystem_Date::getUTCNow('Y-m-d')));
        }
        // add more details about 404 into security log file
        if ($sefConfig->shSecEnableSecurity && $sefConfig->shSecLogAttacks) {
          $logData = array();
          $logData['DATE'] = ShlSystem_Date::getSiteNow( 'Y-m-d');
          $logData['TIME'] = ShlSystem_Date::getSiteNow( 'H:i:s');
          $logData['CAUSE'] = 'Page not found (404)';
          $logData['C-IP'] = empty( $_SERVER['HTTP_CLIENT_IP']) ? '-' : $_SERVER['HTTP_CLIENT_IP'];
          if($_SERVER['REMOTE_ADDR'] != 'localhost' && $_SERVER['REMOTE_ADDR'] != '::1') {
            $name = getHostByAddr( $_SERVER['REMOTE_ADDR']);
          } else {
            $name = '-';
          }
          $logData['NAME'] = $name;
          $logData['USER_AGENT'] = empty($_SERVER['HTTP_USER_AGENT']) ? '-' : $_SERVER['HTTP_USER_AGENT'];
          $logData['REQUEST_METHOD'] = $_SERVER['REQUEST_METHOD'];
          $logData['REQUEST_URI'] = $_SERVER['REQUEST_URI'];
          $logData['COMMENT'] = '';

          shLogToSecFile($logData);
        }
      } catch (Exception $e) {
        ShlSystem_Log::error( 'sh404sef', '%s::%s::%d: %s', __CLASS__, __METHOD__, __LINE__, ' Database error: ' . $e->getMessage());
      }
    }


    // display the error page
    $vars['option'] = 'com_content';
    $vars['view'] = 'article';

    // use provided Itemid
    if (empty($sefConfig->shPageNotFoundItemid)) {
      $shHomePage = JFactory::getApplication()->getMenu()->getDefault();
      $vars['Itemid'] = (empty($shHomePage)) ? null : $shHomePage->id;
    } else {
      $vars['Itemid'] = $sefConfig->shPageNotFoundItemid;
    }

    // user picked our default 404 error page, read its id from DB
    if ($sefConfig->page404 == '0') {
      try {
        $requestedlanguageTag = JFactory::getLanguage()->getTag();
        $languageTag = JRequest::getString(JApplication::getHash('language'), null ,'cookie');
        if(!empty($languageTag)) {
          $vars['lang'] = $languageTag;
        }
        $ids = ShlDbHelper::quoteQuery( 'select ?? from ?? where ?? = ? and ?? = ? and ?? = ? and ?? in ( ?, ?) order by ?? desc'
            , array( 'id', '#__content', 'title', 'catid', 'state', 'language', 'language')
            , array( '__404__', Sh404sefHelperCategories::getSh404sefContentCat()->id, 1, $languageTag, '*')
        )->shlLoadResultArray();
        $id = empty($ids[0]) ? null : $ids[0];
      } catch (Exception $e) {
        ShlSystem_Log::error( 'sh404sef', '%s::%s::%d: %s', __CLASS__, __METHOD__, __LINE__, ' Database error: ' . $e->getMessage());
      }
      if (empty( $id)) {
        JError::raiseError( 404, JText::_( 'Component Not Found' ) . ' ('.$pageInfo->getDefaultFrontLiveSite().'/' . $uri->getPath().')' );
      }
    } else{
      $id = $sefConfig->page404;
    }
    $vars['id'] = $id;
    $vars['lang'] = empty($vars['lang']) ? null : shGetIsoCodeFromName( $vars['lang']);
    $uri = new JURI ($pageInfo->getDefaultFrontLiveSite().'/index.php?'.'option=com_content&view=article&id='.$id.(empty($vars['Itemid'])?'':'&Itemid='.$vars['Itemid'])
        . (empty($vars['lang']) ? '' : ('&lang=' . $vars['lang'])));

    $tmpl = str_replace( '.php', '', $sefConfig->error404SubTemplate);
    if (!empty( $tmpl)) {
      $vars['tmpl'] = $tmpl;
    }

    // and prepare the item for display
    $menus = JFactory::getApplication()->getMenu();
    $menuItem = $menus->getItem( $vars['Itemid']);
    if(!empty( $menuItem)) {
      $menus->setActive( $vars['Itemid']);
    } else {
      $menuItem = $menus->getDefault();
    }

    if( !empty($menuItem->params)) {
      $disableParams = array( 'show_title', 'show_category', 'show_author', 'show_create_date', 'show_modify_date',
          'show_publish_date', 'show_vote', 'show_readmore', 'show_icons', 'show_hits', 'show_feed_link',
          'show_page_heading');
      foreach( $disableParams as $p) {
        $menuItem->params->set( $p, 0);
      }

      //set a custom page title
      $menuItem->params->set( 'page_title', htmlspecialchars( $this->getFullUrl( $uri)));
    }

    // set the menu query array, J! will use that for breadcrumb
    $menuItem->query = $vars;

    // throw 404 http return code, and prepare for page display
    if (!headers_sent()) {
      JResponse::setHeader('status', '404 NOT FOUND');
      // custom error page, faster than loading Joomla 404 page. Not recommended though, why not show
      // your site ?
      if (is_readable(sh404SEF_FRONT_ABS_PATH.'404-Not-Found.tpl.html')) {
        $errorPage = file_get_contents(sh404SEF_FRONT_ABS_PATH.'404-Not-Found.tpl.html');
        if ($errorPage !== false) {
          $errorPage = str_replace('%sh404SEF_404_URL%', ' ('.$reqUrl.')' , $errorPage);
          $errorPage = str_replace('%sh404SEF_404_SITE_URL%', $pageInfo->getDefaultFrontLiveSitegetDefaultFrontLiveSite(), $errorPage);
          $errorPage = str_replace('%sh404SEF_404_SITE_NAME%', JFactory::getApplication()->getCfg('sitename'), $errorPage);
          echo $errorPage;
          die();
        }
      }
    }
    else {
      ShlSystem_Log::error( 'sh404sef', 'Headers already sent before getting control on 404 page - message displayed');
      JError::RaiseError( 500,
          "<br />SH404SEF : headers were already sent when I got control!<br />This is not necessarily a sh404sef error. It may have been caused by any of your extensions or even Joomla itself. If there is no error message above this one, providing more details, then you may look inside the error log file of your web server for an indication of what may be breaking things up.<br />URL=".htmlspecialchars( $reqUrl).'<br />');
    }

    // workaround: disable caching. Joomla! would otherwise just fetch cached version of the page with wrong language and wrong content
    $jConfig = JFactory::getConfig();
    $jConfig->set( 'caching', 0);

    return $vars;
  }

  /**
   *
   * Checks if the path of a request has a trailing slash
   *
   * @param JURI object $uri
   * @return boolean true if has trailing slash
   */
  protected function _hasTrailingSlash() {

    $url = Sh404sefFactory::getPageInfo()->currentSefUrl;
    $rawPath = explode( '?', $url);
    $rawPath = $rawPath[0]; // query string removed, if any
    $rawPath = explode( '#', $rawPath);
    $rawPath = $rawPath[0]; // fragment removed, if any
    $trailingSlash =  JString::substr( $rawPath, -1) == '/';

    return $trailingSlash;
  }

  /**
   *
   * Check a number of conditions, both global and
   * relative to a provided source page uri
   * to decide whether a redirect to another page
   * can take place
   * Will also check configuration settings
   *
   * @param object $uri
   */
  protected function _canRedirectFromNonSef( $uri, $method = '') {

    // if not parsing the initial request, no way we can redirect
    if(self::$requestParsed) {
      return false;
    }

    // what's the requested url?
    $url = Sh404sefFactory::getPageInfo()->currentSefUrl;

    if(strpos( $url, 'index.php?') === false) {
      return false;
    }

    // use framework if no method passed
    if(empty( $method)) {
      $method = JRequest::getMethod();
    }

    // get config
    $sefConfig = Sh404sefFactory::getConfig();

    // get/set data
    $vars = $uri->getQuery( true);
    $canRedirect =  true;

    // first condition: component should not be set to "skip"
    if(!empty( $vars['option'])) {
      $shOption = str_replace('com_', '', $vars['option']);
      if(!empty( $shOption) && in_array( $shOption, $sefConfig->skip)) {
        $canRedirect =  false;
      }
    }

    $canRedirect = $canRedirect && $sefConfig->shRedirectNonSefToSef && $this->_canRedirectFrom( $uri, $method);

    return $canRedirect;
  }

  /**
   * Check various conditions on a request to
   * decide whether it is safe to allow a redirection
   * to another page
   * Does NOT check configuration settings, only look
   * at the passed uri and method parameters
   *
   * @param JURI object $uri object describing the current request, from which we want to redirect
   * @param string $method current request method
   */
  protected function _canRedirectFrom( $uri, $method = '') {

    // use framework if no method passed
    if(empty( $method)) {
      $method = JRequest::getMethod();
    }

    // get the requested url
    $url = $this->getFullUrl( $uri);

    // start with hope
    $canRedirect =  !self::$requestParsed;
    $canRedirect = $canRedirect && !empty($url);
    $canRedirect = $canRedirect && strpos( $url, 'index2.php') === false;
    $canRedirect = $canRedirect && strpos( $url, 'tmpl=component') === false;
    $canRedirect = $canRedirect && strpos( $url, 'no_html=1') === false;
    $canRedirect = $canRedirect && ( empty($_SERVER['HTTP_X_REQUESTED_WITH']) || (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest'));
    $canRedirect = $canRedirect && empty($_POST);
    $canRedirect = $canRedirect && $method != 'POST';

    return $canRedirect;
  }
}

