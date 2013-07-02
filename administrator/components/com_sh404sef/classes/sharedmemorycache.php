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

if (!defined('sh404SEF_FRONT_ABS_PATH')) {
  define('sh404SEF_FRONT_ABS_PATH', str_replace('\\','/',dirname(__FILE__)).'/');
}
if (!defined('sh404SEF_ABS_PATH')) {
  define('sh404SEF_ABS_PATH', str_replace( '/components/com_sh404sef', '', sh404SEF_FRONT_ABS_PATH) );
}
if (!defined('sh404SEF_ADMIN_ABS_PATH')) {
  define('sh404SEF_ADMIN_ABS_PATH', sh404SEF_ABS_PATH.'administrator/components/com_sh404sef/');
}

/**
 * URL caching
 *
 * @author shumisha
 *
 */
class Sh404sefClassSharedmemorycache extends Sh404sefClassBasecache {

  const CACHE_STATS_DATASET_NAME = 'sh404SEF_urls';

  protected static $_livesite = '';

  public function __construct( $config) {

    parent::__construct( $config);
    self::$_livesite = md5( str_replace( 'administrator/', '', JURI::base()));
  }

  public function getSefUrlFromCache( $nonSefUrl, & $sefUrl) {

    if (!$this->_config->shUseURLCache) {
      $sefUrl = null;
      return sh404SEF_URLTYPE_NONE;
    }

    // no urls in cache, no need to go further
    if (empty($this->_urlCacheCount)) {
      $sefUrl = null;
      return sh404SEF_URLTYPE_NONE;
    }

    $key = 'nonsef2sef-' . self::$_livesite . '-' . md5( $nonSefUrl);
    try {
      $read = ShlCache_Manager::read( $key, self::CACHE_STATS_DATASET_NAME);
      if($read !== false) {
        $sefUrl = $read['sefUrl'];
        ShlSystem_Log::debug( 'sh404sef', 'read sef url from shared memory cache: %s <= %s, key: %s', $sefUrl, $nonSefUrl, $key);
        return $read['urlType'];
      }
    } catch (ShlException $e) {
      ShlSystem_Log::debug( 'sh404sef', 'error reading sef for %s from cache: %s', $nonSefUrl, $e->getMessage());
    }

    $this->_urlCacheMissesList[] = $nonSefUrl;
    return sh404SEF_URLTYPE_NONE;

  }

  // fetch an URL from cache, return null if not found
  public function getNonSefUrlFromCache( $sefUrl, & $nonSefUrl) {

    if (!$this->_config->shUseURLCache) {
      $nonSefUrl = null;
      return sh404SEF_URLTYPE_NONE;
    }

    // no urls in cache, no need to go further
    if (empty($this->_urlCacheCount)) {
      $nonSefUrl = null;
      return sh404SEF_URLTYPE_NONE;
    }

    $key = 'sef2nonsef-' . self::$_livesite . '-' . md5( $sefUrl);
    try {
      $read = ShlCache_Manager::read( $key, self::CACHE_STATS_DATASET_NAME);
      if($read !== false) {
        $sefUrl = $read['nonSefUrl'];
        ShlSystem_Log::debug( 'sh404sef', 'read non sef url from shared memory cache: %s <= %s, key: %s', $nonSefUrl, $sefUrl, $key);
        return $read['urlType'];
      }
    } catch (ShlException $e) {
      ShlSystem_Log::debug( 'sh404sef', 'error reading non sef for %s from cache: %s', $sefUrl, $e->getMessage());
    }

    return sh404SEF_URLTYPE_NONE;
  }

  public function addSefUrlToCache( $nonSefUrl, $sefUrl, $urlType) {

    if (!$this->_config->shUseURLCache) {
      return null;
    }

    // insertion key
    $nonSefKey = 'nonsef2sef-' . self::$_livesite . '-' . md5( $nonSefUrl);
    $sefKey = 'sef2nonsef-' . self::$_livesite . '-' . md5( $sefUrl);

    // build a record holding the data
    $record = array( 'nonSefUrl' => $nonSefUrl, 'sefUrl' => $sefUrl, 'urlType' => $urlType);

    // we store data twice, so as to be able to access data from either the sef or the non-sef url
    try {
      $ttl = SH404SEF_URL_CACHE_TTL * 3600;
      ShlCache_Manager::store( $nonSefKey, self::CACHE_STATS_DATASET_NAME, $record, $ttl);
      ShlCache_Manager::store( $sefKey, self::CACHE_STATS_DATASET_NAME, $record, $ttl);
      $this->_urlCacheCount++;
      ShlSystem_Log::debug( 'sh404sef', 'stored url into shared memory cache: %s <= %s, keys: %s - %s', $sefUrl, $nonSefUrl, $nonSefKey, $sefKey);
      return true;
    } catch (ShlException $e) {
      ShlSystem_Log::debug( 'sh404sef', 'error storing into cache: %s', $e->getMessage());
      return false;
    }
  }

  public function removeUrlFromCache( $nonSefUrlList) {

    if (!$this->_config->shUseURLCache || empty($nonSefUrlList) || empty($this->_urlCacheCount)) {
      return null;
    }

    try {
      foreach ($nonSefUrlList as $nonSefURL) {
        $nonSefKey = 'nonsef2sef-' . self::$_livesite . '-' . md5( $nonSefUrl);
        // first read the cache content, to extract the SEF url
        $read = ShlCache_Manager::read( $key, self::CACHE_STATS_DATASET_NAME);
        // no we delete the cache element
        $deletedNonSef = ShlCache_Manager::remove( $nonSefKey, self::CACHE_STATS_DATASET_NAME);

        // and delete the matching element indexed by sef url
        if($read !== false) {
          $sefKey = 'sef2nonsef-' . self::$_livesite . '-' . md5( $read['sefUrl']);
          $deletedSef = ShlCache_Manager::remove($sefKey, self::CACHE_STATS_DATASET_NAME);
        }

        // update cache count
        $this->_urlCacheCount--;

      }
    } catch (ShlException $e) {
      ShlSystem_Log::debug( 'sh404sef', 'error deleting from cache: %s', $e->getMessage());
    }
  }


  public function purge( $allCaches = true) {

    try {
      $purged = ShlCache_Manager::clear();
    } catch( ShlException $e) {
      $purged = false;
      ShlSystem_Log::debug( 'sh404sef', 'could not purge shared memory cache: %s', $e->getMessage());
    }
    ShlSystem_Log::debug( 'sh404sef', __METHOD__ . ': purged shared memory cache: ' . ($purged ? 'ok' : 'failed'));
    return $purged;
  }

  /**
   * Provides an object with cache usage stats
   * for the current page load only
   */
  public function getCacheStats() {

    $out  = 'Handler      : Shared memory (' . ShlCache_Manager::getHandler() . ')<br />';
    
    // collect data from cache manager
    $stats = ShlCache_Manager::getCacheStats( self::CACHE_STATS_DATASET_NAME);
    $this->_urlCacheMisses = $stats->read - $stats->hits;
    $this->_urlCacheHits = $stats->hits;

    // have parent method format that
    $out .= parent::getCacheStats();

    return $out;
  }

}
