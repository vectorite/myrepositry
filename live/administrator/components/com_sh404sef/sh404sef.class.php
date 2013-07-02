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
if (!defined('_JEXEC')) die('Direct Access to this location is not allowed.');

jimport('joomla.filesystem.file');

// load a few constants
require_once JPATH_ROOT . '/administrator/components/com_sh404sef/defines.php';

// prevent timezone not set warnings to appear all over,
// especially for PHP 5.3.3+
$serverTimezone = @date_default_timezone_get();
@date_default_timezone_set( $serverTimezone);

// collect main page data
$pageInfo = & Sh404sefFactory::getPageInfo();
$lang = JFactory::getLanguage();
$pageInfo->currentLanguageTag   = $lang->get('tag', 'en-GB');
$shTemp = explode( '-', $pageInfo->currentLanguageTag);
$pageInfo->currentLanguageShortTag   = $shTemp[0] ? $shTemp[0] : 'en';
$pageInfo->setDefaultLiveSite( JString::rtrim( JURI::base(), '/'));


// include sub-libraries
include_once( sh404SEF_ADMIN_ABS_PATH . 'shSEFConfig.class.php');
include_once( sh404SEF_ADMIN_ABS_PATH . 'shSimpleLogger.class.php');
include_once( sh404SEF_ADMIN_ABS_PATH . 'sh_Net_URL.class.php');

// set of utility functions

function shSortURL($string) {
  // URL must be like : index.php?param2=xxx&option=com_ccccc&param1=zzz
  if((substr( $string, 0, 10) !== 'index.php?')) {
    return $string;
  }
  // URL returned will be ! index.php?option=com_ccccc&param1=zzz&param2=xxx
  $ret = '';
  $st = str_replace('&amp;', '&',$string);
  $st = str_replace('index.php', '', $st);
  $st = str_replace('?', '', $st);
  parse_str( $st,$shTmpVars);
  $shVars = shUrlEncodeDeep( $shTmpVars);
  if (count($shVars) > 0) {
    ksort($shVars);  // sort URL array
    $shNewString = '';
    $ret = 'index.php?';
    foreach ($shVars as $key => $value) {
      if (strtolower($key) != 'option') {
        // option is always first parameter
        if( is_array($value) ) {
          foreach($value as $k=>$v) {
            // fix for arrays, thanks doorknob
            $shNewString .= '&'.$key.'[' . $k . ']='.$v;
          }
        } else {
          $shNewString .= '&'.$key.'='.$value;
        }
      } else {
        $ret .= $key.'='.$value;
      }
    }
    $ret .= $ret == 'index.php?' ? JString::ltrim( $shNewString, '&') : $shNewString;
  }
  return $ret;
}

/**
 * Disable caching of Joomfish language selection module
 *
 * Caching would otherwise new SEF urls in non-default language to
 * be created.
 *
 */
function shDisableJFModuleCaching() {

  ShlSystem_Log::debug('sh404sef', 'Using deprecated shDisableJFModuleCaching, removed as not applicable anymore');
  return;
}

// returns found languages, but will check request language ($_GET or $_POST)
// and use that over user lang if it exists
// returns a lnguage code : en, fr, sp
function shDecideRequestLanguage() {

  $reqLang = JRequest::getVar( 'lang', '' );
  if( $reqLang != '' )
    $finalLang = $reqLang;
  else
    $finalLang = shDiscoverUserLanguage();
  return $finalLang;
}

/** The function finds the language which is to be used for the user/session
 *
 * It is possible to choose the language based on the client browsers configuration,
 * the activated language of the configuration and the language a user has choosen in
 * the past. The decision of this order is done in the JoomFish configuration.
 *
 * This is a modified copy of what's available in Joomfish system bot.
 * Returns a language code : en, fr, sp
 */

function shDiscoverUserLanguage() {

  $shCookieLang = shGetCookieLanguage();
  $userLang = empty( $shCookieLang) ? shGetParamUserLanguage() : $shCookieLang;
  return $userLang;
}

// returns language code (en, fr, sp after lookign up Joomfish params
// probably does not work with J 1.6
function shGetParamUserLanguage() {

  $shortCode = Sh404sefFactory::getPageInfo()->currentLanguageShortTag;
  if (!shIsMultilingual())
    return $shortCode;

  $database = ShlDbHelper::getDb();
  // check if param query has previously been processed
  $determitLanguage     = 1;
  $newVisitorAction   = "browser";
  if ($newVisitorAction=="browser" && !empty($_SERVER['HTTP_ACCEPT_LANGUAGE']) ) {
    // no language chooses - assume from browser configuration
    // language negotiation by Kochin Chang, June 16, 2004
    // retrieve active languages from database
    $active_lang = null;
    $activeLanguages = shGetActiveLanguages();
    if( count( $activeLanguages ) == 0 ) {
      return $shortCode;
    }
    foreach ($activeLanguages as $lang) {
      $active_lang[] = $lang->iso;
    }
    // figure out which language to use
    $browserLang = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
    foreach( $browserLang as $lang ) {
      $shortLang = JString::substr( $lang, 0, 2 );
      if( in_array($lang, $active_lang) ) {
        $client_lang = $lang;
        break;
      }
      if ( in_array($shortLang, $active_lang) ) {
        $client_lang = $shortLang;
        break;
      }
    }
    // if language is still blank then use first active language!
    if (empty($client_lang)) {
      $client_lang = shGetDefaultLanguageSef();
    }
  } elseif ($newVisitorAction=="joomfish"){
    // This list is ordered already!
    $activeLanguages = shGetActiveLanguages();
    if( count( $activeLanguages ) == 0 ) {
      return $shortCode;
    }
    else {
      $client_lang = shGetDefaultLanguageSef();
    }

  } else {// otherwise default use site default language
    $activeLanguages = shGetActiveLanguages();
    if( count( $activeLanguages ) == 0 ) {
      return $shortCode;
    }
    foreach ($activeLanguages as $lang) {
      if ($lang->code == Sh404sefFactory::getPageInfo()->currentLanguageTag){
        $client_lang = $lang->iso;
        break;
      }
    }
    // if language is still blank then use first active language!
    if ($client_lang==""){
      $client_lang = shGetDefaultLanguageSef();
    }
  }
  return $client_lang;
}

function shGetCookieLanguage() {

  $jfCookie = JRequest::getVar( 'jfcookie', null, 'COOKIE' );
  if (isset($jfCookie["lang"]) && $jfCookie["lang"] != "") {
    $lang = $jfCookie["lang"];
  } else {
    $lang = '';
  }
  return $lang;
}

/**
 * Check if user session exists. Adapted from Joomla original code
 */
function shLookupSession() {

  $mainframe = JFactory::getApplication();

  return false;  // does not work in 1.5. Not needed anyway, as long as multilingual 303 redirect is not solved

  $database = ShlDbHelper::getDb();
  // initailize session variables
  $session  = new mosSession( $database );
  $option = strval( strtolower( JRequest::getVar( 'option' ) ) );
  $mainframe = new mosMainFrame( $database, $option, '.' );
  // purge expired sessions
  $session->purge('core');  // can't purge as $mainframe is not initialized yet
  // Session Cookie `name`
  // WARNING : I am using the Hack from
  $sessionCookieName  = mosMainFrame::sessionCookieName();
  // Get Session Cookie `value`
  $sessioncookie    = strval( JRequest::getVar( $sessionCookieName, null, 'COOKIE' ) );
  // Session ID / `value`
  $sessionValueCheck  = mosMainFrame::sessionCookieValue( $sessioncookie );
  // Check if existing session exists in db corresponding to Session cookie `value`
  // extra check added in 1.0.8 to test sessioncookie value is of correct length
  $ret = false;
  if ( $sessioncookie && strlen($sessioncookie) == 32 && $sessioncookie != '-' && $session->load($sessionValueCheck) )
    $ret = true;
  unset($mainframe);
  return $ret;
}

// redirect user according to its language preference
function shGuessLanguageAndRedirect( $queryString) {

  if (!sh404SEF_DE_ACTIVATE_LANG_AUTO_REDIRECT
      && shIsMultilingual() == 'joomfish') {
    $cookieLang = shGetCookieLanguage();
    $sessionExists = shLookupSession();
    $reqLang = shGetUrlVar( $queryString, 'lang', '');
    $targetLang = '';
    if (empty($cookieLang)) {
      // this is really first visit (or visitor does not accept cookie)
      $discoveredLang = shGetParamUserLanguage();
      if ( $discoveredLang != $reqLang)
        $targetLang = $discoveredLang;
    }
    if (!empty($targetLang)) {
      // 303 redirect to same URL in preferred language
      $queryString = shSetURLVar( 'index.php?'.$queryString, 'lang', $targetLang);
      $target = JRoute::_( $queryString);
      ShlSystem_Log::debug( 'sh404sef', 'Redirecting (303) to user language |cookie = '.$cookieLang. '|session='.$sessionExists.'|req='.$reqLang.'|target='.$targetLang);
      shRedirect( $target, '', 303);
    }
  }
}

// 1.2.4.t 10/08/2007 12:17:37 return false if not multilingual
function shIsMultilingual() {

  static $shIsMultiLingual = null;

  if (is_null( $shIsMultiLingual)) {
    // joomfish detection:
    $conf = JFactory::getConfig();
    $shIsMultiLingual = !is_null( $conf->get( 'multilingual_support', null)) ? 'joomfish' : false;

    // falang
    if($shIsMultiLingual === false) {
      if(class_exists('plgSystemFalangdriver')) {
        $falangPluginRecord =JPluginHelper::getPlugin( 'system', 'falangdriver');
        if(!empty( $falangPluginRecord)) {
          $dispatcher = JDispatcher::getInstance();
          $falangPlugin = new plgSystemFalangdriver( $dispatcher, (array) ($falangPluginRecord));
          $shIsMultiLingual = $falangPlugin->isFalangDriverActive() ? 'falang' : false;
        }
      }
    }

    // joomla builtin
    if(empty( $shIsMultiLingual)) {
      jimport('joomla.language.helper');
      $languages	= JLanguageHelper::getLanguages();
      if(count($languages) > 1) {
        $shIsMultiLingual = 'joomla';
      }
    }
  }
  return $shIsMultiLingual;

}

// 1.2.4.t 10/08/2007 12:17:37 return true if param is default language
function shIsDefaultLang( $langName) {

  return ($langName == shGetDefaultLang() || $langName == '*');
}

// 1.2.4.t 10/08/2007 12:17:37 return true if param is default language
function shGetDefaultLang() {

  $type = shIsMultilingual();
  switch ($type) {
    case 'joomfish':
      $conf =JFactory::getConfig();
      $shDefaultLang = $conf->get( 'config.defaultlang');
      $shDefaultLang = empty( $shDefaultLang) ? 'en-GB' : $shDefaultLang; // sometimes defaultlang can be set to 'empty' !
      break;
    case false:
    case 'joomla':
    default:
      jimport('joomla.application.component.helper');
      $params = JComponentHelper::getParams('com_languages');
      $shDefaultLang = $params->get('site', 'en-GB');
      break;
  }
  return $shDefaultLang;
}

function shGetDefaultLanguageSef() {

  static $sef = '';

  if(empty( $sef)) {
    $code = shGetDefaultLang();
    $sef = shGetIsoCodeFromName($code);
  }

  return $sef;
}

/**
 * Get list of front-end available langauges
 *
 * @return unknown
 */
function shGetFrontEndActiveLanguages() {

  static $shLangs = null;

  if(is_null( $shLangs)) {
    $shLangs = array();
    jimport('joomla.language.helper');
    $languages	= JLanguageHelper::getLanguages();
    if(!empty($languages)) {
      foreach($languages as $i => &$language) {
        // Do not display language without frontend UI
        if (!JLanguage::exists($language->lang_code)) {
          unset($languages[$i]);
        }
      }
      foreach($languages as $language) {
        $shLang = new StdClass();
        $shLang->iso = $language->sef;
        if(empty($shLang->iso)) {
          $shLang->iso = substr( $language->lang_code, 0, 2);
        }
        $shLang->code = $language->lang_code;
        $shLangs[] = $shLang;
      }
    }
  }

  return $shLangs;

}


// utility function to return list of available languages / isolate from JFish/Joomla compat issues
function shGetActiveLanguages() {

  $mainframe = JFactory::getApplication();

  static $shActiveLanguages = null;  // cache this, to reduce DB queries
  if (!is_null($shActiveLanguages))
    return $shActiveLanguages;

  $shKind = shIsMultilingual();
  switch ($shKind) {
    case 'joomfish':
      $languages = JoomFishManager::getActiveLanguages();
      if (!empty($languages)) {
        foreach ($languages as $language) {
          $shLang = new stdClass();
          $shLang->code = $language->code;
          $shLang->iso = $language->shortcode;
          $shActiveLanguages[] = $shLang;
        }
      } else $shKind = '';
      break;
    case 'falang':
    case 'joomla':
      jimport('joomla.language.helper');
      $languages	= JLanguageHelper::getLanguages();
      if (!empty($languages)) {
        foreach ($languages as $language) {
          $shLang = new stdClass();
          $shLang->code = $language->lang_code;
          $shLang->iso = $language->sef;
          if(empty($shLang->iso)) {
            $shLang->iso = substr( $language->lang_code, 0, 2);
          }
          $shActiveLanguages[] = $shLang;
        }
      } else $shKind = '';
      break;
  }
  if (empty($shKind)) {
    // not multilingual
    $shActiveLanguages = shGetFrontEndActiveLanguages();
  }
  return $shActiveLanguages;
}

