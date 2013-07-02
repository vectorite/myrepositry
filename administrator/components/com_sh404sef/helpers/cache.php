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

class Sh404sefHelperCache {

  protected static $_handler = '';
  protected static $_params = null;

  protected static function & _getInstance( $handler = '') {

    static $_instance = null;

    if(empty( $_instance)) {

      // get global config
      $config = & Sh404sefFactory::getConfig();

      // instantiate object
      $handler = empty( $handler) ? $config->UrlCacheHandler : $handler;
      $className = 'Sh404sefClass' . ucfirst($handler) . 'cache';
      $_instance = new $className( $config);

    }

    return $_instance;
  }

  public static function getSefUrlFromCache( $nonSefUrl, & $sefUrl) {

    try {
      $cache = &self::_getInstance();
      return $cache->getSefUrlFromCache( $nonSefUrl, $sefUrl);
    } catch (Exception $e) {
      // TODO: should decouple this result from sh404SEF constants
      return sh404SEF_URLTYPE_NONE;
    }
  }

  public static function getNonSefUrlFromCache( $sefUrl, & $nonSefUrl) {

    try {
      $cache = &self::_getInstance();
      return $cache->getNonSefUrlFromCache( $sefUrl, $nonSefUrl);
    } catch (Exception $e) {
      // TODO: should decouple this result from sh404SEF constants
      return sh404SEF_URLTYPE_NONE;
    }

  }

  public static function addSefUrlToCache( $nonSefUrl, $sefUrl, $UrlType) {

    try {
      $cache = &self::_getInstance();
      return $cache->addSefUrlToCache( $nonSefUrl, $sefUrl, $UrlType);
    } catch (Exception $e) {
      return null;
    }

  }

  public static function removeUrlFromCache( $nonSefUrlList) {

    try {
      $cache = &self::_getInstance();
      return $cache->removeUrlFromCache( $nonSefUrlList);
    } catch (Exception $e) {
      return null;
    }

  }

  public static function purge() {

    try {
      $cache = &self::_getInstance();
      return $cache->purge();
    } catch (Exception $e) {
      return null;
    }

  }

  public static function getCacheStats() {

    try {
      // get cache instance, assuming it was already created
      $cache = & self::_getInstance();

      return $cache->getCacheStats();

    } catch (Exception $e) {
      return '';
    }
  }

}