function shAdjustToRewriteMode( $url) {
  return $url;
}

function shFinalizeURL( $url) {
  // V 1.2.4.s hack to workaround Virtuemart/SearchEngines issue with cookie check
  // V 1.2.4.t fixed bug, was checking for vmcchk instead of vmchk
  if (shIsSearchEngine() && (strpos( $url, 'vmchk') !== false)) {
    $url = str_replace('vmchk/', '', $url);  // remove check,
    //cookie will be forced if user agent is searchengine
  }
  $url = str_replace('&amp;', '&', $url);  // when Joomla wil turn that into &amp; we are sur we won't have &amp;amp;
  return $url;
}

// V 1.2.4.p compatibility function with SEFAdvance
function sefencode( $string) {
  return titleToLocation( $string);
}

function titleToLocation(&$title)
{
  $sefConfig = Sh404sefFactory::getConfig();
  $title = JString::trim($title);
  $debug = 0;
  if ($debug) $t[] = $title;
  $shRep = $sefConfig->shGetReplacements();
  if (!empty($shRep)) {
    foreach( $shRep as $from => $to) {
      $title = str_replace( $from, $to, $title);
    }
  }
  if ($debug) $t[] = $title;
  $shStrip = $sefConfig->shGetStripCharList();
  if (!empty($shStrip))
    $title = str_replace( $shStrip, '', $title);
  if ($debug) $t[] = $title;
  // remove spaces
  $title = ShlSystem_Strings::pr( '/[\s]+/iu', $sefConfig->replacement, $title);
  if ($debug) $t[] = $title;
  $title = str_replace('\'', $sefConfig->replacement, $title);
  $title = str_replace('"', $sefConfig->replacement, $title);
  // V x strip # as it breaks anchor management
  $title = str_replace('#', $sefConfig->replacement, $title);
  // remove question marks
  $title = str_replace('?', $sefConfig->replacement, $title);
  if ($debug) $t[] = $title;
  $title = str_replace('\\', $sefConfig->replacement, $title);
  if ($debug) $t[] = $title;
  // remove duplicate replacement chars
  if (!empty($sefConfig->replacement))  // V x protect/allow empty
    $title = ShlSystem_Strings::pr('/'.preg_quote($sefConfig->replacement).'{2,}/u', $sefConfig->replacement, $title);
  if ($debug) $t[] = $title;
  $title = JString::trim( $title, str_replace('|', '', $sefConfig->friendlytrim));  // V 1.2.4.t add SEF URL trimming of user set characters
  $title = $sefConfig->LowerCase ? JString::strtolower($title) : $title;  // V w 27/08/2007 13:11:48
  if ($debug) $t[] = $title;
  if ($debug && strpos($t[0], '\'') !== false) {
    var_dump($t);
    die();
  }
  return $title;
}

// V x utility 01/09/2007 22:18:55 function to remove mosmsg var from url
function shCleanUpMosMsg( $string) {
  return ShlSystem_Strings::pr( '/(&|\?)mosmsg=[^&]*/i', '', $string);
}

// V x utility  function to remove a variable from an URL
function shCleanUpVar( $string, $var) {
  return ShlSystem_Strings::pr( '/(&|\?)'.preg_quote($var, '/').'=[^&]*/iu', '', $string);
}

// V x utility 01/09/2007 22:18:55 function to return mosmsg var from url
function shGetMosMsg( $string) {
  $matches = array();
  $result = preg_match( '/(&|\?)mosmsg=[^&]*/i', $string, $matches);
  if (!empty($matches))
    return JString::trim( $matches[0], '&?');
  else return '';
}

// V x utility function to return lang var from url
function shGetURLLang( $string) {
  $matches = array();
  $string = str_replace('&amp;', '&', $string); // normalize
  $result = preg_match( '/(&|\?)lang=[^&]*/i', $string, $matches);
  if (!empty($matches)) {
    $result = JString::trim( $matches[0], '&?');
    $result = str_replace('lang=', '', $result);
    return shGetNameFromIsoCode($result);
  }
  else return '';
}

// V x utility function to return a var from url
function shGetURLVar( $string, $var, $default = '') {

  if( strpos( $string, 'index.php?') === 0) {
    $string = substr( $string, 10);
  }
  $string = str_replace('&amp;', '&', $string); // normalize
  $string = str_replace('&amp;', '&', $string); // normalize #2
  $vars = array();
  parse_str( $string, $vars);
  $value = isset($vars[$var]) ? $vars[$var] : $default;

  return $value;
}

// V x utility function to set  a var in an url
function shSetURLVar( $string, $var, $value, $canBeEmpty = false) {
  if (empty( $string) || empty($var)) return $string;
  if ( !$canBeEmpty && empty( $value)) {
    return $string;
  }
  $string = str_replace('&amp;', '&', $string); // normalize
  $exp = '/(&|\?)'.preg_quote($var, '/').'=[^&]*/iu';
  $result = preg_match( $exp, $string);
  if ($result)  // var already in URL
    $result = preg_replace( $exp, '$1'.$var.'='.$value, $string);
  else {  // var does not exist in URL
    $result = $string.(strpos( $string, '?') !== false ? '&':'?').$var.'='.$value;
    $result = shSortURL($result);
  }
  return $result;
}

// V 1.2.4.q utility function to clean language and pagination info from url
function shCleanUpPag( $string) {
  $shTempString = preg_replace( '/(&|\?)limit=[^&]*/i', '', $string);
  $shTempString = preg_replace( '/(&|\?)limitstart=[^&]*/i', '', $shTempString);
  return $shTempString;
}

// V 1.2.4.t utility function to clean language from url
function shCleanUpLang( $string) {
  return preg_replace( '/(&|\?)lang=[a-zA-Z]{2}/i', '', $string);
}

// V 1.2.4.q utility function to clean language and pagination info from url
function shCleanUpLangAndPag( $string) {
  $shTempString = shCleanUpLang( $string);
  $shTempString = shCleanUpPag($shTempString);
  return $shTempString;
}

// V 1.2.4.t utility function to clean anchor from url
function shCleanUpAnchor( $string) {
  $bits = explode('#', $string);
  return $bits[0];
}


// V 1.2.4.t
function shIncludeLanguageFile() {
}


function shGETGarbageCollect() {
  // V 1.2.4.m moved to main component from plugins
  // builds up a string using all remaining GET parameters, to be appended to the URL without any sef transformation
  // those variables passed litterally must be removed from $string as well, so that they are not stored in DB
  global $shGETVars;
  $sefConfig = Sh404sefFactory::getConfig();
  if (!$sefConfig->shAppendRemainingGETVars || empty($shGETVars)) return '';
  $ret = '';
  ksort($shGETVars);
  foreach ($shGETVars as $param => $value) {
    if( is_array($value) ) {
      foreach($value as $k=>$v) {
        $ret .= '&'.$param.'['.$k.']='.$v;
      }
    } else {
      $ret .= '&'.$param.'='.$value;
    }

  }
  return $ret;
}

function shRebuildNonSefString( $string) {
  // V 1.2.4.m moved to main component from plugins
  // rebuild a non-sef string, removing all GET vars that were not turned into SEF
  // as we do not want to store them in DB

  global $shRebuildNonSef;
  $sefConfig = & Sh404sefFactory::getConfig();
  if (!$sefConfig->shAppendRemainingGETVars || empty($shRebuildNonSef)) return $string;
  $shNewString = '';
  if (!empty($shRebuildNonSef)) {
    foreach ($shRebuildNonSef as $param) {
      // need to sort, and still place option in first pos.
      if (strpos($param, 'sh404SEF_title=') !== false)
        $param = str_replace('sh404SEF_title=', 'title=', $param);
      $shNewString .= $param;
    }
    $ret = shSortUrl('index.php?'.JString::ltrim( $shNewString, '&'));
  }
  return $ret;
}

function shRemoveFromGETVarsList( $paramName) {
  global $shGETVars, $shRebuildNonSef;

  $sefConfig = Sh404sefFactory::getConfig();
  if (!$sefConfig->shAppendRemainingGETVars) return null;
  if (!empty($paramName)) {
    if (isset($shGETVars[$paramName])) {
      $shValue = $shGETVars[$paramName];
      if( is_array($shValue) ) {
        // array handling, fix provided by VinhCV
        foreach ($shValue as $value){
          $shRebuildNonSef[] = '&'.$paramName.'[]='.$value;
        }
      } else {
        $shRebuildNonSef[] = '&'.$paramName.'='.$shValue;
      }  // build up a non-sef string with the GET vars used to
      // build the SEF string. This string will be the one stored in db instead of
      // the full, original one
      unset( $shGETVars[@$paramName]);
    }
  }
}

function shAddToGETVarsList( $paramName, $paramValue) {
  // V 1.2.4.m
  global $shGETVars, $shRebuildNonSef;
  if (empty( $paramName)) return;
  $shGETVars[$paramName] = $paramValue;
  // check and remove from $shRebuildNonSef, in case this param was previously added to the list, using shRemoveFromGETVarsList
  if (!empty($shRebuildNonSef)) {
    $indexFound = -1;
    $index = -1;
    foreach($shRebuildNonSef as $item) {
      $index++;
      if ($item == '&'.$paramName.'='.$paramValue) {
        $indexFound = $index;
        break;
      }
    }
    if ($indexFound > -1) {
      unset( $shRebuildNonSef[$indexFound]);
    }
  }
}

function shComputeItemidString( $nonSefUrl, &$title, $shLangName) {


  $sefConfig = & shRouter::shGetConfig();

  $shItemidString = '';

  $shHomePageFlag = shIsHomepage ($nonSefUrl);

  if (!$shHomePageFlag) {
    // we may have found that this is homepage, so we msut return an empty string
    // do something about that Itemid thing
    if (!preg_match( '/Itemid=[0-9]+/i', $nonSefUrl)) {
      // if no Itemid in non-sef URL
      // V 1.2.4.t moved back here
      $shCurrentItemid = JRequest::getInt( 'Itemid');
      if ($sefConfig->shInsertGlobalItemidIfNone && !empty($shCurrentItemid)) {
        $nonSefUrl .= '&Itemid='.$shCurrentItemid; ;  // append current Itemid
        $Itemid = $shCurrentItemid;
        shAddToGETVarsList('Itemid', $Itemid); // V 1.2.4.m
      }

      $shItemidString = '';
      if ($sefConfig->shAlwaysInsertItemid && (!empty($Itemid) || !empty($shCurrentItemid))) {
        $shItemidString = JText::_('COM_SH404SEF_ALWAYS_INSERT_ITEMID_PREFIX').$sefConfig->replacement
        .(empty($Itemid)? $shCurrentItemid :$Itemid);
      }
    } else {  // if Itemid in non-sef URL
      $shItemidString = $sefConfig->shAlwaysInsertItemid ?
      JText::_('COM_SH404SEF_ALWAYS_INSERT_ITEMID_PREFIX').$sefConfig->replacement . shGetURLVar($nonSefUrl, 'Itemid')
      : '';
    }

  }

  return $shItemidString;
}

function shFinalizePlugin( $string, $title, &$shAppendString, $shItemidString,
    $limit, $limitstart, $shLangName, $showall = null, $suppressPagination = false) {
  // V 1.2.4.s
  global $shGETVars;
  if(empty($shItemidString)) {
    $shItemidString = shComputeItemidString($string, $title, $shLangName);
  }
  if (!empty($shItemidString)) {
    $title[] = $shItemidString; // V 1.2.4.m
  }
  // stitch back additional parameters, not sef-ified
  $shAppendString .= shGETGarbageCollect();  // add automatically all GET variables that had not been used already
  if (!empty($shAppendString))
    $shAppendString = '?'.JString::ltrim( $shAppendString, '&'); // don't add to $string, otherwise it will be stored in the DB
  return sef_404::sefGetLocation( shRebuildNonSefString( $string), $title, null,
      (isset($limit) ? @$limit : null), (isset($limitstart) ? @$limitstart : null),
      (isset($shLangName) ? @$shLangName : null),
      (isset($showall) ? @$showall : null)
      , $suppressPagination
  );
}

function shInitializePlugin($lang, &$shLangName, &$shLangIso, $option) {

  $conf =JFactory::getConfig();
  $configDefaultLanguage = $conf->get('config.language');

  $shLangName = empty($lang) ? Sh404sefFactory::getPageInfo()->currentLanguageTag : shGetNameFromIsoCode( $lang);
  $shLangIso = (shTranslateUrl($option, $shLangName)) ?
  (isset($lang) ? $lang : shGetIsoCodeFromName( Sh404sefFactory::getPageInfo()->currentLanguageTag))
  : (isset($configDefaultLanguage) ? shGetIsoCodeFromName($configDefaultLanguage) : shGetIsoCodeFromName( Sh404sefFactory::getPageInfo()->currentLanguageTag));
  if (strpos($shLangIso, '_') !== false) {
    //11/08/2007 14:30:16 mambo compat
    $shTemp = explode( '_', $shLangIso);
    $shLangIso = $shTemp[0];
  }

  // reset pageid creation : the plugin must turn it on by itself
  shMustCreatePageId( 'set', false);

  // added protection : do not SEF if component is not installed. Do not attempt to build SEF URL
  // if component is not installed, or else plugin may try to read from comp DB tables. This will cause DB table names
  // to be displayed
  return !sh404SEF_CHECK_COMP_IS_INSTALLED
  || ( sh404SEF_CHECK_COMP_IS_INSTALLED &&
      shFileExists(sh404SEF_ABS_PATH.'components/'.$option.'/'.str_replace('com_', '',$option).'.php'));
}

function shLoadPluginLanguage ( $pluginName, $language, $defaultString, $path = '') {
  // V 1.2.4.m
  global $sh_LANG;

  // load the Language File
  $path = JString::rtrim( $path, DIRECTORY_SEPARATOR);
  $path = JString::rtrim( $path, '/') . '/';
  $path = $path == '/' ? sh404SEF_ADMIN_ABS_PATH .'language/plugins/' : $path;
  if (shFileExists( $path .$pluginName.'.php' )) {
    include_once( $path . $pluginName.'.php' );
  }
  else JError::RaiseWarning( 500, 'sh404SEF - missing language file for plugin '.$pluginName.'.');

  if (!isset($sh_LANG[$language][$defaultString]))
    return 'en';
  else return $language;
}

function shInsertIsoCodeInUrl($compName, $shLang = null) {
  // V 1.2.4.m

  $sefConfig = & Sh404sefFactory::getConfig();

  $shLang = empty($shLang) ? Sh404sefFactory::getPageInfo()->currentLanguageTag : $shLang;  // V 1.2.4.q
  if (empty($compName) || !$sefConfig->shInsertLanguageCode  // if no compname or global param is off
      || $sefConfig->shLangInsertCodeList[$shLang] == 2  // set to not insertcode
      || ( $sefConfig->shLangInsertCodeList[$shLang] == 0 && shGetDefaultlang() == $shLang) // or set to default
  )  // but this is default language
    return false;
  $compName = str_replace('com_', '', $compName);
  return !in_array($compName, $sefConfig->notInsertIsoCodeList);
}

function shTranslateUrl ($compName, $shLang = null) {
  // V 1.2.4.m  // V 1.2.4.q added $shLang param

  // temporarily disable ability to not translate
  // until 1.7+ multi-lingual options are sorted out
  return true;

  $sefConfig = & Sh404sefFactory::getConfig();

  $shLang = empty($shLang) ? Sh404sefFactory::getPageInfo()->currentLanguageTag : $shLang;
  if (empty($compName) || !$sefConfig->shTranslateURL
      || $sefConfig->shLangTranslateList[$shLang] == 2 ) // set to not translate
    return false;
  $compName = str_replace('com_', '', $compName);
  $result = !in_array($compName, $sefConfig->notTranslateURLList);
  return $result;
}

// V 1.2.4.q returns true if current page is home page.
function shIsCurrentPageHome() {

  $currentPage = shSortUrl( ShlSystem_Strings::pr( '/(&|\?)lang=[a-zA-Z]{2,3}/iu', '', empty($_SERVER['QUERY_STRING']) ? '' : $_SERVER['QUERY_STRING'])); // V 1.2.4.t
  $currentPage = JString::ltrim( str_replace('index.php', '', $currentPage), '/');
  $currentPage = JString::ltrim( $currentPage, '?');
  $shHomePage = ShlSystem_Strings::pr( '/(&|\?)lang=[a-zA-Z]{2,3}/iu', '', Sh404sefFactory::getPageInfo()->homeLink);
  $shHomePage = JString::ltrim( str_replace('index.php', '', $shHomePage), '/');
  $shHomePage = JString::ltrim( $shHomePage, '?');
  return  $currentPage == $shHomePage;
}

function shUrlEncode( $path) {
  $ret = $path;
  if (!empty($path)) {
    $bits = explode('/', $path);
    $enc = array();
    if (count($bits)) {
      foreach ($bits as $key=>$value) {
        $enc[$key] = rawurlencode($value);
      }
      $ret = implode($enc,'/');
    }
  }
  return $ret;
}
function shUrlDecode( $path) {
  $ret = $path;
  if (!empty($path)) {
    $bits = explode('/', $path);
    $dec = array();
    if (count($bits)) {
      foreach ($bits as $key=>$value) {
        $dec[$key] = rawurldecode($value);
      }
      $ret = implode($dec,'/');
    }
  }
  return $ret;
}

// returns default items per page from menu items params. menu item selected by its id taken from a URL
function shGetDefaultDisplayNumFromURL($url, $includeBlogLinks = false) {

  $menuItemid = shGetURLVar($url, 'Itemid');
  return shGetDefaultDisplayNum($menuItemid, $url, $fromSession = true, $includeBlogLinks);
}

/**
 * Compared to shGetDefaultDisplayNum, this function only reads default
 * num items per page out of configuration and url requested, regardless of values
 * stored in session
 *
 * @param $url
 * @return unknown_type
 */
function shGetDefaultDisplayNumFromConfig( $url, $includeBlogLinks = false) {

  $menuItemid = shGetURLVar($url, 'Itemid');
  return shGetDefaultDisplayNum($menuItemid, $url, $fromSession = false, $includeBlogLinks);

}


// returns default items per page from menu items params. menu item selected by its id taken from a URL
function shGetDefaultDisplayNum($menuItemid, $url, $fromSession = false, $includeBlogLinks = false) {

  $app = JFactory::getApplication();

  // default value is general configuration list length param
  $ret = $app->getCfg( 'list_limit', 10 );

  // get elements of the url
  $option = shGetURLVar($url, 'option');
  $layout = shGetURLVar( $url, 'layout');
  if (empty( $layout)) {
    $layout = 'default';
  }
  $view = shGetURLVar( $url, 'view');

  // if there is a menu item, we can try read more params
  if (!empty($menuItemid)) {

    // itemid, try read params from the menu item
    $menu = JFactory::getApplication()->getMenu();
    $menuItem = $menu->getItem($menuItemid);  // load menu item from DB
    if (empty($menuItem)) return $ret;  // if none, default
    jimport( 'joomla.html.parameter');

    // Load the parameters. Merge Global and Menu Item params into new object
    $currentOption = JRequest::getCmd('option');
    $params = new JRegistry( $menuItem->params );  // get params from menu item
    if(!empty($currentOption)) {
      $appParams = $app->getParams();
      $params->merge( $appParams);
    }

    // layout = blog and frontpage
    if ( ($option =='com_content' && $layout == 'blog')
        || ($option == 'com_content' && $view == 'featured')) {
      $num_leading_articles = $params->get('num_leading_articles');
      $num_intro_articles = $params->get('num_intro_articles');
      //adjust limit and listLimit for page calculation as blog views include
      //# of links in the limit value, while it should not be included for
      // page number calculation
      $num_links = $includeBlogLinks ? $params->get('num_links') : 0;

      $ret = $num_leading_articles + $num_intro_articles + $num_links;  // calculate how many items on a page
      return $ret;
    }

    // elements with a display_num parameter
    $displayNum = intval($params->get('display_num'));
    $ret = !empty( $displayNum) ? $displayNum : $ret;
  }

  if ($fromSession) {
    // now handle special cases
    if ( $option =='com_content' && $layout != 'blog' && ($view == 'category' || $view == 'section')) {
      $limit = $app->getUserStateFromRequest( 'com_content.sh.' . $view . '.' . $layout . '.limit', 'limit', null);
      if (!is_null($limit)) {
        return $limit;
      }
    }

    if ($option == 'com_contact') {
      $limit = $app->getUserState( $option . '.' . $view. '.limit');
      if (!is_null($limit)) {
        return $limit;
      }
    }

    if ($option == 'com_weblinks') {
      $limit = $app->getUserState( $option . '.limit');
      if (!is_null($limit)) {
        return $limit;
      }
    }
  }

  // return calculated value
  return $ret;
}

function getSefUrlFromDatabase($url, &$sefString) {

  try {
    $result = ShlDbHelper::selectObject( '#__sh404sef_urls', array( 'oldurl', 'dateadd'), array( 'newurl' => $url));

    if (!empty($result)) {
      $sefString = $result->oldurl;
      if (empty($result->oldurl)) {
        return sh404SEF_URLTYPE_404;
      }
      return $result->dateadd == '0000-00-00' ? sh404SEF_URLTYPE_AUTO : sh404SEF_URLTYPE_CUSTOM;
    }

  } catch (Exception $e) {
    ShlSystem_Log::error( 'sh404sef', '%s::%s::%d: %s', __CLASS__, __METHOD__, __LINE__, $e->getMessage());
  }

  return sh404SEF_URLTYPE_NONE;
}

// V 1.2.4.t check both cache and DB
function shGetSefURLFromCacheOrDB($string, &$sefString) {
  $sefConfig = Sh404sefFactory::getConfig();
  if (empty($string)) return sh404SEF_URLTYPE_NONE;
  $sefString = '';
  $urlType = sh404SEF_URLTYPE_NONE;
  if ($sefConfig->shUseURLCache)
    $urlType = Sh404sefHelperCache::getSefUrlFromCache($string, $sefString);
  // Check if the url is already saved in the database.
  if ($urlType == sh404SEF_URLTYPE_NONE) {
    $urlType = getSefUrlFromDatabase($string, $sefString);
    if ($urlType == sh404SEF_URLTYPE_NONE || $urlType == sh404SEF_URLTYPE_404)
      return $urlType;
    else {
      if ($sefConfig->shUseURLCache) {
        Sh404sefHelperCache::addSefUrlToCache( $string, $sefString, $urlType);
      }
    }
  }
  return $urlType;
}

// add URL to DB and cache. URL must no exists, this is insert, not update
function shAddSefUrlToDBAndCache( $nonSefUrl, $sefString, $rank, $urlType) {

  $db = ShlDbHelper::getDb();
  $sefString = JString::ltrim( $sefString, '/'); // V 1.2.4.t just in case you forgot to remove leading slash
  switch ($urlType) {
    case sh404SEF_URLTYPE_AUTO :
      $dateAdd = '0000-00-00';
      break;
    case sh404SEF_URLTYPE_CUSTOM :
      $dateAdd = date("Y-m-d");
      break;
    case sh404SEF_URLTYPE_NONE :
      return null;
      break;
  }

  try {

    $query = '';
    if ($urlType == sh404SEF_URLTYPE_AUTO) {
      $result = ShlDbHelper::quoteQuery(
          'select ??, ?? from ?? where ?? = ? and (?? = ? or ?? = ?)'
          , array( 'id', 'newurl', '#__sh404sef_urls', 'oldurl', 'newurl', 'newurl')
          , array( $sefString, '', addslashes( urldecode( $nonSefUrl)))
      )->shlLoadObject();

      if (!empty($result)) {
        // sef urls was found either as a 404 or as already existing, with also the same non-sef
        if ($result->newurl == $nonSefUrl) {
          // url already in db, nothing to do
          ShlSystem_Log::debug( 'sh404sef', 'url already in db, nothing to do');
          return true;
        }
        ShlDbHelper::update( '#__sh404sef_urls'
            , array( 'newurl' => addslashes(urldecode($nonSefUrl)), 'rank' => $rank, 'dateadd' => $dateAdd)
            , array( 'oldurl' => $sefString));

      } else {
        // another option: sef exists, but with another non-sef: that's a duplicate
        // need to check that
        $result = ShlDbHelper::selectObject( '#__sh404sef_urls',
            array( 'id', 'newurl', 'rank')
            , $db->quoteName( 'oldurl') . ' = ? and ' . $db->quoteName( 'newurl') . ' <> ?'
            , array( $sefString, addslashes(urldecode($nonSefUrl)))
            , array( 'rank' => 'desc'));

        if (!empty( $result)) {
          // we found at least one identical SEF url, with another non-sef. Mark the new one as duplicate of the old one
          $rank = $result->rank + 1;
        }

        ShlDbHelper::insert( '#__sh404sef_urls', array( 'oldurl' => $sefString, 'newurl' => $nonSefUrl, 'rank' => $rank, 'dateadd' => $dateAdd));

      }
    }

    // store new sef/non-sef pair in memory cache
    Sh404sefHelperCache::addSefUrlToCache( $nonSefUrl, $sefString, $urlType);

    // create shURL : get a shURL model, and ask url creation
    $model = ShlMvcModel_Base::getInstance( 'pageids', 'Sh404sefModel');
    $model->createPageId( $sefString, $nonSefUrl);

  } catch (Exception $e) {
    ShlSystem_Log::error( 'sh404sef', '%s::%s::%d: %s', __CLASS__, __METHOD__, __LINE__, $e->getMessage());
  }
}

/**
 * Returns true if current sef url being created can have a shURL
 * Can be set from within a plugin, otherwise default to false
 * Reset to false upon each creation of a new sef url in shInitializePlugin()
 *
 * @param unknown_type $action
 * @param unknown_type $value
 * @return unknown
 */
function shMustCreatePageId( $action = 'get', $value = false) {

  jimport('joomla.application.component.model');
  $model = ShlMvcModel_Base::getInstance( 'pageids', 'Sh404sefModel');
  $mustCreate = $model->mustCreatePageId( $action, $value);

  return $mustCreate;
}

// V 1.2.4.t build up a string with a page number
function shBuildPageNumberString( $pagenum) {
  $sefConfig = Sh404sefFactory::getConfig();

  if ($sefConfig->pagetext && (false !== strpos($sefConfig->pagetext, '%s'))){
    return str_replace('%s', $pagenum, $sefConfig->pagetext);
  } else {
    return $pagenum;
  }
}

function shReadFile($shFileName, $asString = false){
  $ret = array();
  if (is_readable($shFileName)) {
    $shFile = fOpen($shFileName, 'r');
    do {
      $shRead = fgets($shFile,1024);
      if (!empty($shRead) && JString::substr($shRead, 0, 1) != '#') $ret[] = JString::trim(stripslashes($shRead));
    }
    while (!feof($shFile));
    fclose($shFile);
  }
  if ($asString)
    $ret = implode("\n", $ret);
  return $ret;
}

function shSaveFile($shFileName, $fileData){
  if (empty($shFileName)) return;
  $fileIsThere = file_exists($shFileName);
  if (!$fileIsThere || ($fileIsThere && is_writable($shFileName))) {
    if (is_array($fileData)) {
      $fileData = implode("\n",$fileData); //make sure we write a string
    }
    $fileData = empty($fileData) ? '':$fileData;
    JFile::Write( $shFileName, $fileData);
  }
}

// shumisha utility function to obtain iso code from language name
function shGetIsoCodeFromName($langName) {

  static $shIsoCodeCache = null;

  if (!isset( $shIsoCodeCache[$langName])) {
    $type = shIsMultilingual();
    if ($type !== false) {
      $languages = Sh404sefHelperGeneral::getInstalledLanguagesList();
      foreach ($languages as $language) {
        $shIsoCodeCache[$language->code] = empty($language->shortcode) ? $language->iso:$language->shortcode;
      }
    } else { // no joomfish, so it has to be default language
      $langName = Sh404sefFactory::getPageInfo()->currentLanguageTag;
      $shIsoCodeCache[Sh404sefFactory::getPageInfo()->currentLanguageTag] = Sh404sefFactory::getPageInfo()->currentLanguageShortTag;
    }
  }
  return empty($shIsoCodeCache[$langName]) ? 'en' : $shIsoCodeCache[$langName];
}

// shumisha utility function to obtain language name from iso code
function shGetNameFromIsoCode($langCode) {

  static $shLangNameCache = null;
  global $shLangNameCache;

  if (empty( $shLangNameCache)) {
    $type = shIsMultilingual();
    if ($type !== false) {
      $languages = Sh404sefHelperGeneral::getInstalledLanguagesList();
      foreach ($languages as $language) {
        $jfIsoCode = empty($language->shortcode) ? $language->iso:$language->shortcode;
        $shLangNameCache[$jfIsoCode] = $language->code;
      }
      return empty($shLangNameCache[$langCode]) ? Sh404sefFactory::getPageInfo()->currentLanguageTag : $shLangNameCache[$langCode];
    } else { // no joomfish, so it has to be default language
      return Sh404sefFactory::getPageInfo()->currentLanguageTag;
    }
  } else return empty($shLangNameCache[$langCode]) ? Sh404sefFactory::getPageInfo()->currentLanguageTag : $shLangNameCache[$langCode];
}

// returns prefix for $option component, as per user settings
function shGetComponentPrefix( $option) {

  if (empty($option)) return '';
  $sefConfig = Sh404sefFactory::getConfig();
  $option = str_replace('com_', '', $option);
  $prefix = '';
  $prefix = empty($sefConfig->defaultComponentStringList[@$option]) ?
  '':$sefConfig->defaultComponentStringList[@$option];
  return $prefix;
}

function shRedirect( $url, $msg='', $redirKind = '301', $msgType='message' ) {

  $mainframe = JFactory::getApplication();
  $sefConfig = & Sh404sefFactory::getConfig();

  // specific filters
  if (class_exists('InputFilter')) {
    $iFilter = new InputFilter();
    $url = $iFilter->process( $url );
    if (!empty($msg)) {
      $msg = $iFilter->process( $msg );
    }

    if ($iFilter->badAttributeValue( array( 'href', $url ))) {
      $url = Sh404sefFactory::getPageInfo()->getDefaultFrontLiveSite();
    }
  }

  // If the message exists, enqueue it
  if (JString::trim( $msg )) {
    $mainframe->enqueueMessage($msg, $msgType);
  }

  // Persist messages if they exist
  $queue = $mainframe->getMessageQueue();
  if (count($queue)) {
    $session = JFactory::getSession();
    $session->set('application.queue', $queue);
  }

  $document = JFactory::getDocument();
  @ob_end_clean(); // clear output buffer
  if (headers_sent()) {
    echo '<html><head><meta http-equiv="content-type" content="text/html; charset='.$document->getCharset().'" /><script>document.location.href=\''.$url.'\';</script></head><body></body></html>';
  } else {
    switch ($redirKind) {
      case '302':
        $redirHeader ='HTTP/1.1 302 Moved Temporarily';
        break;
      case '303':
        $redirHeader ='HTTP/1.1 303 See Other';
        break;
      default:
        $redirHeader = 'HTTP/1.1 301 Moved Permanently';
        break;
    }
    header( 'Cache-Control: no-cache');  // prevent Firefox5+ and IE9+ to consider this a cacheable redirect
    header( $redirHeader );
    header( 'Location: ' . $url );
    header( 'Content-Type: text/html; charset='.$document->getCharset());
  }
  $mainframe->close();
}

function shCloseLogFile() {

  global $shLogger;
  if (!empty($shLogger)) {
    $shLogger->log('Closing log file at shutdown'."\n\n");
    if (!empty($shLogger->logFile))
      fClose( $shLogger->logFile);
  }
}

function _log($text, $data = '') {

  ShlSystem_Log::debug( 'sh404sef', '_log is deprecated, used shlSystem_Log instead');
}

// J 1.5 : will put unused vars in uri query
function shRebuildVars( $appendString, &$uri) {
  if (empty( $uri)) return;
  $string = empty($appendString) ? '' : JString::ltrim($appendString, '?');
  $uri->setQuery($string);
}

function shFileExists( $fileName) {
  static $files = array();

  $fileMD5 = md5( $fileName);
  if (!isset($files[$fileMD5])) {
    $files[$fileMD5] = file_exists( $fileName);
  }
  return $files[$fileMD5];
}

function shGetMenuItemSsl( $id) {

  if(empty( $id)) {
    return 'ignore';
  }
  $secure = 0;
  $app = JFactory::getApplication();
  $menu = $app->getMenu();
  if(!empty($menu)) {
    $params = $menu->getParams( $id);
    $secure = $params->get('secure');
  }
  switch( $secure) {
    case -1:
      $secure = 'no';
      break;
    case 1:
      $secure = 'yes';
      break;
    default:
      $secure = 'ignore';
  }

  return $secure;
}

function shGetMenuItemLanguage( $id) {

  if(empty( $id)) {
    return '';
  }
  $language = '';
  $app = JFactory::getApplication();
  $menu = $app->getMenu();
  if(!empty($menu)) {
    $item = $menu->getItem( $id);
    if(!empty( $item)) {
      $language = $item->language == '*' ? shGetDefaultLang() : $item->language;
    }
  }
  return $language;
}

function shSefRelToAbs($string, $shLanguageParam, &$uri, &$originalUri) {

  global $_SEF_SPACE, $shGETVars, $shRebuildNonSef;

  ShlSystem_Log::debug( 'sh404sef', 'Entering shSefRelToAbs with '.$string.' | Lang = '.$shLanguageParam);

  $mainframe = JFactory::getApplication();

  $pageInfo = & Sh404sefFactory::getPageInfo();
  $sefConfig = & Sh404sefFactory::getConfig();

  // if superadmin, display non-sef URL, for testing/setting up purposes
  if (sh404SEF_NON_SEF_IF_SUPERADMIN) {
    $user = JFactory::getUser();
    if ($user->usertype == 'Super Administrator' ) {
      ShlSystem_Log::debug( 'sh404sef', 'Returning non-sef because superadmin said so.');
      return 'index.php';
    }
  }
  // return unmodified anchors
  if (JString::substr( $string, 0, 1) == '#') {
    // V 1.2.4.t
    return $string;
  }
  // Quick fix for shared SSL server : if https, switch to non sef
  $id = shGetURLVar($string, 'Itemid', JRequest::getInt( 'Itemid'));
  $secure = 'yes' == shGetMenuItemSsl( $id);
  if ($secure && $sefConfig->shForceNonSefIfHttps ) {
    ShlSystem_Log::debug( 'sh404sef', 'Returning shSefRelToAbs : Forced non sef if https');
    return shFinalizeURL($string);
  }

  $database = ShlDbHelper::getDb();

  $shOrigString = $string;
  $shMosMsg = shGetMosMsg($string); // V x 01/09/2007 22:45:52
  $string = shCleanUpMosMsg($string);// V x 01/09/2007 22:45:52

  // V x : removed shJoomfish module. Now we set $mosConfi_lang here
  $shOrigLang = $pageInfo->currentLanguageTag; // save current language
  $shLanguage = shGetURLLang( $string);  // target language in URl is always first choice
  // second choice is param
  if(empty( $shLanguage)) {
    $shLanguage = !empty($shLanguageParam) ? $shLanguageParam : $shLanguage;
  }
  // third choice is to read from menu, based on Itemid
  // but only for Joomla multilingual, this breaks
  // Joomfish/Falang, as current menu language may not be correct
  if(($pageInfo->isMultilingual === false || $pageInfo->isMultilingual == 'joomla') && empty( $shLanguage) && !empty( $id)) {
    $shLanguage = shGetMenuItemLanguage($id);
  }
  if (empty($shLanguage)) {
    $shLanguage = !empty($shLanguageParam) ? $shLanguageParam : $pageInfo->currentLanguageTag;
  }

  // V 1.3.1 protect against those drop down lists
  if (strpos( $string, 'this.options[selectedIndex].value') !== false) {
    $string .= '&amp;lang='.shGetIsoCodeFromName($shLanguage);
    return $string;
  }
  $pageInfo->currentLanguageTag = $shLanguage;
  ShlSystem_Log::debug( 'sh404sef', 'Language used : '.$shLanguage);

  // V 1.2.4.t workaround for old links like option=compName instead of option=com_compName
  if ( strpos(strtolower($string), 'option=login') === false && strpos(strtolower($string), 'option=logout') === false &&
      strpos(strtolower($string), 'option=&') === false && JString::substr(strtolower($string), -7) != 'option='
      && strpos(strtolower($string), 'option=cookiecheck') === false
      && strpos(strtolower($string), 'option=') !== false && strpos(strtolower($string), 'option=com_') === false) {
    $string = str_replace('option=', 'option=com_', $string);
  }
  // V 1.2.4.j string to be appended to URL, but not saved to DB
  $shAppendString = '';
  $shRebuildNonSef = array();
  $shComponentType = '';  // V w initialize var to avoid notices

  if ($pageInfo->homeLink) {
    // now check URL against our homepage, so as to always return / if homepage
    $v1 = JString::ltrim(str_replace($pageInfo->getDefaultFrontLiveSite(), '', $string), '/');
    // V 1.2.4.m : remove anchor if any
    $v2 = explode( '#', $v1);
    $v1 = $v2[0];
    $shAnchor = isset($v2[1]) ? '#'.$v2[1] : '';
    $shSepString = (JString::substr($v1, -9) == 'index.php' || strpos( $v1, '?') === false) ? '?':'&';
    $shLangString = 'lang='.shGetIsoCodeFromName($shLanguage);
    if (!strpos($v1,'lang=')) {
      $v1 .= $shSepString . $shLangString;
    }
    $v1 = str_replace('&amp;', '&', shSortURL($v1));
    // V 1.2.4.t check also without pagination info
    if (strpos( $v1, 'limitstart=0') !== false) {
      // the page has limitstart=0
      $stringNoPag = shCleanUpPag($v1);  // remove paging info to be sure this is not homepage
    } else {
      $stringNoPag = null;
    }
    if ($v1 == $pageInfo->homeLink
        || $v1 == $pageInfo->allLangHomeLink
        || $v1 == 'index.php?'.$shLangString
        || $stringNoPag == $pageInfo->homeLink
        || $stringNoPag == $pageInfo->allLangHomeLink
    )  {
      $shTemp = $v1 == $pageInfo->homeLink || shIsDefaultLang($shLanguage) ? '' : shGetIsoCodeFromName($shLanguage) . '/';

      if (!empty($sefConfig->shForcedHomePage)) {
        // V 1.2.4.t
        $shTmp = $shTemp.$shAnchor;
        $ret = shFinalizeURL($sefConfig->shForcedHomePage.(empty($shTmp) ? '' : '/'.$shTmp));
        if (empty($uri))  // if no URI, append remaining vars directly to the string
          $ret .= $shAppendString;
        else
          shRebuildVars( $shAppendString, $uri);
        $pageInfo->currentLanguageTag = $shOrigLang;
        ShlSystem_Log::debug( 'sh404sef', 'Returning shSefRelToAbs 1 with '.$ret);
        return $ret;
      } else {
        $shRewriteBit = shIsDefaultLang($shLanguage)? '/': $sefConfig->shRewriteStrings[$sefConfig->shRewriteMode];
        $ret = shFinalizeURL($pageInfo->getDefaultFrontLiveSite().$shRewriteBit.$shTemp.$shAnchor);
        if (empty($uri))  // if no URI, append remaining vars directly to the string
          $ret .= $shAppendString;
        else
          shRebuildVars( $shAppendString, $uri);
        $pageInfo->currentLanguageTag = $shOrigLang;
        ShlSystem_Log::debug( 'sh404sef', 'Returning shSefRelToAbs 2 with '.$ret);
        return $ret;
      }
    }
  }

  $newstring = str_replace($pageInfo->getDefaultFrontLiveSite().'/', '', $string);

  $letsGo = JString::substr($newstring,0,9) == 'index.php'
  && (strpos( $newstring, 'this.options[selectedIndex\].value' ) === false);
  $letsGoSsl = false;
  if ($letsGo || $letsGoSsl)
  {
    // Replace & character variations.
    $string = str_replace(array('&amp;', '&#38;'), array('&', '&'), $letsGo ? $newstring : $newStringSsl);
    $newstring = $string; // V 1.2.4.q
    $shSaveString = $string;
    // warning : must add &lang=xx (only if it does not exists already), so as to be able to recognize the SefURL in the db if it's there
    if (!strpos($string,'lang=')) {
      $shSepString = JString::substr($string, -9) == 'index.php' ? '?':'&';
      $anchorTable = explode('#', $string); // V 1.2.4.m remove anchor before adding language
      $string = $anchorTable[0];
      $string .= $shSepString.'lang='.shGetIsoCodeFromName($shLanguage)
      .(!empty($anchorTable[1])? '#'.$anchorTable[1]:''); // V 1.2.4.m then stitch back anchor
    }
    $URI = new sh_Net_URL($string);
    // V 1.2.4.l need to save unsorted URL
    if (count($URI->querystring) > 0) {
      // Import new vars here.
      $option = null;
      $task = null;
      //$sid = null;  V 1.2.4.s
      // sort GET parameters to avoid some issues when same URL is produced with options not
      // in the same order, ie index.php?option=com_virtuemart&category_id=3&Itemid=2&lang=fr
      // Vs index.php?category_id=3&option=com_virtuemart&Itemid=2&lang=fr
      ksort($URI->querystring);  // sort URL array
      $string = shSortUrl($string);
      // now we are ready to extract vars
      $shGETVars = $URI->querystring;
      extract($URI->querystring, EXTR_REFS);
    }

    if (empty($option)) {
      // V 1.2.4.r protect against empty $option : we won't know what to do
      $pageInfo->currentLanguageTag = $shOrigLang;
      ShlSystem_Log::debug( 'sh404sef', 'Returning shSefRelToAbs 3 with '.$shOrigString);
      return $shOrigString;
    }

    // get plugin associated with the extension
    $extPlugin = & Sh404sefFactory::getExtensionPlugin( $option);

    // get component type
    $shComponentType = $extPlugin->getComponentType();
    $shOption = str_replace('com_', '', $option);

    //list of extension we always skip
    $alwaysSkip = array( 'jce', 'akeeba', 'media');
    if( in_array($shOption, $alwaysSkip)) {
      $shComponentType = Sh404sefClassBaseextplugin::TYPE_SKIP;
    }

    // V 1.2.4.s : fallback to to JoomlaSEF if no extension available
    // V 1.2.4.t : this is too early ; it prevents manual custom redirect to be checked agains the requested non-sef URL
    ShlSystem_Log::debug( 'sh404sef', 'Component type = '.$shComponentType);
    // is there a named anchor attached to $string? If so, strip it off, we'll put it back later.
    if ($URI->anchor)
      $string = str_replace('#'.$URI->anchor, '', $string);  // V 1.2.4.m
    // shumisha special homepage processing (in other than default language)
    if  ((shIsAnyHomePage($string)) || ($string == 'index.php')  // 10/08/2007 18:13:43
    ){
      $sefstring = '';
      $urlType = shGetSefURLFromCacheOrDB($string, $sefstring);
      // still use it so we need it both ways
      if (($urlType == sh404SEF_URLTYPE_NONE || $urlType == sh404SEF_URLTYPE_404) && empty($showall) && (!empty($limit) || (!isset($limit) && !empty($limitstart))) ) {
        $urlType = shGetSefURLFromCacheOrDB(shCleanUpPag($string), $sefstring); // V 1.2.4.t check also without page info
        //to be able to add pagination on custom
        //redirection or multi-page homepage
        if ($urlType != sh404SEF_URLTYPE_NONE && $urlType != sh404SEF_URLTYPE_404) {
          $sefstring = shAddPaginationInfo( @$limit, @$limitstart, @showall,1, $string, $sefstring, null);
          // a special case : com_content  does not calculate pagination right
          // for frontpage and blog, they include links shown at the bottom in the calculation of number of items
          // For instance, with joomla sample data, the frontpage has only 5 articles
          // but the view sets $limit to 9 !!!
          if (($option == 'com_content' && isset($layout) && $layout == 'blog')
              || ($option == 'com_content' && isset( $view) && $view == 'featured' )) {
            $listLimit = shGetDefaultDisplayNumFromURL($string, $includeBlogLinks = true);
            $string = shSetURLVar( $string, 'limit', $listLimit);
            $string = shSortUrl($string);
          }

          // that's a new URL, so let's add it to DB and cache
          shAddSefUrlToDBAndCache( $string, $sefstring, 0, $urlType);  // created url must be of same type as original
        }
        if ($urlType == sh404SEF_URLTYPE_NONE || $urlType == sh404SEF_URLTYPE_404) {
          require_once(sh404SEF_FRONT_ABS_PATH.'sef_ext.php');
          $sef_ext = new sef_404();
          // Rewrite the URL now.
          // a special case : com_content  does not calculate pagination right
          // for frontpage and blog, they include links shown at the bottom in the calculation of number of items
          // For instance, with joomla sample data, the frontpage has only 5 articles
          // but the view sets $limit to 9 !!!
          if (($option == 'com_content' && isset($layout) && $layout == 'blog')
              || ($option == 'com_content' && isset( $view) && $view == 'featured' )) {
            $listLimit = shGetDefaultDisplayNumFromURL($string, $includeBlogLinks = true);
            $string = shSetURLVar( $string, 'limit', $listLimit);
            $string = shSortUrl($string);
            //$URI->addQueryString( 'limit', $listLimit);
          }
          $urlVars = is_array($URI->querystring) ? array_map('urldecode', $URI->querystring): $URI->querystring;
          $sefstring = $sef_ext->create($string, $urlVars, $shAppendString, $shLanguage, $shOrigString, $originalUri); // V 1.2.4.s added original string
        }
      } else if (($urlType == sh404SEF_URLTYPE_NONE || $urlType == sh404SEF_URLTYPE_404)) {
        // not found but no $limit or $limitstart
        $sefstring = shGetIsoCodeFromName($shLanguage).'/';
        shAddSefUrlToDBAndCache( $string, $sefstring, 0, sh404SEF_URLTYPE_AUTO); // create it
      }
      // V 1.2.4.j : added $shAppendString to pass non sef parameters. For use with parameters that won't be stored in DB
      $ret = $pageInfo->getDefaultFrontLiveSite().(empty( $sefstring) ? '' : $sefConfig->shRewriteStrings[$sefConfig->shRewriteMode].$sefstring);

      $ret = shFinalizeURL($ret);
      if (empty($uri))  // if no URI, append remaining vars directly to the string
        $ret .= $shAppendString;
      else
        shRebuildVars( $shAppendString, $uri);
      $pageInfo->currentLanguageTag = $shOrigLang;
      ShlSystem_Log::debug( 'sh404sef', 'Returning shSefRelToAbs 4 with '.$ret);
      return $ret;
    }

    if (isset($option) && !($option=='com_content' && @$task == 'edit') && (strtolower($option) != 'com_sh404sef')) {
      // V x 29/08/2007 23:19:48
      // check also that option = com_content, otherwise, breaks some comp
      switch ($shComponentType) {
        case Sh404sefClassBaseextplugin::TYPE_SKIP : {
          $sefstring = $shSaveString;  // V 1.2.4.q : restore untouched URL, except anchor which will be added later
          // J! 1.6 kill all query vars
          $shGETVars = array();
          $uri->setQuery( array());
          break;
        }

        case Sh404sefClassBaseextplugin::TYPE_SIMPLE:

          // check for custom urls
          $sefstring = '';
          $urlType = shGetSefURLFromCacheOrDB($string, $sefstring);
          // if no custom found, then build default url
          if ($urlType != sh404SEF_URLTYPE_CUSTOM) {
            // if not found then fall back to Joomla! SEF
            if (isset($URI)) {
              unset($URI);
            }
            $sefstring = 'component/';
            $URI = new sh_Net_URL(shSortUrl($shSaveString)); // can't remove yet, anchor is use later down
            $jUri = new JUri(shSortUrl($shSaveString));
            $uriVars = $jUri->getQuery( $asArray = true);
            if (count($uriVars) > 0) {
              foreach($uriVars as $key => $value) {
                if( is_array($value) ) {
                  foreach($value as $k=>$v) {
                    // fix for arrays, thanks doorknob
                    $sefstring .= $key.'[' . $k. '],'. $v . '/';
                  }
                } else {
                  $sefstring .= $key.','.$value . '/';
                }
              }
              $sefstring = str_replace( 'option,', '', $sefstring );
            }
          }
          break;

        default: {
          $sefstring='';
          // base case:
          $urlType = shGetSefURLFromCacheOrDB($string, $sefstring);

          // first special case. User may have customized paginated urls
          // this will be picked up by the line above, except if we're talking about
          // a category or section blog layout, where Joomla does not uses the correct
          // value for limit
          if (($urlType == sh404SEF_URLTYPE_NONE || $urlType == sh404SEF_URLTYPE_404) && empty( $showall) && (!empty($limit) || (!isset($limit) && !empty($limitstart)))) {
            if (($option == 'com_content' && isset($layout) && $layout == 'blog')
                || ($option == 'com_content' && isset( $view) && $view == 'featured' )) {
              $listLimit = shGetDefaultDisplayNumFromURL($string, $includeBlogLinks = true);
              $tmpString = shSetURLVar( $string, 'limit', $listLimit);
              $tmpString = shSortUrl($tmpString);
              $urlType = shGetSefURLFromCacheOrDB($tmpString, $sefstring);
              if ($urlType != sh404SEF_URLTYPE_NONE && $urlType != sh404SEF_URLTYPE_404) {
                // we found a match with pagination info!
                $string = $tmpString;
              }
            }
          }

          // now let's try again without any pagination at all
          /*
           if (($urlType == sh404SEF_URLTYPE_NONE || $urlType == sh404SEF_URLTYPE_404) && empty( $showall) && (!empty($limit) || (!isset($limit) && !empty($limitstart)))) {
          $urlType = shGetSefURLFromCacheOrDB(shCleanUpPag($string), $sefstring); // search without pagination info
          if ($urlType != sh404SEF_URLTYPE_NONE && $urlType != sh404SEF_URLTYPE_404) {
          $sefstring = shAddPaginationInfo( @$limit, @$limitstart, @showall, 1, $string, $sefstring, null);
          // a special case : com_content  does not calculate pagination right
          // for frontpage and blog, they include links shown at the bottom in the calculation of number of items
          // For instance, with joomla sample data, the frontpage has only 5 articles
          // but the view sets $limit to 9 !!!
          if (($option == 'com_content' && isset($layout) && $layout == 'blog')
              || ($option == 'com_content' && isset( $view) && $view == 'featured' )) {
          $listLimit = shGetDefaultDisplayNumFromURL($string, $includeBlogLinks = true);
          $string = shSetURLVar( $string, 'limit', $listLimit);
          $string = shSortUrl($string);
          }

          // that's a new URL, so let's add it to DB and cache
          ShlSystem_Log::debug( 'sh404sef', 'Created url based on non paginated base url:' . $string);
          shAddSefUrlToDBAndCache( $string, $sefstring, 0, $urlType);
          }
          }
          */
          if ($urlType == sh404SEF_URLTYPE_NONE) {
            // If component has its own sef_ext plug-in included.
            $shDoNotOverride = in_array( $shOption, $sefConfig->shDoNotOverrideOwnSef);
            if (shFileExists(sh404SEF_ABS_PATH.'components/'.$option.'/sef_ext.php')
                && ($shDoNotOverride                   // and param said do not override
                    || (!$shDoNotOverride              // or param said override, but we don't have a plugin either in sh404SEF dir or component sef_ext dir
                        && (!shFileExists(sh404SEF_ABS_PATH
                            .'components/com_sh404sef/sef_ext/'.$option.'.php')
                            &&
                            !shFileExists(sh404SEF_ABS_PATH
                                .'components/'.$option.'/sef_ext/'.$option.'.php') )
                    ))) {
              // Load the plug-in file. V 1.2.4.s changed require_once to include
              include_once(sh404SEF_ABS_PATH.'components/'.$option.'/sef_ext.php');
              $_SEF_SPACE = $sefConfig->replacement;
              $comp_name = str_replace('com_', '', $option);
              $className = 'sef_' . $comp_name;
              $sef_ext = new $className;
              // V x : added default string in params
              if (empty($sefConfig->defaultComponentStringList[$comp_name]))
                $title[] = getMenuTitle($option, null, isset($Itemid) ? @$Itemid : null, null, $shLanguage); // V 1.2.4.x
              else $title[] = $sefConfig->defaultComponentStringList[$comp_name];
              // V 1.2.4.r : clean up URL BEFORE sending it to sef_ext files, to have control on what they do
              // remove lang information, we'll put it back ourselves later
              //$shString = preg_replace( '/(&|\?)lang=[a-zA-Z]{2,3}/iU' ,'', $string);
              // V 1.2.4.t use original non-sef string. Some sef_ext files relies on order of params, which may
              // have been changed by sh404SEF
              $shString = preg_replace( '/(&|\?)lang=[a-zA-Z]{2,3}/iu' ,'', $shSaveString);
              $finalstrip = explode("|", $sefConfig->stripthese);
              $shString = str_replace('&', '&amp;', $shString);
              ShlSystem_Log::debug( 'sh404sef', 'Sending to own sef_ext.php plugin : '.$shString);
              $sefstring = $sef_ext->create($shString);
              ShlSystem_Log::debug( 'sh404sef', 'Created by sef_ext.php plugin : '.$sefstring);
              $sefstring = str_replace("%10", "%2F", $sefstring);
              $sefstring = str_replace("%11", $sefConfig->replacement, $sefstring);
              $sefstring = rawurldecode($sefstring);
              if ($sefstring == $string) {
                if (!empty($shMosMsg)) // V x 01/09/2007 22:48:01
                  $string .= '?'.$shMosMsg;
                $ret = shFinalizeURL($string);
                $pageInfo->currentLanguageTag = $shOrigLang;
                ShlSystem_Log::debug( 'sh404sef', 'Returning shSefRelToAbs 5 with '.$ret);
                return $ret;
              } else {
                // V 1.2.4.p : sef_ext extensions for opensef/SefAdvance do not always replace '
                $sefstring = str_replace( '\'', $sefConfig->replacement, $sefstring);
                // some ext. seem to html_special_chars URL ?
                $sefstring = str_replace( '&#039;', $sefConfig->replacement, $sefstring); // V w 27/08/2007 13:23:56
                $sefstring = str_replace(' ', $_SEF_SPACE, $sefstring);
                $sefstring = str_replace(' ', '',
                    (shInsertIsoCodeInUrl($option, $shLanguage) ?   // V 1.2.4.q
                        shGetIsoCodeFromName($shLanguage).'/' : '')
                    .titleToLocation($title[0]).'/'.$sefstring.(($sefstring != '') ? $sefConfig->suffix : ''));
                if (!empty($sefConfig->suffix))
                  $sefstring = str_replace('/'.$sefConfig->suffix, $sefConfig->suffix, $sefstring);

                //$finalstrip = explode("|", $sefConfig->stripthese);
                $sefstring = str_replace($finalstrip, $sefConfig->replacement, $sefstring);
                $sefstring = str_replace($sefConfig->replacement.$sefConfig->replacement.$sefConfig->replacement,
                    $sefConfig->replacement, $sefstring);
                $sefstring = str_replace($sefConfig->replacement.$sefConfig->replacement,
                    $sefConfig->replacement, $sefstring);
                $suffixthere = 0;
                if (!empty($sefConfig->suffix) && strpos($sefstring, $sefConfig->suffix ) !== false)  // V 1.2.4.s
                  $suffixthere = strlen($sefConfig->suffix);
                $takethese = str_replace("|", "", $sefConfig->friendlytrim);
                $sefstring = JString::trim(JString::substr($sefstring,0,strlen($sefstring)-$suffixthere), $takethese);
                $sefstring .= $suffixthere == 0 ? '': $sefConfig->suffix;  // version u 26/08/2007 17:27:16
                // V 1.2.4.m store it in DB so as to be able to use sef_ext plugins really !
                $string = str_replace('&amp;', '&', $string);
                // V 1.2.4.r without mod_rewrite
                $shSefString = shAdjustToRewriteMode($sefstring);
                // V 1.2.4.p check for various URL for same content
                $dburl = ''; // V 1.2.4.t prevent notice error
                $urlType = sh404SEF_URLTYPE_NONE;
                if ($sefConfig->shUseURLCache)
                  $urlType = Sh404sefHelperCache::getNonSefUrlFromCache($shSefString, $dburl);
                $newMaxRank = 0; // V 1.2.4.s
                $shDuplicate = false;
                if ($sefConfig->shRecordDuplicates || $urlType == sh404SEF_URLTYPE_NONE) {
                  try {
                    $sql = "SELECT newurl, rank, dateadd FROM #__sh404sef_urls WHERE oldurl = "
                    .$database->Quote($shSefString)." ORDER BY rank ASC";
                    $database->setQuery($sql);
                    $dbUrlList = $database->shlLoadObjectList();
                  } catch (Exception $e) {
                    ShlSystem_Log::error( 'sh404sef', '%s::%s::%d: %s', __CLASS__, __METHOD__, __LINE__, $e->getMessage());
                    $dbUrlList = array();
                  }
                  if (count($dbUrlList) > 0) {
                    $dburl = $dbUrlList[0]->newurl;
                    $newMaxRank = $dbUrlList[count($dbUrlList)-1]->rank+1;
                    $urlType = $dbUrlList[0]->dateadd == '0000-00-00' ? sh404SEF_URLTYPE_AUTO : sh404SEF_URLTYPE_CUSTOM;
                  }
                }
                if ($urlType != sh404SEF_URLTYPE_NONE && ($dburl != $string)) $shDuplicate = true;
                $urlType = $urlType == sh404SEF_URLTYPE_NONE ? sh404SEF_URLTYPE_AUTO : $urlType;
                ShlSystem_Log::debug( 'sh404sef', 'Adding from sef_ext to DB : '.$shSefString.' | rank = '.($shDuplicate?$newMaxRank:0) );
                shAddSefUrlToDBAndCache( $string, $shSefString, ($shDuplicate?$newMaxRank:0), $urlType);
              }
            }
            // Component has no own sef extension.
            else {
              $string = JString::trim($string, "&?");

              // V 1.2.4.q a trial in better handling homepage articles
              // disabled in J! 1.6. Becomes too complex with multi-language
              // TODO: remove guessItemidOnHomepage setting
              if (false && shIsCurrentPageHome() && ($option == 'com_content')    // com_content component on homepage
                  && (isset($task)) && ($task == 'view')
                  && $sefConfig->guessItemidOnHomepage) {
                $string = preg_replace( '/(&|\?)Itemid=[^&]*/iu', '', $string);  // we remove Itemid, as com_content plugin
                $Itemid = null;                                     // will hopefully do a better job at finding the right one
                unset($URI->querystring['Itemid']);
                unset($shGETVars['Itemid']);
              }

              require_once(sh404SEF_FRONT_ABS_PATH.'sef_ext.php');
              $sef_ext = new sef_404();
              // Rewrite the URL now. // V 1.2.4.s added original string
              // a special case : com_content  does not calculate pagination right
              // for frontpage and blog, they include links shown at the bottom in the calculation of number of items
              // For instance, with joomla sample data, the frontpage has only 5 articles
              // but the view sets $limit to 9 !!!
              if (($option == 'com_content' && isset($layout) && $layout == 'blog')
                  || ($option == 'com_content' && isset( $view) && $view == 'featured' )) {
                $listLimit = shGetDefaultDisplayNumFromURL($string, $includeBlogLinks = true);
                $string = shSetURLVar( $string, 'limit', $listLimit);
                $string = shSortUrl($string);
                //$URI->addQueryString( 'limit', $listLimit);
              }
              $sefstring = $sef_ext->create($string, $URI->querystring, $shAppendString, $shLanguage, $shOrigString, $originalUri);
              ShlSystem_Log::debug( 'sh404sef', 'Created sef url from default plugin: '.$sefstring);
            }
          }
        }
      } // end of cache check shumisha
      if (isset($sef_ext)) unset($sef_ext);

      // if string has not been modified, then we have decided for a non-sef
      if($string == $sefstring) {
        // J! 1.6 kill all query vars
        $shGETVars = array();
        $uri->setQuery( array());
      } else {
        // include rewrite mode bit
        $shRewriteBit = $shComponentType == Sh404sefClassBaseextplugin::TYPE_SKIP ? '/': $sefConfig->shRewriteStrings[$sefConfig->shRewriteMode];
        if (strpos($sefstring,'index.php') === 0 ) $shRewriteBit = '/';  // V 1.2.4.t bug #119
        $string =  $pageInfo->getDefaultFrontLiveSite().$shRewriteBit.JString::ltrim( $sefstring, '/') . (($URI->anchor)?"#".$URI->anchor:'');
      }
    }
    else {  // V x 03/09/2007 13:47:37 editing content
      $shComponentType = Sh404sefClassBaseextplugin::TYPE_SKIP;  // will prevent turning & into &amp;
      ShlSystem_Log::debug( 'sh404sef', 'shSefrelfToAbs: option not set, skipping');
    }
    $ret = $string;
    // $ret = str_replace('itemid', 'Itemid', $ret); // V 1.2.4.t bug #125
    ShlSystem_Log::debug( 'sh404sef', '(1) Setting shSefRelToAbs return string as: ' . $ret);
  }
  if (!isset($ret)) {
    $ret = $string;
    ShlSystem_Log::debug( 'sh404sef', '(2) Setting shSefRelToAbs return string as: ' . $ret);
  }
  $ret = ($shComponentType == Sh404sefClassBaseextplugin::TYPE_DEFAULT) ? shFinalizeURL($ret) : $ret;  // V w 27/08/2007 13:21:28
  ShlSystem_Log::debug( 'sh404sef', '(3) shSefRelToAbs return string after shFinalize: ' . $ret);
  if (empty($uri) || $shComponentType == Sh404sefClassBaseextplugin::TYPE_SKIP) {
    // we don't have a uri : we must be doing a redirect from non-sef to sef or similar
    $ret .= $shAppendString;  // append directly to url
    ShlSystem_Log::debug( 'sh404sef', '(4) shSefRelToAbs return string after appendString: ' . $ret);
  } else {
    if ( empty( $sefstring) || (!empty( $sefstring) && strpos( $sefstring, 'index.php') !== 0 )) {
      shRebuildVars( $shAppendString, $uri);  // instead, add to uri. Joomla will put everything together. Only do this if we have a sef url, and not if we have a non-sef
      ShlSystem_Log::debug( 'sh404sef', '(5) shSefRelToAbs no sefstring, adding rebuild vars : ' . $shAppendString);
    }
  }
  $pageInfo->currentLanguageTag = $shOrigLang;
  ShlSystem_Log::debug( 'sh404sef', 'shSefRelToAbs: finally returning: ' . $ret);
  return $ret;
}

// V 1.2.4.t returns sef url with added pagination information
function shAddPaginationInfo( $limit, $limitstart, $showall, $iteration, $url, $location, $shSeparator = null, $defaultListLimitValue = null){

  $mainframe = JFactory::getApplication();

  //echo 'Incoming pagination : ' . $url . ' | limit : ' . $limit . ' | start : ' . $limitstart . "\n";

  $pageInfo = & Sh404sefFactory::getPageInfo();  // get page details gathered by system plugin
  $sefConfig = & Sh404sefFactory::getConfig();
  $database = ShlDbHelper::getDb();

  // get a default limit value, for urls where it's missing
  $listLimit = shGetDefaultDisplayNumFromURL($url, $includeBlogLinks = true);
  $defaultListLimit = is_null( $defaultListLimitValue) ? shGetDefaultDisplayNumFromConfig( $url, $includeBlogLinks = false) : $defaultListLimitValue;

  //echo 'Incoming pagination : $listLimit : ' . $listLimit . ' | $defaultListLimit : ' . $defaultListLimit . "\n";

  // clean suffix and index file before starting to add things to the url
  // clean suffix
  if (strpos($url, 'option=com_content') !== false && strpos($url, 'format=pdf') !== false) {
    $shSuffix = '.pdf';
  } else {
    $shSuffix = $sefConfig->suffix;
  }
  $suffixLength = JString::strLen( $shSuffix);
  if (!empty($shSuffix) && ($shSuffix != '/') && JString::substr( $location, -$suffixLength) == $shSuffix) {
    $location = JString::substr($location,0,JString::strlen($location) - $suffixLength);
  }

  // clean index file
  if ($sefConfig->addFile && (empty($shSuffix) || JString::subStr( $location, -$suffixLength) != $shSuffix)) {
    $indexFileLength = JString::strlen( $sefConfig->addFile);
    if (($sefConfig->addFile != '/') && JString::substr( $location, -$indexFileLength) == $sefConfig->addFile) {
      $location = JString::substr( $location, 0, JString::strlen($location) - $indexFileLength);
    }
  }

  // do we have a trailing slash ?
  if (empty($shSeparator)) {
    $shSeparator = (JString::substr($location, -1) == '/') ? '':'/';
  }
  if (!empty($limit) && is_numeric( $limit)) {
    $pagenum = intval($limitstart/$limit);
    $pagenum++;
  } else if (!isset($limit) && !empty($limitstart)) {
    // only limitstart
    if (strpos( $url, 'option=com_content') !== false && strpos( $url, 'view=article') !== false) {
      $pagenum = intval($limitstart+1);   // multipage article
    }
    else {
      $pagenum = intval($limitstart/$listLimit)+1;  // blogs, tables, ...
    }
  } else {
    $pagenum = $iteration;
  }
  // Make sure we do not end in infite loop here.
  if ($pagenum < $iteration)
    $pagenum = $iteration;
  // shumisha added to handle table-category and table-section which may have variable number of items per page
  // There still will be a problem with filter, which may reduce the total number of items. Thus the item we are looking for
  if ( $sefConfig->alwaysAppendItemsPerPage || (strpos($url,'option=com_virtuemart') && $sefConfig->shVmUsingItemsPerPage)) {
    $shMultPageLength= $sefConfig->pagerep.(empty($limit) ? $listLimit : $limit);
  } else $shMultPageLength= '';
  // shumisha : modified to add # of items per page to URL, for table-category or section-category

  if (!empty($sefConfig->pageTexts[$pageInfo->currentLanguageTag])
      && (false !== strpos($sefConfig->pageTexts[$pageInfo->currentLanguageTag], '%s'))){
    $page = str_replace('%s', $pagenum, $sefConfig->pageTexts[$pageInfo->currentLanguageTag]).$shMultPageLength;
  } else {
    $page = $sefConfig->pagerep.$pagenum.$shMultPageLength;
  }

  // V 1.2.4.t special processing to replace page number by headings
  $shPageNumberWasReplaced = false;
  if (  strpos($url, 'option=com_content') !== false
      && strpos($url, 'view=article') !== false && !empty($limitstart) ) {
    // this is multipage article - limitstart instead of limit in J1.5
    if ( $sefConfig->shMultipagesTitle ) {
      parse_str($url, $shParams);
      if (!empty($shParams['id'])) {
        $shPageTitle = '';
        try {

          $contentElement = ShlDbHelper::selectObject( '#__content', array( 'id', 'fulltext', 'introtext'), array( 'id' => $shParams['id']));

        } catch (Exception $e) {
          JError::raise(E_ERROR, 500, $e->getMessage());
        }
        $contentText = $contentElement->introtext . $contentElement->fulltext;
        if (!empty($contentElement) && ( strpos( $contentText, 'class="system-pagebreak' ) !== false )) {
          // search for mospagebreak tags
          // copied over from pagebreak plugin
          // expression to search for
          $regex = '#<hr([^>]*)class=(\"|\')system-pagebreak(\"|\')([^>]*)\/>#iU';
          // find all instances of mambot and put in $matches
          $shMatches = array();
          preg_match_all( $regex, $contentText, $shMatches, PREG_SET_ORDER );
          // adds heading or title to <site> Title
          if (empty($limitstart)) {
            // if first page use heading of first mospagebreak
            /* if ( $shMatches[0][2] ) {
             parse_str( html_entity_decode( $shMatches[0][2] ), $args );
            if ( @$args['heading'] ) {
            $shPageTitle = stripslashes( $args['heading'] );
            }
            }*/
          } else {  // for other pages use title of mospagebreak
            if ( $limitstart > 0 && $shMatches[$limitstart-1][1] ) {
              $args = JUtility::parseAttributes( $shMatches[$limitstart-1][0] );
              if ( @$args['title'] ) {
                $shPageTitle = $args['title'];
              } else if (@$args['alt']) {
                $shPageTitle = $args['alt'];
              } else {  // there is a page break, but no title. Use a page number
                $shPageTitle = str_replace('%s', $limitstart+1, $sefConfig->pageTexts[$pageInfo->currentLanguageTag]);
              }
            }
          }
        }
        if (!empty($shPageTitle)) {
          // found a heading, we should use that as a Title
          $location .= $shSeparator.titleToLocation($shPageTitle);
        }
        $shPageNumberWasReplaced = true;  // always set the flag, otherwise we'll a Page-1 added
      }
    } else {
      // mutiple pages article, but we don't want to use smart title.
      // directly use limitstart
      $page = str_replace('%s', $limitstart+1, $sefConfig->pageTexts[$pageInfo->currentLanguageTag]);
    }
  }
  // maybe this is a multipage with "showall=1"
  if ( strpos($url, 'option=com_content') !== false
      && strpos($url, 'view=article') !== false && strpos($url, 'showall=1') !== false ) {
    // this is multipage article with showall
    $tempTitle = JText::_( 'All Pages' );
    $location .= $shSeparator. titleToLocation( $tempTitle);
    $shPageNumberWasReplaced = true;  // always set the flag, otherwise we'll a Page-1 added
  }

  // make sure we remove bad characters
  $takethese = str_replace('|', '', $sefConfig->friendlytrim);
  $location = JString::trim( $location, $takethese);

  // add page number
  if (!$shPageNumberWasReplaced
      && (
          (!isset($limitstart) && (isset($limit) && $limit != 1 && $limit != $listLimit && $limit != $defaultListLimit))
          || !empty( $limitstart)
      )
  ) {
    $location .= $shSeparator.$page;
  }
  // add suffix
  if (!empty($shSuffix) && !empty($location) && $location != '/' && JString::substr($location, -1) != '/') {
    $location = $shSuffix == '/' ?
    $location.$shSuffix
    : str_replace($shSuffix, '', $location).$shSuffix;
  }

  // add default index file
  if ($sefConfig->addFile){
    // V 1.2.4.t
    if ((empty($shSuffix)
        || (!empty($shSuffix) && JString::subStr( $location, -$suffixLength) != $shSuffix) ) )
      $location .= (JString::substr($location, -1) == '/' ? '':'/').$sefConfig->addFile;
  }
  return JString::ltrim($location, '/');
}


// V 1.2.4.t check if this is a request for VM cookie check AND done by a search engine
// if so, this has to be an old link left over in search engine index, and  we must 301 redirectt to
// same URl without vmvhk/
function shCheckVMCookieRedirect() {

  $pageInfo = & Sh404sefFactory::getPageInfo();

  if (shIsSearchEngine() && strpos($pageInfo->currentSefUrl, 'vmchk/') !== false) {
    shRedirect( str_replace('vmchk/', '', $pageInfo->currentSefUrl));
  }
}


/*
 * 404SEF SUPPORT FUNCTIONS
*/

// @TODO: deprecate this function, we don't need sef_ext.php file
// to perform decoding
function sef_ext_exists($this_name) {

  return null;
}

function getExt($URL_ARRAY)
{

  $sefConfig = & Sh404sefFactory::getConfig();

  $ext = array();
  $row = sef_ext_exists($URL_ARRAY[0]);
  $ext['path'] = sh404SEF_FRONT_ABS_PATH.'sef_ext.php';

  if (is_object($row)) {
    $option = str_replace("index.php?option=","",$row->link);
    $ext['path'] = sh404SEF_ABS_PATH."components/$option/sef_ext.php";
  }
  elseif ((strpos($URL_ARRAY[0], "com_") !== false) or ($URL_ARRAY[0] == "component")) {
    $option = "com_component";
  } else{
    $option = "404";
  }
  $ext['name'] = str_replace("com_","",$option);

  return $ext;
}

function is_valid($string)
{
  global $base, $index;
  if (empty($string))
    $state = false;
  elseif (($string == $index )|($string == $base.$index )) {
    $state = true ;
  }
  else {
    $state = false;
    require_once(sh404SEF_FRONT_ABS_PATH.'sef_ext.php');
    $sef_ext = new sef_404;
    $option = (isset($_GET['option'])) ? $_GET['option'] : (isset($_REQUEST['option'])) ? $_REQUEST['option'] : null;

    $vars = array();
    if (is_null($option)) {
      parse_str($string, $vars);
      if (isset($vars['option'])) {
        $option = $vars['option'];
      }
    }
    switch ($option) {
      case is_null($option):
        break;
      case "login":   /*Beat: makes this also compatible with CommunityBuilder login module*/
      case "logout": {
        $state = true;
        break;
      }
      default: {
        if (is_valid_component($option)){
          if ((!($option == "com_content"))|(!($option == "content"))) {
            $state = true;
          }
          else {
            $title=$sef_ext->getContentTitles($_REQUEST['view'],$_REQUEST['id'], empty($_REQUEST['layout']) ? '' : $_REQUEST['layout']);
            if (count($title) > 0) {
              $state = true;
            }
          }
        }
        // shumisha check if this is homepage+lang=xx
        else {
          if (JString::substr($string,0,5)=='lang=')
            $state = true;
        }
        // shumisha end of change
      }
    }
  }
  return $state;
}

function is_valid_component($this)
{
  $state = false;
  $path = sh404SEF_ABS_PATH .'components/';

  if (is_dir($path)) {
    if (($contents = opendir($path))) {
      while (($node = readdir($contents)) !== false) {
        if ($node != '.' && $node != '..') {
          if (is_dir($path.'/'.$node) && $this == $node) {
            $state = true;
            break;
          }
        }
      }
    }
  }
  return $state;
}

// V 1.2.4.q detect homepage, disregarding pagination
function shIsHomepage( $string) {

  static $pages = array();
  static $home = '';

  if( !isset( $pages[$string])) {
    $pageInfo = & Sh404sefFactory::getPageInfo();
    if(empty($home) && !empty( $pageInfo->homeLink)) {
      $home = shSortUrl(shCleanUpPag($pageInfo->homeLink));
    }

    $shTempString = JString::rtrim(str_replace($pageInfo->getDefaultFrontLiveSite(), '', $string), '/');
    $pages[$string] = shSortUrl(shCleanUpPag($shTempString)) == $home; // version t added sorting
  }
  return $pages[$string];
}

function shIsAnyHomepage( $string) {

  static $pages = array();
  static $home = '';
  static $cleanedHomeLinks = array();

  if( !isset( $pages[$string])) {
    $pageInfo = & Sh404sefFactory::getPageInfo();
    if(empty( $cleanedHomeLinks)) {
      foreach( $pageInfo->homeLinks as $link) {
        $cleanedHomeLinks[] = shCleanUpPag( $link);
      }
    }

    $shTempString = JString::rtrim(str_replace($pageInfo->getDefaultFrontLiveSite(), '', $string), '/');
    $shTempString = shSortUrl(shCleanUpPag($shTempString));

    // check all homepages
    $pages[$string] = false;
    foreach( $cleanedHomeLinks as $link) {
      if( $link == $shTempString) {
        $pages[$string] = true;
      }
    }

  }
  return $pages[$string];
}

function getMenuTitle($option, $task, $id = null, $string = null, $shLanguage = null) {

  $pageInfo = & Sh404sefFactory::getPageInfo();
  $sefConfig = & Sh404sefFactory::getConfig();

  $shLanguage = empty($shLanguage) ? $pageInfo->currentLanguageTag : $shLanguage;
  $nameField = $sefConfig->useMenuAlias ? 'alias' : 'title';

  $menu = JFactory::getApplication()->getMenu();

  $attr = array();
  $values = array();
  if(!empty( $string)) {
    $attr[] = 'link';
    $values[] = $string;
  } else if(!empty($id)) {
    $attr[] = 'id';
    $values[] = $id;
  } else if(!empty($option)) {
    // need to find component id
    $component = JComponentHelper::getComponent($option, $strict = true);
    if(!$component->enabled) {
      return ('/');
    }
    $attr[] = 'component_id';
    $values[] = $component->id;
  } else {
    return '/';
  }

  // now ask J! to fetch menu item title
  $menuItem = shFindMenuItem( $menu->getMenu(), $attr, $values, $firstOnly = true);

  if(!empty( $menuItem)) {

    $languages	= JLanguageHelper::getLanguages();
    foreach( $languages as $langId => $language) {
      if(strpos( $pageInfo->homeLinks[$language->lang_code], 'Itemid='.$menuItem->id) !== false) {
        $title = $langId == 0 ? '/' : $language->sef;
        return $title;  // this is one of the homepages, retunr / or a lang code
      }
    }
    // non-homepage
    if(!empty($menuItem->$nameField)) {
      return $menuItem->$nameField;
    }

  }

  return str_replace('com_', '', $option);

}

function shFindMenuItem( $menuItems, $attributes, $values, $firstonly = false)
{
  $items = array();
  $attributes = (array) $attributes;
  $values = (array) $values;

  foreach ($menuItems as $item)
  {
    if (!is_object($item))
    {
      continue;
    }

    $test = true;
    for ($i = 0, $count = count($attributes); $i < $count; $i++)
    {
      if (is_array($values[$i]))
      {
        if (!in_array($item->$attributes[$i], $values[$i]))
        {
          $test = false;
          break;
        }
      }
      else
      {
        if ($item->$attributes[$i] != $values[$i])
        {
          $test = false;
          break;
        }
      }
    }

    if ($test)
    {
      if ($firstonly)
      {
        return $item;
      }

      $items[] = $item;
    }
  }

  return $items;
}

function shIsSearchEngine() {
  // return true if user agant is a search engine
  static $isSearchEngine = null;
  static $searchEnginesAgents = array(
      'B-l-i-t-z-B-O-T'
      ,'Baiduspider'
      ,'BlitzBot'
      ,'btbot'
      ,'DiamondBot'
      ,'Exabot'
      ,'FAST Enterprise Crawler'
      ,'FAST-WebCrawler/'
      ,'g2Crawler'
      ,'genieBot'
      ,'Gigabot'
      ,'Girafabot'
      ,'Googlebot'
      ,'ia_archiver'
      ,'ichiro'
      ,'Mediapartners-Google'
      ,'Mnogosearch'
      ,'msnbot'
      ,'MSRBOT'
      ,'Nusearch Spider'
      ,'SearchSight'
      ,'Seekbot'
      ,'sogou spider'
      ,'Speedy Spider'
      ,'Ask Jeeves/Teoma'
      ,'VoilaBot'
      ,'Yahoo!'
      ,'Slurp'
      ,'YahooSeeker'
      ,'YandexBot'
  );
  //return true;
  if (!is_null ($isSearchEngine)) {
    return $isSearchEngine;
  }
  else {
    $isSearchEngine = false;
    $useragent = empty($_SERVER['HTTP_USER_AGENT']) ? '' : strtolower($_SERVER['HTTP_USER_AGENT']);
    if (!empty($useragent)) {
      $remoteConfig = Sh404sefHelperUpdates::getRemoteConfig( $forced = false);
      $remotes = empty($remoteConfig->config['searchenginesagents']) ? array() : $remoteConfig->config['searchenginesagents'];
      $agents = array_unique( array_merge( $searchEnginesAgents, $remotes));
      foreach ($agents as $agent) {
        if (strpos($useragent, strtolower($agent)) !== false ) {
          $isSearchEngine = true;
          return true;
        }
      }
    }
    return $isSearchEngine;
  }
}

// J 1.5 specific functions

function shFetchLinkFromMenu($Itemid) {

}

function shRemoveSlugs( $vars, $removeWhat = true) {
  // remove slugs from a J! 1.5 non-sef style vars array
  if (!empty($vars)) {
    foreach($vars as $k => $v) {
      $m = is_string( $v) ? explode(':', $v) : null; // tracker #14107, thanks 3dentech
      if (!empty( $m) && !empty($m[1]) && is_numeric($m[0])) {
        // an integer followed by : followed by something
        $vars[$k]= $removeWhat === 'removeId' ? $m[1] : $m[0];  // depending on params, either keep id or slug
      } else {
        // use the raw value, for arrays for instance
        $vars[$k] = $v;
      }
    }
    // fix some problems in incoming URLs
    if (!empty($vars['Itemid'])) {
      // sometimes we get doubles : ?Itemid=xx?Itemid=xx
      $vars['Itemid'] = intval($vars['Itemid']);
    }
    if (!empty($vars['view'])) {
      // some links have view=article;
      $vars['view'] = str_replace('article;', 'article', $vars['view']);
      // view is set but no option : use default controller (com_content)
      if (empty($vars['option']))
        $vars['option'] = 'com_content';
    }
    if (empty( $vars['option']) && !empty($vars['format']) && $vars['format']=='feed') {
      $vars['option'] = 'com_content';
    }
  }
  return $vars;
}

function shNormalizeNonSefUri( & $uri, $menu = null, $removeSlugs = true) {
  // put back a J!1.5 non-sef url to J! 1.0.x format
  // Get the route
  $route = $uri->getPath();
  //Get the query vars
  $vars = $uri->getQuery(true);
  // fix some problems in incoming URLs
  if (!empty($vars['Itemid'])) {
    // sometimes we get doubles : ?Itemid=xx?Itemid=xx
    $vars['Itemid'] = intval($vars['Itemid']);
    $uri->setQuery($vars);
  }

  // fix urls obtained through a single Itemid, in menus : url is option=com_xxx&Itemid=yy
  $urlLang = $uri->getVar('lang', '');
  if ((count($vars) == 2 && $uri->getVar('Itemid')) ||(count($vars) == 3 && $uri->getVar('Itemid') && $uri->getVar('lang')) ) {
    if (empty($menu))
      $menu = JFactory::getApplication()->getMenu();
    $shItem = $menu->getItem($vars['Itemid']);
    if (!empty($shItem)) {
      // we found the menu item
      $url = $shItem->link.'&Itemid='.$shItem->id;
      $uri = new JURI($url);  // rebuild $uri based on this new url
      $uri->setPath($route);
      $vars = $uri->getQuery(true);
      if(!empty( $urlLang)) {
        $vars['lang'] = $urlLang;
      }
    }
  }

  if ($removeSlugs !== false) {
    $vars = shRemoveSlugs($vars, $removeSlugs);
  }
  $uri->setQuery($vars);
}

function shNormalizeNonSefUrl($url){
  // returns non-sef url with slugs removed + a few fixes

  $uri = new JURI($url);
  shNormalizeNonSefUri($uri);
  return $uri->toString(array('path', 'query', 'fragment'));

}

function shCheckRedirect ($dest, $incomingUrl) {

  $sefConfig = & Sh404sefFactory::getConfig();
  if (!empty($dest) && $dest != $incomingUrl) {
    // redirect to alias
    if ($dest == sh404SEF_HOMEPAGE_CODE) {
      if (!empty($sefConfig->shForcedHomePage)) {
        $dest = shFinalizeURL($sefConfig->shForcedHomePage);
      } else {
        $dest = shFinalizeURL(Sh404sefFactory::getPageInfo()->getDefaultFrontLiveSite());
      }
    } else {
      $shUri = new JURI($dest);
      $shOriginalUri = clone( $shUri);
      $dest = shSefRelToAbs($dest, '', $shUri, $shOriginalUri) . $shUri->toString( array('query'));
    }

    if ($dest != $incomingUrl) {
      ShlSystem_Log::debug( 'sh404sef', 'Redirecting to '. $dest .' from alias '.$incomingUrl);
      shRedirect($dest);
    }
  }
}

function shUrlSafeDisplay( $url) {

  return htmlentities( $url, ENT_QUOTES, 'UTF-8');
}

/**
 * Read config values from sobi2 config table
 *
 * @param $key
 * @param $section
 * @return string
 */
function shGetSobi2Config($key, $section ) {

  ShlSystem_Log::debug( 'sh404sef', 'Using removed shGetSobi2Config function, not applicable anymore');
  return false;
}

/**
 * Insert an intro text into the content table
 *
 * @param strng $shIntroText
 * @return boolean, true if success
 */
function shInsertContent( $pageTitle, $shIntroText) {

  jimport('joomla.database.table');
  try {
    $catid = Sh404sefHelperCategories::getUncategorizedCat();
    if(empty($catid)) {
      $this->setError( JText::_('COM_SH404SEF_CANNOT_SAVE_404_NO_UNCAT'));
      return;
    }
    $contentTable = JTable::getInstance( 'content');
    $content = array( 'title' => $pageTitle, 'alias' => $pageTitle, 'title_alias' => $pageTitle, 'introtext' => $shIntroText, 'state' => 1
        , 'catid' => $catid, 'attribs' => '{"menu_image":"-1","show_title":"0","show_section":"0","show_category":"0","show_vote":"0","show_author":"0","show_create_date":"0","show_modify_date":"0","show_pdf_icon":"0","show_print_icon":"0","show_email_icon":"0","pageclass_sfx":""');

    $status = $contentTable->save( $content);
  } catch (Exception $e) {
    $status = false;
  }

  return $status;
}

/**
 * This function based on Joomfish own method, but the
 * JF one only returns the current item translated
 * instead of the full menu set
 *
 * @param $lang
 * @param $getOriginals
 * @param $currentLangMenuItems
 * @return unknown_type
 */
function shGetJFMenu($lang, $getOriginals=true,  $currentLangMenuItems=false){

  die('Deprecated code, should not be called');

}

function shSetupMenuRoutes(&$menus) {

  if($menus) {
    foreach($menus as $key => $menu)
    {
      //Get parent information
      $parent_route = '';
      $parent_tree  = array();
      if(($parent = $menus[$key]->parent) && (isset($menus[$parent])) &&
          (is_object($menus[$parent])) && (isset($menus[$parent]->route)) && isset($menus[$parent]->tree)) {
        $parent_route = $menus[$parent]->route.'/';
        $parent_tree  = $menus[$parent]->tree;
      }

      //Create tree
      array_push($parent_tree, $menus[$key]->id);
      $menus[$key]->tree   = $parent_tree;

      //Create route
      $route = $parent_route.$menus[$key]->alias;
      $menus[$key]->route  = $route;

      //Create the query array
      $url = str_replace('index.php?', '', $menus[$key]->link);
      if(strpos($url, '&amp;') !== false)
      {
        $url = str_replace('&amp;','&',$url);
      }

      parse_str($url, $menus[$key]->query);
    }

    // $cache = JFactory::getCache('_system', 'output');
    // $cache->store(serialize($menus), 'menu_items');
  }
}

/**
 * Returns a string with an article id, in accordance
 * with various settings
 * @param $id
 * @param $view
 * @param $option
 * @param $shLangName
 */
function shGetArticleIdString( $id, $view, $option, $shLangName) {

  $sefConfig = & Sh404sefFactory::getConfig();

  // V 1.5.7 : article id, on some categories only
  $articleId= '';
  if ($sefConfig->ContentTitleInsertArticleId && isset($sefConfig->shInsertContentArticleIdCatList)
      && !empty($id) && ($view == 'article')) {

    $slugsModel = Sh404sefModelSlugs::getInstance();
    $article = $slugsModel->getArticle( $id);
    if(empty($article[$shLangName])) {
      $shLangName = '*';
    }

    // check if article categogy is in the settings categories list,
    // or that the categories list is empty, which means user selected the "All categories" option
    if (!empty($article[$shLangName])) {
      $foundCat = (!empty($sefConfig->shInsertContentArticleIdCatList) && empty($sefConfig->shInsertContentArticleIdCatList[0])) || in_array( $article[$shLangName]->catid, $sefConfig->shInsertContentArticleIdCatList);
      if ($foundCat !== null && $foundCat !== false) {
        $articleId = $article[$shLangName]->id;
      }
    }
  }

  return $articleId;
}

/**
 * Reads an return the page title assigned to either
 * current or a specific menu item
 *
 * @param $Itemid itemid of the desired menu item
 */
function shGetJoomlaMenuItemPageTitle( $Itemid = 0) {

  // get the current menu item, or possibly the one asked for
  $menus = JFactory::getApplication()->getMenu();
  $menuItem = empty( $Itemid) ? $menus->getActive() : $menus->getItem( $Itemid);

  // get value, if any set
  $title = is_object( $menuItem) ? $menuItem->params->get( 'page_title') : '';

  // return whatever we found
  return $title;
}

/**
 * check various conditions to decide if we
 * should redirect from non-sef url to its
 * sef equivalent
 */
function shShouldRedirectFromNonSef( $pageInfo) {

  die( 'voluntary die in ' . __METHOD__ . ' of class ' . __CLASS__);

}

function shCheckCustomRedirects( $path, $pageInfo) {

  die( 'voluntary die in ' . __METHOD__ . ' of class ' . __CLASS__);

}

function shCheckAlias( $incomingUrl) {

  die( 'voluntary die in ' . __METHOD__ . ' of class ' . __CLASS__);

}

// allow 2 levels or urldecoding
// handles 2 D arrays
function shUrlEncodeDeep( $data) {

  if (is_array($data)) {
    foreach( $data as $key => $element) {
      $data[$key] = shUrlEncodeDeep( $element);
    }
    return $data;
  } else {
    return urlencode( $data);
  }

}

function shRawUrlDecodeDeep( $data) {

  if (is_array($data)) {
    foreach( $data as $key => $element) {
      $data[$key] = shRawUrlDecodeDeep( $element);
    }
    return $data;
  } else {
    return rawurldecode( $data);
  }
}

function shUrlDecodeFull( $url) {

  // security checks: copied from Joomla security patch,
  // tracker id: 22767
  // Need to check that the URI is fully decoded in case of multiple-encoded attack vectors.
  $halt = 0;
  while (true)
  {
    $last = $url;
    $url = rawurldecode($url);

    // Check whether the last decode is equal to the first.
    if ($url == $last) {
      // Break out of the while if the URI is stable.
      break;
    }
    else if (++$halt > 10) {
      // Runaway check. URI has been seriously compromised.
      if (!headers_sent()) {
        header('HTTP/1.0 403 FORBIDDEN');
        echo 'Forbidden access';
      }
      jexit();
    }
  }

  return $url;
}

